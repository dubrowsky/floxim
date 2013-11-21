<?php

/**
 * @todo ссылка на скачку - '/floxim/?id=%id%&essence=module_filemanager&action=download';
 * еще есть action - upload - сейчас не используется
 */
class fx_controller_admin_module_filemanager extends fx_controller_admin_module {

    public function init_menu() {
        $this->add_node('filemanager', fx::lang('File-manager','system'), 'module_filemanager.ls');
    }

    protected $root_name = 'root'; // название корня для пути
    protected $base_path = ''; // путь к корню, выше не вылезаем
    protected $file_filters = array(); // фильтры, какие файлы показываем - отрицание так: "!~\.php$~i"
    protected $base_url_template = '#admin.module_filemanager.#action#(#params#)'; // шаблон урл, подстановки - #action# и #params#
    protected $path = false; // текущий каталог
    protected $breadcrumb_target = false;

    public function process() {

        $input = $this->inout;
        $action = $this->action;
        $do_return = $this->process_do_return;
        
    	$this->path = isset($input['path']) ? $input['path'] : $input['params'][0];
    	$this->path = trim($this->path, "/\\");

    	$this->base_path = isset($input['base_path']) ? $input['base_path'] : '';
    	$this->base_path = trim($this->base_path, "/\\");

    	if (!empty($this->base_path)) {
    		$this->path = $this->base_path.'/'.$this->path;
    		$this->path = trim($this->path, "/\\");
    	}

    	foreach (array('root_name', 'file_filters', 'base_url_template') as $prop) {
    		if (isset($input[$prop])) {
				$this->$prop = $input[$prop];
			}
    	}

    	if (isset($input['breadcrumb_target']) && is_object($input['breadcrumb_target'])) {
    		$this->breadcrumb_target = $input['breadcrumb_target'];
    	}

        return parent::process($input, $action, $do_return);
    }

    // обрезать путь, используя $base_path
    protected function _trim_path($path) {
    	$path = trim($path, "/\\");
    	$path = preg_replace("~^".preg_quote($this->base_path, "/\\")."~", '', $path);
    	$path = trim($path, "/\\");
    	return $path;
    }

    // Получить url для экшна
    protected function _get_url($action, $params = false) {
    	if ($params === false) {
    		$params = array();
    	} elseif (!is_array($params)) {
    		$params = array($params);
    	}
    	$tpl = $this->base_url_template;
    	$url = str_replace("#action#", $action, $tpl);
    	$url = str_replace("#params#", join(",", $params), $url);
    	return $url;
    }

    protected function _filter_file($file) {
    	foreach ($this->file_filters as $f) {
    		$inverse = preg_match("~^!~", $f);
    		$f = preg_replace("~^!~", '', $f);
    		$match = preg_match($f, $file);
    		$c_res = ($inverse ? !$match : $match);
    		if (!$c_res) {
    			return false;
    		}
    	}
    	return true;
    }

    /**
     * листинг директории
     */
    public function ls($input) {

        // директория, которую просматриваем
        //$dir = $this->path;
        $dir = isset($input['params'][0]) ? $input['params'][0] : false;
        // каталог без базового (с ограничением)
        $rel_dir = $this->_trim_path($dir);

        // хлебные крошки #2 (путь)
        // если есть this->breadcrumb_target - вернет false, добавив хлебные крошки прямо туда
        if ( ($breadcrumb = $this->get_breadcrumb($rel_dir)) ) {
			$fields[] = $this->ui->label($breadcrumb.'<br>');
		}

        $ar = array('type' => 'list', 'filter' => true);
        $ar['labels'] = array('name' => FX_ADMIN_NAME, 'type' => fx::lang('Type','system'), 'size' => fx::lang('Size','system'), 'permission' => fx::lang('Permissions','system'));

        $ls_res = fx::files()->ls(($dir ? $dir : '/'), false, true);
        if ($dir && $rel_dir) {
            $pos = strrpos($dir, '/');
            $parent_dir = $pos ? substr($dir, 0, $pos) : '';
            $ar['values'][] = array(
            	'name' => array(
            		'name' => fx::lang('Parent directory','system'),
            		'url' => $this->_get_url('ls', $this->_trim_path($parent_dir)) //'module_filemanager.ls('.$parent_dir.')'
            	)
            );
        }

        if ($ls_res) {
            foreach ($ls_res as $v) {
            	if (!$this->_filter_file($v['name'])) {
            		continue;
            	}
                $path = ($dir ? $dir.'/' : '').$v['name'];
                $perm = fx::files()->get_perm($path, true);
                if (!$v['dir']) {
                    $size = fx::files()->filesize($path);
                    if ($size < 1e3) {
                        $size .= ' ' . fx::lang('byte','system');
                    } elseif ($size < 1e6) {
                        $size = number_format($size / 1e3, 1).' ' . fx::lang('Kb','system');
                    } elseif ($size < 1e9) {
                        $size = number_format($size / 1e6, 1).' ' . fx::lang('Mb','system');
                    } else {
                        $size = number_format($size / 1e9, 1).' ' . fx::lang('Gb','system');
                    }
                }
                $item_action = $v['dir'] ? 'ls' : 'editor';
                $ar['values'][] = array(
                	'id' => $path,
					'name' => array('name' => $v['name'], 'url' => $this->_get_url($item_action, $this->_trim_path($path))),
					'size' => ($v['dir'] ? ' - ' : $size),
					'type' => ($v['dir'] ? fx::lang('directory','system') : fx::lang('File','system')),
					'permission' => $perm
				);
            }
        }


        $fields[] = $ar;

        $this->response->add_fields($fields);
        $this->response->add_buttons("add,edit,delete");
        $this->response->add_button_options('add', array('dir' => $dir));
        $this->response->submenu->set_menu('tools')->set_subactive('filemanager');
    }

    public function editor($input) {
        // директория, которую просматриваем
        $filename = isset($input['params'][0]) ? $input['params'][0] : false ;
        //$filename = $this->path;

        if (!$filename) {
            $result['status'] = 'error';
            $result['text'] = fx::lang('Do not pass the file name!','system');
            return $result;
        }

        // хлебные крошки #2 (путь)
        if ( ($breadcrumb = $this->get_breadcrumb($this->_trim_path($filename))) ) {
        	$fields []= $this->ui->label($breadcrumb."<br>");
        }

        $content_type = fx::files()->mime_content_type($filename);
        $is_image = strpos($content_type, 'image/') === 0 || $content_type == 'application/ico';

        if ($is_image) {
            $fields[] = $this->ui->label('<img src="'.fx::config()->SUB_FOLDER.'/'.$filename.'"/>');
        } else {
        	$file_content = fx::files()->readfile($filename);
            if ($file_content !== null) {
            	$content_field = array('name' => 'file_content', 'type' => 'text', 'value' => $file_content);
            	preg_match("~\.([a-z]{1,4})$~", $filename, $ext);
            	if (isset($ext[1]) && !empty($ext[1])) {
					$content_field['code'] = $ext[1];
            	}
                $fields[] = $content_field;
                $fields[] = array('type' => 'hidden', 'name' => 'file_name', 'value' => $filename);
                $fields[] = array('type' => 'hidden', 'name' => 'action', 'value' => 'editor');
            } else {
                $fields[] = $this->ui->error( fx::lang('Reading of file failed','system') );
            }
        }
        $fields[]= array('type' => 'hidden', 'name' => 'essence', 'value' => 'module_filemanager');
        $this->response->add_fields($fields);
        $this->response->submenu->set_menu('tools')->set_subactive('filemanager');
        $this->response->add_form_button('save');
    }

    public function editor_save($input) {
        $result = array('status' => 'ok');

        /* проверки */
        if (!isset($input['file_content']) || !isset($input['file_name'])) {
            $result['status'] = 'error';
            $result['text'] = fx::lang('Not all fields are transferred!','system');
        } else {
            $res = fx::files()->writefile($input['file_name'], $input['file_content'], false);
            if ($res !== 0) {
                $result['status'] = 'error';
                $result['text'] = fx::lang('Writing to file failed','system');
            }
        }
        return $result;
    }

    public function add($input) {
        $fields[] = array(
            'type' => 'select',
            'name' => 'type',
            'values' => array(
                'file' => fx::lang('File','system'),
                'dir' => fx::lang('directory','system')
            ), 
            'label' => fx::lang('What we create','system')
        );
        $fields[] = array('name' => 'name', 'label' => fx::lang('Name of file/directory','system'));
        $fields[] = array('type' => 'hidden', 'name' => 'dir', 'value' => $input['dir']);
        $fields[] = array('type' => 'hidden', 'name' => 'posting', 'value' => 1);
        $fields[] = array('type' => 'hidden', 'name' => 'action', 'value' => 'add');
        $result = array('fields' => $fields);
        $result['dialog_title'] = fx::lang('Create a new file/directory','system');
        return $result;
    }

    public function add_save($input) {
        $result = array('status' => 'ok');

        /* проверки */
        if (!$input['name']) {
            $result['status'] = 'error';
            $result['text'][] = fx::lang('Enter the name of the file/directory','system');
            $result['fields'][] = 'name';
        }
        if (!isset($input['dir'])) {
            $result['status'] = 'error';
            $result['text'][] = fx::lang('Not all fields are transferred','system');
            $result['fields'][] = 'name';
        }

        if ($result['status'] == 'ok') {
            if ($input['type'] == 'dir') {
                $res = fx::files()->mkdir($input['dir'].'/'.$input['name'], false);
            } else {
                $res = fx::files()->writefile($input['dir'].'/'.$input['name'], '', false);
            }
            if ($res !== 0) {
                $result['status'] = 'error';
                $result['text'][] = fx::lang('An error occurred while creating the file/directory','system');
            } elseif ($input['type'] != 'dir') {
                $result['location'] = 'tools.module_filemanager.editor('.$input['dir'].'/'.$input['name'].')';
            }
        }

        return $result;
    }

    public function edit($input) {
        $filename = $input['id'];
        if (!$filename) {
            $result['status'] = 'error';
            $result['text'] = fx::lang('Do not pass the file name!','system');
        }

        $pos = strrpos($filename, '/');
        $only_name = ($pos !== false) ? substr($filename, $pos + 1) : $filename;

        $perms = fx::files()->get_perm($filename);
        $is_dir = ($perms & 0x4000) == 0x4000;
        $perms = $perms & 0777;

        $fields[] = array('name' => 'name', 'label' => fx::lang('Name','system'), 'value' => $only_name);

        $active_perm = array();
        if ($perms & 0400) $active_perm[] = 'r';
        if ($perms & 0200) $active_perm[] = 'w';
        if ($perms & 0100) $active_perm[] = 'x';
        $fields[] = array('name' => 'perm_user', 'type' => 'checkbox',
                'label' => fx::lang('Permissions for the user owner','system'),
                'values' => array(
                        'r' => fx::lang('Reading','system'),
                        'w' => fx::lang('Writing','system'),
                        'x' => ($is_dir ? fx::lang('View the contents','system') : fx::lang('Execution','system')),
                ), 'value' => $active_perm);

        $active_perm = array();
        if ($perms & 040) $active_perm[] = 'r';
        if ($perms & 020) $active_perm[] = 'w';
        if ($perms & 010) $active_perm[] = 'x';
        $fields[] = array('name' => 'perm_group', 'type' => 'checkbox',
                'label' => fx::lang('Permissions for the group owner','system'),
                'values' => array(
                        'r' => fx::lang('Reading','system'),
                        'w' => fx::lang('Writing','system'),
                        'x' => ($is_dir ? fx::lang('View the contents','system') : fx::lang('Execution','system')),
                ), 'value' => $active_perm);

        $active_perm = array();
        if ($perms & 04) $active_perm[] = 'r';
        if ($perms & 02) $active_perm[] = 'w';
        if ($perms & 01) $active_perm[] = 'x';
        $fields[] = array('name' => 'perm_other', 'type' => 'checkbox',
                'label' => fx::lang('Permissions for the rest','system'),
                'values' => array(
                        'r' => fx::lang('Reading','system'),
                        'w' => fx::lang('Writing','system'),
                        'x' => ($is_dir ? fx::lang('View the contents','system') : fx::lang('Execution','system')),
                ), 'value' => $active_perm);

        $fields[] = array('type' => 'hidden', 'name' => 'filename', 'value' => $filename);
        $fields[] = array('type' => 'hidden', 'name' => 'posting', 'value' => 1);
        $fields[] = array('type' => 'hidden', 'name' => 'action', 'value' => 'edit');
        $result = array('fields' => $fields);
        $result['dialog_title'] = fx::lang('Edit the file/directory','system');
        return $result;
    }

    public function edit_save($input) {
        $filename = $input['filename'];
        if (!$filename) {
            return array('status' => 'error', 'text' => fx::lang('Not all fields are transferred!','system'));
        }
        $result = array('status' => 'ok');

        /* проверки */
        if (!$input['name']) {
            $result['status'] = 'error';
            $result['text'][] = fx::lang('Enter the name','system');
            $result['fields'][] = 'name';
        }
        if (!$input['perm_user'] && !$input['perm_group'] && !$input['perm_other']) {
            $result['status'] = 'error';
            $result['text'][] = fx::lang('Set permissions','system');
            $result['fields'][] = 'perm_user';
        }

        if ($result['status'] == 'ok') {  // смена прав
            $perms = 0;

            if ($input['perm_user']) {
                if (in_array('r', $input['perm_user'])) $perms += 0400;
                if (in_array('w', $input['perm_user'])) $perms += 0200;
                if (in_array('x', $input['perm_user'])) $perms += 0100;
            }

            if ($input['perm_group']) {
                if (in_array('r', $input['perm_group'])) $perms += 040;
                if (in_array('w', $input['perm_group'])) $perms += 020;
                if (in_array('x', $input['perm_group'])) $perms += 010;
            }

            if ($input['perm_other']) {
                if (in_array('r', $input['perm_other'])) $perms += 04;
                if (in_array('w', $input['perm_other'])) $perms += 02;
                if (in_array('x', $input['perm_other'])) $perms += 01;
            }

            $old_perms = fx::files()->get_perm($filename) & 0777;
            if ($old_perms != $perms) {
                $res = fx::files()->chmod($filename, $perms);
                if ($res !== 0) {
                    $result = array('status' => 'error');
                    $result['text'][] = fx::lang('Error when permission','system');
                }
            }
        }

        if ($result['status'] == 'ok') {  // переименование
            $pos = strrpos($filename, '/');
            $old_name = ($pos !== false) ? substr($filename, $pos + 1) : $filename;

            if ($old_name != $input['name']) {
                $res = fx::files()->rename($filename, $input['name']);
                if ($res !== 0) {
                    $result = array('status' => 'error');
                    $result['text'][] = fx::lang('Error when changing the name','system');
                }
            }
        }

        return $result;
    }

    public function delete_save($input) {
        if ($input['id']) {
            $filename = $input['id'];
            if (is_array($filename)) {
                foreach ($filename as $i => $v) {
                    $filename[$i] = $v;
                }
            } else {
                $filename = array($filename);
            }

            foreach ($filename as $v) {
                $res = fx::files()->rm($v);
                if ($res !== 0) {
                    $result = array('status' => 'error');
                    $result['text'][] = fx::lang('Error Deleting File','system') .' "'.$v. '"';
                    break;
                }
            }
        }
    }

    public function upload($input) {
        $fields[] = array('type' => 'file', 'name' => 'file',
                'label' => fx::lang('Upload file','system'));
        $fields[] = array('type' => 'hidden', 'name' => 'dir', 'value' => $input['dir']);
        $fields[] = array('type' => 'hidden', 'name' => 'posting', 'value' => 1);
        $fields[] = array('type' => 'hidden', 'name' => 'action', 'value' => 'upload');
        return array('fields' => $fields);
    }

    public function upload_save($input) {
        if (!isset($input['dir'])) {
            return array('status' => 'error', 'text' => fx::lang('Not all fields are transferred!','system'));
        }
        $dir = $input['dir'];
        $result = array('status' => 'ok');

        /* проверки */
        if (!$input['file']) {
            $result['status'] = 'error';
            $result['text'][] = fx::lang('Enter the file','system');
            $result['fields'][] = 'file';
        }

        if ($result['status'] == 'ok') {
            $file = $input['file'];

            $res = fx::files()->move_uploaded_file($file['tmp_name'], $dir.'/'.$file['name']);
            if ($res !== 0) {
                $result = array('status' => 'error');
                $result['text'][] = fx::lang('Error when downloading a file','system');
            }
        }

        return $result;
    }

    protected function get_breadcrumb($url) {
        foreach ($this->get_breadcrumb_arr($url) as $v) {
        	if ($this->breadcrumb_target) {
        		$this->breadcrumb_target->add_item($v['name'], $v['url']);
        		continue;
        	}
            if ($v['url']) {
				$breadcrumbs[] = '<a href="'.$v['url'].'">'.$v['name'].'</a>';
            } else {
            	$breadcrumbs[] = $v['name'];
            }
        }
        if ($this->breadcrumb_target) {
        	return false;
        }
        return join(' / ', $breadcrumbs);
    }

    private function get_breadcrumb_arr($url) {
        $breadcrumb = array();
        // если есть хлебные крошки родителя - не копируем туда корень
        if (!$this->breadcrumb_target) {
        	$breadcrumb []= array(
        		'name' => '<i>'.$this->root_name.'</i>',
        		'url' => $this->_get_url('ls') //'#admin.manage.tools.module_filemanager.ls()'
        	);
        }
        if ($url) {
            $dir_pieces = explode('/', $url);
            foreach ($dir_pieces as $v) {
                $path_piece .= $path_piece ? '/'.$v : $v;
                $breadcrumb[] = array(
                	'name' => $v,
                	'url' => $this->_get_url('ls', $path_piece) //'#admin.manage.tools.module_filemanager.ls('.$path_piece.')'
                );
            }
        }
        unset($breadcrumb[count($breadcrumb) - 1]['url']);  // последний - не ссылка
        return $breadcrumb;
    }

    public function download($input) {
        $filename = $input['id'];
        $pos = strrpos($filename, '/');
        $only_name = ($pos !== false) ? substr($filename, $pos + 1) : $filename;
        $file_size = fx::files()->filesize($filename);
        $file_content = fx::files()->readfile($filename);
        if ($file_content !== null) {
            while (ob_get_level()) {
                @ob_end_clean();
            }
            header($_SERVER['SERVER_PROTOCOL']." 200 OK");
            // for CGI header
            if (fx::config()->PHP_TYPE == "cgi") header("Status: 200 OK");
            header("Content-type: ".fx::files()->mime_content_type($filename));
            header("Content-Disposition: attachment; filename=\"".urldecode($only_name)."\"");
            header('Content-Transfer-Encoding: binary');

            if ($file_size) {
                header("Content-Length: ".$file_size);
                header("Connection: close");
            }
            echo $file_content;
            die;
        } else {
            $result['status'] = 'error';
            $result['text'] = fx::lang('Could not open file!','system');
            return $result;
        }
    }

}
?>
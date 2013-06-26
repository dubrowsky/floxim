<?php

/**
 * @todo ссылка на скачку - '/floxim/?id=%id%&essence=module_filemanager&action=download';
 * еще есть action - upload - сейчас не используется
 */
class fx_controller_admin_module_filemanager extends fx_controller_admin_module {

    public function init_menu() {
        $this->add_node('filemanager', fx::lang('Файл-менеджер','system'), 'module_filemanager.ls');
    }

    protected $root_name = 'корневой каталог'; // название корня для пути
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
        $dir = $this->path;

        // каталог без базового (с ограничением)
        $rel_dir = $this->_trim_path($dir);

        $fx_core = fx_core::get_object();


        // хлебные крошки #2 (путь)
        // если есть this->breadcrumb_target - вернет false, добавив хлебные крошки прямо туда
        if ($breadcrumb = $this->get_breadcrumb($rel_dir)) {
			$fields[] = $this->ui->label($breadcrumb.'<br>');
		}

        $ar = array('type' => 'list', 'filter' => true);
        $ar['labels'] = array('name' => FX_ADMIN_NAME, 'type' => fx::lang('Тип','system'), 'size' => fx::lang('Размер','system'), 'permission' => fx::lang('Права','system'));

        $ls_res = $fx_core->files->ls(($dir ? $dir : '/'), false, true);

        if ($dir && $rel_dir) {
            $pos = strrpos($dir, '/');
            $parent_dir = $pos ? substr($dir, 0, $pos) : '';
            $ar['values'][] = array(
            	'name' => array(
            		'name' => fx::lang('родительский каталог','system'),
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
                $perm = $fx_core->files->get_perm($path, true);
                if (!$v['dir']) {
                    $size = $fx_core->files->filesize($path);
                    if ($size < 1e3) {
                        $size .= ' ' . fx::lang('байт','system');
                    } elseif ($size < 1e6) {
                        $size = number_format($size / 1e3, 1).' ' . fx::lang('Кб','system');
                    } elseif ($size < 1e9) {
                        $size = number_format($size / 1e6, 1).' ' . fx::lang('Мб','system');
                    } else {
                        $size = number_format($size / 1e9, 1).' ' . fx::lang('Гб','system');
                    }
                }
                $item_action = $v['dir'] ? 'ls' : 'editor';
                $ar['values'][] = array(
                	'id' => $path,
					//'name' => ($v['dir'] ? array('name' => $v['name'], 'url' => 'module_filemanager.ls('.$path.')') : array('name' => $v['name'], 'url' => 'module_filemanager.editor('.$path.')')),
					'name' => array('name' => $v['name'], 'url' => $this->_get_url($item_action, $this->_trim_path($path))),
					'size' => ($v['dir'] ? ' - ' : $size),
					'type' => ($v['dir'] ? fx::lang('каталог','system') : fx::lang('файл','system')),
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
        //$filename = $input['params'][0];
        $filename = $this->path;

        $fx_core = fx_core::get_object();

        if (!$filename) {
            $result['status'] = 'error';
            $result['text'] = fx::lang('Не передано имя файла!','system');
            return $result;
        }

        // хлебные крошки #2 (путь)
        if ($breadcrumb = $this->get_breadcrumb($this->_trim_path($filename))) {
        	$fields []= $this->ui->label($breadcrumb."<br>");
        }

        $content_type = $fx_core->files->mime_content_type($filename);
        $is_image = strpos($content_type, 'image/') === 0 || $content_type == 'application/ico';

        if ($is_image) {
            $fields[] = $this->ui->label('<img src="'.fx::config()->SUB_FOLDER.'/'.$filename.'"/>');
        } else {
        	$file_content = $fx_core->files->readfile($filename);
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
                $fields[] = $this->ui->error( fx::lang('Не удалось прочитать файл!','system') );
            }
        }
        $fields[]= array('type' => 'hidden', 'name' => 'essence', 'value' => 'module_filemanager');
        $this->response->add_fields($fields);
        $this->response->submenu->set_menu('tools')->set_subactive('filemanager');
        $this->response->add_form_button('save');
    }

    public function editor_save($input) {
        $fx_core = fx_core::get_object();
        $result = array('status' => 'ok');

        /* проверки */
        if (!isset($input['file_content']) || !isset($input['file_name'])) {
            $result['status'] = 'error';
            $result['text'] = fx::lang('Не все поля переданы!','system');
        } else {
            $res = $fx_core->files->writefile($input['file_name'], $input['file_content'], false);
            if ($res !== 0) {
                $result['status'] = 'error';
                $result['text'] = fx::lang('Не удалась запись в файл','system');
            }
        }
        return $result;
    }

    public function add($input) {
        $fx_core = fx_core::get_object();

        $fields[] = array(
            'type' => 'select',
            'name' => 'type',
            'values' => array(
                'file' => fx::lang('файл','system'),
                'dir' => fx::lang('каталог','system')
            ), 
            'label' => fx::lang('Что создаём','system')
        );
        $fields[] = array('name' => 'name', 'label' => fx::lang('Имя файла/каталога','system'));
        $fields[] = array('type' => 'hidden', 'name' => 'dir', 'value' => $input['dir']);
        $fields[] = array('type' => 'hidden', 'name' => 'posting', 'value' => 1);
        $fields[] = array('type' => 'hidden', 'name' => 'action', 'value' => 'add');
        $result = array('fields' => $fields);
        $result['dialog_title'] = fx::lang('Создание нового файла/директории','system');
        return $result;
    }

    public function add_save($input) {
        $fx_core = fx_core::get_object();
        $result = array('status' => 'ok');

        /* проверки */
        if (!$input['name']) {
            $result['status'] = 'error';
            $result['text'][] = fx::lang('Укажите имя файла/каталога','system');
            $result['fields'][] = 'name';
        }
        if (!isset($input['dir'])) {
            $result['status'] = 'error';
            $result['text'][] = fx::lang('Не все поля переданы','system');
            $result['fields'][] = 'name';
        }

        if ($result['status'] == 'ok') {
            if ($input['type'] == 'dir') {
                $res = $fx_core->files->mkdir($input['dir'].'/'.$input['name'], false);
            } else {
                $res = $fx_core->files->writefile($input['dir'].'/'.$input['name'], '', false);
            }
            if ($res !== 0) {
                $result['status'] = 'error';
                $result['text'][] = fx::lang('Ошибка при создании файла/каталога','system');
            } elseif ($input['type'] != 'dir') {
                $result['location'] = 'tools.module_filemanager.editor('.$input['dir'].'/'.$input['name'].')';
            }
        }

        return $result;
    }

    public function edit($input) {
        $fx_core = fx_core::get_object();
        $filename = $input['id'];
        if (!$filename) {
            $result['status'] = 'error';
            $result['text'] = fx::lang('Не передано имя файла!','system');
        }

        $pos = strrpos($filename, '/');
        $only_name = ($pos !== false) ? substr($filename, $pos + 1) : $filename;

        $perms = $fx_core->files->get_perm($filename);
        $is_dir = ($perms & 0x4000) == 0x4000;
        $perms = $perms & 0777;

        $fields[] = array('name' => 'name', 'label' => fx::lang('Имя','system'), 'value' => $only_name);

        $active_perm = array();
        if ($perms & 0400) $active_perm[] = 'r';
        if ($perms & 0200) $active_perm[] = 'w';
        if ($perms & 0100) $active_perm[] = 'x';
        $fields[] = array('name' => 'perm_user', 'type' => 'checkbox',
                'label' => fx::lang('Права для пользователя-владельца','system'),
                'values' => array(
                        'r' => fx::lang('Чтение','system'),
                        'w' => fx::lang('Запись','system'),
                        'x' => ($is_dir ? fx::lang('Просмотр содержимого','system') : fx::lang('Выполнение','system')),
                ), 'value' => $active_perm);

        $active_perm = array();
        if ($perms & 040) $active_perm[] = 'r';
        if ($perms & 020) $active_perm[] = 'w';
        if ($perms & 010) $active_perm[] = 'x';
        $fields[] = array('name' => 'perm_group', 'type' => 'checkbox',
                'label' => fx::lang('Права для группы-владельца','system'),
                'values' => array(
                        'r' => fx::lang('Чтение','system'),
                        'w' => fx::lang('Запись','system'),
                        'x' => ($is_dir ? fx::lang('Просмотр содержимого','system') : fx::lang('Выполнение','system')),
                ), 'value' => $active_perm);

        $active_perm = array();
        if ($perms & 04) $active_perm[] = 'r';
        if ($perms & 02) $active_perm[] = 'w';
        if ($perms & 01) $active_perm[] = 'x';
        $fields[] = array('name' => 'perm_other', 'type' => 'checkbox',
                'label' => fx::lang('Права для остальных','system'),
                'values' => array(
                        'r' => fx::lang('Чтение','system'),
                        'w' => fx::lang('Запись','system'),
                        'x' => ($is_dir ? fx::lang('Просмотр содержимого','system') : fx::lang('Выполнение','system')),
                ), 'value' => $active_perm);

        $fields[] = array('type' => 'hidden', 'name' => 'filename', 'value' => $filename);
        $fields[] = array('type' => 'hidden', 'name' => 'posting', 'value' => 1);
        $fields[] = array('type' => 'hidden', 'name' => 'action', 'value' => 'edit');
        $result = array('fields' => $fields);
        $result['dialog_title'] = fx::lang('Правка файла/директории','system');
        return $result;
    }

    public function edit_save($input) {
        $filename = $input['filename'];
        if (!$filename) {
            return array('status' => 'error', 'text' => fx::lang('Не все поля переданы!','system'));
        }

        $fx_core = fx_core::get_object();
        $result = array('status' => 'ok');

        /* проверки */
        if (!$input['name']) {
            $result['status'] = 'error';
            $result['text'][] = fx::lang('Укажите имя','system');
            $result['fields'][] = 'name';
        }
        if (!$input['perm_user'] && !$input['perm_group'] && !$input['perm_other']) {
            $result['status'] = 'error';
            $result['text'][] = fx::lang('Задайте права доступа','system');
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

            $old_perms = $fx_core->files->get_perm($filename) & 0777;
            if ($old_perms != $perms) {
                $res = $fx_core->files->chmod($filename, $perms);
                if ($res !== 0) {
                    $result = array('status' => 'error');
                    $result['text'][] = fx::lang('Ошибка при изменении прав доступа','system');
                }
            }
        }

        if ($result['status'] == 'ok') {  // переименование
            $pos = strrpos($filename, '/');
            $old_name = ($pos !== false) ? substr($filename, $pos + 1) : $filename;

            if ($old_name != $input['name']) {
                $res = $fx_core->files->rename($filename, $input['name']);
                if ($res !== 0) {
                    $result = array('status' => 'error');
                    $result['text'][] = fx::lang('Ошибка при изменении имени','system');
                }
            }
        }

        return $result;
    }

    public function delete_save($input) {
        if ($input['id']) {
            $fx_core = fx_core::get_object();
            $filename = $input['id'];
            if (is_array($filename)) {
                foreach ($filename as $i => $v) {
                    $filename[$i] = $v;
                }
            } else {
                $filename = array($filename);
            }

            foreach ($filename as $v) {
                $res = $fx_core->files->rm($v);
                if ($res !== 0) {
                    $result = array('status' => 'error');
                    $result['text'][] = fx::lang('Ошибка при удалении файла','system') .' "'.$v. '"';
                    break;
                }
            }
        }
    }

    public function upload($input) {
        $fx_core = fx_core::get_object();
        $fields[] = array('type' => 'file', 'name' => 'file',
                'label' => fx::lang('Закачать файл','system'));
        $fields[] = array('type' => 'hidden', 'name' => 'dir', 'value' => $input['dir']);
        $fields[] = array('type' => 'hidden', 'name' => 'posting', 'value' => 1);
        $fields[] = array('type' => 'hidden', 'name' => 'action', 'value' => 'upload');
        return array('fields' => $fields);
    }

    public function upload_save($input) {
        if (!isset($input['dir'])) {
            return array('status' => 'error', 'text' => fx::lang('Не все поля переданы!','system'));
        }
        $dir = $input['dir'];
        $fx_core = fx_core::get_object();
        $result = array('status' => 'ok');

        /* проверки */
        if (!$input['file']) {
            $result['status'] = 'error';
            $result['text'][] = fx::lang('Укажите файл','system');
            $result['fields'][] = 'file';
        }

        if ($result['status'] == 'ok') {
            $file = $input['file'];

            $res = $fx_core->files->move_uploaded_file($file['tmp_name'], $dir.'/'.$file['name']);
            if ($res !== 0) {
                $result = array('status' => 'error');
                $result['text'][] = fx::lang('Ошибка при закачке файла','system');
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
        $fx_core = fx_core::get_object();
        $filename = $input['id'];
        $pos = strrpos($filename, '/');
        $only_name = ($pos !== false) ? substr($filename, $pos + 1) : $filename;
        $file_size = $fx_core->files->filesize($filename);
        $file_content = $fx_core->files->readfile($filename);
        if ($file_content !== null) {
            while (ob_get_level()) {
                @ob_end_clean();
            }
            header($_SERVER['SERVER_PROTOCOL']." 200 OK");
            // for CGI header
            if (fx::config()->PHP_TYPE == "cgi") header("Status: 200 OK");
            header("Content-type: ".$fx_core->files->mime_content_type($filename));
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
            $result['text'] = fx::lang('Не получилось открыть файл!','system');
            return $result;
        }
    }

}
?>
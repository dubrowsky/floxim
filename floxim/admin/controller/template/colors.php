<?php

class fx_controller_admin_template_colors extends fx_controller_admin_template {

	public function all($input) {

		$template_id = $input['params'][0];

		$template = fx::data('template')->get_by_id($template_id);

        $ar = array('type' => 'list', 'filter' => true);
        $ar['labels'] = array('name' => FX_ADMIN_NAME, 'colors' => 'Цвета');

        $colors = $template['colors'];

        $color_vars = array(
			'red' => '#ff0000',
			'orange' => '#FB940B',
			'yellow' => '#FFFF00',
			'green' => '#00FF00',
			'lightblue' => '#00FFFF',
			'blue' => '#0000FF',
			'purple' =>  '#FF00FF',
			'grey' => '#C0C0C0',
			'black' => '#000000'
		);

        if ($colors) {
			foreach ($colors as $id => $item) {
				$c1 = isset($item['color']) && isset($color_vars[$item['color']]) ? $color_vars[$item['color']] : 'grey';
				$c2 = isset($item['color_alt']) && isset($color_vars[$item['color_alt']]) ? $color_vars[$item['color_alt']] : $c1;

				$html_colors = '<div style="width:0; height:0; '.
								'border-top:10px solid '.$c1.'; border-left:10px solid '.$c1.'; '.
								'border-bottom:10px solid '.$c2.'; border-right:10px solid '.$c2.';"></div>';


				$el = array('id' => $id, 'name' => $item['name'], 'colors' => $html_colors);

				if ($item['default']) {
					$el['name'] .= " (используется по умолчанию)";
				}

				$el['file'] = array('name' => $item['file'], 'url' => 'template_colors.edit_file('.$template['id'].','.$id.')');
				$ar['values'][] = $el;
			}
        }

        $fields[] = $ar;


        $buttons = array("add", "edit", "delete");
        $buttons_action['add']['options'] = array( 'template_id' => $template['id']);
        $buttons_action['edit']['options'] = array('template_id' => $template['id']);
        $buttons_action['delete']['options'] = array('template_id' => $template['id']);
        return array('fields' => $fields, 'buttons' => $buttons, 'buttons_action' => $buttons_action, 'essence' => 'template_colors');
    }

    public function add($input) {
        $color = array();
        $fields = $this->_form_color($color, $input['template_id']);
        //$fields[] = $this->ui->hidden('template_id', $input['template_id']);

        return array('fields' => $fields);
    }

    public function edit($input) {
        $template = fx::data('template')->get_by_id($input['template_id']);
        $color = $template['colors'][$input['id']];

        $fields = $this->_form_color($color, $input['template_id']);

        $fields[] = $this->ui->hidden('id', $input['id']);

        return array('fields' => $fields);
    }

    protected function _get_file_name($color_name) {
    	$trimmed = preg_replace("~[^a-z0-9_-]~", '-', $color_name);
    	$trimmed = preg_replace("~\-+~", '-', $trimmed);
    	$trimmed = trim($trimmed, '-');
    	if (strlen($trimmed) == 0) {
    		return false;
    	}
    	return 'css/'.$trimmed.".css";
    }

    protected function _form_color($color, $template_id) {

    	$fx_core = fx_core::get_object();
        $template = fx::data('template')->get_by_id($template_id);

        $filecontent = '';
    	if (isset($color['name']) && !empty($color['name'])) {
			$fields[]= $this->ui->hidden('old_name', $color['name']);

			$file_name = $this->_get_file_name($color['name']);
			$file_path = $template->get_path();

			if ( (!$file_name || !$fx_core->files->file_exists($file_path.$file_name)) && isset($color['file'])) {
				$file_name = $color['file'];
			}

			$fields []= $this->ui->hidden('file_name_old', $file_name);

			try {
				$filecontent = $fx_core->files->readfile($file_path.$file_name);
			} catch (Exception $e) {
				$filecontent = '';
			}
    	}

        $fields[] = $this->ui->input('name', 'Название расцветки', $color['name']);

        $fields[] = array('type' => 'colorbasic', 'name' => 'color', 'label' => 'Основной цвет', 'value' => $color['color']);
        $fields[] = array('type' => 'colorbasic', 'name' => 'color_alt', 'label' => 'Дополнительный цвет', 'value' => $color['color_alt']);

        $fields[] = $this->ui->code_editor('filecontent', 'CSS', $filecontent, 'css');

        //$fields[] = $this->ui->input('file', 'Путь до файла', $color['file']);
        $fields[] = $this->ui->checkbox('default', 'Использовать по умолчанию', '', $color['default']);

        $fields[] = $this->ui->hidden('posting');
        $fields[] = $this->ui->hidden('action', 'add');

        $fields[] = $this->ui->hidden('template_id', $template_id);

        return $fields;
    }

    public function delete_save($input) {
        
        $template = fx::data('template')->get_by_id($input['template_id']);
        $colors = $template['colors'];

        $ids = $input['id'];
        if (!is_array($ids)) $ids = array($ids);
        foreach ($ids as $id) {
            unset($colors[$id]);
        }

        $template->set('colors', $colors)->save();
        return array('status' => 'ok');
    }

    public function add_save($input) {
        return $this->_save($input);
    }

    public function edit_save($input) {
        return $this->_save($input);
    }

    protected function _save($input) {
        $fx_core = fx_core::get_object();
        $template = fx::data('template')->get_by_id($input['template_id']);

        if (!$input['name']) {
            $result['status'] = 'error';
            $result['text'][] = 'Введите  название расцветки';
            return $result;
        }

        if (preg_match("~[^a-z0-9\s_-]~", $input['name'])) {
        	$result['status'] = 'error';
            $result['text'][] = 'Название расцветки может содержать только цифры, буквы латинского алфавита и дефис';
            return $result;
        }

        /*
        if (!$input['file']) {
            $result['status'] = 'error';
            $result['text'][] = 'Введите путь до файла';
            return $result;
        }
        */


        $file_name = self::_get_file_name($input['name']);
        $path = $template->get_path().$file_name;

        try {
            $fx_core->files->writefile($path, $input['filecontent']);

        } catch (Exception $e) {
            $result['status'] = 'error';
            $result['text'][] = 'Не удается сохранить файл';
            return $result;
        }

        $new_color = array(
        	'name' => $input['name'],
        	'file' => $file_name,
        	'color' => $input['color'],
        	'color_alt' => $input['color_alt'],
        	'default' => $input['default']
        );

        $colors = $template['colors'];

        if ($input['default']) {
            if ($colors) {
                foreach ($colors as &$v) {
                    if ($v['default']) unset($v['default']);
                }
            }
        }

        $id = $input['id'];
        if ($id) {
            $colors[$id] = $new_color;
        } else {
            // skip id=0
            if (!$colors) {
                $colors[1] = $new_color;
            } else {
                $colors[] = $new_color;
            }
        }

        $template->set('colors', $colors)->save();

        // удаляем старый файл
        if ($input['file_name_old']) {
        	$file_path_old = $template->get_path().$input['file_name_old'];
        	if ($file_path_old != $path) {
        		$fx_core->files->rm($file_path_old);
        	}
        }

        return array('status' => 'ok');
    }

    protected function edit_file($input) {
        $fx_core = fx_core::get_object();

        $template = fx::data('template')->get_by_id($input['params'][0]);
        $color_id = $input['params'][1];

        $filepath = fx::config()->HTTP_TEMPLATE_PATH.$template['keyword'].'/';
        $filepath .= $template['colors'][$color_id]['file'];

        try {
			$filecontent = $fx_core->files->readfile($filepath);
		} catch (Exception $e) {
			$filecontent = '';
		}

        $fields[] = $this->ui->code_editor('filecontent', 'Содержимое файла', $filecontent, 'css');
        $fields[] = $this->ui->hidden('action', 'edit_file');
        $fields[] = $this->ui->hidden('posting');
        $fields[] = $this->ui->hidden('template_id', $template['id']);
        $fields[] = $this->ui->hidden('id', $color_id);

        $result['fields'] = $fields;
        $result['form_button'] = array('save');

        $this->response->submenu->set_menu('template-'.$template['id'])->set_subactive('colors');

        $result['breadcrumb'][] = array('name' => $template['name'], 'url' => 'template.operating('.$template['id'].',colors)');
        $result['breadcrumb'][] = array('name' => 'Расцветка "'.$template['colors'][$color_id]['name'].'"');
        return $result;
    }

    public function edit_file_save($input) {
        $fx_core = fx_core::get_object();
        $result = array('status' => 'ok', 'text' => 'Файл сохранен');

        $template = fx::data('template')->get_by_id($input['template_id']);
        $color_id = $input['id'];
        $filepath = $template->get_path().$template['colors'][$color_id]['file'];

        try {
            $fx_core->files->writefile($filepath, $input['filecontent']);
        } catch (Exception $e) {
            $result['ststus'] = 'error';
            $result['text'] = $e->getMessage();
        }

        return $result;
    }

}

?>

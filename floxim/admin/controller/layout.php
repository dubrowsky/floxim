<?php

class fx_controller_admin_layout extends fx_controller_admin {

    /**
     * Вывод списка всех макетов дизайна "в разработке"
     */
    public function all($input) {
        $items = array();
        
        $layouts = fx::data('layout')->all(); //('parent_id', 0);
        foreach ($layouts as $layout) {
            $layout_id = $layout['id'];
            $items[$layout_id] = $layout;
        }

        $layout_use = array(); // [номер макет][номер сайта] => 'Имя сайта'
        foreach (fx::data('site')->all() as $site) {
            $layout_use[$site['layout_id']][$site['id']] = '<a href="#admin.site.map('.$site['id'].')">'.$site['name'].'</a>';
        }

        $ar = array('type' => 'list', 'filter' => true);
        $ar['labels'] = array('name' => FX_ADMIN_NAME, 'use' => fx::lang('Используется на сайтах','system'), 'buttons' => array('type' => 'buttons'));

        foreach ($items as $item) {
        	$submenu = self::get_template_submenu($item);
        	$submenu_first = current($submenu);
            $name = array(
            	'name' => $item['name'],
            	'url' => $submenu_first['url']
            );
            $el = array('id' => $item['id'], 'name' => $name);
            if ($layout_use[$item['id']]) {
                $el['use'] = join(', ', $layout_use[$item['id']]);
                $el['fx_not_available_buttons'] = array('delete');
            } else {
                $el['use'] = ' - ';
            }
            $el['buttons'] = array();
            
            foreach ($submenu as $submenu_item) {
            	$el['buttons'][]= array(
            		'label' => $submenu_item['title'],
            		'url' => $submenu_item['url']
                );
            }

            $ar['values'][] = $el;
        }

        $fields[] = $ar;

        $buttons = array("add", "delete");
        $buttons_pulldown['add'] = array(
                array('name' => fx::lang('пустой','system'), 'options' => array('source' => 'new')),
                array('name' => fx::lang('импортировать','system'), 'options' => array('source' => 'import')),
                array('name' => fx::lang('установить с FloximStore','system'), 'options' => array('source' => 'store'))
        );

        $result = array('fields' => $fields, 'buttons' => $buttons, 'buttons_pulldown' => $buttons_pulldown);

        $this->response->submenu->set_menu('template');
        return $result;
    }

    public function add($input) {

        switch ($input['source']) {
            case 'import' :
                $fields[] = array('name' => 'importfile', 'type' => 'file', 'label' => fx::lang('Файл','system'));
                $result['dialog_title'] = fx::lang('Импорт макета дизайна','system');
                $fields[] = $this->ui->hidden('action', 'import');
                break;
            default :
                $input['source'] = 'new';
                $fields[] = $this->ui->hidden('action', 'add');
                $fields[] = array('name' => 'name', 'label' => fx::lang('Название макета','system'));
                $fields[] = array('name' => 'keyword', 'label' => fx::lang('Keyword (название папки с макетом)','system'));
                $result['dialog_title'] = fx::lang('Добавление макета дизайна','system');
        }

        $fields[] = $this->ui->hidden('source', $input['source']);
        
        $fields[] = $this->ui->hidden('posting');

        $result['fields'] = $fields;
        return $result;
    }
    

    public function add_save($input) {
        $result = array('status' => 'ok');
        $keyword = trim($input['keyword']);
        $name = trim($input['name']);

        $data = array('name' => $name, 'keyword' => $keyword);
        $layout = fx::data('layout')->create($data);
        $path = $layout->get_path();
        try {
            fx::files()->mkdir($path);
            $layout->save();
        } catch (Exception $e) {
            $result['status'] = 'error';
            $result['text'][] = fx::lang('Не удалось создать каталог','system'). ' ' .$path;
        }
        return $result;
    }

    public function delete_save($input) {
        $result = array('status' => 'ok');
        $ids = $input['id'];
        if (!is_array($ids)) $ids = array($ids);

        foreach ($ids as $id) {
            try {
                $layout = fx::data('layout')->get_by_id($id);
                $layout->delete();
            } catch (Exception $e) {
                $result['status'] = 'ok';
                $result['text'][] = $e->getMessage();
            }
        }
        return $result;
    }
    
    
    public function operating($input) {
        $layout = fx::data('layout', $input['params'][0]); //->get_by_id($input['params'][0]);
        $action = isset($input['params'][1]) ? $input['params'][1] : 'layouts';

        if (!$layout) {
            $fields[] = $this->ui->error(fx::lang('Макет не найден','system'));
            return array('fields' => $fields);
        }
        
        self::make_breadcrumb($layout, $action, $this->response->breadcrumb);
        
        if (method_exists($this, $action)) {
        	$result = call_user_func(array($this, $action), $layout);
        }

        $this->response->submenu->set_menu('layout-'.$layout['id'])->set_subactive($action);
        return $result;
    }
    
    public static function make_breadcrumb($template, $action, $breadcrumb) {
    	$tpl_submenu = self::get_template_submenu($template);
        $tpl_submenu_first = current($tpl_submenu);
        
    	$breadcrumb->add_item(fx::lang('Макеты','system'), '#admin.layout.all');
        $breadcrumb->add_item($template['name'], $tpl_submenu_first['url']);
		$breadcrumb->add_item($tpl_submenu[$action]['title'], $tpl_submenu[$action]['url']);
    }

    public function layouts($template) {
        $items = $template->get_layouts();

        $ar = array('type' => 'list', 'filter' => true);
        $ar['labels'] = array('name' => FX_ADMIN_NAME);

        foreach ($items as $item) {
            $name = array('name' => $item['name'], 'url' => 'layout.edit('.$item['id'].')');
            $el = array('id' => $item['id'], 'name' => $name);
            $ar['values'][] = $el;
        }

        $fields[] = $ar;

        $buttons = array("add", "delete");
        $buttons_action['add']['options']['parent_id'] = $template['id'];
        $result = array('fields' => $fields, 'buttons' => $buttons, 'buttons_action' => $buttons_action, 'essence' => 'layout');

        return $result;
    }
    
    public static function get_template_submenu($layout) {
    	$titles = array(
    		// 'layouts' => 'Лэйауты',
			// 'files' => 'Файлы',
			// 'colors' => 'Расцветки',
			'settings' => fx::lang('Настройки','system')
		);

        $layout_id = $layout['id'];
        
		$items = array();
		foreach ($titles as $key => $title) {
			$items [$key]= array(
				'title' => $title,
				'code' => $key,
				'url' => 'layout.operating('.$layout_id.','.$key.')'
			);
		}
		return $items;
    }

    public function files($template) {
    	$params = isset($this->input['params']) ? $this->input['params'] : array();
    	
    	$fm_action = isset($params[2]) ? $params[2] : 'ls';
    	$fm_path = isset($params[3]) ? $params[3] : '';
    	
    	$filemanager = new fx_controller_admin_module_filemanager($fm_input, $fm_action, true);
    	$path = $template->get_path();
    	$fm_input = array(
    		'base_path' => $path,
    		'path' => $fm_path,
    		'base_url_template' => '#admin.template.operating('.$template['id'].',files,#action#,#params#)',
    		'root_name' => $template['name'],
    		'file_filters' => array('!~^\.~', '!~\.php$~'),
    		'breadcrumb_target' => $this->response->breadcrumb
		);
    	$result = $filemanager->process();
    	$result['buttons_essence'] = 'module_filemanager';
    	return $result;
    }
    
    public function _files($template) {
        $files = $template['files'];

        $ar = array('type' => 'list', 'filter' => true);
        $ar['labels'] = array('name' => FX_ADMIN_NAME);

        if ($files)
                foreach ($files as $id => $item) {
                $name = $item['file'];
                $el = array('id' => $id, 'name' => array('name' =>$name, 'url' => 'template_files.edit('.$template['id'].','.$id.') '));
                $ar['values'][] = $el;
            } else {
            $fields[] = $this->ui->label( fx::lang('Нет файлов','system') );
        }

        $fields[] = $ar;

        $buttons = array("add", "delete");
        $buttons_action['add']['options']['template_id'] = $template['id'];
        $buttons_action['delete']['options']['template_id'] = $template['id'];
        return array('fields' => $fields, 'buttons' => $buttons, 'buttons_action' => $buttons_action, 'essence' => 'template_files');
    }
    
    public function colors($template) {
    	$controller = new fx_controller_admin_template_colors(array('params' => array($template['id'])), 'all', true);
    	return $controller->process();
    }

    public function settings($template) {
    	$fields[] = $this->ui->label("<a href='/floxim/?essence=template&amp;action=export&amp;id=".$template['id']."'>" . fx::lang('Экспортировать в файл','system') . "</a>");
        $fields[] = $this->ui->input('name', fx::lang('Название макета','system'), $template['name']);
        $fields[] = $this->ui->hidden('action', 'settings');
        $fields[] = $this->ui->hidden('id', $template['id']);
        
        $result = array('fields' => $fields, 'form_button' => array('save'));
        return $result;
    }
    
    public function settings_save ( $input ) {
        $name = trim($input['name']);
        if ( !$name ) {
            $result['status'] = 'error';
            $result['text'][] = fx::lang('Укажите название макета','system');
            $result['fields'][] = 'name';
        }
        else {
            $template = fx::data('template')->get_by_id($input['id']);
            if ( $template ) {
                $result['status'] = 'ok';
                $template->set('name', $name)->save();
            }
            else {
                $result['status'] = 'error';
                $result['text'][] = fx::lang('Макет не найден','system');
            }
        }
        
        return $result;
    }
    
    // вернуть id текущего, если false
    protected static  function _get_site_id($site_id = false) {
    	if ($site_id) {
    		return $site_id;
    	}
    	$fx_core = fx_core::get_object();
    	$site = $fx_core->env->get_site();
    	return $site['id'];
    }
    
    public static function set_preview_data($template_id, $color_id = false, $site_id = false) {
    	$fx_core = fx_core::get_object();
    	$site_id = self::_get_site_id($site_id);
    	$fx_core->input->set_service_session('preview_template_id_site_'.$site_id, $template_id);
    	$fx_core->input->set_service_session('preview_color_id_site_'.$site_id, $color_id);
    }
    
    public static function unset_preview_data($site_id = false) {
    	$fx_core = fx_core::get_object();
    	$site_id = self::_get_site_id($site_id);
    	$fx_core->input->unset_service_session('preview_template_id_site_'.$site_id);
    	$fx_core->input->unset_service_session('preview_color_id_site_'.$site_id);
    }
    
    public static function get_preview_data($site_id = false) {
    	$fx_core = fx_core::get_object();
    	$site_id = self::_get_site_id($site_id);
    	$res =  array(
    		'template_id' => $fx_core->input->get_service_session('preview_template_id_site_'.$site_id),
    		'color_id' => $fx_core->input->get_service_session('preview_color_id_site_'.$site_id),
		);
		if (!$res['template_id']) {
			return false;
		}
		return $res;
    }
    

    public function set_preview($input) {
        $template_id = $input['template_id'];
        $color = $input['color'];
        
        $site = fx::data('site')->get_by_id(self::_get_site_id($input['site_id']));
        
        if (!$template_id || !is_numeric($template_id)) {
        	$template_id = $site['template_id'];
        	$color = $site['color'];
        }
        
        self::set_preview_data($template_id, $color, $site_id);
        
        $suitable = new fx_suitable();
        $suitable->apply_design($site, $template_id);
            
        $result = array('status' => 'ok');
        
        
        $result['reload'] = $input['panel_mode'] ? true : 'http://'.$site['domain'].'/';
        return $result;
    }

    public function exit_preview($input) {
    	self::unset_preview_data($input['site_id']);
        return array('status' => 'ok', 'reload' => true);
    }

    public function approve_preview($input) {
        $site_id = self::_get_site_id($input['site_id']);
        
        if ($preview = self::get_preview_data($site_id) ) {
        	$site = fx::data('site')->get_by_id($site_id);
			$site['template_id'] = $preview['template_id'];
			$site['color'] = $preview['color_id'];
			$site->save();
			self::unset_preview_data($site_id);
        }
		
        return array('status' => 'ok', 'reload' => true);
    }
    
    public function preview_panel($input) {
    	
    	$tpl_values = array();
        $tpl_all = fx::data('template')->get_all('type', 'parent');

        $colors = array();
        foreach ($tpl_all as $tpl) {
            $tpl_values[$tpl['id']] = $tpl['name'];
            $colors[$tpl['id']] = $tpl['colors'];
        }
        
        $site_id = self::_get_site_id($input['site_id']);
        if (! ($preview = self::get_preview_data($site_id)) ) {
        	// данные из текущего сайта подставляются в превью из set_preview
        	return;
        }
        
        $post_on_change = array(
        	'action' => 'set_preview',
        	'essence' => 'template',
        	'fx_admin' =>	true,
        	'panel_mode' => true
		);

        $fields = array();
        $fields []= array(
        		'name' => 'template_id', 
        		'type' => 'select', 
        		'values' => $tpl_values, 
        		'value' => $preview['template_id'], 
        		'label' => fx::lang('Макет','system'),
        		'post' => $post_on_change
        );
        foreach ($colors as $tpl_id => $color) {
            if ($color) {
                $color_value = array( fx::lang('По умолчанию','system') );
                foreach ($color as $color_id => $v) {
                    $color_value[$color_id] = $v['name'];
                }
                $fields[] = array(
                	'label' => fx::lang('Расцветка','system'),
                	'name' => 'color', 
                	'type' => 'select', 
                	'value' => $preview['color_id'], 
                	'values' => $color_value, 
                	'parent' => array('template_id', $tpl_id), 
                	'unactive' => true,
                	'post' => $post_on_change
                );
            }
        }
        $fields []= array('type' => 'button', 'label' => fx::lang('Применить текущий','system'), 'post' => array('essence' => 'template', 'action' => 'approve_preview'));
        $fields []= array('type' => 'button', 'label' => fx::lang('Выход','system'), 'post' => array('essence' => 'template', 'action' => 'exit_preview'));
        $this->response->add_fields($fields);
    }
}
?>
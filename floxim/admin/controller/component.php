<?php

class fx_controller_admin_component extends fx_controller_admin {

    /**
     * Список всех компонентов И ВИДЖЕТОВ (!) или компонентов определенной группы
     */
    public function group($input) {
        $fx_core = fx_core::get_object();
        $essence = $this->essence_type;
        $user_component_id = $fx_core->get_settings('user_component_id', 'auth');
        
        if ($input['params'][0]) {
            $components = fx::data($essence)->get_all(" MD5(`group`)", $input['params'][0]);
        } else {
            $components = fx::data($essence)->get_all();
        }

        $field = array('type' => 'list', 'filter' => true);
        $field['labels'] = array('name' => FX_ADMIN_NAME, 'buttons' => array('type' => 'buttons'));
        $field['values'] = array();
        foreach ($components as $v) {
        	
        	$submenu = self::get_component_submenu($v);
        	$submenu_first = current($submenu);
        	
            $r = array(
				'id' => $v['id'],
				'name' => array(
					'name' => $v['name'],
					'url' => $submenu_first['url'] //$essence.'.edit('.$v['id'].')'));
				),
				'tip_text' => "id: ".$v['id']
			);
			
            if ( $essence == 'component' && $user_component_id == $v['id'] ) {
                $r['fx_not_available_buttons'] = array('delete');
            }
            
            $r['buttons'] = array();
            foreach ($submenu as $submenu_item) {
            	$r['buttons'] []= array(
            		'type' => 'button', 
            		'label' => $submenu_item['title'], 
            		'url' => $submenu_item['url']
            	);
            }
            
            $field['values'][] = $r;
        }
        $fields[] = $field;

        $buttons = array("add", "delete");
        $buttons_pulldown['add'] = array(
                array('name' => 'новый', 'options' => array('source' => 'new')),
                array('name' => 'импортировать', 'options' => array('source' => 'import')),
                array('name' => 'установить с FloximStore', 'options' => array('source' => 'store'))
        );

        $result = array('fields' => $fields, 'buttons' => $buttons, 'buttons_pulldown' => $buttons_pulldown);

        $this->response->breadcrumb->add_item(self::$_essence_types[$essence], '#admin.'.$essence.'.group');
        if ($input['params'][0]) {
            $this->response->submenu->set_menu($essence.'group-'.md5($components[0]['group']));
            $this->response->breadcrumb->add_item($components[0]['group']);
        }
        else {
            $this->response->submenu->set_menu($essence);
        }

        return $result;
    }
    
    public function get_component_submenu($component) {
    	
    	$essence_code = str_replace('fx_','',get_class($component));
    	
    	$titles = array(
    		'component' => array(
				'ctpl' => 'Шаблоны',
				'fields' => 'Поля',
				'settings' => 'Настройки'
			), 
			'widget' => array(
				'tpl' => 'Шаблон',
				'fields' => 'Поля',
				'settings' => 'Настройки'
			)
		);
		
		$res = array();
		foreach($titles[$essence_code] as $code => $title) {
			$res [$code]= array(
				'title' => $title,
				'code' => $code,
				'url' => $essence_code.'.edit('.$component['id'].','.$code.')'
			);
		}
		return $res;
    }

    public function add($input) {
        $fields = array();

        switch ($input['source']) {
            case 'import':
                $fields[] = array('name' => 'importfile', 'type' => 'file', 'label' => 'Файл');
                $fields[] = $this->ui->hidden('action', 'import');
                break;
            case 'store':
                $fields[] = $this->ui->store('component');
                break;
            default:
                $input['source'] = 'new';
                $groups = fx::data('component')->get_all_groups();

                $fields[] = $this->ui->hidden('action', 'add');
                $fields[] = array('label' => 'Название', 'name' => 'name');
                $fields[] = array('label' => 'Ключевое слово', 'name' => 'keyword');
                $fields[] = array('label' => 'Группа', 'type' => 'select', 'values' => $groups, 'name' => 'group', 'extendable' => 'Другая группа');
        }

        $fields[] = $this->ui->hidden('source', $input['source']);
        $fields[] = $this->ui->hidden('posting');


        return array('fields' => $fields);
    }

    public function store($input) {
        return $this->ui->store('component', $input['filter'], $input['reason'], $input['position']);
    }

    public function store_save($input) {
        $store = new fx_admin_store();
        $file = $store->get_file($input['store_id']);

        $result = array('status' => 'ok');
        try {
            $imex = new fx_import();
            $imex->import_by_content($file);
        } catch (Exception $e) {
            $result = array('status' => 'error');
            $result['text'][] = $e->getMessage();
        }

        return $result;
    }

    public function edit($input) {
        
        $essence_code = $this->essence_type;

        $component = fx::data($essence_code)->get_by_id($input['params'][0]);
        
        $action = isset($input['params'][1]) ? $input['params'][1] : 'ctpl';
        
        self::make_breadcrumb($component, $action, $this->response->breadcrumb);
        
        if (method_exists($this, $action)) {
        	$result = call_user_func(array($this, $action), $component);
        }
        
        $result['tree']['mode'] = $essence_code.'-'.$component['id'];
        $this->response->submenu->set_menu($essence_code.'-'.$component['id']);
        
        return $result;
    }
    
    protected static $_essence_types = array(
    	'widget' => 'Виджеты',
    	'component' => 'Компоненты'
	);
    
    public static function make_breadcrumb($component, $action, $breadcrumb) {
    	$essence_code = str_replace('fx_','',get_class($component));
    	$submenu = self::get_component_submenu($component);
        $submenu_first = current($submenu);
    	$breadcrumb->add_item(self::$_essence_types[$essence_code], '#admin.'.$essence_code.'.group');
        $breadcrumb->add_item($component['group'], '#admin.'.$essence_code.'.group('.md5($component['group']).')');
        $breadcrumb->add_item($component['name'], $submenu_first['url']);
        if (isset($submenu[$action])) {
			$breadcrumb->add_item($submenu[$action]['title'], $submenu[$action]['url']);
        }
    }

    public function export($input) {
        $fx_core = fx_core::get_object();
        $widget = fx::data('component')->get_by_id($input['id']);

        $fx_export = new fx_export();
        $fx_export->export_essence($widget);
    }

    public function add_save($input) {
        $fx_core = fx_core::get_object();
        $result = array('status' => 'ok');

        $data['name'] = trim($input['name']);
        $data['keyword'] = trim($input['keyword']);

        $data['group'] = $input['group'];
        if (($data['group'] == 'fx_new') && $input['fx_new_group']) {
            $data['group'] = $input['fx_new_group'];
        }
        $data['has_page'] = $input['has_page'];

        $component = fx::data('component')->create($data);

        if (!$component->validate()) {
            $result['status'] = 'error';
            $result['errors'] = $component->get_validate_error();
            return $result;
        }

        try {
            $component->create_dir();
            $component->save();

            $ctpl_data = array('component_id' => $component['id'], 'keyword' => 'main', 'name' => $component['name']);
            $ctpl = fx::data('ctpl')->create($ctpl_data);
            $ctpl->create_file();
            $ctpl->save();
        } catch (Exception $e) {
            $result['status'] = 'error';
            $result['text'][] = $e->getMessage();
            if ($component['id']) {
                $component->delete();
            }
        }

        return $result;
    }
    
    public function edit_save($input){
        if (! ($component = fx::data('component', $input['id'])) ) {
            dev_log('no cmp');
            die("NO CMP");
        }
        if (!empty($input['name'])) {
            $component['name'] = $input['name'];
        }
        if ($input['new_group']) {
            $component['group'] = $input['new_group'];
        } else {
            $component['group'] = $input['group'];
        }
        $component['has_page'] = $input['has_page'];
        $component['description'] = $input['description'];
        $component->save();
        return array('status' => 'ok');
        dev_log($input);
        echo "SAVVV";
        die();
    }

    public function import_save($input) {
        $file = $input['importfile'];
        if (!$file) {
            $result = array('status' => 'error');
            $result['text'][] = 'Ошибка при создании временного файла';
        }

        $result = array('status' => 'ok');
        try {
            $imex = new fx_import();
            $imex->import_by_file($file['tmp_name']);
        } catch (Exception $e) {
            $result = array('status' => 'ok');
            $result['text'][] = $e->getMessage();
        }

        return $result;
    }

    public function ctpl($component) {
        $fx_core = fx_core::get_object();
        $ctpls = fx::data('ctpl')->get_by_component($component['id']);

        $ar = array('type' => 'list', 'filter' => true, 'tab' => 'ctpl');
        $ar['labels'] = array('name' => FX_ADMIN_NAME);

        $ar['values'] = array();
        foreach ($ctpls as $v) {
            $r = array(
                    'id' => $v['id'],
                    'name' => array('name' => $v['name'], 'url' => 'ctpl.edit('.$v['id'].')'),
            );
            if ( $v->is_default() ) {
                $r['name']['name'] .= " (по умолчанию)";
                $r['fx_not_available_buttons'] = array('delete');
            }
            
            $ar['values'][] = $r;
        }
        $fields[] = $ar;

        $buttons = array("add", "delete");
        $buttons_action['add']['options']['component_id'] = $component['id'];

        $this->response->submenu->set_subactive('ctpl-'.$component['id']);
        return array('essence' => 'ctpl', 'fields' => $fields, 'buttons' => $buttons, 'buttons_action' => $buttons_action);
    }

    public function fields($component) {
        $controller = new fx_controller_admin_field();
        $this->response->submenu->set_subactive('fields');
        return $controller->items($component);
    }

    public function settings($component) {
        $fx_core = fx_core::get_object();

        $groups = fx::data('component')->get_all_groups();

        $fields[] = $this->ui->label("<a href='/floxim/?essence=admin_component&amp;action=export&amp;id=".$component['id']."'>Экспортировать в файл</a>");
        $fields[] = array('label' => 'Ключевое слово: '.$component['keyword'], 'type' => 'label');

        $fields[] = array('label' => 'Название', 'name' => 'name', 'value' => $component['name']);
        $fields[] = array('label' => 'Группа', 'type' => 'select', 'values' => $groups, 'name' => 'group', 'value' => $component['group'], 'extendable' => 'Другая группа');

        $fields[] = array('label' => 'Описание', 'name' => 'description', 'value' => $component['description'], 'type' => 'text');
        $fields []= array(
            'label' => 'Создает страницы?',
            'name' => 'has_page',
            'type' => 'checkbox',
            'value' => $component['has_page']
        );

        //$fields[] = array('label' => 'И еще можно сменить иконку', 'type' => 'label');

        $fields[] = array('type' => 'hidden', 'name' => 'phase', 'value' => 'settings');
        $fields[] = array('type' => 'hidden', 'name' => 'id', 'value' => $component['id']);
        
        $this->response->submenu->set_subactive('settings-'.$component['id']);

        return array('fields' => $fields, 'form_button' => array('save'));
    }
    
    public function edit_field($component) {
    	$controller = new fx_controller_admin_field();
    	$field_id = $this->input['params'][2];
    	
    	$fx_core = fx_core::get_object();
    	$field = fx::data('field')->get_by_id($field_id);
    	
    	$result = $controller->edit(array('id' => $field_id));
    	$result['form_button'] = array('save');
    	
    	$submenu = self::get_component_submenu($component);
    	$this->response->breadcrumb->add_item($submenu['fields']['title'], $submenu['fields']['url']);
    	
    	$this->response->breadcrumb->add_item($field['name']);
    	
    	$this->response->submenu->set_subactive('field-'.$field_id);
    	
    	return $result;
    }

}

?>

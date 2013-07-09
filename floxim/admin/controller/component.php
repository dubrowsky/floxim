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
        $field['labels'] = array('name' => fx::lang('Название', 'system'), 'buttons' => array('type' => 'buttons'));
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
                array('name' => fx::lang('новый','system'), 'options' => array('source' => 'new')),
                array('name' => fx::lang('импортировать','system'), 'options' => array('source' => 'import')),
                array('name' => fx::lang('установить с FloximStore','system'), 'options' => array('source' => 'store'))
        );

        $result = array('fields' => $fields, 'buttons' => $buttons, 'buttons_pulldown' => $buttons_pulldown);

        $this->response->breadcrumb->add_item(self::_essence_types($essence), '#admin.'.$essence.'.group');
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
				'fields' => fx::lang('Поля','system'),
				'settings' => fx::lang('Настройки','system')
			), 
			'widget' => array(
				'settings' => fx::lang('Настройки','system')
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
                $fields[] = array('name' => 'importfile', 'type' => 'file', 'label' => fx::lang('Файл','system'));
                $fields[] = $this->ui->hidden('action', 'import');
                break;
            case 'store':
                $fields[] = $this->ui->store('component');
                break;
            default:
                $input['source'] = 'new';
                $groups = fx::data('component')->get_all_groups();

                $fields[] = $this->ui->hidden('action', 'add');
                $fields[] = array('label' => fx::lang('Название компонента (по-русски)','system'), 'name' => 'name');
                $fields[] = array('label' => fx::lang('Название сущности создаваемой компонентом (по-русски)','system'), 'name' => 'item_name');
                $fields[] = array('label' => fx::lang('Ключевое слово','system'), 'name' => 'keyword');
                $fields[] = array('label' => fx::lang('Группа','system'), 'type' => 'select', 'values' => $groups, 'name' => 'group', 'extendable' => fx::lang('Другая группа','system'));
        }

        $fields[] = $this->ui->hidden('source', $input['source']);
        $fields[] = $this->ui->hidden('posting');
        $fields[]= $this->_get_parent_component_field();


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
    

    protected static function _essence_types( $key = null ) {
        $arr = array (
            'widget' => fx::lang('Виджеты','system'),
            'component' => fx::lang('Компоненты','system')
        );
        return ( empty($key) ? $arr : $arr[$key] );
    }
    
    public static function make_breadcrumb($component, $action, $breadcrumb) {
    	$essence_code = str_replace('fx_','',get_class($component));
    	$submenu = self::get_component_submenu($component);
        $submenu_first = current($submenu);
    	$breadcrumb->add_item(self::_essence_types($essence_code), '#admin.'.$essence_code.'.group');
        $breadcrumb->add_item($component['group'], '#admin.'.$essence_code.'.group('.md5($component['group']).')');
        $breadcrumb->add_item($component['name'], $submenu_first['url']);
        if (isset($submenu[$action])) {
			$breadcrumb->add_item($submenu[$action]['title'], $submenu[$action]['url']);
        }
    }

    public function add_save($input) {
        $result = array('status' => 'ok');

        $data['name'] = trim($input['name']);
        $data['keyword'] = trim($input['keyword']);

        $data['group'] = $input['group'];
        if (($data['group'] == 'fx_new') && $input['fx_new_group']) {
            $data['group'] = $input['fx_new_group'];
        }
        $data['parent_id'] = $input['parent_id'];
        $data['item_name'] = $input['item_name'];

        $component = fx::data('component')->create($data);

        if (!$component->validate()) {
            $result['status'] = 'error';
            $result['errors'] = $component->get_validate_error();
            return $result;
        }

        try {
            $component->save();
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
        $component['parent_id'] = $input['parent_id'];
        $component['description'] = $input['description'];
        $component['item_name'] = $input['item_name'];
        $component->save();
        return array('status' => 'ok');
    }

    public function import_save($input) {
        $file = $input['importfile'];
        if (!$file) {
            $result = array('status' => 'error');
            $result['text'][] = fx::lang('Ошибка при создании временного файла','system');
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

    public function fields($component) {
        $controller = new fx_controller_admin_field();
        $this->response->submenu->set_subactive('fields');
        return $controller->items($component);
    }
    
    protected function _get_parent_component_field($component = null) {
        $field = array(
            'label' => fx::lang('Компонент-родитель','system'),
            'name' => 'parent_id',
            'type' => 'select',
            'values' => array('' => fx::lang('--нет--','system'))
        );
        $c_finder = fx::data('component');
        if ($component) {
            $c_finder->where('id', $component['id'], '!=');
            $field['value'] = $component['parent_id'];
        }
        $field['values'] = array_merge(
                array(array('', fx::lang('--нет--','system'))),
                $c_finder->get_select_values()
        );
        return $field;
    }

    public function settings($component) {
        $groups = fx::data('component')->get_all_groups();

        $fields[] = array('label' => fx::lang('Ключевое слово:','system') . ' '.$component['keyword'], 'type' => 'label');
        $fields[] = array('label' => fx::lang('Название компонента','system'), 'name' => 'name', 'value' => $component['name']);
        $fields[] = array('label' => fx::lang('Название сущности создаваемой компонентом','system'), 'name' => 'item_name', 'value' => $component['item_name']);
        $fields[] = array('label' => fx::lang('Группа','system'), 'type' => 'select', 'values' => $groups, 'name' => 'group', 'value' => $component['group'], 'extendable' => fx::lang('Другая группа','system'));
        $fields[] = array('label' => fx::lang('Описание','system'), 'name' => 'description', 'value' => $component['description'], 'type' => 'text');
        
        $fields []= $this->_get_parent_component_field($component);

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
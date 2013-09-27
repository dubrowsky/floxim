<?php

class fx_controller_admin_component extends fx_controller_admin {

    /**
     * Список всех компонентов И ВИДЖЕТОВ (!) или компонентов определенной группы
     */
    public function group($input) {
        $essence = $this->essence_type;
        $finder = fx::data($essence);
        if ($input['params'][0]) {
            $finder->where('MD5(`group`)', $input['params'][0]);
        }
        $components = $finder->all();

        $field = array('type' => 'list', 'filter' => true);
        $field['labels'] = array(
            'name' => fx::lang('Name', 'system'), 
            'buttons' => array('type' => 'buttons')
        );
        $field['values'] = array();
        $field['essence'] = $essence;
        foreach ($components as $v) {
            $submenu = self::get_component_submenu($v);
            $submenu_first = current($submenu);
            $r = array(
                'id' => $v['id'],
                'name' => array(
                    'name' => $v['name'],
                    'url' => $submenu_first['url']
                )
            );
            
            $r['buttons'] = array();
            foreach ($submenu as $submenu_item) {
                if (!$submenu_item['parent']) {
                    $r['buttons'] []= array(
                        'type' => 'button', 
                        'label' => $submenu_item['title'], 
                        'url' => $submenu_item['url']
                    );
                }
            }
            
            $field['values'][] = $r;
        }
        $fields[] = $field;

        $this->response->add_buttons(array(
            array(
                'key' => "add", 
                'title' => fx::lang('Add new '.$essence, 'system'),
                'url' => '#admin.'.$essence.'.add'
            ),
            "delete"
        ));
        
        $result = array('fields' => $fields);

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
                'settings' => fx::lang('Settings','system'),
                'fields' => fx::lang('Fields','system'),
                //'actions' => fx::lang('Component actions', 'system'),
                'templates' => fx::lang('Templates', 'system')
            ), 
            'widget' => array(
                'settings' => fx::lang('Settings','system')
            )
        );
		
        $res = array();
        foreach($titles[$essence_code] as $code => $title) {
            $res[$code]= array(
                'title' => $title,
                'code' => $code,
                'url' => $essence_code.'.edit('.$component['id'].','.$code.')',
                'parent' => null
            );
            if ($code == 'fields') {
                foreach ($component->fields() as $v) {
                    $res['field-'.$v['id']] = array(
                        'title' => $v['name'], 
                        'code' => 'field-'.$v['id'],
                        'url' => 'component.edit('.$component['id'].',edit_field,'.$v['id'].')', 
                        'parent' => 'fields'
                    );
                }
            }
        }
	return $res;
    }
    
    protected function _get_component_templates($component) {
        $controller_name = 'component_'.$component['keyword'];
        $controller = fx::controller($controller_name);
        $actions = $controller->get_actions();
        $templates = array();
        foreach (array_keys($actions) as $action_code) {
            $action_controller = fx::controller($controller_name.'.'.$action_code);
            $action_templates = $action_controller->get_available_templates();
            foreach ($action_templates as $atpl) {
                $templates[$atpl['full_id']] = $atpl;
            }
        }
        return fx::collection($templates);
    }

    public function add($input) {
        $fields = array();

        switch ($input['source']) {
            case 'import':
                $fields[] = array('name' => 'importfile', 'type' => 'file', 'label' => fx::lang('File','system'));
                $fields[] = $this->ui->hidden('action', 'import');
                break;
            case 'store':
                $fields[] = $this->ui->store('component');
                break;
            default:
                $input['source'] = 'new';
                $groups = fx::data('component')->get_all_groups();

                $fields[] = $this->ui->hidden('action', 'add');
                $fields[] = array('label' => fx::lang('Component name','system'), 'name' => 'name');
                $fields[] = array('label' => fx::lang('Name of an entity created by the component','system'), 'name' => 'item_name');
                $fields[] = array('label' => fx::lang('Keyword','system'), 'name' => 'keyword');
                $fields[] = array('label' => fx::lang('Group','system'), 'type' => 'select', 'values' => $groups, 'name' => 'group', 'extendable' => fx::lang('Another group','system'));
        }

        $fields[] = $this->ui->hidden('source', $input['source']);
        $fields[] = $this->ui->hidden('posting');
        $fields[] = $this->_get_parent_component_field();
        
        $essence =$this->essence_type;
        $fields[] = $this->ui->hidden('essence', $essence);
        
        $this->response->breadcrumb->add_item(
            self::_essence_types($essence), 
            '#admin.'.$essence.'.group'
        );
        $this->response->breadcrumb->add_item(
            fx::lang('Add new '.$essence, 'system')
        );
        
        $this->response->submenu->set_menu($essence);
        $this->response->add_form_button('save');

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
            $result = call_user_func(array($this, $action), $component, $input);
        }
        
        $result['tree']['mode'] = $essence_code.'-'.$component['id'];
        $this->response->submenu->set_menu($essence_code.'-'.$component['id']);
        
        return $result;
    }
    
    protected static function _essence_types( $key = null ) {
        $arr = array (
            'widget' => fx::lang('Widgets','system'),
            'component' => fx::lang('Components','system')
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
            $result['text'][] = fx::lang('Error creating a temporary file','system');
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
        $controller = new fx_controller_admin_field(
             array(
                 'essence' => $component,
                 'do_return' => true
            ),
            'items'
        );
        $this->response->submenu->set_subactive('fields');
        return $controller->process();
    }
    
    public function add_field($component) {
        $controller = new fx_controller_admin_field(
            array(
                'to_id' => $component['id'],
                'to_essence' => 'component',
                'do_return' => true
            ),
            'add'
        );
        $this->response->breadcrumb->add_item(
            fx::lang('Fields', 'system'),
            '#admin.component.edit('.$component['id'].',fields)'
        );
        $this->response->breadcrumb->add_item(
            fx::lang('Add new field', 'system')
        );
        return $controller->process();
    }
    
    public function templates($component, $input) {
        $this->response->submenu->set_subactive('templates');
        if (isset($input['params'][2])) {
            return $this->template(array('template_full_id' => $input['params'][2]));
        }
        $templates = $this->_get_component_templates($component);
        $visuals = fx::data('infoblock_visual')->
                where('template', $templates->get_values('full_id'))->
                all();
        $field = array('type' => 'list', 'filter' => true);
        $field['labels'] = array(
            'name' => fx::lang('Name', 'system'),
            'action' => fx::lang('Action', 'system'),
            'type' => fx::lang('Type', 'system'),
            'source' => fx::lang('Source', 'system'),
            'file' => fx::lang('File', 'system'),
            'used' => fx::lang('Used', 'system')
        );
        $field['values'] = array();
        foreach ($templates as $tpl) {
            $r = array(
				'id' => $tpl['full_id'],
				'name' => array(
                    'name' => $tpl['name'],
                    'url' => 'component.edit('.$component['id'].',templates,'.$tpl['full_id'].')', 
                ),
                'action' => preg_replace("~^.+\.~", '', $tpl['of']),
                'used' => count($visuals->find('template', $tpl['full_id']))
			);
            $owner_com = preg_replace("~\..+$~", '', $tpl['of']);
            $owner_com = preg_replace("~^component_~", '', $owner_com);
            if ($owner_com == $component['keyword']) {
                $r['type'] = 'Own template';
            } else {
                $r['type'] = 'Inherited from '.$owner_com;               
            }
            if (preg_match("~^layout_~", $tpl['full_id'])) {
                $layout_code = 
                    preg_replace('~\..+$~', '', 
                        preg_replace('~layout_~', '', $tpl['full_id'])
                    );
                $r['source'] = 
                            fx::data('layout')->
                                where('keyword', $layout_code)->
                                one()->
                                get('name') . ' (layout)';
            } else {
                $com_code = 
                    preg_replace('~\..+$~', '', 
                        preg_replace('~component_~', '', $tpl['full_id'])
                    );
                $r['source'] = 
                           fx::data('component', $com_code)->get('name');
            }
            $r['file'] = str_replace(fx::config()->DOCUMENT_ROOT, '', $tpl['file']);
            $field['values'][] = $r;
        }
        return array('fields' => array('templates' => $field));
    } 
    
    protected function _get_template_info($full_id) {
        $tpl = fx::template($full_id);
        if (!$tpl) {
            return;
        }
        $info = $tpl->get_info();
        if (!isset($info['file']) || !isset($info['offset'])) {
            return;
        }
        $res = array();
        $source = file_get_contents($info['file']);
        $res['file'] = $info['file'];
        $res['hash'] = md5($source);
        $res['full'] = $source;
        $offset = explode(',', $info['offset']);
        $length = $offset[1]-$offset[0];
        $res['source'] = mb_substr($source, $offset[0], $length);
        $res['start'] = $offset[0];
        $res['length'] = $length;
        return $res;
    }
    
    public function template($input) {
        $template_full_id = $input['template_full_id'];
        $this->response->breadcrumb->add_item($template_full_id);
        $info = $this->_get_template_info($template_full_id);
        if (!$info){
            return;
        }
        $fields = array(
            $this->ui->hidden('essence', 'component'),
            $this->ui->hidden('action', 'template'),
            $this->ui->hidden('data_sent', '1'),
            $this->ui->hidden('template_full_id', $template_full_id),
            $this->ui->hidden('hash', $info['hash']),
            'source' => array(
                'type' => 'text',
                'value' => $info['source'],
                'name' => 'source',
                'code' => 'htmlmixed'
            )
        );
        $this->response->add_fields($fields);
        $this->response->add_form_button('save');
    }
    
    public function template_save($input) {
        $info = $this->_get_template_info($input['template_full_id']);
        if (!$info) {
            return;
        }
        if ($info['hash'] !== $input['hash']) {
            die("Hash error");
        }
        $res = mb_substr($info['full'], 0, $info['start']);
        $res .= $input['source'];
        $res .= mb_substr($info['full'], $info['start']+$info['length']);
        $fh = fopen($info['file'], 'w');
        fputs($fh, $res);
        fclose($fh);
        return array('status' => 'ok');
    }
    
    protected function _get_parent_component_field($component = null) {
        $field = array(
            'label' => fx::lang('Parent component','system'),
            'name' => 'parent_id',
            'type' => 'select',
            'values' => array('' => fx::lang('--no--','system'))
        );
        $c_finder = fx::data('component');
        if ($component) {
            $c_finder->where('id', $component['id'], '!=');
            $field['value'] = $component['parent_id'];
        }
        $field['values'] = array_merge(
                array(array('', fx::lang('--no--','system'))),
                $c_finder->get_select_values()
        );
        return $field;
    }

    public function settings($component) {
        $groups = fx::data('component')->get_all_groups();

        $fields[] = array('label' => fx::lang('Keyword:','system') . ' '.$component['keyword'], 'type' => 'label');
        $fields[] = array('label' => fx::lang('Component name','system'), 'name' => 'name', 'value' => $component['name']);
        $fields[] = array('label' => fx::lang('Name of entity created by the component','system'), 'name' => 'item_name', 'value' => $component['item_name']);
        $fields[] = array('label' => fx::lang('Group','system'), 'type' => 'select', 'values' => $groups, 'name' => 'group', 'value' => $component['group'], 'extendable' => fx::lang('Another group','system'));
        $fields[] = array('label' => fx::lang('Description','system'), 'name' => 'description', 'value' => $component['description'], 'type' => 'text');
        
        $fields []= $this->_get_parent_component_field($component);

        $fields[] = array('type' => 'hidden', 'name' => 'phase', 'value' => 'settings');
        $fields[] = array('type' => 'hidden', 'name' => 'id', 'value' => $component['id']);
        
        $this->response->submenu->set_subactive('settings-'.$component['id']);
        $fields[] = $this->ui->hidden('essence', 'component');
        $fields[] = $this->ui->hidden('action', 'edit_save');

        return array('fields' => $fields, 'form_button' => array('save'));
    }
    
    public function edit_field($component) {
    	$controller = new fx_controller_admin_field();
    	$field_id = $this->input['params'][2];
    	
    	$field = fx::data('field', $field_id);
    	
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
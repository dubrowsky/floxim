<?php

class fx_controller_admin_field extends fx_controller_admin {

    public function items( $input ) {
        $essence = $input['essence'];
        
        $items = $essence->fields();
        
        $ar = array('type' => 'list', 'filter' => true, 'sortable' => true);
        
        $essence_code = str_replace('fx_','',get_class($essence));
        
        $ar['essence'] = 'field';
        $ar['values'] = array();
        $ar['labels'] = array(
            'name' => fx::lang('Name','system'), 
            'label' => fx::lang('Description','system'),
            'type' => fx::lang('Type','system')
        );
        foreach ( $items as $field ) {
            $r = array(
                'id' => $field->get_id(), 
                'name' => array(
                    'name' => $field->get_name(), 
                    'url' =>  '#admin.'.$essence_code.'.edit('.$essence['id'].',edit_field,'.$field->get_id().')'
                ),
                'label' => $field->get_description(), 
                'type' => fx::lang("FX_ADMIN_FIELD_".strtoupper($field->get_type(false)), 'system')
            );
            $ar['values'][] = $r;
        }
        
        $result['fields'] = array($ar);
        $this->response->add_buttons(
            array(
                array(
                    'key' => 'add', 
                    'title' => fx::lang('Add new field', 'system'),
                    'url' => '#admin.'.$essence_code.'.edit('.$essence['id'].',add_field)'
                ),
                "delete"
            )
        );
        return $result;
    }
    
    public function add ( $input ) {
        $fields = $this->_form();

        $fields[] = $this->ui->hidden('action', 'add');
        $fields[] = $this->ui->hidden('to_essence', $input['to_essence']);
        $fields[] = $this->ui->hidden('to_id', $input['to_id']);
        $this->response->add_form_button('save');
        return array('fields' => $fields);
    }
    
    
    protected function _form ( $info = array() ) {
        $fields[] = $this->ui->input('name', fx::lang('Field keyword','system'), $info['name']);
        $fields[] = $this->ui->input('description', fx::lang('Description','system'), $info['description']);
        
        $fields []= array(
            'type' => 'select',
            'name' => 'form_tab',
            'label' => 'Form col',
            'values' => array(
                array('', '-auto-'),
                array(1, '1'),
                array(2, '2'),
                array(3, '3'),
                array(4, '4')
            ),
            'value' => $info['form_tab']
        );
        
        $finder = fx_data::optional('datatype');
        foreach ($finder->get_all() as $v ) {
            $values[$v['id']] = fx::lang("FX_ADMIN_FIELD_".strtoupper($v['name']), 'system');
        }
        $fields[] = array(
        	'type' => 'select', 
        	'name' => 'type', 
        	'label' => fx::lang('Field type','system'),
        	'values' => $values, 
        	'value' => $info['type'] ?  $info['type']  : 1, 
        	'post' => array(
        		'essence' => 'field', 
        		'id' => $info['id'], 
        		'action' => 'format_settings'
        	),
        	'change_on_render' => true
        );
        
        $values = array(
            fx_field::EDIT_ALL => fx::lang('anybody','system'), 
            fx_field::EDIT_ADMIN => fx::lang('admins only','system'), 
            fx_field::EDIT_NONE => fx::lang('nobody','system')
        );
        $fields[] = $this->ui->select(
                'type_of_edit', 
                fx::lang('Field is available for','system'), 
                $values, 
                $info['type_of_edit'] ? $info['type_of_edit'] : fx_field::EDIT_ALL  
        );
        
        $fields[] = $this->ui->hidden('posting');
        $fields[] = $this->ui->hidden('action', 'add');
        $fields[] = $this->ui->hidden('essence', 'field');
        return $fields;
    }
    
    public function add_save( $input ) {
        $params = array('format', 'type', 'not_null', 'searchable', 'default', 'type_of_edit', 'form_tab');
        $data['name'] = trim($input['name']);
        $data['description'] = trim($input['description']);
        foreach ( $params as $v ) {
            $data[$v] = $input[$v];
        }
        
        $field = fx::data('field')->create($data);
        $field['checked'] = 1;
        $field[ $input['to_essence'].'_id'] = $input['to_id'];
        $field['priority'] = fx::data('field')->next_priority();
        
        if (!$field->validate()) {
            $result['status'] = 'error';
            $result['errors'] = $field->get_validate_error();
        }
        else {
            $result = array('status' => 'ok');
            $field->save();
        }
        
        
        return $result;
    }
    
    public function edit ( $input ) {
        $field = fx::data('field')->get_by_id ( $input['id']);
        
        if ( $field ) {
            $fields = $this->_form($field);
            $fields[] = $this->ui->hidden('id',$input['id'] );
            $fields[] = $this->ui->hidden('action','edit');
        }
        else {
            $fields[] = $this->ui->error( fx::lang('Field not found','system') );
        }

        return array('fields' => $fields);
    }
    
    public function edit_save ( $input ) {
        $field = fx::data('field')->get_by_id( $input['id']);

        $params = array('name', 'description', 'format', 'type', 'not_null', 'searchable', 'default', 'type_of_edit', 'form_tab');
        $input['name'] = trim($input['name']);
        $input['description'] = trim($input['description']);
        foreach ( $params as $v ) {
            $field->set( $v, $input[$v]);
        }

        if (!$field->validate()) {
            $result['status'] = 'error';
            $result['errors'] = $field->get_validate_error();
        }
        else {
            $result = array('status' => 'ok');
            $field->save();
        }
        
        return $result;
    }
    
    public function format_settings ( $input ) {
        $fields = array();
        
        $input['id'] = intval($input['id']);
        if ( $input['id'] ) {
            $field = fx::data('field', $input['id']);
        }
        
        if ( !$input['id'] || $field['type'] != $input['type'] ) {
            if ($field && $field['type'] != $input['type']) {
                $to_key = null;
                $to_val = null;
                foreach ($field->get() as $ffk => $ffv) {
                    if ($ffv && in_array($ffk, array('component_id', 'widget_id', 'system_table_id'))) {
                        $to_key = $ffk;
                        $to_val = $ffv;
                        break;
                    }
                }
            } else {
                $to_key = $input['to_essence'].'_id';
                $to_val = $input['to_id'];
            }
            $field = fx::data('field')->create( array('type' => $input['type']));
            $field[$to_key] = $to_val;
        }
       
        $datatype = fx_data::optional('datatype')->get_by_id($input['type']);
        if ( $datatype['not_null'] ) {
            $fields[] = $this->ui->checkbox('not_null', fx::lang('Required','system'), null, $field['not_null']);
        }
        if ( $datatype['searchable'] ) {
            $fields[] = $this->ui->checkbox('searchable', fx::lang('Field can be used for searching','system'), null, $field['searchable']);
        }
        if ( $datatype['default'] ) {
            $fields[] = $this->ui->input('default', fx::lang('Default value','system'), $field['default']);
        }

        $format_settings =  $field->format_settings();  
        if ( $format_settings ) {
            foreach ( $format_settings as $v) {
                $fields[] = $v;
            }
        }
        return (array('fields' => $fields)) ;
    }
    
    public function move_save ( $input ) {
        $positions = $input['positions'];
        if ( $positions ) {  
            $priority = 0;
            foreach ( $positions as $field_id ) {
                $field = fx::data('field')->get_by_id($field_id);
                if ( $field ) {
                    $field->set('priority', $priority++ )->save();
                }
            }
        }
        return array('status' => 'ok');
    }
}

?>

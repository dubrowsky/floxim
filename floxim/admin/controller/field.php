<?php

class fx_controller_admin_field extends fx_controller_admin {

    public function items( $essence ) {
        
        $items = $essence->fields();
        
        $ar = array('type' => 'list', 'filter' => true, 'sortable' => true);
        
        $essence_code = str_replace('fx_','',get_class($essence));
        
        $ar['values'] = array();
        $ar['labels'] = array('name' => fx::lang('Имя','system'), 'label' => fx::lang('Описание','system'),'type' => fx::lang('Тип','system'));
        foreach ( $items as $field ) {
            $r = array(
                'id' => $field->get_id(), 
                'name' => array(
                	'name' => $field->get_name(), 
                	'url' =>  '#admin.'.$essence_code.'.edit('.$essence['id'].',edit_field,'.$field->get_id().')'
                ),
                'label' => $field->get_description(), 
                'type' => constant("FX_ADMIN_FIELD_".strtoupper($field->get_type(false)))
		   );
            $ar['values'][] = $r;
        }
        
        $fields[] = $ar;
        $result['labels'] = array('name' => FX_ADMIN_NAME, 'label' => FX_ADMIN_LABEL, 'type' => FX_ADMIN_TYPE);
        $result['buttons'] = array('add', 'delete');
        
        $result['buttons_action']['add']['options'] = array(
            'to_essence' => $essence_code,
            'to_id' => $essence['id'],
            'essence' => 'field',
            'action' => 'add'
        );
        //$result['buttons_action']['add']['options']['to_essence'] = $essence_code;
        //$result['buttons_action']['add']['options']['to_id'] = $essence['id'];
        $result['fields'] = $fields;
        $result['essence'] = 'field';
        return $result;
    }
    
    public function add ( $input ) {
        $fields = $this->_form();

        $fields[] = $this->ui->hidden('action', 'add');
        $fields[] = $this->ui->hidden('to_essence', $input['to_essence']);
        $fields[] = $this->ui->hidden('to_id', $input['to_id']);

        return array('fields' => $fields);
    }
    
    protected function _form ( $info = array() ) {
        $fields[] = $this->ui->input('name', fx::lang('Название на латинице','system'), $info['name']);
        $fields[] = $this->ui->input('description', fx::lang('Описание','system'), $info['description']);
        
        $finder = fx_data::optional('datatype');
        foreach ($finder->get_all() as $v ) {
            $values[$v['id']] = constant("FX_ADMIN_FIELD_".strtoupper($v['name']));
        }
        $fields[] = array(
        	'type' => 'select', 
        	'name' => 'type', 
        	'label' => fx::lang('Тип поля','system'),
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
<<<<<<< HEAD
            fx_field::EDIT_ALL => fx::lang('всем','system'), 
            fx_field::EDIT_ADMIN => fx::lang('только админам','system'), 
            fx_field::EDIT_NONE => fx::lang('никому','system')
        );
        $fields[] = $this->ui->select(
                'type_of_edit', 
                fx::lang('Поле доступно','system'), 
=======
            fx_field::EDIT_ALL => fx_lang('всем'), 
            fx_field::EDIT_ADMIN => fx_lang('только админам'), 
            fx_field::EDIT_NONE => fx_lang('никому')
        );
        $fields[] = $this->ui->select(
                'type_of_edit', 
                fx_lang('Поле доступно'), 
>>>>>>> 288c9e46e2f230f751ca01c7cc1429f3dd095c39
                $values, 
                $info['type_of_edit'] ? $info['type_of_edit'] : fx_field::EDIT_ALL  
        );
        
        $fields[] = $this->ui->hidden('posting');
        $fields[] = $this->ui->hidden('action', 'add');
        $fields[] = $this->ui->hidden('essence', 'field');
        return $fields;
    }
    
    public function add_save( $input ) {
        $params = array('format', 'type', 'not_null', 'searchable', 'default', 'type_of_edit');
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
            $fields[] = $this->ui->error( fx::lang('Поле не найдено','system') );
        }

        return array('fields' => $fields);
    }
    
    public function edit_save ( $input ) {
        $field = fx::data('field')->get_by_id( $input['id']);

        $params = array('name', 'description', 'format', 'type', 'not_null', 'searchable', 'default', 'type_of_edit');
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
            dev_log('saving field', $field);
            $field->save();
        }
        
        return $result;
    }
    
    public function format_settings ( $input ) {
        $fields = array();
        
        $input['id'] = intval($input['id']);
        if ( $input['id'] ) {
            $field = fx::data('field')->get_by_id($input['id']);
        }
        
        if ( !$input['id'] || $field['type'] != $input['type'] ) {
            $field = fx::data('field')->create( array('type' => $input['type']));
            $field[$input['to_essence'].'_id'] = $input['to_id'];
        }
       
        $datatype = fx_data::optional('datatype')->get_by_id($input['type']);
        if ( $datatype['not_null'] ) {
            $fields[] = $this->ui->checkbox('not_null', fx::lang('Обязательно для заполнения','system'), null, $field['not_null']);
        }
        if ( $datatype['searchable'] ) {
            $fields[] = $this->ui->checkbox('searchable', fx::lang('Возможен поиск по полю','system'), null, $field['searchable']);
        }
        if ( $datatype['default'] ) {
            $fields[] = $this->ui->input('default', fx::lang('Значение по умолчанию','system'), $field['default']);
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

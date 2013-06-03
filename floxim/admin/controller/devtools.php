<?php

class fx_controller_admin_devtools extends fx_controller_admin {
    
    public function sql() {
        $fields[] = $this->ui->label('Выполните здесь запрос "select login, email, name from {{user}}" без кавычек');
        $fields[] = $this->ui->code_editor('query','Запрос','',  'sql');

        $result = array('fields' => $fields, 'form_button' => array('save'));
        $result['tree']['mode'] = 'devtools';
        $result['breadcrumbs'] = array('name' => 'Командная строка SQL');
        
        return $result;
    }

    
    public function php () {
        $fields[] = $this->ui->label('Выполните запрос "echo fx_some_function();" без кавычек.');
        $fields[] = $this->ui->code_editor('phpcode', 'PHP-код', '', 'php');
        
        $result = array('fields' => $fields,  'form_button' => array('save'));
        $result['tree']['mode'] = 'devtools';
        $result['breadcrumbs'] = array('name' => 'PHP-консоль');
        
        return $result;
    }
    
     public function sql_save($input) {
        $fx_core = fx_core::get_object();

        $result = array('status' => 'ok');
        $res = $fx_core->db->get_results($input['query']);
        ob_end_clean();
        if ($fx_core->db->is_error() ) {
            $result['status'] = 'error';
            $result['text'][] = $fx_core->db->get_last_error();
        }
       
        if ( $res ) {
            $list_field = array('type' => 'table');

            $list_field['labels'] = array_keys($res[0]);
            foreach ( $res as $k => $v ) {
                $list_field['values'][] = array_values($v);
            }
            
            $result['fields'] = array($list_field);
        }
        return $result;
    }
    
    public function php_save ($input) {
        $fx_core = fx_core::get_object();
        ob_start();
        eval($input['phpcode']);
        $output = ob_get_contents();
        ob_end_clean();
        
        $fields[] = array('type' => 'table', 'values' => array(array($output)));
        
        $result['fields'] = $fields;
        $result['status'] = 'ok';
        
        return $result; 
    }

}

function fx_some_function() {
    return "Тут результат выполнения php-кода";
    
}

?>

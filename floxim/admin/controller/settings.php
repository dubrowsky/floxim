<?php

class fx_controller_admin_settings extends fx_controller_admin {

    public function system() {
        return array();
    }
    
    
    public function module ( $input ) {
        $fx_core = fx_core::get_object();
        $module_name = $input['params'][0];
        $result = $this->get_module_controller($module_name)->set_input($input)->settings($input);
        
        $result['tree']['mode'] = 'settings';
        $result['fields'][] = $this->ui->hidden('essence', 'admin_module_'.$module_name);
        $result['fields'][] = $this->ui->hidden('action', 'settings');
        $module = $fx_core->modules->get_by_keyword($module_name);
        $name = defined($module['name']) ? constant($module['name']) : $module['name'];
        $result['breadcrumbs'] = array('name' => $name);
        return $result;
        
    }
    
    protected function get_module_controller ( $name ) {
        $classname = 'fx_controller_admin_module_'.$name;
        $obj = new $classname;
        return $obj;
    }
}
?>
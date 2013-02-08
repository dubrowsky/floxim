<?php

class fx_controller_admin_administrate extends fx_controller_admin {


    public function module() {
        $fx_core = fx_core::get_object();

        $all_modules = $fx_core->modules->get_data();

        $ar = array('type' => 'list', 'filter' => true, 'tab' => 'fields');
        $ar['labels'] = array('name' => FX_ADMIN_NAME);

        foreach ($all_modules as $module) {
            $name = defined($module['name']) ? constant($module['name']) : $module['name'];
            $ar['values'][] = array('id' => $module['id'], 'name' => $name);
        }

        $fields[] = $ar;

        
        $result =array('fields' => $fields);
        
        $this->response->submenu->set_menu('administrate')->set_subactive('module');
        return $result;
    }
}

?>

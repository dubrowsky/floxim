<?php
class fx_controller_component_text extends fx_controller_component {
    public function get_action_settings($action) {
        $fields = parent::get_action_settings_list_parent();
        unset($fields['sorting']);
        unset($fields['parent_type']);
        unset($fields['parent_id']);
        return $fields;
    }
    
    public function do_listing() {
        $this->set_param('parent_type', 'current_page_id');
        $this->listen('build_query', function($q) {
           dev_log('text q', $q); 
        });
        return parent::do_listing();
    }
}
?>
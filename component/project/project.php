<?php
    
class fx_controller_component_project extends fx_controller_component_page {
    public function do_listing_last_projects () {
        $this->set_param('skip_parent_filter', true);
        $this->set_param('skip_infoblock_filter',true);
        $this->listen('query_ready', function ($query) {
           $query->limit(
                $this->get_param('limit') ? $this->get_param('limit') : 5           
           )->order('date', 'DESC');     
        });
        return parent::do_listing();;
    }
    
    
    public function settings_listing_last_projects() {
        $fields ['limit']= array(
            'type' => 'int',
            'name' => 'limit',
            'label' => 
                'limit'
        );
        return $fields;
    }
}
?>
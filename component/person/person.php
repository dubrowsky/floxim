<?php
class fx_controller_component_person extends fx_controller_component_page {
    public function do_listing() {
        $this->_with_contacts();
        return parent::do_listing();
    }
    public function do_record() {
        $this->_with_contacts();
        return parent::do_record();
    }
   
    protected function _with_contacts () {
        $this->listen('query_ready', function (fx_data $query) {
            $query->with('contacts');
        });
    }
    
    public function do_listing_on_main () {
        $this->set_param('skip_parent_filter', true);
        $this->set_param('skip_infoblock_filter',true);
        
        $this->listen('query_ready', function ($query) {
            $query->where('is_featured', 1)
                ->limit(1);        
        });
        return parent::do_listing();
    }
} 
?>
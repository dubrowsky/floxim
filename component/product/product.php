<?php
class fx_controller_component_product extends fx_controller_component_page {
    public function do_listing_by_category() {
        $this->set_param('skip_parent_filter', true);
        $this->listen('query_ready', function($query) {
            $ids = fx::data('content_classifier_linker')->
                    where('classifier_id', fx::env('page'))->
                    select('content_id')->
                    get_data()->get_values('content_id');
            $query->where('id', $ids);
        });
        $this->set_param('skip_infoblock_filter',true);
        return $this->do_listing();
    }    
    
    public function do_listing_featured_products () {
        $this->set_param('skip_parent_filter', true);
        $this->set_param('skip_infoblock_filter',true);
        $this->listen('query_ready', function ($query) {
           $query->where('is_featured', 1)->
                limit($this->get_param('limit') ? $this->get_param('limit') : 4);    
        });
        return parent::do_listing();
    }
    public function settings_listing_featured_products () {
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
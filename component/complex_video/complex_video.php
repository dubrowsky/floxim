<?php
class fx_controller_component_complex_video extends fx_controller_component_page {
    public function do_list_infoblock() {
        $this->listen('query_ready', function (fx_data $query) {
            $query->with('tags');
        });
        return parent::do_list_infoblock();
    }
    public function do_listing_by_tag() {
        $this->listen('query_ready', function($query) {
            $ids = fx::data('content_classifier_linker')->
                    where('classifier_id', fx::env('page')->get('id'))->
                    select('content_id')->
                    get_data()->get_values('content_id');
            $query->where('id', $ids);
            dev_log($query, $query->all());
        });
        $this->set_param('skip_infoblock_filter',true);
        return $this->do_list_infoblock();
    }
    
    
    
}
?>
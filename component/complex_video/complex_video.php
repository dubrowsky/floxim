<?php
class fx_controller_component_complex_video extends fx_controller_component_page {
    public function do_listing() {
        $this->listen('query_ready', function (fx_data $query) {
            $query->with('tags');
        });
        return parent::do_listing();
    }
    public function do_listing_by_tag() {
        $this->listen('query_ready', function($query) {
            $ids = fx::data('content_classifier_linker')->
                    where('classifier_id', fx::env('page'))->
                    select('content_id')->
                    get_data()->get_values('content_id');
            $query->where('id', $ids);
            dev_log($query, $query->all());
        });
        $this->set_param('skip_infoblock_filter',true);
        dev_log($this);
        return $this->do_listing();
    }
    
    
    
}
?>
<?php
class fx_controller_component_news extends fx_controller_component_publication {
    public function do_list_infoblock() {
        $this->listen('query_ready', function (fx_data $query) {
            $query->with('tags');
        });
        if ( isset($_GET['month']) ) {
            $this->listen('query_ready', function (fx_data $query) {
                list($month, $year) = explode(".", $_GET['month']);
                $start = $year.'-'.$month.'-01, 00:00:00';
                $end = $year.'-'.$month.'-'.date('t', strtotime($start)).', 23:59:59';
                $query->where('publish_date', $start, '>=');
                $query->where('publish_date', $end, '<=');
            });
        };
        $res = parent::do_list_infoblock();
        return $res;
    }
    
    public function do_listing_by_tag() {
        $this->set_param('skip_parent_filter', true);
        $this->set_param('skip_infoblock_filter',true);
        
        $this->listen('query_ready', function($query) {
            $ids = fx::data('content_classifier_linker')->
                    where('classifier_id', fx::env('page')->get('id'))->
                    select('content_id')->
                    get_data()->get_values('content_id');
            $query->where('id', $ids);
        });
        return $this->do_list_infoblock();
    }
    
    
}
?>
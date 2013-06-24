<?php
class fx_controller_component_blogpost extends fx_controller_component {
    public function do_listing() {
        $this->listen('query_ready', function (fx_data $query) {
            $query->with('tags');
        });
        if ( isset($_GET['month']) ) {
            $this->listen('build_query', function (fx_data $query) {
                list($month, $year) = explode(".", $_GET['month']);
                $start = $year.'-'.$month.'-01, 00:00:00';
                $end = $year.'-'.$month.'-'.date('t', strtotime($start)).', 23:59:59';
                $query->where('publish_date', $start, '>=');
                $query->where('publish_date', $end, '<=');
            });
        }
        return parent::do_listing();
    }

    public function do_calendar() {
        $months = $this->_get_finder()->
            select('DATE_FORMAT(`publish_date`, "%m") as month')->
            select('DATE_FORMAT(`publish_date`, "%Y") as year')->
            select('COUNT(DISTINCT({{content}}.id)) as `count`')->
            where('site_id', fx::env('site')->get('id'))->
            order('publish_date', 'DESC')->
            group('month')->group('year')->
            get_data();
    
        $base_url = fx::data(
                'content_page', 
                fx::data('infoblock', $this->param('infoblock_id'))->get('page_id')
        )->get('url');
        
        $years = new fx_collection();
        $c_full_month = isset($_GET['month']) ? $_GET['month'] : null;
        $c_year = $c_full_month ? preg_replace("~\d+\.~", '', $c_full_month) : date('Y');
        foreach ($months as $m) {
            if (!isset($years[$m['year']])) {
                $years[$m['year']] = array(
                    'year' => $m['year'],
                    'months' => new fx_collection(),
                    'active' => $c_year == $m['year']
                );
            }
            
            $full_month = $m['month'].'.'.$m['year'];
            $m['active'] = $full_month == $c_full_month;
            $m['url'] = $base_url .'?month='.$full_month;
            $years[$m['year']]['months'][] = $m;
        }
        return array('items' => $years);
    }
}
?>
<?php
class fx_controller_component_blogpost extends fx_controller_component {
    
}
/*
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
        };
        $res = parent::do_listing();
        if (isset($_GET['rss'])) {
            return $this->show_rss($res);
        }
        return $res;
    }
    
    public function do_listing_rss() {
        
    }
    
    protected function show_rss($data){
        $data['base_url'] = 'http://'.$_SERVER['HTTP_HOST'];
        $data['blog'] = $this->_get_blog_page();
        
        $template_params = 
                fx::data('infoblock', $this->get_param('infoblock_id'))->
                get_prop_inherited('visual.template_visual');
        if (!$template_params) {
            $template_params = array();
        }
        
        if (fx::is_admin() && isset($_GET['configure_rss'])) {
            return fx::template(
                    'component_blogpost.listing_rss_configurator', 
                    array_merge(
                        $template_params,
                        $data
                    )
            )->render();
        }
        
        $rss = fx::template(
                'component_blogpost.listing_rss', 
                array_merge(
                    $template_params,
                    $data
                )
        )->render();
        
        foreach (range(1, ob_get_level()) as $level) {
            ob_end_clean();
        }
        fx::http()->header('Content-Type', 'application/rss+xml');
        echo $rss;
        die();
    }
    
    public function do_listing_by_tag() {
        $this->listen('query_ready', function($query) {
            $ids = fx::data('content_tagpost')->
                    where('tag_id', fx::env('page')->get('id'))->
                    select('post_id')->
                    get_data()->get_values('post_id');
            $query->where('id', $ids);
        });
        $this->set_param('skip_infoblock_filter',true);
        return $this->do_listing();
    }
    
    protected function _get_blog_page() {
        return fx::data(
            'content_page', 
            fx::data('infoblock', $this->get_param('infoblock_id'))->get('page_id')
        );
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
    
        $base_url = $this->_get_blog_page()->get('url');
        
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
*/
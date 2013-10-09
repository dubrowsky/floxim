<?php
class fx_controller_component_news extends fx_controller_component_publication {
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
        dev_log($res);
        return $res;
    }
    
    
    public function do_listing_rss() {
        
    }
    
    protected function show_rss($data){
        $data['base_url'] = 'http://'.$_SERVER['HTTP_HOST'];
        $data['blog'] = parent::_get_publication_page();
        
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
        $this->set_param('skip_parent_filter', true);
        $this->set_param('skip_infoblock_filter',true);
        
        $this->listen('query_ready', function($query) {
            $ids = fx::data('content_classifier_linker')->
                    where('classifier_id', fx::env('page'))->
                    select('content_id')->
                    get_data()->get_values('content_id');
            $query->where('id', $ids);
        });
        return $this->do_listing();
    }
    
    public function do_listing_on_main () {
        $this->set_param('skip_parent_filter', true);
        $this->set_param('skip_infoblock_filter',true);
        
        $this->listen('query_ready', function ($query) {
            $query->where('on_main', 1)
                ->limit(1);        
        });
        return parent::do_listing();
    }
    
}
?>
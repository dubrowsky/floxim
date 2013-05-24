<?
class fx_router_front extends fx_router {

    public function route($url = null, $context = null) {
        $site = fx::data('site', $context['site_id']);
        $page = fx::data('content_page')->
                    where('url', $url)->
                    where('site_id', $site['id'])->
                    one();
        if (!$page) {
            return null;
        }
        fx::env('page', $page['id']);
        $layout_id = fx::env('layout');

        $infoblocks = $this->get_page_infoblocks($page['id'], $layout_id);
        
        
        $layout_ib = $infoblocks['layout'][0];
        return fx::controller(
            'infoblock.render', 
            array(
                'infoblock_id' => $layout_ib['id'],
                'override_params' => array(
                    'page_id' => $page['id'],
                    'layout_id' => $layout_id
                )
            )
        );
    }
    
    protected $ib_cache = array();
    
    public function  get_page_infoblocks($page_id, $layout_id) {
        $infoblocks = fx::data('infoblock')->get_for_page($page_id);
        $areas = array();
        $visual = fx::data('infoblock_visual')->
                where('infoblock_id', $infoblocks->get_values('id'))->
                where('layout_id', $layout_id)->
                all();
        foreach ($infoblocks as $ib) {
            if (($c_visual = $visual->find_one('infoblock_id', $ib['id']))) {
                $ib->set_visual($c_visual);
            } else {
                $suitable = new fx_template_suitable();
                $suitable->suit($infoblocks, $layout_id);
                echo fen_debug(
                    'no visual:', 
                    $ib->get_prop_inherited('controller'),
                    $ib->get_prop_inherited('action')
                );
                die();
            }
            
            if ($ib->get_prop_inherited('controller') == 'layout') {
                $c_area = 'layout';
            } elseif ($c_visual) {
                $c_area = $ib->get_prop_inherited('visual.area');
            } else {
                $c_area = 'unknown';
            }
            fx::dig_set($areas, $c_area.'.', $ib);
        }
        return $areas;
    }
}
?>
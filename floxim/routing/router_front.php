<?
class fx_router_front extends fx_router {

    public function route($url = null, $context = null) {
        $page = fx::data('content_page')->get_by_url($url, $context['site_id']);
        if (!$page) {
            return null;
        }
        fx::env('page', $page['id']);
        $layout_id = fx::env('layout');
        $infoblocks = $this->get_page_infoblocks($page['id'], $layout_id);
        $layout_ib = $infoblocks['layout'][0];
        fx::http()->status('200');
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
    
    public function get_path($url) {
        
    }
    
    protected $_ib_cache = array();
    
    public function  get_page_infoblocks($page_id, $layout_id) {
        $cache_key = $page_id.'.'.$layout_id;
        if (isset($this->_ib_cache[$cache_key])) {
            return $this->_ib_cache[$cache_key];
        }
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
                if ($ib->get_visual()->get('is_stub')) {
                    $suitable = new fx_template_suitable();
                    $suitable->suit($infoblocks, $layout_id);
                }
            }

            if ($ib->get_prop_inherited('controller') == 'layout') {
                $c_area = 'layout';
            } elseif ( ($visual_area = $ib->get_prop_inherited('visual.area')) ) {
                $c_area = $visual_area;
            } else {
                $c_area = 'unknown';
            }
            fx::dig_set($areas, $c_area.'.', $ib);
        }
        // force root layout infoblock
        if (!isset($areas['layout'])) {
            $lay_ib = fx::data('infoblock')
                        ->where('controller', 'layout')
                        ->where('site_id', fx::env('site_id'))
                        ->where('parent_infoblock_id', 0)
                        ->one();
            $areas['layout'] = array($lay_ib);
        }
        $this->_ib_cache[$cache_key] = $areas;
        return $areas;
    }
}
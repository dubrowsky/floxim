<?
class fx_router_front extends fx_router {

    public function route($url = null, $context = null) {
        $site = fx::data('site', $context['site_id']);
        $page = fx::data('content_page')->get('url', $url, 'site_id', $site['id']);
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
        $cache_path = $page_id.'.'.$layout_id;
        if (($cached = fx::dig($this->ib_cache, $cache_path) )) {
            return $cached;
        }
        $page = fx::data('content_page', $page_id);
        if (!$page) {
            dev_log('no pg in router_front', debug_backtrace());
            return;
        }
        $site = fx::data('site', $page['site_id']);
        $ids = $page->get_parent_ids();
        $ids []= $page['id'];
        $ids []= 0; // корень
        $infoblocks = fx::data('infoblock')->get_all(array(
            'page_id' => $ids, 
            'site_id' => $site['id'],
            'checked' => 1
        ));
        
        $areas = array();
        // получаем все привязки "инфоблок-макет"
        $visual = fx::data('infoblock2layout')->get_for_infoblocks($infoblocks);
        // id-шники наследованных блоков
        $inherited_ids = $infoblocks->find('parent_infoblock_id')->get_values('parent_infoblock_id');
        
        foreach ($infoblocks as $ib) {
            // если инфоблок наследуется одним из привязанных к текущей странице инфоблоков,
            // помечаем его как наследуемый
            if (in_array($ib['id'], $inherited_ids)) {
                $ib->is_inherited = true;
                //continue;
            }
            
            if (fx::dig($ib, 'scope.pages') == 'this' && $ib['page_id'] != $page_id) {
                continue;
            }
            $ib_visuals = array();
            $c_visual = null;
            foreach ($visual as $vis) {
                if ($vis['infoblock_id'] == $ib['id']) {
                    if ($vis['layout_id'] == $layout_id) {
                        $c_visual = $vis;
                        break;
                    }
                    $ib_visuals []= $vis;
                }
            }
            if (!$c_visual && count($ib_visuals) > 0) {
                // какой-то обход
            }
            if ($ib['controller'] == 'layout'){
                $c_area = 'layout';
            } elseif ($c_visual) {
                $ib->set_infoblock2layout($c_visual);
                //$c_area = $c_visual['area'];
                $c_area = $ib->get_prop_inherited('visual.area');
            } else {
                $c_area = 'unknown';
            }
            
            if (!isset($areas[$c_area])) {
                $areas[$c_area] = array();
            }
            $areas[$c_area][]= $ib;
        }
        dev_log('ibs after grouping', $areas);
        fx::dig_set($this->ib_cache, $cache_path, $areas);
        //$this->_show_admin_panel();
        return $areas;
    }
}
?>
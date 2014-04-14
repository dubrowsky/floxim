<?
class fx_router_infoblock extends fx_router {
    public function route($url = null, $context = null) {
        if (!preg_match("~^/\~ib/(\d+|fake(?:\-\d+)?)@(\d+)$~", $url, $ib_info)) {
            return null;
        }
        if (isset($_POST['c_url'])) {
            $_SERVER['REQUEST_URI'] = $_POST['c_url'];
            $c_url = parse_url($_POST['c_url']);
            if (isset($c_url['query'])) {
                parse_str($c_url['query'], $_GET);
            }
        }
        $ib_id = $ib_info[1];
        $page_id = $ib_info[2];
        $is_fake = preg_match("~fake~", $ib_id);
        fx::env('page', $page_id);
        
        $page_infoblocks = fx::router('front')->get_page_infoblocks(
            $page_id, 
            fx::env('layout')
        );
        fx::page()->set_infoblocks($page_infoblocks);
        
        if (!$is_fake) {
            // front end can try to reload the layout which is out of date
            // when updating from "layout settings" panel
            $infoblock = fx::data('infoblock', $ib_id);
            if ((!$infoblock && isset($_POST['infoblock_is_layout'])) || $infoblock->is_layout()) {
                $c_lay = fx::data('content_page', $page_id)->get_layout_infoblock(); //$page_infoblocks['layout'][0];
                if ($c_lay) {
                    $ib_id = $c_lay['id'];
                }
            }
        }
        
        fx::http()->status('200');
        $infoblock_overs = null;
        if (fx::is_admin() && isset($_POST['override_infoblock'])) {
            $infoblock_overs = $_POST['override_infoblock'];
            if (is_string($infoblock_overs)){
                parse_str($infoblock_overs, $infoblock_overs);
            }
            $infoblock_overs['params']['is_overriden'] = true;
        }
        $controller = fx::controller(
            'infoblock.render', 
            array(
                'infoblock_id' => $ib_id,
                'ajax_mode' => true,
                'override_infoblock' => $infoblock_overs
            )
        );
        
        return $controller;
    }
}
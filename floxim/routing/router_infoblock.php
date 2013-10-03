<?
class fx_router_infoblock extends fx_router {
    public function route($url = null, $context = null) {
        if (!preg_match("~^/\~ib/(\d+|fake)@(\d+)$~", $url, $ib_info)) {
            return null;
        }
        if (isset($_POST['c_url'])) {
            $c_url = parse_url($_POST['c_url']);
            if (isset($c_url['query'])) {
                parse_str($c_url['query'], $_GET);
            }
        }
        fx::env('page', $ib_info[2]);
        fx::http()->status('200');
        $infoblock_overs = null;
        if (fx::is_admin() && isset($_POST['override_infoblock'])) {
            parse_str($_POST['override_infoblock'], $infoblock_overs);
        }
        $controller = fx::controller(
            'infoblock.render', 
            array(
                'infoblock_id' => $ib_info[1],
                'ajax_mode' => true,
                'override_infoblock' => $infoblock_overs
            )
        );
        dev_log('ibc', $controller);
        
        return $controller;
    }
}
?>
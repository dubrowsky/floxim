<?
class fx_router_infoblock extends fx_router {
    public function route($url = null, $context = null) {
        if (!preg_match("~^/\~ib/(\d+)@(\d+)$~", $url, $ib_info)) {
            return null;
        }
        
        fx::env('page', $ib_info[2]);
        $controller = fx::controller(
            'infoblock.render', 
            array(
                'infoblock_id' => $ib_info[1],
                'ajax_mode' => true
            )
        );
        
        return $controller;
    }
}
?>
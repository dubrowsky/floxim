<?php

/**
 * Description of router_admin
 *
 * @author Nikita Dezzpil Orlov <n.dezz.orlov@gmail.com>
 */
class fx_router_error extends fx_router_front {
    
    public function route($url = null, $context = null) {
        $site = fx::env('site');
        $page404_id = $site['e404_sub_id'];
        $page = fx::data('content_page', $page404_id);
        fx::env('page', $page['id']);
        $layout_id = fx::env('layout');
        $infoblocks = $this->get_page_infoblocks($page['id'], $layout_id);
        $layout_ib = $infoblocks['layout'][0];
        $controller = fx::controller(
            'infoblock.render', 
            array(
                'infoblock_id' => $layout_ib['id'],
                'override_params' => array(
                    'page_id' => $page['id'],
                    'layout_id' => $layout_id
                )
            )
        );
        return $controller;
    }

}
?>

<?php
class fx_router_dynamic extends fx_router_front {
    public function route($url = null, $context = null) {
        if( count($_GET) > 1 )
        {
            $url = array($_GET['REQUEST_URI']);
            $site = fx::data('site', $context['site_id']);
            $action = $_GET['action'];
            if ( !empty($url) ) {
                if ( substr($url[0], -1) != '/' ) {
                    $url[1] = $url[0] . '/';
                } else {
                    $url[1] = substr($url[0], 0, strlen($url[0])-1);
                }
            }
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
        } else {
            return null;
        }
    }
}
<?php
class fx_router_error extends fx_router_front {
    public function route($url = null, $context = null) {
        fx::http()->status('404');
        $site = fx::data(
                'site', 
                isset($context['site_id']) ? $context['site_id'] : fx::env('site')
        );
        $error_page = fx::data('content_page', $site['error_page_id']);
        return fx::router('front')->route($error_page['url'], $context);
    }
}
?>

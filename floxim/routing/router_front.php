<?
class fx_router_front extends fx_router {
    public function route($url) {
        $site = fx::env('site');
        $page = fx::data('content_page')->get('url', $url, 'site_id', $site['id']);
        if (!$page) {
            //dev_log('page not found', $url);
            return null;
        }
        return fx::controller('page.show', array('page_id'=>$page['id']));
    }
}
?>
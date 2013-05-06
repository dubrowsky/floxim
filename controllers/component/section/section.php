<?
class fx_controller_component_section extends fx_controller_component {
    public function do_breadcrumbs() {
        $page_id = fx::env('page');
        $essence_page = fx::data('content_page',$page_id);
        $parents = $essence_page->get_parent_ids();
        $pages = fx::data('content_page', $parents);
        $pages[]= $essence_page;
        return array('items' => $pages);
    }
}
?>
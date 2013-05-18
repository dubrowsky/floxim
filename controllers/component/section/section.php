<?
class fx_controller_component_section extends fx_controller_component {
    
    public function do_listing() {
        $this->listen('items_ready', function($items) {
            $c_page_id  = fx::env('page');
            $path = fx::data('content_page', $c_page_id)->get_parent_ids();
            $path []= $c_page_id;
            if ( ($active_item = $items->find_one('id', $path)) ) {
                $active_item->set('active',true);
            }
        });
        return parent::do_listing();
    }
    
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
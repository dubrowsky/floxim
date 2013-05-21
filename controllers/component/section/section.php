<?
class fx_controller_component_section extends fx_controller_component {
    
    /*
     * Отключаем "Отдельную страницу" для компонента
     */
    public function info_record() {
        return array('disabled' => true);
    }
    
    public function info_listing() {
        return array(
            'name' => 'Меню'
        );
    }
    
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
    
    public function info_breadcrumbs() {
        return array(
            'name' => 'Хлебные крошки',
            'description' => 'Отображает путь до текущей страницы в структуре сайта'
        );
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
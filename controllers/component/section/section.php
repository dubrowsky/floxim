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

    public function get_action_settings($action)
    {
        $fields = parent::get_action_settings_list_parrent();
        $fields['submenu'] = array(
            'name' => 'submenu',
            'label' => 'Показывать подразделы',
            'type' => 'checkbox',
        );
        return $fields;
    }
    
    public function do_listing() {
        if ( $this->input['submenu'] == 1 ) {
            $this->listen('build_query', function($f) {
                $f->with('submenu');
            });
        }
        $this->listen('items_ready', function($items) {
            $c_page_id  = fx::env('page');
            $path = fx::data('content_page', $c_page_id)->get_parent_ids();
            $path []= $c_page_id;

            if ( ($active_item = $items->find_one('id', $path)) ) {
                $active_item->set('active',true);
            }
            if ( ($active_item = $items->find_one('alias', $path)) ) {
                $active_item->set('active',true);
            }

            foreach ( $items as $item ) {
                if ( !empty($item['alias']) ) {
                    $alias = fx::data('content_' . $this->get_content_type(), $item['alias']);
                    $item['url'] = $alias['url'];
                    $item['name'] = empty($item['name']) ? $alias['name'] : $item['name'];
                }
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
        if ( !($page_id = $this->param('page_id'))) {
            $page_id = fx::env('page');
        }
        $essence_page = fx::data('content_page',$page_id);
        $parents = $essence_page->get_parent_ids();
        $pages = fx::data('content_page', $parents);
        $essence_page['active'] = true;
        $pages[]= $essence_page;
        return array('items' => $pages);
    }
}
?>
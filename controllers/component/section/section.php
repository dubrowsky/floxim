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
            'name' => fx_lang('Меню')
        );
    }

    public function get_action_settings($action)
    {
        $fields = parent::get_action_settings($action);
        if ($action == 'listing') {
            foreach (
                array('parent_type', 'parent_id', 'sorting', 'sorting_dir', 'limit', 'show_pagination')
                as $fk) {
                    unset($fields[$fk]);
            }
            $fields['submenu'] = array(
                'name' => 'submenu',
                'label' => fx_lang('Подразделы'),
                'type' => 'select',
                'values' => array(
                    'none' => fx_lang('Не показывать'),
                    'active' => fx_lang('Показывать у активного'),
                    'all' => fx_lang('Показывать у всех')
                )
            );
        }
        return $fields;
    }
    
    public function do_listing() {
        $this->set_param('sorting', 'manual');
        $this->set_param('parent_type', 'mount_page_id');
        $controller = $this;
        $c_page_id  = fx::env('page');
        $path = fx::data('content_page', $c_page_id)->get_parent_ids();
        $path []= $c_page_id;
        $this->listen('build_query', function($f) use ($controller, $path) {
            $submenu = $controller->param('submenu');
            switch ($submenu) {
                case 'none':
                    return;
                case 'all':
                    $f->with('submenu');
                    return;
                case 'active':
                    $sub_f = fx::data('content_section')->where('parent_id', $path);
                    $f->with('submenu', $sub_f);
                    return;
            }
        });
        $this->listen('items_ready', function($items) use ($path, $controller) {
            if ( ($active_item = $items->find_one('id', $path)) ) {
                $active_item->set('active',true);
                
                $controller->accept_content(array(
                    'title' => "Подраздел &rarr; ".$active_item['name'],
                    'parent_id' => $active_item['id'],
                ));
            }
        });
        return parent::do_listing();
    }
    
    public function info_breadcrumbs() {
        return array(
            'name' => fx_lang('Хлебные крошки'),
            'description' => fx_lang('Отображает путь до текущей страницы в структуре сайта')
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
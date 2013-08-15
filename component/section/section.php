<?
class fx_controller_component_section extends fx_controller_component_page {

    /*
     * Отключаем "Отдельную страницу" для компонента
     */
    public function info_record() {
        return array('disabled' => true);
    }
    
    public function info_listing() {
        return array(
            'name' => fx::lang('Меню','component_section')
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
                'label' => fx::lang('Подразделы','component_section'),
                'type' => 'select',
                'values' => array(
                    'none' => fx::lang('Не показывать','component_section'),
                    'active' => fx::lang('Показывать у активного','component_section'),
                    'all' => fx::lang('Показывать у всех','component_section')
                )
            );/*
            $fields['submenu_level'] = array(
                'name' => 'submenu_level',
                'label' => fx::lang('Уровень вложенности', 'component_section'),
                'type' => 'select',
                'values' => array(
                    array('2', fx::lang('2 уровня', 'component_section')),
                    array('3', fx::lang('3 уровня', 'component_section')),
                    array('onemore', fx::lang('Текущий +1', 'component_section')),
                    array('infinity', fx::lang('Без ограничения', 'component_section'))
                ),
                'parent' => array('submenu' => '!=none')
            );*/
        } elseif ($action == 'breadcrumbs') {
            $fields = array(
                'header_only' => array(
                    'name' => 'header_only',
                    'type' => 'checkbox',
                    'label' => fx::lang('Показывать только заголовок?', 'component_section'),
                ),
                'hide_on_index' => array(
                    'name' => 'hide_on_index',
                    'type' => 'checkbox',
                    'label' => fx::lang('Скрыть на главной?', 'component_section')
                )
            );
        }
        return $fields;
    }
    
    public function do_listing() {
        
        $c_page_id  = fx::env('page');
        $path = fx::data('content_page', $c_page_id)->get_parent_ids();
        $path []= $c_page_id;
        
        $submenu_type = $this->get_param('submenu');
        if ($submenu_type == 'none') {
            $this->set_param('parent_type', 'mount_page_id');
        }
        
        $this->set_param('sorting', 'manual');
        
        $this->listen('query_ready', function($q) use ($path, $submenu_type) {
            switch ($submenu_type) {
                case 'all':
                    $q->clear_where('parent_id');
                    break;
                case 'active':
                    $q->clear_where('parent_id')->where('parent_id', $path);
                    break;
                default:
                    dev_log($q);
                    break;
            }
        });
        
        $this->listen('items_ready', function($items, $ctr) use ($path, $submenu_type) {
            foreach ($items as $item) {
                if (in_array($item['id'], $path)) {
                    $item['active'] = true;
                    if ($ctr->get_param('submenu') !== 'none') {
                        $ctr->accept_content(array(
                            'title' => fx::lang('Подраздел','component_section') 
                                        . ' &rarr; ' . $item['name'],
                            'parent_id' => $item['id']
                        ));
                    }
                } else {
                    $item['active'] = false;
                }
            }
            if ($submenu_type != 'none') {
                fx::data('content_page')->make_tree($items);
            }
        });
        return parent::do_listing();
    }
    
    public function info_breadcrumbs() {
        return array(
            'name' => fx::lang('Хлебные крошки','component_section'),
            'description' => fx::lang('Отображает путь до текущей страницы в структуре сайта','component_section')
        );
    }
    
    public function do_breadcrumbs() {
        if ( !($page_id = $this->get_param('page_id'))) {
            $page_id = fx::env('page');
        }
        $essence_page = fx::data('content_page',$page_id);
        $parents = $essence_page->get_parent_ids();
        if (count($parents) == 0 && $this->get_param('hide_on_index')) {
            $this->_meta['disabled'] = true;
            return array();
        }
        $essence_page['active'] = true;
        if ($this->get_param('header_only')) {
            $pages = new fx_collection(array($essence_page));
        } else {
            $pages = fx::data('content_page', $parents);
            $pages[]= $essence_page;
        }
        return array('items' => $pages);
    }
}
?>
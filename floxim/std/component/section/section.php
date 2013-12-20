<?
class fx_controller_component_section extends fx_controller_component_page {

    /*
     * Отключаем "Отдельную страницу" для компонента
     */
    
    public function do_list_infoblock() {
        $this->set_param('parent_id', false);
        $c_page_id  = fx::env('page')->get('id');
        $path = fx::env('page')->get_parent_ids();
        $path []= $c_page_id;
        $submenu_type = $this->get_param('submenu');
        if ($submenu_type == 'none') {
            $this->set_param('parent_type', 'mount_page_id');
        }
        
        //$this->set_param('sorting', 'manual');
        
        $this->listen('query_ready', function($q) use ($path, $submenu_type) {
            switch ($submenu_type) {
                case 'all':
                    $q->clear_where('parent_id');
                    break;
                case 'active':
                    $q->clear_where('parent_id')->where('parent_id', $path);
                    break;
            }
        });
        
        $this->listen('items_ready', function($items, $ctr) use ($path, $submenu_type) {
            foreach ($items as $item) {
                if (in_array($item['id'], $path)) {
                    $item['active'] = true;
                    if ($ctr->get_param('submenu') !== 'none') {
                        $ctr->accept_content(array(
                            'title' => fx::lang('Subsection','component_section') 
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
        return parent::do_list_infoblock();
    }

    public function do_list_filtered () {
        $c_page_id  = fx::env('page')->get('id');
        $path = fx::env('page')->get_parent_ids();
        $path []= $c_page_id;
        $submenu_type = $this->get_param('submenu');
        $this->listen('query_ready', function($q) use ($path, $submenu_type) {
            switch ($submenu_type) {
                case 'all':
                    $q->clear_where('parent_id');
                    break;
                case 'active':
                    $q->clear_where('parent_id')->where('parent_id', $path);
                    break;
            }
        });
        $this->listen('items_ready', function($items) use ($path, $submenu_type) {
            foreach ($items as $item) {
                if (in_array($item['id'], $path))
                    $item['active'] = true;
            }
            $items->make_tree();
            if ($submenu_type == 'none')
                $items->apply(function($item){
                    unset($item['children']);
                });
        });
        return parent::do_list_filtered();
    }

    public function do_list_selected () {
        $c_page_id  = fx::env('page')->get('id');
        $path = fx::env('page')->get_parent_ids();
        $path []= $c_page_id;
        $submenu_type = $this->get_param('submenu');
        $this->listen('query_ready', function($q) use ($path, $submenu_type) {
            switch ($submenu_type) {
                case 'all':
                    $q->clear_where('parent_id');
                    break;
                case 'active':
                    $q->clear_where('parent_id')->where('parent_id', $path);
                    break;
            }
        });
        $ctr = $this;
        $recurcive_children = function ($items) use (&$recurcive_children, $ctr) {
            $sub_items = $ctr
                    ->get_finder()
                    ->where('parent_id', $items->get_values('id'))
                    ->all();
            if (!count($sub_items)>0) {
                return;
            }
            $items->attache_many($sub_items, 'parent_id', 'children');
            $recurcive_children($sub_items);

        };
        $this->listen('items_ready', function ($items) use ($recurcive_children, $submenu_type) {
            $recurcive_children($items);
            if ($submenu_type == 'none')
                $items->apply(function($item){
                    unset($item['children']);
                });
        });
        return parent::do_list_selected();
    }

    public function do_list_submenu() {
        $source = $this->get_param('source_infoblock_id');
        $path = fx::env('page')->get_path();
        if (isset($path[1])) {
            $this->listen('query_ready', function($q) use ($path, $source){
                $q->where('parent_id', $path[1]->get('id'))->where('infoblock_id', $source);
            });
        }
        $paths = fx::env('page')->get_parent_ids();
        $paths[] = fx::env('page')->get('id');
        $this->listen('items_ready', function($items, $ctr) use ($paths) {
            foreach ($items as $item) {
                if (in_array($item['id'], $paths)) {
                    $item['active'] = true;
                }
            }
        });
        return $this->do_list();
    }
    
    public function do_breadcrumbs() {
        if ( !($page_id = $this->get_param('page_id'))) {
            $page_id = fx::env('page_id');
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
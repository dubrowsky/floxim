<?
class fx_controller_component_section extends fx_controller_component_page {

    /*
     * Отключаем "Отдельную страницу" для компонента
     */

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
                'label' => fx::lang('Subsections','component_section'),
                'type' => 'select',
                'values' => array(
                    'none' => fx::lang('Don\'t show','component_section'),
                    'active' => fx::lang('Show for the active item','component_section'),
                    'all' => fx::lang('Show for all items','component_section')
                )
            );/*
            $fields['submenu_level'] = array(
                'name' => 'submenu_level',
                'label' => fx::lang('Nesting level', 'component_section'),
                'type' => 'select',
                'values' => array(
                    array('2', fx::lang('2 levels', 'component_section')),
                    array('3', fx::lang('3 levels', 'component_section')),
                    array('onemore', fx::lang('Current level +1', 'component_section')),
                    array('infinity', fx::lang('No limit', 'component_section'))
                ),
                'parent' => array('submenu' => '!=none')
            );*/
        } elseif ($action == 'breadcrumbs') {
            $fields = array(
                'header_only' => array(
                    'name' => 'header_only',
                    'type' => 'checkbox',
                    'label' => fx::lang('Show only header?', 'component_section'),
                ),
                'hide_on_index' => array(
                    'name' => 'hide_on_index',
                    'type' => 'checkbox',
                    'label' => fx::lang('Hide on the index page', 'component_section')
                )
            );
        }
        return $fields;
    }
    
    public function do_list_infoblock() {
        $this->set_param('parent_id', false);
        $c_page_id  = fx::env('page')->get('id');
        $path = fx::env('page')->get_parent_ids();
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
            }
            fx::log($q->show_query(), $q->all());
        });
        
        $this->listen('items_ready', function($items, $ctr) use ($path, $submenu_type) {
            dev_log('Menu items', $items);
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

    public function do_list_submenu() {
        $source = $this->get_param('source_infoblock_id');
        $path = fx::env('page')->get_path();
        if (isset($path[1])) {
            $this->listen('query_ready', function($q) use ($path, $source){
                $q->where('parent_id', $path[1]->get('id'))->where('infoblock_id', $source);
            });
        }
        fx::log('subm', $this);
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

    public function config_list_submenu($config) {
        $site_id = fx::env('site')->get('id');
        $sections = fx::data('infoblock')->where('site_id', $site_id)->get_content_infoblocks('section')->get_values('name', 'id');
        $config['settings']['source_infoblock_id']['values'] = $sections;
        return $config;
    }
    
    public function info_breadcrumbs() {
        return array(
            'name' => fx::lang('Bread crumbs','component_section'),
            'description' => fx::lang('Show path to the current page','component_section')
        );
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
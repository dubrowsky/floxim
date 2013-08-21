<?php

class fx_controller_admin_site extends fx_controller_admin {

    public function all() {
        $sites = fx::data('site')->get_all();

        $list = array('type' => 'list', 'filter' => true, 'tpl' => 'imgh', 'sortable' => true);
        $list['labels'] = array();

        $list['values'] = array();
        foreach ($sites as $v) {
            $text = fx::lang('Language:','system') . ' ' . $v['language'];
            if ($v['domain']) {
                $text .= "<br />".$v['domain'];
            }
            $text = '<a href="http://'.$v['domain'].'" style="color:#666;" target="_blank"> '.$v['domain'].'</a>';
            $text .=" <span style='font-size:10px; color:#777;'>&middot;</span> ".$v['language'];
            if ($v['type'] == 'mobile') $text .= "<br/>" . fx::lang('for mobile devices','system');
            $r = array(
                    'id' => $v['id'],
                    'header' => array('name' => $v['name'], 'url' => 'site.settings('.$v['id'].')'),
                    'text' => $text
            );
            $list['values'][] = $r;
        }

        $this->response->add_field($list);

        $this->response->add_buttons(
            array(
                array('key' => 'add', 'title' => fx::lang('Add new site','system')),
                'delete'
            )
        );
        $this->response->add_button_options('add', array(
            'essence' => 'site',
            'action' => 'add'
        ));
        $this->response->breadcrumb->add_item( fx::lang('Sites','system') );
        $this->response->submenu->set_menu('site');
    }

    public function add($input) {
        $fields = array();

        $fields[] = $this->ui->hidden('action', 'add');
        $fields[] = $this->ui->input('name', fx::lang('Site name','system'), fx::lang('Add new site','system'));
        $fields[] = $this->ui->input('domain', fx::lang('Domain','system'), fx::lang('Domain','system'));
        
        $fields[] = $this->ui->hidden('posting');
        $this->response->add_fields($fields);
        $this->response->dialog->set_title( fx::lang('Create a new site','system') );
    }

    public function import_save($input) {
        $file = $input['importfile'];
        if (!$file) {
            $result = array('status' => 'error');
            $result['text'][] = fx::lang('Error creating a temporary file','system');
        }

        $result = array('status' => 'ok');
        try {
            $imex = new fx_import(array('template_id' => intval($input['template_id'])));
            $imex->import_by_file($file['tmp_name']);
        } catch (Exception $e) {
            $result = array('status' => 'error');
            $result['text'][] = $e->getMessage();
        }

        return $result;
    }

    public function add_save($input) {
        $result = array('status' => 'ok');

        $site = fx::data('site')->create(array('name' => $input['name'], 'domain' => $input['domain']));

        if (!$site->validate()) {
            $result['status'] = 'error';
            $result['errors'] = $site->get_validate_error();
            return $result;
        }

        $current_site = fx::data('site')->get_by_host_name();
        $layout_id = $current_site['layout_id'];
        if (!$layout_id) {
            $layout_id = fx::data('layout')->one()->get('id');
        }
        
        $site['layout_id'] = $layout_id;
        $site['checked'] = 1;
        $site->save();

        $index_page = fx::data('content_page')->create(array(
            'name' => fx::lang('Cover Page','system'),
            'url' => '/',
            'site_id' => $site['id']
        ))->save();
        
        $error_page = fx::data('content_page')->create(array(
            'name' => fx::lang('Page not found','system'),
            'url' => '/404', 
            'site_id' => $site['id'],
            'parent_id' => $index_page['id']
        ))->save();
        
        $site['error_page_id'] = $error_page['id'];
        $site['index_page_id'] = $index_page['id'];
        
        fx::data('infoblock')->create(
            array(
                'controller' => 'layout',
                'action' => 'show',
                'name' => 'Layout',
                'site_id' => $site['id']
            )
        )->save();
        $site->save();
        return $result;
    }

    public function map($input) {
        $site = fx::data('site')->get_by_id($input['params'][0]);
        if (!$site) {
            $this->response->set_status_error( fx::lang('Site not found','system') );
            return;
        }
        $fields = array();
        $fields[] = $this->ui->tree($this->_get_site_tree($site));

        $this->response->add_fields($fields);
        $this->response->add_buttons("add,settings,on,off,delete");
        $this->response->add_button_options('add', 'site_id='.$site['id']);
        $this->response->set_essence('content');
        $this->_set_layout('map', $site);
    }
    
    protected function _set_layout($section, $site) {
    	$titles = array(
    		'map' => fx::lang('Site map','system'),
    		'settings' => fx::lang('Settings','system'),
    		'design' => fx::lang('Design','system')
		);
    	$this->response->breadcrumb->add_item( fx::lang('Sites','system'), '#admin.site.all');
        $this->response->breadcrumb->add_item($site['name'], '#admin.site.settings('.$site['id'].')');
        $this->response->breadcrumb->add_item($titles[$section]);
        $this->response->submenu->set_menu('site-'.$site['id'])->set_subactive('site'.$section.'-'.$site['id']);
    }

    protected function _get_site_tree($site) {
        $content = fx::data('content')->where('site_id', $site['id'])->all();
        $tree = fx::data('content_page')->make_tree($content);
        $res = $this->_get_tree_branch($tree);
        return $res[0]['children'];
    }
    
    protected function _get_tree_branch($level_collection) {
        $result = array();
        $content_blocks = $level_collection->group('infoblock_id');
        $infoblocks = fx::data('infoblock')->where('action', 'listing')->all();
        foreach ($content_blocks as $ib_id => $items) {
            $infoblock = $infoblocks->find_one('id', $ib_id);
            $ib_name = $infoblock && $infoblock['name'] ? $infoblock['name'] : 'ib #'.$ib_id;
            $type_result = array();
            foreach ($items as $item) {
                $name = isset($item['name']) ? $item['name'] : $item['type'].' #'.$item['id'];
                $item_res = array(
                    'data' => $name,
                    'metadata' => array(
                        'id' => $item['id']
                    )
                );
                if ($item['children']) {
                    $item_res['children'] = $this->_get_tree_branch($item['children']);
                }
                $type_result []= $item_res;
            }
            $result []= array(
                'data' => $ib_name,
                'metadata' => array(
                    'id' => $ib_id,
                    'is_groupper' => 1
                ),
                'children' => $type_result
            );
        }
        if (count($result) == 1) {
            $result = $result[0]['children'];
        }
        return $result;
    }

    public function settings($input) {
        $site_id = isset($input['id']) ? $input['id'] : isset($input['params'][0]) ? $input['params'][0] : null;
        
        $site = fx::data('site', $site_id);

        $main_fields = array();
        $main_fields[] = $this->ui->input('name', fx::lang('Site name','system'), $site['name']);
        $main_fields[] = $this->ui->input('domain', fx::lang('Domain','system'), $site['domain']);
        $main_fields[] = $this->ui->input('mirrors', fx::lang('Aliases','system'), $site['mirrors']);
        $main_fields[] = $this->ui->input('language', fx::lang('Site language','system'), $site['language']);
        $this->response->add_fields($main_fields);

        $fields = array();
        $fields[] = $this->ui->hidden('essence', 'site');
        $fields[] = $this->ui->hidden('action', 'settings');
        $fields[] = $this->ui->hidden('posting');
        $fields [] = $this->ui->hidden('id', $site['id']);
        $this->response->add_fields($fields);
        $this->response->add_form_button('save');
        $this->_set_layout('settings', $site);
    }

    public function settings_save($input) {
        
        $site = fx::data('site')->get_by_id($input['id']);
        $result = array('status' => 'ok');
        $params = array('name', 'domain', 'mirrors', 'language', 'robots', 'language', 'robots', 'index_page_id', 'error_page_id', 'offline_text');

        foreach ($params as $v) {
            if (isset($input[$v])) {
                $site[$v] = $input[$v];
            }
        }
        /*
        $params = array('checked', 'disallow_indexing');
        foreach ($params as $v) {
            $site[$v] = intval($input[$v]);
        }
         * 
         */
        
        $site->save();
        return $result;
    }
    
    public function design($input) {
      	$site_id = $input['params'][0];
        $site = fx::data('site')->get_by_id($site_id);
        $layouts = fx::data('layout')->all();
        $layouts_select = array();
        foreach ( $layouts  as $layout ) {
            $layouts_select[] = array($layout['id'], $layout['name']);
        }

        $fields = array(
        	array(
				'name' => 'layout_id',
				'type' => 'select', 
				'values' => $layouts_select,
				'value' => $site['layout_id'],
				'label' => fx::lang('Layout','system')
			),
			array(
				'type' => 'hidden',
				'name' => 'site_id',
				'value' => $site_id
			)
		);

		/*
            foreach ($colors as $tpl_id => $color) {
                if ($color) {
                    $color_value = array();
                    $color_value[0] = "По умолчанию";
                    foreach ($color as $color_id => $v) {
                        $color_value[$color_id] = $v['name'];
                    }
                    $fields[] = array(
                        'label' => 'Расцветка',
                        'name' => 'color',
                        'type' => 'select',
                        'value' => ($tpl_id == $site['template_id'] ? $site['color'] : 0 ),
                        'values' => $color_value,
                        'parent' => array('template_id', "$tpl_id"),
                        'unactive' => true
                    );
                }
            }
         
        $fields []= array(
        	'type' => 'button',
        	'label' => fx::lang('Preview','system'),
        	'send_form' => true,
        	'post' => array(
        		'essence' => 'layout',
        		'action' => 'set_preview',
        		'posting' => false
        	)
        );

        $fields []= array(
            'type' => 'button',
            'label' => 'Создать',
            'send_form' => true,
            'post' => array(
                'essence' => 'layout',
                'action' => 'add',
                'posting' => false
            )
        );
        */
        $this->response->add_fields($fields);
        
        $this->response->add_form_button('save');
        $this->_set_layout('design', $site);
    }
    
    public function design_save($input) {
        $site = fx::data('site', $input['site_id']);
    	$old_template_id = $site['layout_id'];
        $site['layout_id'] = $this->input['layout_id'];
        $site->save();
        /*
        if ($old_template_id != $input['template_id']) {
            $suitable = new fx_suitable();
            $suitable->apply_design($site, $input['template_id']);
            $site['template_id'] = $input['template_id'];
        } */
    }

    public function download($input) {
        $items = $input['params'];
        if ($items) {
            $store = new fx_admin_store();
            $fields[] = $this->ui->label( fx::lang('You are about to install:','system') );
            foreach ($items as $store_id) {
                $info = $store->get_info($store_id);
                $fields[] = $this->ui->hidden('download['.$info['type'].']', $store_id);
                $fields[] = $this->ui->label($info['name']);
            }
        }

        $fields[] = $this->ui->hidden('action', 'download');
        $fields[] = $this->ui->hidden('essence', 'site');
        $fields[] = $this->ui->hidden('posting');

        $result['fields'] = $fields;
        $result['tree']['mode'] = 'administrate';
        $result['form_button'] = array('save');
        return $result;
    }

    public function download_save($input) {
        $store = new fx_admin_store();

        $download = $input['download'];
        if ($download['design']) {
            $content = $store->get_file($download['design']);
            $imex = new fx_import();
            $result = $imex->import_by_content($content);
            $template = $result[0];
        }

        if ($download['site']) {
            $content = $store->get_file($download['site']);
            $imex = new fx_import('template_id='.$template['id']);
            $result = $imex->import_by_content($content);
        }
    }
}
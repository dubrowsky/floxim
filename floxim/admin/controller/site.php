<?php

class fx_controller_admin_site extends fx_controller_admin {

    public function all() {
        $sites = fx::data('site')->get_all();

        $list = array('type' => 'list', 'filter' => true, 'tpl' => 'imgh', 'sortable' => true);
        $list['labels'] = array();

        $list['values'] = array();
        foreach ($sites as $v) {
            $text = fx::lang('Язык:','system') . ' ' . $v['language'];
            if ($v['domain']) {
                $text .= "<br />".$v['domain'];
            }
            $text = '<a href="http://'.$v['domain'].'" style="color:#666;" target="_blank"> '.$v['domain'].'</a>';
            $text .=" <span style='font-size:10px; color:#777;'>&middot;</span> ".$v['language'];
            if ($v['type'] == 'mobile') $text .= "<br/>" . fx::lang('для мобильный устройств','system');
            $r = array(
                    'id' => $v['id'],
                    'header' => array('name' => $v['name'], 'url' => 'site.settings('.$v['id'].')'),
                    'text' => $text/*,
                    'buttons' => array(
                    	array('url' => 'site.settings('.$v['id'].')', 'label' => fx::lang('Настройки','system')),
                    	array('url' => 'site.design('.$v['id'].')', 'label' => fx::lang('Дизайн','system'))
                    )*/
            );
            $list['values'][] = $r;
        }

        $this->response->add_field($list);

        $this->response->add_pulldown_item('add', fx::lang('Новый сайт','system'), 'source=new');
        
        $this->response->add_buttons("add,delete");//settings,
        $this->response->breadcrumb->add_item( fx::lang('Сайты','system') );
        $this->response->submenu->set_menu('site');
    }

    public function add($input) {
        $fields = array();

        switch ($input['source']) {
            case 'store':
                $fields[] = $this->ui->store('site');
                break;
            default:
                $fields[] = $this->ui->hidden('action', 'add');
                $fields[] = $this->ui->input('name', fx::lang('Название сайта','system'), fx::lang('Новый сайт','system'));
                $fields[] = $this->ui->input('domain', fx::lang('Домен','system'), fx::lang('Домен','system'));
        }

        $fields[] = $this->ui->hidden('posting');
        $this->response->add_fields($fields);
        $this->response->dialog->set_title( fx::lang('Добавление нового сайта','system') );
    }

    public function store($input) {
        return $this->ui->store('site', $input['filter']);
    }

    public function store_save($input) {
        $store = new fx_admin_store();
        $file = $store->get_file($input['store_id']);

        $result = array('status' => 'ok');
        try {
            $param = array('template_id' => fx_core::get_object()->env->get_site('template_id'));
            $imex = new fx_import($param);
            $imex->import_by_content($file);
        } catch (Exception $e) {
            $result = array('status' => 'ok');
            $result['text'][] = $e->getMessage();
        }

        return $result;
    }

    public function import_save($input) {
        $file = $input['importfile'];
        if (!$file) {
            $result = array('status' => 'error');
            $result['text'][] = fx::lang('Ошибка при создании временного файла','system');
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
            'name' => fx::lang('Титульная страница','system'),
            'url' => '/',
            'site_id' => $site['id']
        ))->save();
        
        $error_page = fx::data('content_page')->create(array(
            'name' => fx::lang('Страница не найдена','system'),
            'url' => '/404', 
            'site_id' => $site['id'],
            'parent_id' => $index_page['id']
        ))->save();
        
        $site['error_page_id'] = $error_page['id'];
        $site['index_page_id'] = $index_page['id'];
        dev_log('site to add', $site);
        
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
            $this->response->set_status_error( fx::lang('Сайт не найден','system') );
            return;
        }
        $fields = array();
        $fields[] = $this->ui->tree($this->_get_site_tree($site));

        $this->response->add_fields($fields);
        $this->response->add_buttons("add,settings,on,off,delete");
        $this->response->add_button_options('add', 'site_id='.$site['id']);
        $this->response->set_essence('subdivision');
        $this->_set_layout('map', $site);
    }
    
    protected function _set_layout($section, $site) {
    	$titles = array(
    		'map' => fx::lang('Карта сайта','system'),
    		'settings' => fx::lang('Настройки','system'),
    		'design' => fx::lang('Дизайн','system')
		);
    	$this->response->breadcrumb->add_item( fx::lang('Сайты','system'), '#admin.site.all');
        $this->response->breadcrumb->add_item($site['name'], '#admin.site.settings('.$site['id'].')');
        $this->response->breadcrumb->add_item($titles[$section]);
        $this->response->submenu->set_menu('site-'.$site['id'])->set_subactive('site'.$section.'-'.$site['id']);
    }

    protected function _get_site_tree($site) {
        $result = array();
        $subs = fx::data('subdivision')->get_all('site_id', $site['id']);

        $subdivisions = array();
        foreach ($subs as $sub) {
            $subdivisions[$sub['id']] = array('name' => $sub['name'], 'parent_id' => $sub['parent_id'], 'checked' => $sub['checked'], 'url' => $sub['hidden_url']);
            $child_subs[$sub['parent_id']][] = $sub['id'];
        }

        $result = $this->_map_get_childerns($subdivisions, $child_subs, 0);
        return $result;
    }

    protected function _map_get_childerns($subdivisions, $child_subs, $parent_id) {
        $result = false;

        if ($child_subs[$parent_id]) {
            foreach ($child_subs[$parent_id] as $sub_id) {
                $r = array(
                		'data' => $subdivisions[$sub_id]['name'],/*
                        'attr' => array(
                        	'id' => 'tree-sub-'.$sub_id, 
                        	'class' => $subdivisions[$sub_id]['checked'] ? "" : "fx_admin_unchecked"
                        ),*/
                        'metadata' => array(
                        	'id' => $sub_id, 
                        	'url' => $subdivisions[$sub_id]['url'],
                        	'checked' => $subdivisions[$sub_id]['checked'],
                        	'options' => array('add' => array('parent_id' => $sub_id))
                        )
                );
                $childs = $this->_map_get_childerns($subdivisions, $child_subs, $sub_id);
                if ($childs) {
                    $r['children'] = $childs;
                }
                $result[] = $r;
            }
        }

        return $result;
    }

    public function settings($input) {
        $site_id = isset($input['id']) ? $input['id'] : isset($input['params'][0]) ? $input['params'][0] : null;
        
        $site = fx::data('site', $site_id);

        $main_fields = array();
        $main_fields[] = $this->ui->input('name', fx::lang('Название сайта','system'), $site['name']);
        $main_fields[] = $this->ui->input('domain', fx::lang('Домен','system'), $site['domain']);
        $main_fields[] = $this->ui->input('mirrors', fx::lang('Зеркала','system'), $site['mirrors']);
        $main_fields[] = $this->ui->input('language', fx::lang('Язык сайта','system'), $site['language']);
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
				'label' => fx::lang('Макет','system')
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
        	'label' => fx::lang('Превью','system'),
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
            $fields[] = $this->ui->label( fx::lang('Вы собираетесь установить:','system') );
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
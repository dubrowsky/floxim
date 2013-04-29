<?php

class fx_controller_admin_site extends fx_controller_admin {

    public function all() {
        $sites = fx::data('site')->get_all();

        $list = array('type' => 'list', 'filter' => true, 'tpl' => 'imgh', 'sortable' => true);
        $list['labels'] = array();

        $list['values'] = array();
        foreach ($sites as $v) {
            $text = 'Язык: '.$v['language'];
            if ($v['domain']) {
                $text .= "<br />".$v['domain'];
            }
            $text = '<a href="http://'.$v['domain'].'" style="color:#666;" target="_blank"> '.$v['domain'].'</a>';
            $text .=" <span style='font-size:10px; color:#777;'>&middot;</span> ".$v['language'];
            if ($v['type'] == 'mobile') $text .= "<br/>для мобильный устройств";
            $r = array(
                    'id' => $v['id'],
                    'img' => '/floxim/admin/skins/default/images/site1.png',
                    'header' => array('name' => $v['name'], 'url' => 'site.map('.$v['id'].')'),
                    'text' => $text,
                    'buttons' => array(
                    	array('url' => 'site.map('.$v['id'].')', 'label' => 'Карта сайта'),
                    	array('url' => 'site.settings('.$v['id'].')', 'label' => 'Настройки'),
                    	array('url' => 'site.design('.$v['id'].')', 'label' => 'Дизайн'),
                    	array('label' => 'Экспорт', array('essence' => 'site', 'action' => 'export', 'id' => $v['id']))
                    )
            );
            $list['values'][] = $r;
        }

        $this->response->add_field($list);

        $this->response->add_pulldown_item('add', 'Новый', 'source=new');
        $this->response->add_pulldown_item('add', 'импортировать', 'source=import');
        $this->response->add_pulldown_item('add', 'установить с FloximStore', 'source=store');

        $this->response->add_buttons("add,delete");//settings,
        $this->response->breadcrumb->add_item('Сайты');
        $this->response->submenu->set_menu('site');
    }

    public function add($input) {
        $fields = array();

        switch ($input['source']) {
            case 'store':
                $fields[] = $this->ui->store('site');
                break;
            case 'import':
                $fields[] = $this->ui->select('template_id', "Макет дизайна", $this->get_templates());
                $fields[] = $this->ui->file('importfile', 'Файл');
                $fields[] = $this->ui->hidden('action', 'import');
                break;
            default:
                $fields[] = $this->ui->hidden('action', 'add');
                $fields[] = $this->ui->input('name', 'Название сайта', 'Новый сайт');
                $fields[] = $this->ui->input('domain', 'Название сайта', 'Домен');
        }

        $fields[] = $this->ui->hidden('posting');
        $this->response->add_fields($fields);
        $this->response->dialog->set_title('Добавление нового сайта');
    }

    protected function get_templates() {
        $tpl_values = array();
        foreach (fx::data('template')->get_all('type', 'parent') as $tpl) {
            $tpl_values[$tpl['id']] = $tpl['name'];
        }
        return $tpl_values;
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
            $result['text'][] = 'Ошибка при создании временного файла';
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
        $template_id = $current_site['template_id'];
        if (!$template_id) {
            $template = fx::data('template')->get('type', 'parent');
            $template_id = $template['id'];
        }

        $site['template_id'] = $template_id;
        $site['checked'] = 1;
        $site->save();

        $sub_index = fx::data('subdivision')->create(array('name' => 'Титульная страница', 'keyword' => 'index', 'site_id' => $site['id']))->save();
        $site['title_sub_id'] = $sub_index['id'];

        $sub_e404 = fx::data('subdivision')->create(array('name' => 'Страница не найдена', 'keyword' => 'e404', 'site_id' => $site['id']))->save();
        $site['e404_sub_id'] = $sub_e404['id'];

        $site->save();
        return $result;
    }

    public function map($input) {
        $site = fx::data('site')->get_by_id($input['params'][0]);
        if (!$site) {
            $this->response->set_status_error("Сайт не найден");
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
    		'map' => 'Карта сайта',
    		'settings' => 'Настройки',
    		'design' => 'Дизайн'
		);
    	$this->response->breadcrumb->add_item('Сайты', '#admin.site.all');
        $this->response->breadcrumb->add_item($site['name'], '#admin.site.map('.$site['id'].')');
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

    public function export($input) {
        $configure = new fx_admin_configure();
        $reqs = $configure->get_requirements($input['id']);


        foreach ($reqs as $i => $page) {
            $page_type = $page['type'];
            $fields[] = $this->ui->hidden("requirements[$i][type]", $page_type);
            $blocks = $menu = array('type' => 'set',
                    'without_add' => true,
                    'name' => "requirements[$i][units]",
                    'label' => 'Блоки '.$page_type,
                    'labels' => array('Блок', 'Обязательный'),
                    'tpl' => array(
                            array('type' => 'label', 'name' => 'name'),
                            array('type' => 'select', 'values' => array('yes' => 'yes', 0 => 'no'), 'name' => 'nessesary'),
                            array('type' => 'hidden', 'name' => 'id'),
                            array('type' => 'hidden', 'name' => 'type'),
                            array('type' => 'hidden', 'name' => 'embed'),
                    ));
            $menu = $menu = array('type' => 'set',
                    'name' => "requirements[$i][units]",
                    'without_add' => true,
                    'label' => 'Меню '.$page_type,
                    'labels' => array('Меню', 'Направление', 'Обязательный'),
                    'tpl' => array(
                            array('type' => 'label', 'name' => 'name'),
                            array('type' => 'select', 'values' => array(0 => 'любое', 'vertical' => 'вертикальное'), 'name' => 'direct'),
                            array('type' => 'select', 'values' => array('yes' => 'yes', 0 => 'no'), 'name' => 'nessesary'),
                            array('type' => 'hidden', 'name' => 'id'),
                            array('type' => 'hidden', 'name' => 'type'),
                            array('type' => 'hidden', 'name' => 'keyword')
                    ));
            if ($page['units']) {
                foreach ($page['units'] as $j => $unit) {
                    $type = $unit['type'];
                    if ($type == 'infoblock') {
                        $blocks['values'][$j] = array('name' => $unit['id'], 'nessesary' => 'yes', 'id' => $unit['id'], 'type' => 'infoblock', 'embed' => $unit['embed']);
                    } else {
                        $menu['values'][$j] = array('name' => 'menu '.$unit['keyword'], 'direct' => $unit['direct'], 'nessesary' => 1, 'id' => $unit['id'], 'type' => 'menu', 'keyword' => $unit['keyword']);
                    }
                }
            }

            $fields[] = $blocks;
            $fields[] = $menu;
        }

        $fields[] = $this->ui->hidden('id', $input['id']);
        $fields[] = $this->ui->hidden('essence', 'site');
        $fields[] = $this->ui->hidden('action', 'export');
        $fields[] = $this->ui->hidden('posting');
        $result = array('fields' => $fields);

        return $result;
    }

    public function export_save($input) {
        $site = fx::data('site')->get_by_id($input['id']);
        $requirements = $input['requirements'];

        $export = new fx_export();
        $export->export_configure(array($site), $requirements);
    }

    public function settings($input) {
        $site_id = isset($input['id']) ? $input['id'] : isset($input['params'][0]) ? $input['params'][0] : null;
        
        $site = fx::data('site')->get_by_id($site_id);

        
        // используются subdivisions (запрещено)
        /* $sub_values = array();
        $sub_all = fx::data('subdivision')->get_all('site_id', $site['id']);
        foreach ($sub_all as $sub) {
            $sub_values[$sub['id']] = $sub['name'];
        } */
        
        // используются content_pages
        $content_pages_list = array();
        $content_pages = fx::data('content_page')->get_all('site_id', $site_id);
        foreach ($content_pages as $page)
        {
            $content_pages_list[$page['id']] = $page['url'];
        }

        $this->response->add_tab('main', 'Основные');
        //$this->response->add_tab('design', 'Дизайн');
        $this->response->add_tab('seo', 'SEO');
        $this->response->add_tab('system', 'Системные');

        $main_fields = array();
        $main_fields[] = $this->ui->checkbox('checked', 'Включен', null, $site['checked']);
        $main_fields[] = $this->ui->input('name', 'Название сайта', $site['name']);
        $main_fields[] = $this->ui->input('domain', 'Домен', $site['domain']);
        $main_fields[] = $this->ui->input('mirrors', 'Зеркала', $site['mirrors']);
        $main_fields[] = $this->ui->input('language', 'Язык сайта', $site['language']);
        $this->response->add_fields($main_fields, 'main');

        $seo_fields = array();
        $seo_fields[] = $this->ui->text('robots', 'Содержимое robots.txt', $site['robots']);
        $seo_fields[] = $this->ui->checkbox('disallow_indexing', 'Запретить индексирование', null, $site['disallow_indexing']);
        $this->response->add_fields($seo_fields, 'seo');

        $system_fields = array();
        $system_fields[] = array('name' => 'title_sub_id', 'type' => 'select', 'values' => $content_pages_list, 'value' => $site['title_sub_id'], 'label' => 'Титульная страница');
        $system_fields[] = array('name' => 'e404_sub_id', 'type' => 'select', 'values' => $content_pages_list, 'value' => $site['e404_sub_id'], 'label' => 'Страница не найдена (ошибка 404)');
        $system_fields[] = array('name' => 'offline_text', 'type' => 'textarea', 'value' => $site['offline_text'], 'label' => 'Показывать, когда сайт выключен');
        $this->response->add_fields($system_fields, 'system');

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
        $params = array('name', 'domain', 'mirrors', 'language', 'robots', 'language', 'robots', 'title_sub_id', 'e404_sub_id', 'offline_text');

        foreach ($params as $v) {
            if (isset($input[$v])) {
                $site[$v] = $input[$v];
            }
        }

        $params = array('checked', 'disallow_indexing');
        foreach ($params as $v) {
            $site[$v] = intval($input[$v]);
        }
        
        $site->save();
        return $result;
    }
    
    public function design($input) {

       	$site_id = $input['params'][0]; //isset($input['id']) ? $input['id'] : isset($input['params'][0]) ? $input['params'][0] : null;
        $site = fx::data('site')->get_by_id($site_id);

        $layouts = fx::data('layout')->all();
        $layouts_select = array();
        foreach ( $layouts  as $layout )
        {
            $layouts_select[] = array('l_' . $layout['id'], $layout['name']);
        }
        dev_log($layouts_select);

        /*
        $tpl_values = array();
        $tpl_all = fx::data('template')->get_all('type', 'parent');
        
        $colors = array();
        foreach ($tpl_all as $tpl) {
            $tpl_values[$tpl['id']] = $tpl['name'];
            $colors[$tpl['id']] = $tpl['colors'];
        }
        */

        
        $fields = array(
        	array(
				'name' => 'layout_id',
				'type' => 'select', 
				'values' => $layouts_select,
				'value' => $site['layout_id'],
				'label' => 'Макет'
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
        */
        
        $fields []= array(
        	'type' => 'button', 
        	'label' => 'Превью',
        	'send_form' => true,
        	'post' => array(
        		'essence' => 'template',
        		'action' => 'set_preview',
        		'posting' => false
        	)
        );


        dev_log('fields',$fields);
        		
        
        $this->response->add_fields($fields);
        
        $this->response->add_form_button('save');
        $this->_set_layout('design', $site);
    }
    
    public function design_save($input) {
    	$site_id = $input['site_id'];
        $site = fx::data('site')->get_by_id($site_id);
    	
    	$old_template_id = $site['template_id'];
        if ($old_template_id != $input['template_id']) {
            $suitable = new fx_suitable();
            $suitable->apply_design($site, $input['template_id']);
            $site['template_id'] = $input['template_id'];
        }
        $site->save();

    }

    public function download($input) {
        $items = $input['params'];
        if ($items) {
            $store = new fx_admin_store();
            $fields[] = $this->ui->label("Вы собираетесь установить:");
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

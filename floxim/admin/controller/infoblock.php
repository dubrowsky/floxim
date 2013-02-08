<?php
class fx_controller_admin_infoblock extends fx_controller_admin {

    /**
     * @todo Проверять права!
     */
    public function index($input) {
        $fx_core = fx_core::get_object();
        $result = array();

        if ($input['id']) {
            $id = $input['id'];
            $infoblock = fx::data('infoblock')->get_by_id($id);
            if ($infoblock) {
                if ( $infoblock['main_content'] ) {
                    $func_param = array();
                    if ( $input['page'] ) $func_param['page'] = intval($input['page']);
                    $key = fx::config()->SEARCH_KEY;
                    $search = $fx_core->input->fetch_get_post($key);
                    if ( $search ) {
                        $func_param[$key] = $search;
                    }
                }
                echo $infoblock->show_index($func_param);
            }
            return false;
        }

        //if ($input['admin_mode'])
        $fx_core->set_admin_mode();

        $url = $input['url'];
        $route = new fx_route($url);
        $result = $route->resolve();

        $current_sub = $result['sub_env'];

        $fx_core->env->set_ibs($result['ibs_env']);
        if ($result['content_id'])
            $fx_core->env->set_content($result['content_id']);
        $fx_core->env->set_action($result['action']);

        if ($result['page'])
            $fx_core->env->set_page($result['page']);
        $fx_core->env->set_sub($current_sub);

        $template = $current_sub->get_data_inherit('template_id');
        $fx_core->env->set_template ( $template );

        //$p = new fx_controller_page();



        $infoblocks = $input['infoblocks'];

        $fx_core->page->set_numbers($input['block_number']++, $input['field_number']++);

        if ($infoblocks) {
            foreach ($infoblocks as $keyword => $params) {
                $ib = new fx_unit_infoblock ();
                $result[$keyword] = $ib->show($keyword, $params, true);
            }
        }

        $fl = $fx_core->page->get_edit_fields();
        if ($fl) {
            $result['nc_scripts'] = '$fx.set_data(' . json_encode(array('fields' => $fx_core->page->get_edit_fields())) . ');';
        }

        $blocks = $fx_core->page->get_blocks();
        if ($blocks) {
            $result['nc_scripts'] .= '$fx.set_data(' . json_encode(array('blocks' => $fx_core->page->get_blocks())) . ');';
        }
        $sortable = $fx_core->page->get_sortable();
        if ( $sortable ) {
            $result['nc_scripts'] .= '$fx.set_data(' . json_encode(array('sortable' => $fx_core->page->get_sortable())) . ');';
        }

        $result['nc_scripts'] .= '$fx.set_data(' . json_encode(array('addition_block' => $fx_core->page->get_addition_block())) . ');';
        echo json_encode($result);
    }

    
    public function add($input) {
    	$step_map = array(
            'type' => 'type_select',
            'settings' => 'settings_select',
            'save' => 'add_save'
        );
        $c_step = isset($input['step']) && isset($step_map[$input['step']]) ? $input['step'] : 'type';
        $c_step_method = $step_map[$c_step];

        if (method_exists($this, $c_step_method)) {
            return call_user_func(array($this, $c_step_method), $input);
        }
    }
    
    public function add_save($input) {
        dev_log($input);
        
        $ib_params = array(
            'site_id' => fx::env('site_id'),
            'page_id' => 0,
            'checked' => 1,
            'priority' => 0,
            'name' => 'Olo',
            'is_listing' => 1,
            'controller' => $input['controller'],
            'action' => 'listing',
            'params' => $input['params']
        );
        
        $ib = fx::data('infoblock')->create($ib_params);
        $visual = fx::data('infoblock2layout')->create($input['visual']);
        
        $ib->save();
        $visual->set('infoblock_id', $ib['id']);
        $visual->set('layout_id', 1);
        $visual->save();
        
        die("Saving");
    }
    
    public function type_select($input) {
    	
        $controllers_data = self::get_all_controllers();
    	
    	$fields = array(
            $this->ui->hidden('action', 'add'),
            $this->ui->hidden('essence', 'infoblock'),
            $this->ui->hidden('fx_admin', true),
            $this->ui->hidden('step', 'settings')
            //$this->ui->hidden('infoblock_info', serialize($input['infoblock_info'])),
            //$this->ui->hidden('subdivision_id', $input['subdivision_id'])
	);
		
	$types = array(
            'widget' => 'Виджет', 
            'component' => 'Компонент'
	);
    	
        /* Список контроллеров */
        $fields['controller']= array(
            'type' => 'list', 
            'name' => 'controller',
            'labels' => array(
                'name' => array('label' => 'Название', 'filter' => 'text'),
                'type' => array('label' => 'Тип', 'filter' => 'select'),
                'group' => array('label' => 'Группа', 'filter' => 'select')
            ), 
            'values' => array()
    	);
    	foreach ($controllers_data['controllers'] as $cn) {
            $cn_type = $cn instanceof fx_component ? 'component' : 'widget';
            $fields['controller']['values'] []= array(
                'name' => array('name' => $cn['name'], 'url' => null), 
                'type' => $types[$cn_type],
                'group' => $cn['group'],
                'id' => $cn_type.'_'.$cn['keyword']
            );
    	}
    	
    	$result = array(
            'fields' => $fields,
            'dialog_title' => 'Добавление инфоблока',
            //'step' => 'type_select',
            'dialog_button' => array(
                array('key' => 'store', 'text' => 'Установить с Store'),
                array('key' => 'save', 'text' => 'Продолжить')
            )
    	);
    	return $result;
    }
    
    public function settings_select($input) {
    	// Текущий (редактируемый) инфоблок
    	$current_infoblock = null;
    	
    	// Информация о блоках для форматирования
    	$infoblock_info = is_string($input['infoblock_info']) ? unserialize($input['infoblock_info']) : $input['infoblock_info'];
    	
        if (isset($input['id']) && is_numeric($input['id'])) {
            // Редактируем существующий инфоблок
            $current_infoblock = fx::data('infoblock')->get_by_id($input['id']);
            //$controller_type = $current_infoblock['type'] == 'content' ? 'component' : 'widget';
            $controller_name = $current_infoblock['controller'];
    	} else {
            // Создаем новый, тип и ID контроллера получаем с предыдущего шага
            //$controller_type = $input['controller_type'];
            $controller_name = $input['controller'];
    	}
    	
    	//dev_log($current_infoblock);
    	
    	// Запоминаем для после-отправки
    	$fields = array(
            $this->ui->hidden('controller', $controller_name),
            $this->ui->hidden('infoblock_id', $current_infoblock ? $current_infoblock['id'] : null),
            $this->ui->hidden('action', 'add'),
            $this->ui->hidden('essence', 'infoblock'),
            $this->ui->hidden('fx_admin', true),
            $this->ui->hidden('step', 'save')
    	);
    	
    	$this->response->add_fields($fields);
    	
    	$this->response->add_tab('settings', 'Что показывать');
    	
        $controller = fx::controller($controller_name);
        $settings = $controller->get_action_settings('listing');
        $this->response->add_fields($settings, 'settings', 'params');
        
        dev_log($this->response);
        
        /*
    	// Для компонента
    	if ($controller_type == 'component') {
            $c_controller = fx::data('component')->get_by_id($controller_id);
            $main_fields []= array(
                'type' => 'radio', 
                'values' => array('block' =>'Свои данные', 'mirror' => 'Миррор'), 
                'className' => 'inline',
                'name' => 'component_type', 
                'value' => $current_infoblock ? ($current_infoblock['subtype'] == 'block' ? 'block' : 'mirror') : 'block'
            );
            $main_fields []= array(
                'name' => 'name', 
                'label' => 'Название', 
                'type' => 'input', 
                'value' => $current_infoblock ? $current_infoblock['name'] : $c_controller['name'], 
                'parent' => array('component_type', 'block')
            );
            // !!! напрашивается явное разделение компонентов 
            // на те, у которых есть страницы 
            // и те, которые отображаются только списком (тескст, картинка и т.д.)
            $main_fields []= array(
                'name' => 'url', 
                'label' => 'Часть URL', 
                'type' => 'input', 
                'value' => $current_infoblock ? $current_infoblock['url'] : $c_controller['keyword'], 
                'parent' => array('component_type', 'block')
            );

            // Нужно получить ID сайта, чтоб выбор разделов для миррора был только по нашему сайту
            if ($current_infoblock) {
                $site_id = $current_infoblock['site_id'];
            } else {
                $subdivision = fx::data('subdivision')->get_by_id($input['subdivision_id']);
                $site_id = $subdivision['site_id'];
            }

            $mirror_fields = $this->_get_mirror_fields($controller_id, $current_infoblock, $site_id);
            foreach ($mirror_fields as $mf) {
                if (!isset($mf['parent'])) {
                    $mf['parent'] = array();
                }
                $mf['parent']['component_type'] = 'mirror';
                $main_fields[]= $mf;
            }
        // для виджета
    	} elseif ($controller_type == 'widget') { 
            $widget = fx::data('widget')->get_by_id($controller_id);
	
            $widget_form = $widget->load_tpl_object()->set_vars('widget', $widget)->set_vars('infoblock', $current_infoblock)->add_form();

            if ($widget_form) {
                foreach ($widget_form as $v) {
                    if (strpos($v['name'], 'visual[') === false) {
                        $v['name'] = 'visual['.$v['name'].']';
                    }
                    $main_fields[] = $v;
                }
            }
    	}
         * 
         */
        
        /*
    	// Настройки отображения для компонента
    	if ($controller_type == 'component') {
            $visual_fields = array();
            $ctpls = fx::data('ctpl')->get_by_component($controller_id);
            // Поле-селект для выбора шаблона компонента 
            $ctpl_field = array(
                'type' => 'select',
                'name' => 'ctpl_id',
                'values' => array(),
                'label' => 'Шаблон компонента',
                'value' => $current_infoblock ? $current_infoblock['list_ctpl_id'] : $ctpls[0]['id']
            );
            foreach ($ctpls as $ctpl) {
                $ctpl_field['values'][$ctpl['id']] = $ctpl['name'];
            }
            $visual_fields []= $ctpl_field;

            // Для каждого шаблона собираем поля визуальных настроек
            foreach ($ctpls as $ctpl) {
                foreach ($ctpl->fields() as $field) {
                    $visual_field = $field->get_js_field($infoblock['visual'], 'visual['.$ctpl['id'].'][%name%]');
                    $visual_field['parent'] = array('ctpl_id', $ctpl['id']);
                    preg_match("~\[([^\]]+)\]$~", $visual_field['name'], $visual_field_name);
                    if ($current_infoblock && isset($current_infoblock['visual'][$visual_field_name[1]])) {
                        $visual_field['value'] = $current_infoblock['visual'][$visual_field_name[1]];
                    }
                    $visual_fields []= $visual_field;
                }
            }
            // Только один шаблон и нету визуальных настроек
            if (count($visual_fields) == 1 && count($ctpls) == 1) {
                $this->response->add_fields( array($this->ui->hidden('ctpl_id', $ctpls[0]['id'])));
            } else {
                $this->response->add_tab('visual', 'Как показывать');
                $this->response->add_fields($visual_fields, 'visual');
            }
    	}
        */
        
        $this->response->add_tab('visual', 'Как показывать');
        $format_fields = $this->_get_format_fields($infoblock_info, $current_infoblock);
        $this->response->add_fields($format_fields, 'visual', 'visual');
    	
    	// Оформление блока - переезжает во вкладку "Как показывать"
    	//$this->response->add_tab('format', 'Оформление блока');
    	//$format_fields = $this->_get_format_fields($infoblock_info, $current_infoblock);
    	//$this->response->add_fields($format_fields, 'format');
    	
    	$this->response->add_tab('area', 'Где показывать');
    	$area_fields = array(
            array('type' => 'select', 'name' => 'pages', 'values' => array('all' => 'На всех страницах', 'this' => 'Только на этой страницах', 'brothers' => 'На страницах этого уровня')),
            array('name' => 'but', 'label' => 'Кроме...', 'parent' => array('pages' => 'all')),
            array('name' => 'area_type', 'label' => 'Имеющих тип', 'type' => 'select', 'values' => array("Любой", "Новость", "Раздел", "Вакансия"))
        );
        $this->response->add_fields($area_fields, 'area', 'area');
    	
    	$result = array(
            'dialog_title' => 'Настройка инфоблока',
            'step' => 'settings_select',
            'dialog_button' => array(
                array('key' => 'save', 'text' => 'Закончить')
            )
    	);
    	return $result;
    }
	
    protected function _get_format_fields($infoblock_info, $current_infoblock) {
        
        $fields = array(
            array(
                'label' => "Area",
                'name' => 'area'
            ), array(
                'label' => 'Шаблон-обертка',
                'name' => 'wrapper_name'
            ), array(
                'label' => 'Параметры обертки',
                'name' => 'wrapper_visual',
                'type' => 'textarea'
            ), array(
                'label' => 'Шаблон',
                'name' => 'template_name'
            ), array(
               'label' => 'Вариант шаблона',
                'name' => 'template_variant'
            ), array(
                'label' => 'Параметры шаблона',
                'name' => 'template_visual',
                'type' => 'textarea'
            )
        );
        return $fields;
        dev_log('get formatters', $infoblock_info, $current_infoblock);
        $fields_child = array();
        foreach ($infoblock_info['blocks'] as $k => $block) {
            $values[$k] = $block['name'] && $block['name'] != 'null' ? $block['name'] : 'Блок (' . $k . ')';
            if ($block['params']) {
                foreach ($block['params'] as $param_num => $param) {
                    $label = $param['name'] ? $param['name'] : 'String ' . $param_num;
                    $name = 'replace_value[' . $param_num . ']';
                    $value = $current_infoblock ? $current_infoblock['replace_value'][$param_num] : $param['default'];
                    $fields_child[] = array('name' => $name, 'value' => $value, 'label' => $label, 'parent' => array('use_format', $k), 'unactive' => true);
                }
            }
        }
        $fields[] = array(
            'label' => 'Оформление', 'id' => 'use_format', 'name' => 'use_format', 'values' => $values, 'type' => 'select', 
            'value' => $current_infoblock ? $current_infoblock['use_format'] : 0, 
            'hidden_on_one_value' => true
        );
        foreach ($fields_child as $v) {
            $fields[] = $v;
        }
        return $fields;
    }
	
    protected function _get_mirror_fields($component_id, $current_infoblock, $site_id) {
        $component = fx::data('component')->get_by_id($component_id);
        $fields = array();

        $contents_infoblocks = fx::data('infoblock')->get_content_infoblocks($component_id);
        foreach ($contents_infoblocks as $content_infoblock) {
            $sub_id = $content_infoblock['subdivision_id'];
            $subdivision = fx::data('subdivision')->get_by_id($sub_id);
            if ($subdivision && $subdivision['site_id'] == $site_id) {
                $sub_name = $subdivision->get('name');
                $values[$content_infoblock['id']] = $content_infoblock['name']." (раздел $sub_name)";
            }
        }

        $source_types = array('all' => 'все', 'select' => 'выбранные');
        $fields[] = array(
            'type' => 'radio', 'name' => 'source_type', 'id' => 'source_type', 'label' => 'Разделы, откуда брать данные', 
            'value' => $current_infoblock ? $current_infoblock['source']['type'] : 'all', 
            'values' => $source_types,
            'className' => 'inline'
        );
        $fields[] = array(
            'name' => 'source_infoblocks', 'label' => '', 'type' => 'checkbox', 
            'values' => $values, 
            'value' => $current_infoblock && isset($current_infoblock['source']['infoblocks']) ? $current_infoblock['source']['infoblocks'] : array(),
            'parent' => array('source_type'=>'select')
        );


        $obj_select = array('auto' => 'автоматически', 'manual' => 'вручную');
        $fields[] = array(
            'type' => 'radio', 'id' => 'content_selection_type', 'name' => 'content_selection_type', 'label' => 'Выбриать объекты', 
            'values' => $obj_select, 
            'value' => $current_infoblock ? $current_infoblock['content_selection']['type'] : 'auto',
            'className' => 'inline'
        );

        $fields[] = array(
            'name' => 'rec_num', 'label' => 'Количество', 'parent' => array('content_selection_type' => 'auto'),
            'value' => $current_infoblock ? $current_infoblock['rec_num'] : ''
        );

        $values = array(0 => 'наследовать от компонента');
        foreach (array('manual', 'field', 'last', 'random') as $v) {
            $values[$v] = constant('FX_ADMIN_SORT_'.strtoupper($v));
        }
        $fields[] = array(
            'id' => 'sort', 'name' => 'sort_type', 'type' => 'radio', 'label' => FX_ADMIN_SORT, 
            'value' => $current_infoblock ? $current_infoblock['sort']['type'] : 0, 
            'values' => $values, 
            'parent' => array('content_selection_type' => 'auto'),
            'className' => 'inline'
        );
        // поля для сортировки
        $sortable_fields = $component->get_sortable_fields();
        $order = array('asc' => FX_ADMIN_SORT_ASC, 'desc' => FX_ADMIN_SORT_DESC);
        $fields[] = array('name' => 'sort_fields', 'label' => '', 'type' => 'set', 'parent' => array('sort_type' => 'field'),
            'unactive' => true,
            'labels' => array(FX_ADMIN_SORT_BY, FX_ADMIN_SORT_ORDER),
            'tpl' => array(
                    array('name' => 'field', 'type' => 'select', 'values' => $sortable_fields),
                    array('name' => 'order', 'type' => 'select', 'values' => $order)),
            'values' => $sort_fields
        );
        return $fields;
    }
    
    /* Возвращает все контроллеры - виджеты и компоненты */
    public static function get_all_controllers() {
    	$controllers = array();
    	foreach (fx::data('component')->get_all() as $c) {
    		$controllers['component-'.$c['id']] = $c;
    	}
    	foreach (fx::data('widget')->get_all() as  $w){
    		$controllers['widget-'.$w['id']]= $w;
    	}
    	$groups = array();
    	foreach ($controllers as $c) {
    		$groups [md5($c['group'])]= $c['group'];
    	}
    	$groups = array_unique($groups);
    	return array('groups' => $groups, 'controllers' => $controllers);
    }
    
    
    public function store($input) {
        return $this->ui->store('infoblock', $input['filter']);
    }

    public function settings($input) {
        if ( $input['simple'] ) {
            return $this->settings_simple($input);
        }
        //return $this->add($input);
        return $this->settings_select($input);
    }

    protected function _get_child_object($type, $subtype = '') {
        $classname = get_class($this) . '_' . $type;
        if ($subtype) {
            $classname .= '_' . $subtype;
        }
        return new $classname();
    }

    protected function _form_store($input) {
        $fields[] = $this->ui->store('infoblock');
        $fields[] = $this->ui->hidden('phase', 2);
        $result['fields'] = $fields;
        $result['step'] = 10;
        return $result;
    }

    public function store_save($input) {
        $result['status'] = 'ok';
        return $result;
    }
    
    public function save_var($input) {
        $ib = fx::data('infoblock', $input['infoblock']['id']);
        $ib_visual = fx::data('infoblock2layout', $input['infoblock']['visual_id']);
        $var_id = $input['var']['id'];
        $var_tpl_parts = explode(".", $input['var']['template']);
        //dev_log($ib_visual, $input);
        if ($ib_visual) {
            if ($ib_visual['wrapper_name'] == $var_tpl_parts[1]) {
                $wrapper_visual = $ib_visual['wrapper_visual'];
                if (!is_array($wrapper_visual)) {
                    $wrapper_visual = array();
                }
                $wrapper_visual[$var_id] = $input['value'];
                $ib_visual['wrapper_visual'] = $wrapper_visual;
            } elseif ($ib_visual['template_name'] == $var_tpl_parts[0] && $ib_visual['template_variant'] == $var_tpl_parts[1]) {
                $template_visual = $ib_visual['template_visual'];
                if (!is_array($template_visual)) {
                    $template_visual = array();
                }
                $template_visual[$var_id] = $input['value'];
                $ib_visual['template_visual'] = $template_visual;
            }
            $ib_visual->save();
        }
    }

    public function edit_save($input) {
        
        if ($input['edit_in_place']) {
            return $this->edit_in_place($input);
        }

        $infoblock = fx::data('infoblock')->get_by_id($input['id']);
        $component_id = $infoblock['essence_id'];

        $content = fx::data('content')->get($component_id, 'infoblock_id', $infoblock['id']);

        if ($content) {
            $new_input = $input;
            $new_input['essence'] = 'content';
            $new_input['action'] = 'edit';
            $new_input['fx_admin'] = 1;
            $new_input['id'] = $component_id . '-' . $content['id'];
            $m = new fx_controller_admin_content();
            return $m->edit_save($new_input);
        }
    }

    public function settings_save($input) {
        
        if ($input['edit_in_place']) {
            return $this->edit_in_place($input);
        }

        $ib = fx::data('infoblock')->get_by_id($input['id']);
        // конкретный тип инфоблока может сделать свои действия при сохранении
        $this->_get_child_object($input['type'], $input['subtype'])->save($ib, $input);

        // основные параметры инфоблока
        $params = array('keyword', 'url', 'name', 'type', 'subtype', 'essence_id', 'list_ctpl_id', 'visual', 'use_format', 'replace_value', 'default_action', 'rec_num');
        foreach ($params as $v) {
            if (isset($input[$v]))
                $ib->set($v, $input[$v]);
        }

        $result = array('status' => 'ok');
        $ib->save();
        return $result;
    }

    public function settings_simple ( $input ) {
        $params = $input['params'];
        $fields = array(
        	$this->ui->hidden('essence', 'infoblock'),
        	$this->ui->hidden('action', 'settings_simple_save'),
        	$this->ui->hidden('fx_admin'),
        	$this->ui->hidden('id', $input['id']),
        	$this->ui->hidden('subdivision_id', $input['subdivision_id']),
        	$this->ui->hidden('keyword', $input['keyword'])
		);
        if ( $params ) {
            foreach ( $params as $k => $param ) {
                $fields[] = $this->ui->input('replace_value['.$k.']', $param['name'].' ('.$param['type'].')', $param['value']);
            }
        }
        $result['fields'] = $fields;
        $result['step'] = 10;
        return $result;
    }

    public function settings_simple_save($input) {
    	$infoblock = null;
        if ($input['id']) {
            $infoblock = fx::data('infoblock')->get_by_id($input['id']);
        }
        if (!$infoblock) {
        	$data = array('keyword' => $input['keyword'], 'checked' => 1);
            $infoblock = fx::data('infoblock')->create($data);

            $subdivision = fx::data('subdivision')->get_by_id($input['subdivision_id']);

            if ($subdivision['own_design']) {
                $infoblock['subdivision_id'] = $subdivision['id'];
                $infoblock['individual'] = 1;
            }

            $infoblock['site_id'] = $subdivision['site_id'];
            $site = fx::data('site')->get_by_id($subdivision['site_id']);
            $infoblock['template_id'] = $site['template_id'];
        }

        $infoblock['replace_value'] = $input['replace_value'];

        $infoblock->save();

        return array('status' => 'ok');
    }

    public function edit_in_place($input) {
        if ($input['id']) {
            $infoblock = fx::data('infoblock')->get_by_id($input['id']);
        } else {
            $data = array('keyword' => $input['keyword'], 'checked' => 1);
            $infoblock = fx::data('infoblock')->create($data);

            $subdivision = fx::data('subdivision')->get_by_id($input['subdivision_id']);
            if ($subdivision['own_design']) {
                $infoblock['subdivision_id'] = $subdivision['id'];
                $infoblock['individual'] = 1;
            }
        }

        $infoblock['replace_value'] = array($input['replace_value']);
        $infoblock->save();

        return array('status' => 'ok');
    }

    public function _add_save($input) {
        $fx_core = fx_core::get_object();

        if ($input['id']) {
            return $this->settings_save($input);
        }

        $ib = fx::data('infoblock')->create();
        $site = $fx_core->env->get_site();
        $ib['site_id'] = $site['id'];

        $infoblock_info = unserialize($input['infoblock_info']);
        $main_content = (bool) $infoblock_info['main'];

        // конкретный тип инфоблока может сделать свои действия при сохранении
        $this->_get_child_object($input['type'], $input['subtype'])->save($ib, $input);

        // основные параметры инфоблока
        $params = array('keyword', 'url', 'name', 'type', 'subtype', 'parent_id', 'field_id', 'essence_id', 'list_ctpl_id', 'visual', 'use_format', 'replace_value', 'default_action', 'rec_num');
        foreach ($params as $v) {
            if (isset($input[$v]))
                $ib->set($v, $input[$v]);
        }
        $ib->set('priority', fx::data('infoblock')->next_priority($input['keyword']));
        if ( $main_content ) {
            $ib['main_content'] = 1;
        }
        else {
            $ib['template_id'] = $site['template_id'];
        }

        // инфоблок привязывается к разделу, если раздел имеет индивидуальный дизайн
        // или если это основной контент
        $sub = fx::data('subdivision')->get_by_id($input['subdivision_id']);
        if ($input['subtype'] == 'block' || $sub['own_design'] || $main_content) {
            $ib->set('subdivision_id', $input['subdivision_id']);
        }

        if ($input['type'] == 'content' ) {
            $component = fx_core::get_object()->component->get_by_id($input['essence_id']);
            if ( $component->is_user_component() ) {
                $ib->set('subtype', 'user');
            }
        }
        // main content
        if ($main_content || $sub['own_design']) {
            $ib->set('individual', 1);
        }

        $result = array('status' => 'ok');

        $ib->save();


        // после добавления инфоблока сразу можно показать форму добавления объекта в него
        if ($input['subtype'] == 'block' && $ib['default_action'] == 'index') {
            $result = array();
            $content_input = array('fx_infoblock' => $ib['id']);
            $content_controller = new fx_controller_admin_content();
            $result = $content_controller->add($content_input);
            $result['clear_previous_steps'] = 1;
            $result['step'] = 10;
        }

        return $result;
    }

    public function save($infoblock, $input) {
        return false;
    }

    public function choose_content($input) {
        $fx_core = fx_core::get_object();
        $id = $input['fx_infoblock'] ? $input['fx_infoblock'] : $input['id'];
        $infoblock = fx::data('infoblock')->get_by_id($id);
        $component = fx::data('component')->get_by_id($infoblock['essence_id']);

        $fields[] = array('type' => 'label', 'label' => 'Выберите объекты<br/>тут должен быть фильтр');
        $fields[] = array('type' => 'hidden', 'name' => 'essence', 'value' => 'infoblock');
        $fields[] = array('type' => 'hidden', 'name' => 'action', 'value' => 'choose_content');
        $fields[] = array('type' => 'hidden', 'name' => 'id', 'value' => $infoblock['id']);
        $fields[] = array('name' => 'posting', 'type' => 'hidden', 'value' => 1);

        $values = fx_infoblock_content::objects_list('component' . $component['id'], 'output=array&ctpl=select');
        $value = $infoblock['content_selection']['content'] ? $infoblock['content_selection']['content'] : array();
        $value = array_values($value);

        $fields[] = array('name' => 'selected_content_ids', 'label' => '<h2>Выберите объекты</h2>', 'type' => 'itemselect', 'values' => $values, 'value' => $value, 'multiple' => 1);

        $result['dialog_title'] = 'Выбор объектов';
        $result['fields'] = $fields;

        return $result;
    }

    /**
     * Сохранение ручного выбора объектов для вывода
     */
    public function choose_content_save($input) {
        $infoblock = fx::data('infoblock')->get_by_id($input['id']);

        $content_selection = array();
        $content_selection['type'] = 'manual';
        $content_selection['content'] = is_array($input['selected_content_ids']) ? array_map('intval', $input['selected_content_ids']) : array();

        $infoblock->set('content_selection', $content_selection)->save();

        return array('status' => 'ok');
    }

    public function file_edit($input) {
        $site_name = fx::data('site')->get_by_host_name()->get('name');

        $fields[] = $this->ui->hidden('essence', 'infoblock');
        $fields[] = $this->ui->hidden('action', 'file_edit');
        $fields[] = $this->ui->hidden('keyword', $input['keyword']);
        $fields[] = $this->ui->hidden('subdivision_id', $input['subdivision_id']);
        $fields[] = $this->ui->hidden('posting', 1);

        $fields[] = array('name' => 'file', 'type' => 'file', 'label' => 'Загрузите изображение');

        if ($input['id']) {
            $infoblock = fx::data('infoblock')->get_by_id($input['id']);
            $fields[] = $this->ui->hidden('id', $infoblock['id']);
            $replace_values = $infoblock['replace_value'];
            if ($replace_values['src']) {
                $fields[] = $this->ui->label('<label>Текущее изображение:</label><img style="max-width:120px;" src="' . $replace_values['src'] . '" />');
            }
            $alt_text = $replace_values['alt'];
            $title_text = $replace_values['title'];
        } else {
            $alt_text = $site_name;
            $title_text = $site_name;
        }


        $fields[] = $this->ui->input('alt', 'Альтернативный текст', $alt_text);
        $fields[] = $this->ui->input('title', 'Всплывающий текст при наведении на изображение', $title_text);

        $result['fields'] = $fields;
        return $result;
    }

    public function file_edit_save($input) {
        $fx_core = fx_core::get_object();

        $src = false;
        $file = $input['file'];

        if ($file['error'] && $file['error'] != UPLOAD_ERR_NO_FILE) {
            return array('status' => 'error', 'text' => $fx_core->files->get_file_error($file['error']));
        } else if (!$file['error']) {
            $filename = $file['name'];
            $src = fx::config()->HTTP_FILES_PATH . 'infoblock/' . $filename;
            $fx_core->files->move_uploaded_file($file['tmp_name'], $src);

            $image = $fx_core->files->is_image($src);
            if (!$image) {
                $fx_core->files->rm($src);
                return array('status' => 'error', 'text' => 'Загруженный файл не является изображением');
            }
        }

        if ($input['id']) {
            $infoblock = fx::data('infoblock')->get_by_id($input['id']);
        } else {
            $infoblock = fx::data('infoblock')->create();
            $infoblock['keyword'] = $input['keyword'];

            $subdivision = fx::data('subdivision')->get_by_id($input['subdivision_id']);
            if ($subdivision['own_design']) {
                $infoblock['subdivision_id'] = $subdivision['id'];
                $infoblock['individual'] = 1;
            }
        }

        $replace_value = $infoblock['replace_value'];
        $replace_value['alt'] = $input['alt'];
        $replace_value['title'] = $input['title'];
        if ($src) {
            $replace_value['src'] = $src;
        }
        $infoblock->set('replace_value', $replace_value)->save();

        return array('status' => 'ok');
    }

}

?>

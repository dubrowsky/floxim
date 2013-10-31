<?php
class fx_controller_admin_infoblock extends fx_controller_admin {

    protected function _component_actions ( $key ) {
        $arr = array(
            'listing' => fx::lang('List','system'),
            'mirror' => fx::lang('Mirror','system'),
            'record' => fx::lang('Single entry','system')
        );
        return empty($key) ? $arr : $arr[$key];
    }
    
    
    /**
     * Выбор контроллера-экшна
     */
    public function select_controller($input) {
        $fields = array(
            $this->ui->hidden('action', 'select_settings'),
            $this->ui->hidden('essence', 'infoblock'),
            $this->ui->hidden('fx_admin', true),
            $this->ui->hidden('area', $input['area']),
            $this->ui->hidden('page_id', $input['page_id']),
            $this->ui->hidden('admin_mode', $input['admin_mode'])
        );
	
        /* Список контроллеров */
        $fields['controller'] = array(
            'type' => 'tree', 
            'name' => 'controller',
            'values' => array()
        );
        
        fx::env('page', $input['page_id']);
        
        $controllers = fx::data('component')->all();
        $controllers->concat(fx::data('widget')->all());
        
        foreach ($controllers as $c) {
            $controller_type = $c instanceof fx_component ? 'component' : 'widget';
            $controller_name = $controller_type.'_'.$c['keyword'];
            $c_item = array(
                'data' => $c['name'],
                'metadata' => array('id' => $controller_name),
                'children' => array()
            );
            $ctrl = fx::controller($controller_name);
            $actions = $ctrl->get_actions();
            foreach ($actions as $action_code => $action_info) {
                $act_ctr = fx::controller($controller_name.'.'.$action_code);
                $act_templates = $act_ctr->get_available_templates(fx::env('layout'));
                if (count($act_templates) == 0) {
                    continue;
                }
                $action_name = ($c['name'] ? $c['name'] . ' / ' : '').$action_info['name']; 
                switch ($controller_type) {
                    case 'widget':
                        $action_type = 'widget';
                        break;
                    case 'component':
                        if (!preg_match("~^listing~", $action_code)) {
                            $action_type = 'widget';
                        } elseif (preg_match('~mirror~', $action_code)) {
                            $action_type = 'mirror';
                        } else {
                            $action_type = 'content';
                        }
                        break;
                }
                $c_item['children'][]= array(
                    'data' => $action_name,
                    'metadata' => array(
                        'id' => $controller_type.'_'.$c['keyword'].'.'.$action_code,
                        'description' => $action_info['description'],
                        'type' => $action_type
                    )
                );
            }
            if (count($c_item['children']) > 0) {
                $fields['controller']['values'][]= $c_item;
            }
        }
        //dev_log($fields);
        $this->response->add_form_button(array(
            'key' => 'next',
            'label' => fx::lang('Next','system')
        ));
        $this->response->add_form_button(array(
            'key' => 'finish',
            'label' => fx::lang('Finish','system')
        ));
        $result = array(
            'fields' => $fields,
            'dialog_title' => fx::lang('Adding infoblock','system'),
            'dialog_button' => array(
                array('key' => 'save', 'text' => fx::lang('Next','system'))
            )
    	);
        return $result;
    }
    
    protected function _get_controller_name($controller) {
        list($controller, $action) = explode(".", $controller);
        list($type, $controller) = explode("_", $controller);
        if (!$type) {
            return $controller;
        }
        $ctr = fx::data($type, $controller);
        if ($type == 'component') {
            $action_name = $this->_component_actions($action);
        } else {
            $action_name = fx::lang('Widget','system');
        }
        return $ctr['name'].' / '.$action_name;
    }
    
    /**
     * Выбор настроек для контроллера-экшна
     * 
     */
    
    public function select_settings($input) {
        // Текущий (редактируемый) инфоблок
    	$infoblock = null;
        // special mode for layouts
        $is_layout = isset($input['mode']) && $input['mode'] == 'layout';
        if (isset($input['page_id'])) {
            // устанавливаем в окружение текущую страницу
            // из нее можно получить лейаут
            fx::env('page', $input['page_id']);
        }
    	
    	if (isset($input['id']) && is_numeric($input['id'])) {
            // Редактируем существующий инфоблок
            /* @var $infoblock fx_infoblock */
            $infoblock = fx::data('infoblock', $input['id']);
            $controller = $infoblock->get_prop_inherited('controller');
            $action = $infoblock->get_prop_inherited('action');
            $i2l = $infoblock->get_visual();
    	} else {
            // Создаем новый, тип и ID контроллера получаем с предыдущего шага
            list($controller, $action) = explode(".", $input['controller']);
            $site_id = fx::data('content_page', $input['page_id'])->get('site_id');
            $infoblock = fx::data("infoblock")->create(array(
                'name' => $this->_get_controller_name($input['controller']),
                'controller' => $controller,
                'action' => $action,
                'page_id' => $input['page_id'],
                'site_id' => $site_id
            ));
            $last_visual = fx::data('infoblock_visual')->
                    where('area', $input['area'])->
                    order(null)->
                    order('priority', 'desc')->
                    one();
            $priority = $last_visual ? $last_visual['priority'] + 1 : 0;
            $i2l = fx::data('infoblock_visual')->create(array(
                'area' => $input['area'],
                'layout_id' => fx::env('layout'),
                'priority' => $priority
            ));
            $infoblock->set_visual($i2l);
    	}


        if (!isset($infoblock['params']) || !is_array($infoblock['params'])) {
            $infoblock['params'] = array();
        }
        
        
        if (!$is_layout) {
            $controller_name = $controller;
            $controller = fx::controller($controller);
            $controller->set_action($action);
            $controller->set_input($input);
            $settings = $controller->get_action_settings($action);
            foreach ($infoblock['params'] as $ib_param => $ib_param_value) {
                if (isset($settings[$ib_param])) {
                    $settings[$ib_param]['value'] = $ib_param_value;
                }
            }
            //$this->response->add_tab('settings', fx::lang('What to show','system'));
            $this->response->add_fields(
                    array(array(
                        'label' => fx::lang('Block name','system'),
                        'name' => 'name', 
                        'value' => $infoblock['name']
                    ))
            );
            //$this->response->add_fields($settings, 'settings', 'params');
            dev_log('done', $settings);
            $this->response->add_fields($settings, false, 'params');
        }
        
        $format_fields = $this->_get_format_fields($infoblock);

        if (!$is_layout) {
            //$this->response->add_tab('visual', fx::lang('How to show','system'));
        }
        //$this->response->add_fields($format_fields, $is_layout ? false : 'visual', 'visual');
        $this->response->add_fields($format_fields, false, 'visual');
        
        
        $c_page = fx::data('content_page', $input['page_id']);
        $scope_fields = $this->_get_scope_fields($infoblock, $c_page, $input['admin_mode']);
        
        $scope_tab = !$is_layout;
        if ($scope_tab) {
            // выставляем в false и ищем хоть одно не-хидден поле
            $scope_tab = false;
            foreach ($scope_fields as $scope_field) {
                if ($scope_field['type'] != 'hidden') {
                    $scope_tab = true;
                    break;
                }
            }
        }

        if ($scope_tab) {
            //$this->response->add_tab('scope', fx::lang('Where to show','system'));
        }
        //$this->response->add_fields($scope_fields, $scope_tab ? 'scope' : false, 'scope');
        $this->response->add_fields($scope_fields, false, 'scope');
        if ($is_layout) {
            $this->response->add_field(array(
               'type' => 'bool',
                'name' => 'create_inherited',
                'label' => 'Create inherited'
            ));
            if ($infoblock['parent_infoblock_id']) {
                $this->response->add_field(array(
                   'type' => 'bool',
                    'name' => 'delete_inherited',
                    'label' => 'Delete inherited'
                ));
            }
        }
        
        if ($input['settings_sent'] == 'true') {
            if ($is_layout) {
                $this->response->set_reload(true);
            }
            if (
                    $input['delete_inherited'] && 
                    $infoblock['parent_infoblock_id'] != 0
                ) {
                $infoblock->delete();
                $this->response->set_status_ok();
                return;
            }
            $inherit_mode = $input['create_inherited'];
            
            
            if ($inherit_mode) {
                $source_ib = $infoblock;
                $source_i2l = $i2l;
                $infoblock = fx::data('infoblock')->create(array(
                    'parent_infoblock_id' => $source_ib['id'],
                    'site_id' => $source_ib['site_id'],
                    'checked' => true
                ));
                $i2l = fx::data('infoblock_visual')->create(array(
                    'layout_id' => $source_i2l['layout_id']
                ));
            }
            $infoblock['name'] = $input['name'];
            $action_params = array();
            if (!$is_layout) {
                foreach (array_keys($settings) as $setting_key) {
                    if (isset($input['params'][$setting_key])) {
                        $action_params[$setting_key] = $input['params'][$setting_key];
                    } else {
                        $action_params[$setting_key] = false;
                    }
                }
            }
            $infoblock['params'] = $action_params;
            if (!is_array($infoblock['scope'])) {
                $infoblock['scope'] = array();
            }
            if ($input['scope']['page_id'] == 0) {
                $input['scope']['pages'] = 'all';
            }
            $ib_scope = array(
                'pages' => $input['scope']['pages'],
                'page_type' => $input['scope']['page_type']
            );
            $infoblock['scope'] = $ib_scope;
            $infoblock['page_id'] = $input['scope']['page_id'];
            
            $i2l['wrapper'] = fx::dig($input, 'visual.wrapper');
            $i2l['template'] = fx::dig($input, 'visual.template');
            $infoblock->save();
            $i2l['infoblock_id'] = $infoblock['id'];
            $i2l->save();
            $controller->input['id'] = $i2l['infoblock_id'];
            $controller->after_save();
            $this->response->set_status_ok();
            $this->response->set_prop('infoblock_id', $infoblock['id']);
            return;
        }
    	
    	$result = array(
            'dialog_title' => $is_layout ? 
                fx::lang('Page layout','system') : 
                fx::lang('Infoblock settings','system'). 
                ', ' . $controller_name . '.' . $action.' #'.$infoblock['id'],
            'dialog_button' => array(
                array(
                    'key' => 'save', 
                    'text' => $input['id'] 
                                ? fx::lang('Update','system') 
                                : fx::lang('Create','system')
                )
            )
    	);
        if ($input['id']) {
            $is_inherited = $infoblock['parent_infoblock_id'] != 0;
            $result['dialog_button'] []= array(
                'key' => 'inherit', 
                'text' => fx::lang('Create a new rule','system'), 
                'act_as' => 'save'
            );
            if ($is_inherited) {
                $result['dialog_button'] []= array(
                    'key' => 'inherit_delete', 'text' => fx::lang('Remove this rule','system'), 'act_as' => 'save'
                );
            }
            
        }
    	$fields = array(
            $this->ui->hidden('essence', 'infoblock'),
            $this->ui->hidden('action', 'select_settings'),
            $this->ui->hidden('fx_admin', true),
            $this->ui->hidden('settings_sent', 'true'),
            $this->ui->hidden('controller', $input['controller']),
            $this->ui->hidden('page_id', $input['page_id']),
            $this->ui->hidden('area', $input['area']),
            $this->ui->hidden('id', $input['id']),
            $this->ui->hidden('mode', $input['mode'])
    	);
    	
    	$this->response->add_fields($fields);
    	return $result;
    }
    
    public function layout_settings($input) {
        $c_page = fx::data('content_page', $input['page_id']);
        $ib = fx::data('infoblock')->
                get_for_page($input['page_id'])->
                find_one(
                    function ($item) {
                        return $item->get_prop_inherited('controller') == 'layout';
                    }
                );
        return $this->select_settings(array(
            'id' => $ib['id'],
            'page_id' => $input['page_id'],
            'mode' => 'layout'
        ));
        $this->response->add_fields($this->_get_format_fields($ib));
        $this->response->add_fields($this->_get_scope_fields($ib, $c_page), false, 'scope');
    }
    
    /*
     * Получение полей формы для вкладки "Где показывать"
     * @param fx_infoblock $infoblock - инфоблок, для которого подыскиваем место
     * @param fx_content_page $c_page - страница, на которой открыли окошко настроек
     */
    
    protected function _get_scope_fields(
                fx_infoblock $infoblock, 
                fx_content_page $c_page, 
                $admin_mode, 
                $defaults = array() // kill em pls
            ) {
        
        if (!is_array($defaults)) {
            $defaults = array();
        }
        $fields = array();
        
        if ($admin_mode == 'design') {
            $index_page_id = fx::env('site')->get('index_page_id');
            $fields []= array(
                'type' => 'hidden',
                'name' => 'page_id',
                'value' => $index_page_id
            );
            $fields[]= array(
                'type' => 'hidden',
                'name' => 'pages',
                'value' => 'descendants'
            );
            return $fields;
        }
        
        $path_vals = array('0' => fx::lang('On all pages','system'));
        $path_ids = $c_page->get_parent_ids();
        $path = fx::data('content_page', $path_ids);
        $path []= $c_page;
        foreach ($path as $level => $pp) {
            $path_vals [$pp['id']]= str_repeat('&nbsp;&nbsp;&nbsp;', $level).$pp['name'];
        }
        
        if ( ! ($c_pages_val = fx::dig($infoblock, 'scope.pages')) ) {
            $c_pages_val = $defaults['pages'] ? $defaults['pages'] : 'all';
        }
        
        if ($c_pages_val == 'all'){
            $c_page_id_val = 0;
        } else {
            if ($infoblock['id'] || !$defaults['page_id']) {
                $c_page_id_val = $infoblock['page_id'];
            } else {
                $c_page_id_val = $defaults['page_id'];
            }
        }
        
        $fields []= array(
            'type' => 'select', 
            'name' => 'page_id', 
            'label' => fx::lang('Page','system'),
            'values' => $path_vals,
            'value' => $c_page_id_val
        );
                
        $page_vals = array(
            'this' => fx::lang('Only on the page','system')
        );
        
        //if ($c_page['url'] != '/') {
            $page_vals['children'] = fx::lang('Only on children','system');
            $page_vals['descendants'] = fx::lang('On the page and it\'s children','system');
        //}
        
        $fields []= array(
            'type' => 'select', 
            'name' => 'pages', 
            'values' => $page_vals,
            'value' => $c_pages_val,
            'parent' => array('page_id' => '!=0')
        );
        
        $page_types = array();
        $page_types []= array('', fx::lang('-Any-', 'system'));
        $coms = fx::data('component')->get_select_values('page');
        foreach ($coms as $com) {
            $com = fx::data('component', $com[0]);
            $page_types []= array($com['keyword'], $com['item_name']);
        }
        
        $fields []= array(
            'name' => 'page_type',
            'label' => fx::lang('Only on pages of type','system'),
            'value' => fx::dig($infoblock, 'scope.page_type'),
            'parent' => array('pages' => '!=this'),
            'type' => 'select',
            'values' => $page_types
        );
        return $fields;
    }
    
    /*
     * Получение полей формы для вкладки "Как показывать"
     */
    protected function _get_format_fields(fx_infoblock $infoblock) {
        $i2l = $infoblock->get_visual();
        $fields = array(
            array(
                'label' => "Area",
                'name' => 'area',
                'value' => $i2l['area'],
                'type' => 'hidden'
            )
        );

        $wrappers = array('' => fx::lang('With no wrapper','system'));
        //$templates = array('auto.auto' => ' - ' . fx::lang('Auto select','system') . ' - ');
        $layout_name = fx::data('layout', $i2l['layout_id'])->get('keyword');
        
        $controller_name = $infoblock->get_prop_inherited('controller');

        $action_name = $infoblock->get_prop_inherited('action');

        // Собираем доступные wrappers
        // TODO: разобраться зачем они нужны и нужны ли вообще и если нужны - перенести логику сборки в соответствующее место, чтобы не плодить сущности
        
        if ( ($layout_tpl = fx::template('layout_'.$layout_name)) ) {
            foreach ( $layout_tpl->get_template_variants() as $tplv) {
                $full_id = 'layout_'.$layout_name.'.'.$tplv['id'];
                if ($tplv['of'] == 'block') {
                    $wrappers[$full_id] = $tplv['name'];
                }
            }
        }

        // Собираем доступные шаблоны
        $controller = fx::controller($controller_name.'.'.$action_name);
        dev_log('ctr', $controller_name.'.'.$action_name);
        $tmps = $controller->get_available_templates($layout_name);
        if ( !empty($tmps) ) {
            foreach ( $tmps as $template ) {
                $templates[$template['full_id']] = $template['name'];// . ' (' . $template['full_id'] . ')';
            }
        }

        $fields []= array(
            'label' => fx::lang('Template','system'),
            'name' => 'template',
            'type' => 'select',
            'values' => $templates,
            'value' => $i2l['template']
        );
        if ($controller_name != 'layout') {
            $fields []= array(
                'label' => fx::lang('Block wrapper template','system'),
                'name' => 'wrapper',
                'type' => 'select',
                'values' => $wrappers,
                'value' => $i2l['wrapper']
            );
        }
        return $fields;
    }
	
    /*
     * Сохранить несколько полей из front-end
     */
    public function save_var($input) {
        /* @var $ib fx_infoblock */
        $ib = fx::data('infoblock', $input['infoblock']['id']);
        // для инфоблоков-лейаутов всегда сохраняем параметры в корневой инфоблок
        if ($ib->get_prop_inherited('controller') == 'layout') {
            $root_ib = $ib->get_root_infoblock();
            $ib_visual = $root_ib->get_visual();
        } elseif ( ($visual_id = fx::dig($input, 'infoblock.visual_id')) ) {
            $ib_visual = fx::data('infoblock_visual', $visual_id);
        } else {
            $ib_visual = $ib->get_visual();
        }
        foreach ($input['vars'] as $c_var) {
            $var = $c_var['var'];
            $value = $c_var['value'];
            if ($var['var_type'] == 'visual' && $ib_visual) {
                $wrapper_name = $ib_visual['wrapper'];
                if ($var['template'] == $wrapper_name) {
                    $wrapper_visual = $ib_visual['wrapper_visual'];
                    if (!is_array($wrapper_visual)) {
                        $wrapper_visual = array();
                    }
                    if ($value == 'null') {
                        unset($wrapper_visual[$var['id']]);
                    } else {
                        $wrapper_visual[$var['id']] = $value;
                    }
                    $ib_visual['wrapper_visual'] = $wrapper_visual;
                } else {
                    $template_visual = $ib_visual['template_visual'];
                    if (!is_array($template_visual)) {
                        $template_visual = array();
                    }
                    if ($value == 'null') {
                        unset($template_visual[$var['id']]);
                    } else {
                        $template_visual[$var['id']] = $value;
                    }
                    $ib_visual['template_visual'] = $template_visual;
                }
                $ib_visual->save();
            } elseif ($var['var_type'] == 'content') {
                $content_id = $var['content_id'];
                $content_type_id = $var['content_type_id'];
                $content = fx::data(array('content',$content_type_id), $content_id);
                if ($content) {
                    $content[$var['name']] = $value;
                    dev_log('saving val', $value);
                    $content->save();
                }
            }
        }
    }
    
    public function delete_infoblock($input) {
        /* @var $infoblock fx_infoblock */
        $infoblock = fx::data('infoblock', $input['id']);
        $controller = $infoblock->get_prop_inherited('controller');
        $action = $infoblock->get_prop_inherited('action');
        $controller = fx::controller($controller);
        $controller->set_action($action);
        $controller->set_input($input);
        $fields = array(
            array(
                'label' => fx::lang('I am REALLY sure','system'),
                'name' => 'delete_confirm',
                'type' => 'checkbox'
            ),
            $this->ui->hidden('id', $input['id']),
            $this->ui->hidden('essence', 'infoblock'),
            $this->ui->hidden('action', 'delete_infoblock'),
            $this->ui->hidden('fx_admin', true)
        );        
        $ib_content = $infoblock->get_owned_content();
        if ($ib_content->length > 0) {
            $fields[]= array(
                'name' => 'content_handle',
                'label' => fx::lang('The infoblock contains some content','system') . ', <b>' . $ib_content->length . '</b> '. fx::lang('items. What should we do with them?','system'),
                'type' => 'select',
                'values' => array('unbind' => fx::lang('Unbind/Hide','system'), 'delete' => fx::lang('Delete','system')),
                'parent' => array('delete_confirm' => true)
            );
        }
        if ($infoblock['controller'] == 'layout' && !$infoblock['parent_infoblock_id']) {
            unset($fields[0]);
            $fields []= array('type' => 'html', 'html' => fx::lang('Layouts can not be deleted','system'));
        }
        $this->response->add_fields($fields);
        if ($input['delete_confirm']) {
            $this->response->set_status_ok();
            if ($input['content_handle'] == 'delete') {
                foreach ($ib_content as $ci) {
                    $ci->delete();
                }
            }

            $infoblock->delete();
            $controller->after_delete();
        }
    }
    
    protected function _get_area_visual($area, $layout_id, $site_id) {
        return fx::db()->get_results(
                "SELECT V.* 
                    FROM {{infoblock}} as I 
                    INNER JOIN {{infoblock_visual}} as V ON V.infoblock_id = I.id
                    WHERE
                        I.site_id = '".$site_id."' AND
                        V.layout_id = '".$layout_id."' AND 
                        V.area = '".$area."'
                    ORDER BY V.priority"
        );
    }
    
    public function move($input) {
        if (!isset($input['visual_id']) || !isset($input['area'])) {
            return;
        }
        
        $vis = fx::data('infoblock_visual', $input['visual_id']);
        if (!$vis) {
            return;
        }
        
        $infoblock = fx::data('infoblock', $input['infoblock_id']);
        if (!$infoblock) {
            return;
        }
        
        // переносим из области в область
        // нужно пересортировать блоки из старой area
        // пока очень тупо, по порядку
        if ($vis['area'] != $input['area']) {
            $source_vis = $this->_get_area_visual(
                $vis['area'], $vis['layout_id'], $infoblock['site_id']
            );
            $cpos = 1;
            foreach ($source_vis as $csv) {
                if ($csv['id'] == $vis['id']) {
                    continue;
                }
                fx::db()->query(
                    "UPDATE {{infoblock_visual}} 
                    SET priority = '".$cpos."'
                    WHERE id = '".$csv['id']."'"
                );
                $cpos++;
            }
        }
        
        $target_vis = $this->_get_area_visual($input['area'], $vis['layout_id'], $infoblock['site_id']);
        
        $next_visual_id = isset($input['next_visual_id']) ? $input['next_visual_id'] : null;
        
        $cpos = 1;
        $new_priority = null;
        foreach ( $target_vis as $ctv) {
            if ($ctv['id'] == $vis['id']) {
                continue;
            }
            if ($ctv['id'] == $next_visual_id) {
                $new_priority = $cpos;
                $cpos++;
            }
            if ($ctv['priority'] != $cpos) {
                fx::db()->query(
                    "UPDATE {{infoblock_visual}} 
                    SET priority = '".$cpos."'
                    WHERE id = '".$ctv['id']."'"
                );
            }
            $cpos++;
        }
        if (!$new_priority) {
            $new_priority = $cpos;
        }
        
        fx::db()->query(
            "UPDATE {{infoblock_visual}} 
            SET priority = '".$new_priority."', area = '".$input['area']."'
            WHERE id = '".$vis['id']."'"
        );
        
        return array('status' => 'ok');
        
        $next_vis = null;
        if ($input['next_visual_id']) {
            $next_vis = fx::data('infoblock_visual', $input['next_visual_id']);
        }
        
        if ($next_vis) {
            $new_priority = $next_vis['priority']-1;
        } else {
            $last_priority = fx::db()->get_col(
                'SELECT MAX(priority) FROM {{infoblock_visual}} 
                 WHERE layout_id = '.$vis['layout_id'].' AND area = "'.$input['area'].'"'
            );
            $new_priority = isset($last_priority[0]) ? $last_priority[0] : 1;
        }
        
        $q = "UPDATE {{content_".$ctype.'}} 
                SET priority = priority'.($new_priority > $old_priority ? '-1' : '+1').
                ' WHERE 
                    parent_id = '.$parent_id.' AND 
                    infoblock_id = '.$ib_id.' AND 
                    priority >= '.min($old_priority, $new_priority).  ' AND 
                    priority <='.max($old_priority, $new_priority);
        fx::db()->query($q);
        fx::db()->query('UPDATE {{content_'.$ctype.'}} 
                    SET priority = '.$new_priority.'
                    WHERE id = '.$content['id']);
    }
}
?>
<?php
class fx_controller_admin_infoblock extends fx_controller_admin {

    protected function _component_actions ( $key ) {
        $arr = array(
            'listing' => fx::alang('List','system'),
            'mirror' => fx::alang('Mirror','system'),
            'record' => fx::alang('Single entry','system')
        );
        return empty($key) ? $arr : $arr[$key];
    }
    
    
    /**
     * Select a controller action
     */
    public function select_controller($input) {
        $fields = array(
            $this->ui->hidden('action', 'select_settings'),
            $this->ui->hidden('essence', 'infoblock'),
            $this->ui->hidden('fx_admin', true),
            $this->ui->hidden('area', serialize($input['area'])),
            $this->ui->hidden('page_id', $input['page_id']),
            $this->ui->hidden('admin_mode', $input['admin_mode'])
        );
	
        fx::env('page', $input['page_id']);
        $page = fx::data('content_page', $input['page_id']);
        
        $area_meta = $input['area'];
        
        /* The list of controllers */
        $fields['controller'] = array(
            'type' => 'tree', 
            'name' => 'controller',
            'values' => array()
        );
        
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
                if (isset($action_info['check_context'])) {
                    $is_avail = call_user_func($action_info['check_context'], $page);
                    if (!$is_avail) {
                        continue;
                    }
                }
                $act_ctr = fx::controller($controller_name.'.'.$action_code);
                $act_templates = $act_ctr->get_available_templates(fx::env('layout'), $area_meta);
                if (count($act_templates) == 0) {
                    continue;
                }
                if (!isset($action_info['name'])) {
                    $action_info['name'] = $c['name'];
                }
                
                $action_name = $action_info['name'];
                switch ($controller_type) {
                    case 'widget':
                        $action_type = 'widget';
                        break;
                    case 'component':
                        if (preg_match('~^list_(selected|filter)~', $action_code)) {
                            $action_type = 'mirror';
                        } elseif (preg_match('~^list_infoblock~', $action_code)) {
                            $action_type = 'content';
                        } else {
                            $action_type = 'widget';
                        }
                        break;
                }
                $c_item['children'][]= array(
                    'data' => $action_name,
                    'metadata' => array(
                        'id' => $controller_type.'_'.$c['keyword'].'.'.$action_code,
                        'description' => $action_info['description'],
                        'type' => $action_type,
                        'icon' => $action_info['icon'],
                        'icon_extra' => $action_info['icon_extra'],
                    )
                );
            }
            if (count($c_item['children']) > 0) {
                $fields['controller']['values'][]= $c_item;
            }
        }
        $this->response->add_form_button(array(
            'key' => 'next',
            'label' => fx::alang('Next','system')
        ));
        $this->response->add_form_button(array(
            'key' => 'finish',
            'label' => fx::alang('Finish','system')
        ));
        $result = array(
            'fields' => $fields,
            'header' => fx::alang('Adding infoblock','system'),
            'dialog_button' => array(
                array('key' => 'save', 'text' => fx::alang('Next','system'))
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
            $action_name = fx::alang('Widget','system');
        }
        return $ctr['name'].' / '.$action_name;
    }
    
    protected function _get_layouts ($page_id) {
        $page = fx::data('content_page', $page_id);
        if (!$page) {
            return;
        }
        $ids []= 0; // root
        $ids = $page->get_parent_ids();
        //$ids []= $page['id'];
        $layouts = array();
        foreach ($ids as $id) {
            $infoblocks = fx::data('infoblock')->get_for_page($id);
            if (!$infoblocks) {
                continue;
            }
            foreach ($infoblocks as $ib) {
                if ($ib->get_prop_inherited('controller') == 'layout') {
                    if ($ib['scope']['pages'] != 'this') {
                        if ($ib['scope']['page_type'] && $ib['scope']['page_type'] != $page['type'])  {
                            continue;
                        }
                        $layouts[] = $ib;
                    }
                }
            }
        }
        return $layouts;
    } 

    protected function _compare_templates ($input, $layout_infoblock) {
        $visual = fx::data('infoblock_visual')->where('infoblock_id', $layout_infoblock['id'])->one();
        return $visual['template'] == $input;
    }

    protected function _compare_scope ($input, $infoblock) {
        $ib_scope = array(
            'page_id' => $infoblock['page_id'],
            'pages' => $infoblock['scope']['pages'],
            'page_type' => $infoblock['scope']['page_type']
        );
        $input_scope = array();
        list($input_scope['page_id'], $input_scope['pages'], $input_scope['page_type']) = explode("-", $input);
        return $ib_scope == $input_scope;
    }
    /**
     * The choice of settings for the controller-action games
     *
     */

    
    public function select_settings($input) {
        // The current, editable) InfoBlock
    	$infoblock = null;
        // special mode for layouts
        $is_layout = isset($input['mode']) && $input['mode'] == 'layout';
        
        if (isset($input['page_id'])) {
            // set into the environment of the current page
            // it is possible to get the layout
            fx::env('page', $input['page_id']);
        }
        
        $area_meta = is_string($input['area']) ? unserialize($input['area']) : $input['area'];
    	
    	if (isset($input['id']) && is_numeric($input['id'])) {
            // Edit existing InfoBlock
            /* @var $infoblock fx_infoblock */
            $infoblock = fx::data('infoblock', $input['id']);
            $controller = $infoblock->get_prop_inherited('controller');
            $action = $infoblock->get_prop_inherited('action');
            $i2l = $infoblock->get_visual();
    	} else {
            // Create a new type and ID of the controller received from the previous step
            list($controller, $action) = explode(".", $input['controller']);
            $site_id = fx::data('content_page', $input['page_id'])->get('site_id');
            $infoblock = fx::data("infoblock")->create(array(
                //'name' => $this->_get_controller_name($input['controller']),
                'controller' => $controller,
                'action' => $action,
                'page_id' => $input['page_id'],
                'site_id' => $site_id
            ));
            $last_visual = fx::data('infoblock_visual')->
                    where('area', $area_meta['id'])->
                    order(null)->
                    order('priority', 'desc')->
                    one();
            $priority = $last_visual ? $last_visual['priority'] + 1 : 0;
            $i2l = fx::data('infoblock_visual')->create(array(
                'area' => $area_meta['id'],
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
            $controller = fx::controller(
                    $controller.'.'.$action, 
                    array('infoblock_id' => $infoblock['id']) + $infoblock['params']
            );
            $settings = $controller->get_action_settings($action);
            if (!$infoblock['id']) {
                $cfg = $controller->get_config();
                $infoblock['name'] = $cfg['actions'][$action]['name'];
            }
            foreach ($infoblock['params'] as $ib_param => $ib_param_value) {
                if (isset($settings[$ib_param])) {
                    $settings[$ib_param]['value'] = $ib_param_value;
                }
            }
            //$this->response->add_tab('settings', fx::lang('What to show','system'));
            $this->response->add_fields(
                    array(array(
                        'label' => fx::alang('Block name','system'),
                        'name' => 'name', 
                        'value' => $infoblock['name'],
                        'tip' => $infoblock['controller'].'.'.$infoblock['action']
                    ))
            );
            //$this->response->add_fields($settings, 'settings', 'params');
            $this->response->add_fields($settings, false, 'params');
        } else {
            $controller = null;
        }
        
        $format_fields = $this->_get_format_fields($infoblock, $area_meta);

        if (!$is_layout) {
            //$this->response->add_tab('visual', fx::lang('How to show','system'));
        }
        //$this->response->add_fields($format_fields, $is_layout ? false : 'visual', 'visual');
        $this->response->add_fields($format_fields, false, 'visual');
        
        
        $c_page = fx::data('content_page', $input['page_id']);
        $scope_fields = $this->_get_scope_fields($infoblock, $c_page);
        $this->response->add_fields($scope_fields, false, 'scope');
        /*
        $scope_tab = !$is_layout;
        if ($scope_tab) {
            // put in false and are looking for one not-hidden field
            $scope_tab = false;
            foreach ($scope_fields as $scope_field) {
                if ($scope_field['type'] != 'hidden') {
                    $scope_tab = true;
                    break;
                }
            }
        }
         * 
         */

        
        if ($input['settings_sent'] == 'true') {
            if ($is_layout) {
                $this->response->set_reload(true);
                $layouts = $this->_get_layouts($input['page_id']);
                if ($infoblock['id'] && $input['visual']['template'] != $i2l['template']) {
                    if (!$this->_compare_scope($input['scope']['complex_scope'], $infoblock)) {
                        if ($this->_compare_templates($input['visual']['template'], end($layouts)) && $infoblock['parent_infoblock_id'] != 0) {
                            $infoblock->delete();
                            $this->response->set_status_ok();
                            return;
                        }
                    }
                } else {
                    if (!$this->_compare_templates($input['visual']['template'], end($layouts))) {
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
                }
            }
            
            $infoblock['name'] = $input['name'];
            $action_params = array();
            if (!$is_layout && $settings && is_array($settings)) {
                //foreach (array_keys($settings) as $setting_key) {
                foreach ($settings as $setting_key => $setting) {
                    if (isset($setting['stored']) && !$setting['stored']) {
                        continue;
                    }
                    if (isset($input['params'][$setting_key])) {
                        $action_params[$setting_key] = $input['params'][$setting_key];
                    } else {
                        $action_params[$setting_key] = false;
                    }
                }
            }
            
            $infoblock['params'] = $action_params;
            if (isset($controller) && $controller instanceof fx_controller) {
                $controller->set_input($action_params);
            }
            
            $infoblock->set_scope_string($input['scope']['complex_scope']);
            
            $i2l['wrapper'] = fx::dig($input, 'visual.wrapper');
            $i2l['template'] = fx::dig($input, 'visual.template');
            $is_new_infoblock = !$infoblock['id'];
            $infoblock->save();
            $i2l['infoblock_id'] = $infoblock['id'];
            $i2l->save();
            if ($controller) {
                $controller->set_param('infoblock_id', $infoblock['id']);
                if (isset($controller)) {
                    if ($is_new_infoblock) {
                        $controller->handle_infoblock('install', $infoblock, $input);
                    }
                    $controller->handle_infoblock('save', $infoblock, $input);
                }
            }
            $this->response->set_status_ok();
            $this->response->set_prop('infoblock_id', $infoblock['id']);
            return;
        }
    	
        $result = array(
            'header' => $is_layout ? 
                fx::alang('Page layout','system') :
                fx::alang('Infoblock settings','system').
                ', ' . $controller_name . '.' . $action.' #'.$infoblock['id'],
            'dialog_button' => array(
                array(
                    'key' => 'save', 
                    'text' => $input['id'] 
                                ? fx::alang('Update','system')
                                : fx::alang('Create','system')
                )
            )
    	);
        
        if (!$is_layout) {
            $actions = $controller->get_actions();
            $action_name = $actions[$action]['name'];
        }
        
        if (!$infoblock['id']) {
            $result['header'] = ' <a class="back">'.fx::alang('Adding infoblock','system').'</a>';
            $result['header'] .= ' / '.$action_name;
        } else {
            $result['header'] = 'Settings / <span title="'.$infoblock['id'].'">'.$action_name.'</span>';
        }
        
        if ($input['id']) {
            $is_inherited = $infoblock['parent_infoblock_id'] != 0;
            $result['dialog_button'] []= array(
                'key' => 'inherit', 
                'text' => fx::alang('Create a new rule','system'),
                'act_as' => 'save'
            );
            if ($is_inherited) {
                $result['dialog_button'] []= array(
                    'key' => 'inherit_delete', 'text' => fx::alang('Remove this rule','system'), 'act_as' => 'save'
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
            $this->ui->hidden('area', serialize($area_meta)),
            $this->ui->hidden('id', $input['id']),
            $this->ui->hidden('mode', $input['mode'])
    	);
    	
    	$this->response->add_fields($fields);
    	return $result;
    }
    
    public function layout_settings($input) {
        $c_page = fx::data('content_page', $input['page_id']);
        /*$infoblock = fx::data('infoblock')->
                get_for_page($input['page_id'])->
                find_one(
                    function ($item) {
                        return $item->get_prop_inherited('controller') == 'layout';
                    }
                );*/
        $infoblock = $c_page->get_layout_infoblock();
        
        $c_page = fx::data('content_page', $input['page_id']);
        $scope_fields = $this->_get_scope_fields($infoblock, $c_page);
        $this->response->add_fields($scope_fields, false, 'scope');
        
        $format_fields = $this->_get_format_fields($infoblock);
        $this->response->add_fields($format_fields, false, 'visual');
        
        if ($input['settings_sent']) {
            $this->_save_layout_settings($infoblock, $input);
            return;
        }
        
        $fields = array(
            $this->ui->hidden('essence', 'infoblock'),
            $this->ui->hidden('action', 'layout_settings'),
            $this->ui->hidden('fx_admin', true),
            $this->ui->hidden('settings_sent', 'true'),
            $this->ui->hidden('page_id', $input['page_id'])
    	);
        
        $existing = fx::data('infoblock')->is_layout()->get_for_page($c_page['id'], false);
        if (count($existing) > 1) {
            $existing = fx::data('infoblock')->sort_infoblocks($existing);
            $next = $existing->eq(1);
            $fields []= array(
                'type' => 'button',
                'role' => 'preset',
                'label' => fx::alang('Use wider rule', 'system'),
                'data' => array(
                    'scope[complex_scope]' => $next->get_scope_string(),
                    'visual[template]' => $next->get_prop_inherited('visual.template')
                )
            );
        }
    	
    	$this->response->add_fields($fields);
        $res = array(
            'header' => fx::alang('Layout settings','system'),
            'view' => 'horizontal'
        );
        return $res;
    }
    
    protected function _save_layout_settings($infoblock, $input) {
        $visual = $infoblock->get_visual();
        $old_scope = $infoblock->get_scope_string();
        $new_scope = $input['scope']['complex_scope'];
        
        $old_layout = $visual['template'];
        $new_layout = $input['visual']['template'];
        
        $c_page = fx::data('content_page', $input['page_id']);
        
        if ($old_layout == $new_layout && $old_scope == $new_scope) {
            return;
        }
        $create = false;
        $update = false;
        $delete = false;
        // this is the root infoblock - default rule for all pages
        if (!$infoblock['parent_infoblock_id']) {
            // they changed the scope, we must create new iblock
            // but only if template also changed
            // because if it didn't, default rule will mean the same
            if ($old_scope != $new_scope && $old_layout != $new_layout) {
                $create = true;
            } else {
                // they changed default layout
                $update = true;
            }
        } else {
            // if everything changed, let's create new ib
            if ($old_scope != $new_scope && $old_layout != $new_layout) {
                $create = true;
            } 
            // if there's only one modified param, update existing rule
            else {
                $update = true;
            }
            $existing = fx::data('infoblock')->is_layout()->get_for_page($c_page['id'], false);
            if (count($existing) > 1) {
                $existing = fx::data('infoblock')->sort_infoblocks($existing);
                $next = $existing->eq(1);
                if ($next->get_scope_string() == $new_scope && $next->get_prop_inherited('visual.template') == $new_layout) {
                    $delete = true;
                } 
                //fx::log('existng srtd', $existing, $next);
            }
        }
        if ($delete) {
            $infoblock->delete();
        } elseif ($create) {
            $params = $infoblock->get();
            unset($params['id']);
            $new_ib = fx::data('infoblock')->create($params);
            $c_parent = $infoblock['parent_infoblock_id'];
            $new_ib['parent_infoblock_id'] =  $c_parent ? $c_parent : $infoblock['id'];
            $new_ib->set_scope_string($new_scope);
            $new_vis = fx::data('infoblock_visual')->create(array(
                'layout_id' => $visual['layout_id']
            ));
            $new_vis['template'] = $new_layout;
            $new_ib->save();
            $new_vis['infoblock_id'] = $new_ib['id'];
            $new_vis->save();
        } elseif ($update) {
            $infoblock->set_scope_string($new_scope);
            $visual->set('template', $new_layout);
            $infoblock->save();
            $visual->save();
            fx::log('updating', $infoblock, $visual);
        }
        //fx::log($infoblock, $old_scope.' vs '.$new_scope, $old_layout.' vs '.$new_layout);
    }
    
    /*
     * Receipt of the form fields tab "Where show"
     * @param fx_infoblock $infoblock - information block whose looking for a suitable place
     * @param fx_content_page $c_page - page, where he opened the window settings
     */
    
    protected function _get_scope_fields(
                fx_infoblock $infoblock, 
                fx_content_page $c_page//, 
                //$admin_mode, 
                //$defaults = array() // kill em pls
            ) {
        
        //if (!is_array($defaults)) {
            //$defaults = array();
        //}
        $fields = array();
        
        // ACHTUNG
        
        // format: [page_id]-[descendants|children|this]-[|type_id]
        
        
        $path_ids = $c_page->get_parent_ids();
        $path = fx::data('content_page', $path_ids);
        $path []= $c_page;
        $path_count = count($path);
        $c_type = $c_page['type'];
        
        $c_scope_code = $infoblock->get_scope_string();
        
        $vals = array();
        
        foreach ($path as $i => $pi) {
            $sep = str_repeat(" -- ", $i);
            $pn = '"'.$pi['name'].'"';
            $is_last = $i === $path_count - 1;
            $c_page_id = $pi['id'];
            if ($i === 0) {
                $c_page_id = fx::env('site')->get('index_page_id');
                $vals []= array($c_page_id.'-descendants-', 'All pages');
                if ($path_count > 1) {
                    $vals []= array($c_page_id.'-children-'.$c_type, 'All pages of type '.$c_type);
                }
            }
            if ($is_last) {
                $vals []= array($c_page_id.'-this-', $sep.$pn.' only');
            } else {
                $vals []= array($c_page_id.'-children-', $sep.$pn.' children only');
            }
            if ($i !== 0 ) {
                $vals []= array($c_page_id.'-descendants-', $sep.$pn.' and children');
            }
            if (!$is_last) {
                $vals []= array(
                    $c_page_id.'-children-'.$c_type, 
                    $sep.$pn.' children of type '.$c_type
                );
            }
        }
        $fields []= array(
            'type' => 'select',
            'label' => 'Scope',
            'name' => 'complex_scope',
            'values' => $vals,
            'value' => $c_scope_code
        );
        return $fields;
    }
    
    /*
     * Receipt of the form fields for the tab "How to show"
     */
    protected function _get_format_fields(fx_infoblock $infoblock, $area_meta = null) {
        $i2l = $infoblock->get_visual();
        $fields = array(
            array(
                'label' => "Area",
                'name' => 'area',
                'value' => $i2l['area'],
                'type' => 'hidden'
            )
        );
        $area_suit = fx_template_suitable::parse_area_suit_prop($area_meta['suit']);
        
        $force_wrapper = $area_suit['force_wrapper'];
        $default_wrapper = $area_suit['default_wrapper'];
        
        $wrappers = array();
        $c_wrapper = '';
        if (!$force_wrapper) {
            $wrappers[''] = fx::alang('With no wrapper','system');
            if ($i2l['id'] || !$default_wrapper) { 
                $c_wrapper = $i2l['wrapper'];
            } else {
                $c_wrapper = $default_wrapper[0];
            }
        }
        $layout_name = fx::data('layout', $i2l['layout_id'])->get('keyword');
        
        $controller_name = $infoblock->get_prop_inherited('controller');

        $action_name = $infoblock->get_prop_inherited('action');

        // Collect available wrappers
        $layout_tpl = fx::template('layout_'.$layout_name);
        if ( $layout_tpl ) {
            $template_variants = $layout_tpl->get_template_variants();
            foreach ($template_variants  as $tplv) {
                $full_id = 'layout_'.$layout_name.'.'.$tplv['id'];
                if ($tplv['suit'] == 'local' && $area_meta['id'] != $tplv['area']) {
                    continue;
                }
                if ($force_wrapper && !in_array($tplv['full_id'], $force_wrapper)) {
                    continue;
                }
                    
                if ($tplv['of'] == 'widget_wrapper.show') {
                    $wrappers[$full_id] = $tplv['name'];
                    if ($force_wrapper && empty($c_wrapper)) {
                        $c_wrapper = $full_id;
                    }
                }
            }
        }

        // Collect the available templates
        $controller = fx::controller($controller_name.'.'.$action_name);
        $tmps = $controller->get_available_templates($layout_name, $area_meta);
        if ( !empty($tmps) ) {
            foreach ( $tmps as $template ) {
                $templates[] = array($template['full_id'], $template['name']);
            }
        }

        $fields []= array(
            'label' => fx::alang('Template','system'),
            'name' => 'template',
            'type' => 'select',
            'values' => $templates,
            'value' => $i2l['template']
        );
        if ($controller_name != 'layout' && (count($wrappers) > 1 || !isset($wrappers['']))) {
            $fields []= array(
                'label' => fx::alang('Block wrapper','system'),
                'name' => 'wrapper',
                'type' => 'select',
                'values' => $wrappers,
                'value' => $c_wrapper
            );
        }
        return $fields;
    }
	
    /*
     * Save multiple fields from the front-end
     */
    public function save_var($input) {
        /* @var $ib fx_infoblock */
        
        if (isset($input['page_id'])) {
            fx::env('page_id', $input['page_id']);
        }
        
        $ib = fx::data('infoblock', $input['infoblock']['id']);
        // for InfoBlock-layouts always save the parameters in the root InfoBlock
        if ($ib->get_prop_inherited('controller') == 'layout') {
            $root_ib = $ib->get_root_infoblock();
            $ib_visual = $root_ib->get_visual();
        } elseif ( ($visual_id = fx::dig($input, 'infoblock.visual_id')) ) {
            $ib_visual = fx::data('infoblock_visual', $visual_id);
        } else {
            $ib_visual = $ib->get_visual();
        }


        $contents = array();


        foreach ($input['vars'] as $c_var) {
            $var = $c_var['var'];
            $value = $c_var['value'];
            if ($var['type'] == 'livesearch' && !$value) {
                $value = array();
            }
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

                if (!isset($contents[$var['content_id']])) {
                    $contents[$var['content_id']] = array(
                        'content_type_id' => $var['content_type_id'],
                        'values' => array($var['name'] => $value)
                    );
                } else {
                    $contents[$var['content_id']]['values'][$var['name']] = $value;
                }
            } elseif ($var['var_type'] == 'ib_param') {
                $controller = $ib->init_controller();
                $ib_params = $ib['params'];
                $ib_params[$var['name']] = $value;
                $ib['params'] = $ib_params;
                if (!isset($var['stored']) || ($var['stored'] && $var['stored'] != 'false')) {
                    $ib->save();
                }
                $controller->handle_infoblock('save', $ib, array('params' => array($var['name'] => $value)));
            }
        }

        foreach ($contents as $content_id => $content_info) {
            $content = fx::data(array('content', $content_info['content_type_id']), $content_id);
            if ($content) {
                $content->set_field_values($content_info['values'], array_keys($content_info['values']));
                $content->save();
            }
        }


    }
    
    
    public function delete_infoblock($input) {
        /* @var $infoblock fx_infoblock */
        $infoblock = fx::data('infoblock', $input['id']);
        $controller_name = $infoblock->get_prop_inherited('controller');
        $action = $infoblock->get_prop_inherited('action');
        /*$controller = fx::controller($controller_name);
        $controller->set_action($action);
        $controller->set_input($input);
        $controller->set_param('infoblock_id', $infoblock['id']);
         * 
         */
        $controller = $infoblock->init_controller();
        $fields = array(
            array(
                'label' => fx::alang('I am REALLY sure','system'),
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
                'label' => fx::alang('The infoblock contains some content','system') . ', <b>' . $ib_content->length . '</b> '. fx::alang('items. What should we do with them?','system'),
                'type' => 'select',
                'values' => array('unbind' => fx::alang('Unbind/Hide','system'), 'delete' => fx::alang('Delete','system')),
                //'parent' => array('delete_confirm' => true)
            );
        }
        
        if ($infoblock['controller'] == 'layout' && !$infoblock['parent_infoblock_id']) {
            unset($fields[0]);
            $fields []= array('type' => 'html', 'html' => fx::alang('Layouts can not be deleted','system'));
        }
        $this->response->add_fields($fields);
        if ($input['delete_confirm']) {
            $this->response->set_status_ok();
            if ($input['content_handle'] == 'delete') {
                foreach ($ib_content as $ci) {
                    $ci->delete();
                }
            }
            $controller->handle_infoblock('delete', $infoblock, $input);
            $infoblock->delete();
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
        
        // move from region to region
        // need to rearrange the blocks from the old area
        // until very stupidly, in order
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

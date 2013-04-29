<?php
class fx_controller_admin_infoblock extends fx_controller_admin {

    protected $_component_actions = array(
        'listing' => 'Список',
        'mirror' => 'Mirror',
        'record' => 'Отдельный объект'
        //'add' => 'Добавление',
        //'edit' => 'Редактирование'
    );
    
    
    /**
     * Выбор контроллера-экшна
     */
    public function select_controller($input) {
        $fields = array(
            $this->ui->hidden('action', 'select_settings'),
            $this->ui->hidden('essence', 'infoblock'),
            $this->ui->hidden('fx_admin', true),
            $this->ui->hidden('area', $input['area']),
            $this->ui->hidden('page_id', $input['page_id'])
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
                'action' => array('label' => 'Действие', 'filter' => 'select'),
                'type' => array('label' => 'Тип', 'filter' => 'select'),
                'group' => array('label' => 'Группа', 'filter' => 'select')
            ), 
            'values' => array()
    	);
        foreach (fx::data('component')->get_all() as $c) {
            if (!file_exists(fx::config()->DOCUMENT_ROOT.'/controllers/component/'.$c['keyword'])) {
                continue;
            }
            foreach (array('record', 'mirror', 'listing', 'add', 'edit') as $c_action) {
                if (isset($this->_component_actions[$c_action])) {
                    $fields['controller']['values'][]= array(
                        'name' => array('name' => $c['name'], 'url' => null), 
                        'action' => $this->_component_actions[$c_action],
                        'type' => $types['component'],
                        'group' => $c['group'],
                        'id' => 'component_'.$c['keyword'].'.'.$c_action
                    );
                }
            }
    	}
        
    	foreach (fx::data('widget')->get_all() as  $c){
            $fields['controller']['values'][]= array(
                'name' => array('name' => $c['name'], 'url' => null), 
                'action' => 'Показать',
                'type' => $types['widget'],
                'group' => $c['group'],
                'id' => 'widget_'.$c['keyword'].'.show'
            );
    	}
        
        $result = array(
            'fields' => $fields,
            'dialog_title' => 'Добавление инфоблока',
            'dialog_button' => array(
                array('key' => 'store', 'text' => 'Установить с Store'),
                array('key' => 'save', 'text' => 'Продолжить')
            )
    	);
        return $result;
    }
    
    protected function _get_controller_name($controller) {
        list($controller, $action) = explode(".", $controller);
        list($type, $controller) = explode("_", $controller);
        $ctr = fx::data($type, $controller);
        if ($type == 'component') {
            $action_name = $this->_component_actions[$action];
        } else {
            $action_name = 'Виджет';
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
    	
    	if (isset($input['id']) && is_numeric($input['id'])) {
            // Редактируем существующий инфоблок
            /* @var $infoblock fx_infoblock */
            $infoblock = fx::data('infoblock', $input['id']);
            $controller = $infoblock->get_prop_inherited('controller');
            $action = $infoblock->get_prop_inherited('action');
            $i2l = $infoblock->get_visual();
    	} else {
            // устанавливаем в окружение текущую страницу
            // из нее можно получить лейаут
            fx::env('page', $input['page_id']);
            // Создаем новый, тип и ID контроллера получаем с предыдущего шага
            list($controller, $action) = explode(".", $input['controller']);
            $infoblock = fx::data("infoblock")->create(array(
                'name' => $this->_get_controller_name($input['controller']),
                'controller' => $controller,
                'action' => $action,
                'page_id' => $input['page_id'],
                'site_id' => fx::data('content_page', $input['page_id'])->get('site_id')
            ));
            $i2l = fx::data('infoblock_visual')->create(array(
                'area' => $input['area'],
                'layout_id' => fx::env('layout')
            ));
            $infoblock->set_visual($i2l);
    	}
        if (!isset($infoblock['params']) || !is_array($infoblock['params'])) {
            $infoblock['params'] = array();
        }

        $controller_name = $controller;
        $controller = fx::controller($controller);
        $settings = $controller->get_action_settings($action);
        foreach ($infoblock['params'] as $ib_param => $ib_param_value) {
            if (isset($settings[$ib_param])) {
                $settings[$ib_param]['value'] = $ib_param_value;
            }
        }

        //if (count($settings) > 0) {
        $this->response->add_tab('settings', 'Что показывать');
        $this->response->add_fields(
                array(array(
                    'label' => 'Название блока', 
                    'name' => 'name', 
                    'value' => $infoblock['name']
                )),
                'settings'
        );
        $this->response->add_fields($settings, 'settings', 'params');
        //}
        
        $this->response->add_tab('visual', 'Как показывать');
        $format_fields = $this->_get_format_fields($infoblock);
        $this->response->add_fields($format_fields, 'visual', 'visual');
        
        $c_page = fx::data('content_page', $input['page_id']);
        
        $this->response->add_tab('scope', 'Где показывать');
    	$scope_fields = $this->_get_scope_fields($infoblock, $c_page);
        $this->response->add_fields($scope_fields, 'scope', 'scope');
        
        if ($input['settings_sent'] == 'true') {
            $inherit_mode = $input['fx_dialog_button'] == 'inherit';
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
            foreach (array_keys($settings) as $setting_key) {
                if (isset($input['params'][$setting_key])) {
                    $action_params[$setting_key] = $input['params'][$setting_key];
                } else {
                    $action_params[$setting_key] = false;
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
            dev_log('ib saving', $infoblock, $i2l, $input);
            $this->response->set_status_ok();
            return;
        }
    	
    	$result = array(
            'dialog_title' => 'Настройка инфоблока. ' . $controller_name . '.' . $action,
            'step' => 'settings_select',
            'dialog_button' => array(
                array('key' => 'save', 'text' => $input['id'] ? 'Обновить' : 'Создать')
            )
    	);
        if ($input['id']) {
            $result['dialog_button'] []= array(
                'key' => 'inherit', 'text' => 'Наследоваться', 'act_as' => 'save'
            );
        }
    	$fields = array(
            $this->ui->hidden('essence', 'infoblock'),
            $this->ui->hidden('action', 'select_settings'),
            $this->ui->hidden('fx_admin', true),
            $this->ui->hidden('settings_sent', 'true'),
            $this->ui->hidden('controller', $input['controller']),
            $this->ui->hidden('page_id', $input['page_id']),
            $this->ui->hidden('area', $input['area']),
            $this->ui->hidden('id', $input['id'])
    	);
    	
    	$this->response->add_fields($fields);
    	return $result;
    }
    
    /*
     * Получение полей формы для вкладки "Где показывать"
     * @param fx_infoblock $infoblock - инфоблок, для которого подыскиваем место
     * @param fx_content_page $c_page - страница, на которой открыли окошко настроек
     */
    
    protected function _get_scope_fields(fx_infoblock $infoblock, fx_content_page $c_page) {
        $fields = array();
        
        $path_vals = array('0' => 'На всех страницах');
        $path_ids = $c_page->get_parent_ids();
        $path = fx::data('content_page', $path_ids);
        $path []= $c_page;
        foreach ($path as $level => $pp) {
            $path_vals [$pp['id']]= str_repeat('&nbsp;&nbsp;&nbsp;', $level).$pp['name'];
        }
        
        if ( ! ($c_pages_val = fx::dig($infoblock, 'scope.pages')) ) {
            $c_pages_val = 'all';
        }
        
        
        if ($c_pages_val == 'all'){
            $c_page_id_val = 0;
        } else {
            $c_page_id_val = $infoblock['page_id'];
        }
        
        $fields []= array(
            'type' => 'select', 
            'name' => 'page_id', 
            'label' => 'Страница',
            'values' => $path_vals,
            'value' => $c_page_id_val
        );
                
        $page_vals = array(
            'this' => 'Только на этой странице'
        );
        
        //if ($c_page['url'] != '/') {
            $page_vals['children'] = 'Только на вложеннных страницах';
            $page_vals['descendants'] = 'На этой и на вложенных';
        //}
        
        $fields []= array(
            'type' => 'select', 
            'name' => 'pages', 
            'values' => $page_vals,
            'value' => $c_pages_val,
            'parent' => array('page_id' => '!=0')
        );
        
        $fields []= array(
            'name' => 'page_type',
            'label' => 'Если тип страницы (пусто - любой)',
            'value' => fx::dig($infoblock, 'scope.page_type')
        );
        dev_log('scope fields', $fields, $infoblock, $c_page);
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
        $wrappers = array('' => 'Без оформления');
        $templates = array('auto.auto' => 'Автовыбор');
        $layout_essence = fx::data('layout', $i2l['layout_id']);
        $action_variants = array($infoblock['controller'].".".$infoblock['action']);
        if ($infoblock['action'] == 'mirror') {
            $action_variants []= $infoblock['controller'].".listing";
        }
        // варианты шаблона из лейаута
        foreach ( fx::template('layout_'.$layout_essence['keyword'])->get_template_variants() as $tplv) {
            $full_id = 'layout_'.$layout_essence['keyword'].'.'.$tplv['id'];
            //dev_log('lay', $full_id, $tplv['for']);
            if ($tplv['for'] == 'wrap') {
                $wrappers[$full_id] = $tplv['name'];
            } elseif (in_array($tplv['for'], $action_variants)) {
                $templates[$full_id] = $tplv['name'];
            }
        }
        // варианты шаблонов из шаблона контроллера
        foreach (fx::template($infoblock['controller'])->get_template_variants() as $tplv) {
            $full_id = $infoblock['controller'].'.'.$tplv['id'];
            //dev_log('ctr', $full_id, $tplv['for']);
            if ($tplv['for'] == 'wrap') {
                $wrappers[$full_id] = $tplv['name'];
            } elseif (in_array($tplv['for'], $action_variants)) {
                $templates[$full_id] = $tplv['name'];
            }
        }
        
        $fields []= array(
            'label' => 'Шаблон',
            'name' => 'template',
            'type' => 'select',
            'values' => $templates,
            'value' => $i2l['template']
        );
        if ($infoblock->get_prop_inherited('controller') != 'layout') {
            $fields []= array(
                'label' => 'Оформление блока',
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
        if ( ($visual_id = fx::dig($input, 'infoblock.visual_id')) ) {
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
                $content[$var['name']] = $value;
                $content->save();
            }
        }
    }
    
    public function delete_infoblock($input) {
        /* @var $infoblock fx_infoblock */
        $infoblock = fx::data('infoblock', $input['id']);
        $fields = array(
            array(
                'label' => 'Будет удалено куча всего, я понимаю последствия',
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
                'label' => 'Инфоблок содержит контент, <b>'.$ib_content->length.'</b> шт. Что с ним делать?',
                'type' => 'select',
                'values' => array('unbind' => 'Отвязать/скрыть', 'delete' => 'Удалить'),
                'parent' => array('delete_confirm' => true)
            );
        }
        if ($infoblock['controller'] == 'layout' && !$infoblock['parent_infoblock_id']) {
            unset($fields[0]);
            $fields []= array('type' => 'html', 'html' => 'Удалять лейауты нельзя!');
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
        dev_log("MOVING IB", $input);
        
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
                //$priorities [$vis['id']] = $cpos;
                $new_priority = $cpos;
                $cpos++;
                dev_log('new prior', $new_priority);
            }
            if ($ctv['priority'] != $cpos) {
                fx::db()->query(
                    "UPDATE {{infoblock_visual}} 
                    SET priority = '".$cpos."'
                    WHERE id = '".$ctv['id']."'"
                );
                dev_log($ctv['id'].' -- '.$cpos);
            }
            $cpos++;
        }
        if (!$new_priority) {
            $new_priority = $cpos;
            dev_log('new prior last', $new_priority);
        }
        
        fx::db()->query(
            "UPDATE {{infoblock_visual}} 
            SET priority = '".$new_priority."', area = '".$input['area']."'
            WHERE id = '".$vis['id']."'"
        );
        
        dev_log($target_vis);
        
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
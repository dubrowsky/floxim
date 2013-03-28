<?php
class fx_controller_admin_infoblock extends fx_controller_admin {

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
        
        $actions = array(
            'listing' => 'Список',
            'mirror' => 'Mirror',
            //'item' => 'Отдельный объект',
            //'show' => 'Показать',
            //'add' => 'Добавление',
            //'edit' => 'Редактирование'
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
            foreach (array('item', 'mirror', 'listing', 'add', 'edit') as $c_action) {
                if (isset($actions[$c_action])) {
                    $fields['controller']['values'][]= array(
                        'name' => array('name' => $c['name'], 'url' => null), 
                        'action' => $actions[$c_action],
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
                'action' => $actions['show'],
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
        
        $controller = fx::controller($controller);
        $settings = $controller->get_action_settings($action);
        foreach ($infoblock['params'] as $ib_param => $ib_param_value) {
            if (isset($settings[$ib_param])) {
                $settings[$ib_param]['value'] = $ib_param_value;
            }
        }
        if (count($settings) > 0) {
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
        }
        
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
            
            // SCOPE LOGIC
            $ib_scope = array();
            switch ( $input['scope']['pages']) {
                case 'all':
                    $infoblock['page_id'] = fx::env('home_id');
                    $ib_scope['pages'] = 'all';
                    break;
                case 'this':
                    $ib_scope['pages'] = 'this';
                    $infoblock['page_id'] = $input['page_id'];
                    break;
                case 'brothers':
                    $ib_scope['pages'] = 'children';
                    $c_page = fx::data('content_page', $infoblock['page_id']);
                    $infoblock['page_id'] = $c_page['parent_id'];
                    break;
                case 'descendants':
                    $infoblock['page_id'] = $input['page_id'];
                    $ib_scope['pages'] = 'descendants';
                    break;
            }
            
            $infoblock['scope'] = $ib_scope;
            $i2l['wrapper'] = fx::dig($input, 'visual.wrapper');
            $i2l['template'] = fx::dig($input, 'visual.template');
            $infoblock->save();
            $i2l['infoblock_id'] = $infoblock['id'];
            $i2l->save();
            $this->response->set_status_ok();
            return;
        }
    	
    	$result = array(
            'dialog_title' => 'Настройка инфоблока',
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
        if ( ! ($c_page_val = fx::dig($infoblock, 'scope.pages')) ) {
            $c_page_val = 'all';
        }
        $mnt_page = null;
        if ( ($mnt_page_id = $infoblock['page_id']) && $mnt_page_id != $c_page['id']) {
            $mnt_page = fx::data('content_page', $mnt_page_id);
        }
        
        $page_vals = array(
            'all' => 'На всех страницах'
        );
        $page_vals['this'] = 'Только на этой странице ('.$c_page['url'].')';
        if ($mnt_page && $c_page_val == 'this') {
            $page_vals['mnt'] = 'На странице '.$mnt_page['url'];
        }
        if ($c_page['url'] != '/') {
            $page_vals['brothers'] = 'На страницах этого уровня';
            $page_vals['descendants'] = 'Во всем разделе';
        }
        
        $fields []= array(
            'type' => 'select', 
            'name' => 'pages', 
            'values' => $page_vals,
            'value' => $c_page_val
        );
        
        dev_log('scope fields', $fields, $infoblock, $c_page, $mnt_page, $mnt_page_id);
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
        $wrappers = array('' => 'Без обертки');
        $templates = array('auto.auto' => 'Автовыбор');
        $layout_essence = fx::data('layout', $i2l['layout_id']);
        $action_variants = array($infoblock['controller'].".".$infoblock['action']);
        if ($infoblock['action'] == 'mirror') {
            $action_variants []= $infoblock['controller'].".listing";
        }
        // варианты шаблона из лейаута
        foreach ( fx::template('layout_'.$layout_essence['keyword'])->get_template_variants() as $tplv) {
            $full_id = 'layout_'.$layout_essence['keyword'].'.'.$tplv['id'];
            dev_log('lay', $full_id, $tplv['for']);
            if ($tplv['for'] == 'wrap') {
                $wrappers[$full_id] = $tplv['name'];
            } elseif (in_array($tplv['for'], $action_variants)) {
                $templates[$full_id] = $tplv['name'];
            }
        }
        // варианты шаблонов из шаблона контроллера
        foreach (fx::template($infoblock['controller'])->get_template_variants() as $tplv) {
            $full_id = $infoblock['controller'].'.'.$tplv['id'];
            dev_log('ctr', $full_id, $tplv['for']);
            if ($tplv['for'] == 'wrap') {
                $wrappers[$full_id] = $tplv['name'];
            } elseif (in_array($tplv['for'], $action_variants)) {
                $templates[$full_id] = $tplv['name'];
            }
        }
        
        if ($infoblock->get_prop_inherited('controller') != 'layout') {
            $fields []= array(
                'label' => 'Шаблон-обертка',
                'name' => 'wrapper',
                'type' => 'select',
                'values' => $wrappers,
                'value' => $i2l['wrapper']
            );
        }
        
        $fields []= array(
            'label' => 'Шаблон',
            'name' => 'template',
            'type' => 'select',
            'values' => $templates,
            'value' => $i2l['template']
        );
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
}
?>
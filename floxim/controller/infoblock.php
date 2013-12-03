<?php

class fx_controller_infoblock extends fx_controller {

    /*
     * @return fx_infoblock
     */
    protected function _get_infoblock() {
        if ( ($ib = $this->get_param('infoblock'))) {
            return $ib;
        }
        if (($infoblock_id = $this->get_param('infoblock_id'))) {
            if ($infoblock_id === 'fake') {
                $ib = fx::data('infoblock')->create();
                $ib['id'] = 'fake';
                return $ib;
            }
            return fx::data('infoblock', $infoblock_id);
        }
        return null;
    }


    public function render() {
        
        $infoblock = $this->_get_infoblock();
        
        if (!$infoblock) {
            dev_log('no ib to render', $this);
            die("IB NOT FOUND");
        }
        
        $params = $infoblock->get_prop_inherited('params');
        
        if (!is_array($params)) {
            $params = array();
        }
        // override_infoblock - параметры для всего инфоблока, предпросмотр настроек
        // override_params - параметры только контроллера
        $ib_overs = $this->get_param('override_infoblock');
        
        if ( ($override_params = $this->get_param('override_params'))) {
            $params = array_merge($params, $override_params);
        }
        
        if (isset($ib_overs['params'])) {
            //$params = array_merge($params, $ib_overs['params']);
            $params = $ib_overs['params'];
        }
        
        $params['ajax_mode'] = $this->get_param('ajax_mode');
        
        if (!isset($params['infoblock_id'])) {
            $params['infoblock_id'] = $infoblock['id'];
        }
        
        
        if ($infoblock['id'] === 'fake'){
            $params['is_fake'] = true;
            
            list(
                    $infoblock['controller'], 
                    $infoblock['action']
                ) = explode(".", $ib_overs['controller']);
            
            $infoblock['scope'] = $ib_overs['scope'];
            $infoblock['page_id'] = $ib_overs['page_id'];
        }
        
        $controller = fx::controller(
            $infoblock->get_prop_inherited('controller'), 
            $params, 
            $infoblock->get_prop_inherited('action')
        );
        
        
        $result = $controller->process();
        
        if (is_string($result)) {
            if (fx::env('is_admin')) {
                $result = $this->_add_infoblock_meta($result, $infoblock);
            }
            $result = $controller->postprocess($result);
            return $result;
        }
        
        $controller_meta = fx::dig($result, '_meta');
        if (fx::dig($controller_meta, 'disabled')) { // !fx::is_admin()
            return;
        }
        $tpl = null;
        
        // берем шаблон для предпросмотра
        if (isset($ib_overs['visual']['template'])) {
            $tpl = fx::template($ib_overs['visual']['template']);
        } elseif ( ($tpl_name = $infoblock->get_prop_inherited('visual.template'))) {
            if ($tpl_name != 'auto.auto') {
                $tpl = fx::template($tpl_name);
            }
        }
        
        if (!$tpl) {
            $tpl = $controller->find_template();
        }
        $tpl_params = $infoblock->get_prop_inherited('visual.template_visual');
        
        if (!is_array($tpl_params)) {
            $tpl_params = array();
        }
        if (is_array($result)) {
            $tpl_params = array_merge($tpl_params, $result);
        }

        $tpl_params['infoblock'] = $infoblock;
        $output = $tpl->render($tpl_params);
        $is_subroot = $tpl->is_subroot;
        
        if (isset($ib_overs['visual']['wrapper'])) {
            $wrapper = $ib_overs['visual']['wrapper'];
        } else {
            $wrapper = $infoblock->get_prop_inherited('visual.wrapper');
        }
        
        if ($wrapper) {
            $tpl_wrap = fx::template($wrapper);
            $wrap_params = $infoblock->get_prop_inherited('visual.wrapper_visual');
            if (is_array($wrap_params)) {
                foreach ( $wrap_params as $wrap_param_key => $wrap_param_val) {
                    $tpl_wrap->set_var($wrap_param_key, $wrap_param_val);
                }
            }
            $tpl_wrap->set_var('content', $output);
            $tpl_wrap->set_var('infoblock', $infoblock);
            $output = $tpl_wrap->render();
            $is_subroot = $tpl_wrap->is_subroot;
        }
        if (fx::env('is_admin')) {
            $output = $this->_add_infoblock_meta($output, $infoblock, $controller_meta, $is_subroot);
        }
        $processed_output = $controller->postprocess($output);
        return $processed_output;
    }
    
    /**
     * "Подпись" инфоблока для отладки
     * @param fx_infoblock $infoblock
     */
    protected static function _get_infoblock_sign($infoblock) {
        return "infoblock#".$infoblock["id"].
                ($infoblock['name'] ? " &laquo;".$infoblock['name'].'&raquo;' : '').'<br />'.
                'controller: <b>'.$infoblock->get_prop_inherited('controller').'.'.
                $infoblock->get_prop_inherited('action')."</b><br />".
                'template: <b>'.$infoblock->get_prop_inherited('visual.template').'</b>';
    }
    
    protected function _add_infoblock_meta($html_result, $infoblock, $controller_meta = null, $is_subroot = false) {
        $ib_info = array('id' => $infoblock['id']);
        if (($vis = $infoblock->get_visual()) && $vis['id']) {
            $ib_info['visual_id'] = $vis['id'];
        }
        
        $meta = array(
            'data-fx_infoblock' => $ib_info,
            'class' => 'fx_infoblock fx_infoblock_'.$infoblock['id']
        );
        
        // определяем scope/режим правки инфоблока
        $meta['class'] .= ' fx_infoblock_'.$this->_get_infoblock_scope_name($infoblock);
        
        
        if ($controller_meta) {
            if (fx::dig($controller_meta, 'hidden')) {
                $meta['class'] .= ' fx_infoblock_hidden';
            }
            $meta['data-fx_controller_meta'] = $controller_meta;
        }
        
        if ($infoblock->get_prop_inherited('controller') == 'layout') {
            $meta['class'] .= ' fx_unselectable';
            $html_result = preg_replace_callback(
                '~<body[^>]*?>~is', 
                function($matches) use ($meta) {
                    $body_tag = fx_template_html_token::create_standalone($matches[0]);
                    $body_tag->add_meta($meta);
                    return $body_tag->serialize();
                }, 
                $html_result
            );
        } elseif ($is_subroot) {
            $html_result = preg_replace_callback(
                "~^(\s*?)(<[^>]+?>)~", 
                function($matches) use ($meta) {
                    $tag = fx_template_html_token::create_standalone($matches[2]);
                    $tag->add_meta($meta);
                    return $matches[1].$tag->serialize();
                }, 
                $html_result
            );
        } else {
            $html_proc = new fx_template_html($html_result);
            $html_result = $html_proc->add_meta($meta, true);
        }
        return $html_result;
    }
    
    protected function _get_infoblock_scope_name(fx_infoblock $infoblock) {
        $ib_controller = $infoblock->get_prop_inherited('controller');
        $ib_action = $infoblock->get_prop_inherited('action');
        if ($ib_controller == 'layout' && $ib_action == 'show') {
            return 'design';
        }
        $ib_page_id = $infoblock['page_id'];
        $scope = $infoblock->get_prop_inherited('scope');
        if (!$scope) {
            if ($ib_page_id == 0) {
                return 'design';
            }
            return 'edit';
        }
        
        if ($ib_page_id == 0 && !$scope['page_type']) {
            return 'design';
        }
        if ($scope['pages'] != 'descendants') {
            return 'edit';
        }
        if (fx::data('content_page', $ib_page_id)->get('url') == '/' && !$scope['page_type']) {
            return 'design';
        }
        return 'edit';
    }
}
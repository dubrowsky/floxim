<?php

class fx_controller_infoblock extends fx_controller {

    /***************************************************
     * TODO пропертя, в которую будут складываться параметры, с которыми должен быть вызван контроллер, чтобы он ничего не знал о том кто и где его вызывает
     ****************************************************/
    private $controller_data = array();
    
    /*
     * @return fx_infoblock
     */
    protected function _get_infoblock() {
        if ( ($ib = $this->param('infoblock'))) {
            return $ib;
        }
        if (($infoblock_id = $this->param('infoblock_id'))) {
            return fx::data('infoblock', $infoblock_id);
        }
        return null;
    }


    public function render() {

        $infoblock = $this->_get_infoblock();
        
        if (!$infoblock) {
            dev_log('no ib to rnd', $this);
            die("IB NOT FOUND");
        }
        
        $params = $infoblock->get_prop_inherited('params');
        
        if (!is_array($params)) {
            $params = array();
        }
        if ( ($override_params = $this->param('override_params'))) {
            $params = array_merge($params, $override_params);
        }
        
        $params['ajax_mode'] = $this->param('ajax_mode');
        
        if (!isset($params['infoblock_id'])) {
            $params['infoblock_id'] = $infoblock['id'];
        }

        $controller = fx::controller(
            $infoblock->get_prop_inherited('controller'), 
            $params, 
            $infoblock->get_prop_inherited('action')
        );
        
        $result = $controller->process();
        $controller_meta = fx::dig($result, '_meta');
        if (fx::dig($controller_meta, 'disabled') && !fx::is_admin()) {
            return;
        }
        $tpl_params = array();
        $tpl = null;
        
        if ( ($tpl_name = $infoblock->get_prop_inherited('visual.template'))) {
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
        
        if (fx::env('is_admin')) {
            if (!preg_match("~[^\s+]~", $output)) {
                //dev_log('ib empty', htmlspecialchars($output), strip_tags($output));
                $output .= '<span class="fx_empty_infoblock">[empty: '.self::_get_infoblock_sign($infoblock).']</span>';
            }
        }
        
        $wrapper = $infoblock->get_prop_inherited('visual.wrapper');
        
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
        }
        
        if (fx::env('is_admin')) {
            $output = $this->_add_infoblock_meta($output, $infoblock, $controller_meta);
        }
        $output = $controller->postprocess($output);
        return $output;
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
    
    protected function _add_infoblock_meta($html_result, $infoblock, $controller_meta = null) {
        $ib_info = array('id' => $infoblock['id']);
        if (($vis = $infoblock->get_visual()) && $vis['id']) {
            $ib_info['visual_id'] = $vis['id'];
        }
        
        $meta = array(
            'data-fx_infoblock' => htmlentities(json_encode($ib_info)),
            'class' => 'fx_infoblock'
        );
        
        // определяем scope/режим правки инфоблока
        $meta['class'] .= ' fx_infoblock_'.$this->_get_infoblock_scope_name($infoblock);
        
        
        if ($controller_meta) {
            if (fx::dig($controller_meta, 'disabled')) {
                $meta['class'] .= ' fx_infoblock_disabled';
            }
            $meta['data-fx_controller_meta'] = htmlentities(json_encode($controller_meta));
        }
        
        if ($infoblock->get_prop_inherited('controller') == 'layout') {
            $meta['class'] .= ' fx_unselectable';
            $html_result = preg_replace_callback(
                '~<body[^>]*?>~is', 
                function($matches) use ($meta) {
                    $body_tag = fx_html_token::create_standalone($matches[0]);
                    $body_tag->add_meta($meta);
                    return $body_tag->serialize();
                }, 
                $html_result
            );
        } else {
            //dev_log('adding meta', htmlspecialchars($html_result));
            $subroot_found = false;
            $html_result = preg_replace_callback(
                "~^(\s*?)(<[^>]+fx:is_sub_root[^>]+>)~", 
                function($matches) use (&$subroot_found, $meta) {
                    $subroot_found = true;
                    $tag = fx_html_token::create_standalone($matches[2]);
                    $tag->add_meta($meta);
                    $tag->remove_attribute('fx:is_sub_root');
                    return $matches[1].$tag->serialize();
                }, 
                $html_result
            );
            if (!$subroot_found) {
                $html_proc = new fx_template_html($html_result);
                $html_result = $html_proc->add_meta($meta, true);
            }
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
        if (fx::data('content_page', $ib_page_id)->get('url') == '/') {
            return 'design';
        }
        return 'edit';
    }
}
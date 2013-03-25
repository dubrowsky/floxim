<?php

class fx_controller_infoblock extends fx_controller {
    
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
        if (fx::dig($controller_meta, 'disabled') && !fx::env('is_admin')) {
            return;
        }
        $tpl_params = array();
        $tpl = null;
        $tpl_action = null;
        
        if ( ($tpl_name = $infoblock->get_prop_inherited('visual.template_name')) && $tpl_name != 'auto' ) {
            $tpl = fx::template($tpl_name);
        }
        $tpl_action = $infoblock->get_prop_inherited('visual.template_variant');
        if ( ! $tpl_action || $tpl_action == 'auto' ) {
            $tpl_action = $infoblock->get_prop_inherited('action');
        }
        
        $tpl_params = $infoblock->get_prop_inherited('visual.template_visual');
        if (!$tpl) {
            $tpl = $controller->find_template();
        }
        if (!$tpl_action) {
            $tpl_action = $controller->find_template_variant();
        }
        $tpl_params['input'] = $result;
        $tpl_params['infoblock'] = $infoblock;
        $output = $tpl->render($tpl_action, $tpl_params);
        if (fx::env('is_admin')) {
            if (!preg_match("~[^\s+]~", strip_tags($output))) {
                $output .= '<span class="fx_empty_infoblock">[empty: '.self::_get_infoblock_sign($infoblock).']</span>';
            }
        }
        
        $wrapper_name = $infoblock->get_prop_inherited('visual.wrapper_name');
        $wrapper_variant = $infoblock->get_prop_inherited('visual.wrapper_variant');
        
        if ($wrapper_name && $wrapper_variant) {
            $tpl_wrap = fx::template($wrapper_name);
            $wrap_params = $infoblock->get_prop_inherited('visual.wrapper_visual');
            if (is_array($wrap_params)) {
                foreach ( $wrap_params as $wrap_param_key => $wrap_param_val) {
                    $tpl_wrap->set_var($wrap_param_key, $wrap_param_val);
                }
            }
            $tpl_wrap->set_var('content', $output);
            $tpl_wrap->set_var('infoblock', $infoblock);
            $output = $tpl_wrap->render($wrapper_variant);
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
                ($infoblock['name'] ? " &laquo;".$infoblock['name'].'&raquo;' : '').' '.
                $infoblock['controller'].'.'.
                $infoblock['action'];
    }
    
    protected function _add_infoblock_meta($html_result, $infoblock, $controller_meta = null) {
        $ib_info = array('id' => $infoblock['id']);
        if (($vis = $infoblock->get_infoblock2layout()) && $vis['id']) {
            $ib_info['visual_id'] = $vis['id'];
        }
        
        $meta = array(
            'data-fx_infoblock' => htmlentities(json_encode($ib_info)),
            'class' => 'fx_infoblock'
        );
        if ($controller_meta) {
            if (fx::dig($controller_meta, 'disabled')) {
                $meta['class'] .= ' fx_infoblock_disabled';
            }
            $meta['data-fx_controller_meta'] = htmlentities(json_encode($controller_meta));
        }
        
        if ($infoblock['controller'] == 'layout') {
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
            $html_proc = new fx_template_html($html_result);
            $html_result = $html_proc->add_meta($meta);
        }
        return $html_result;
        
        
        $wrapper_div = '<div class="fx_infoblock" data-fx_infoblock="'.$ib_info.'"';
        if ($controller_meta) {
            $controller_meta = htmlentities(json_encode($controller_meta));
            $wrapper_div .= ' data-fx_controller_meta="'.$controller_meta.'"';
        }
        $wrapper_div .='>';
        
        if ($infoblock['controller'] == 'layout') {
            // для лейаутов - добавляем мета-данные сразу после body
            $html_result = preg_replace("~<body[^>]*?>~is", '$0'.$wrapper_div, $html_result);
            $html_result = str_replace('~</body>~', '</div></body>', $html_result);
        } else {
            // для прочих - оборачиваем в div
            $html_result = 
                $wrapper_div.
                    $html_result.
                '</div>';
        }
        return $html_result;
    }
}
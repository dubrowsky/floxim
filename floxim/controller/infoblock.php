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
            if (preg_match("~^fake~", $infoblock_id)) {
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
            fx::debug('no ib to render', $this, debug_backtrace());
            die("IB NOT FOUND");
        }
        
        $params = $infoblock->get_prop_inherited('params');
        
        if (!is_array($params)) {
            $params = array();
        }
        // override_infoblock - wide settings InfoBlock, preview settings
        // override_params - only parameters of the controller
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
        
        
        if (preg_match('~^fake~', $infoblock['id'])) {
            $params['is_fake'] = true;
            if ($ib_overs) {
                list(
                        $infoblock['controller'], 
                        $infoblock['action']
                    ) = explode(".", $ib_overs['controller']);

                $infoblock['scope'] = $ib_overs['scope'];
                $infoblock['page_id'] = $ib_overs['page_id'];
            }
        }
        
        $controller_name = $infoblock->get_prop_inherited('controller');
        $controller_action = $infoblock->get_prop_inherited('action');
        
        if ($controller_name && $controller_action) {
            $controller = fx::controller(
                $controller_name, 
                $params, 
                $controller_action
            );


            $result = $controller->process();

            if (is_string($result)) {
                if (fx::is_admin()) {
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
            // get the template to preview
            if (isset($ib_overs['visual']['template'])) {
                $tpl = fx::template($ib_overs['visual']['template']);
            } elseif ( ($tpl_name = $infoblock->get_prop_inherited('visual.template'))) {
                $tpl = fx::template($tpl_name);
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
                if ($tpl_wrap->has_action()) {
                    $wrap_params = $infoblock->get_prop_inherited('visual.wrapper_visual');
                    if (isset($params['infoblock_area_position'])) {
                        $wrap_params['infoblock_area_position'] = $params['infoblock_area_position'];
                    }
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
            }
        } else {
            $output = '';
            $controller_meta = array(
                'hidden_placeholder' => fx::alang('Fake infoblock data', 'system')
            );
        }
        if (fx::is_admin()) {
            $output = $this->_add_infoblock_meta($output, $infoblock, $controller_meta, $is_subroot);
        }
        if ($controller) {
            $output = $controller->postprocess($output);
        }
        return $output;
    }
    
    /**
     * "Signature" of the InfoBlock for debugging
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
        
        if (preg_match("~^fake~", $infoblock['id'])) {
            $meta['class'] .= ' fx_infoblock_fake';
        }
         
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
        } elseif ($is_subroot && preg_match("~^(\s*?)(<[^>]+?>)~", $html_result)) {
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
}
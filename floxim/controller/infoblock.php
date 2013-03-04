<?php

class fx_controller_infoblock extends fx_controller {
    
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
        $params = $infoblock['params'];
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
        $controller = fx::controller($infoblock['controller'], $params, $infoblock['action']);
        $result = $controller->process();
        $controller_meta = fx::dig($result, '_meta');
        $tpl_params = array();
        $tpl = null;
        $tpl_action = null;
        if ( ($vis = $infoblock->get_infoblock2layout())) {
            if ($vis['template_name']) {
                $tpl = fx::template($vis['template_name']);
            }
            $tpl_action = $vis['template_variant'] ? $vis['template_variant'] : $infoblock['action'];
            if ($vis['template_visual']) {
                $tpl_params = $vis['template_visual'];
            }
        }
        if (!$tpl) {
            $tpl = $controller->find_template();
        }
        if (!$tpl_action) {
            $tpl_action = $controller->find_template_variant();
        }
        $tpl_params['input'] = $result;
        $tpl_params['infoblock'] = $infoblock;
        $output = $tpl->render($tpl_action, $tpl_params);
        if (fx::env()->is_admin()) {
            dev_log('cleared output', strip_tags($output));
            if (!preg_match("~[^\s+]~", strip_tags($output))) {
                $output = '<span class="fx_empty_infoblock">[empty: '.self::_get_infoblock_sign($infoblock).']</span>';
            }
        }
        if ($vis && $vis['wrapper_name'] && $vis['wrapper_variant']) {
            $tpl_wrap = fx::template($vis['wrapper_name']);
            $wrap_params = $vis['wrapper_visual'] ? $vis['wrapper_visual'] : array();
            foreach ( $wrap_params as $wrap_param_key => $wrap_param_val) {
                $tpl_wrap->set_var($wrap_param_key, $wrap_param_val);
            }
            $tpl_wrap->set_var('content', $output);
            $tpl_wrap->set_var('infoblock', $infoblock);
            $output = $tpl_wrap->render($vis['wrapper_variant']);
        }
        if (fx::env()->is_admin()) {
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
        return "infoblock[".$infoblock["id"].'] '.
                $infoblock['controller'].'.'.
                $infoblock['action'];
    }
    
    protected function _add_infoblock_meta($html_result, $infoblock, $controller_meta = null) {
        $ib_info = array('id' => $infoblock['id']);
        if (($vis = $infoblock->get_infoblock2layout()) && $vis['id']) {
            $ib_info['visual_id'] = $vis['id'];
        }
        
        $ib_info = htmlentities(json_encode($ib_info));
        
        
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
    /**
     
    public function index($input) {
        $fx_core = fx_core::get_object();
        $result = array();

        if ($input['id']) {
            $id = $input['id'];
            $infoblock = $fx_core->infoblock->get_by_id($id);
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
     * 
     */
}
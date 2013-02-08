<?php

class fx_controller_infoblock extends fx_controller {

    /**
     * @todo Проверять права!
     */
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
}
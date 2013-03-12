<?php

/**
 * Description of router_admin
 *
 * @author Nikita Dezzpil Orlov <n.dezz.orlov@gmail.com>
 */
class fx_router_admin extends fx_router {
    
    public function route($url = null, $context = null) {

        if (!preg_match("~(\/floxim)+~", $url, $ib_info)) 
        {
            return null;
        }
        
        if (empty($_REQUEST)) 
        {
            // параметров запроса нет, идем стандартной 
            // для всех контроллеров дорогой
            return new fx_controller_admin();
        }
        
        // ниже - дебри старой админки ...
        
        $fx_core = fx_core::get_object();
        $fx_core->modules->load_env();

        if (!isset($essence)) {
            $essence = $fx_core->input->fetch_get_post('essence');
        }

        if (!isset($action)) {
            $action = $fx_core->input->fetch_get_post('action');
        }

        $fx_subdivision = $fx_core->input->fetch_get_post('fx_subdivision');
        $infoblock = $fx_core->input->fetch_get_post('infoblock');
        $fx_infoblock = $fx_core->input->fetch_get_post('fx_infoblock');
        $posting = $fx_core->input->fetch_get_post('posting');

        if ($posting && $posting !== 'false') {
            $action .= "_save";
        }

        if ($fx_core->input->fetch_get_post('fx_admin')) {
            $fx_core->set_admin_mode();
        }

        $input = $fx_core->input->make_input();

        if (!$essence && !$fx_subdivision) {

            $fx_core->url->parse_url();

            // admin
            if ($fx_core->url->get_parsed_url('path') == fx::config()->HTTP_ROOT_PATH) {
                $essence = 'layout';
                $action = 'admin';
            } else { // обычный вывод страницы
                if (!$current_site) {
                    die("В системе нет ни одного сайта");
                }

                $route = new fx_route($fx_core->url->get_parsed_url('path'));

                $route->attempt_to_redirect();
                $result = $route->resolve();

                $current_sub = $result['sub_env'];

                $fx_core->env->set_ibs($result['ibs_env']);

                if ($result['content_id']) {
                    $fx_core->env->set_content($result['content_id']);
                }

                $fx_core->env->set_action($result['action']);

                if ($result['page']) {
                    $fx_core->env->set_page($result['page']);
                }

            }
        } else if ($fx_subdivision) {
            $current_sub = fx::data('subdivision')->get_by_id($fx_subdivision);
            if ($infoblock) {
                $infoblocks = fx::data('infoblock')->get_by_id($infoblock);
                $infoblocks = array($infoblocks);
            } else {
                $infoblocks = fx::data('infoblock')->get_all('subdivision_id', $current_sub->get_id());
            }

            $fx_core->env->set_ibs($infoblocks);
        } else if ($fx_infoblock) {
            $infoblock = fx::data('infoblock')->get_by_id($fx_infoblock);
            if ($infoblock) {
                $current_sub = fx::data('subdivision')->get_by_id($infoblock->get('subdivision_id'));
            }
        }

        if (!$essence) {
            $essence = 'layout';
        }

        if (!$action) {
            $action = 'index';
        }

        if ($current_sub['id']) {
            $fx_core->env->set_sub($current_sub);
        }

        if ($input['fx_admin']) {
            $essence = 'admin_' . $essence;
        }

        try {
            $classname = 'fx_controller_' . $essence;
            $controller = new $classname;
        } catch (Exception $e) {
            die("Error! Essence: " . htmlspecialchars($essence));
        }

        // дебри старой админки вынудили меня поставить костыль (ниже)...
        
        if ($controller instanceof fx_controller_admin)
        {
            // главный контроллер вызывает 
            // post_postprocess самостоятельно
            echo $controller->process($input, $action);
            die();
        }
        else
        {
            // мутные контроллеры не умеют вызывать
            // post_postprocess самостоятельно
            ob_start("$this->fx_buffer");
            $controller->process($input, $action);
            $str = ob_get_clean();
            echo $str;
            die();

            function fx_buffer($str) {
                $fx_core = fx_core::get_object();
                $str = $fx_core->page->post_proccess($str);
                return $str;
            }
        }
    }

}
?>

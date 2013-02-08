<?php
// выношу инициализацию сервисов в boot.php (бывший vars.inc.php)

if (!defined('FLOXIM')) {
    require_once('../boot.php');
}

fx_content_user::attempt_to_authorize();

fx::router()->register_system();
if ($controller = fx::router()->route()) {
    //dev_log($controller);
    $result = $controller->process();
    $template = $controller->find_template();
    $template_var = $controller->find_template_variant();
    $output = $template->render($template_var, array('input' => $result));
    $output = fx::page()->post_proccess($output);
    echo $output;
    die();
}


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
        $essence = 'page';
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
    $essence = 'page';
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

ob_start("fx_buffer");
$controller->process($input, $action);
ob_end_flush();

function fx_buffer($str) {
    $fx_core = fx_core::get_object();
    $str = $fx_core->page->post_proccess($str);

    return $str;
}
?>
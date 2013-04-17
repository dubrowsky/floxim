<?php
// выношу инициализацию сервисов в boot.php (бывший vars.inc.php)

if (!defined('FLOXIM')) {
    require_once('../boot.php');
}

fx_content_user::attempt_to_authorize();
ob_start();
if ( ($controller = fx::router()->route() ) ) {
    $result = $controller->process();
    if (!is_string($result)) {
        $template = $controller->find_template();
        $result = $template->render(array('input' => $result));
        $result = fx::page()->post_proccess($result);
    }

    echo $result;
    echo ob_get_clean();
    die();
}
?>
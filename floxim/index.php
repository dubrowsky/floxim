<?php
// выношу инициализацию сервисов в boot.php (бывший vars.inc.php)

if (!defined('FLOXIM')) {
    require_once('../boot.php');
}

register_shutdown_function(function() {
    if (!fx::env()->get('complete_ok')) {
    	$ob_level = ob_get_level();
        $res = '';
        for ($i = 0; $i < $ob_level; $i++) {
            $res .= ob_get_clean();
        }
        fx::log('down', $res, debug_backtrace(), $_SERVER, $_POST); 
    }
});

if ( ($controller = fx::router()->route() ) ) {
    echo $controller->process();
    fx::env()->set('complete_ok', true);
}
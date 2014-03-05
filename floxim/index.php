<?php
// выношу инициализацию сервисов в boot.php (бывший vars.inc.php)

if (!defined('FLOXIM')) {
    require_once('../boot.php');
}
fx::profiler()->block('page');
register_shutdown_function(function() {
    if (!fx::env()->get('complete_ok')) {
    	$ob_level = ob_get_level();
        $res = '';
        for ($i = 0; $i < $ob_level; $i++) {
            $res .= ob_get_clean();
        }
        fx::log('down', $res, debug_backtrace(), $_SERVER, $_POST); 
    }
    fx::profiler()->stop();
    fx::log('profiled', fx::profiler()->show());
});

fx::profiler()->block('routing');
$controller = fx::router()->route();
fx::profiler()->then('processing');

if ( $controller ) {
    echo $controller->process();
    fx::env()->set('complete_ok', true);
}
fx::profiler()->stop();
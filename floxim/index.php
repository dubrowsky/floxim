<?php
// if request directs right to /floxim/index.php 
// e.g. admin interface
require_once (dirname(__FILE__).'/../boot.php');

fx::profiler()->block('page');
register_shutdown_function(function() {
    if (!fx::env()->get('complete_ok')) {
    	$ob_level = ob_get_level();
        $res = '';
        for ($i = 0; $i < $ob_level; $i++) {
            $res .= ob_get_clean();
        }
        echo $res;
        fx::log('down', $res, debug_backtrace(), $_SERVER, $_POST); 
    }
    fx::profiler()->stop();
    //fx::log('profiled', fx::profiler()->show());
});

fx::profiler()->block('routing');
$controller = fx::router()->route();
fx::profiler()->then('processing');

if ( $controller ) {
    echo $controller->process();
    fx::env()->set('complete_ok', true);
}
fx::profiler()->stop();
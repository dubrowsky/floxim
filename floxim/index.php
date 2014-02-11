<?php
// выношу инициализацию сервисов в boot.php (бывший vars.inc.php)

if (!defined('FLOXIM')) {
    require_once('../boot.php');
}

register_shutdown_function(function() {
    $ob_level = ob_get_level();
    if ($ob_level > 0) {
        $res = '';
        for ($i = 0; $i < $ob_level; $i++) {
            $res .= ob_get_clean();
        }
        fx::log('down', $res, debug_backtrace()); 
    }
});

if ( ($controller = fx::router()->route() ) ) {
    echo $controller->process();
    die();
}
?>
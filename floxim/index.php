<?php
// выношу инициализацию сервисов в boot.php (бывший vars.inc.php)

if (!defined('FLOXIM')) {
    require_once('../boot.php');
}
ob_start();
if ( ($controller = fx::router()->route() ) ) {
    echo $controller->process();
    die();
}
?>
<?php
if ( $_SERVER['HTTP_HOST'] || $_SERVER['REQUEST_METHOD'] || !isset($_SERVER['argv'])  ) {
    die("Скрипт нельзя загружать по http");
}

set_time_limit(0);
define("FLOXIM_CRONTAB", true);

require_once ("../vars.inc.php");
$essence = 'page';
$action = 'blank';
require fx::config()->ROOT_FOLDER."index.php";

fx_controller_admin_crontask::run();

?>

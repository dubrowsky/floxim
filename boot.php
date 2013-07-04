<?php
define("FLOXIM", 1);
require_once '_devlog/log.php';
require_once 'floxim/system/config.php';
require_once 'floxim/system/fx.php';

fx::config()->load(include_once(dirname(__FILE__) . '/config.php'));

include_once fx::config()->ROOT_FOLDER . 'system.php';
require_once fx::config()->SYSTEM_FOLDER . 'core.php';

session_start();
fx::core();

fx::env('site', fx::data('site')->get_by_host_name($_SERVER['HTTP_HOST'], 1));
fx_content_user::attempt_to_authorize();
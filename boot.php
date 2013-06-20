<?php
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_devlog' . DIRECTORY_SEPARATOR . 'log.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lang.php');

dev_log('start');
 //die();

$db_config = array(
    'ilya_local' =>  array(
        'DB_DSN' => 'mysql:dbname=floxim;host=localhost',
        'DB_USER' => 'root',
        'DB_PASSWORD' => ''
    ),
    'remote' =>  array(
        'DB_DSN' => 'mysql:dbname=floxim;host=81.177.142.25',
        'DB_USER' => 'floxim',
        'DB_PASSWORD' => 'floxim12345'
    ),
    'remote_new' => array(
        'DB_DSN' => 'mysql:dbname=fxm_new;host=81.177.142.25',
        'DB_USER' => 'floxim',
        'DB_PASSWORD' => 'floxim12345'
    )
);

$config = $db_config['ilya_local'];

$SYSTEM_FOLDER = dirname(__FILE__) . (isset($config['HTTP_ROOT_PATH']) ? $config['HTTP_ROOT_PATH'] : '/floxim/') . 'system/';
define("FX_JQUERY_PATH", '/floxim/lib/js/jquery-1.7.1.js');

require_once $SYSTEM_FOLDER.'config.php';
require_once $SYSTEM_FOLDER.'fx.php';

fx::config()->load($config);

define("FLOXIM", 1);

include_once fx::config()->ROOT_FOLDER . 'system.php';
require_once fx::config()->SYSTEM_FOLDER . 'core.php';

session_start();
fx::core();
//$fx_core->db_init();
// load default extensions
//$fx_core->load_default_extensions();

/* Загрузка языка */
$lang = 'en';
fx::lang()->load($lang);

$current_site = fx::data('site')->get_by_host_name($_SERVER['HTTP_HOST'], 1);
fx::env('site', $current_site);
fx_content_user::attempt_to_authorize();
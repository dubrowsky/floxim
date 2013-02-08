<?php

$config =  array(
    'DB_DSN' => 'mysql:dbname=floxim;host=localhost',
    'DB_USER' => 'root',
    'DB_PASSWORD' => ''
);

$SYSTEM_FOLDER = dirname(__FILE__) . (isset($config['HTTP_ROOT_PATH']) ? $config['HTTP_ROOT_PATH'] : '/floxim/') . 'system/';

require_once $SYSTEM_FOLDER.'config.php';
require_once $SYSTEM_FOLDER.'fx.php';

fx::config()->load($config);

define("FLOXIM", 1);

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_devlog' . DIRECTORY_SEPARATOR . 'log.php');

if (!class_exists('fx_config')) {
    $folder = join(strstr($_SERVER['SCRIPT_FILENAME'], "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", $_SERVER['SCRIPT_FILENAME']), 0, -2)) . ( strstr($_SERVER['SCRIPT_FILENAME'], "/") ? "/" : "\\" );
    require_once $folder . 'vars.inc.php';
    unset($folder);
}

include_once fx::config()->ROOT_FOLDER . 'system.php';
require_once fx::config()->SYSTEM_FOLDER . 'core.php';

session_start();
$fx_core = fx_core::get_object();
$fx_core->db_init();
// load default extensions
$fx_core->load_default_extensions();

/* Загрузка языка */
// империческим путем выяснили, что все используют русский язык
$lang = 'ru';
$fx_core->lang->load($lang);

$current_site = fx::data('site')->get_by_host_name($_SERVER['HTTP_HOST'], 1);
fx::env('site', $current_site);
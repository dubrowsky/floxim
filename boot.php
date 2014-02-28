<?php
define("FLOXIM", 1);
define("DOCUMENT_ROOT", dirname(__FILE__)); 
require_once DOCUMENT_ROOT.'/_devlog/log.php';
require_once DOCUMENT_ROOT.'/floxim/system/config.php';
require_once DOCUMENT_ROOT.'/floxim/system/fx.php';

$config_res = @ include_once( DOCUMENT_ROOT. '/config.php');
if (!$config_res) {
    header("Location: /install/");
    die();
}

fx::config()->load($config_res);
fx::load();

fx::env('site', fx::data('site')->get_by_host_name($_SERVER['HTTP_HOST'], 1));
fx_content_user::attempt_to_authorize();
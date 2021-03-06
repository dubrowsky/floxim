<?php
if (!isset($_SERVER['REQUEST_TIME_FLOAT'])) {
    $_SERVER['REQUEST_TIME_FLOAT'] = microtime(true);
}
define("DOCUMENT_ROOT", dirname(__FILE__)); 

if (!($config_res = include_once( DOCUMENT_ROOT. '/config.php'))) {
    header("Location: /install/");
    die();
}

require_once DOCUMENT_ROOT.'/floxim/system/config.php';
require_once DOCUMENT_ROOT.'/floxim/system/fx.php';

fx::config()->load($config_res);
fx::load();

fx::listen('unlink', function($e) {
    if (fx::path()->is_inside($e->file, fx::path('thumbs'))) {
        return;
    }
    $thumbs = fx_thumb::find_thumbs($e->file);
    foreach ($thumbs as $thumb) {
        fx::files()->rm($thumb);
    }
});
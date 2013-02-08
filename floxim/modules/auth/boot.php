<?php

spl_autoload_register('auth_load_class', false, true);

function auth_load_class($classname) {
    $folder = fx::config()->MODULE_FOLDER."auth/";
    $file = false;
    if ($classname == 'fx_auth') $file = "auth";
    if ($classname == 'fx_auth_user_relation') $file = "relation";
    if ($classname == 'fx_auth_user_mail') $file = "mail";
    if ($classname == 'fx_auth_facebook') $file = "external/facebook";
    if ($classname == 'fx_auth_twitter') $file = "external/twitter";
    if ($classname == 'fx_auth_external') $file = "external/external";
    if ($file) {
        require_once $folder.$file.'.php';
    }
}

fx_auth::get_object();
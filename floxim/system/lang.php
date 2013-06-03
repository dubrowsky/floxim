<?php

/* $Id: nc_lang.class.php 4290 2011-02-23 15:32:35Z denis $ */

class fx_system_lang extends fx_system {

    public function load($lang) {
        $fx_core = fx_core::get_object();
        if (!preg_match("/^\w+$/", $lang)) return false;
        require_once fx::config()->ADMIN_FOLDER."lang/".$lang.".php";
    }

    public function detect_lang() {
        return "ru";
    }

}

?>

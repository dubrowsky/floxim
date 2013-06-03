<?php
/* $Id: redirect.php 6965 2012-05-12 15:18:46Z denis $ */
defined("FLOXIM") || die("Unable to load file.");

class fx_redirect extends fx_essence {

    public function validate() {
        $res = true;

        if (!$this['old_url']) {
            $this->validate_errors[] = array('field' => 'old_url', 'text' => 'Укажите старый url');
            $res = false;
        }

        if (!$this['new_url']) {
            $this->validate_errors[] = array('field' => 'new_url', 'text' => 'Укажите новый url');
            $res = false;
        }

        if ($this['old_url'] && $this['old_url'] == $this['new_url']) {
            $this->validate_errors[] = array('field' => 'new_url', 'text' => 'Адреса не могут быть одинаковыми');
            $res = false;
        }

        return $res;
    }

}


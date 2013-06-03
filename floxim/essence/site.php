<?php

/* $Id: site.php 8536 2012-12-17 10:55:01Z myasin $ */
defined("FLOXIM") || die("Unable to load file.");

class fx_site extends fx_essence {

    public function get_title_sub_id() {
        return $this->data['title_sub_id'];
    }

    public function get_404_sub() {
        $id = $this->data['e404_sub_id'];
        return fx::data('subdivision')->get_by_id($id);
    }

    public function validate() {
        $res = true;

        if (!$this['name']) {
            $this->validate_errors[] = array('field' => 'name', 'text' => 'Укажите название сайта');
            $res = false;
        }

        /*if (!$this['domain']) {
            $this->validate_errors[] = array('field' => 'domain', 'text' => 'Укажите домен');
            $res = false;
        }*/

        return $res;
    }
    
    protected function _before_delete() {
        $this->delete_subdivisions();
        $this->delete_infoblocks();
        $this->delete_menu();
    }
    
    protected function delete_subdivisions () {
        $subs = fx::data('subdivision')->get_all('site_id', $this['id']);
        foreach ( $subs as $sub ) {
            $sub->delete();
        }
    }
    
    protected function delete_infoblocks () {
        $infoblocks = fx::data('infoblock')->get_all('site_id', $this['id']);
        foreach ($infoblocks as $infoblock) {
            $infoblock->delete();
        }
    }
    
     protected function delete_menu () {
        $menu = fx::data('menu')->get_all('site_id', $this['id']);
        foreach ($menu as $v) {
            $v->delete();
        }
    }

}

?>

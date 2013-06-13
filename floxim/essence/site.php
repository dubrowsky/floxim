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
            $this->validate_errors[] = array('field' => 'name', 'text' => fx_lang('Укажите название сайта'));
            $res = false;
        }

        /*if (!$this['domain']) {
            $this->validate_errors[] = array('field' => 'domain', 'text' => 'Укажите домен');
            $res = false;
        }*/

        return $res;
    }
    
    protected function _before_delete() {
        $this->delete_infoblocks();
        $this->delete_content();
    }
    
    protected function delete_content() {
        $content = fx::data('content')->where('site_id', $this['id'])->all();
        foreach ( $content as $content_item ) {
            $content_item->delete();
        }
    }
    
    protected function delete_infoblocks () {
        $infoblocks = fx::data('infoblock')->where('site_id', $this['id'])->all();
        foreach ($infoblocks as $infoblock) {
            $infoblock->delete();
        }
    }
}

?>
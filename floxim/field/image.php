<?php

class fx_field_image extends fx_field_file {

    public function get_js_field($content, $tname = 'f_%name%', $layer = '', $tab = '') {
        parent::get_js_field($content, $tname, $layer, $tab);
        $this->_js_field['type'] = 'image';

        return $this->_js_field;
    }

    public function get_edit_jsdata($content) {
        parent::get_edit_jsdata($content);

        $this->_edit_jsdata['type'] = 'image';
        return $this->_edit_jsdata;
    }

    protected function get_field_vars($info) {
        return new fx_field_vars_image($info);
    }

}

?>
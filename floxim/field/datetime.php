<?php

class fx_field_datetime extends fx_field_baze {

    protected $day = '', $month = '', $year = '', $hours = '', $minutes = '', $seconds = '';

    public function content_procces(fx_content $content, fx_infoblock_content $infoblock = null, $hash_obj = null) {
        $fx_core = fx_core::get_object();
        $result = array();
        $name = $this->name;

        $var = new fx_field_vars_datetime($content[$name]);

        $result = array();
        $result['f_'.$name.'_none'] = $var->format();
        $result['f_'.$name] = $var;

        if ($hash_obj) {
            $eit_in_place_info = $this->get_edit_jsdata($content);
            $hash = $fx_core->page->add_edit_field('f_'.$name, $eit_in_place_info, null, $hash_obj);

            $var->set_to_str_value('<'.$this->_wrap_tag.' class="'.$hash.'">'.$var->format().'</'.$this->_wrap_tag.'>');

            $result['f_'.$name.'_hash'] = $hash;
        } else {
            $result['f_'.$name.'_hash'] = '';
        }

        return $result;
    }

   /* public function get_edit_jsdata($content) {
        parent::get_edit_jsdata($content);
        $this->_edit_jsdata['type'] = 'date';
        $this->_edit_jsdata['datetime'] = date('d.m.Y', $content['f_'.$this->name]);
        return $this->_edit_jsdata;
    }*/

    public function get_js_field($content, $tname = 'f_%name%', $layer = '', $tab = '') {
        parent::get_js_field($content, $tname, $layer, $tab);

        $this->load_values_by_str($content[$this->name]);
        $this->_js_field['day'] = $this->day;
        $this->_js_field['month'] = $this->month;
        $this->_js_field['year'] = $this->year;
        $this->_js_field['hours'] = $this->hours;
        $this->_js_field['minutes'] = $this->minutes;
        $this->_js_field['seconds'] = $this->seconds;

        return $this->_js_field;
    }

    public function set_value($value) {
        if (is_array($value)) {
            $this->day = $value['day'];
            $this->month = $value['month'];
            $this->year = $value['year'];
            $this->hours = $value['hours'];
            $this->minutes = $value['minutes'];
            $this->seconds = $value['seconds'];

            $this->value = $this->year.'-'.$this->month.'-'.$this->day.' ';
            $this->value .= $this->hours.':'.$this->minutes.':'.$this->seconds;
        } else if ($value) {
            $this->value = $value;
            $this->load_values_by_str($this->value);
        } else {
            $this->value = '';
        }
    }

    public function get_sql_type() {
        return "DATETIME";
    }

    protected function load_values_by_str($str) {
        if ($str) {
            $timestamp = strtotime($str);
            $this->day = date('d', $timestamp);
            $this->month = date('m', $timestamp);
            $this->year = date('Y', $timestamp);
            $this->hours = date('H', $timestamp);
            $this->minutes = date('i', $timestamp);
            $this->seconds = date('s', $timestamp);
        }
    }

}

?>

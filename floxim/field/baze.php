<?php

/**
 * Базовый класс для различных типов полей 
 */
class fx_field_baze extends fx_field {

    protected $value, $error, $is_error = false;
    protected $_edit_jsdata;
    protected $_js_field = array();
    protected $_wrap_tag = 'span';

    public function content_procces(fx_content $content, fx_infoblock_content $infoblock = null, $hash_obj = null) {
        $fx_core = fx_core::get_object();
        $name = $this->name;
        
        $result = array();
        $result['f_'.$name.'_none'] = $content[$name];
        
        if ( $hash_obj ) {
            $eit_in_place_info = $this->get_edit_jsdata($content);
            $hash = $fx_core->page->add_edit_field('f_'.$name, $eit_in_place_info, null, $hash_obj);
                    
            $result['f_'.$name] = '<'.$this->_wrap_tag.' class="'.$hash.'">'.$content[$name].'</'.$this->_wrap_tag.'>';
            $result['f_'.$name.'_hash'] = $hash; 
        }
        else {
            $result['f_'.$name] = $content[$name];
            $result['f_'.$name.'_hash'] = '';
        }

        return $result;
    }

    public function get_edit_jsdata($content) {
        $data = $this->get_js_field($content);
        unset($data['label'], $data['id'], $data['parent'], $data['name']);
        return $data;
        return $this->_edit_jsdata;
    }

    public function get_js_field($content, $tname = 'f_%name%', $layer = '', $tab = '') {

        $name = $tname ? str_replace('%name%', $this->name, $tname) : $this->name;
        $this->_js_field = array('id' => $name, 'name' => $name, 'label' => $this->description, 'type' => $this->get_type());
        $this->_js_field['value'] = $this['default'];
        if ($content[$this->name]) {
            $this->_js_field['value'] = $content[$this->name];
        }
        /* else if ( $this['default']) {
          $this->_js_field['value'] = $this['default'];
          } */

        if ($this['parent']) {
            $this->_js_field['parent'] = array('visual['.$this['parent'][0].']', $this['parent'][1]);
        }

        if ($layer) $this->_js_field['layer'] = $layer;
        if ($tab) $this->_js_field['tab'] = $tab;

        return $this->_js_field;
    }

    public function get_input($opt = '') {
        return "<input class='".$this->get_css_class()."' ".$opt." type='text' name='f_".$this->name."' value='".htmlspecialchars($this->value, ENT_QUOTES)."' />";
    }

    public function get_html($opt = '') {
        $asterisk = $this['not_null'] ? '<span class="fx_field_asterisk">*</span>' : '';
        return '<div class="'.$this->get_wrap_css_class().'"><label>'.$this->get_description().$asterisk.'</label>:'.$this->get_input($opt).'</div>';
    }

    protected function get_css_class() {
        return "fx_form_field fx_form_field_".fx_field::get_type_by_id($this->type_id).($this->is_error ? " fx_form_field_error" : "");
    }

    protected function get_wrap_css_class() {
        return "fx_form_wrap fx_form_wrap_".fx_field::get_type_by_id($this->type_id);
    }

    public function set_value($value) {
        $this->value = $value;
    }

    public function validate_value($value) {
        if (!is_array($value)) $value = trim($value);
        if ($this['not_null'] && !$value) {
            $this->error = sprintf(FX_FRONT_FIELD_FILED, $this->description);
            return false;
        }
        return true;
    }

    public function get_savestring() {
        return $this->value;
    }

    public function post_save($content) {
        return false;
    }

    public function get_error() {
        return $this->error;
    }

    public function set_error() {
        $this->is_error = true;
    }
    
    public function get_export_value ( $value, $dir = '' ) {
        return $value;
    }
    
    public function get_import_value ( $content, $value, $dir = '' ) {
        return $value;
    }
    
    public function get_search_cond ( $cond ) {
        return false;
    }

}

?>

<?php

class fx_template_field  {
    protected $_value = null;
    
    protected $_meta = array();
    
    public function __construct($value = null, $meta = array()) {
        $this->_value = $value;
        $this->_meta = array_merge($this->_meta, $meta);
    }
    
    public function get_value(){
        return $this->_value;
    }
    
    public function set_meta($key, $value) {
        $this->_meta[$key] = $value;
    }
    
    public function get_meta($key) {
        return isset($this->_meta[$key])? $this->_meta[$key] : null;
    }
    
    public function __toString() {
        $val = $this->get_value();
        if (!$this->get_meta('editable')) {
            return $val;
        }
        $id = $this->get_meta('id');
        return "###fx_template_field|".$id."|".json_encode($this->_meta)."###".$val."###fx_template_field_end###";
    }
    
    /**
     * Постпроцессинг полей
     * @param string $html
     */
    public static function replace_fields($html) {
        $field_regexp = "~###fx_template_field\|([^\|]*?)\|(.+?)###(.+?)###fx_template_field_end###~s";
        // заменяем подстановки в атрибутах
        $html = preg_replace_callback(
            "~<[^>]+###fx_template_field[^>]+?>~", 
            function($tag_matches) use ($field_regexp) {
                $att_fields = array();
                $tag = preg_replace_callback(
                    $field_regexp, 
                    function($field_matches) use (&$att_fields) {
                        $att_fields[$field_matches[1]] = array(
                            'meta' => $field_matches[2], 
                            'value' => $field_matches[3]
                        );
                        return $field_matches[3];
                    }, 
                    $tag_matches[0]
                );
                $fx_atts = '';
                foreach ($att_fields as $afk => $af) {
                    $af_meta = json_decode($af['meta']);
                    $af_meta->value = $af['value'];
                    $af['meta'] = json_encode($af_meta);
                    $fx_atts .= 'data-fx_template_var_'.$afk.'="'.htmlentities($af['meta']).'" ';
                }
                $tag = preg_replace("~^<[^\s>]+~", '$0 '.$fx_atts, $tag);
                if (preg_match("~class\s*=[\s\'\"]*[^\'\"\>]+~i", $tag, $class_att)) {
                    $class_att_new = preg_replace("~class\s*=[\s\'\"]*~", '$0fx_template_var_in_att ', $class_att[0]);
                    $tag = str_replace($class_att, $class_att_new, $tag);
                } else {
                    $tag = preg_replace("~^<[^\s>]+~", '$0 class="fx_template_var_in_att"', $tag);
                }
                return $tag;
            }, 
            $html
        );
        // остались только в тексте
        $html = preg_replace_callback($field_regexp, function($field_matches) {
            $field_content = $field_matches[3];
            $tag = preg_match("~<(?:div|ul|li|table|br)~i", $field_content) ? 'div' : 'span';
            return '<'.$tag.' class="fx_template_var" data-fx_var="'.htmlentities($field_matches[2]).'">'.
                    $field_matches[3].'</'.$tag.'>';
        }, $html);
        return $html;
    }
}
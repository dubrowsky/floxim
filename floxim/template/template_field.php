<?php

class fx_template_field  {
    protected $_value = null;
    
    protected $_meta = array();
    
    public function __construct($value = null, $meta = array()) {
        $this->_value = $value;
        $this->_meta = array_merge($this->_meta, $meta);
    }
    
    public function getValue(){
        return $this->_value;
    }
    
    public function setMeta($key, $value) {
        $this->_meta[$key] = $value;
    }
    
    public function getMeta($key) {
        return isset($this->_meta[$key])? $this->_meta[$key] : null;
    }
    
    public function __toString() {
        $val = $this->getValue();
        if (!$this->getMeta('editable')) {
            return $val;
        }
        $id = $this->getMeta('id');
        return "###fx_template_field|".$id."|".json_encode($this->_meta)."###".$val."###fx_template_field_end###";
    }
    
    /**
     * Постпроцессинг полей
     * @param string $html
     */
    public static function replace_fields($html) {
        $field_regexp = "~###fx_template_field\|([^\|]*?)\|(.+?)###(.+?)###fx_template_field_end###~";
        // заменяем подстановки в атрибутах
        $html = preg_replace_callback(
            "~<[^>]+###fx_template_field[^>]+?>~", 
            function($tag_matches) use ($field_regexp) {
                $att_fields = array();
                $tag = preg_replace_callback(
                    $field_regexp, 
                    function($field_matches) use (&$att_fields) {
                        //echo "<pre>" . htmlspecialchars(print_r($field_matches, 1)) . "</pre>";
                        //echo "<pre>" . htmlspecialchars(print_r($matches, 1)) . "</pre>";
                        $att_fields[$field_matches[1]] = array(
                            'meta' => $field_matches[2], 
                            'value' => $field_matches[3]
                        );
                        return $field_matches[3];
                    }, 
                    $tag_matches[0]
                );
                //echo "<pre>" . htmlspecialchars(print_r($att_fields, 1)) . "</pre><hr/>";
                $fx_atts = '';
                foreach ($att_fields as $afk => $af) {
                    $fx_atts .= 'data-fx_template_var_'.$afk.'="'.htmlentities($af['meta']).'" ';
                }
                $tag = preg_replace("~^<[^\s>]~", '$0 '.$fx_atts, $tag);
                return $tag;
            }, 
            $html
        );
        // остались только в тексте
        $html = preg_replace_callback($field_regexp, function($field_matches) {
            return '<span class="fx_template_var" data-fx_var="'.htmlentities($field_matches[2]).'">'.$field_matches[3].'</span>';
        }, $html);
        return $html;
    }
}
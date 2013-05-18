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
        if (!$this->get_meta('editable') || !fx::env('is_admin')) {
            return $val;
        }
        $id = $this->get_meta('id');
        return "###fx_template_field|".$id."|".json_encode($this->_meta)."###".$val."###fx_template_field_end###";
    }
    
    protected static $_field_regexp = "###fx_template_field\|([^\|]*?)\|(.+?)###(.*?)###fx_template_field_end###";
    
    /**
     * Постпроцессинг полей
     * @param string $html
     */
    public static function replace_fields($html) {
        $html = self::_replace_fields_in_atts($html);
        $html = self::_replace_fields_wrapped_by_tag($html);
        $html = self::_replace_fields_in_text($html);
        return $html;
    }
    
    /**
     * Замена полей в атрибутах
     * @param string $html
     */
    protected static function _replace_fields_in_atts($html) {
        // Регулярка для атрибутов
        $field_regexp = "~".self::$_field_regexp."~s";
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
                $tag_meta = array('class' => 'fx_template_var_in_att');
                foreach ($att_fields as $afk => $af) {
                    $af_meta = json_decode($af['meta']);
                    $af_meta->value = $af['value'];
                    $tag_meta['data-fx_template_var_'.$afk] = htmlentities(json_encode($af_meta));
                }
                $tag = fx_html_token::create_standalone($tag);
                $tag->add_meta($tag_meta);
                $tag = $tag->serialize();
                return $tag;
            }, 
            $html
        );
        return $html;
    }
    
    /*
     * Заменяет переменные, завернутые в единственный тег
     * (чтоб не создавать лишнюю разметку)
     */
    protected static function _replace_fields_wrapped_by_tag($html) {
        $html = preg_replace("~<!--.*?-->~s", '', $html);
        $field_regexp = '~(<([a-z0-9]+)[^>]*?>)([\s]*?)'.self::$_field_regexp.'([\s]*?)(</\2>)~s';
        $html = preg_replace_callback(
            $field_regexp, 
            function($matches) {
                $tag = fx_html_token::create_standalone($matches[1]);
                $tag->add_meta(array(
                    'class' => 'fx_template_var',
                    'data-fx_var' => htmlentities($matches[5])
                ));
                $tag = $tag->serialize();
                return $tag.$matches[3].$matches[6].$matches[7].$matches[8];
            },
            $html
        );
        return $html;
    }
    
    protected static function _replace_fields_in_text($html) {
        $html = preg_replace_callback("~".self::$_field_regexp."~s", function($field_matches) {
            $field_content = $field_matches[3];
            $tag = preg_match("~<(?:div|ul|li|table|br)~i", $field_content) ? 'div' : 'span';
            return '<'.$tag.' class="fx_template_var" data-fx_var="'.htmlentities($field_matches[2]).'">'.
                    $field_matches[3].'</'.$tag.'>';
        }, $html);
        return $html;
    }
}
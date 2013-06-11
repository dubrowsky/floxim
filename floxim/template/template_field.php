<?php

class fx_template_field  {
    protected $_value = null;
    
    protected $_meta = array();
    
    public function __construct($value = null, $meta = array()) {
        $this->_value = $value;
        $this->_meta = $meta;
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
    
    public static $replacements = array();
    
    public function __toString() {
        $val = $this->get_value();
        if (!$this->get_meta('editable') || !fx::is_admin()) {
            return $val;
        }
        $id = $this->get_meta('id');
        self::$replacements []= array($id, $this->_meta, $val);
        return "###fxf".(count(self::$replacements)-1)."###";
    }
    
    /**
     * Постпроцессинг полей
     * @param string $html
     */
    public static function replace_fields($html) {
        //echo fen_debug('rep start');
        $html = self::_replace_fields_in_atts_2($html);
        //echo fen_debug('atts don');
        $html = self::_replace_fields_wrapped_by_tag_2($html);
        //echo fen_debug('tags don');
        $html = self::_replace_fields_in_text_2($html);
        //echo fen_debug('text don');
        return $html;
        /*
        $html = self::_replace_fields_in_atts($html);
        $html = self::_replace_fields_wrapped_by_tag($html);
        $html = self::_replace_fields_in_text($html);
        return $html;
        */
    }
    
    protected static function _replace_fields_in_atts_2($html) {
        $html = preg_replace_callback(
            "~<[^>]+###fxf\d+###[^>]+?>~", 
            function($tag_matches) {
                $att_fields = array();
                $tag = preg_replace_callback(
                    '~###fxf(\d+)###~', 
                    function($field_matches) use (&$att_fields) {
                        $replacement = fx_template_field::$replacements[$field_matches[1]];
                        $replacement[1]['value'] = $replacement[2];
                        $att_fields[$replacement[0]] = $replacement[1];
                        fx_template_field::$replacements[$field_matches[1]] = null;
                        return $replacement[2];
                    }, 
                    $tag_matches[0]
                );
                $tag_meta = array('class' => 'fx_template_var_in_att');
                foreach ($att_fields as $afk => $af) {
                    //$tag_meta['data-fx_template_var_'.$afk] = htmlentities(json_encode($af));
                    $tag_meta['data-fx_template_var_'.$afk] = $af;
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

    protected static function _replace_fields_wrapped_by_tag_2($html) {
        $html = preg_replace_callback(
            "~(<[a-z0-9_-]+[^>]*?>)(\s*?)###fxf(\d+)###(\s*?</[a-z0-9_-]+>)~", 
            function($matches) {
                $replacement = fx_template_field::$replacements[$matches[3]];
                $tag = fx_html_token::create_standalone($matches[1]);
                $tag->add_meta(array(
                    'class' => 'fx_template_var',
                    'data-fx_var' => $replacement[1]
                ));
                $tag = $tag->serialize();
                fx_template_field::$replacements[$matches[3]] = null;
                return $tag.$matches[2].$replacement[2].$matches[4];
            },
            $html
        );
        return $html;
    }
    
    protected static function _replace_fields_in_text_2($html) {
        $html = preg_replace_callback(
            "~###fxf(\d+)###~", 
            function($matches) {
                $replacement = fx_template_field::$replacements[$matches[1]];
                $tag_name = preg_match("~<(?:div|ul|li|table|br)~i", $replacement[2]) ? 'div' : 'span';
                $tag = fx_html_token::create_standalone('<'.$tag_name.'>');
                $tag->add_meta(array(
                    'class' => 'fx_template_var',
                    'data-fx_var' => $replacement[1]
                ));
                fx_template_field::$replacements[$matches[1]] = null;
                return $tag->serialize().$replacement[2].'</'.$tag_name.'>';
            },
            $html
        );
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
        echo fen_debug(htmlspecialchars($html));
        $frex = "###fx_template_field\|([^\|]*?)\|(.+?)###(.*?)###fx_template_field_end###";
        preg_match_all("~<([a-z0-9]+)[^>]*?>\s*?".$frex.'\s*?(<\/\1>)~s', $html, $matches);
        echo fen_debug($matches);
        die();
        $field_regexp = '~(<([a-z0-9]+)[^>]*?>)([\s]*?)'.self::$_field_regexp.'([\s]*?)(</\2>)~s';
        /*$field_regexp = '~(<([a-z0-9]+)[^>]*?>)([\s]*?)'.self::$_field_regexp."([\s]*?)(</[^>]+>)~s";*/
        $html = preg_replace_callback(
            $field_regexp, 
            function($matches) {
                $tag = fx_html_token::create_standalone($matches[1]);
                $tag->add_meta(array(
                    'class' => 'fx_template_var',
                    'data-fx_var' => htmlentities($matches[5])
                ));
                dev_log($matches, $tag);
                $tag = $tag->serialize();
                
                return $tag.$matches[3].$matches[6].$matches[7].$matches[8];
            },
            $html
        );
        dev_log('wbt repd', htmlspecialchars($html));
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
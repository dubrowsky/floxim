<?php

class fx_template_field  {
	
	public static function format_date($value, $format) {
		if (!is_numeric($value)) {
			$value = strtotime($value);
		}
		return date($format, $value);
	}
    
    public static function format_image($value, $format) {
        try {
            $thumber = new fx_thumb($value, $format);
            $value = $thumber->get_result_path();
        } catch (Exception $e) {
            $value = '';
        }
        return $value;
    }
	
    protected $_value = null;
    
    protected $_meta = array();
    
    public function __construct($value = null, $meta = array()) {
        $this->_value = $value;
        $this->_meta = $meta;
    }
    
    public function get_value(){
        return $this->_value;
    }
    
    public function set_value($value) {
        $this->_value = $value;
    }
    
    public function set_meta($key, $value) {
        $this->_meta[$key] = $value;
    }
    
    public function get_meta($key) {
        return isset($this->_meta[$key])? $this->_meta[$key] : null;
    }
    
    public static $replacements = array();
    
    public function __toString() {
        $val = isset($this->_meta['display_value']) ? $this->_meta['display_value'] : $this->get_value();
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
        $html = self::_replace_fields_in_atts($html);
        $html = self::_replace_fields_wrapped_by_tag($html);
        $html = self::_replace_fields_in_text($html);
        return $html;
    }
    
    protected static function _replace_fields_in_atts($html) {
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
                $tag = fx_template_html_token::create_standalone($tag);
                $tag->add_meta($tag_meta);
                $tag = $tag->serialize();
                return $tag;
            }, 
            $html
        );
        return $html;
    }

    protected static function _replace_fields_wrapped_by_tag($html) {
        $html = preg_replace_callback(
            "~(<[a-z0-9_-]+[^>]*?>)(\s*?)###fxf(\d+)###(\s*?</[a-z0-9_-]+>)~", 
            function($matches) {
                $replacement = fx_template_field::$replacements[$matches[3]];
                $tag = fx_template_html_token::create_standalone($matches[1]);
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
    
    protected static function _replace_fields_in_text($html) {
        $html = preg_replace_callback(
            "~###fxf(\d+)###~", 
            function($matches) {
                $replacement = fx_template_field::$replacements[$matches[1]];
                $tag_name = preg_match("~<(?:div|ul|li|table|br)~i", $replacement[2]) ? 'div' : 'span';
                $tag = fx_template_html_token::create_standalone('<'.$tag_name.'>');
                $tag->add_meta(array(
                    'class' => 'fx_template_var',
                    'data-fx_var' => $replacement[1]
                ));
                fx_template_field::$replacements[$matches[1]] = null;
                $res = $tag->serialize().$replacement[2].'</'.$tag_name.'>';
                return $res;
            },
            $html
        );
        return $html;
    }

}
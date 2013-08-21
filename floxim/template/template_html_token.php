<?php
class fx_template_html_token {
    public $type;
    public $name;
    public $source;
    
    /*
     * Создать html-токен из исходника
     * @param string $source - строка с html-тегом
     * @return fx_template_html_token
     */
    public static function create($source) {
        $token = new self();
        $single = array('input', 'img', 'link', 'br', 'meta');
        if (preg_match("~^<(/?)([a-z0-9]+).*?(/?)>$~is", $source, $parts)) {
            $token->name = strtolower($parts[2]);
            $token->original_name = $parts[2];
            if (in_array($token->name, $single)||$parts[3]) {
                $token->type = 'single';
            } else {
                $token->type = $parts[1] ? 'close' : 'open';
            }
        } else {
            $token->type = 'single';
            $token->name = 'text';
        }
        $token->source = $source;
        return $token;
    }
    
    /*
     * Создать html-токен и назначить ему тип "вне дерева"
     * @param $source - строка с html-тегом
     * @return fx_template_html_token
     */
    public static function create_standalone($source) {
        $token = self::create($source);
        $token->type = 'standalone';
        return $token;
    }
    
    public function remove() {
        foreach ($this->parent->children as $child_index => $child) {
            if ($child == $this) {
                unset($this->parent->children[$child_index]);
                break;
            }
        }
    }
    
    public function add_child(fx_template_html_token $token, $before_index = null) {
        if (!isset($this->children)) {
            $this->children = array();
        }
        if (!is_null($before_index)) {
            array_splice($this->children, $before_index, 0, array($token));
        } else {
            $this->children[]= $token;
        }
        $token->parent = $this;
    }
    
    public function add_child_first(fx_template_html_token $token) {
        $this->add_child($token, 0);
    }
    
    public function add_child_before(fx_template_html_token $new_child, fx_template_html_token $ref_child) {
        if (($ref_index = $this->get_child_index($ref_child)) === null ) {
            return;
        }
        $this->add_child($new_child, $ref_index);
    }
    
    public function add_child_after(fx_template_html_token $new_child, fx_template_html_token $ref_child) {
        if (($ref_index = $this->get_child_index($ref_child)) === null ) {
            return;
        }
        $this->add_child($new_child, $ref_index+1);
    }
    
    public function get_child_index(fx_template_html_token $ref_child) {
        if ( ($index = array_search($ref_child, $this->get_children())) === false) {
            return null;
        }
        return $index;
    }
    
    public function serialize() {
        $res = '';
        // свойство omit добавляется из transform_to_floxim
        $omit = false;
        $omit_conditional = false;
        if ( isset($this->omit) ) {
            if ($this->omit =='true') {
                $omit = true;
            } else {
                $omit_conditional = true;
                $omit_var_name = '$omit_'.md5($this->omit);
                $res .= '<?'.$omit_var_name.' = '.$this->omit.'; if ('.$omit_var_name.') {?>';
            }
        }
        $tag_start = '';
        if ($this->name != 'root' && !$omit)  {
            if (isset($this->attributes) && isset($this->attributes_modified)) {
                $tag_start .= '<'.$this->original_name;
                foreach ($this->attributes as $att_name => $att_val) {
                    $tag_start .= ' '.$att_name;
                    
                    if ($att_val === null) {
                        continue;
                    }
                    //$quot = in_array($att_name, $this->fx_meta_atts) ? "'" : '"';
                    $quot = isset($this->att_quotes[$att_name]) 
                                ? $this->att_quotes[$att_name] 
                                : '"';
                    $tag_start .= '='.$quot.$att_val.$quot;
                }
                if ($this->type == 'single') {
                    $tag_start .= ' /';
                }
                $tag_start .= '>';
            } else {
                $tag_start .= $this->source;
            }
            if ( isset($this->_injections) && count($this->_injections) > 0) {
                $injections = $this->_injections;
                $tag_start = preg_replace_callback(
                    "~#inj(\d+)#~", 
                    function($matches) use ($injections) {
                        return $injections[$matches[1]];
                    }, 
                    $tag_start
                );
            }
        }
        
        $res .= $tag_start;
        if ($omit_conditional) {
            $res .= '<?}?>';
        }
        
        // закончили собирать сам тег
        if (isset($this->children)) {
            foreach ($this->children as $child) {
                $res .= $child->serialize();
            }
        }
        if ($this->type == 'open' && $this->name != 'root' && !$omit) {
            if ($omit_conditional) {
                $res .= '<?if ('.$omit_var_name.') {?>';
            }
            $res .= "</".$this->original_name.">";
            
            if ($omit_conditional) {
                $res .= '<?}?>';
            }
        }
        return $res;
    }
    
    public function get_children() {
        if (!isset($this->children)) {
            return array();
        }
        return $this->children;
    }
    
    protected function _parse_attributes() {
        $source = preg_replace("~^<[a-z0-9_]+~", '', $this->source);
        
        // Сохраняем в массив field-маркеры, восстановим при обратной сборке
        $injections = array();
        
        $source = preg_replace_callback( 
            "~{.+?}~i", 
            function($matches) use (&$injections) {
                $injections []= $matches[0];
                return '#inj'.(count($injections)-1).'#';
            }, 
            $source
        );
        
        // и аналогично для пыха
        $source = preg_replace_callback(
            "~<\?.+?\?>~i", 
            function($matches) use (&$injections) {
                $injections []= $matches[0];
                return '#inj'.(count($injections)-1).'#';
            }, 
            $source
        );
        
        $this->_injections = $injections;
        $source  = preg_replace("~\s([a-z0-9\:_-]+)\s*?=\s*?([^\'\\\"\s]+)~", ' $1="$2"', $source);
        $atts = null;
        preg_match_all('~(#inj\d+#)|([a-z0-9\:_-]+)=(["\'])(.*?)\3~s', $source, $atts);
        $this->attributes = array();
        foreach ($atts[0] as $att_num => $att_full) {
            $att_name = $atts[2][$att_num];
            $att_val = $atts[4][$att_num];
            $this->att_quotes[$att_name] = $atts[3][$att_num];
            if (empty($att_name)) {
                $att_name = $att_full;
                $att_val = null;
            }
            $this->attributes[$att_name] = $att_val;
        }
    }
    
    public function get_attribute($att_name) {
        if ($this->name == 'text') {
            return null;
        }
        if (!isset($this->attributes)) {
            $this->_parse_attributes();
        }
        if (!isset($this->attributes[$att_name])) {
            return null;
        }
        $att = $this->attributes[$att_name];
        if (!$this->_injections) {
            return $att;
        }
        $injections = $this->_injections;
        $att = preg_replace_callback(
            "~#inj(\d+)#~", 
            function($matches) use ($injections) {
                return $injections[$matches[1]];
            }, 
            $att
        );
        return $att;
    }
    
    public function set_attribute($att_name, $att_value) {
        if ($this->name == 'text') {
            return;
        }
        if (!isset($this->attributes)) {
            $this->_parse_attributes();
        }
        $this->attributes[$att_name] = $att_value;
        $this->attributes_modified = true;
    }
    
    public function add_class($class) {
        if (! ($c_class = $this->get_attribute('class')) ) {
            $this->set_attribute('class', $class);
			return;
        }
        $c_class = preg_split("~\s+~", $c_class);
        if (in_array($class, $c_class)) {
            return;
        }
        $this->set_attribute('class', join(" ", $c_class)." ".$class);
    }
    
    public function remove_attribute($att_name) {
        if (!$this->attributes) {
            $this->_parse_attributes();
        }
        unset($this->attributes[$att_name]);
        $this->attributes_modified = true;
    }
    
    protected $att_quotes = array();
    public function add_meta($meta) {
        foreach ($meta as $k => $v) {
            if ($k == 'class') {
                $this->add_class($v);
            } else {
                if (is_array($v) || is_object($v)) {
                    $v = htmlentities(json_encode($v));
                    
                    $v = str_replace("'", '&apos;', $v);
                    $v = str_replace("&quot;", '"', $v);
                    $this->att_quotes[$k] = "'";
                }
                $this->set_attribute($k, $v);
            }
        }
    }


    public function apply($callback) {
        call_user_func($callback, $this);
        foreach ($this->get_children() as $child) {
            $child->apply($callback);
        }
    }
}
?>
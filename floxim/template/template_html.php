<?php
class fx_template_html {
    protected $_string = null;
    public function __construct($string) {
        $string = trim($string);
        $string = preg_replace("~(<[^>]+>)\s+(?=<)~", '$1', $string);
        $this->_string = $string;
    }
    
    public static function has_floxim_atts($string) {
        $res = preg_match(
                '~<[^>]+fx_(template|area|render|var|replace)[a-z_]*?=[\'\"]~', 
                $string, 
                $atts
        );
        return $res;
    }
    
    public function tokenize() {
        $tags = preg_split(
                "~(<[/]?[a-z0-9]+[^>]*?>)~is", 
                $this->_string, 
                -1, 
                PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );
        $tokens = array();
        foreach ($tags as $tag) {
            $tokens []= fx_html_token::create($tag);
        }
        return $tokens;
    }
    
    public function add_meta($meta = array()) {
        $tree = $this->make_tree($this->tokenize());
        $children = $tree->get_children();
        if (count($children) == 1) {
            $root = $children[0];
            $root->add_meta($meta);
            return $tree->serialize();
        }
        $wrapper = fx_html_token::create('<div>');
        $wrapper->add_meta($meta);
        foreach ($children as $child) {
            $wrapper->add_child($child);
        }
        return $wrapper->serialize();
    }
    
    public function transform_to_floxim() {
        
        $tree = $this->make_tree($this->tokenize());
        
        $unnamed_replaces = array();
        
        $tree->apply( function(fx_html_token $n) use (&$unnamed_replaces) {
            if ($n->name == 'text') {
                return;
            }
            if ( ($fx_replace = $n->get_attribute('fx_replace')) ){
                $replace_atts = explode(",", $fx_replace);
                foreach ($replace_atts as $replace_att) {
                    if (!isset($unnamed_replaces[$replace_att])) {
                        $unnamed_replaces[$replace_att] = 0;
                    }
                    $var_name = 'replace_'.$replace_att.'_'.$unnamed_replaces[$replace_att];
                    $unnamed_replaces[$replace_att]++;
                    $default_val = $n->get_attribute($replace_att);
                    switch($replace_att) {
                        case 'src':
                            $var_title = 'Картинка';
                            break;
                        case 'href':
                            $var_title = 'Ссылка';
                            break;
                        default:
                            $var_title = $replace_att;
                            break;
                    }
                    $n->set_attribute($replace_att, '{%'.$var_name.' title="'.$var_title.'"}'.$default_val.'{/%'.$var_name.'}');
                }
            }
            if ( ($var_name = $n->get_attribute('fx_var')) ) {
                if (!preg_match("~^[\$\%]~", $var_name)) {
                    $var_name = '%'.$var_name;
                }
                $n->add_child_first(fx_html_token::create('{'.$var_name.'}'));
                $n->add_child(fx_html_token::create('{/'.$var_name.'}'));
                $n->remove_attribute('fx_var');
            }
            if ( ($tpl_id = $n->get_attribute('fx_template'))) {
                $tpl_macro_tag = '{template id="'.$tpl_id.'"';
                if ( ($tpl_for = $n->get_attribute('fx_template_for')) ) {
                    $tpl_macro_tag .= ' for="'.$tpl_for.'"';
                    $n->remove_attribute('fx_template_for');
                }
                $tpl_macro_tag .= '}';
                $n->parent->add_child_before(fx_html_token::create($tpl_macro_tag), $n);
                $n->parent->add_child_after(fx_html_token::create('{/template}'), $n);
                $n->remove_attribute('fx_template');
            }
            if ( ($render_id = $n->get_attribute('fx_render')) ) {
                $render_macro_tag = '{render';
                if (!empty($render_id)) {
                    $render_macro_tag .= ' select="'.$render_id.'"';
                }
                $render_macro_tag .= '}';
                $n->parent->add_child_before(fx_html_token::create($render_macro_tag), $n);
                $n->parent->add_child_after(fx_html_token::create('{/render}'), $n);
                $n->remove_attribute('fx_render');
            }
            if ( ($area_id = $n->get_attribute('fx_area'))) {
                $n->remove_attribute('fx_area');
                $area = '{area id="'.$area_id.'" /}';
                $n->add_child_first(fx_html_token::create($area));
            }
            if ( ($if_test = $n->get_attribute('fx_if'))) {
                $n->remove_attribute('fx_if');
                $if = '{if test="'.$if_test.'"}';
                $n->parent->add_child_before(fx_html_token::create($if), $n);
                $n->parent->add_child_after(fx_html_token::create('{/if}'), $n);
            }
        });
        return $tree->serialize();
    }
    
    public function make_tree($tokens) {
        $root = new fx_html_token();
        $root->name = 'root';
        $stack = array($root);
        while ($token = array_shift($tokens)) {
            switch ($token->type) {
                case 'open':
                    if (count($stack) > 0) {
                        end($stack)->add_child($token);
                    }
                    $stack []= $token;
                    break;
                case 'close':
                    array_pop($stack);
                    break;
                case 'single': default:
                    $stack_last = end($stack);
                    if (!$stack_last) {
                        dev_log("fx_template_html tree error", $tokens, $root);
                        echo "fx_template_html error: stack empty, trying to add: ";
                        echo "<pre>" . htmlspecialchars(print_r($token, 1)) . "</pre>";
                        die();
                    }
                    $stack_last->add_child($token);
                    break;
            }
        }
        return $root;
    }
    
    public static function add_class_to_tag($tag_html, $class) {
        if (preg_match("~class\s*=[\s\'\"]*[^\'\"\>]+~i", $tag_html, $class_att)) {
            $class_att_new = preg_replace(
                "~class\s*=[\s\'\"]*~", 
                '$0'.$class.' ', 
                $class_att[0]
            );
            $tag_html = str_replace($class_att, $class_att_new, $tag_html);
        } else {
            $tag_html = self::add_att_to_tag($tag_html, 'class', $class);
        }
        return $tag_html;
    }
    
    public static function add_att_to_tag($tag_html, $att, $value) {
        $tag_html = preg_replace("~^<[^\s>]+~", '$0 '.$att.'="'.htmlentities($value).'"', $tag_html);
        return $tag_html;
    }
}

class fx_html_token {
    public $type;
    public $name;
    public $source;
    
    /*
     * Создать html-токен из исходника
     * @param string $source - строка с html-тегом
     * @return fx_html_token
     */
    public static function create($source) {
        $token = new self();
        $single = array('input', 'img', 'link', 'br');
        if (preg_match("~^<(/?)([a-z0-9]+).*?(/?)>$~is", $source, $parts)) {
            $token->name = strtolower($parts[2]);
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
     * @return fx_html_token
     */
    public static function create_standalone($source) {
        $token = self::create($source);
        $token->type = 'standalone';
        return $token;
    }
    
    public function add_child(fx_html_token $token, $before_index = null) {
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
    
    public function add_child_first(fx_html_token $token) {
        $this->add_child($token, 0);
    }
    
    public function add_child_before(fx_html_token $new_child, fx_html_token $ref_child) {
        if (($ref_index = $this->get_child_index($ref_child)) === null ) {
            return;
        }
        $this->add_child($new_child, $ref_index);
    }
    
    public function add_child_after(fx_html_token $new_child, fx_html_token $ref_child) {
        if (($ref_index = $this->get_child_index($ref_child)) === null ) {
            return;
        }
        $this->add_child($new_child, $ref_index+1);
    }
    
    public function get_child_index(fx_html_token $ref_child) {
        if ( ($index = array_search($ref_child, $this->get_children())) === false) {
            return null;
        }
        return $index;
    }
    
    public function serialize() {
        $res = '';
        if ($this->name != 'root')  {
            if (isset($this->attributes) && isset($this->attributes_modified)) {
                $res .= '<'.$this->name;
                foreach ($this->attributes as $att_name => $att_val) {
                    $res .= ' '.$att_name.'="'.
                                $att_val.
                                // последний аргумент - выключаем double_encode
                                //htmlentities($att_val, ENT_COMPAT | ENT_HTML401, 'UTF-8', false).
                                '"';
                }
                if ($this->type == 'single') {
                    $res .= ' /';
                }
                $res .= '>';
            } else {
                $res .= $this->source;
            }
            $res .= "\n";
        }
        if (isset($this->children)) {
            foreach ($this->children as $child) {
                $res .= $child->serialize();
            }
        }
        if ($this->type == 'open' && $this->name != 'root') {
            $res .= "</".$this->name.">\n";
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
        $source  = preg_replace("~\s([a-z]+)\s*?=\s*?([^\'\\\"\s]+)~", ' $1="$2"', $source);
        preg_match_all('~([a-z0-9_-]+)="([^\"]+)"~', $source, $atts);
        $this->attributes = array();
        foreach ($atts[1] as $att_num => $att_name) {
            $this->attributes[$att_name] = $atts[2][$att_num];
        }
    }
    
    public function get_attribute($att_name) {
        if ($this->name == 'text') {
            return null;
        }
        if (!isset($this->attributes)) {
            $this->_parse_attributes();
        }
        return isset($this->attributes[$att_name]) ? $this->attributes[$att_name] : null;
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
    
    public function add_meta($meta) {
        foreach ($meta as $k => $v) {
            if ($k == 'class') {
                $this->add_class($v);
            } else {
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
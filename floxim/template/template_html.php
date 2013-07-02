<?php
class fx_template_html {
    protected $_string = null;
    public function __construct($string) {
        $string = trim($string);
        //$string = preg_replace("~(<[^>]+>)\s+(?=<)~", '$1', $string);
        $this->_string = $string;
    }
    
    public static function has_floxim_atts($string) {
        $res = preg_match(
                '~<[^>]+fx:(template|area|each|var|replace)[a-z_]*?=[\'\"]~', 
                $string, 
                $atts
        );
        return $res;
    }
    
    public function tokenize() {
        $tokenizer = new fx_html_tokenizer();
        $tokens = $tokenizer->parse($this->_string);
        return $tokens;
    }
    
    public function add_meta($meta = array(), $skip_parsing = false) {
        // добавляем сразу обертку
        if ($skip_parsing) {
            $wrapper = fx_html_token::create_standalone('<div class="fx_wrapper">');
            $wrapper->add_meta($meta);
            return $wrapper->serialize().$this->_string."</div>";
        }
        $tree = $this->make_tree($this->tokenize());
        $children = $tree->get_children();
        if (count($children) == 1) {
            if ($children[0]->name != 'text') {
                $root = $children[0];
                $root->add_meta($meta);
                return $tree->serialize();
            }
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
            if (preg_match("~\{\%~", $n->source)) {
                $n->source = fx_template_html::parse_floxim_vars_in_atts($n->source);
            }
            if ($n->name == 'meta' && ($layout_id = $n->get_attribute('fx:layout'))) {
                $layout_name = $n->get_attribute('fx:name');
                $tpl_tag = '{template id="'.$layout_id.'" name="'.$layout_name.'"}';
                $tpl_tag .= '{call id="_layout_body"}';
                $content = $n->get_attribute('content');
                $vars = explode(",", $content);
                foreach ($vars as $var) {
                    $var = trim($var);
                    $negative = false;
                    if (preg_match("~^!~", $var)) {
                        $negative = true;
                        $var = preg_replace("~^!~", '', $var);
                    }
                    $tpl_tag .= '{$'.$var.' select="'.($negative ? 'false' : 'true').'" /}';
                }
                $tpl_tag .= '{/call}{/template}';
                $n->parent->add_child_before(fx_html_token::create($tpl_tag), $n);
                $n->remove();
                return;
            }
            if ( ($fx_replace = $n->get_attribute('fx:replace')) ){
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
                            $var_title = fx::lang('Картинка','system');
                            break;
                        case 'href':
                            $var_title = fx::lang('Ссылка','system');
                            break;
                        default:
                            $var_title = $replace_att;
                            break;
                    }
                    $n->set_attribute($replace_att, '{%'.$var_name.' title="'.$var_title.'"}'.$default_val.'{/%'.$var_name.'}');
                    $n->remove_attribute('fx:replace');
                }
            }
            if ( ($var_name = $n->get_attribute('fx:var')) ) {
                if (!preg_match("~^[\$\%]~", $var_name)) {
                    $var_name = '%'.$var_name;
                }
                $n->add_child_first(fx_html_token::create('{'.$var_name.'}'));
                $n->add_child(fx_html_token::create('{/'.$var_name.'}'));
                $n->remove_attribute('fx:var');
            }
            if ( ($tpl_id = $n->get_attribute('fx:template'))) {
                $tpl_macro_tag = '{template id="'.$tpl_id.'"';
                if ( ($tpl_for = $n->get_attribute('fx:of')) ) {
                    $tpl_macro_tag .= ' of="'.$tpl_for.'"';
                    $n->remove_attribute('fx:of');
                }
                if ( ($tpl_name = $n->get_attribute('fx:name'))) {
                    $tpl_macro_tag .= ' name="'.$tpl_name.'"';
                    $n->remove_attribute('fx:name');
                }
                $tpl_macro_tag .= '}';
                $n->parent->add_child_before(fx_html_token::create($tpl_macro_tag), $n);
                $n->parent->add_child_after(fx_html_token::create('{/template}'), $n);
                $n->remove_attribute('fx:template');
                if (
                    !in_array($tpl_id, array('item', 'active', 'inactive', 'separator')) &&
                    !preg_match('~^\$~', $tpl_id)
                    ) {
                        $n->set_attribute('fx:is_sub_root', $tpl_id);
                }
            }
            if ( ($each_id = $n->get_attribute('fx:each')) ) {
                $each_macro_tag = '{each';
                if (!empty($each_id)) {
                    $each_macro_tag .= ' select="'.$each_id.'"';
                }
                if ( ($each_as = $n->get_attribute('fx:as'))) {
                    $each_macro_tag .= ' as="'.$each_as.'"';
                    $n->remove_attribute('fx:as');
                }
                if (($each_key = $n->get_attribute('fx:key'))) {
                    $each_macro_tag .= ' key="'.$each_key."'";
                    $n->remove_attribute('fx:key');
                }
                $each_macro_tag .= '}';
                $n->parent->add_child_before(fx_html_token::create($each_macro_tag), $n);
                $n->parent->add_child_after(fx_html_token::create('{/each}'), $n);
                $n->remove_attribute('fx:each');
            }
            if ( ($area_id = $n->get_attribute('fx:area'))) {
                $n->remove_attribute('fx:area');
                $area = '{area id="'.$area_id.'" ';
                if ( ($area_size = $n->get_attribute('fx:size')) ) {
                    $area .= 'size="'.$area_size.'" ';
                    $n->remove_attribute('fx:size');
                }
                $area .= '/}';
                $n->add_child_first(fx_html_token::create($area));
            }
            if ( ($if_test = $n->get_attribute('fx:if'))) {
                $n->remove_attribute('fx:if');
                $if = '{if test="'.$if_test.'"}';
                $n->parent->add_child_before(fx_html_token::create($if), $n);
                $n->parent->add_child_after(fx_html_token::create('{/if}'), $n);
            }
            if ( ($omit = $n->get_attribute('fx:omit'))) {
                $n->omit = $omit;
                $n->remove_attribute('fx:omit');
            }
        });
        $res = $tree->serialize();
        return $res;
    }
    
    public static function parse_floxim_vars_in_atts($input_source) {
        $res = '';
        $source = preg_split("~(\s|=[\'\"]|:|<\?.+?\?>|\{[^\}]+?\})~", $input_source, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $c_tag = null;
        $c_att = null;
        $c_prop = null;
        $c_mode = null;
        //echo fen_debug($source);
        foreach ($source as $part) {
            if (!$c_tag && preg_match("~^<~", $part)) {
                $c_tag = preg_replace("~^<~", '', $part);
                $res .= $part;
                $c_mode = 'tag';
                continue;
            }
            if (preg_match("~^<\?~", $part)) {
                $res .= $part;
                continue;
            }
            if (preg_match("~^\{[^\%]~", $part)) {
                $res .= $part;
                continue;
            }
            if (preg_match("~^\s+$~", $part)) {
                $res .= $part;
                continue;
            }
            if ($c_mode == 'tag') {
                $res .= $part;
                $c_att = $part;
                $c_mode = 'att';
                continue;
            }
            if ($c_mode == 'att') {
                if (preg_match("~^=[\'\"]$~", $part)) {
                    $c_prop = null;
                    $c_mode = 'attval';
                } else {
                    $c_att .= $part;
                }
                $res .= $part;
                continue;
            }
            if ($c_mode == 'attval') {
                // конец атрибута
                if (preg_match('~\"$~', $part)) {
                    $c_att = '';
                    $c_mode = 'tag';
                    $res .= $part;
                    continue;
                }
                // запоминаем css-свойство
                if ($c_att == 'style' && !$c_prop) {
                    $c_prop = preg_replace('~^\"~', '', $part);
                    $res.= $part;
                    continue;
                }
                if ($c_att == 'style' && preg_match("~;$~", $part)) {
                    $c_prop = null;
                    $res .= $part;
                    continue;
                }
                if (preg_match("~^\{\%~", $part)) {
                    if (!preg_match("~type=~", $part)) {
                        $c_type = '';
                        if ($c_att == 'style') {
                            if (
                                    ($c_prop == 'background' 
                                    && preg_match('~url\([\'\"]?$~', $res) ) ||
                                    $c_prop == 'background-image'
                                ) {
                                $c_type = 'image';
                            } elseif ($c_prop == 'background' || $c_prop == 'background-color') {
                                $c_type = 'color';
                            } elseif ($c_prop == 'width' || $c_prop == 'height') {
                                $c_type = 'number';
                            }
                        } elseif ($c_att == 'src') {
                            $c_type = 'image';
                        }
                        if ($c_type) {
                            $part = preg_replace("~\}$~", ' type="'.$c_type.'"}', $part);
                        }
                    }
                    $res .= $part;
                    continue;
                }
            }
            $res .= $part;
        }
        return $res;
    }
    
    public function make_tree($tokens) {
        $root = new fx_html_token();
        $root->name = 'root';
        $stack = array($root);
        $token_index = -1;
        while ($token = array_shift($tokens)) {
            $token_index++;
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
                        echo fx_debug(
                                "fx_template_html error: stack empty, trying to add: ",
                                '#'.$token_index,
                                $token,
                                $tokens,
                                $root);
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
    
    public function remove() {
        foreach ($this->parent->children as $child_index => $child) {
            if ($child == $this) {
                unset($this->parent->children[$child_index]);
                break;
            }
        }
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
                $tag_start .= '<'.$this->name;
                foreach ($this->attributes as $att_name => $att_val) {
                    $tag_start .= ' '.$att_name;
                    
                    if ($att_val === null) {
                        continue;
                    }
                    $tag_start .= '="'.
                                $att_val.
                                // последний аргумент - выключаем double_encode
                                //htmlentities($att_val, ENT_COMPAT | ENT_HTML401, 'UTF-8', false).
                                '"';
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
            $res .= "</".$this->name.">";
            
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
        $source = preg_replace_callback("~###fx_template_field.*?fx_template_field_end###~", function($matches) use (&$injections) {
            $injections []= $matches[0];
            return "#inj".(count($injections)-1)."#";
        }, $source);
        
        // здесь должна быть более общая регулярка
        // пока ловим fx-ы
        $source = preg_replace_callback(
            //"~{if.*?/if}~i", 
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
        
        preg_match_all('~(#inj\d+#)|([a-z0-9\:_-]+)="([^\"]+)"~', $source, $atts);
        
        $this->attributes = array();
        foreach ($atts[0] as $att_num => $att_full) {
            $att_name = $atts[2][$att_num];
            $att_val = $atts[3][$att_num];
            if (empty($att_name)) {
                $att_name = $att_full;
                $att_val = null;
            }
            $this->attributes[$att_name] = $att_val;
        }
        /*
        foreach ($atts[1] as $att_num => $att_name) {
            $this->attributes[$att_name] = $atts[2][$att_num];
        }*/
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
                if (is_array($v) || is_object($v)) {
                    $v = htmlentities(json_encode($v));
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

class fx_html_tokenizer {
    const STATE_TEXT = 1;
    const STATE_TAG = 2;
    const STATE_PHP = 3;
    const STATE_ANY = 4;
    const STATE_ATT_NAME = 5;
    const STATE_ATT_VAL = 6;
    const STATE_FX = 7;
    
    public function __construct() {
		$this->state = self::STATE_TEXT;
		$this->add_rule(self::STATE_ANY, '<?', self::STATE_PHP, 'php_start');
		$this->add_rule(self::STATE_PHP, '?>', false, 'php_end');
		
        $this->add_rule(self::STATE_TAG, '{', self::STATE_FX, 'fx_start');
        $this->add_rule(self::STATE_TEXT, '{', self::STATE_FX, 'fx_start');
        $this->add_rule(self::STATE_ATT_NAME, '{', self::STATE_FX, 'fx_start');
        $this->add_rule(self::STATE_ATT_VAL, '{', self::STATE_FX, 'fx_start');
        $this->add_rule(self::STATE_FX, '}', false, 'fx_end');
        
		$this->add_rule(self::STATE_TEXT, '<', self::STATE_TAG, 'text_to_tag');
		$this->add_rule(self::STATE_TAG, '>', self::STATE_TEXT, 'tag_to_text');
		$this->add_rule(self::STATE_TAG, ' ', self::STATE_ATT_NAME);
		$this->add_rule(self::STATE_ATT_NAME, '="', self::STATE_ATT_VAL, 'att_value_start');
		$this->add_rule(self::STATE_ATT_NAME, "='", self::STATE_ATT_VAL, 'att_value_start');
		$this->add_rule(self::STATE_ATT_NAME, "=", self::STATE_ATT_VAL, 'att_value_start');
		$this->add_rule(self::STATE_ATT_NAME, '>', self::STATE_TEXT, 'tag_to_text');
		$this->add_rule(self::STATE_ATT_VAL, '"', self::STATE_TAG, 'att_value_end');
		$this->add_rule(self::STATE_ATT_VAL, "'", self::STATE_TAG, 'att_value_end');
		$this->add_rule(self::STATE_ATT_VAL, ' ', self::STATE_TAG, 'att_value_end');
		$this->add_rule(self::STATE_ATT_VAL, '>', self::STATE_TAG, 'att_value_end');
	}
	protected $rules = array();
	public function add_rule($first_state, $char, $new_state = false, $callback = null) {
		$this->rules [] = array($first_state, $char, $new_state, $callback, $char[0], strlen($char));
	}
	
	public function set_state($state) {
		$this->prev_state = $this->state;
		$this->state = $state;
	}
	
	protected $stack = '';
	
    public function parse($string) {
		$this->position = 0;
        $parts = preg_split("~(<[a-z0-9\/\?]+|>|<\?|\?>|[\{\}]|=[\'\"]?|[\'\"]|\s+)~", $string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        foreach ($parts as $ch) {
			foreach($this->rules as $rule) {
				list($old_state, $r_char, $new_state, $callback, $r_first, $test_length) = $rule;
				
				if ($old_state != $this->state && $old_state != self::STATE_ANY) { 
					continue;
				}
                if (substr($ch, 0, $test_length) != $r_char) {
                    continue;
                }
                $callback_ok = true;
                if ($callback) {
                    $callback_ok = $this->$callback($ch);
                }
                if ($callback_ok !== false) {
                    if ($new_state) {
                        $this->prev_state = $this->state;
                        $this->state = $new_state;
                    }
                    if (!$callback) {
                        $this->stack .= $ch;
                    }
                    continue 2;
                }
			}
			$this->stack .= $ch;
		}
        if (!empty($this->stack)) {
            $this->text_to_tag('');
        }
		return $this->res;
	}
	
	protected $res = array();
	protected function text_to_tag($ch) {
        if ($this->stack !== '') {
            $this->res []= fx_html_token::create($this->stack);
        }
		$this->stack = $ch;
	}
	
	protected function tag_to_text($ch) {
		$this->res []= fx_html_token::create($this->stack.$ch);
		$this->stack = '';
	}
	
	protected function php_start($ch) {
		$this->stack .= $ch;
	}
	
	protected function php_end($ch) {
		$this->stack .= $ch;
		$this->set_state($this->prev_state);
	}
    
    protected  function fx_start($ch) {
        $this->stack .= $ch;
    }
    
    protected function fx_end($ch) {
        $this->stack .= $ch;
        $this->set_state($this->prev_state);
    }


    protected $att_quote = null;
	protected function att_value_start($ch) {
		if (preg_match("~[\'\"]$~", $ch, $att_quote)) {
			$this->att_quote = $att_quote[0];
		}
		$this->stack .= $ch;
	}
	
	protected function att_value_end($ch) {
		switch ($ch) {
			case '"': case "'":
				if ($this->att_quote !== $ch) {
					return false;
				}
				break;
			case ' ':
				if ($this->att_quote) {
					return false;
				}
				break;
			case '>':
				if ($this->att_quote) {
					return false;
				}
				break;
		}
		$this->att_quote = null;
		if ($ch == '>') {
			$this->tag_to_text($ch);
		} else {
			$this->stack .= $ch;
		}
	}
}
?>
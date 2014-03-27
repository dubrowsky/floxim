<?php
/*
 * Class for a separate token fx-templating engine
 */
class fx_template_token {
    public $name = null;
    public $type = null;
    public $props = array();
    
    
    /**
     * to create a token from source
     * @param string $source
     * @return fx_template_token
     */
    public static function create($source) {
        if (!preg_match('~^\{~', $source)) {
            $type = 'single';
            $name = 'code';
            $props['value'] = $source;
            return new fx_template_token($name, $type, $props);
        }
        $props = array();
        $source = preg_replace("~^\{|\}$~", '', $source);
        $is_close = preg_match('~^\/~', $source);
        preg_match("~^\/?([^\s\/\\|}]+)~", $source, $name);
        $source = substr($source, strlen($name[0]));
        $name = $name[1];
        $type_info = self::get_token_info($name);
        if (preg_match("~^[\\\$%]~", $name, $var_marker)) {
            $props['id'] = preg_replace("~^[\\\$%]~", '', $name);
            $props['var_type'] = $var_marker[0] == '%' ? 'visual' : 'data';
            $name = 'var';
        }
        if ($name == 'with-each') {
            $name = 'with_each';
        }
        if (preg_match("~\/$~", $source)) {
            $type = 'single';
            $source = preg_replace("~/$~", '', $source);
        } elseif ($is_close) {
            $type = 'close';
        } elseif ($type_info['type'] == 'single') {
            $type = 'single';
        } elseif ($type_info['type'] == 'double') {
            $type = 'open';
        } else {
            $type = 'unknown';
        }
        if ($name == 'apply') {
            $name = 'call';
            $props['apply'] = true;
        }
        if ( ($name == 'if' || $name == 'elseif') && $type == 'open' && !preg_match('~test=~', $source)) {
            $props['test'] = $source;
        } elseif ($name == 'call' && $type != 'close' && !preg_match('~id=~', $source)) {
            $source = trim($source);
            $parts = preg_split("~\s+(with|each|as)\s+~", $source, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            $props['id'] = array_shift($parts);
            foreach ($parts as $prop_num => $prop){
                if ($prop_num % 2 == 0 && isset($parts[$prop_num + 1])) {
                    $props[$prop] = $parts[$prop_num + 1];
                }
            }
        } elseif (( $name == 'each' || $name == 'with_each') && $type != 'close' && !preg_match ('~select=~', $source)) {
            $props['select'] = trim($source);
        } elseif ($name == 'set') {
            $parts = explode("=", $source, 2);
            $props['var'] = trim($parts[0]);
            if (isset($parts[1])) {
                $props['value'] = trim($parts[1]);
            }
        } elseif ($name == 'with' && !preg_match("~select=~", $source)) {
            $props['select'] = trim($source);
        } else {
            $props = array_merge($props, fx_template_token_att_parser::get_atts($source));
            /*
            // add the missing attribute quotes
            // so far removed, it breaks case {if test="$x == 1}
            //$source = preg_replace("~([a-z0-9\:_-]+)\s*?=\s*?([^\'\\\"\s]+)~", ' $1="$2"', $source);
            $source = preg_replace_callback(
                '~([a-z0-9\:_-]+)=(["\'])(.+?)(?<!\\\\)\2~',
                function ($matches) use (&$props) {
                    $props[$matches[1]] = str_replace('\"', '"', $matches[3]);
                    return '';
                },
                $source
            );
            
             * 
             */
            if ($name == 'var' && preg_match("~^\s*\|~", $source)) {
                $props['modifiers'] = self::get_var_modifiers($source);
            }
        }
        
        if ($name == 'each' || $name == 'with_each') {
            $arr_id_parts = null;
            $item_key = null;
            $item_alias = null;
            $arr_id = $props['select'];
            if (preg_match("~(.+?)\sas\s(.+)$~", $arr_id, $arr_id_parts)) {
                $arr_id = trim($arr_id_parts[1]);
                $as_parts = explode("=>", $arr_id_parts[2]);
                if (count($as_parts) == 2) {
                    $item_key = trim($as_parts[0]);
                    $item_alias = trim($as_parts[1]);
                } else {
                    $item_alias = trim($as_parts[0]);
                }
            }
            $props['select'] = $arr_id;
            $props['as'] = $item_alias;
            $props['key'] = $item_key;
        }
        return new fx_template_token($name, $type, $props);   
    }
    
    public function is_empty() {
        if ($this->name != 'code') {
            return false;
        }
        return !trim($this->get_prop('value'));
    }


    public static function get_var_modifiers($source_str) {
        $p = new fx_template_modifier_parser();
        $res = $p->parse($source_str);
        return $res;
    }
    
    protected static $_token_types = array(
        'template' => array(
            'type' => 'double',
            'contains' => array('code', 'template', 'area', 'var', 'call', 'each','if', 'with_each', 'separator', 'with')
        ),
        'code' => array(
            'type' => 'single'
        ),
        'area' => array(
            'type' => 'both',
            'contains' => array('code', 'template', 'var', 'area', 'each', 'with_each')
        ),
        'var' => array(
            'type' => 'both',
            'contains' => array('code', 'var', 'call', 'area', 'template', 'each','if')
        ),
        'call' => array(
            'type' => 'both',
            'contains' => array('var', 'each', 'if')
        ),
        'templates'=> array(
            'type' => 'double',
            'contains' => array('template', 'templates')
        ),
        'each' => array(
            'type' => 'double',
            'contains' => array('code', 'template', 'area', 'var', 'call', 'each', 'if', 'elseif', 'else', 'separator')
        ),
        'with_each' => array(
            'type' => 'double',
            'contains' => array('item', 'code', 'template', 'area', 'var', 'call', 'each', 'if', 'elseif', 'else', 'separator')
        ),
        'with' => array(
            'type' => 'double',
            'contains' => array('item', 'code', 'template', 'area', 'var', 'call', 'each', 'if', 'elseif', 'else', 'separator')
        ),
        'item' => array(
            'type' => 'double',
            'contains' => array('code', 'template', 'area', 'var', 'call', 'each', 'if', 'elseif', 'else')
        ),
        'if' => array(
            'type' => 'double',
            'contains' => array('code', 'template', 'area', 'var', 'call', 'each', 'elseif', 'else')
        ),
        'else' => array(
            'type' => 'double',
            'contains' => array('code', 'template', 'area', 'var', 'call', 'each', 'elseif', 'else')
        ),
        'elseif' => array(
            'type' => 'double',
            'contains' => array('code', 'template', 'area', 'var', 'call', 'each', 'elseif', 'else')
        ),
        'separator' => array(
            'type' => 'double'
        )
    );
    
    public static function get_token_info($type) {
        $info = isset(self::$_token_types[$type]) ? self::$_token_types[$type] : array();
        if (!isset($info['contains'])) {
            $info['contains'] = array();
        }
        return $info;
    }
    
    /**
     *
     * @param type $name name of the token, e.g. "template"
     * @param type $type the type - open/close/single
     * @param type $props attributes token
     */
    public function __construct($name, $type, $props) {
        $this->name = $name;
        $this->type = $type;
        $this->props = $props;
    }
    
    public function add_child(fx_template_token $token) {
        if (!isset($this->children)) {
            $this->children = array();
        }
        $this->children []= $token;
    }
    
    public function add_children(array $children) {
        foreach ($children as $child) {
            $this->add_child($child);
        }
    }


    public function clear_children() {
        $this->children = array();
    }
    
    public function get_children() {
        return isset($this->children) ? $this->children : array();
    }
    
    public function has_children() {
        return isset($this->children) && count($this->children) > 0;
    }
    
    public function set_child($child, $index) {
        if ($child === null) {
            unset($this->children[$index]);
        } else {
            $this->children[$index] = $child;
        }
    }
    
    public function set_children($children){
        $this->children = $children;
    }


    public function set_prop($name, $value) {
        if ($value === null){
            unset($this->props[$name]);
        } else {
            $this->props[$name] = $value;
        }
    }
    
    public function get_prop($name) {
        return isset($this->props[$name]) ? $this->props[$name] : null;
    }
    
    public function get_all_props() {
        return $this->props;
    }
    
    public function show() {
        $r = '['.($this->type == 'close' ? 
                '/' : 
                ($this->type == 'unknown' ? '?' : '')).$this->name.' ';
        foreach ($this->props as $pk => $pv) {
            $r .= $pk.'="'.$pv.'" ';
        }
        $r .= ']';
        return $r;
    }
}

class fx_template_token_att_parser extends fx_template_fsm {
    public static function get_atts(&$source) {
        $p = new self();
        $res = $p->parse($source);
        $source = !empty($p->modifiers) ? ' | '.$p->modifiers : '';
        ///fx::debug($res);
        return $res;
    }
    
    public $split_regexp = '~((?<!\\\["\'])|\s+|[a-z0-9_-]+\=|\|)~s';
    
    const INIT = 1;
    const ATT_NAME = 2;
    const ATT_VAL = 3;
    const MODIFIERS = 4;
    
    public $modifiers = '';
    
    public function __construct() {
        //$this->debug = true;
        $this->init_state = self::INIT;
        $this->add_rule(self::INIT, '~.+=$~', self::ATT_NAME, 'read_att_name');
        $this->add_rule(self::ATT_NAME, array('"', "'"), self::ATT_VAL, 'start_att_val');
        $this->add_rule(self::ATT_VAL, array('"', "'"), self::INIT, 'end_att_val');
        $this->add_rule(self::INIT, '|', self::MODIFIERS);
        //$this->add_rule($first_state, $char, $new_state);
    }
    
    public function read_att_name($ch) {
        $this->c_att = array(
            'name' => trim($ch, '='),
            'value' => ''
        );
    }
    
    public function start_att_val($ch) {
        $this->c_att['quot'] = $ch;
    }
    
    public function end_att_val($ch) {
        if ($ch !== $this->c_att['quot']) {
            return false;
        }
        $this->res [$this->c_att['name']] = $this->c_att['value'];
    }
            
    public function default_callback($ch) {
        switch($this->state) {
            case self::ATT_VAL:
                $this->c_att['value'] .= $ch;
                break;
            case self::ATT_NAME:
                $this->c_att['value'] = $ch;
                $this->c_att['quot'] = '';
                $this->end_att_val('');
                $this->set_state(self::INIT);
                break;
            case self::MODIFIERS:
                $this->modifiers .= $ch;
                break;
        }
    }
    /*
    public function parse($s) {
        $res = parent::parse($s);
        fx::debug($res);
    }*/
}
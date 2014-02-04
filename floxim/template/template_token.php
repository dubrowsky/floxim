<?php
/*
 * Класс для отдельного токена fx-шаблонизатора
 */
class fx_template_token {
    public $name = null;
    public $type = null;
    public $props = array();
    
    
    /**
     * создать токен из исходника
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
        if ($name == 'if' && $type == 'open' && !preg_match('~test=~', $source)) {
            $props['test'] = $source;
        } elseif ($name == 'call' && $type != 'close' && !preg_match('~id=~', $source)) {
            $props['id'] = trim($source);
        } elseif ($name == 'each' && $type != 'close' && !preg_match ('~select=~', $source)) {
            //fx::debug('eeahc', $source);
            $props['select'] = trim($source);
        } else {
            // добавляем отсутствующие кавычки атрибутов
            // пока убрал, ломается случай {if test="$x == 1"}
            //$source = preg_replace("~([a-z0-9\:_-]+)\s*?=\s*?([^\'\\\"\s]+)~", ' $1="$2"', $source);
            $source = preg_replace_callback(
                '~([a-z0-9\:_-]+)=(["\'])(.+?)(?<!\\\\)\2~',
                function ($matches) use (&$props) {
                    $props[$matches[1]] = str_replace('\"', '"', $matches[3]);
                    return '';
                },
                $source
            );
            if ($name == 'var' && preg_match("~^\s*\|~", $source)) {
                $props['modifiers'] = self::get_var_modifiers($source);
            }
        }
        return new fx_template_token($name, $type, $props);   
    }
    
    public static function get_var_modifiers($source) {
        $source = preg_replace("~^\s*\|~", '', $source);
        $source = trim($source);
        $source .= '|';
        $source = preg_split("~(\s*\|\s*|\s*(?<!:):(?!:)\s*|\s*\,\s*|[\'\"])~", $source, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if (count($source) == 0) {
            return null;
        }
        $modifiers = array();
        $c_modifier = array();
        $c_state = 'default';
        $c_arg_quote = '';
        $c_arg = '';
        foreach ($source as $chunk) {
            $is_separator = preg_match("~^\s*\|\s*$~", $chunk);
            $is_arg_separator = preg_match("~^\s*[\,\:]\s*$~", $chunk);
            if ($c_state == 'default') {
                // нормальное название модификатора
                if (preg_match("~^[a-z0-9_:\.\$-]+$~i", $chunk)) {
                    $c_modifier['name'] = $chunk;
                    $c_state = 'modifier';
                    $c_arg_quote = '';
                    continue;
                }
                // вместо модификатора - аргумент в кавычках
                if ($chunk == '"' || $chunk = "'") {
                    $c_modifier['name'] = 'fx_default';
                    $c_state = 'arg';
                    $c_arg_quote = $chunk;  
                    $c_arg = $chunk;
                    continue;
                }
            }
            if ($c_state == 'arg') {
                if ( $c_arg_quote == '' && ($chunk == '"' || $chunk == "'")) {
                    $c_arg_quote = $chunk;
                    $c_arg .= $chunk;
                    continue;
                }
                // закрылась кавычка аргумента
                if ( $c_arg_quote != '' && $chunk == $c_arg_quote ) {
                    $c_arg .= $chunk;
                    $c_arg_quote = '';
                    continue;
                }
                // начался новый аргумент
                if ($c_arg_quote == '' && $is_arg_separator) {
                    $c_modifier['args'][]= $c_arg;
                    $c_arg = '';
                    continue;
                }
                // конец аргумента и модификатора
                if ($c_arg_quote == '' && $is_separator) {
                    $c_state = 'default';
                    $c_modifier['args'] []= $c_arg;
                    $modifiers []= $c_modifier;
                    $c_modifier = array('args' => array());
                    $c_arg = '';
                    continue;
                }
                $c_arg .= $chunk;
                continue;
            }
            // конец модификатора
            if ($c_state == 'modifier' && $is_separator) {
                $c_state = 'default';
                $modifiers []= $c_modifier;
                $c_modifier = array('args' => array());
                continue;
            }
            if ($c_state == 'modifier' && $is_arg_separator) {
                $c_state = 'arg';
                $c_arg = '';
                continue;
            }

        }
        
        if (count($modifiers) == 0) {
            return null;
        }
        $res = array();
        foreach ($modifiers as $modifier) {
            $c_mod = array($modifier['name']);
            if (isset($modifier['args'])) {
                $c_mod = array_merge($c_mod, $modifier['args']);
            }
            $res[]= $c_mod;
        }
        return $res;
    }
    
    protected static $_token_types = array(
        'template' => array(
            'type' => 'double',
            'contains' => array('code', 'template', 'area', 'var', 'call', 'each','if')
        ),
        'code' => array(
            'type' => 'single'
        ),
        'area' => array(
            'type' => 'both',
            'contains' => array('code', 'template', 'var')
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
            'type' => 'both',
            'contains' => array('code', 'template', 'area', 'var', 'call', 'each', 'if')
        ),
        'if' => array(
            'type' => 'double',
            'contains' => array('code', 'template', 'area', 'var', 'call', 'each', 'elseif', 'else')
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
     * @param type $name название токена, e.g. "template"
     * @param type $type тип - open/close/single
     * @param type $props атрибуты токена
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


    public function set_prop($name, $value) {
        $this->props[$name] = $value;
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
?>
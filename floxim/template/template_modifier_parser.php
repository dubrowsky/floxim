<?php
class fx_template_modifier_parser extends fx_template_fsm {
    
    public $split_regexp = "~(\|+|(?<!:):(?!:)|\,|\\\\?[\'\"])~";
    
    const INIT = 1;
    const MODIFIER = 2;
    const PARAM = 3;
    const QSTRING = 4;
    const DQSTRING = 5;
    
    protected $res = array();
    protected $stack = '';
    
    public function __construct() {
        $this->debug = false;
        $this->init_state = self::INIT;
        $this->add_rule(array(self::INIT, self::MODIFIER, self::PARAM), "~^\|+~", false, 'start_modifier');
        $this->add_rule(array(self::MODIFIER, self::PARAM), ":", false, 'start_param');
        $this->add_rule(array(self::MODIFIER, self::PARAM), '~[\"\']~', false, 'start_string');
        $this->add_rule(array(self::QSTRING, self::DQSTRING), '~\\\?[\"\']~', false, 'end_string');
    }
    
    protected $c_mod = null;
    
    public function parse($s) {
        parent::parse($s);
        $this->_bubble();
        return $this->res;
    }
    
    protected function _bubble() {
        while ($this->state != self::INIT) {
            if ($this->state == self::PARAM) {
                $this->end_param();
            } elseif ($this->state == self::MODIFIER) {
                $this->end_modifier();
            }
            $this->pop_state();
        }
    }
    
    public function start_modifier($ch) {
        $this->_bubble();
        $this->push_state(self::MODIFIER);
        $this->c_mod = array(
            'name' => '',
            'is_each' => $ch == '||',
            'args' => array()
        );
    }
    
    public function end_modifier() {
        if ($this->stack != '') {
            $this->c_mod['name'] = $this->stack;
            $this->stack = '';
        }
        $m = $this->c_mod;
        
        $m['name'] = trim($m['name']);
        
        // if mod name looks like arg, use default modifier
        if (count($m['args']) == 0 && preg_match("~^[\'\"]~", $m['name'])) {
            $m['args'] = array($m['name']);
            $m['name'] = '';
        } elseif (preg_match("~\.~", $m['name'])) {
            $m['name'] = preg_replace("~^\.~", '', $m['name']);
            $m['is_template']  = true;
        }
        
        $this->res []= $m;
    }
    
    public function end_param() {
        $param = $this->stack;
        $this->c_mod['args'] []= trim($param);
        $this->stack = '';
    }
    
    public function start_param($ch) {
        if ($this->state == self::MODIFIER) {
            $this->c_mod['name'] = $this->stack;
        } else {
            $this->pop_state();
            $this->end_param();
        }
        $this->stack = '';
        $this->push_state(self::PARAM);
    }
    
    public function start_string($ch) {
        $this->push_state($ch == '"' ? self::DQSTRING : self::QSTRING);
        $this->stack .= $ch;
    }
    
    public function end_string($ch) {
        if (
            ($this->state == self::DQSTRING && ($ch == "'" || $ch == '\"')) ||
            ($this->state == self::QSTRING && ($ch == '"' || $ch == "\'"))
        ) {
                return false;
        }
        $this->pop_state();
        $this->stack .= $ch;
    }
    
    public function default_callback($ch) {
        $this->stack .= $ch;
    }
}

/*
 * public static function get_var_modifiers($source_str) {
        $p = new fx_template_modifiers_parser();
        $res = $p->parse($source_str);
        return $res;
        $source_str = preg_replace("~^\s*~", '', $source_str);
        $source_str = trim($source_str);
        $source_str .= '|';
        $source = preg_split("~(\s*\|+\s*|\s*(?<!:):(?!:)\s*|\s*\,\s*|[\'\"])~", $source_str, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if (count($source) == 0) {
            return null;
        }
        fx::debug($source, $source_str);
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
 
 */
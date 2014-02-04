<?php
require_once (dirname(__FILE__).'/template_fsm.php');
class fx_template_expression_parser extends fx_template_fsm {
    
    public $split_regexp = '~(\$\{|\$|\s+|[\[\]]|[\'\"]|[\{\}]|[+=&\|\)\(-])~';
    
    const CODE = 1;
    const VAR_NAME = 2;
    const ARR_INDEX = 3;
    const STR = 4;
    
    const T_CODE = 1;
    const T_VAR = 2;
    const T_ARR = 3;
    const T_ROOT = 0;
    
    public function __construct() {
        $this->add_rule(array(self::CODE, self::ARR_INDEX, self::VAR_NAME), '~^\$~', null, 'start_var');
        $this->add_rule(self::VAR_NAME, '[', null, 'start_arr');
        $this->add_rule(
            self::VAR_NAME, 
            "~^[^a-z0-9_]~i",
            null, 
            'end_var'
        );
        $this->add_rule(self::ARR_INDEX, ']', null, 'end_arr');
        $this->init_state = self::CODE;
    }
    
    public $stack = array();
    public $curr_node = null;
    public function push_stack($node) {
        $this->stack[]= $node;
        $this->curr_node = $node;
    }
    public function pop_stack() {
        $node = array_pop($this->stack);
        $this->curr_node = end($this->stack);//$node;
        return $node;
    }
    
    public function parse($string) {
        $this->root = self::node(self::T_ROOT);
        $this->push_stack($this->root);
        parent::parse($string);
        return $this->root;
    }
    
    public function start_arr($ch) {
        $arr = self::node(self::T_ARR);
        $this->curr_node->add_child($arr);
        $this->push_stack($arr);
        $this->push_state(self::ARR_INDEX);
    }
    
    public function start_var($ch) {
        $var = self::node(self::T_VAR);
        $var->name = array();
        if ($this->curr_node->type == self::T_VAR) {
            $this->curr_node->name []= $var;
        } else {
            $this->curr_node->add_child($var);
        }
        $this->push_stack($var);
        $this->push_state(self::VAR_NAME);
    }
    
    public function end_var($ch) {
        do {
            $this->pop_state();
            $this->pop_stack();
        } while ($this->state == self::VAR_NAME);
        if ($ch == ']') {
            $this->end_arr();
        } else {
            $this->read_code($ch);
        }
    }
    
    public function end_arr() {
        $this->pop_stack();
        $this->pop_state();
    }
    
    public function default_callback($ch) {
        switch ($this->state){
            case self::VAR_NAME:
                $this->curr_node->name []= $ch;
                break;
            case self::CODE: case self::ARR_INDEX:
                $this->read_code($ch);
                break;
        }
    }
    
    public function read_code($ch) {
        $node = $this->curr_node;
        
        if ($node->last_child && $node->last_child->type == self::T_CODE) {
            $node->last_child->data .= $ch;
        } else {
            $code = self::node(self::T_CODE);
            $code->data = $ch;
            $node->add_child($code);
        }
    }
    
    
    public static function node($type) {
        return new fx_template_expression_node($type);
    }
    
    public function compile($node) {
        $res = '';
        $proc = $this;
        $add_children = function($n) use (&$res, $proc) {
            if (isset($n->children)) {
                foreach ($n->children as $child) {
                    $res .= $proc->compile($child);
                }
            }
        };
        switch($node->type) {
            case self::T_ROOT:
                $add_children($node);
                break;
            case self::T_VAR:
                $var = '(isset(';
                //$var = '$this->v(';
                $var_name = '';
                foreach ($node->name as $np) {
                    if (is_string($np)) {
                        $var_name .= '"'.$np.'"';
                    } else {
                        $var_name .= '.'.$this->compile($np);
                    }
                }
                
                if (preg_match('~^\"[a-z0-9_]+\"$~', $var_name)) {
                    $plain_name = '$'.preg_replace('~\"~', '', $var_name);
                } else {
                    $plain_name = '${'.$var_name.'}';
                }
                $var = ' (isset('.$plain_name.') ? '.$plain_name .': $this->v('.$var_name.') )';
                
                if ($node->last_child) {
                    $res .= "fx::dig(".$var.", ";
                    $indexes = array();
                    foreach ($node->children as $arr_index) {
                        $indexes []= $this->compile($arr_index);
                    }
                    $res .= join(", ", $indexes);
                    $res .= ")";
                } else {
                    $res .= $var;
                }
                break;
            case self::T_CODE:
                $res .= $node->data;
                break;
            case self::T_ARR:
                $res .= $add_children($node);
                break;
        }
        return $res;
    }
}

class fx_template_expression_node {
    public $type;
    public function __construct($type = fx_template_expression_parser::T_CODE) {
        $this->type = $type;
    }
    public $last_child = null;
    //public $children = array();
    public function add_child($n) {
        if (!$this->last_child) {
            $this->children = array();
        }
        $this->children []= $n;
        $this->last_child = $n;
    }
}
/*
require_once '../../boot.php';
fx::debug('start');

$ep = new fx_template_expression_parser();
//$str = '$arr["xx_".$b]["yy"] > strlen($pic[3], $gic_$id)';
$str = '$image';
$tree = $ep->parse($str);
fx::debug($str, $tree, $ep->compile($tree));
foreach (range(0, 1000) as $n) {
    $tree = $ep->parse($str);
    $res = $ep->compile($tree);
}
fx::debug($res);
die();
 * 
 */
<?php
require_once (dirname(__FILE__).'/template_fsm.php');
class fx_template_expression_parser extends fx_template_fsm {
    
    public $split_regexp = '~(\$\{|\`|\$|\s+|\.|\,|[\[\]]|[\'\"]|[\{\}]|[+=&\|\)\(-])~';
    
    const CODE = 1;
    const VAR_NAME = 2;
    const ARR_INDEX = 3;
    const STR = 4;
    const ESC = 5;
    
    const T_CODE = 1;
    const T_VAR = 2;
    const T_ARR = 3;
    const T_ROOT = 0;
    
    public function __construct() {
        $this->add_rule(self::CODE, '`', null, 'start_esc');
        $this->add_rule(self::ESC, '`', null, 'end_esc');
        $this->add_rule(array(self::CODE, self::ARR_INDEX, self::VAR_NAME), '~^\$~', null, 'start_var');
        $this->add_rule(array(self::VAR_NAME, self::ARR_INDEX), array('[', '.'), null, 'start_arr');
        $this->add_rule(
            self::VAR_NAME, 
            "~^[^a-z0-9_]~i",
            null, 
            'end_var'
        );
        $this->add_rule(self::ARR_INDEX, "~^[^a-z0-9_\.]~", null, 'end_var_dot');
        $this->add_rule(self::ARR_INDEX, ']', null, 'end_arr');
        $this->init_state = self::CODE;
    }
    
    public $stack = array();
    public $curr_node = null;
    
    
    public function start_esc($ch) {
        $this->push_state(self::ESC);
    }
    
    public function end_esc($ch) {
        $this->pop_state();
    }
    
    public function push_stack($node) {
        $this->stack[]= $node;
        $this->curr_node = $node;
    }
    
    public function pop_stack() {
        $node = array_pop($this->stack);
        $this->curr_node = end($this->stack);
        return $node;
    }
    
    public function parse($string) {
        $this->root = self::node(self::T_ROOT);
        $this->push_stack($this->root);
        parent::parse($string);
        return $this->root;
    }
    
    public function start_arr($ch) {
        // $item["olo".$id] - ignore dot
        if ($ch == '.' && $this->state == self::ARR_INDEX && $this->curr_node->starter != '.') {
            return false;
        }
        if (
                ($ch == '.' && $this->state == self::ARR_INDEX) ||
                $this->curr_node->starter == '.'
            ) {
            $this->end_arr();
        }
        
        $arr = self::node(self::T_ARR);
        $arr->starter = $ch;
        $this->curr_node->add_child($arr);
        $this->push_stack($arr);
        $this->push_state(self::ARR_INDEX);
    }
    
    public function end_var_dot($ch) {
        if ($this->curr_node->starter != '.') {
            return false;
        }
        $this->end_var($ch);
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
        switch ($this->state) {
            case self::VAR_NAME:
                $this->curr_node->name []= $ch;
                break;
            case self::CODE: case self::ESC:
                $this->read_code($ch);
                break;
            case self::ARR_INDEX:
                if ($this->curr_node->starter == '.' && preg_match("~^[a-z0-9_]+$~i", $ch)) {
                    $ch = '"'.$ch.'"';
                }
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
    
    public $local_vars = array('this');
    
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
                $is_local = false;
                $var_name = '';
                // simple var
                if (count($node->name) == 1 && is_string($node->name[0])) {
                    $var_name = $node->name[0];
                    if (in_array($var_name, $this->local_vars)) {
                        $is_local = true;
                        $var = '$'.$var_name;
                    } else {
                        $var = '$this->v("'.$var_name.'")';
                    }
                } 
                // complex var such as $image_$id
                else {
                    foreach ($node->name as $np) {
                        $var_name .= is_string($np) ? '"'.$np.'"' : '.'.$this->compile($np);
                    }
                    $var = '$this->v('.$var_name.')';
                }
                
                if ($node->last_child) {
                    $indexes = array();
                    foreach ($node->children as $arr_index) {
                        $indexes []= $this->compile($arr_index);
                    }
                    if ($is_local) {
                        $res .= $var.'['.join('][', $indexes).']';
                    } else {
                        $res .= "fx::dig(".$var.", ";
                        $res .= join(", ", $indexes);
                        $res .= ")";
                    }
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
    
    public function pop_child() {
        if (!$this->last_child) {
            return null;
        }
        $child = array_pop($this->children);
        if (count($this->children) == 0) {
            $this->last_child = null;
        } else {
            $this->last_child = end($this->children);
        }
        return $child;
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
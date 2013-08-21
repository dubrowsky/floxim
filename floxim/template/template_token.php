<?php
class fx_template_token {
    public $name = null;
    public $type = null;
    public $props = array();
    
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
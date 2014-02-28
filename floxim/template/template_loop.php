<?php
class fx_template_loop implements ArrayAccess {
    
    public $loop;
    
    public function __construct($items, $key = 'key', $alias = 'item') {
        $this->loop = $this;
        $this->items = $items;
        $this->total = count($items);
        $this->position = 0;
        $this->current_key = $key;
        $this->current_alias = $alias;
        $this->current = null;
    }
    
    public function _move() {
        $this->position++;
        if ($this->current === null) {
            $this->current = current($this->items);
        } else {
            $this->current = next($this->items);
        }
        $this->key = key($this->items);
    }
    
    public function is_last() {
        return $this->position == $this->total;
    }
    
    public function is_first() {
        return $this->position == 1;
    }
    
    public function is_even() {
        return $this->position % 2 == 0;
    }
    
    public function is_odd() {
        return $this->position % 2 != 0;
    }
    
    public function offsetGet($offset) {
        if (isset($this->$offset)) {
            return $this->$offset;
        }
        if (method_exists($this, $offset)) {
            return $this->$offset();
        }
    }
    public function offsetSet($offset, $value) {
        ;
    }
    public function offsetExists($offset) {
        return isset($this->$offset) || method_exists($this, $offset);
    }
    public function offsetUnset($offset) {
        ;
    }
}
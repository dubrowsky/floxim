<?php

class fx_query_param implements ArrayAccess {

    protected $data = array();

    public function offsetGet($offset) {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value) {
        $this->data[$offset] = $value;
    }

    public function offsetExists($offset) {
        return key_exists($offset, $this->data);
    }

    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }

}

abstract class fx_tpl {

    protected $vars;
    protected $m = array();

    public function __construct() {
        $this->vars['query_param'] = new fx_query_param();
    }

    public function set_vars($name, $value = '') {
        if (is_array($name)) {
            if ($name)
                    foreach ($name as $k => $v) {
                    $this->vars[$k] = $v;
                }
        } else {
            $this->vars[$name] = $value;
        }

        return $this;
    }

    public function get_vars($name = null) {

        $vars = $this->vars ? $this->vars : array();
        $m = $this->m ? $this->m : array();
        if ($name) {
            return key_exists($name, $vars) ? $vars[$name] : $m[$name];
        }

        return array_merge($m, $vars);
    }

}

?>

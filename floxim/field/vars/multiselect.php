<?php

class fx_field_vars_multiselect  {
    protected $all_values, $values;
    protected $elements = array();
    
    protected $to_string = null;
    
    public function __construct( $all_values = array(), $values = array()) {
       $this->all_values = is_array($all_values) ? $all_values : array();
       $this->values = is_array($values) ? $values : array();
       
       foreach ( $values as $id ) {
           $this->elements[$id] = $all_values[$id];
       }
    }
    
    public function to_str ( $divider = ', ') {
        return join($divider, $this->elements);
    }
    
    public function to_array(){
        return $this->elements;
    }
    
    public function ids(){
        return array_keys($this->elements);
    }
    
    public function has_id ( $id ) {
        return in_array($id, $this->ids());
    }
    
    public function __toString() {
        return $this->to_string ? $this->to_string : $this->to_str();
    }
    
    public function set_to_str_value ( $value ) {
        $this->to_string = $value;
    }
}

?>

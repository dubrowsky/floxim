<?php

class fx_field_int extends fx_field_baze {

    public function validate_value($value) {
        if (!parent::validate_value($value)) {
            return false;
        }
        if ($value && ($value != strval(intval($value)))) {
            $this->error = sprintf(FX_FRONT_FIELD_INT_ENTER_INTEGER, $this->get_description());
            return false;
        }
        return true;
    }

    public function get_sql_type (){
        return "INT";
    }
    
    public function get_search_cond ( $cond ) {
        $result = array();
        if ( isset($cond['min']) ) {
            $result[] = " a.`".$this->name."` >= '".intval($cond['min'])."' ";
        }
        if ( isset($cond['max']) ) {
            $result[] = " a.`".$this->name."` <= '".intval($cond['max'])."' ";
        }
        if ( isset($cond['eq']) ) {
            $result[] = " a.`".$this->name."` = '".intval($cond['eq'])."' ";
        }
        
        return $result ? join('AND', $result) : false;
    }
}

?>
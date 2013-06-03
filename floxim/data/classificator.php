<?php

class fx_data_classificator extends fx_data {
    public function get_by_table ( $table ) {
        $classname = $this->get_class_name( $res );
        $obj = new $classname ( array( 'data' => array(), 'finder' => $this, 'table' => $table) );
        
        return $obj;
    }
}

?>

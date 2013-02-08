<?php

class fx_data_classificator_item extends fx_data {
  public function  __construct() {
    parent::__construct();
    $this->serialized[] = 'prestate';
    $this->serialized[] = 'poststate';
    $this->serialized[] = 'essence_id';
  }
  
  public function set_table ( $table ) {
      $this->table = 'classificator_'.fx_core::get_object()->db->escape( $table );
      return $this;
  }
}

?>

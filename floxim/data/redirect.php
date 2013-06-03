<?php

class fx_data_redirect extends fx_data {

    public function __construct() {
        parent::__construct();
        $this->order = 'priority';
    }

    public function create($data = array()) {
        $classname = $this->get_class_name($data);
        $obj = new $classname(array('data' => $data, 'finder' => $this));
        $obj['priority'] = $this->next_priority();
        return $obj;
    }

}

?>

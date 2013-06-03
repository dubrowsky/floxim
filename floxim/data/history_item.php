<?php

class fx_data_history_item extends fx_data {

    public function __construct() {
        parent::__construct();
        $this->serialized[] = 'prestate';
        $this->serialized[] = 'poststate';
        $this->serialized[] = 'essence_id';
    }

}
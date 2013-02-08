<?php

class fx_data_rights extends fx_data {
    public function __construct() {
        parent::__construct();
        $this->table = 'permission';
    }
}

?>

<?php

class fx_data_menu extends fx_data {

    public function __construct() {
        parent::__construct();
        $this->serialized = array('settings');
        $this->classname = 'fx_menubaze';
    }

}

?>

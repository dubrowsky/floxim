<?php

class fx_data_ctpl extends fx_data {

    public function __construct() {
        $this->serialized = array('action', 'sort', 'access');
        parent::__construct();
    }

    public function get_by_component($component_id) {
        if ( $component_id instanceof fx_component ) {
            $component_id = $component_id['id'];
        }
        return $this->get_all('component_id', $component_id);
    }

}

?>

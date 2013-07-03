<?php

class fx_data_history extends fx_data {

    public function get_last() {
        $this->order = "id DESC";

        return $this->get_all('marker', 0);
    }

    public function get_next() {
        $this->order = "id ASC";
        return $this->get_all('marker', 1);
    }

}

?>
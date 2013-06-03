<?php

class fx_data_template extends fx_data {

    public function __construct() {
        $this->serialized[] = 'colors';
        $this->serialized[] = 'files';
        parent::__construct();
    }

    public function get_by_keyword($keyword) {
        return $this->get_all('keyword', $keyword);
    }

    public function get_parents() {
        return $this->get_all('parent_id', 0);
    }

    public function get_all_store_ids() {
        $result = fx_core::get_object()->db->get_col("SELECT `store_id` FROM `{{template}}` WHERE `store_id` IS NOT NULL");
        if (!$result) $result = array();

        return $result;
    }

}

?>

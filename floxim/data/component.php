<?php

class fx_data_component extends fx_data {

    public function __construct() {
        $this->order = '`group`, `id`';
        parent::__construct();
    }

    public function get_all_groups() {
        $result = array();
        $groups = fx_core::get_object()->db->get_col("SELECT DISTINCT `group` FROM `{{component}}` ORDER BY `group`");
        if ($groups)
                foreach ($groups as $v) {
                $result[$v] = $v;
            }

        return $result;
    }

    public function get_all_store_ids() {
        $result = fx_core::get_object()->db->get_col("SELECT `store_id` FROM `{{component}}` WHERE `store_id` IS NOT NULL");
        if (!$result) $result = array();

        return $result;
    }
    
    public function get_by_id($id) {
        if (!is_numeric($id)) {
            $component = $this->get_by_keyword($id);
        } else {
            $component = parent::get_by_id($id);
        }
        return $component;
    }
    
    public function get_by_keyword($keyword) {
    	return $this->get('keyword', $keyword);
    }
}
?>
<?php

class fx_data_widget extends fx_data {

    public function get_all_groups () {
        $result = array();
        $groups = fx::db()->get_col("SELECT DISTINCT `group` FROM `{{widget}}` ORDER BY `group`");
        if ( $groups ) foreach ( $groups as $v ) {
            $result[$v] = $v;
        }
        
        return $result;
    }
    
    public function get_by_id($id) {
        return $this->where(is_numeric($id) ? 'id' : 'keyword', $id)->one();
    }

}

?>
<?php

class fx_data_widget extends fx_data {

    public function get_all_groups () {
        $result = array();
        $groups = fx_core::get_object()->db->get_col("SELECT DISTINCT `group` FROM `{{widget}}` ORDER BY `group`");
        if ( $groups ) foreach ( $groups as $v ) {
            $result[$v] = $v;
        }
        
        return $result;
    }

}

?>

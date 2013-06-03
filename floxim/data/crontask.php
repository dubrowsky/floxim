<?php

class fx_data_crontask extends fx_data {
    public function get_actual () {
        $where = " checked = 1 AND ( every_days OR every_hours OR every_minutes )";
        $where .= " AND ( last_launch + every_days*86400 + every_hours*3600 + every_minutes*60 ) < unix_timestamp()";
        
        return $this->get_all($where);
    }
}

?>

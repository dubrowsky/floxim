<?php

class fx_infoblock_content_user extends fx_infoblock_content {

    protected function _get_base_select_field() {
        return array('a.`id`', 'a.`site_id`', 'a.`checked`', 'a.`created`',
                'a.`last_updated`', 'a.`keyword`', 'a.`login`',
                'CONCAT( \''.fx::config()->SUB_FOLDER.'\', sub.`hidden_url`) AS `hidden_url`',
                '`infoblock`.`id` AS `infoblock_id`', '`infoblock`.`url` AS `url`');
    }

    protected function get_main_table() {
        return 'user';
    }

    protected function _make_system_query_join() {
        $this->sql['join'] = "LEFT JOIN `{{infoblock}}` AS `infoblock` ON `infoblock`.`id` = ".$this['id']."
        LEFT JOIN `{{subdivision}}` AS `sub` ON `sub`.`id` = `infoblock`.`subdivision_id`";
        if ($this->sql_param['query_join']) {
            $this->sql['join'] .= " ".$this->sql_param['query_join'];
        }
    }

    protected function _make_system_query_where($func_param) {
        $fx_core = fx_core::get_object();

        $ignore_check = ($fx_core->is_admin_mode() || $this->sql_param['ignore_check']);

        $this->sql['where'] = '1';

        // показ явно заданных объектов
        if ($this->messages_id) {
            $this->sql['where'] .= " AND ".( is_array($this->messages_id) ? " a.`id` IN (".join(',', array_map('intval', $this->messages_id)).") " : "a.`id` = '".intval($this->messages_id)."' " );
        }

        $this->sql['where'] .= $ignore_check ? "" : " AND a.`checked` = 1";
        $this->sql['where'] .= $this->sql_param['query_where'] ? " AND ".$this->sql_param['query_where']." " : "";
    }

    protected function get_essence_obj ( $data ) {
        $fx_core = fx_core::get_object();
        return new fx_user(array('data' => $data, 'finder' => $fx_core->user) );
    }

}

?>

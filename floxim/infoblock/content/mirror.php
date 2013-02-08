<?php

class fx_infoblock_content_mirror extends fx_infoblock_content {

    public function show_index($func_param = '') {

        $content = parent::show_index($func_param);

        if ($this->is_manual_content_selection() && $this->func_param['block_hash']) {
            $add_data = array();
            $add_data['mode'] = $func_param['main_content'] ? 'edit' : 'design';
            $add_data['post'] = array('choose_content' => 1, 'id' => $this['id'], 'essence' => 'infoblock');
            $add_data['name'] = "Новый объект";
            $add_data['preview'] = '<div>Новый объект</div>';
            $add_data['preview_parent'] = '.'.$this->func_param['block_hash'];
            $add_data['decent_parent'] = $this->func_param['block_hash'];
            fx_core::get_object()->page->addition_block($add_data);
        }

        return $content;
    }

    protected function _make_system_query_where($func_param) {
        $fx_core = fx_core::get_object();

        $ignore_check = ($fx_core->is_admin_mode() || $this->sql_param['ignore_check']);
        $by_user_id = $func_param ['by_user_id'] ? $func_param ['by_user_id'] : ( $this->sql_param['by_user_id'] ? $this->sql_param['by_user_id'] : 0);
        $by_user_id = intval($by_user_id);

        $this->sql['where'] = '1';

        if ($this->is_manual_content_selection()) {
            $this->sql['where'] .= " AND ".$this->_get_in_statement('id', $this['content_selection']['content']);
        } else {
            if ($this['source']['type'] == 'select') {
                $this->sql['where'] .= " AND ".$this->_get_in_statement('infoblock_id', $this['source']['infoblocks']);
            }
        }

        $this->sql['where'] .= $ignore_check ? "" : " AND a.`checked` = 1";
        $this->sql['where'] .= $by_user_id ? " AND a.`user_id` = '".$by_user_id."'" : "";
        $this->sql['where'] .= $this->sql_param['query_where'] ? " AND ".$this->sql_param['query_where']." " : "";
    }

    protected function _get_in_statement($field, $values) {
        if (!is_array($values)) {
            $values = array(0);
        }
        return "a.`$field` IN (".join(',', array_map('intval', $values)).") ";
    }

    protected function get_object_hash($content) {
        $fx_core = fx_core::get_object();
        $post_data = array('essence' => 'content', 'id' => $content->get_component_id()."-".$content['id'], 'infoblock_id' => $this['id'] );
        $mode = $this->func_param['main_content'] ? 'edit' : 'design';
        $hash_obj = $fx_core->page->add_block($post_data, 'delete', $mode, $this->func_param['block_hash'], $content['checked']);

        return $hash_obj;
    }
    
    public function delete_content ( $ids ) {
        if ( !is_array($ids) ) {
            $ids = array($ids);
        }
        
        $content_selection = $this['content_selection'];
        $content = $content_selection['content'];
        if ( !$content  ) {
        	$content  = array();
        }
        $content_selection['content'] = array_diff($content , $ids);
        $this->set('content_selection', $content_selection)->save();
    }

}

?>
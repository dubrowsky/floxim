<?php

class fx_infoblock_content_block extends fx_infoblock_content {

    public function show_index($func_param = '') {
        $fx_core = fx_core::get_object();

        $content = parent::show_index($func_param);

        if ($this->ctpl['with_list'] && !$this['parent_id'] && $this->func_param['block_hash']) {
            $add_data = array();
            $add_data['mode'] = 'edit';
            $add_data['post'] = array('essence' => 'content', 'fx_infoblock' => $this['id']);
            $add_data['name'] = $this['name'];
            $add_data['key'] = $this->func_param['block_hash'];
            $fx_core->page->addition_block($add_data);
        }

        if ($this->ctpl['with_list'] && $this['parent_id'] ) {
            $add_data = array();
            $add_data['mode'] = 'edit';
            $add_data['post'] = array('essence' => 'content', 'fx_infoblock' => $this['id'], 'parent_id' => $this->func_param['parent_id']);
            $add_data['name'] = $this['name'] ? $this['name'] : $this->component['name'];
            $add_data['key'] = $this->func_param['block_hash'];
            if ($fx_core->env->get_action() != 'full') {
                $add_data['parent_key'] = $this->func_param['parent_block_hash'];
            }

            $fx_core->page->addition_block($add_data);
        }
        //dev_log($content, $this);

        return $content;
    }
    
    protected function get_object_hash($content) {
        $fx_core = fx_core::get_object();
        if ($this->ctpl['with_list']) {
            $hash_obj = $fx_core->page->add_block('essence=content&id='.$this->component_id.'-'.$content['id'], 'edit,on,off,delete,select_block', 'edit', $this->func_param['block_hash'], $content['checked']);
            $fx_core->page->update_block($this->func_param['block_hash'], 'hidden=1');
        } else {
            $data = array();
            $data['buttons'] = array('edit', 'on', 'off', 'delete', 'settings');
            $fx_core->page->update_block($this->func_param['block_hash'], $data);
            $hash_obj = $this->func_param['block_hash'];
        }

        return $hash_obj;
    }

    
    protected function _before_delete() {
        $fx_core = fx_core::get_object();
        
        $content_items = $fx_core->content->get_all( $this['essence_id'], 'infoblock_id', $this['id']);
        foreach ( $content_items as $content ) {
            $content->delete();
        }
    }

}

?>

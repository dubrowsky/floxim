<?php

class fx_field_vars_infoblock  {
    protected $infoblock, $content_id, $field, $parent_hash;
    protected $init = false;
    protected $dependent_infoblock;
    protected $content = '', $count = 0;
    public function __construct( fx_infoblock_content $infoblock, $content_id, $field, $parent_hash) {
        $this->infoblock = $infoblock;
        $this->content_id = $content_id;
        $this->field = $field;
        $this->parent_hash = $parent_hash;
    }
    
    protected function get_ctpl() {
        $ctpl = $this->dependent_infoblock['list_ctpl_id'] ? $this->dependent_infoblock['list_ctpl_id'] : 0;
        if ($this->infoblock->get_current_action() == 'full' && $this->dependent_infoblock['full_ctpl_id']) {
            $ctpl = $this->dependent_infoblock['full_ctpl_id'];
        }
        return $ctpl;
    }
    
    protected function _init () {
        if ( $this->init ) return;
        
        $fx_core = fx_core::get_object();
        
         $this->dependent_infoblock = $fx_core->infoblock->get('parent_id', $this->infoblock['id'], 'field_id', $this->field['id']);
       
         if ( $this->dependent_infoblock ) {
            $ctpl = $this->get_ctpl();
                                   
            if ( $this->parent_hash ) {
                $hash = $fx_core->page->add_block('essence=infoblock&id=', 'settings,on,off,delete', 'edit', $hash_obj); 
                $edit_param = 'edit_mode=1&block_hash='.$hash.'&';     
            }
            
            $this->content = $this->dependent_infoblock->show_index($edit_param.'parent_id='.$this->content_id.'&ctpl='.$ctpl.'&parent_block_hash='.$this->parent_hash);
            $this->count = $this->dependent_infoblock->get_total_rows();

            if ( $this->parent_hash  ) {
                $this->content = '<div class="fx_page_block '.$hash.'">'.$this->content.'</div>';
            }
            
        }
        else if ( $this->parent_hash ) {
            $add_data = array();
            $add_data['mode'] = 'edit';
            $add_data['post'] = array('essence' => 'infoblock', 'parent_id' => $this->infoblock['id'], 'field_id' => $this->field['id'] );
            $add_data['post']['available_type'] = 'content';
            $add_data['name'] = $this->field->get_description();
            if ( $fx_core->env->get_action() != 'full' ) {
                $add_data['parent_key'] = $hash_obj;
            }
            if ( $this->field['format']['components_type'] == 'select' && $this->field['format']['components_id'] ) {
                $add_data['post']['available_components'] = $this['format']['components_id'];
            }
            fx_core::get_object()->page->addition_block($add_data);
            
            $this->content = '';
        }
        
    }
    public function show () {
        $this->_init();
        return $this->content;
    }
    
    public function count () {
        $this->_init();
        return $this->count;
    }
    
    public function __toString() {
        return $this->show();
    }
}
?>

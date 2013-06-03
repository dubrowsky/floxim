<?php

class fx_field_infoblock extends fx_field_baze {

    public function content_procces(fx_content $content, fx_infoblock_content $infoblock = null, $hash_obj = null) {
        $fx_core = fx_core::get_object();
        $name = $this->name;
        //$hash_obj = null;
        $result = array();
        
        $var = new fx_field_vars_infoblock($infoblock, $content['id'], $this, $hash_obj );
        
        $result = array();
        $result['f_'.$name.'_none'] = $var;
        $result['f_'.$name] = $var;
        
        return $result;
        
        $dependent_infoblock = fx::data('infoblock')->get('parent_id', $infoblock['id'], 'field_id', $this['id']);
       
         if ($dependent_infoblock) {
            $ctpl = $dependent_infoblock['list_ctpl_id'] ? $dependent_infoblock['list_ctpl_id'] : 0;
            if ($infoblock->get_current_action() == 'full' && $dependent_infoblock['full_ctpl_id']) {
                $ctpl = $dependent_infoblock['full_ctpl_id'];
            }
                                   
            if ( $hash_obj ) {
                $hash = $fx_core->page->add_block('essence=infoblock&id=', 'settings,on,off,delete', 'edit', $hash_obj); 
                $edit_param = 'edit_mode=1&block_hash='.$hash.'&';     
            }
            
            $result['f_'.$name] = $dependent_infoblock->show_index($edit_param.'parent_id='.$content['id'].'&ctpl='.$ctpl.'&parent_block_hash='.$hash_obj);
            $result['f_'.$name.'_count'] = $dependent_infoblock->get_total_rows();

            if ( $hash_obj ) {
                $result['f_'.$name] = '<div class="fx_page_block '.$hash.'">'.$result['f_'.$name].'</div>';
            }
            
        }
        else if ( $hash_obj ) {
            $add_data = array();
            $add_data['mode'] = 'edit';
            $add_data['post'] = array('essence' => 'infoblock', 'parent_id' => $infoblock['id'], 'field_id' => $this['id'] );
            $add_data['post']['available_type'] = 'content';
            $add_data['name'] = $this->get_description();
            if ( $fx_core->env->get_action() != 'full' ) {
                $add_data['parent_key'] = $hash_obj;
            }
            if ( $this['format']['components_type'] == 'select' && $this['format']['components_id'] ) {
                $add_data['post']['available_components'] = $this['format']['components_id'];
            }
            fx_core::get_object()->page->addition_block($add_data);
            
            $result['f_'.$name] = '';
        }


        return $result;
    }
    

    public function format_settings() {
        $components = array();
        foreach (fx::data('component')->get_all() as $v) {
            $components[$v['id']] = $v['name'];
        }
        $fields = array();
        $val = array('all' => 'Все', 'select' => 'Выбрать');

        $fields[] = array('id' => 'format[components_type]', 'name' => 'format[components_type]', 'label' => 'Компоненты', 'type' => 'radio', 'values' => $val, 'value' => 'all');
        $fields[] = array('name' => 'format[components_id]', 'type' => 'select', 'values' => $components, 'parent' => array('format[components_type]', 'select'), 'unactive' => true, 'multiple' => true);

        return $fields;
    }
    
      public function get_js_field($content, $tname = 'f_%name%', $layer = '', $tab = '') {
        $this->_js_field = array('id' => $name, 'name' => $name, 'label' => $this->description, 'type' => 'hidden');
        return $this->_js_field;
    }
    

}

?>

<?php
class fx_field_multilink extends fx_field_baze {
    public function get_sql_type() {
        return false;
    }
    
    public function get_js_field($content, $tname = 'f_%name%', $layer = '', $tab = '') {
        parent::get_js_field($content, $tname, $layer, $tab);
        $this->_js_field['type'] = 'livesearch';
        $this->_js_field['is_multiple'] = true;
        if (isset($content[$this['name']])) {
            $this->_js_field['value'] = array();
            foreach ($content[$this['name']] as $v) {
                $this->_js_field['value'] []= array(
                    'id' => $v['id'], 
                    'name' => $v['name'], 
                    'value_id' => $v['_linker_id']
                );
            }
        }
        return $this->_js_field;
        $this->_js_field['type'] = 'select';
        $this->_js_field['multiple'] = true;
        return $this->_js_field;
    }
    
    public function format_settings() {
        $fields = array();
        $linking_types = array();
        
        if (!$this['component_id']) {
            return $fields;
        }
        
        $com = fx::data('component', $this['component_id']);
        $chain = new fx_collection($com->get_chain());
        $chain_ids = $chain->get_values('id');
        $link_fields = fx::data('field')->where('type', 13)->where('component_id', 0, '!=')->all();
        
        foreach ($link_fields as $lf) {
            if (in_array($lf['format']['target'], $chain_ids)) {
                $linking_component = fx::data('component', $lf['component_id']);
                $linking_types[]= array(
                    $lf['id'], 
                    $linking_component['name'].' - '.$lf['name']
                );
                // Для связей MANY_MANY
                // получаем поля-ссылки компонента, указывающие на другие компоненты
                $linking_component_links = $linking_component->
                        all_fields()->
                        find('type', 13)->
                        find('id', $lf['id'], '!=');
                foreach ($linking_component_links as $linking_component_link) {
                    $target_component = fx::data('component', $linking_component_link['format']['target']);
                    $linking_types []= array(
                        $lf['id'].'.'.$linking_component_link['id'],
                        $target_component['name'].' - '.$linking_component['name'].' - '.$lf['name']
                    );
                }
            }
        }
        
        $fields[] = array(
            'id' => 'format[target]',
            'name' => 'format[target]',
            'label' => fx_lang('Связанный тип'),
            'type' => 'select',
            'values' => $linking_types,
            'value' => $this['format']['target']
        );
        return $fields;
    }
    
    public function set_value($value) {
        dev_log('setting multilink val', $value, $this);
        parent::set_value($value);
    }
    
    public function get_relation() {
        if (!$this['format']['target']) {
            return false;
        }
        $target_fields = explode(".", $this['format']['target']);
        $direct_target_field = fx::data('field', array_shift($target_fields));
        $direct_target_component = fx::data('component', $direct_target_field['component_id']);
        if (count($target_fields) == 0) {
            return array(
                fx_data::HAS_MANY,
                'content_'.$direct_target_component['keyword'],
                $direct_target_field['name']
            );
        }
        
        $next_target_field = fx::data('field', array_shift($target_fields));
        return array(
            fx_data::MANY_MANY,
            'content_'.$direct_target_component['keyword'],
            $direct_target_field['name'],
            $next_target_field->get_prop_name()
        );
    }
}
?>
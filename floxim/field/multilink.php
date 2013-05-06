<?php
class fx_field_multilink extends fx_field_baze {
    public function get_sql_type() {
        return false;
    }
    
    public function get_js_field($content, $tname = 'f_%name%', $layer = '', $tab = '') {
        parent::get_js_field($content, $tname, $layer, $tab);
        $this->_js_field['type'] = 'select';
        return $this->_js_field;
    }
    
    public function format_settings() {
        $fields = array();
        $linking_types = array();
        dev_log($this);
        
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
            'label' => 'Связанный тип',
            'type' => 'select',
            'values' => $linking_types,
            'value' => $this['format']['target']
        );
        return $fields;
    }
}
?>
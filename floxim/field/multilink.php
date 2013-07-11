<?php
class fx_field_multilink extends fx_field_baze {
    public function get_sql_type() {
        return false;
    }
    
    public function get_js_field($content, $tname = 'f_%name%', $layer = '', $tab = '') {
        parent::get_js_field($content, $tname, $layer, $tab);
        $render_type = $this['format']['render_type'];
        if ($render_type == 'livesearch') {
            $this->_js_field['type'] = 'livesearch';
            $this->_js_field['is_multiple'] = true;
            $this->_js_field['params'] = array(
                'content_type' => $this->get_end_data_type()
            );
            if (isset($content[$this['name']])) {
                $this->_js_field['value'] = array();
                $linker_ids = array_keys($content[$this['name']]->linker_map);
                foreach ($content[$this['name']] as $num => $v) {
                    $this->_js_field['value'] []= array(
                        'id' => $v['id'], 
                        'name' => $v['name'], 
                        'value_id' => $linker_ids[$num]
                    );
                }
            }
        } elseif ($render_type == 'table') {
            $rel = $this->get_relation();
            $essence = fx::data($rel[1])->create();
            $essence_fields = $essence->get_form_fields();
            $this->_js_field['tpl'] = array();
            $this->_js_field['labels'] = array();
            foreach ($essence_fields as $ef) {
                $this->_js_field['tpl'] []= $ef;
                $this->_js_field['labels'] []= $ef['label'];
            }
            dev_log($rel, $essence_fields);
            $this->_js_field['type'] = 'set';
            /*
                array('name' => 'id', 'type' => 'string'),
                array('name' => 'value', 'type' => 'string')
            );
            //'values' => $this['format']['values'] ? $this['format']['values'] : array(),
            $this->_js_field['labels'] = array('olo id', 'trolo value');
             * 
             */
        }
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
            'label' => fx::lang('Связанный тип','system'),
            'type' => 'select',
            'values' => $linking_types,
            'value' => $this['format']['target']
        );
        $fields[]= array(
            'id' => 'format[render_type]',
            'name' => 'format[render_type]',
            'label' => fx::lang('Render type', 'system'),
            'type' => 'select',
            'values' => array(
                'livesearch' => fx::lang('Live search','system'),
                'table' => fx::lang('Fields table','system')
            ),
            'value' => $this['format']['render_type']
        );
        return $fields;
    }
    
    public function set_value($value) {
        parent::set_value($value);
    }
    
    /*
     * Преобразует значение из формы в коллекцию
     * Кажется, пока заточен только под MANY_MANY
     */
    public function get_savestring($content) {
        // дергаем предыдущее значение,
        // чтобы заполнить его
        $content->get($this['name']);
        
        $end_data_type = $this->get_end_data_type();
        
        // айдишники объектов, которые будут привязаны
        $new_rel_ids = array();
        
        foreach ($this->value as $v) {
            // значение числовое, значит объект уже есть
            if (is_numeric($v)) {
                $new_rel_ids []= $v;
            } else {
                // тут надо создать новый объект
            }
        }
        $new_value = fx::data($end_data_type, $new_rel_ids);
        return $new_value;
    }
    
    public function get_end_data_type() {
        // связь, генерируемая этим полем
        $relation = $this->get_relation();
        $related_relation = fx::data($relation[1])->relations();
        $end_data_type = $related_relation[$relation[3]][1];
        return $end_data_type;
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
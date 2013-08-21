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
            $rel = $this->get_relation();
            $related_relation = fx::data($rel[1])->relations();
            $linker_field = $related_relation[$rel[3]][2];
            
            $this->_js_field['name_postfix'] = 'f_'.$linker_field;
            if (isset($content[$this['name']])) {
                $this->_js_field['value'] = array();
                $linkers = $content[$this['name']]->linker_map;
                foreach ($content[$this['name']] as $num => $v) {
                    $this->_js_field['value'] []= array(
                        'id' => $v['id'], 
                        'name' => $v['name'], 
                        'value_id' => $linkers[$num]['id']
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
            $this->_js_field['values'] = array();
            if (isset($content[$this['name']])) {
                $linkers = $content[$this['name']]->linker_map;
                foreach ($linkers as $linker) {
                    $linker_fields = $linker->get_form_fields();
                    $val_array = array('_index' => $linker['id']);
                    foreach ($linker_fields as $lf) {
                        $val_array [$lf['name']]= $lf['value'];
                    }
                    $this->_js_field['values'] []= $val_array;
                }
            }
            $this->_js_field['type'] = 'set';
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
            'label' => fx::lang('Related type','system'),
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
        
        $rel = $this->get_relation();
        
        $is_mm = $rel[0] == fx_data::MANY_MANY;
        
        // конечный тип (для полей много-много)
        $end_data_type = $this->get_end_data_type();
        // связующий тип, который непосредственно ссылается
        $first_data_type = $is_mm ? $rel[1] : $end_data_type;
        
        
        /*
         * many_many, eg. tag-tag_post-post:
         * $values = array(
         *      123 => array('tag_id' => 1, 'comment' => 'olo'),
         *      124 => array('tag_id' => 2, 'comment' => 'trolo'),
         *      new_1 => array('tag_id' => 3, 'comment' => ''),
         *      new_2 => array('tag_id' => array('title' => 'New tag'), 'comment' => '')
         * );
         * 
         * has_many, eg. link-person:
         * $values = array(
         *      123 => array('url' => 'http://olo.ru/', 'title' => 'Olo site'),
         *      new_1 => array('url' => 'http://trolo.ru/', 'title' => 'Trolo site')
         * );
         */
        
        // собираем существующие значения, чтоб не запрашивать в цикле
        $existing_ids = array();
        foreach (array_keys($this->value) as $item_id) {
            if (is_numeric($item_id)) {
                $existing_ids []= $item_id;
            }
        }
        $existing_items = null;
        if (count($existing_ids) > 0) {
            $existing_items = fx::data($first_data_type, $existing_ids);
        }
        
        $new_value = new fx_collection();
        if ($is_mm) {
            $new_value->linker_map = new fx_collection();
        }
        $linker_infoblock_id = null;
        foreach ($this->value as $item_id => $item_props) {
            if (is_numeric($item_id)) {
                $linked_item = $existing_items->find_one('id', $item_id);
            } 
            // значение ключа не числовое, значит надо создать объект, на который ссылаемся
            else {
                // получаем инфоблок, куда добавлять новую сущность
                if (!$linker_infoblock_id) {
                    $linker_infoblock_id = $content->get_link_field_infoblock($this['id']);
                }
                $linked_item = fx::data($first_data_type)->create();
                $linked_item['infoblock_id'] = $linker_infoblock_id;
            }
            $linked_item->set_field_values($item_props);
            if ($is_mm) {
                $new_value[]= $linked_item[$rel[3]];
                $new_value->linker_map []= $linked_item;
            } else {
                $new_value[]= $linked_item;
            }
        }
        return $new_value;
    }
    
    public function get_end_data_type() {
        // связь, генерируемая этим полем
        $relation = $this->get_relation();
        $related_relation = fx::data($relation[1])->relations();
        $end_data_type = $related_relation[$relation[3]][1];
        return $end_data_type;
    }
    
    /*
     * Получить компонент, на который ссылается поле
     */
    public function get_related_component() {
        $rel = $this->get_relation();
        return fx::data(
                'component', 
                preg_replace("~^content_~", '', $rel[1])
        );
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
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
                if ($rel[0] === fx_data::HAS_MANY) {
                    $linkers = $content[$this['name']];
                } else {
                    $linkers = $content[$this['name']]->linker_map;
                }
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
        
        if (!$this['component_id']) {
            return $fields;
        }
        
        $com = fx::data('component', $this['component_id']);
        $chain = new fx_collection($com->get_chain());
        $chain_ids = $chain->get_values('id');
        $link_fields = fx::data('field')
                        ->where('type', fx_field::FIELD_LINK)
                        ->where('component_id', 0, '!=')
                        ->all();
        
        // селект с доступными полями-ссылками
        $linking_field_values = array();
        
        // массив селектов с уточнением типа данных
        $res_datatypes = array();
        
        // массив селектов с уточнением поля для many-many
        $res_many_many_fields = array();
        
        // массив селектов с уточнением типа для many-many
        $res_many_many_types = array();
        
        foreach ($link_fields as $lf) {
            if (in_array($lf['format']['target'], $chain_ids)) {
                // компонент, которому принадлежит текущее поле-ссылка
                $linking_field_owner_component = fx::data('component', $lf['component_id']);
                
                $linking_field_values[]= array(
                    $lf['id'], 
                    $linking_field_owner_component['keyword'].'.'.$lf['name']
                );
                
                // получаем список из ссылающегося компонента и всех его потомков
                $component_tree = fx::data('component')->get_select_values($lf['component_id']);
                
                $res_datatypes[$lf['id']] = array();
                foreach ($component_tree as $com_variant) {
                    $linking_component = fx::data('component', $com_variant[0]);
                    $res_datatypes[$lf['id']] []= array(
                        $com_variant[0],
                        $com_variant[1]
                    );
                    
                    // Для связей MANY_MANY
                    // получаем поля-ссылки компонента, указывающие на другие компоненты
                    $linking_component_links = $linking_component->
                            all_fields()->
                            find('type', fx_field::FIELD_LINK)->
                            find('id', $lf['id'], '!=');
                    
                    // исключаем поля, привязанные к родителю
                    if ($lf['format']['is_parent']){
                        $linking_component_links = $linking_component_links->find('name', 'parent_id', '!=');
                    }
                    if (count($linking_component_links) === 0) {
                        continue;
                    }
                    // ключ для many-many
                    $mmf_key = $lf['id'].'_'.$com_variant[0];
                    
                    $res_many_many_fields[$mmf_key] = array( array('', '--') );
                    foreach ($linking_component_links as $linking_component_link) {
                        $res_many_many_fields[$mmf_key] []= array(
                            $linking_component_link['id'],
                            $linking_component_link['name'],
                        );
                        
                        $target_component = fx::data(
                            'component', 
                            $linking_component_link['format']['target']
                        );
                        $end_tree = fx::data('component')->get_select_values($target_component['id']);
                        $mmt_key = $mmf_key.'|'.$linking_component_link['id'];
                        $res_many_many_types[$mmt_key] = array();
                        foreach ($end_tree as $end_com) {
                           $end_component = fx::data('component', $end_com[0]);
                           $res_many_many_types[$mmt_key] []= array(
                               $end_com[0],
                               $end_component['keyword']
                           );
                           /*
                           $linking_types[
                               $c_opt_key.'/'.$linking_component_link['id'].'/'.$end_com[0]
                           ]  = $c_opt_name.' :: '.$end_component['keyword'].'.'.$linking_component_link['name'];
                            * 
                            */
                        }
                    }
                }
            }
        }
        
        $fields[] = array(
            'id' => 'format[linking_field]',
            'name' => 'format[linking_field]',
            'label' => 'Linking field',
            'type' => 'select',
            'values' => $linking_field_values,
            'value' => $this['format']['linking_field']
        );
        foreach ($res_datatypes as $rel_field_id => $linking_datatype) {
            $field_id = 'format[linking_field_'.$rel_field_id.'_datatype]';
            $fields[]= array(
                'id' => $field_id,
                'name' => $field_id,
                'type' => 'select',
                'label' => 'Linking datatype',
                'parent' => array('format[linking_field]' => $rel_field_id),
                'values' => $linking_datatype,
                'value' => $this['format']['linking_datatype']
            );
        }
        
        foreach ($res_many_many_fields as $res_mmf_key => $mm_fields) {
            list($check_field, $check_type) = explode("_", $res_mmf_key);
            $field_id = 'format[linking_mm_field_'.$res_mmf_key.']';
            $fields[]= array(
                'id' => $field_id,
                'name' => $field_id,
                'type' => 'select',
                'label' => 'Many-many field',
                'parent' => array(
                    'format[linking_field_'.$check_field.'_datatype]' => $check_type
                ),
                'values' => $mm_fields,
                'value' => $this['format']['mm_field']
            );
        }
        foreach ($res_many_many_types as $res_mmt_key => $mmt_fields) {
            list($check_mmf, $check_field) = explode("|", $res_mmt_key);
            $field_id = 'format[linking_mm_type_'.str_replace("|", "_", $res_mmt_key).']';
            $fields[]= array(
                'id' => $field_id,
                'name' => $field_id,
                'type' => 'select',
                'label' => 'Many-many datatype',
                'parent' => array(
                    'format[linking_mm_field_'.$check_mmf.']' => $check_field
                ),
                'values' => $mmt_fields,
                'value' => $this['format']['mm_datatype']
            );
        }
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
    
    protected function _before_save() {
        $c_lf = $this['format']['linking_field'];
        $format = array(
            'render_type' => $this['format']['render_type'],
            'linking_field' => $c_lf
        );
        $c_ldt = $this['format']['linking_field_'.$c_lf.'_datatype'];
        $format['linking_datatype'] = $c_ldt;
        $mm_field = $this['format']['linking_mm_field_'.$c_lf.'_'.$c_ldt];
        if ($mm_field) {
            $format['mm_field'] = $mm_field;
            $format['mm_datatype'] = $this['format']['linking_mm_type_'.$c_lf.'_'.$c_ldt.'_'.$mm_field];
        }
        $this['format'] = $format;
        parent::_before_save();
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
        $first_data_type = $rel[1];
        
        
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
                if ($is_mm && $end_data_type) {
                    // Находим название для поля, например "tag_id"
                    // что-то страшненько...
                    $end_link_field = fx::data(
                            'component', preg_replace('~^content_~', '', $rel[1])
                        )->all_fields()->find_one(function($i) use ($rel) {
                            return $i['format']['prop_name'] == $rel[3];
                        });
                    if ($end_link_field) {
                        $item_props['f_'.$end_link_field['name']]['type'] = $end_data_type;
                    }
                }
                $linked_item = fx::data($first_data_type)->create();
                $linked_item['infoblock_id'] = $linker_infoblock_id;
            }
            dev_log($rel, $linked_item, $item_props);
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
        if (isset($relation[4])) {
            return $relation[4];
        }
        // !!! старый вариант без явного хранения типа данных для many-many
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
        if (!$this['format']['linking_field']) {
            return false;
        }
        /*
        $target_fields = explode(".", $this['format']['target']);
        $direct_target_field = fx::data('field', array_shift($target_fields));
        $direct_target_component = fx::data('component', $direct_target_field['component_id']);
         * 
         */
        $direct_target_field = fx::data('field', $this['format']['linking_field']);
        $direct_target_component = fx::data('component', $this['format']['linking_datatype']);
        if (!$this['format']['mm_field']) {
            return array(
                fx_data::HAS_MANY,
                'content_'.$direct_target_component['keyword'],
                $direct_target_field['name']
            );
        }
        
        $end_target_field = fx::data('field', $this['format']['mm_field']);
        $end_datatype = fx::data('component', $this['format']['mm_datatype']);
        return array(
            fx_data::MANY_MANY,
            'content_'.$direct_target_component['keyword'],
            $direct_target_field['name'],
            $end_target_field->get_prop_name(),
            'content_'.$end_datatype['keyword']
        );
    }
}
?>
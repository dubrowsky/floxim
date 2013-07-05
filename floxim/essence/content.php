<?php
defined("FLOXIM") || die("Unable to load file.");

class fx_content extends fx_essence {

    protected $component_id;

    public function __construct($input = array()) {
        parent::__construct($input);
        if ($input['component_id']) {
            $this->component_id = $input['component_id'];
        }
        return $this;
    }
    
    /*
     * Возвращает тип вида "content_page"
     * А если $full = false - вида "page"
     */
    public function get_type($full = true) {
        if (!$this->component_id) {
            return parent::get_type($full);
        }
        return ($full ? 'content_' : '').fx::data('component', $this->component_id)->get('keyword');
    }
    
    public function set_component_id($component_id) {
        if ($this->component_id && $component_id != $this->component_id) {
            throw new fx_Exception_content("Component id can not be changed");
        }
        $this->component_id = intval($component_id);
    }

    public function get_component_id() {
        return $this->component_id;
    }

    protected function _add_history_operation($type, $data = array()) {
        fx_history::add_operation(
            $type, 
            str_replace('fx_data_', '', get_class($this->finder)), 
            array($this->component_id, $this->data[$this->pk]), $this->modified_data, $data
        );
    }

    public function get_upload_folder() {
        return "content/".$this->component_id;
    }

    /*
     * Заполняет $this->data на основе админской формы
     * @param array $values массив значений из формы вида array('f_name' => 'Название', 'f_title => '');
     */
    public function set_field_values($values = array()) {
        if (count($values) == 0) {
            return;
        }
        
        $fields = fx::data('component', $this->component_id)->all_fields();
        $result = array('status' => 'ok');

        foreach ($fields as $field) {
            $field_name = $field->get_name();
            if (!isset($values['f_'.$field_name])) {
                if ($field['type'] == fx_field::FIELD_MULTILINK) {
                    $value = array();
                } else {
                    continue;
                }
            } else {
                $value = $values['f_'.$field_name];
            }
            
            if (!$field->check_rights()) {
                continue;
            }
            
            if ($field->validate_value($value)) {
                $field->set_value($value);
                $this[$field_name] = $field->get_savestring($this);
            } else {
                $field->set_error();
                $result['status'] = 'error';
                $result['text'][] = $field->get_error();
                $result['fields'][] = 'f_'.$field_name;
            }
        }

        return $result;
    }
    
    //protected $_fields_to_show = null;
    
    protected static $content_fields_by_component = array();


    public function get_fields_to_show() {
        $fields_to_show = array();
        $com_id = $this->component_id;
        if (!isset(self::$content_fields_by_component[$com_id])) {
            self::$content_fields_by_component[$com_id] = array();
            foreach ( fx::data('component', $com_id)->all_fields()  as $f) {
                self::$content_fields_by_component[$com_id][$f['name']] = $f;
            }
        }
        $com_fields = self::$content_fields_by_component[$com_id];
        
        $is_admin = fx::is_admin();
        
        foreach ($this->data as $fkey => $v) {
            $cf = $com_fields[$fkey];
            // не-поля и поля-мультилинки - всегда возращаем просто значение
            if (!$cf || $cf->type == 'multilink' || $cf['type_of_edit'] == fx_field::EDIT_NONE) {
                $fields_to_show[$fkey] = $v;
                continue;
            }
            // простое поле для не-админа - возвращаем значение
            if (!in_array($cf->type, array('image', 'file', 'datetime')) && !$is_admin) {
                $fields_to_show[$fkey] = $v;
                continue;
            }
            
            $field_meta = array();
            if ($cf->type == 'image' || $cf->type == 'file') {
                if ($v && is_numeric($v) && ($file_obj = fx::data('filetable', $v)) ) {
                    $field_meta['filetable_id'] = $v;
                    $v = fx::config()->HTTP_FILES_PATH.$file_obj['path'];
                }
            }
            if ($cf->type == 'datetime') {
                $field_meta['value'] = $v;
            }
            if ($cf->type == 'text') {
                $field_meta['html'] = $cf['format']['html'];
            }
            
            $field_meta = array_merge(array(
                'var_type' => 'content', 
                'content_id' => $this['id'],
                'content_type_id' => $com_id,
                'id' => $cf['id'],
                'name' => $cf['name'],
                'title' => $cf['description'],
                'type' => $cf->type,
                'editable' => true
            ), $field_meta);
            
            $fields_to_show[$fkey] = new fx_template_field($v, $field_meta);
        }

        return $fields_to_show;
    }
    
    public function get_field_to_show($field) {
        $fields = $this->get_fields_to_show();
        return isset($fields[$field]) ? $fields[$field] : null;
    }
    
    public function get_form_fields() {
        $all_fields = fx::data('component', $this->component_id)->all_fields();
        $form_fields = array();
        foreach ($all_fields as $field) {
            if ($field['type_of_edit'] == fx_field::EDIT_NONE) {
                continue;
            }
            $form_fields[]= $field->get_js_field($this);
        }
        return $form_fields;
    }
    
    public function add_template_record_meta($html) {
        $proc = new fx_template_html($html);
        $html = $proc->add_meta(array(
            'data-fx_content_essence' => array(
                'id' => $this->get('id'),
                'type' => $this->get_type(false)
            ), 
            'class' => 'fx_content_essence'
        ));
        return $html;
    }
    
    protected function _before_save() {
        $component = fx::data('component', $this->component_id);
        $link_fields = $component->fields()->find('type', fx_field::FIELD_LINK);
        foreach ($link_fields as $lf) {
            if ($lf['format']['is_parent']) {
                $this[$lf['name']] = $this['parent_id'];
            }
        }
        parent::_before_save();
    }
    /*
     * Сохраняет множественные ссылки, привязанные к сущности
     */
    protected function _save_multi_links() {
        $link_fields = 
            fx::data('component', $this->component_id)->
            all_fields()->
            find('name', $this->modified)->
            find('type', fx_field::FIELD_MULTILINK);
        foreach ($link_fields as $link_field) {
            $val = $this[$link_field['name']];
            $relation = $link_field->get_relation();
            $related_data_type = $relation[1];
            $related_field_name = $relation[2];
            // получаем компонент, управляющий связанной сущностью
            $related_component = fx::data(
                    'component', 
                    preg_replace("~^content_~", '', $related_data_type)
            );
            $related_component_fields = $related_component->all_fields();
            // и поле сущности, которое ссылается на нас
            $related_field = $related_component_fields->find_one('name', $related_field_name);
            // если оно привязано к родителю, будем сохранять порядок (сортировку)
            $related_is_child = $related_field['format']['is_parent'];
            $old_linker_map = isset($this->modified_data[$link_field['name']]->linker_map) ? 
                        $this->modified_data[$link_field['name']]->linker_map : 
                        array();
            
            switch ($relation[0]) {
                case fx_data::HAS_MANY:
                    break;
                case fx_data::MANY_MANY:
                    $priority = 0;
                    // получаем релейшны сущности-связки (tagpost например)
                    // чтобы достать оттуда $end_field
                    $related_relations = fx::data($related_data_type)->relations();
                    $end_field = $related_relations[$relation[3]][2];
                    $actual_linker_ids = array();
                    $linker_infoblock_id = null;
                    foreach ($val as $rel_obj) {
                        // $rel_obj - тэг например
                        // $end_field - конечное поле мультисвязи, tag например
                        // $linker_id - id тагпоста
                        
                        // если у конечного объекта нет id (новый) - сохраняем
                        if (!$rel_obj['id']) {
                            $rel_obj->save();
                        }
                        
                        // находим id линкера для текущего значения
                        // и удаляем его из старой карты линкеров
                        $found_linkers = array_keys($old_linker_map, $rel_obj['id']);
                        if (count($found_linkers) > 0) {
                            $linker_id = $found_linkers[0];
                            unset($old_linker_map[$linker_id]);
                            $linker_obj  = fx::data($related_data_type, $linker_id);
                        } else {
                            // выясняем, к какому инфоблоку прикреплять новую сущность-связь
                            if (is_null($linker_infoblock_id)) {
                                // инфоблок, где живем мы сами
                                $our_infoblock = fx::data('infoblock', $this['infoblock_id']);
                                
                                // достаем значение поля настроек листинга "инфоблок для поля тагпосты"
                                $c_infoblock_id = $our_infoblock['params']['field_'.$link_field['id'].'_infoblock'];
                                if ($c_infoblock_id) {
                                    $linker_infoblock_id = $c_infoblock_id;
                                } 
                                // если такого нет, используем первый попавшийся инфоблок, 
                                // содержащий объекты нашего типа
                                else {
                                    $related_container_infoblock = fx::data('infoblock')->
                                            where('site_id', $this['site_id'])->
                                            get_content_infoblocks($related_component['keyword'])->
                                            first();
                                    if ($related_container_infoblock) {
                                        $linker_infoblock_id = $related_container_infoblock['id'];
                                    }
                                }
                            }
                            $linker_params = array(
                                $relation[2] => $this['id'],
                                $end_field => $rel_obj['id'],
                                'infoblock_id' => $linker_infoblock_id,
                                'site_id' => $this['site_id']
                            );
                            if ($related_is_child) {
                                $linker_params['parent_id'] = $this['id'];
                            }
                            $linker_obj = fx::data($related_data_type)->create($linker_params);
                        }
                        $priority++;
                        if ($related_is_child) {
                            $linker_obj['priority'] = $priority;
                        }
                        //echo fen_debug('let-s save', $linker_obj);
                        $linker_obj->save();
                        $actual_linker_ids []= $linker_obj['id'];
                    }
                    // теперь удаляем старые связи
                    // если были
                    if (!isset($this->modified_data[$link_field['name']]->linker_map)) {
                        break;
                    }
                    foreach ($this->modified_data[$link_field['name']]->linker_map as $old_linker_id => $old_rel_id) {
                        if (!in_array($old_linker_id, $actual_linker_ids)) {
                            $old_linker = fx::data($related_data_type, $old_linker_id);
                            $old_linker->delete();
                        }
                    }
                    break;
            }
        }
    }

}

class fx_Exception_content extends Exception {
    
}


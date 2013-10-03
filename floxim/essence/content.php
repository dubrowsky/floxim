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

    /*
    protected function _add_history_operation($type, $data = array()) {
        fx_history::add_operation(
            $type, 
            str_replace('fx_data_', '', get_class($this->finder)), 
            array($this->component_id, $this->data[$this->pk]), $this->modified_data, $data
        );
    }
     * 
     */

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
        
        $fields = $this->get_fields();
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
        $com_fields = $this->get_fields();
        
        $is_admin = fx::is_admin();
        
        foreach ($this->data as $fkey => $v) {
            $field_meta = array();
            $cf = $com_fields[$fkey];
            // не-поля и поля-мультилинки - всегда возращаем просто значение
            if (!$cf || $cf->type == 'multilink' || $cf['type_of_edit'] == fx_field::EDIT_NONE) {
                $fields_to_show[$fkey] = $v;
                continue;
            }
            
            // поле-селект
            if ($cf->type == 'select') {
                $jsf = $cf->get_js_field($this);
                $values = $cf->get_values();
                $field_meta['display_value'] = $v ? $values[$v] : '';
                // для не админов показываем название варианта
                if (!$is_admin) {
                    $fields_to_show[$fkey] = $field_meta['display_value'];
                    continue;
                }
                $field_meta['values'] = $jsf['values'];
                $field_meta['value'] = $v;
            }
            
            // простое поле для не-админа - возвращаем значение
            if (!in_array($cf->type, array('image', 'file', 'datetime')) && !$is_admin) {
                $fields_to_show[$fkey] = $v;
                continue;
            }
            if ($cf->type == 'image' || $cf->type == 'file') {
                if ($v && is_numeric($v) && ($file_obj = fx::data('filetable', $v)) ) {
                    $field_meta['filetable_id'] = $v;
                    $v = fx::config()->HTTP_FILES_PATH.$file_obj['path'];
                }/* elseif ($cf->type == 'image' && $is_admin) {
                    $v = '/floxim/admin/images/0.gif';
                }*/
            }
            if ($cf->type == 'datetime') {
                $field_meta['value'] = $v;
            }
            if ($cf->type == 'text') {
                $field_meta['html'] = isset($cf['format']['html']) ? $cf['format']['html'] : 0;
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
        $all_fields = $this->get_fields();
        $form_fields = array();
        foreach ($all_fields as $field) {
            if ($field['type_of_edit'] == fx_field::EDIT_NONE) {
                continue;
            }
            $form_fields[]= $field->get_js_field($this);
        }
        return $form_fields;
    }
    
    public function add_template_record_meta($html, $collection, $index, $is_subroot) {
        $essence_meta = array(
            'id' => $this->get('id'),
            'type' => $this->get_type(false)
        );
        if ($collection->linker_map && isset($collection->linker_map[$index])) {
            $linker = $collection->linker_map[$index];
            $essence_meta['linker_id'] = $linker['id'];
            $essence_meta['linker_type'] = $linker['type'];
        }
        $essence_atts = array(
            'data-fx_content_essence' => $essence_meta, 
            'class' => 'fx_content_essence'. ($collection->is_sortable ? '' : ' fx_not_sortable')
        );
        if ($is_subroot) {
            $html = preg_replace_callback(
                "~^(\s*?)(<[^>]+>)~", 
                function($matches) use ($essence_atts) {
                    $tag = fx_template_html_token::create_standalone($matches[2]);
                    $tag->add_meta($essence_atts);
                    return $matches[1].$tag->serialize();
                }, 
                $html
            );
            return $html;
        } else {
            $proc = new fx_template_html($html);
            $html = $proc->add_meta($essence_atts);
        }
        return $html;
    }
    
    protected function _before_save() {
        
        $component = fx::data('component', $this->component_id);
        $link_fields = $component->fields()->find('type', fx_field::FIELD_LINK);
        foreach ($link_fields as $lf) {
            // сохраняем случаи типа $tagpost['tag'] -> $tagpost['tag_id']
            $lf_prop = $lf['format']['prop_name'];
            if (
                    isset($this[$lf_prop]) && 
                    $this[$lf_prop] instanceof fx_content && 
                    empty($this[$lf['name']])
                ) {
                if (!$this[$lf_prop]['id']) {
                    $this[$lf_prop]->save();
                }
                $this[$lf['name']] = $this[$lf_prop]['id'];
            }
            // синхронизируем поля, привязанные к родителю
            if ($lf['format']['is_parent']) {
                $lfv = $this[$lf['name']];
                if ($lfv != $this['parent_id']) {
                    if (!$this['parent_id'] && $lfv) {
                        $this['parent_id'] = $lfv;
                    } elseif ($lfv != $this['parent_id']) {
                        $this[$lf['name']] = $this['parent_id'];
                    }
                }
            }
        }
        parent::_before_save();
    }
    /*
     * Сохраняет множественные ссылки, привязанные к сущности
     */
    protected function _save_multi_links() {
        $link_fields = 
            $this->get_fields()->
            find('name', $this->modified)->
            find('type', fx_field::FIELD_MULTILINK);
        foreach ($link_fields as $link_field) {
            $val = $this[$link_field['name']];
            $relation = $link_field->get_relation();
            $related_field_name = $relation[2];
            
            switch ($relation[0]) {
                case fx_data::HAS_MANY:
                    $old_data = isset($this->modified_data[$link_field['name']]) ? 
                        $this->modified_data[$link_field['name']] :
                        new fx_collection();
                    foreach ($val as $linked_item) {
                        $linked_item[$related_field_name] = $this['id'];
                        $linked_item->save();
                    }
                    $old_data->find_remove('id', $val->get_values('id'));
                    $old_data->apply(function($i) {
                        $i->delete();
                    });
                    break;
                case fx_data::MANY_MANY:
                    $old_linkers = isset($this->modified_data[$link_field['name']]->linker_map) ? 
                        $this->modified_data[$link_field['name']]->linker_map : 
                        new fx_collection();
                    
                    // новые линкеры
                    // обязательно должны быть установлены
                    // @todo потом сделаем хитрое вычисление
                    if (!isset($val->linker_map) || count($val->linker_map) != count($val)) {
                        throw new Exception('Wrong linker map');
                    }
                    foreach ($val->linker_map as $linker_obj) {
                        $linker_obj[$related_field_name] = $this['id'];
                        $linker_obj->save();
                    }
                    
                    $old_linkers->find_remove('id', $val->linker_map->get_values('id'));
                    $old_linkers->apply(function ($i) {
                        $i->delete();
                    });
                    break;
            }
        }
    }

    /*
     * Получить id инфоблока, куда добавлять новые связанные объекты по полю $link_field
     */
    public function get_link_field_infoblock($link_field_id) {
        $linker_infoblock_id = null;
        $link_field = fx::data('field', $link_field_id);
        $related_component = $link_field->get_related_component();
        
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
        return $linker_infoblock_id;
    }
    
    public function get_fields() {
        $com_id= $this->component_id;
        
        if (!isset(self::$content_fields_by_component[$com_id])) {
            $fields = array();
            foreach ( fx::data('component', $com_id)->all_fields()  as $f) {
                $fields[$f['name']] = $f;
            }
            self::$content_fields_by_component[$com_id] = fx::collection($fields);
        }
        return self::$content_fields_by_component[$com_id];
    }
    
    protected function _after_delete() {
        parent::_after_delete();
        // delete images when deleting content
        $image_fields = $this->get_fields()->
                        find('type', fx_field::FIELD_IMAGE);
        foreach ($image_fields as $f) {
            if ( ($field_val = $this[$f['name']]) ) {
                $file = fx::data('filetable', $field_val);
                if ($file) {
                    $file->delete();
                }
            }
        }
    }
    
    protected function _after_update() {
        parent::_after_update();
        // modified image fields
        $image_fields = $this->get_fields()->
                        find('name', $this->modified)->
                        find('type', fx_field::FIELD_IMAGE);
        foreach ($image_fields as $img_field) {
            if ( ($old_value = $this->modified_data[$img_field['name']]) ) {
                if ( ($old_file = fx::data('filetable', $old_value)) ) {
                    $old_file->delete();
                }
            }
        }
        
    }
    
    public function fake() {
        $fields = $this->get_fields();
        foreach ($fields as $f) {
            $this[$f['name']] = $f->fake_value();
        }
        //echo fx_debug($fields);
    }
}

class fx_Exception_content extends Exception {
    
}
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
        return ($full ? 'content_' : '').fx::data('component', $this->component_id)->get('keyword');
    }
    
    public function set_component_id($component_id) {
        if ($this->component_id && $component_id != $this->component_id) {
            dev_log('was-is', $this->component_id, $component_id, debug_backtrace());
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

    public function set_field_values($fields = array(), $values = array()) {
        $result = array('status' => 'ok');

        foreach ($fields as $field) {
            $name = $field->get_name();

            if ($this['id']) {
                if (!isset($this->modified_data[$name])) {
                    if (isset($values['f_'.$name]) && $field->check_rights()) {
                        $value = $values['f_'.$name];
                    } else {
                        continue;
                    }
                } else {
                    $value = $this->data[$name];
                }
            } else {
                if (isset($this[$name])) {
                    $value = $this[$name];
                } else if (isset($values['f_'.$name]) && $field->check_rights()) {
                    $value = $values['f_'.$name];
                } else {
                    $value = $field['default'];
                }
            }


            if ($field->validate_value($value)) {
                $field->set_value($value);
                $this[$name] = $field->get_savestring($this);
            } else {
                $field->set_error();
                $result['status'] = 'error';
                $result['text'][] = $field->get_error();
                $result['fields'][] = 'f_'.$name;
            }
        }

        return $result;
    }
    
    //protected $_fields_to_show = null;
    
    public function get_fields_to_show() {
        if (!isset($this->_fields_to_show)) {
            $this->_fields_to_show = array();
            if (!$this->component_id) {
                dev_log('no component!', $this);
                die();
            }
            $component = fx::data('component', $this->component_id);
            $chain = $component->get_chain();
            $content_fields = new fx_collection();

            foreach ( $chain as $chain_level ) {
                if ( $chain_level['keyword'] == 'content') {
                    continue;
                }
                $content_fields->concat ( $chain_level->fields() );
            }

            foreach ($this->data as $k => $v) {
                $cf = $content_fields->find_one('name', $k);
                $fkey = $k;
                if ($cf && $cf->type != 'multilink') {
                    $this->_fields_to_show[$fkey] = new fx_template_field($v, array(
                        'var_type' => 'content', 
                        'content_id' => $this['id'],
                        'content_type_id' => $this->get_component_id(),
                        'id' => $cf['id'],
                        'name' => $cf['name'],
                        'title' => $cf['description'],
                        'editable' => true
                    ));
                } else {
                    $this->_fields_to_show[$fkey] = $v;
                }
            }
        }
        return $this->_fields_to_show;
    }
    
    public function get_field_to_show($field) {
        $fields = $this->get_fields_to_show();
        $index = $field;
        return isset($fields[$index]) ? $fields[$index] : 'nu';
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
        $link_fields = $component->fields()->find('type', 13);
        foreach ($link_fields as $lf) {
            if ($lf['format']['is_parent']) {
                $this[$lf['name']] = $this['parent_id'];
            }
        }
        parent::_before_save();
    }

}

class fx_Exception_content extends Exception {
    
}


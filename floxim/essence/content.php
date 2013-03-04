<?php
defined("FLOXIM") || die("Unable to load file.");

class fx_content extends fx_essence {

    protected $component_id;

    public function __construct($input = array()) {
        parent::__construct($input);
        if ($input['component_id']) {
            $this->component_id = $input['component_id'];
            $this->finder->set_component($input['component_id']);
        }
        return $this;
    }

    protected function _before_save() {
        $this->finder->set_component($this->component_id);
    }
    
    protected $_page = null;
    public function set_page(fx_content_page $page) {
        $this->_page = $page;
    }
    
    public function get_page() {
        return $this->_page;
    }
    
    public function get_url() {
        if (! ($page = $this->get_page())) {
            return '/unknown/';
        }
        return $page['url'];
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

    public function get_link() {
        $fx_core = fx_core::get_object();
        $infoblock = fx::data('infoblock')->get_by_id($this['infoblock_id']);
        $subdivision = fx::data('subdivision')->get_by_id($infoblock['subdivision_id']);

        $url = $subdivision['hidden_url'];
        $url .= $this['keyword'] ? $this['keyword'] : $infoblock['url'].'_'.$this['id'];
        $url .= '.html';

        return $url;
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
    
    protected $_fields_to_show = null;
    
    const field_to_show_prefix = 'f_';


    public function get_fields_to_show() {
        if (!$this->_fields_to_show) {
            $this->_fields_to_show = array();
            if (!$this->component_id) {
                dev_log('no component!', $this);
                die();
            }
            $content_fields = fx::data('component', $this->component_id)->fields();
            foreach ($this->data as $k => $v) {
                foreach ($content_fields as $cf) {
                    if ($cf['name'] == $k) {
                        $this->_fields_to_show[fx_content::field_to_show_prefix.$k] = new fx_template_field($v, array(
                            'var_type' => 'content', 
                            'content_id' => $this['id'],
                            'content_type_id' => $this->get_component_id(),
                            'id' => $cf['id'],
                            'name' => $cf['name'],
                            'title' => $cf['description'],
                            'editable' => true
                        ));
                        continue;
                    }
                }
            }
            if ( ($page = $this->get_page())) {
                $this->_fields_to_show[fx_content::field_to_show_prefix."url"] = $page->get_field_to_show('url');
            }
        }
        return $this->_fields_to_show;
    }
    
    public function get_field_to_show($field) {
        $fields = $this->get_fields_to_show();
        $index = fx_content::field_to_show_prefix.$field;
        return isset($fields[$index]) ? $fields[$index] : 'nu';
    }
    
    protected function _after_delete() {
        $component = fx::data('component', $this->component_id);
        if ($component['has_page']) {
            /* @var $page fx_content_page */
            if ( !( $page = $this->get_page()) ) {
                $page = fx::data('content_page')->get(array(
                    'content_type' => $component['keyword'],
                    'content_id' => $this['id']
                ));
            }
            if ($page) {
                $page->delete();
            }
        }
    }

}

class fx_Exception_content extends Exception {
    
}


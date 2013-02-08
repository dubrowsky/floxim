<?php

class fx_data_field extends fx_data {

    public function __construct() {
        parent::__construct();
        $this->serialized = array('format', 'parent');
        $this->order = 'priority';
    }

    public function get_by_component($component_id) {
        return $this->get_all('component_id', $component_id);
    }

    protected function get_class_name($data = array()) {
        $class_name  = parent::get_class_name($data);
        if (isset($data['type'])) {
            $class_name .= '_'.fx_field::get_type_by_id($data['type']);
        }
        return $class_name;
    }
}

?>

<?php

class fx_data_infoblock extends fx_data {

    public function __construct() {
        parent::__construct();
        $this->classname = 'fx_infoblock';
        $this->serialized = array('params', 'scope');
    }

    protected function get_class_name($data = array()) {
        $classname = $this->classname;
        if ($data['type']) {
            $classname .= '_'.$data['type'];
            if ($data['subtype']) {
                $classname .= '_'.$data['subtype'];
            }
        }

        return $classname;
    }

    public function get_content_infoblocks($content_type = null) {
        $params = array();//'is_listing' => '1');
        if ($content_type) {
            $params ['controller'] = 'component_'.$content_type;
        }
        return $this->get_all($params);
    }

    public function next_priority($keyword) {
        $db = fx_core::get_object()->db;
        return $db->get_var("SELECT MAX(`priority`)+1 FROM `{{".$this->table."}}` WHERE `keyword` = '".$db->escape($keyword)."' ");
    }

}

?>

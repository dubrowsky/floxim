<?php

class fx_data_content extends fx_data {

    public function get_tables() {
        $chain = fx::data('component', $this->component_id)->get_chain();
        $tables = array();
        foreach ($chain as $comp) {
            $tables []= $comp['keyword'] == 'content' ? 'content' : 'content_'.$comp['keyword'];
        }
        return $tables;
        //return $chain->get_values('keyword');
    }
    
    protected $component_id = null;
    
    public function __construct($table = null) {
        parent::__construct($table);
        if (preg_match("~^fx_data_content_(.+)$~", get_class($this), $content_type)) {
            $this->set_component($content_type[1]);
        }
    }
    
    public function set_component($component_id_or_code) {
        $component = fx::data('component', $component_id_or_code);
        if (!$component) {
            dev_log($component_id_or_code, debug_backtrace());
            die("Component not found: ".$component_id_or_code);
        }
        $this->component_id = $component['id'];
        $this->table = 'content_' . $component['keyword'];
        return $this;
    }

    public function create($data = array()) {
        $fx_core = fx_core::get_object();
        $user = $fx_core->env->get_user();
        
        $obj = $this->essence($data);
        
        $obj['created'] = date("Y-m-d H:i:s");
        $obj['user_id'] = +$user['id'];
        $obj['checked'] = 1;
        $obj['priority'] = $this->next_priority($this->component_id);
        return $obj;
    }

    public function next_priority ( $class_id ) {
        $this->set_component($class_id);
        return fx_core::get_object()->db->get_var("SELECT MAX(`priority`)+1 FROM `{{".$this->table."}}`");
    }
    
    protected static $content_classes = array();
    
    protected function get_class_name($data = null) {
        if ($data && isset($data['type'])) {
            if (isset(fx_data_content::$content_classes[$data['type']])) {
                return fx_data_content::$content_classes[$data['type']];
            }
            $c_type = $data['type'];
            $component = fx::data('component', $c_type);
        } else {
            $component = fx::data('component', $this->component_id);
            $c_type = $component['keyword'];
        }
        
        $chain = array_reverse($component->get_chain());
        
        $exists = false;
        
        while(!$exists && count($chain) > 0) {
            $c_level = array_shift($chain);
            $class_name = $c_level['keyword'] == 'content' ? 'fx_content' : 'fx_content_'.$c_level['keyword'];
            try {
                $exists = class_exists($class_name);
            } catch (Exception $e) {}
        }
        fx_data_content::$content_classes[$data['type']] = $class_name;
        return $class_name;
    }
    
    /**
     * Возвращает essence с установленным component_id
     * @param array $data
     * @return fx_content
     */
    public function essence($data = array()) {
        $classname = $this->get_class_name($data);
        $obj = new $classname(array(
            'data' => $data,
            'component_id' => $this->component_id
        ));
        return $obj;
    }
}
?>

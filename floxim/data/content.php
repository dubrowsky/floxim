<?php

class fx_data_content extends fx_data {

    protected $component_id = null;
    
    public function __construct($table = null) {
        parent::__construct($table);
        if (preg_match("~^fx_data_content_(.+)$~", get_class($this), $content_type)) {
            $this->set_component($content_type[1]);
        }
    }
    
    

    public function get_by_id($id = '') {
        return $this->get('id', $id);
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
    
    protected function get_class_name() {
        $component = fx::data('component')->get('id', $this->component_id);
        $class_name = 'fx_content_'.$component['keyword'];
        try {
            if (class_exists($class_name)) {
                return $class_name;
            }
        } catch (Exception $e) {
            return 'fx_content';
        }
    }
    
    /**
     * Возвращает essence с установленным component_id
     * @param array $data
     * @return fx_content
     */
    public function essence($data = array()) {
        $essence = parent::essence($data);
        if (!$this->component_id) {
            dev_log('no clid', $this);
            die("NO CLID");
        }
        $essence->set_component_id($this->component_id);
        return $essence;
    }
}
?>

<?php

class fx_data_content extends fx_data {

    
    protected function _get_data() {
        $data = parent::_get_data();
        $types_by_id = $data->get_values('type', 'id');
        $base_type = fx::data('component', $this->component_id)->get('keyword');
        $base_table = $base_type == 'content' ? 'content' : 'content_'.$base_type;
        $types = array();
        foreach ($types_by_id as $id => $type) {
            if ($type != $base_type) {
                if (!isset($types[$type])) {
                    $types[$type] = array();
                }
                $types[$type] []= $id;
            }
        }
        foreach ($types as $type => $ids) {
            $type_tables = array_reverse(fx::data('content_'.$type)->get_tables());
            $missed_tables = array();
            foreach ($type_tables as $table) {
                if ($table == $base_table) {
                    break;
                }
                $missed_tables []= $table;
            }
            $base_missed_table = array_shift($missed_tables);
            $q = "SELECT * FROM `{{".$base_missed_table."}}` \n";
            foreach ($missed_tables as $mt) {
                $q .= " INNER JOIN `{{".$mt.'}}` ON `{{'.$mt.'}}`.id = `{{'.$base_missed_table."}}`.id\n";
            }
            $q .= "WHERE `{{".$base_missed_table."}}`.id IN (".join(", ", $ids).")";
            $extensions = fx::db()->get_results($q);
            echo fen_debug($extensions);
        }
        echo fen_debug($types);
        die();
    }
    
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
        if (isset($data['type'])) {
            $component_id = fx::data('component', $data['type'])->get('id');
        } else {
            $component_id = $this->component_id;
        }
        
        $obj = new $classname(array(
            'data' => $data,
            'component_id' => $component_id
        ));
        return $obj;
    }
    
    public function update($data, $where = array()) {
        $wh = array();
        foreach ($where as $k => $v) {
            $wh[] = "`".fx::db()->escape($k)."` = '".fx::db()->escape($v)."' ";
        }

        
        
        $update = $this->_set_statement($data);
        foreach ($update as $table => $props) {
            $q = 'UPDATE `{{'.$table.'}}` SET '.join(', ', $props);
            if ($wh) {
                $q .= " WHERE ".join(' AND ', $wh);
            }
            echo $q."<br />";
        }
        die();

        if ($update) {
            fx::db()->query("UPDATE `{{".$this->table."}}` SET ".join(',', $update)." ".( $wh ? " WHERE ".join(' AND ', $wh) : "")." ");
        }
    }
    
    protected function _set_statement($data) {
        $res = array();
        $chain = fx::data('component', $this->component_id)->get_chain();
        foreach ($chain as $level_component) {
            $table_res = array();
            $fields = $level_component->fields()->get_values('name');
            // пока базовые поля контента выписываем вручную
            if ($level_component['keyword'] == 'content') {
                $fields += array(
                    'priority', 
                    'checked',
                    'created',
                    'last_updated',
                    'user_id',
                    'parent_id',
                    'type',
                    'infoblock_id',
                    'site_id'
                );
            }
            foreach ($fields as $f) {
                if (isset($data[$f])) {
                    $table_res[]= "`".fx::db()->escape($f)."` = '".fx::db()->escape($data[$f])."' ";
                }
            }
            if (count($table_res) > 0) {
                $res[$level_component['keyword']] = $table_res;
            }
        }
        return $res;
    }
}
?>

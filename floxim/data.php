<?php

/**
 * Прослойка между таблицей и объектом
 */
class fx_data {

    protected $table;
    protected $pk = 'id';
    protected $order = array();
    //protected $classname;
    protected $serialized = array();
    protected $sql_function = array();
    
    protected $limit;
    protected $where = array();
    
    protected $with = array();
    
    const BELONGS_TO = 0;
    const HAS_MANY = 1;
    const HAS_ONE = 2;
    const MANY_MANY = 3;

    public function quicksearch($term = null) {
        if (!isset($term)) {
            return;
        }
        $terms = explode(" ", $term);
        if (count($terms)>0) {
            foreach ($terms as $tp) {
                $this->where('name', '%'.$tp.'%', 'LIKE');
            }
        }
        $items = $this->all();
        $res = array('meta' => array(), 'results' => array());
        foreach ($items as $i) {
            $res['results'][]= array(
                'name' => $i['name'],
                'id' => $i['id']
            );
        }
        return $res;
    }

    public function relations() {
        return array();
    }

    public function get_multi_lang_fields() {
        return array();
    }

    /*
     * @return fx_collection
     */
    public function all() {
        $data = $this->_get_essences();
        return $data;
    }

    public function one() {
        $this->limit = 1;
        $data = $this->_get_essences();
        return isset($data[0]) ? $data[0] : false;
    }
    
    public function limit() {
        $args = func_get_args();
        if (count($args) == 1) {
            $this->limit = $args[0];
        } elseif (count($args) == 2) {
            $this->limit = $args[0].', '.$args[1];
        }
        return $this;
    }
    
    public function where($field, $value, $type = '=') {
        $this->where []= array($field, $value, $type);
        return $this;
    }
    
    public function where_or() {
        $conditions = func_get_args();
        $this->where []= array($conditions, null, 'OR');
        return $this;
    }
    
    public function clear_where($field, $value = null) {
        
        foreach ($this->where as $where_num => $where_props) {
            if ($where_props[0] == $field) {
                if (func_num_args() == 1 || $value == $where_props[1]) {
                    unset($this->where[$where_num]);
                }
            }
        }
        return $this;
    }
    
    public function order($field, $direction = 'ASC') {
        // clear order by passing null
        if ($field === null) {
            $this->order = array();
            return $this;
        }
        if (!preg_match("~asc|desc~i", $direction)) {
            $direction = 'ASC';
        }
        $this->order []= "`".$field."` ".$direction;
        return $this;
    }
        
    public function with($relation, $finder = null) {
        $this->with []= array($relation, $finder);
        return $this;
    }
    
    protected $calc_found_rows = false;
    public function calc_found_rows($on = true) {
        $this->calc_found_rows = (bool) $on;
    }
    
    public function get_found_rows() {
        return isset($this->found_rows) ? $this->found_rows : null;
    }
    
    protected $select = null;
    
    public function select($what) {
        // сбросить так: $finder->select(null)
        if (func_num_args() == 1 && is_null($what)) {
            $this->select = null;
            return $this;
        }
        if (is_null($this->select)) {
            $this->select = array();
        }
        foreach (func_get_args() as $arg) {
            $this->select []= $arg;
        }
        return $this;
    }
    
    protected $group = array();
    public function group($by) {
        if (func_num_args() == 1 && is_null($by)) {
            $this->group = array();
            return $this;
        }
        $this->group []= $by;
        return $this;
    }
    
    public function build_query() {
        // 1. Получить таблицы-родители
        $tables = $this->get_tables();
        $base_table = array_shift($tables);
        $q = 'SELECT ';
        if ($this->calc_found_rows) {
            $q .= 'SQL_CALC_FOUND_ROWS ';
        }
        
        if (!is_null($this->select)) {
            $q .= join(", ", $this->select);
        } else {
            $q .= ' * ';
        }
        $q .= ' FROM `{{'.$base_table."}}`\n";
        foreach ($tables as $t) {
            $q .= 'INNER JOIN `{{'.$t.'}}` ON `{{'.$t.'}}`.id = `{{'.$base_table."}}`.id\n";
        }
        foreach ($this->joins as $join) {
            $q .= $join['type'].' ';
            $q .= 'JOIN ';
            $q .= $join['table'].' ON '.$join['on'].' ';
        }
        if (count($this->where) > 0) {
            $conds = array();
            foreach ($this->where as $cond) {
                $conds []= $this->_make_cond($cond, $base_table);
            }
            $q .= "WHERE ".join(" AND ", $conds);
        }
        if (count($this->group) > 0) {
            $q .= " GROUP BY ".join(", ", $this->group);
        }
        if (is_array($this->order) && count($this->order) > 0) {
            $q .= " ORDER BY ".join(", ", $this->order);
        }
        if ($this->limit){
            $q .= ' LIMIT '.$this->limit;
        }
        return $q;
    }
    
    protected $joins = array();
    
    public function join($table, $on, $type = 'inner') {
        $this->joins[]= array(
            'table' => $table, 
            'on' => $on, 
            'type' => strtoupper($type)
        );
        return $this;
    }
    
    protected function _make_cond($cond, $base_table) {
        if (strtoupper($cond[2]) === 'OR') {
            $parts = array();
            foreach ($cond[0] as $sub_cond) {
                if (!isset($sub_cond[2])) {
                    $sub_cond[2] = '=';
                }
                $parts []= $this->_make_cond($sub_cond, $base_table);
            }
            return " (".join(" OR ", $parts).") ";
        }
        if (strtoupper($cond[2]) === 'RAW') {
            return '`'.$cond[0].'` '.$cond[1];
        }
        list($field, $value, $type) = $cond;
        if ($field == 'id') {
            $field = "`{{".$base_table."}}`.id";
        } else {
            // use conditions like "MD5(`field`)" as is
            if (!preg_match("~[a-z0-9_-]\s*\(.*?\)~i", $field)) {
                //$field = '`'.$field.'`';
            }
        }
        if (is_array($value)) {
            if (count($value) == 0) {
                return 'FALSE';
            }
            if ($type == '=') {
                $type = 'IN';
            }
            $value = " ('".join("', '", $value)."') ";
        } elseif (in_array(strtolower($type), array('is null', 'is not null'))) {
            $value = '';
        } else {
            $value = "'".$value."'";
        }
        return $field.' '.$type.' '.$value;
    }
    
    public function show_query() {
        return fx::db()->prepare_query($this->build_query());
    }
    
     /*
     * Метод собирает плоские данные
     */
    public function get_data() {
        $query = $this->build_query();
        $res = fx::db()->get_results($query);

        if (fx::db()->is_error()) {
            throw new Exception("SQL ERROR");
        }
        
        if ($this->calc_found_rows) {
            $this->found_rows = fx::db()->get_var('SELECT FOUND_ROWS()');
        }

        $objs = array();
        foreach ($res as $v) {
            // не забыть serialized
            foreach ($this->serialized as $serialized_field_name) {
                if (isset($v[$serialized_field_name])) {
                    $v[$serialized_field_name] = unserialize($v[$serialized_field_name]);
                }
            }
            $objs[] = $v;
        }
        $collection = new fx_collection($objs);
        if (is_array($this->order)) {
            $sorting = strtolower(trim(join("", $this->order)));
            $sorting = str_replace("asc", '', $sorting);
            $sorting = str_replace("`", '', $sorting);
            $sorting = trim($sorting);
            $collection->is_sortable = $sorting == 'priority';
        }
        return $collection;
    }
    
    /*
     * Метод вызывает $this->get_data(),
     * и из коллекции плоских данных собирает эссенсы
     */
    protected function _get_essences() {
        $data = $this->get_data();
        foreach ($data as $dk => $dv) {
            $data[$dk] = $this->essence($dv);
        }
        $this->_add_relations($data);
        return $data;
    }
    
    protected function _get_default_relation_finder($rel, $rel_name = null) {
        return fx::data($rel[1]);
    }
    
    public function add_related($rel_name, $essences, $rel_finder = null) {
        //echo fx_debug('adding rel', 'bt5', $rel_name, $essences, $rel_finder);
        $relations = $this->relations();
        if (!isset($relations[$rel_name])) {
            return;
        }
        $rel = $relations[$rel_name];
        list($rel_type, $rel_datatype, $rel_field) = $rel;

        if (!$rel_finder){
            $rel_finder = $this->_get_default_relation_finder($rel, $rel_name);
        }

        // e.g. $rel = array(fx_data::HAS_MANY, 'field', 'component_id');
        switch ($rel_type) {
            case self::BELONGS_TO:
                $rel_items = $rel_finder->where('id', $essences->get_values($rel_field))->all();
                $essences->attache($rel_items, $rel_field, $rel_name);
                break;
            case self::HAS_MANY:
                //echo fx_debug('has manu', $rel_finder);
                $rel_items = $rel_finder->where($rel_field, $essences->get_values('id'))->all();
                $essences->attache_many($rel_items, $rel_field, $rel_name);
                break;
            case self::HAS_ONE:
                break;
            case self::MANY_MANY:
                $end_rel = $rel[3];
                // чтобы вынимались связанные сущности 
                // только с непустым полем, по которому связываем
                $end_rel_data = $rel_finder->relations();
                $end_rel_field = $end_rel_data[$end_rel][2];
                
                // $rel[4] - тип данных для many-many
                $end_finder = null;
                if (isset($rel[4])) {
                    $end_rel_datatype = $rel[4];
                    $end_finder = fx::data($end_rel_datatype);
                }
                
                
                $rel_finder
                        ->with($end_rel, $end_finder)
                        ->where($rel_field, $essences->get_values('id'));
                if ($end_rel_field) {
                    $rel_finder->where($end_rel_field, 0, '!=');
                }
                $rel_items = $rel_finder->all()->find($end_rel, null, '!=');
                //echo fx_debug($rel_items, $rel_finder);
                $essences->attache_many($rel_items, $rel_field, $rel_name, 'id', $end_rel);
                break;
        }
    }
    
    /*
     * Метод добавляет релейтед-сущности к коллекции
     * использует $this->with и $this->relations
     */
    protected function _add_relations(fx_collection $essences) {

        if (count($this->with) == 0) {
            return;
        }
        if (count($essences) == 0) {
            return;
        }
        $relations = $this->relations();
        foreach ($this->with as $with) {
            list($rel_name, $rel_finder) = $with;
            if (!isset($relations[$rel_name])) {
                continue;
            }
            $this->add_related($rel_name, $essences, $rel_finder);
        }
    }
    

    /**
     * @todo ДАЛЕЕ: разобраться, что можно убить;
     */
///////////////////////////
    
    static public function optional($table) {
        return new self($table);
    }

    public function __construct($table = null) {
        if (!$table) {
            $table = str_replace('fx_data_', '', get_class($this));
        }
        $this->table = $table;
    }
    
    public function get_tables() {
        return array($this->table);
    }

    public function get_pk() {
        return $this->pk;
    }

    /**
     *
     * @param type $id
     * @return fx_essence
     */
    public function get_by_id($id) {
        return $this->where('id', $id)->one();
    }
    
    /**
     * Получить объекты по списку id
     * @param type $ids
     * @return array
     */
    public function get_by_ids($ids) {
        return $this->where('id', $ids)->all();
    }
    
    public function get() {
        fx::log(debug_backtrace());
        die();
        $argc = func_num_args();
        $argv = func_get_args();
        $query = $this->make_query($argc, $argv);
        $res = fx::db()->get_row("SELECT * FROM `{{".$this->table."}}`".$query);

        if (fx::db()->is_error()) {
            throw new Exception("SQL ERROR ".fx::db()->debug());
        }

        if (!$res) return false;

        if (!empty($this->serialized) && $res) {
             foreach ($this->serialized as &$key) {
                $original = $res[$key];
                $res[$key] = unserialize($res[$key]);
                if (!is_array($res[$key])) {
                    $res[$key] = $original;
                }
            }
        }
        $obj = $this->essence($res);
        return $obj;
    }

    /**
     * Создать новый essence-экземпляр, заполнить значениями по умолчанию
     * @param array $data
     * @return fx_essence
     */
    public function create($data = array()) {
        return $this->essence($data);
    }
    
    /**
     * Инициализировать essence
     * @param type $data
     * @return fx_essence
     */
    public function essence($data = array()) {
        $classname = $this->get_class_name($data);
        $obj = new $classname(array('data' => $data));
        if ($classname == 'fx_simplerow') {
            $obj->table = $this->table;
        }
        return $obj;
    }

    public function insert($data) {
        $set = $this->_set_statement($data);
        if ($set) {
            fx::db()->query("INSERT INTO `{{".$this->table."}}` SET ".join(",", $set));
            $id = fx::db()->insert_id();
        }

        return $id;
    }

    public function update($data, $where = array()) {
        $wh = array();
        $update = $this->_set_statement($data);
        
        foreach ($where as $k => $v) {
            $wh[] = "`".fx::db()->escape($k)."` = '".fx::db()->escape($v)."' ";
        }

        if ($update) {
            fx::db()->query("UPDATE `{{".$this->table."}}` SET ".join(',', $update)." ".( $wh ? " WHERE ".join(' AND ', $wh) : "")." ");
        }
    }

    public function delete() {
        $argc = func_num_args();
        $argv = func_get_args();

        $where = array();
        for ($i = 0; $i < $argc; $i = $i + 2) {
            $where[] = "`".$argv[$i]."` = '".fx::db()->escape($argv[$i + 1])."'";
        }
        if ($where) $where = " WHERE ".join(" AND ", $where);

        $res = fx::db()->get_results("DELETE FROM `{{".$this->table."}}`".$where);
    }

    public function get_parent($item) {
        $id = $item;
        if ($item instanceof fx_essence || is_array($item)) {
            $id = $item['parent_id'];
        }

        return $this->get_by_id($id);
    }

    public function next_priority() {
        return fx::db()->get_var("SELECT MAX(`priority`)+1 FROM `{{".$this->table."}}`");
    }

    /**
     * Получить название класса для essence
     * @param array $data данные essence'а
     * @return string
     */
    protected function get_class_name($data = array()) {
        $classname = 'fx_'.str_replace('fx_data_', '', get_class($this));
        try {
            if (class_exists($classname)) {
                return $classname;
            }
        } catch (Exception $e) {}
        return 'fx_simplerow';
    }
    
    protected function _get_columns($table = null) {
        if (!$table) {
            $table = $this->table;
        }
        $cache_key = 'table_columns_'.$table;
        if ( ($columns = fx::cache($cache_key)) ) {
            return $columns;
        }
        $columns = fx::db()->get_col('SHOW COLUMNS FROM {{'.$table.'}}', 0);
        fx::cache($cache_key, $columns);
        return $columns;
    }

    protected function _set_statement($data) {
        
        $cols = $this->_get_columns();
        
        $set = array();

        foreach ($data as $k => $v) {
            if (!in_array($k, $cols)) {
                continue;
            }
            if (in_array($k, $this->serialized) && is_array($v)) {
                $v = serialize($v);
            }
            $str = "'".fx::db()->escape($v)."' ";
            if (isset($this->sql_function[$k])) {
                $str = $this->sql_function[$k]."(".$str.")";
            }

            $set[] = "`".fx::db()->escape($k)."` = ".$str;
        }

        return $set;
    }
}
?>
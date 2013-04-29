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

    
    public function all() {
        $data = $this->_get_essences();
        return $data;
    }

    public function one() {
        $this->limit = 1;
        $data = $this->_get_essences();
        return isset($data[0]) ? $data[0] : false;
    }
    
    public function where($field, $value, $type = '=') {
        $this->where []= array($field, $value, $type);
        return $this;
    }
    
    public function order($field, $direction = 'ASC') {
        if (!preg_match("~asc|desc~i", $direction)) {
            $direction = 'ASC';
        }
        $this->order []= "`".$field."` ".$direction;
        return $this;
    }
    
    public function with($relation, $finder = null) {
        
    }
    
    public function build_query() {
        // 1. Получить таблицы-родители
        $tables = $this->get_tables();
        $base_table = array_shift($tables);
        $q = 'SELECT * FROM `{{'.$base_table."}}`\n";
        foreach ($tables as $t) {
            $q .= 'INNER JOIN `{{'.$t.'}}` ON `{{'.$t.'}}`.id = `{{'.$base_table."}}`.id\n";
        }
        if (count($this->where) > 0) {
            $conds = array();
            foreach ($this->where as $cond) {
                list($field, $value, $type) = $cond;
                if ($field == 'id') {
                    $field = "`{{".$base_table."}}`.id";
                } else {
                    $field = '`'.$field.'`';
                }
                if (is_array($value) && $type = '=') {
                    if (count($value) == 0) {
                        $conds []= 'FALSE';
                        continue;
                    }
                    $type = 'IN';
                    $value = " ('".join("', '", $value)."') ";
                } else {
                    $value = "'".$value."'";
                }
                $conds []= $field.' '.$type.' '.$value;
            }
            $q .= "WHERE ".join(" AND ", $conds);
        }
        if (is_array($this->order) && count($this->order) > 0) {
            $q .= " ORDER BY ".join(", ", $this->order);
        }
        if ($this->limit){
            $q .= ' LIMIT '.$this->limit;
        }
        return $q;
    }
    
    public function show_query() {
        return fx::db()->prepare_query($this->build_query());
    }
    
     /*
     * Метод собирает плоские данные
     */
    protected function _get_data() {
        $query = $this->build_query();
        $res = fx::db()->get_results($query);

        if (fx::db()->is_error()) {
            throw new Exception("SQL ERROR ".fx::db()->debug());
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
        return new fx_collection($objs);
    }
    
    /*
     * Метод вызывает $this->_get_data(),
     * и из коллекции плоских данных собирает эссенсы
     */
    protected function _get_essences() {
        $data = $this->_get_data();
        //echo fen_debug($data);
        foreach ($data as $dk => $dv) {
            // а вот тут будет разбор serialized etc.
            $data[$dk] = $this->essence($dv);
        }
        return $data;
    }

    /**
     * @todo ДАЛЕЕ: разобраться, что можно убить
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
    	return $this->get('id', $id);
    }
    
    /**
     * Получить объекты по списку id
     * @param type $ids
     * @return array
     */
    public function get_by_ids($ids) {
        return $this->where('id', $ids)->all();
        return $this->get_all(array('id' => $ids));
    }
    
    public function get() {
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
     * Форматы входных параметров:
     * 1) Поля и значения через запятые:
     *  "a",1,"b",3 -> WHERE `a` = '1' AND `b` = '3'
     * 2) Прямое задание WHERE (одна строчка )
     *  "a=3" -> WHERE a=3 ( ничего не экранируется! )
     * 3) Прямое задание WHERE c подготовленным выражениями ( placeholder )
     * "a = ? OR b = ?", array( $_GET['a'], $b )
     * 4) Задание WHERE через массив ( ключ - поле, значение - значение, все через AND )
     *  array('a' => 3, 'b' => 4 ) -> `a` = '1' AND `b` = '3'
     * @return classname
     */
    public function get_all() {
        $argc = func_num_args();
        $argv = func_get_args();
        $query = "SELECT * FROM `{{".$this->table."}}`".$this->make_query($argc, $argv);
        $res = fx::db()->get_results($query);

        if (fx::db()->is_error()) {
            throw new Exception("SQL ERROR ".fx::db()->debug());
        }

        $objs = array();
        foreach ($res as $v) {
            if (!empty($this->serialized)) {
                foreach ($this->serialized as &$key) {
                    if (isset($v[$key])) {
                        $original = $v[$key];
                        $v[$key] = unserialize($v[$key]);
                        if (!is_array($v[$key])) {
                            $v[$key] = $original;
                        }
                    }
                }
            }
            $essence = $this->essence($v);
            $objs[] = $essence;
        }
        return new fx_collection($objs);
    }

    protected function make_query($argc, $argv) {
        $query = '';
        $where = '';
        $order = $this->order ? $this->order : '';
        $limit = '';
        // OMG!!!
        // передаем 2 аргумента - условия и параметры
        // array($conds), array('order' => 'priority', 'limit' => 10)
        $special_syntax = false;
        if ($argc == 2 && is_array($argv[1]) && is_array($argv[0])) {
            $special_keys = array('order', 'group', 'limit');
            foreach ($special_keys as $spk) {
                if (array_key_exists($spk, $argv[1])){
                    $special_syntax = true;
                    break;
                }
            }
            if (isset($argv[1]['order'])) {
                $order = $argv[1]['order'];
            }
            if (isset($argv[1]['limit'])) {
                $limit = $argv[1]['limit'];
            }
            // делаем вид, что у нас 1 аргумент
            $argc = 1;
        }

        if ($argc == 1 && is_string($argv[0])) {
            $where = $argv[0];
        } else if ($argc == 1 && is_array($argv[0])) {
            $cond = (array_key_exists('order', $argv[0]) || array_key_exists('where', $argv[0]) || array_key_exists('limit', $argv[0]));
            if ($cond) {
                $order = $argv[0]['order'];
                $where = $argv[0]['where'];
                $limit = $argv[0]['limit'];
            } else {
                foreach ($argv[0] as $k => $v) {
                    $where_cond = "`".$k."` ";
                    if (is_array($v)) {
                        if (count($v) > 0) {
                            array_walk($v, array(fx::db(), 'escape'));
                            $where_cond .= " IN ('".join("', '", $v)."')";
                        } else {
                            $where_cond = ' FALSE ';
                        }
                    } else {
                        $where_cond .= "= '".fx::db()->escape($v)."'";
                    }
                    $where[] = $where_cond;
                }
            }
        } else if ($argc == 2 && is_array($argv[1])) {
            $parts = explode('?', $argv[0]);
            $count = count($parts);
            foreach ($parts as $k => $v) {
                $where .= $v;
                if ($count - 1 <> $k) $where .= " '".$argv[1][$k]."' ";
            }
        }
        else {
            for ($i = 0; $i < $argc; $i = $i + 2) {
                $arg = (strpos($argv[$i], '`') === false ) ? "`".$argv[$i]."`" : $argv[$i];
                $where[] = $arg." = '".fx::db()->escape($argv[$i + 1])."'";
            }
        }
        if ($where) {
            $query .= " WHERE ".(is_array($where) ? join(" AND ", $where) : $where);
        }
        if ($order) {
            if (is_array($order)) {
                $order = join(", ", $order);
            }
            $query .= " ORDER BY ".$order;
        }
        if ($limit) {
            $query .= " LIMIT ".$limit;
        }
        return $query;
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
        return $obj;
    }

    public function insert($data) {
        $set = $this->_set_statement($data);

        if ($set) {
            fx::db()->query("INSERT INTO `{{".$this->table."}}` SET ".join(",", $set)."");
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

    protected function _set_statement($data) {
        $set = array();

        foreach ($data as $k => $v) {
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
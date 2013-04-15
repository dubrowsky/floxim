<?
class fx_collection implements ArrayAccess, IteratorAggregate, Countable {
    
    protected $data = array();
    
    public function __construct($data = array()) {
        if (is_array($data)){
            $this->data = $data;
        }
    }
    
    public function __get($name) {
        if ($name == 'length') {
            return $this->count();
        }
    }
    
    public function count() {
        return count($this->data);
    }
    
    /*
     * Получить первый элемент коллекции
     */
    public function first() {
        foreach ($this->data as $di) {
            return $di;
        }
    }
    
    /*
     * Создает новую коллекцию с результатами
     * $collection->find( array('price', '10', '>'), array('visibililty', 'on'));
     * $collection->find('price', '10', '>');
     * $collection->find('visibility', 'on'); 
     * $collection->find(function($item){});
     * @return fx_collection
     */
    public function find() {
        $res = array();
        $filters = func_get_args();
        foreach ($this->data as $di) {
            if ($this->_check_item($di, $filters)) {
                $res []= $di;
            }
        }
        return new fx_collection($res);
    }
    
    public function find_one() {
        $filters = func_get_args();
        foreach ($this->data as $di) {
            if ($this->_check_item($di, $filters)) {
                return $di;
            }
        }
        return null;
    }
    
    protected function _check_item($item, $filters) {
        if (is_array($filters) && !is_array($filters[0]) && !is_callable($filters[0])) {
            $filters = array($filters);
        }
        foreach ($filters as $f) {
            if (is_callable($f) && !call_user_func($f, $item)) {
                return false;
            } elseif (is_array($f)) {
                $f_keys = array_keys($f);
                if (count($f) == 1 && !is_numeric($f_keys[0])) {
                    $f = array($f_keys['0'], $f[$f_keys['0']], '==');
                } else {
                    if (!isset($f[1])) {
                        $f[1] = '';
                        $f[2] = 'exists';
                    } elseif (!isset($f[2])) {
                        $f[2] = '==';
                    }
                }
                list($test_field, $test_val, $test_cond) = $f;
                switch ($test_cond) {
                    case '==':
                        if (! ($item[$test_field] == $test_val)) {
                            return false;
                        }
                        break;
                    case 'exists':
                        if (!isset($item[$test_field]) || empty($item[$test_field])) {
                            return false;
                        }
                        break;
                    case '>': case '<': case '>=': case '<=':
                        // реализовать!!!
                        break;
                }
            }
        }
        return true;
    }
    
    /*
     * Фильтрует текущую коллекцию по условию (удаляет не совпадающие записи)
     */
    public function filter() {
        $filters = func_get_args();
        foreach ($this->data as $dk => $di) {
            if (!$this->_check_item($di, $filters)) {
                unset($this->data[$dk]);
            }
        }
        return $this;
    }
    
    /*
     * Сортирует текущую коллекцию
     * $c->sort('id')
     * $c->sort(function($a,$b) {});
     */
    public function sort() {
        
    }
    
    /*
     * Применить функцию ко всем элементам
     */
    public function apply($callback) {
        foreach ($this->data as $di) {
            call_user_func($callback, $di);
        }
        return $this;
    }
    
    public function get_values($field, $key_field = null) {
        $result = array();
        foreach ($this->data as $k => $v) {
            $res_key = $key_field ? $key_field : $k;
            if ( (is_array($v) || $v instanceof ArrayAccess) && isset($v[$field])) {
                $result[$res_key] = $v[$field];
            } elseif (is_object($v) && isset($v->$field)) {
                $result[$res_key] = $v->$field;
            } else {
                $result[$res_key] = null;
            }
        }
        return $result;
    }
    
    /* IteratorAggregate */
    
    public function getIterator() {
        return new ArrayIterator($this->data);
    }

    public function set($offset, $value) {
        $this->data[$offset]= $value;
    }
    
    /* Array access */

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $last_key = end(array_keys($this->data));
            if (!$last_key) {
                $offset = 0;
            } else {
                $offset = $last_key+1;
            }
        }
        $this->set($offset, $value);
    }

    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }
}

if (preg_match("~collection~", $_SERVER['REQUEST_URI'])) {
    require_once '../../boot.php';
    $pages = fx::data('content_page')->get_all();
    $page = $pages->find('parent_id', 6)->get_values('url');
    echo "<pre>" . htmlspecialchars(print_r($page, 1)) . "</pre>";
}
?>
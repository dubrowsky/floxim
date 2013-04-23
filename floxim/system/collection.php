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
     * $collection->find('price', '10', '>');
     * $collection->find('visibility', 'on'); 
     * $collection->find(function($item){});
     * @return fx_collection
     */
    public function find($field, $prop = null, $compare_type = self::FILTER_EQ) {
        if (count($this->data) == 0) {
            return new fx_collection();
        }
        if (is_null($prop)) {
            $compare_type = is_callable($field) ? self::FILTER_CALLBACK : self::FILTER_EXISTS;
        } else {
            $compare_type = self::FILTER_EQ;
        }
        if ($compare_type == self::FILTER_EQ) {
            foreach ($this->data as $item) {
                if ($item[$field] == $prop) {
                    $res []= $item;
                }
            }
            return new fx_collection($res);
        }
        if ($compare_type == self::FILTER_CALLBACK) {
            foreach ($this->data as $item) {
                if (call_user_func($field, $item)) {
                    $res []= $item;
                }
            }
            return new fx_collection($res);
        }
        /*
        $filters = $this->_make_filters(func_get_args());
        foreach ($this->data as $item) {
            if ($this->_check_item($item_index)) {
                $res []= $di;
            }
        }*/
        return new fx_collection($res);
    }
    
    public function find_one($field, $prop = null, $compare_type = self::FILTER_EQ) {
        if (count($this->data) == 0) {
            return false;
        }
        if (is_null($prop)) {
            $compare_type = is_callable($field) ? self::FILTER_CALLBACK : self::FILTER_EXISTS;
        } else {
            $compare_type = self::FILTER_EQ;
        }
        if ($compare_type == self::FILTER_EQ) {
            foreach ($this->data as &$item) {
                if ($item[$field] == $prop) {
                    return $item;
                }
            }
            return false;
        }
        if ($compare_type == self::FILTER_CALLBACK) {
            foreach ($this->data as $item) {
                if (call_user_func($field, $item)) {
                    return $item;
                }
            }
            return false;
        }
        return false;
    }
    /*
    public function find_one() {
        $filters = func_get_args();
        $filters = $this->_make_filters($filters);
        foreach ($this->data as $di) {
            if ($this->_check_item($di, $filters)) {
                return $di;
            }
        }
        return null;
    }*/
    
    const FILTER_EQ = 1;
    const FILTER_EXISTS = 2;
    const FILTER_CALLBACK = 3;
    /**
     * Превращает аргументы, переданные find или find_one в фильтры вида
     * array('==', 'prop_name', 'prop_value'), array('callback')
     * @param type $filters
     */
    protected function _make_filters($filters) {
        // вызовы типа find('parent_id', 12)
        //if (!is_array($filters[0])) {
            $filters = array($filters);
        //}
        $res = array();
        foreach ($filters as $f) {
            // вызовы типа find( function($x) {} );
            /*
            if (is_callable($f)) {
                $res []= array('callback', $f);
                continue;
            }*/
            if (!isset($f[1])) {
                $f[1] = '';
                $f[2] = self::FILTER_EXISTS;
            } elseif (!isset($f[2])) {
                $f[2] = self::FILTER_EQ;
            }
            $res []= array(
                $f[2], $f[0], $f[1]
            );
        }
        return $res;
    }
    
    protected function _check_item($item_index, $filters = false) {
        return true;
        foreach ($filters as $f) {
            switch ($f[0]) {
                case self::FILTER_EQ:
                    if ($item[$f[1]] != $f[2]) {
                        return false;
                    }
                    break;
                case 'callback':
                    if (!call_user_func($f[1], $item)) {
                        return false;
                    }
                    break;
                case 'exists':
                    if (!isset($item[$f[0]]) || empty($item[$f[0]])) {
                        return false;
                    }
                    break;
            }
        }
        return true;
        
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
    
    public function get_values($field, $key_field = null, $as_collection = false) {
        $result = array();
        foreach ($this->data as $k => $v) {
            $res_key = $key_field ? $v[$key_field] : $k;
            if ( (is_array($v) || $v instanceof ArrayAccess) && isset($v[$field])) {
                $result[$res_key] = $v[$field];
            } elseif (is_object($v) && isset($v->$field)) {
                $result[$res_key] = $v->$field;
            } else {
                $result[$res_key] = null;
            }
            // если понадобится автоопределение, создавать ли коллекцию или массив
            /*
            if ($as_collection == 'auto' && gettype($result[$res_key]) != 'object') {
                $as_collection = false;
            }
             */
        }
        if ($as_collection) {
            $result = new fx_collection($result);
        }
        return $result;
    }
    
    /*
     * $users = fx::data("user")->all();
     * $posts = fx::data("post")->all();
     * $user['posts'] = fx_collection(1,2,3);
     * $users->attache_many($posts, 'author_id', 'posts');
     * 
     * $post['author'] = $user;
     * $posts->attache($users, 'this.creator_id=author.user_id')
     */
    public function attache(fx_collection $what, $cond_field, $res_field = null, $check_field = 'id') {
        if ($res_field === null) {
            $res_field = preg_replace("~_id$~", '', $cond_field);
        }
        
        foreach ($what as $what_item) {
            // $what_item = тег
            // $cond_field = 'tag_id'
            // $target_item = тагпост[tag_id = what_item.id]
            $target_items = $this->find($cond_field, $what_item[$check_field]);
            foreach ($target_items as $target_item) {
                $target_item[$res_field] = $what_item;
            }
        }
        return $this;
    }
    
    public function attache_many(
            fx_collection $what, 
            $cond_field, 
            $res_field, 
            $check_field = 'id',
            $extract_field = null) {
        // what = [post1,post2]
        // this = [user1, user2]
        // cond_field = 'author'
        // res_field = 'posts'
        foreach ($this as $our_item) {
            $what_items = $what->find($cond_field, $our_item[$check_field]);
            if (count($what_items) > 0) {
                if (!is_null($extract_field)) {
                    $what_items = $what_items->get_values($extract_field, null, true);
                }
                $our_item[$res_field]=$what_items;
            }
        }
        return $this;
    }

    public function concat ( $collection ) {
        foreach ($collection as $item) {
            $this[]= $item;
        }
        return $this;
    }
    
    /* IteratorAggregate */
    
    public function getIterator() {
        return new ArrayIterator($this->data);
    }

    public function set($offset, $value) {
        if (is_null($offset)) {
            $this->data []= $value;
        } else {
            $this->data[$offset]= $value;
        }
    }
    
    /* Array access */

    public function offsetSet($offset, $value) {
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
?>
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
    public function find($field, $prop = null, $compare_type = null) {
        if (count($this->data) == 0) {
            return new fx_collection();
        }
        if (is_null($prop)) {
            $compare_type = is_callable($field) ? self::FILTER_CALLBACK : self::FILTER_EXISTS;
        } elseif (is_null ($compare_type)) {
            if (is_array($prop)) {
                $compare_type = self::FILTER_IN;
            } else {
                $compare_type = self::FILTER_EQ;
            }
        } elseif ($compare_type == '!=') {
            $compare_type = self::FILTER_NEQ;
        }
        if ($compare_type == self::FILTER_EQ) {
            foreach ($this->data as $item) {
                if ($item[$field] == $prop) {
                    $res []= $item;
                }
            }
            return new fx_collection($res);
        }
        if ($compare_type == self::FILTER_NEQ) {
            foreach ($this->data as $item) {
                if ($item[$field] != $prop) {
                    $res []= $item;
                }
            }
            return new fx_collection($res);
        }
        if ($compare_type == self::FILTER_IN) {
            foreach ($this->data as $item) {
                if (in_array($item[$field], $prop)) {
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
        return new fx_collection($res);
    }
    
    public function find_one($field, $prop = null, $compare_type = null) {
        if (count($this->data) == 0) {
            return false;
        }
        if (is_null($prop)) {
            $compare_type = is_callable($field) ? self::FILTER_CALLBACK : self::FILTER_EXISTS;
        } elseif (is_null ($compare_type)) {
            if (is_array($prop)) {
                $compare_type = self::FILTER_IN;
            } else {
                $compare_type = self::FILTER_EQ;
            }
        }
        if ($compare_type == self::FILTER_EQ) {
            foreach ($this->data as $item) {
                if ($item[$field] == $prop) {
                    return $item;
                }
            }
            return false;
        }
        if ($compare_type == self::FILTER_NEQ) {
            foreach ($this->data as $item) {
                if ($item[$field] != $prop) {
                    return $item;
                }
            }
            return false;
        }
        if ($compare_type == self::FILTER_IN) {
            foreach ($this->data as $item) {
                if (in_array($item[$field], $prop)) {
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
    
    const FILTER_EQ = 1;
    const FILTER_EXISTS = 2;
    const FILTER_CALLBACK = 3;
    const FILTER_IN = 4;
    const FILTER_NEQ = 5;

    /*
     * Фильтрует текущую коллекцию по условию (удаляет не совпадающие записи)
     */
    public function filter() {
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
    
    /*
     * Remove element from collection
     */
    public function remove($item) {
        foreach ($this->data as $dk => $di) {
            if ($item === $di) {
                unset($this->data[$dk]);
                return;
            }
        }
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
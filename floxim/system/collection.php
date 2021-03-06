<?
class fx_collection implements ArrayAccess, IteratorAggregate, Countable {
    
    protected $data = array();
    
    public function __construct($data = array()) {
        if (is_array($data)){
            $this->data = $data;
        } else {
            $this->data = array($data);
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
    
    public function get_data() {
        return $this->data;
    }
    
    /*
     * Get the first element of the collection
     */
    public function first() {
        reset($this->data);
        return current($this->data);
    }
    
    public function next() {
        return next($this->data);
    }
    
    public function key() {
        return key($this->data);
    }

    public function last(){ 
        return end($this->data);
    }
    
    /*
     * Creates a new collection with the results
     * $collection->find('price', '10', '>')
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
        $res = array();
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
        if ($compare_type == self::FILTER_EXISTS) {
            foreach ($this->data as $item) {
                if (isset($item[$field]) && $item[$field]) {
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
     * Filters the current collection by condition (removes not matching records)
     */
    public function filter() {
        return $this;
    }
    
    public function unique($field = null) {
        $res = array();
        if (is_null($field) && $this->first() instanceof fx_essence) {
            $field = 'id';
        }
        if (!is_null($field)) {
            foreach ($this->data as $item) {
                $res[$item[$field]] = $item;
            }
            $this->data = $res;
            return $this;
        }
        
        $this->data = array_unique($this->data);
        return $this;
    }


    /*
     * Sorts the current collection
     * $c->sort('id')
     * $c->sort(function($a,$b) {})
     */
    public function sort($sorter) {
        uasort($this->data, $sorter);
        return $this;
    }
    
    public function slice($offset, $length = null) {
        return fx::collection(array_slice($this->data, $offset, $length));
    }
    
    public function eq($offset) {
        return $this->slice($offset, 1)->first();
    }
    
    public function group($groupper, $force_property = false) {
        $res = new fx_collection();
        if (!$force_property) {
            if (is_numeric($groupper)) {
                $c = 0;
                $r = 0;
                foreach ($this as $item) {
                    if ($c % $groupper == 0) {
                        $r++;
                    }
                    if (!isset($res[$r])) {
                        $res[$r] = new fx_collection();
                    }
                    $res[$r] []= $item;
                    $c++;
                }
                return $res;
            }
            if (is_callable($groupper)) {
                foreach ($this as $item) {
                    $key = call_user_func($groupper, $item);
                    if (!isset($res[$key])) {
                        $res[$key] = new fx_collection();
                    }
                    $res[$key] []= $item;
                }
                return $res;
            }
        }
        if (is_string($groupper)) {
            $modifiers = array();
            if (preg_match("~\|~", $groupper)) {
                
                $groupper_parts = explode("|", $groupper, 2);
                $groupper = trim($groupper_parts[0]);
                
                $p = new fx_template_modifier_parser();
                $parsed_modifiers = $p->parse('|'.$groupper_parts[1]);
                if ($parsed_modifiers) {
                    foreach ($parsed_modifiers as $pmod) {
                        $callback = $pmod['name'];
                        $args = $pmod['args'];
                        if (!is_callable($callback)) {
                            continue;
                        }
                        $self_key = array_keys($args, "self");
                        if (isset($self_key[0])) {
                            $self_key = $self_key[0];
                        } else {
                            array_unshift($args, '');
                            $self_key = 0;
                        }
                        foreach ($args as &$arg_v) {
                            $arg_v = trim($arg_v, '"\'');
                        }
                        $modifiers []= array(
                            $callback,
                            $args,
                            $self_key
                        );
                    }
                }
            }
            foreach ($this as $item) {
                $key = $item[$groupper];
                if (is_null($key)) {
                    $key = '';
                } else {
                    foreach ($modifiers as $mod) {
                        $callback = $mod[0];
                        $self_key = $mod[2];
                        $args = $mod[1];
                        $args[$self_key] = $key;
                        $key = call_user_func_array($callback, $args);
                    }
                }
                
                if (!isset($res[$key])) {
                    $res[$key] = new fx_collection();
                }
                $res[$key] []= $item;
            }
            return $res;
        }
    }
    
    /*
     * To apply a function to all elements
     */
    public function apply($callback) {
        foreach ($this->data as &$di) {
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
    
    public function reverse($preserve_keys = true) {
        $this->data = array_reverse($this->data, $preserve_keys);
        return $this;
    }


    /*
     * Find elemenets and remove them from the collection
     */
    public function find_remove($field, $prop = null, $compare_type = null) {
        $items=  $this->find($field, $prop, $compare_type);
        foreach ($items as $i) {
            $this->remove($i);
        }
        return $this;
    }
    
    // alias for get_values()
    public function column($field, $key_field = null, $as_collection = true) {
        return $this->get_values($field, $key_field, $as_collection);
    }
    
    public function get_values($field, $key_field = null, $as_collection = false) {
        $result = array();
        if ($field instanceof Closure) {
            foreach ($this->data as $k => $v) {
                $res_key = $key_field ? $v[$key_field] : $k;
                $result[$res_key] = call_user_func($field, $v, $k);
            }
            if ($as_collection) {
                $result = new fx_collection($result);
            }
            return $result;
        }
        if (is_array($field)) {
            foreach ($field as $fd) {
                foreach ($this->data as $k => $v) {
                    $res_key = $key_field ? $v[$key_field] : $k;
                    if ( (is_array($v) || $v instanceof ArrayAccess) && isset($v[$fd])) {
                        $result[$res_key][$fd] = $v[$fd];
                    } elseif (is_object($v) && isset($v->$fd)) {
                        $result[$res_key][$fd] = $v->$fd;
                    } else {
                        $result[$res_key][$fd] = null;
                    }
                }
            }

            if ($as_collection) {
                $result = new fx_collection($result);
            }
            return $result;
        } 
        foreach ($this->data as $k => $v) {
            $res_key = $key_field ? $v[$key_field] : $k;
            if ( (is_array($v) || $v instanceof ArrayAccess) && isset($v[$field])) {
                $result[$res_key] = $v[$field];
            } elseif (is_object($v) && isset($v->$field)) {
                $result[$res_key] = $v->$field;
            } else {
                $result[$res_key] = null;
            }
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
        
        $res_index = array();
        foreach ($what as $what_item) {
            $res_index[$what_item[$check_field]] = $what_item;
        }
        foreach ($this as $our_item) {
            $index_val = $our_item[$cond_field];
            $our_item[$res_field] = isset($res_index[$index_val]) ? $res_index[$index_val] : null;
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
        $res_index = array();
        $col_sortable = $what->is_sortable;
        foreach ($what as $what_item) {
            $index_key = $what_item[$cond_field];
            if (!isset($res_index[$index_key])) {
                $res_index[$index_key] = new fx_collection();
                $res_index[$index_key]->is_sortable = $col_sortable;
                if ($extract_field) {
                    $res_index[$index_key]->linker_map = new fx_collection();
                }
            }
            if (!$extract_field) {
                $res_index[$index_key] []= $what_item;
            } else {
                $end_value = $what_item[$extract_field];
                $res_index[$index_key][]= $end_value;
                $res_index[$index_key]->linker_map[]= $what_item;
            }
            //$res_index[$index_key][]= $extract_field ? $what_item[$extract_field] : $what_item;
        }
        foreach ($this as $our_item) {
            $check_value = $our_item[$check_field];
            $our_item[$res_field] = isset($res_index[$check_value]) ?
                                    $res_index[$check_value] : fx::collection();
        }
        return $this;
    }

    public function concat ( $collection ) {
        if (!is_array($collection) && ! ($collection instanceof Traversable) )  {
            return $this;
        }
        foreach ($collection as $item) {
            $this[]= $item;
        }
        return $this;
    }
    
    public function add() {
        $this->concat(func_get_args());
        return $this;
    }
    
    public function make_tree($parent_field = 'parent_id', $children_field = 'nested', $id_field = 'id') {
        $index_by_parent = array();
        
        foreach ($this as $item) {
            $pid = $item[$parent_field];
            if (!isset($index_by_parent[$pid])) {
                $index_by_parent[$pid] = fx::collection();
                $index_by_parent[$pid]->is_sortable = $this->is_sortable;
            }
            $index_by_parent[$pid] []= $item;
        }
        foreach ($this as $item) {
            if (isset($index_by_parent[$item[$id_field]])) {
                $item[$children_field] = $index_by_parent[$item[$id_field]];
                $this->find_remove(
                    $id_field,
                    $index_by_parent[$item[$id_field]]->get_values($id_field)
                );
            } else {
                $item[$children_field] = null;
            }
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
    
    public function keys() {
        return array_keys($this->data);
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

<?php

abstract class fx_essence implements ArrayAccess {

    // ссылка на объект класса fx_data_
    protected $finder;
    // тип сущности
    protected $essence_type;
    // значения полей
    protected $data;
    // набор полей, которые изменились
    protected $modified = array();
    protected $modified_data = array();
    // первичный ключ
    protected $pk = 'id';
    protected $inherit = false;
    protected $parent_field = 'parent_id';
    protected $inherit_fields = array();
    
    protected $validate_errors = array();
    

    public function __construct($input = array()) {
        if (isset($input['data']))
            $this->data = $input['data'];
        if (isset($input['finder']) && $input['finder'] instanceof fx_data ) {
            $this->finder = $input['finder'];
            $this->pk = $this->finder->get_pk();
        }
        
        $this->essence_type = str_replace("fx_", "", get_class($this));

        return $this;
    }

    public function save($dont_log = false, $action = 'update') { 
        $this->_before_save();
        // update
        if ($this->data[$this->pk] && $action === 'update') {
            $this->_before_update();
            // обновляем только изменившиеся поля
                        $data = array();
            foreach ($this->modified as $v) {
                $data[$v] = $this->data[$v];
            }
                
            $this->finder->update($data, array($this->pk => $this->data[$this->pk]));
            $this->_after_update();
            if (!$dont_log)
                $this->_add_history_operation('update', $data);
        } // insert
        else {
            $this->_before_insert();
            $id = $this->finder->insert($this->data);
            $this->data['id'] = $id;
            $this->_after_insert();
            if (!$dont_log)
                $this->_add_history_operation('add', $this->data);
        }

        return $this;
    }
    
    protected function _before_save () {
        
    }

    /**
     * Получить свойство данных или весь набор свойств
     * @param strign $prop_name
     * @return mixed
     */
    public function get($prop_name = null) {
        if ($prop_name) {
            if (isset($this->data[$prop_name])) {
                return $this->data[$prop_name];
            } else {
                dev_log($this, $prop_name);
                throw new Exception("Class: " . get_class($this) . ", undefined item: " . $prop_name);
            }
        }
        return $this->data;
    }

    public function set($item, $value = '') {
        if ( is_array($item) ) {
            foreach ( $item as $k => $v ) {
                $this->set($k, $v);
            }
            return $this;
        }
        if (!isset($this->modified_data[$item]))
            $this->modified_data[$item] = $this->data[$item];

        $this->data[$item] = $value;
        $this->modified[] = $item;

        return $this;
    }

    public function get_id() {
        return $this->data[$this->pk];
    }

    public function delete( $dont_log = false ) {
        $this->_before_delete();
        $this->finder->delete($this->pk, $this->data[$this->pk]);
        $this->modified_data = $this->data;
        $this->_after_delete();
        if (!$dont_log) $this->_add_history_operation('delete');
    }

    public function unchecked() {
        return $this->set('checked', 0)->save();
    }

    public function checked() {
        return $this->set('checked', 1)->save();
    }
    
    public function validate () {
        return true;
    }
    
    public function get_validate_error () {
        return $this->validate_errors;
    }

    public function __toString() {
        $res = '';
        foreach ($this->data as $k => $v)
            $res .= "$k = $v " . PHP_EOL;
        return $res;
    }

    protected function _add_history_operation($type, $data = array()) {
        fx_history::add_operation($type, str_replace('fx_data_', '', get_class($this->finder)), $this->data[$this->pk], $this->modified_data, $data);
    }
    
    protected function _before_insert () {
        return false;
    }
    
    protected function _after_insert () {
        return false;
    }
    
    protected function _before_update () {
        return false;
    }
    
    protected function _after_update () {
        return false;
    }
    
    protected function _before_delete () {
        return false;
    }
    
    protected function _after_delete () {
        return false;
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
    
    public function get_type() {
        return $this->essence_type;
    }

}
?>
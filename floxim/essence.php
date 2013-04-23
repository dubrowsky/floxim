<?php

abstract class fx_essence implements ArrayAccess {

    // ссылка на объект класса fx_data_
    //protected $finder;
    // значения полей
    protected $data;
    // набор полей, которые изменились
    protected $modified = array();
    protected $modified_data = array();
    
    protected $validate_errors = array();
    
    protected function _get_finder() {
        return fx::data($this->get_type());
    }

    public function __construct($input = array()) {
        if (isset($input['data'])) {
            $this->data = $input['data'];
        }
        return $this;
    }

    public function save($dont_log = false, $action = 'update') {
        $this->_before_save();
        $pk = $this->_get_pk();
        // update
        if ($this->data[$pk] && $action === 'update') {
            $this->_before_update();
            // обновляем только изменившиеся поля
            $data = array();
            foreach ($this->modified as $v) {
                $data[$v] = $this->data[$v];
            }
                
            $this->_get_finder()->update($data, array($pk => $this->data[$pk]));
            $this->_after_update();
            if (!$dont_log)
                $this->_add_history_operation('update', $data);
        } // insert
        else {
            $this->_before_insert();
            $id = $this->_get_finder()->insert($this->data);
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
            if (array_key_exists($prop_name, $this->data)) {
                return $this->data[$prop_name];
            }
            dev_log($this, $prop_name);
            throw new Exception("Class: " . get_class($this) . ", undefined item: " . $prop_name);
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
        return $this->data[$this->_get_pk()];
    }

    public function delete( $dont_log = false ) {
        $pk = $this->_get_pk();
        $this->_before_delete();
        $this->_get_finder()->delete($pk, $this->data[$pk]);
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
    
    protected function _get_pk() {
        return 'id';
    }

    public function __toString() {
        $res = '';
        foreach ($this->data as $k => $v)
            $res .= "$k = $v " . PHP_EOL;
        return $res;
    }

    protected function _add_history_operation($type, $data = array()) {
        //fx_history::add_operation($type, str_replace('fx_data_', '', get_class($this->_get_finder())), $this->data[$this->pk], $this->modified_data, $data);
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
        return str_replace("fx_", "", get_class($this));
    }
    
    /*
     * Добавить мета-данные для редактирования с фронта
     * @param string $html html-код рекорда
     * @return string строка с добавленными мета-данными
     */
    public function add_template_record_meta($html) {
        return $html;
    }

}
?>
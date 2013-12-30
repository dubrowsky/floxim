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
    
    protected function get_finder() {
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
            if ($this->validate() === false) {
                $this->throw_invalid();
            }
            // обновляем только изменившиеся поля
            $data = array();
            foreach ($this->modified as $v) {
                $data[$v] = $this->data[$v];
            }
            $this->get_finder()->update($data, array($pk => $this->data[$pk]));
            $this->_save_multi_links();
            $this->_after_update();
        } // insert
        else {
            $this->_before_insert();
            if ($this->validate() === false) {
                $this->throw_invalid();
            }
            $id = $this->get_finder()->insert($this->data);
            $this->data['id'] = $id;
            $this->_save_multi_links();
            $this->_after_insert();
        }
        $this->_after_save();

        return $this;
    }
    
    protected function throw_invalid() {
        throw new Exception(
                "Unable to save essence \"".$this->get_type()."\": ".
                join("<br />", $this->validate_errors)
        );
    }


    /*
     * Сохраняет поля-ссылки, определяется в fx_data_content
     */
    protected function _save_multi_links() {
        
    }
    
    protected function _before_save () {
        
    }
    
    protected function _after_save() {
        
    }

    /**
     * Получить свойство данных или весь набор свойств
     * @param strign $prop_name
     * @return mixed
     */
    public function get($prop_name = null) {
        if ($prop_name) {
            return $this->offsetGet($prop_name);
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
        $this->offsetSet($item, $value);
        return $this;
    }

    public function get_id() {
        return $this->data[$this->_get_pk()];
    }

    public function delete( $dont_log = false ) {
        $pk = $this->_get_pk();
        $this->_before_delete();
        $this->get_finder()->delete($pk, $this->data[$pk]);
        $this->modified_data = $this->data;
        $this->_after_delete();
        if (!$dont_log) {
            $this->_add_history_operation('delete');
        }
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
        //fx_history::add_operation($type, str_replace('fx_data_', '', get_class($this->get_finder())), $this->data[$this->pk], $this->modified_data, $data);
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
    public function offsetGet($offset) {
        if (isset($this->data[$offset])) {
            return $this->data[$offset];
        }
        if ($offset == 'id') {
            return null;
        }
        /**
         * Например для $post['tags'], где tags - поле-мультисвязь
         * Если связанные не загружены, просим файндер их загрузить
         */
         
         $finder = $this->get_finder();
         $rels = $finder->relations();
        
        if (!isset($rels[$offset])) {
            return null;
        }
        $finder->add_related($offset, new fx_collection(array($this)));
        if (!isset($this->data[$offset])) {
            return null;
        }
        $this->modified_data[$offset] = clone $this->data[$offset];
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value) {
        // ставим modified | modified_data только если существовал ключик
        // чтобы при первой догрузке полей-связей они не помечались как обновленные
        
        $offset_exists = array_key_exists($offset, $this->data);
        if ($offset_exists && $this->data[$offset] === $value) {
            return;
        }
        
        if (!is_object($value) || $offset_exists) {
            if (!isset($this->modified_data[$offset])) {
                $this->modified_data[$offset] = $this->data[$offset];
            }
            $this->modified[] = $offset;
        }
        $this->data[$offset] = $value;
    }

    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->data[$offset]);
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
    
    public function is_modified($field = null) {
        if ($field === null) {
            return count($this->modified) > 0;
        }
        return is_array($this->modified) && in_array($field, $this->modified);
    }

}
?>
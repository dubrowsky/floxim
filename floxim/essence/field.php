<?php

/* $Id: field.php 8536 2012-12-17 10:55:01Z myasin $ */
defined("FLOXIM") || die("Unable to load file.");

class fx_field extends fx_essence {

    protected $name, $format, $type_id, $description;

    public static function get_type_by_id($id) {

        static $res = array();
        if (empty($res)) {
            $types = fx_data::optional('datatype')->get_all();
            foreach ($types as $v) {
                $res[$v['id']] = $v['name'];
            }
        }

        return $id ? $res[$id] : $res;
    }

    public function __construct($input = array()) {
        parent::__construct($input);

        $this->name = $this['name'];
        $this->format = $this['format'];
        $this->type_id = $this['type'];
        $this->type = fx_field::get_type_by_id($this->type_id);
        $this->description = $this['description'];

        $this->_edit_jsdata = array('type' => 'input');
    }

    public function get_type() {
        return $this->type;
    }

    public function get_type_id() {
        return $this->type_id;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_description() {
        return $this->description;
    }

    public function is_not_null() {
        return $this['not_null'];
    }

    public function validate() {
        $fx_core = fx_core::get_object();
        $res = true;

        if (!$this['name']) {
            $this->validate_errors[] = array('field' => 'name', 'text' => 'Укажите название поля');
            $res = false;
        }
        if ($this['name'] && !preg_match("/^[a-z][a-z0-9_]*$/i", $this['name'])) {
            $this->validate_errors[] = array('field' => 'name', 'text' => 'Имя поля может содержать только латинские буквы, цифры и знак подчеркивания');
            $res = false;
        }

        $modified = $this->modified_data['name'] && $this->modified_data['name'] != $this->data['name'];
        if ($this['component_id'] && ( $modified || !$this['id'])) {
            if ($fx_core->util->is_mysql_keyword($this->data['name'])) {
                $this->validate_errors[] = array('field' => 'name', 'text' => 'Данное поле зарезервированно');
                $res = false;
            }
            if ($fx_core->db->column_exists($this->get_table(), $this->data['name'])) {
                $this->validate_errors[] = array('field' => 'name', 'text' => 'Такое поле уже существует');
                $res = false;
            }
        }


        if (!$this['description']) {
            $this->validate_errors[] = array('field' => 'description', 'text' => 'Укажите описание поля');
            $res = false;
        }

        return $res;
    }

    protected function get_table() {
        return fx::data('component')->get_by_id($this['component_id'])->get_content_table();
    }

    protected function _after_insert() {
        if ($this['component_id']) {
            $type = $this->get_sql_type();
            fx::db()->query("ALTER TABLE `{{".$this->get_table()."}}`
                ADD COLUMN `".$this->name."` ".$type);
        }
    }

    protected function _after_update() {
        if ($this['component_id']) {
            $type = self::get_sql_type_by_type($this->data['type']);

            if ($this->modified_data['name'] && $this->modified_data['name'] != $this->data['name']) {
                fx_core::get_object()->db->query("ALTER TABLE `{{".$this->get_table()."}}` 
                CHANGE `".$this->modified_data['name']."` `".$this->data['name']."` ".$type);
            } else if ($this->modified_data['type'] && $this->modified_data['type'] != $this->data['type']) {
                fx_core::get_object()->db->query("ALTER TABLE `{{".$this->get_table()."}}`
                MODIFY `".$this->data['name']."` ".$type);
            }
        }
    }

    protected function _after_delete() {
        if ($this['component_id']) {
            fx_core::get_object()->db->query("ALTER TABLE `{{".$this->get_table()."}}` DROP COLUMN `".$this->name."`");
        }
    }

    /* -- for admin interface -- */

    public function format_settings() {
        return array();
    }

    public function get_sql_type() {
        return "TEXT";
    }

    public function check_rights() {
        if ($this['type_of_edit'] <= 1) {
            return true;
        }
        if ($this['type_of_edit'] == 2) {
            $fx_core = fx_core::get_object();
            $user = $fx_core->env->get_user();
            return $user && $user->perm()->is_supervisor();
        }

        return false;
    }

    static public function get_sql_type_by_type($type_id) {
        $type = self::get_type_by_id($type_id);
        $classname = "fx_field_".$type;

        $field = new $classname();
        return $field->get_sql_type();
    }

}

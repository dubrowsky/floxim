<?php

/* $Id: field.php 8536 2012-12-17 10:55:01Z myasin $ */
defined("FLOXIM") || die("Unable to load file.");

class fx_field extends fx_essence {

    protected $name, $format, $type_id, $description;
    
    const FIELD_STRING = 1;
    const FIELD_INT = 2;
    const FIELD_TEXT = 3;
    const FIELD_SELECT = 4;
    const FIELD_BOOL = 5;
    const FIELD_FILE = 6;
    const FIELD_FLOAT = 7;
    const FIELD_DATETIME = 8;
    const FIELD_COLOR = 9;
    const FIELD_INFOBLOCK = 10;
    const FIELD_IMAGE = 11;
    const FIELD_MULTISELECT = 12;
    const FIELD_LINK = 13;
    const FIELD_MULTILINK = 14;
    
    const EDIT_ALL = 1;
    const EDIT_ADMIN = 2;
    const EDIT_NONE = 3;
    
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

    public function get_type($full = true) {
        return ($full ? 'field_' : '').$this->type;
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
        $res = true;

        if (!$this['name']) {
            $this->validate_errors[] = array('field' => fx_lang('name'), 'text' => fx_lang('Укажите название поля'));
            $res = false;
        }
        if ($this['name'] && !preg_match("/^[a-z][a-z0-9_]*$/i", $this['name'])) {
            $this->validate_errors[] = array('field' => 'name', 'text' => fx_lang('Имя поля может содержать только латинские буквы, цифры и знак подчеркивания'));
            $res = false;
        }

        $modified = $this->modified_data['name'] && $this->modified_data['name'] != $this->data['name'];

        if ($this['component_id'] && ( $modified || !$this['id'])) {
            if (fx::util()->is_mysql_keyword($this->data['name'])) {
                $this->validate_errors[] = array('field' => 'name', 'text' => fx_lang('Данное поле зарезервировано'));
                $res = false;
            }
            /// Правим тут
            $component = fx::data('component')->where('id',$this['component_id'])->one();
            $chain = $component->get_chain();
            foreach ( $chain as $c_level ) {

                if ( fx::db()->column_exists( $c_level->get_content_table(), $this->data['name']) ) {
                    $this->validate_errors[] = array('field' => 'name', 'text' => fx_lang('Такое поле уже существует'));
                    $res = false;
                }
            }
            if (fx::db()->column_exists($this->get_table(), $this->data['name'])) {
                $this->validate_errors[] = array('field' => 'name', 'text' => fx_lang('Такое поле уже существует'));
                $res = false;
            }
        }


        if (!$this['description']) {
            $this->validate_errors[] = array('field' => 'description', 'text' => fx_lang('Укажите описание поля'));
            $res = false;
        }

        return $res;
    }

    protected function get_table() {
        return fx::data('component')->where('id',$this['component_id'])->one()->get_content_table();
    }

    protected function _after_insert() {
        if ($this['component_id']) {
            $type = $this->get_sql_type();
            if ($type) {
                fx::db()->query("ALTER TABLE `{{".$this->get_table()."}}`
                    ADD COLUMN `".$this->name."` ".$type);
            }
        }
    }

    protected function _after_update() {
        if ($this['component_id']) {
            $type = self::get_sql_type_by_type($this->data['type']);
            if ($type) {
                if ($this->modified_data['name'] && $this->modified_data['name'] != $this->data['name']) {
                    fx::db()->query("ALTER TABLE `{{".$this->get_table()."}}` 
                    CHANGE `".$this->modified_data['name']."` `".$this->data['name']."` ".$type);
                } else if ($this->modified_data['type'] && $this->modified_data['type'] != $this->data['type']) {
                    fx::db()->query("ALTER TABLE `{{".$this->get_table()."}}`
                    MODIFY `".$this->data['name']."` ".$type);
                }
            }
        }
    }

    protected function _after_delete() {
        if ($this['component_id']) {
            if (self::get_sql_type_by_type($this->data['type'])) {
                fx::db()->query("ALTER TABLE `{{".$this->get_table()."}}` DROP COLUMN `".$this->name."`");
            }
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
        if ($this['type_of_edit'] == fx_field::EDIT_ALL || empty($this['type_of_edit'])) {
            return true;
        }
        if ($this['type_of_edit'] == fx_field::EDIT_ADMIN) {
            $user = fx::env()->get_user();
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

<?php

/* $Id: component.php 8576 2012-12-27 15:14:06Z myasin $ */
defined("FLOXIM") || die("Unable to load file.");

class fx_component extends fx_essence {
    
    public function get_content_table() {
        return $this['keyword'] == 'content' ? $this['keyword'] : 'content_'.$this['keyword'];
    }
    
    public function get_chain() {
        $chain = array($this);
        $c_pid = $this->get('parent_id');
        while ($c_pid != 0) {
            $c_parent = fx::data('component', $c_pid);
            $chain []= $c_parent;
            $c_pid = $c_parent['parent_id'];
        }
        return array_reverse($chain);
    }

    protected $_class_id;

    public function __construct($input = array()) {
        parent::__construct($input);

        $this->_class_id = $this->data['id'];
    }

    public function validate() {
        $res = true;

        if (!$this['name']) {
            $this->validate_errors[] = array('field' => 'name', 'text' => fx_lang('Название компонента не может быть пустым'));
            $res = false;
        }

        if (!$this['keyword']) {
            $this->validate_errors[] = array('field' => 'keyword', 'text' => fx_lang('Укажите keyword компонента'));
            $res = false;
        }

        if ($this['keyword'] && !preg_match("/^[a-z][a-z0-9-]*$/i", $this['keyword'])) {
            $this->validate_errors[] = array('field' => 'keyword', 'text' => fx_lang('Keyword может содержать только буквы, цифры, символы "дефис" и "подчеркивание"'));
            $res = false;
        }

        if ($this['keyword']) {
            $components = fx::data('component')->get_all();
            foreach ($components as $component) {
                if ($component['id'] != $this['id'] && $component['keyword'] == $this['keyword']) {
                    $this->validate_errors[] = array('field' => 'keyword', 'text' => fx_lang('Такой keyword уже используется компоненте') . ' "'.$component['name'].'"');
                    $res = false;
                }
            }
        }


        return $res;
    }

    protected $_stored_fields = null;
    public function fields() {
        if (!$this->_stored_fields) {
            $this->_stored_fields = fx::data('field')->get_by_component($this->_class_id);
        }
        return $this->_stored_fields;
    }
    
    public function all_fields() {
        $fields = new fx_collection();
        foreach ($this->get_chain() as $component) {
            $fields->concat($component->fields());
        }
        return $fields;
    }

    public function get_sortable_fields() {
        //$this->_load_fields();

        $result = array();

        $result['created'] = fx_lang('Дата создания');
        $result['id'] = 'ID';
        $result['priority'] = fx_lang('Приоритет');


        foreach ($this->fields() as $v) {
            $result[$v['name']] = $v['description'];
        }

        return $result;
    }

    /**
     * Возвращает относительный пункт до иконки компонента
     * Определяется:
     * 1. по полю icon
     * 2. по наличию icon.png в директории компонента
     * 3. по умолчанию
     * @return string
     */
    public function get_icon() {
        if ($this['icon']) {
            return fx::config()->ADMIN_TEMPLATE.'icons/'.$this['icon'].'.png';
        }

        $fullpath = fx::config()->COMPONENT_FOLDER.$this['keyword'].'/icon.png';
        if (file_exists($fullpath)) {
            return fx::config()->HTTP_COMPONENT_PATH.$this['keyword'].'/icon.png';
        }

        return fx::config()->ADMIN_TEMPLATE.'icons/component.png';
    }

    public function is_user_component() {
        return fx_core::get_object()->get_settings('user_component_id', 'auth') == $this['id'];
    }

    protected function _after_insert() {
        $this->create_content_table();
    }

    protected function create_content_table() {
        $sql = "DROP TABLE IF  EXISTS `{{content_".$this['keyword']."}}`;
            CREATE TABLE IF NOT EXISTS `{{content_".$this['keyword']."}}` (
            `id` int(11) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
        fx::db()->query($sql);
    }

    protected function _before_delete() {
        $this->delete_fields();
        $this->delete_content_table();
        $this->delete_infoblocks();
    }

    protected function delete_fields() {
        foreach ($this->fields() as $field) {
            $field->delete();
        }
    }

    protected function delete_content_table() {
        $sql = "DROP TABLE `{{content_".$this['keyword']."}}`";
        fx::db()->query($sql);
    }

    protected function delete_infoblocks() {
        return;
        $infoblocks = fx::data('infoblock')->get_all('essence_id', $this['id']);
        foreach ($infoblocks as $infoblock) {
            $infoblock->delete();
        }
    }
}

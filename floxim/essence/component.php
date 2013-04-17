<?php

/* $Id: component.php 8576 2012-12-27 15:14:06Z myasin $ */
defined("FLOXIM") || die("Unable to load file.");

class fx_component extends fx_essence {

    protected $_class_id;

    public function __construct($input = array()) {
        parent::__construct($input);

        $this->_class_id = $this->data['id'];
    }

    public function validate() {
        $res = true;

        if (!$this['name']) {
            $this->validate_errors[] = array('field' => 'name', 'text' => 'Название компонента не может быть пустым');
            $res = false;
        }

        if (!$this['keyword']) {
            $this->validate_errors[] = array('field' => 'keyword', 'text' => 'Укажите keyword компонента');
            $res = false;
        }

        if ($this['keyword'] && !preg_match("/^[a-z][a-z0-9-]*$/i", $this['keyword'])) {
            $this->validate_errors[] = array('field' => 'keyword', 'text' => 'Keyword может сожержать только буквы и цифры');
            $res = false;
        }

        if ($this['keyword']) {
            $components = fx::data('component')->get_all();
            foreach ($components as $component) {
                if ($component['id'] != $this['id'] && $component['keyword'] == $this['keyword']) {
                    $this->validate_errors[] = array('field' => 'keyword', 'text' => 'Такой keyword уже используется компоненте "'.$component['name'].'"');
                    $res = false;
                }
            }
        }


        return $res;
    }

    public function fields() {
        return fx::data('field')->get_by_component($this->_class_id);
    }

    public function get_sortable_fields() {
        //$this->_load_fields();

        $result = array();

        $result['created'] = 'Дата создания';
        $result['id'] = 'ID';
        $result['priority'] = 'Приоритет';


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

    public function get_table_name() {
        return $this->is_user_component() ? 'user' : 'content_'.$this['keyword'];
    }

    protected function _after_insert() {
        $this->create_content_table();
    }

    protected function create_content_table() {
        $sql = "DROP TABLE IF  EXISTS `{{content_".$this['keyword']."}}`;
            CREATE TABLE IF NOT EXISTS `{{content_".$this['keyword']."}}` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `parent_id` int(11) NOT NULL DEFAULT '0',
            `user_id` int(11) NOT NULL DEFAULT '0',
            `infoblock_id` int(11) NOT NULL DEFAULT '0',
            `priority` int(11) NOT NULL DEFAULT '0',
            `checked` tinyint(4) NOT NULL DEFAULT '1',
            `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
        fx_core::get_object()->db->query($sql);
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

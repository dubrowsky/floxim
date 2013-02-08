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
        $fx_core = fx_core::get_object();
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

    public function create_dir() {
        fx_core::get_object()->files->mkdir($this->get_path());
    }

    public function get_path() {
        return fx::config()->HTTP_COMPONENT_PATH.$this['keyword'].'/';
    }

    public function load_tpl_object($template = null) {
        $fx_core = fx_core::get_object();
        $is_mobile = false;

        $classname = 'ctpl_'.$this->data['keyword'];

        if (!$template) {
            $t = fx::data('ctpl')->get('component_id', $this['id'], 'keyword', 'main', 'parent_id', 0);
            $keyword = $t['keyword'];
            $classname .= '_'.$keyword;
        } else if ($template === 'select') {
            $t = fx::data('ctpl')->get('component_id', $this['id'], 'type', 'select');
            if (!$t) {
                return $this->load_tpl_object();
            }
            $classname .= '_select';
        } else {
            $t = fx::data('ctpl')->get_by_id($template);
            $keyword = $t['keyword'];
            $classname .= '_'.$keyword;
        }

        if ($is_mobile) {
            $mobile_ctpl = fx::data('ctpl')->get('parent_id', $t['id'], 'device', 'mobile');
            if ($mobile_ctpl['id']) {
                $classname .= '_mobile';
            }
        }

        try {
            $obj = new $classname();
        } catch (Exception $e) {
            die("Unable to load component <b>".$this->data['keyword']."</b>, template <b>$keyword</b>");
        }

        $obj->set_vars('fx_core', $fx_core);
        $obj->set_vars('fx_path', fx::config()->HTTP_COMPONENT_PATH.$this->data['keyword'].'/');
        $obj->set_vars('fx_path_css', fx::config()->HTTP_COMPONENT_PATH.$this->data['keyword'].'/'.'css/');
        $obj->set_vars('fx_path_js', fx::config()->HTTP_COMPONENT_PATH.$this->data['keyword'].'/'.'js/');

        return $obj;
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
            `keyword` varchar(255) DEFAULT NULL,
            `user_id` int(11) NOT NULL DEFAULT '0',
            `infoblock_id` int(11) NOT NULL DEFAULT '0',
            `priority` int(11) NOT NULL DEFAULT '0',
            `checked` tinyint(4) NOT NULL DEFAULT '1',
            `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `seo_h1` text,
            `seo_title` varchar(255) DEFAULT NULL,
            `seo_keywords` varchar(255) DEFAULT NULL,
            `seo_description` text,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
        fx_core::get_object()->db->query($sql);
    }

    protected function _before_delete() {
        $this->delete_ctpls();
        $this->delete_fields();
        $this->delete_content_table();
        $this->delete_infoblocks();
        $this->delete_dir();
    }

    protected function delete_ctpls() {
        $ctpls = fx::data('ctpl')->get_by_component($this['id']);
        foreach ($ctpls as $ctpl) {
            $ctpl->delete();
        }
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

    protected function delete_dir() {
        fx_core::get_object()->files->rm($this->get_path());
    }

}

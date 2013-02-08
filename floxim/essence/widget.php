<?php

/* $Id: widget.php 8536 2012-12-17 10:55:01Z myasin $ */
defined("FLOXIM") || die("Unable to load file.");

class fx_widget extends fx_essence {

    public function load_tpl_object() {
        $fx_core = fx_core::get_object();
        $file = fx::config()->WIDGET_FOLDER.$this->data['keyword'].'/'.'main.tpl.php';

        if (file_exists($file)) {
            require_once $file;
        } else {
            die("widget file: ".$file);
        }

        $classname = 'widget_'.$this->data['keyword'];
        $tpl = new $classname();

        return $tpl;
    }

    public function fields() {
        return fx::data('field')->get_all('widget_id', $this['id']);
    }

    public function get_path() {
        return $this->get_folder_path().'main.tpl.php';
    }

    public function get_folder_path() {
        return fx::config()->HTTP_WIDGET_PATH.$this['keyword'].'/';
    }

    public function validate() {
        $fx_core = fx_core::get_object();
        $res = true;

        if (!$this['name']) {
            $this->validate_errors[] = array('field' => 'name', 'text' => 'Укажите название виджета');
            $res = false;
        }

        if (!$this['keyword']) {
            $this->validate_errors[] = array('field' => 'keyword', 'text' => 'Укажите keyword виджета');
            $res = false;
        }

        if ($this['keyword'] && !preg_match("/^[a-z][a-z0-9-]*$/i", $this['keyword'])) {
            $this->validate_errors[] = array('field' => 'keyword', 'text' => 'Keyword может сожержать только буквы и цифры');
            $res = false;
        }

        if ($this['keyword']) {
            $widgets = fx::data('widget')->get_all();
            foreach ($widgets as $widget) {
                if ($widget['id'] != $this['id'] && $widget['keyword'] == $this['keyword']) {
                    $this->validate_errors[] = array('field' => 'keyword', 'text' => 'Такой keyword уже используется в виджете "'.$widget['name'].'"');
                    $res = false;
                }
            }
        }


        return $res;
    }

    /**
     * Возвращает относительный пункт до иконки виджета
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

        $fullpath = fx::config()->WIDGET_FOLDER.$this['keyword'].'/icon.png';
        if (file_exists($fullpath)) {
            return fx::config()->HTTP_WIDGET_PATH.$this['keyword'].'/icon.png';
        }

        return fx::config()->ADMIN_TEMPLATE.'icons/widget.png';
    }

    public function _before_delete() {
        foreach ($this->fields() as $field) {
            $field->delete();
        }
        fx_core::get_object()->files->rm($this->get_folder_path());
    }

}


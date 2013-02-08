<?php

/* $Id: ctpl.php 8536 2012-12-17 10:55:01Z myasin $ */
defined("FLOXIM") || die("Unable to load file.");

class fx_ctpl extends fx_essence {

    public function fields() {
        return fx::data('field')->get_all('ctpl_id', $this['id']);
    }

    public function get_component() {
        return fx::data('component')->get_by_id($this['component_id']);
    }

    public function create_file() {
        $fx_core = fx_core::get_object();
        $path = $this->get_path();
        $content = $this->initial_file_content();
        $fx_core->files->writefile($path, $content);
    }

    public function get_path() {
        $path = $this->get_component()->get_path();
        $path .= $this['keyword'];
        if ($this['device'] == 'mobile') $path .= '_mobile';
        return $path.'.tpl.php';
    }

    public function get_class_name() {
        $classname = "ctpl_".$this->get_component()->get('keyword')."_".$this['keyword'];
        if ($this['device'] == 'mobile') $classname .= '_mobile';
        return $classname;
    }

    public function get_default_action() {
        return $this['action']['default'] ? $this['action']['default'] : 'index';
    }

    public function get_available_actions() {
        $actions = $this['action']['enabled'];
        if (!$actions) $actions = array('index', 'add', 'search');
        return $actions;
    }

    public function get_sort_type() {
        return $this['sort']['type'] ? $this['sort']['type'] : 'manual';
    }

    public function get_access($item = '') {
        $items = fx_rights::get_user_types();
        $types = fx_rights::get_rights_types();
        $access = $this['access'];

        // права по умолчанию
        foreach ($types as $type) {
            if (!in_array($access[$type], $items)) {
                $access[$type] = $type == 'read' ? 'all' : 'auth';
            }
        }

        return $item ? $access[$item] : $access;
    }

    public function is_default() {
        return (bool) ($this['keyword'] == 'main');
    }

    public function validate() {
        $fx_core = fx_core::get_object();
        $res = true;

        if (!$this['component_id']) {
            $this->validate_errors[] = array('field' => 'name', 'text' => 'Компонент не найден');
            $res = false;
        }

        if (!$this['name']) {
            $this->validate_errors[] = array('field' => 'name', 'text' => 'Название шаблона не может быть пустым');
            $res = false;
        }

        if (!$this['keyword']) {
            $this->validate_errors[] = array('field' => 'keyword', 'text' => 'Укажите keyword шаблона');
            $res = false;
        }

        if ($this['keyword'] && !preg_match("/^[a-z][a-z0-9-]*$/i", $this['keyword'])) {
            $this->validate_errors[] = array('field' => 'keyword', 'text' => 'Keyword может сожержать только буквы и цифры');
            $res = false;
        }

        if ($this['keyword']) {
            $ctpls = fx::data('ctpl')->get_by_component($this['component_id']);
            foreach ($ctpls as $ctpl) {
                if ($ctpl['id'] != $this['id'] && $ctpl['keyword'] == $this['keyword']) {
                    $this->validate_errors[] = array('field' => 'keyword', 'text' => 'Такой keyword уже используется в шаблоне "'.$ctpl['name'].'"');
                    $res = false;
                }
            }
        }

        return $res;
    }

    protected function initial_file_content() {
        $extend = $this['keyword'] == 'main' ? 'fx_tpl_component' : 'ctpl_'.$this->get_component()->get('keyword').'_main';
        $text = "<?php\n";
        $text .= "class ".$this->get_class_name()." extends ".$extend." {\n";
        $text .= "}\n";

        return $text;
    }

    protected function _before_delete() {
        $path = $this->get_path();
        fx_core::get_object()->files->rm($path);
    }

}

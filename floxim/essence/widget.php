<?php
class fx_widget extends fx_essence {

    public function get_folder_path() {
        return fx::config()->HTTP_WIDGET_PATH.$this['keyword'].'/';
    }

    public function validate() {
        $res = true;

        if (!$this['name']) {
            $this->validate_errors[] = array('field' => 'name', 'text' => fx::lang('Укажите название виджета','system'));
            $res = false;
        }

        if (!$this['keyword']) {
            $this->validate_errors[] = array('field' => 'keyword', 'text' => fx::lang('Укажите keyword виджета','system'));
            $res = false;
        }

        if ($this['keyword'] && !preg_match("/^[a-z][a-z0-9-]*$/i", $this['keyword'])) {
            $this->validate_errors[] = array('field' => 'keyword', 'text' => fx::lang('Keyword может сожержать только буквы и цифры','system'));
            $res = false;
        }

        if ($this['keyword']) {
            $widgets = fx::data('widget')->get_all();
            foreach ($widgets as $widget) {
                if ($widget['id'] != $this['id'] && $widget['keyword'] == $this['keyword']) {
                    $this->validate_errors[] = array('field' => 'keyword', 'text' => fx::lang('Такой keyword уже используется в виджете','system') . ' "'.$widget['name'].'"');
                    $res = false;
                }
            }
        }

        return $res;
    }
}
<?php
class fx_widget extends fx_essence {

    public function get_folder_path() {
        return fx::config()->HTTP_WIDGET_PATH.$this['keyword'].'/';
    }

    public function validate() {
        $res = true;

        if (!$this['name']) {
            $this->validate_errors[] = array('field' => 'name', 'text' => fx::alang('Specify the name of the widget','system'));
            $res = false;
        }

        if (!$this['keyword']) {
            $this->validate_errors[] = array('field' => 'keyword', 'text' => fx::alang('Enter the keyword of widget','system'));
            $res = false;
        }

        if ($this['keyword'] && !preg_match("/^[a-z][a-z0-9-]*$/i", $this['keyword'])) {
            $this->validate_errors[] = array('field' => 'keyword', 'text' => fx::alang('Keyword can contain only letters and numbers','system'));
            $res = false;
        }

        if ($this['keyword']) {
            $widgets = fx::data('widget')->get_all();
            foreach ($widgets as $widget) {
                if ($widget['id'] != $this['id'] && $widget['keyword'] == $this['keyword']) {
                    $this->validate_errors[] = array('field' => 'keyword', 'text' => fx::alang('This keyword is used by widget','system') . ' "'.$widget['name'].'"');
                    $res = false;
                }
            }
        }

        return $res;
    }
}
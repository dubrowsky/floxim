<?php

class fx_field_multiselect extends fx_field_select {

    protected $_show_type = 'select';

    public function __construct($input = array()) {
        parent::__construct($input);

        if ($this->format['show'] == 'checkbox') {
            $this->_show_type = 'checkbox';
        }
    }

    public function content_procces(fx_content $content, fx_infoblock_content $infoblock = null, $hash_obj = null) {
        $fx_core = fx_core::get_object();
        $name = $this->name;

        $var = new fx_field_vars_multiselect($this->_get_values(), $this->_str_2_array($content[$name]));

        /**
         * Мультиселект работает немного не так, в отличии от большинства полей, так как он возвращает объект
         * в режиме редактирования вывод $name должен обрамиться тегом, но и $name->method() должен работать
         */
        $result = array();
        $result['f_'.$name.'_none'] = $var;
        $result['f_'.$name] = $var;

        if ($hash_obj) {
            $eit_in_place_info = $this->get_edit_jsdata($content);
            $hash = $fx_core->page->add_edit_field('f_'.$name, $eit_in_place_info, null, $hash_obj);

            $var->set_to_str_value('<'.$this->_wrap_tag.' class="'.$hash.'">'.$var->to_str().'</'.$this->_wrap_tag.'>');

            $result['f_'.$name.'_hash'] = $hash;
        } else {
            $result['f_'.$name.'_hash'] = '';
        }

        return $result;
    }

    /*public function get_edit_jsdata($content) {
        parent::get_edit_jsdata($content);

        $this->_edit_jsdata['multiple'] = true;
        $this->_edit_jsdata['value'] = $this->_str_2_array($content[$this->name]);

        return $this->_edit_jsdata;
    }*/

    public function get_js_field($content, $tname = 'f_%name%', $layer = '') {
        parent::get_js_field($content, $tname, $layer);

        $this->_js_field['type'] = $this->_show_type;
        $this->_js_field['multiple'] = true;
        $this->_js_field['value'] = $this->_str_2_array($content[$this->name]);

        return $this->_js_field;
    }

    public function get_savestring() {
        if (is_array($this->value)) {
            return join(',', $this->value);
        }

        return "";
    }

    public function post_save($content) {
        if ($this->format['source'] == 'manual') {
            return;
        }
        if (!($content_id = intval($content['id']))) {
            return;
        }

        $multiselect = fx_data::optional('multiselect');

        $old = $multiselect->get_all('content_id', $content_id, 'field_id', $this['id']);
        foreach ($old as $v) {
            $v->delete();
        }

        if (is_array($this->value)) {
            $data = array('field_id' => $this['id'], 'content_id' => $content_id);
            foreach ($this->value as $v) {
                $data['element_id'] = $v;
                $multiselect->create($data)->save();
            }
        }
    }

    public function set_value($value) {
        if (is_string($value)) {
            $value = $this->_str_2_array($value);
        }

        $this->value = $value;
    }

    protected function _str_2_array($str_value) {
        $res = explode(',', trim($str_value, ','));
        if (!$res || !$res[0]) return array();
        return $res;
    }

    protected function _get_values() {
        $fx_core = fx_core::get_object();

        switch ($this->format['source']) {
            case 'manual':
                if ($this->format['values'])
                        foreach ($this->format['values'] as $v) {
                        $values[$v['id']] = $v['value'];
                    }
                break;
            default:
                $values = $fx_core->classificator->get_by_table($this->format['table'])->elements('array');
                break;
        }

        return is_array($values) ? $values : array();
    }

    public function get_sql_type() {
        return "TEXT";
    }

    public function format_settings() {
        $fields = $this->format_settings_source();

        $show = array('select' => 'Select', 'checkbox' => 'Checkbox');
        $fields[] = array('name' => 'format[show]', 'label' => fx::lang('Показывать как','system'), 'values' => $show, 'type' => 'select', 'value' => $this->_show_type);
        return $fields;
    }

    public function get_input() {
        if ($this->_show_type == 'checkbox') {
            $input = '';
            foreach ($this->_get_values() as $k => $v) {
                $checked = in_array($k, (array) $this->value) ? " checked='checked'" : "";
                $input .= "<label><input type='checkbox' name='f_".$this->name."[".$k."]'".$checked." value='".$k."'/>$v</label>";
            }
        } else {


            $input = "<select multiple='multiple' class='".$this->get_css_class()."' name='f_".$this->name."[]'>\n";
            foreach ($this->_get_values() as $k => $v) {
                $selected = in_array($k, (array) $this->value) ? " selected='selected'" : "";
                $input .= "\t<option value='".$k."'".$selected.">".$v."</option>\n";
            }
            $input .= "</select>\n";
        }

        return $input;
    }

    public function get_search_cond($cond) {
        if (!$cond) {
            return false;
        }
        if (is_array($cond)) {
            die("ToDo");
        } else {
            $need_id = intval($cond);
            if ($need_id) {
                return "a.`id` IN ( SELECT `content_id` FROM `{{multiselect}}` WHERE `field_id` = '".$this['id']."' AND `element_id` = '".$need_id."' )  ";
            }
        }
    }

}

?>

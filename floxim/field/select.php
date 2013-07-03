<?php

class fx_field_select extends fx_field_baze {

    public function get_js_field($content, $tname = 'f_%name%', $layer = '') {
        if ($this->format['multiple']) {
            $tname .= '[]';
        }

        parent::get_js_field($content, $tname, $layer);

        $this->_js_field['values'] = $this->_get_values();

        if ($this->format['show'] == 'radio') {
            $this->_js_field['type'] = 'radio';
        }

        $this->_js_field['value'] = $content[$this->name];


        return $this->_js_field;
    }

    public function content_procces(fx_content $content, fx_infoblock_content $infoblock = null, $hash_obj = null) {
        $fx_core = fx_core::get_object();
        $name = $this->name;

        $elements = $this->_get_values();
        $id = $content[$name];
        $value = $elements[$content[$name]];

        $result = array();
        $result['f_'.$name.'_id'] = $id;
        $result['f_'.$name.'_none'] = $value;

        if ($hash_obj) {
            $eit_in_place_info = $this->get_edit_jsdata($content);
            $hash = $fx_core->page->add_edit_field('f_'.$name, $eit_in_place_info, null, $hash_obj);

            $result['f_'.$name] = '<'.$this->_wrap_tag.' class="'.$hash.'">'.$value.'</'.$this->_wrap_tag.'>';
            $result['f_'.$name.'_hash'] = $hash;
        } else {
            $result['f_'.$name] = $value;
            $result['f_'.$name.'_hash'] = '';
        }

        return $result;
    }

    public function format_settings() {
        $fields = $this->format_settings_source();
        return $fields;
    }

    protected function format_settings_source() {
        $fx_core = fx_core::get_object();

        $cl_values = array();
        foreach (fx::data('classificator')->get_all() as $cl) {
            $cl_values[$cl['table']] = $cl['name'];
        }

        $fields = array();

        $fields[] = array('id' => 'format[source]',
                'name' => 'format[source]',
                'label' => fx::lang('Источник','system'),
                'type' => 'select',
                'values' => array('classificator' => fx::lang('Классификатор','system'), 'manual' => fx::lang('Вручную','system')),
                'value' => $this['format']['source'] ? $this['format']['source'] : 'classificator');

        $fields[] = array('name' => 'format[table]', 'label' => fx::lang('Классификатор','system'), 'type' => 'select', 'values' => $cl_values, 'value' => $this['format']['table'], 'parent' => array('format[source]', 'classificator'), 'unactive' => true);
        $fields[] = array('name' => 'format[values]', 'label' => fx::lang('Элементы','system'), 'type' => 'set', 'tpl' => array(
                        array('name' => 'id', 'type' => 'string'),
                        array('name' => 'value', 'type' => 'string')
                ),
                'values' => $this['format']['values'] ? $this['format']['values'] : array(),
                'labels' => array('id', 'value'), 'parent' => array('format[source]', 'manual'), 'unactive' => true);

        return $fields;
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
                $values = fx::data('classificator')->get_by_table($this->format['table'])->elements('array');
                break;
        }

        if (!$this->is_not_null() && is_array($values)) {
            array_unshift($values, FX_FRONT_LISTS_CHOOSE);
        }

        return is_array($values) ? $values : array();
    }

    public function get_sql_type() {
        return "INT";
    }

    public function get_input() {
        $input = "<select class='".$this->get_css_class()."' name='f_".$this->name."'>\n";
        foreach ($this->_get_values() as $k => $v) {
            $selected = $this->value == $k ? " selected='selected'" : "";
            $input .= "\t<option value='".$k."'".$selected.">".$v."</option>\n";
        }
        $input .= "</select>\n";

        return $input;
    }
    
    
    public function get_search_cond ( $cond ) {
        $need_id = intval($cond);
        if ( $need_id ) {
            return "a.`".$this->name."` = '".$need_id."'";
        }
    }

}

?>

<?php

class fx_field_select extends fx_field_baze {

    public function get_js_field($content, $tname = 'f_%name%', $layer = '') {
        if ($this->format['multiple']) {
            $tname .= '[]';
        }

        parent::get_js_field($content, $tname, $layer);

        $values = $this->get_options();
        if (!$this->is_not_null() && is_array($values)) {
            $values = array_merge(
                array( array('', fx::lang('-- choose something --', 'system'))),
                $values
            );
        }
        $this->_js_field['values'] = $values;

        if ($this->format['show'] == 'radio') {
            $this->_js_field['type'] = 'radio';
        }

        $this->_js_field['value'] = $content[$this->name];
        return $this->_js_field;
    }

    public function format_settings() {
        $fields = array();

        $fields[] = array(
            'id' => 'format[source]',
            'name' => 'format[source]',
            'type' => 'hidden',
            'value' => 'manual'
        );

        $fields[] = array(
            'name' => 'format[values]', 
            'label' => fx::lang('Elements','system'), 
            'type' => 'set', 
            'tpl' => array(
                array('name' => 'id', 'type' => 'string'),
                array('name' => 'value', 'type' => 'string')
            ),
            'values' => $this['format']['values'] ? $this['format']['values'] : array(),
            'labels' => array('id', 'value')
        );

        return $fields;
    }

    public function get_options() {
        $values = array();
        if ($this->format['values']) {
            foreach ($this->format['values'] as $v) {
                $values[]= array($v['id'], $v['value']);
            }
        }
        return $values;
    }
    
    public function get_values() {
        $values = array();
        if ($this->format['values']) {
            foreach ($this->format['values'] as $v) {
                $values[$v['id']] = $v['value'];
            }
        }
        return $values;
    }

    public function get_sql_type() {
        return "VARCHAR (255)";
    }
}
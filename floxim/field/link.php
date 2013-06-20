<?php

class fx_field_link extends fx_field_baze {

    public function validate_value($value) {
        if (!parent::validate_value($value)) {
            return false;
        }
        if ($value && ($value != strval(intval($value)))) {
            $this->error = sprintf(FX_FRONT_FIELD_INT_ENTER_INTEGER, $this->get_description());
            return false;
        }
        return true;
    }

    public function get_sql_type (){
        return "INT";
    }
    
    public function format_settings() {
        $fields = array();
        
        $comp_values = fx::data('component')->get_select_values();
        $fields[] = array(
            'id' => 'format[target]',
            'name' => 'format[target]',
            'label' => fx_lang('Куда ссылается'),
            'type' => 'select',
            'values' => $comp_values,
            'value' => $this['format']['target'] ? $this['format']['target'] : 'classificator'
        );
        $fields[] = array(
            'id' => 'format[prop_name]',
            'name' => 'format[prop_name]',
            'label' => fx_lang('Ключ для свойства'),
            'value' => $this->get_prop_name()
        );
        $fields[]= array(
            'id' => 'format[is_parent]',
            'name' => 'format[is_parent]',
            'label' => fx_lang('Привязать значение к родителю'),
            'type' => 'checkbox',
            'value' => $this['format']['is_parent']
        );
        $fields[]= array(
            'id' => 'format[render_type]',
            'name' => 'format[render_type]',
            'label' => fx_lang('Способ отображения'),
            'type' => 'select',
            'values' => array(
                'select' => fx_lang('Выпадающий список'), 
                'livesearch' => fx_lang('Live Search')
            ),
            'value' => $this['format']['render_type']
        );
        return $fields;
    }
    
    public function get_prop_name() {
        if ($this['format']['prop_name']) {
            return $this['format']['prop_name'];
        }
        if ($this['name']) {
            return preg_replace("~_id$~", '', $this['name']);
        }
        return '';
    }
    
    public function get_js_field($content, $tname = 'f_%name%', $layer = '', $tab = '') {
        parent::get_js_field($content, $tname, $layer, $tab);
        $target_component = fx::data('component', $this['format']['target']);
        $target_content = 'content_'.$target_component['keyword'];
        $finder = fx::data($target_content);
        
        if ($this['format']['render_type'] == 'livesearch') {
            $this->_js_field['type'] = 'livesearch';
            $this->_js_field['datatype'] = $target_content;
            if ( ($c_val = $content[$this->name])) {
                $c_val_obj = $finder->where('id', $c_val)->one();
                if ($c_val_obj) {
                    $this->_js_field['value'] = array(
                        'id' => $c_val_obj['id'],
                        'name' => $c_val_obj['name']
                    );
                }
            }
            dev_log('link fld', $this, $content);
            return $this->_js_field;
        }
        // else:
        
        $this->_js_field['type'] = 'select';
        
        $finder->where('site_id', $content['site_id']);
        $val_items = $finder->all();
        $this->_js_field['values'] = $val_items->get_values('name', 'id');
        return $this->_js_field;
    }
    
    public function get_relation() {
        if (!$this['format']['target']) {
            return false;
        }
        return array(
            fx_data::BELONGS_TO,
            'content_'.fx::data('component', $this['format']['target'])->get('keyword'),
            $this['name']
        );
    }
}

?>
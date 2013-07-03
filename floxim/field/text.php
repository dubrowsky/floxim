<?php

class fx_field_text extends fx_field_baze {

    public function content_procces(fx_content $content, fx_infoblock_content $infoblock = null, $hash_obj = null) {
        $fx_core = fx_core::get_object();
        $name = $this->name;

        $value = $content[$name];
        if (!$this['format']['html']) {
            $value = htmlspecialchars($value);
        }
        if ($this['format']['nl2br']) {
            $value = nl2br($value);
        }

        $result = array();
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
    
    public function get_input() {
        return "<textarea class='".$this->get_css_class()."' type='text' name='f_".$this->name."'' value='' >".htmlspecialchars($this->value, ENT_QUOTES)."</textarea>";
    }

    public function get_js_field($content) {
        parent::get_js_field($content);

        if ($this['format']['html']) {
            $this->_js_field['wysiwyg'] = true;
        }

        return $this->_js_field;
    }

    public function format_settings() {
        $fields[] = array('type' => 'checkbox', 'name' => 'format[html]', 'label' => fx::lang('разрешить html-теги','system'), 'value' => $this['format']['html']);
        $fields[] = array('type' => 'checkbox', 'name' => 'format[nl2br]', 'label' => fx::lang('заменять перенос строки на br','system'), 'value' => $this['format']['nl2br']);
        return $fields;
    }

}

?>

<?php

class fx_field_string extends fx_field_baze {

    static public function show_optional($name, $desc, $type = 'text') {
    	$html = '<div class="fx_form_wrap fx_form_wrap_string">';
        $html .= '<label class="fx_form_label" for="inp_'.$name.'">'.$desc.':</label>';
        //$html .= '<div class="fx_form_div">';
        $html .= '<input name="'.$name.'" type="'.$type.'" id="inp_'.$name.'" />';
        $html .= '</div>';

        return $html;
    }

    public function format_settings() {
        $fields[] = array('type' => 'checkbox', 'name' => 'html', 'label' => 'разрешить html-теги');
        return $fields;
    }

    public function get_sql_type() {
        return "VARCHAR(255)";
    }
    
     public function get_search_cond ( $cond ) {
        if ( $cond ) {
            $cond = fx_core::get_object()->db->escape($cond);
            return "a.`".$this->name."` LIKE '%".$cond."%'";
        }
    }

}

?>

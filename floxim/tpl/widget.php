<?php

abstract class fx_tpl_widget extends fx_tpl {

    public function record() {
        
    }

    public function settings() {
        
    }

    public function add_form() {
        extract($this->get_vars());
        $fields = array();
        
        foreach ( $widget->fields() as $field ) {
            $fields[] = $field->get_js_field( $infoblock['settings'], '' );
        }
        
        return $fields;
    }

}

?>

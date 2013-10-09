<?php
class fx_controller_component_text extends fx_controller_component {    
    public function defaults_listing() {
        return array_merge_recursive(
            parent::defaults_listing(),
            array(
                'params' => array(
                    'sorting' => 'manual',
                    'parent_type' => 'current_page_id',
                    'limit' => 1,
                    'show_pagination' => false
                ),
                'force' => 'params.*'
            )
        );
    }
}
?>
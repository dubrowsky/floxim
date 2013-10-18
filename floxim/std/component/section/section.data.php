<?php
class fx_data_content_section extends fx_data_content_page {
    public function relations() {
        return parent::relations() + array(
            'submenu' => array(
                self::HAS_MANY,
                'content_section',
                'parent_id'
            )
        );
    }
}
?>
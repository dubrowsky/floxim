<?php
class fx_data_content_section extends fx_data_content {
    public function relations() {
        $rel = parent::relations();
        $childs = array(
            'childs' => array(
                self::HAS_MANY,
                'content',
                'parent_id'
            )
        );
        $relations = array_merge($rel,$childs);
        return $relations;
    }
}
?>
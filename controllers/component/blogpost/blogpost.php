<?php
class fx_controller_component_blogpost extends fx_controller_component {
    public function listing() {
        $this->listen('items_ready', function(fx_collection $items) {
            $ids = $items->get_values('id');
            $tags = fx::data('content_tagpost')->get_all(array('parent_id' => $ids));
            dev_log('tags for posts', $tags, $ids);
        });
        return parent::listing();
    }
}
?>
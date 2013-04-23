<?php
class fx_controller_component_blogpost extends fx_controller_component {
    public function listing() {
        $this->listen('items_ready', function(fx_collection $items) {
            $tags = fx::data('content_tagpost')->where('parent_id', $items->get_values('id'))->all();
            $items->attache_many($tags, 'parent_id', 'tags', 'id', 'tag');
            dev_log($tags, $items);
        });
        return parent::listing();
    }
}
?>
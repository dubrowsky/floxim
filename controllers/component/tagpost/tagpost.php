<?
class fx_controller_component_tagpost extends fx_controller_component {
    /*
    public function listing() {
        $this->listen('items_ready', function(fx_collection $items) 
            $tag_ids = $items->get_values('tag_id');
            $tags = fx::data('content_tag', $tag_ids);
            foreach ($items as $item) {
                $item['tag'] = $tags->find_one(array('id' => $item['tag_id']));
            }
            dev_log('tagpost coll', $items);
        });
        return parent::listing();
    }
     */
}
?>
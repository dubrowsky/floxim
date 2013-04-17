<?php
class fx_controller_component_blogpost extends fx_controller_component {
    public function listing() {
        $this->listen('items_ready', function(fx_collection $items) {
            $pages = array();
            foreach ( $items as $item )
            {
                $page = $item->get_page();
                $pages[] = $page['id'];
            }
            $tags = fx::data('content_tagpost')->get_all(array('parent_id' => $pages));
            foreach ( $items as $item )
            {
                $item['tags'] = $tags->find(array('parent_id' => $item->get_page()->get('id')))->get_values('tag');
            }
            dev_log($items);
        });
        return parent::listing();
    }
}
?>
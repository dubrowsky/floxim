<?php
class fx_data_content_page extends fx_data_content {
    public function attache_to_content(Traversable $content_items) {
        if ($content_items->length == 0) {
            return;
        }
        $component_id = $content_items->first()->get_component_id();
        
        $pages = $this->get_all(
            array(
                'content_type' => fx::data('component', $component_id)->get('keyword'), 
                'content_id' => $content_items->get_values('id')
            )
        );
        foreach ($pages as $p) {
            $content_items->find('id', $p['content_id'])->apply(function($item) use ($p) {
                $item->set_page($p);
            });
        }
    }
}
?>
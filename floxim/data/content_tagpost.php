<?
class fx_data_content_tagpost extends fx_data_content {
    public function get_all() {
        $items = call_user_func_array(array('parent', 'get_all'), func_get_args());
        $tag_ids = $items->get_values('tag_id');
        $tags = fx::data('content_tag', $tag_ids);
        fx::data('content_page')->attache_to_content($tags);
        foreach ($items as $item) {
            $item['tag'] = $tags->find_one(array('id' => $item['tag_id']));
        }
        return $items;
    }
}
?>
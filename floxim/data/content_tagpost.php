<?
class fx_data_content_tagpost extends fx_data_content {
    public function _get_essences() {
        $items = parent::_get_essences();
        $tags = fx::data('content_tag', $items->get_values('tag_id'));
        $items->attache($tags, 'tag_id');
        return $items;
    }
}
?>
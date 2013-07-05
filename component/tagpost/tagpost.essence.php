<?php
class fx_content_tagpost extends fx_content {
    
    protected function _get_tag(){
        if (!$this['tag_id']) {
            return;
        }
        return fx::data('content_tag', $this['tag_id']);
    }
    
    protected function _after_insert() {
        parent::_after_insert();
        $tag = $this->_get_tag();
        if (!$tag) {
            return;
        }
        $tag['counter'] = $tag['counter']+1;
        $tag->save();
    }
    
    protected function _after_delete() {
        parent::_after_delete();
        if (! ($tag = $this->_get_tag()) ) {
            return;
        }
        $tag['counter'] = $tag['counter']-1;
        $tag->save();
    }
}
?>
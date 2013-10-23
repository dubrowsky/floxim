<?php
class fx_content_tag extends fx_content_page {    
    protected function _after_delete() {
        parent::_after_delete();
        $tagposts = fx::data('content_tagpost')->where('tag_id', $this['id'])->all();
        $tagposts->apply(function($tp) {
            $tp['tag_id'] = null;
            $tp->delete(); 
        });
    }
}
?>
<?php
class fx_infoblock_visual extends fx_essence {
    protected  function _before_save() {
        parent::_before_save();
        unset($this['is_stub']);
    }
}
?>
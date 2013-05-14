<?php
class fx_layout extends fx_essence {
    public function get_path() {
        return fx::config()->SUB_FOLDER.fx::config()->HTTP_LAYOUT_PATH.$this['keyword'].'/';
    }
}
?>
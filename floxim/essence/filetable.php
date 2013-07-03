<?php
class fx_filetable extends fx_essence {
    public static function get_path($id) {
        if (!is_numeric($id)) {
            return $id;
        }
        $file = fx::data('filetable', $id);
        if (!$file) {
            return null;
        }
        return $file->get_full_path();
    }
    public function get_full_path() {
        return fx::config()->HTTP_FILES_PATH.$this['path'];
    }
}
?>
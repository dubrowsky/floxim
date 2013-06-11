<?php
class fx_content_page extends fx_content {
    /**
     * Получить id страниц-родителей
     * @return array
     */
    public function get_parent_ids() {
        $c_pid = $this->get('parent_id');
        $ids = array();
        while ($c_pid != 0) {
            array_unshift($ids, $c_pid);
            $c_pid = fx::data('content_page')->get_by_id($ids[0])->get('parent_id');
        }
        return $ids;
    }
    
    public function get_path() {
        $path_ids = $this->get_parent_ids();
        $path_ids []= $this['id'];
        $path = fx::data('content_page')->where('id', $path_ids)->all();
        return $path;
    }
}
?>

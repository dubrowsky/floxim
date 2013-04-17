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
        
    }
}
?>

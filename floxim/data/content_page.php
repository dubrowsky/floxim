<?php
class fx_data_content_page extends fx_data_content {
    public function attache_to_content(array $content_items) {
        if (count($content_items) == 0) {
            //dev_log('no to attache');
            return;
        }
        $component_id = current($content_items)->get_component_id();
        $ids = array();
        foreach ($content_items as $ci) {
            $ids []= $ci['id'];
        }
        
        $pages = $this->get_all(
            array(
                'content_type' => fx::data('component', $component_id)->get('keyword'), 
                'content_id' => $ids
            )
        );
        $q = fx::db()->get_last_query();
        //dev_log('pages to attch', $pages, array('content_type' => $component_id, 'id' => $ids), $q);
        foreach ($pages as $p) {
            foreach ($content_items as $ci) {
                if ($p['content_id'] == $ci['id']) {
                    $ci->set_page($p);
                    break;
                }
            }
        }
        //dev_log('bind pages', $content_items);
    }
}
?>
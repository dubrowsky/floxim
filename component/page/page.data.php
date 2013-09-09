<?php
class fx_data_content_page extends fx_data_content {
    public function get_tree() {
        $data = $this->all();
        $tree = $this->make_tree($data);
        return $tree;
    }
    
    public function get_by_url($url, $site_id = null) {
        $url_variants = array($url);
        if ($site_id === null){
            $site_id = fx::env('site')->get('id');
        }
        $url_with_no_params = preg_replace("~\?.+$~", '', $url);
        
        $url_variants []= 
            preg_match("~/$~", $url_with_no_params) ? 
            preg_replace("~/$~", '', $url_with_no_params) : 
            $url_with_no_params . '/';
        
        if ($url_with_no_params != $url) {
            $url_variants []= $url_with_no_params;
        }
        
        $page = fx::data('content_page')->
            where('url', $url_variants)->
            where('site_id', $site_id)->
            one();
        return $page;
    }
    
    public function make_tree($data) {
        
        $index_by_parent = array();
        
        foreach ($data as $item) {
            $pid = $item['parent_id'];
            if (!isset($index_by_parent[$pid])) {
                $index_by_parent[$pid] = fx::collection();
                $index_by_parent[$pid]->is_sortable = $data->is_sortable;
            }
            $index_by_parent[$pid] []= $item;
        }
        foreach ($data as $item) {
            if (isset($index_by_parent[$item['id']])) {
                $item['children'] = $index_by_parent[$item['id']];
                $data->find_remove(
                    'id',
                    $index_by_parent[$item['id']]->get_values('id')
                );
            } else {
                $item['children'] = null;
            }
        }
        return $data;
    }
}
?>
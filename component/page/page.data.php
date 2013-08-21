<?php
class fx_data_content_page extends fx_data_content {
    public function get_tree() {
        $data = $this->all();
        $tree = $this->make_tree($data);
        return $tree;
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
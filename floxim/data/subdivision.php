<?php

class fx_data_subdivision extends fx_data {

    public function __construct() {
        parent::__construct();
        $this->order = 'priority';
    }

    public function get_by_uri($uri, $site = 0) {
        return $this->get('hidden_url', $uri, 'site_id', $site);
    }

    public function get_parent_tree($sub_id) {
        $result = array();

        $sub = $this->get_by_id($sub_id);
        $result[] = $sub;

        while ($sub['parent_id']) {
            $sub = $this->get_by_id($sub['parent_id']);
            $result[] = $sub;
        }

        return $result;
    }


    public function get_by_infoblock($ib) {
        $fx_core = fx_core::get_object();
        if (is_object($ib)) {
            if (is_subclass_of($ib, 'fx_infoblock')) {
                return $fx_core->subdivision->get_by_id($ib->get('subdivision_id'));
            } else {
                return null;
            }
        } else {
            $ib = intval($ib);
            if ($ib) {
                return $fx_core->subdivision->get_by_id($fx_core->infoblock->get_by_id($ib)->get('subdivision_id'));
            } else {
                return null;
            }
        }
    }
    
    public function create($data = array()) {
        $obj = new $this->classname(array('data' => $data, 'finder' => $this));
        $obj['created'] = date("Y-m-d H:i:s");
        
        if ( isset($data['parent_id']) ) {
            $obj['priority'] = $this->next_priority($data['parent_id']);
        }
      
        return $obj;
    }
    

    public function next_priority($parent_id) {
        $db = fx_core::get_object()->db;
        return $db->get_var("SELECT MAX(`priority`)+1 FROM `{{".$this->table."}}` WHERE `parent_id` = '".intval($parent_id)."' ");
    }

}

?>

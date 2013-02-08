<?php

class fx_layout_info implements fx_layout {

    protected $infoblocks = array();
    protected $menu = array();

    public function place_infoblock($keyword, $params) {
        $params = unserialize($params);
        unset($params['blocks'], $params['divider']);
        $this->infoblocks[$keyword] = $params;
    }

    public function place_infoblock_simple($keyword, $params) {
        
    }

    public function place_menu($keyword, $params, $template = array()) { 
        parse_str($params, $params);
        $this->menu[$keyword] = $params;
    }

    public function get_infoblocks() {
        return $this->infoblocks;
    }
    
    public function get_menu() {
        return $this->menu;
    }

}

?>

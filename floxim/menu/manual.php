<?php

class fx_menu_manual extends fx_menu {

    public function __construct($params = '', $template = array()) {
        parent::__construct($params, $template);

        $this->sort_post = array('essence' => 'menu', 'action' => 'move', 'id' => $params['id']);
    }

    protected function get_items() {
        $items = $this->params['items'];
        if ($items) {
            foreach ($items as $k => &$item) {
                $item['id'] = $k;
            }
        }

        return $items ? $items : array();
    }

    protected function init_post_settings() {
        parent::init_post_settings();
        $this->settings_post['type'] = 'manual';
    }
    
    protected function get_hash_for_menu_item ( $item ) {
        $fx_core = fx_core::get_object();
        $hash = $fx_core->page->add_block('essence=menu_item&id='.intval($item["id"]).'&menu_id='.$this->params['id'], 'settings,delete', 'design', $this->parent_cl);
        return $hash;
    }

}

?>

<?php

class fx_menu_level extends fx_menu {
    protected $level = 0;
    
    public function __construct($params = '', $template = array()) {
        parent::__construct($params, $template);
        $this->level = intval($this->params['level']);
    }
    
    protected function get_items() {
        $fx_core = fx_core::get_object();
        $level = $this->level;

        $current_sub = $fx_core->env->get_sub();
        $parent_tree = fx::data('subdivision')->get_parent_tree($current_sub['id']);

        $level_id = count($parent_tree) - $level;
        if ($level_id < 0) {
            return array();
        }
        if ($level == 0) {
            $sub = 0;
        } else {
            $sub = $parent_tree[$level_id]['id'];
        }

        $site_id = $fx_core->env->get_site('id');
        return fx::data('subdivision')->get_all('parent_id', $sub, 'checked', 1, 'site_id', $site_id);
    }
    
    protected function init_post_settings() {
        parent::init_post_settings();
        $this->settings_post['type'] = 'level';
        $this->settings_post['level'] = $this->level;
    }

}

?>  
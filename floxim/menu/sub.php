<?php

class fx_menu_sub extends fx_menu {

    protected $sub;
    protected $is_current_sub = false;

    public function __construct($params = '', $template = array()) {
        parent::__construct($params, $template);
        $this->sub = $this->params['sub'] ? $this->params['sub'] : 0;
        
        if ( $this->sub == fx_menu::CURRENT_SUB ) {
            $this->sub = fx_core::get_object()->env->get_sub('id');
            $this->is_current_sub = true;
        }
    }
    
    protected function _replace_prefix() {
        $fx_core = fx_core::get_object();
        
        $prefix = parent::_replace_prefix();
        
        if (strpos($prefix, '%name%') !== false ) {
            $parent_name = fx::data('subdivision')->get_by_id($this->sub)->get('name');
            $prefix = str_replace('%name%', $parent_name, $prefix);
        }
        
        return $prefix;
        
    }

    protected function get_items() {
        $fx_core = fx_core::get_object();
        $site_id = $fx_core->env->get_site('id');
        return fx::data('subdivision')->get_all('parent_id', $this->sub, 'checked', 1, 'site_id', $site_id);
    }
    
    protected function init_post_settings() {
        parent::init_post_settings();
        $this->settings_post['type'] = 'sub';
        $this->settings_post['sub'] = $this->is_current_sub ? fx_menu::CURRENT_SUB :  $this->sub;
    }

}

?>

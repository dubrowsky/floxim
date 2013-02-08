<?php

class fx_menu_path extends fx_menu {

    protected $from, $end, $reverse;
    protected $sort_post = false;
    
    public function __construct($params = '', $template = array()) {
        parent::__construct($params, $template);
        
        $this->reverse = intval($this->params['reverse']);
        $this->from = intval($this->params['from']);
        $this->end = intval($this->params['end']);
    }
    protected function get_items() {
        $fx_core = fx_core::get_object();

        $current_sub = $fx_core->env->get_sub();
        $items = $current_sub->get_parents();
        $items[] = $current_sub;

        // site 
        $site = $fx_core->env->get_site();
        $site['url'] = '/';
        array_unshift($items, $site);

        if ( $this->reverse ) {
            $items = array_reverse($items);
        }

        // срез
        $len = count($items) - $this->from - $this->end;
        $items = array_slice($items, $this->from, $len);

        return $items;
    }
    
    protected function init_post_settings() {
        parent::init_post_settings();
        $this->settings_post['type'] = 'path';
        $this->settings_post['from'] = $this->from;
        $this->settings_post['end'] = $this->end;
        $this->settings_post['reverse'] = $this->reverse;
    }

}

?>
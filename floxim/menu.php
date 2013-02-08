<?php

class fx_menu {

    const CURRENT_SUB = -1;

    const KIND_INDEPENDENT = 'independent';
    const KIND_DEPENDENT = 'dependent';
    const KIND_BOTH = 'independent,dependent';
    const KIND_SECONDARY = 'secondary';
    
    protected $params, $function;
    protected $template, $sub_template;
    protected $parent_cl, $wrap_menu, $forcibly_wrap = false;
    protected $settings_post, $sort_post = array('essence' => 'subdivision', 'action' => 'move');
    protected $items;

    public function __construct($params = '', $template = array()) {
        $fx_core = fx_core::get_object();
        
        if (is_string($params)) parse_str($params, $params);
        $this->params = $params;
        // массив template содержит шаблоны вывода и для подменю
        $this->template = $this->shift($template);
        $this->sub_template = $template;

        $this->show_sub_pages = isset($params['show_sub_pages']) ? $params['show_sub_pages'] : 'active';
        $this->sub_page_level = isset($params['sub_page_level']) ? $params['sub_page_level'] : 1;

        if ( ($fx_core->env->get_user() && $fx_core->env->get_user()->perm()->is_supervisor()) ) {
            $this->wrap_menu = $this->params['keyword'];
        }
        
        
    }

    public function show() {
        $this->items = $this->get_items();

        $count = count($this->items);
        $i = 1;
        $divider = isset($this->template['divider']) ? $this->template['divider'] : '';

        $result = $this->_replace_prefix();

        foreach ($this->items as $subdivision) {
            $result .= str_replace('%I', $i, $this->_replace($subdivision));
            if ($i++ < $count) $result .= $divider;
        }

        $result .= $this->_replace_suffix();
        $result = preg_replace("/%[a-z0-9_-]*%/i", '', $result);
        return $result;
    }
    
    

    protected function _replace_prefix() {
        $fx_core = fx_core::get_object();

        if ($this->items) {
            $prefix = $this->template['prefix'];
        } else {
            $prefix = '';
        }

        if ($this->wrap_menu) { 
            $this->init_post_settings();
            $button = $this->params['deleted'] ? '' : 'settings'.($this->params['necessary'] ?  '' : ',delete');
            $hash = $fx_core->page->add_block($this->settings_post,  $button, 'design', null);
            $this->parent_cl = $hash;

            $add_data = array();
            $add_data['mode'] = 'design';
            $add_data['post'] = array('essence' => 'menu', 'keyword' => $this->params['keyword']);
            $add_data['preview'] = '<li>Новый пункт</li>';
            $add_data['preview_parent'] = '.'.$hash;
            $add_data['decent_parent'] = $hash;
            $fx_core->page->addition_block($add_data);

            // если префикс не задан, то все меню надо обернуть, чтобы его можно было настраивать
            if (!$prefix) {
                $prefix = '<div>';
                $this->forcibly_wrap = true;
            }

            $prefix = $this->_insert_hash_class($prefix);

            if ($this->sort_post) {
                $fx_core->page->add_sortable(
                        array('parent' => '.'.$hash,
                                'mode' => 'design',
                                'without_placeholder' => true,
                                'post' => $this->sort_post));
            }
        } else {
            $hash = '';
        }

        $prefix = str_replace('%hash%', $hash.' fx_page_block', $prefix);

        return $prefix;
    }

    protected function _replace($subdivision) {
        if (!isset($this->template['active'])) {
            $this->template['active'] = $this->template['unactive'];
        }
        if (!isset($this->template['active_link'])) {
            $this->template['active_link'] = $this->template['active'];
        }

        $result = $this->_replace_macrovars($subdivision, $this->template[$this->_get_type($subdivision)]);

        if (strpos($result, '%submenu%') !== false) {
            if ($this->show_submenu($subdivision)) {
                $sub_page_level = $this->sub_page_level - 1;
                $smenu = new fx_menu_sub('show_sub_pages=all&sub='.$subdivision['id'].'&sub_page_level='.$sub_page_level, $this->sub_template);
                $submenu = $smenu->show();
            } else {
                $submenu = '';
            }

            $result = str_replace('%submenu%', $submenu, $result);
        }

        if (preg_match('/%func_(\d+)%/i', $result, $match)) {
            $context['item'] = $subdivision;
            $function = $this->function[$match[1]];
            $res = call_user_func(array($function['class'], $function['function']), $context);
            $result = str_replace($match[0], $res, $result);
        }

        if ($this->wrap_menu) {
            $hash = $this->get_hash_for_menu_item($subdivision);

            $result = $this->_insert_hash_class($result);
            $hash .= ' fx_page_block fx_sortable_'.str_replace('fx_page_block_', '', $this->parent_cl);
            $result = str_replace('%hash%', $hash, $result);
        }
        
        fx_menu_force::add_shown_sub($subdivision);


        return $result;
    }

    protected function _insert_hash_class($result) {
        if (strpos($result, '%hash%') === false) {
            preg_match("/^<(.*?)>/", $result, $match);
            if ($match) {
                $result = preg_replace("/class\s*=\s*\"([^\"]+)\"/i", "class=\" $1 %hash%\"", $result, -1, $count);
                if (!$count) {
                    $result = preg_replace("/^<([a-z]+)/i", '<$1 class="%hash%" ', $result);
                }
            }
        }
        return $result;
    }

    protected function _replace_suffix() {
        $result = '';
        if ($this->items) {
            $result .= $this->template['suffix'];
        }
        if ($this->forcibly_wrap) {
            $result .= '</div>';
        }

        return $result;
    }

    protected function show_submenu($subdivision) {
        if (!$this->sub_page_level) {
            return false;
        }
        if ($this->show_sub_pages == 'none') {
            return false;
        }
        if ($this->show_sub_pages == 'active' && $this->_get_type($subdivision) == 'unactive') {
            return false;
        }

        return true;
    }

    protected function _get_type($subdivision) {
        $type = 'unactive';
        $fx_core = fx_core::get_object();

        // активные разделы
        $current_sub = $fx_core->env->get_sub();
        $active_subs = $current_sub->get_parents(1);
        $active_subs[] = $current_sub['id'];

        if (in_array($subdivision['id'], $active_subs)) {
            $type = 'active';
        }

        if ($this->_get_url($subdivision) == $_SERVER['REQUEST_URI']) {
            $type = 'active_link';
        }

        return $type;
    }

    protected function _get_name($subdivision) {
        return $subdivision['name'];
    }

    protected function _get_url($item) {
        return $item['external_url'] ? $item['external_url'] : ($item['hidden_url'] ? $item['hidden_url'] : $item['url']);
    }

    protected function _replace_macrovars($item, $template) {
        $vars = $values = array();
        $vars[] = '%name%';
        $values[] = $this->_get_name($item);
        $vars[] = '%url%';
        $values[] = $this->_get_url($item);
        if ($item instanceof fx_subdivision) {
            foreach ($item->get() as $k => $v) {
                $vars[] = '%'.$k.'%';
                $values[] = $v;
            }
        }

        $result = str_replace($vars, $values, $template);
        return $result;
    }

    protected function get_hash_for_menu_item($item) {
        $fx_core = fx_core::get_object();
        $hash = $fx_core->page->add_block('essence=subdivision&id='.intval($item["id"]), 'on,off,settings', 'design', $this->parent_cl);
        return $hash;
    }

    protected function init_post_settings() {
        $this->settings_post = array('essence' => 'menu');
        $this->settings_post['id'] = intval($this->params['id']);
        $this->settings_post['keyword'] = $this->params['keyword'];
        $this->settings_post['kind'] = $this->params['kind'];
        $this->settings_post['submenu_count'] = count($this->sub_template);
        $this->settings_post['show_sub_pages'] = $this->show_sub_pages;
        $this->settings_post['sub_page_level'] = $this->sub_page_level;
    }

    protected function shift(&$array) {
        $ret = array();
        $new_array = array();

        foreach ($array as $k => $v) {
            if ($k == 0) {
                $ret = $v;
            } else {
                $new_array[$k - 1] = $v;
            }
        }

        $array = $new_array;
        return $ret;
    }

}

?>

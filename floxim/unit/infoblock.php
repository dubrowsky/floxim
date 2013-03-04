<?php

class fx_unit_infoblock {

    protected $keyword;
    // инфоблок из контентной части
    protected $main_content;
    // действие ( index, full,add, etc)
    protected $show_method;
    protected $is_admin;

    public function __construct() {
        $user = fx_core::get_object()->env->get_user();
        $this->is_admin = $user && $user->perm()->is_supervisor();
    }

    public function show($keyword, $params = '', $edit_mode = false) {
    	$fx_core = fx_core::get_object();
        if ( $edit_mode && !$this->is_admin ) {
            $edit_mode = false;
        }
        $this->show_method = false;

        if (!is_array($params)) $params = unserialize($params);

        $blocks = $params['blocks'];
        $divider = isset($params['divider']) ? $params['divider'] : '';
        $this->keyword = htmlspecialchars($keyword, ENT_QUOTES);

        $this->main_content = (bool) $params['main'];

        $before = '';
        if ($this->main_content && !$edit_mode) {
            $before = fx_menu_force::place_menu();
        }

        if ($this->main_content && $fx_core->env->get_main_content()) {
            return $before.$fx_core->env->get_main_content();
        }
        $infoblocks = $this->get_blocks($edit_mode);
        dev_log('blocks to show', $infoblocks);
        
        $count = count($infoblocks);
        $block_num = 0;

        $result = '';
        foreach ($infoblocks as $block) {
            $template = $blocks[$block['use_format']]['template'];
            if (!$template) {
                $template = '%FX_CONTENT%';
            }

            $template = $this->replace_values($template, $block);

            // добавление js-информации о блоке
            // это информация нужна только в режиме редактирования
            if ($edit_mode) {
                $mode = $this->main_content ? 'edit' : 'design';
                $block_hash = $this->get_hash_for_block($block, $mode, $params);
                $template = $this->wrap_block($template, $block_hash);
            }

            // контентная часть
            $func_param = array('main_content' => $this->main_content);
            $func_param['block_hash'] = $block_hash;
            $func_param['edit_mode'] = $edit_mode;

            // Для основного блока можно передать страници и поисковый фильтр
            if ($this->main_content) {
                $func_param['page'] = $fx_core->env->get_page();
                $key = fx::config()->SEARCH_KEY;
                $search = $fx_core->input->fetch_get_post($key);
                if ($search) {
                    $func_param[$key] = $search;
                }
            }

            $method = $this->show_method ? $this->show_method : ( $block['default_action'] ? $block['default_action'] : 'index' );

            if ($block->check_rights($method)) {
                $content = call_user_func(array($block, 'show_'.$method), $func_param);
                $template = str_replace('%FX_CONTENT%', $content, $template);
            } else {
                $template = fx_user::get_auth_form();
            }

            $result .= $template;

            // divider
            if ($block_num++ < $count - 1) $result .= $divider;
        }

        // весь инфоблок так же надо обернуть
        // добавление js информации о блоке
        // это нужно делать при первом показе инфоблоков,
        // еще до перехода в  edit_mode - режим редактирования блоков
        if (!$edit_mode && $this->is_admin) {
            $fx_core->page->add_infoblock($this->keyword, $params);
            $result = $this->wrap_infoblock($result);
        }

        if ($edit_mode && $this->is_admin) {
            if ($count > 1) {
                $this->add_sortable();
            }
            $this->add_addition_info($params);
        }

        return $before.$result;
    }

    protected function replace_values($template, $block) {
        preg_match_all('/%FX_REPLACE_([0-9]+)%/', $template, $matches, PREG_SET_ORDER);

        if ($matches) {
            foreach ($matches as $match) {
                $template = str_replace($match[0], $block['replace_value'][$match[1]], $template);
            }
        }
        return $template;
    }

    // todo - выбирать все инфоблоки одним запросом, без учета keyword
    protected function get_blocks($edit_mode = false) {
        $fx_core = fx_core::get_object();
        $sub = $fx_core->env->get_sub();
        $result = array();

        $cond_checked = $edit_mode ? " " : "`checked` = 1 AND ";
        if ($this->main_content && $fx_core->env->get_action()) {
            $result = $fx_core->env->get_ibs();
            $this->show_method = $fx_core->env->get_action();
        } else {
            $site = $fx_core->env->get_site();
            $dop = $sub['own_design'] ? "" : " OR (`individual` = 0 AND `subdivision_id` = 0) ";
            $site_cond = "AND `site_id` = '".$site['id']."'";
            if (!$this->main_content) {

            	if (($preview = fx_controller_admin_template::get_preview_data()) ) {
            		$template = $preview['template_id'];
                } else {
                    $template = $site['template_id'];
                }

                $site_cond .= " AND `template_id` = '".$template."' ";
            }
            $where = "( ".$cond_checked." `keyword` = '".$this->keyword."' $site_cond ) AND ( `subdivision_id` = '".$sub['id']."' $dop ) ";
            $result = fx::data('infoblock')->get_all($where);
        }

        return $result;
    }

    protected function add_addition_info($params) {
        $template = $params['blocks'][0]['template'];
        $preview = str_replace('%FX_CONTENT%','Новый инфоблок', $template );
        $add_data = array();
        $add_data['mode'] = $this->main_content ? 'edit' : 'design';
        $add_data['post'] = array('essence' => 'infoblock', 'keyword' => $this->keyword, 'infoblock_info' => $params);
        $add_data['name'] = "Новый инфоблок";
        $add_data['preview'] = $preview;
        $add_data['preview_parent'] = '#fx_design_'.$this->keyword;
        fx_core::get_object()->page->addition_block($add_data);
    }

    protected function add_sortable() {
        fx_core::get_object()->page->add_sortable(
                array('parent' => '#fx_design_'.$this->keyword,
                        'mode' => $this->main_content ? 'edit' : 'design',
                        'post' => array('essence' => 'infoblock', 'action' => 'move')));
    }

    protected function get_hash_for_block($block, $mode, $infoblock_info) {
        $fx_core = fx_core::get_object();
        $post['essence'] = 'infoblock';
        $post['id'] = $block['id'];
        $post['infoblock_info'] = $infoblock_info;
        $hash_block = $fx_core->page->add_block($post, 'settings,on,off,delete', $mode, null, $block['checked']);
        return $hash_block;
    }

    protected function wrap_infoblock($content) {
        return '<div class="fx_wrap" id="fx_design_'.$this->keyword.'">'.$content.'</div>';
    }

    /**
     * Оборачивает блок служебным div'ом или всталвяет в существующий спец.класс
     * @todo подумать, как заменить три регулрки на одну
     */
    protected function wrap_block($content, $hash) {
        $this->hash_block = $hash;
        $this->hash_block .= ' fx_page_block fx_sortable_'.$this->keyword;
        $insert_string = "class=\"".$this->hash_block."\"";

        if (preg_match("@^\s*<(\w+).+</\\1>\s*$@us", $content, $regs)) {
            $content = preg_replace_callback("@^(\s*<(".$regs[1].")(.*?)>)@s", array($this, 'insert_class'), $content);
        } else { // если не удалось - добавим <div>
            $content = "<div ".$insert_string.">".$content."</div>";
        }

        return $content;
    }

    protected function insert_class($match) {
        // может быть, аттрибут class уже есть в теге
        $new_class = preg_replace("/class\s*=\s*\"([^\"]+)\"/i", "class=\" $1 ".$this->hash_block."\"", $match[3], -1, $count);
        if ($count) {
            $result = "<".$match[2]." ".$new_class.">";
        } else {
            $result = "<".$match[2]." ".$match[3]." class=\"".$this->hash_block."\">";
        }

        return $result;
    }

}

?>

<?php

/**
 * Класс для вывода "простых" инфоблоков, например, логотип 
 */
class fx_unit_infoblock_simple extends fx_unit_infoblock {

    protected $type, $params;
    protected $edit_in_place_post, $edit_in_place_type;

    public function show($keyword, $params = '') {
        $fx_core = fx_core::get_object();

        $this->keyword = htmlspecialchars($keyword, ENT_QUOTES);
        $this->params = unserialize($params);
        $this->edit_mode = true;

        $result = $this->params['template'];
        $infoblocks = $this->get_blocks();
        
        $infoblock = $infoblocks[0];

        $user_value = $infoblock['replace_value'];

        foreach ($this->params['params'] as $k => $param) {
            if ($user_value[$k]) {
                $value = $user_value[$k];
            } else {
                $value = $this->get_default_value($param);
            }
            $this->params['params'][$k]['value'] = $value;

            $result = str_replace('%FX_REPLACE_'.$k.'%', $value, $result);
        }

        if ($this->edit_mode) {
        	$post = array();
            $post['essence'] = 'infoblock';
            $post['simple'] = 1;
            $post['params'] = $this->params['params'];
            
            if ($infoblock) { // уже сохранен
            	$post['id'] = $infoblock['id'];
            } else { // еще не сохранен
            	$post['keyword'] = $keyword;
            	$subdivision = $fx_core->env->get_sub();
				$post['subdivision_id'] = $subdivision['id'];
            }
            //dev_log('poost', $post);
            $hash = $fx_core->page->add_block($post, 'settings', 'design');
            $result = $this->wrap_block($result, $hash, 1);
        }

        return $result;
    }

    protected function get_default_value($param) {
        $fx_core = fx_core::get_object();

        if ($param['type'] != 'image') {
            return $param['default'];
        }

        $src = $param['default'];
        if ($src && substr($src, 0, 1) != '/' && substr($src, 0, 4) != 'http') {
            $template = $fx_core->env->get_template();
            $current_template = fx::data('template')->get_by_id($template);
            if ( $current_template['parent_id'] ) {
                $current_template = $current_template->get_parent();
            }
            $src = $current_template->get_path().$src;
        }

        return $src;
    }

}

?>

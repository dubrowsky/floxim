<?php

defined("FLOXIM") || die("Unable to load file.");

class fx_infoblock extends fx_essence {

    
    protected $_infoblock2layout = null;
    
    public function set_infoblock2layout(fx_infoblock2layout $visual) {
        $this->_infoblock2layout = $visual;
    }
    
    public function get_infoblock2layout() {
        if (!$this->_infoblock2layout) {
            $stored = fx::data('infoblock2layout')->get(
                    'infoblock_id', $this->get('id'), 
                    'layout_id', fx::env('site')->get('layout_id')
            );
            if ($stored) {
                $this->_infoblock2layout = $stored;
            } else {
                $i2l_params = array('layout_id' => fx::env('layout'));
                if (($ib_id = $this->get('id'))) {
                    $i2l_params['infoblock_id'] = $ib_id;
                }
                $this->_infoblock2layout = fx::data('infoblock2layout')->create($i2l_params);
            }
        }
        return $this->_infoblock2layout;
    }
    
    public function get_type() {
        return 'infoblock';
    }
    
    public function render() {
        return fx::controller('infoblock.render', array('infoblock' => $this))->process();
    }

    public function get_access($item = '', $consider_inheritance = true) {
        $fx_core = fx_core::get_object();

        $items = fx_rights::get_user_types();
        $types = fx_rights::get_rights_types();
        $access = $this['access'];

        // права по умолчанию
        foreach ($types as $type) {
            if (!in_array($access[$type], $items)) {
                $access[$type] = 'inherit';
            }
        }

        if ($consider_inheritance && $this['type'] == 'content') {
            $ctpl = fx::data('ctpl')->get_by_id($this['list_ctpl_id']);
            if ($ctpl) {
				$ctpl_access = $ctpl->get_access();
				foreach ($access as $type => $v) {
					if ($v == 'inherit') {
						$access[$type] = $ctpl_access[$type];
					}
				}
			}
        }

        return $item ? $access[$item] : $access;
    }

    public function check_rights($action) {
        return true;
    }
    
    protected function _after_delete() {
        $visual = fx::data('infoblock2layout')->get_all('infoblock_id', $this->get('id'));
        $killer = function($cv) {
            $cv->delete();
        };
        $visual->apply($killer);
        $inherit = fx::data('infoblock')->get_all('parent_infoblock_id', $this->get('id'));
        $inherit->apply($killer);
    }
    
    public function get_owned_content() {
        if ($this['action'] != 'listing') {
            return false;
        }
        $controller = fx::controller($this['controller']);
        $content_type = $controller->get_content_type();
        $content = fx::data('content_'.$content_type)->get_all(array('infoblock_id' => $this->get('id')));
        return $content;
    }
    
    public function get_parent_infoblock() {
        if ( !( $parent_ib_id = $this->get('parent_infoblock_id'))) {
            return;
        }
        return fx::data('infoblock', $parent_ib_id);
    }
    
    public function get_prop_inherited($path_str) {
        $own_result = null;
        $path = explode(".", $path_str);
        if ($path[0] == 'visual') {
            $c_i2l = $this->get_infoblock2layout();
            $vis_path_str = join(".", array_slice($path, 1));
            $own_result = fx::dig($c_i2l, $vis_path_str);
        } else {
            $own_result = fx::dig($this, $path_str);
        }
        if ($own_result && !is_array($own_result)) {
            return $own_result;
        }
        if ( ($parent_ib = $this->get_parent_infoblock()) ) {
            $parent_result = $parent_ib->get_prop_inherited($path_str);
        }
        if (is_array($own_result) && is_array($parent_result)) {
            return array_merge($parent_result, $own_result);
        }
        return $own_result ? $own_result : $parent_result;
    }
    
    public function get_root_infoblock() {
        $cib = $this;
        while ($cib['parent_infoblock_id']) {
            $cib = $cib->get_parent_infoblock();
        }
        return $cib;
    }
}
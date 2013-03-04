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
}
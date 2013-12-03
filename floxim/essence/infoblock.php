<?php
class fx_infoblock extends fx_essence {
    
    protected $_visual = array();
    
    public function set_visual(fx_infoblock_visual $visual) {
        $this->_visual[$visual['layout_id']] = $visual;
    }
    
    public function get_visual($layout_id = null) {
        if (!$layout_id) {
            $layout_id = fx::env('layout');
        }
        if (!isset($this->_visual[$layout_id])) {
            $stored = fx::data('infoblock_visual')->
                    where('infoblock_id', $this['id'])-> 
                    where('layout_id', $layout_id)->
                    one();
            if ($stored) {
                $this->_visual[$layout_id] = $stored;
            } else {
                $i2l_params = array(
                    'layout_id' => $layout_id,
                    'is_stub' => true
                );
                if (($ib_id = $this->get('id'))) {
                    $i2l_params['infoblock_id'] = $ib_id;
                }
                $this->_visual[$layout_id] = fx::data('infoblock_visual')->create($i2l_params);
            }
        }
        return $this->_visual[$layout_id];
    }
    
    public function get_type() {
        return 'infoblock';
    }
    
    public function render() {
        return fx::controller('infoblock.render', array('infoblock' => $this))->process();
    }
    
    protected function _after_delete() {
        $killer = function($cv) {
            $cv->delete();
        };
        fx::data('infoblock_visual')->where('infoblock_id', $this['id'])->all()->apply($killer);
        fx::data('infoblock')->where('parent_infoblock_id', $this['id'])->all()->apply($killer);
    }
    
    public function get_owned_content() {
        if ($this['action'] != 'list_infoblock') {
            return false;
        }
        $content_type = fx::controller($this['controller'])->get_content_type();
        $content = fx::data('content_'.$content_type)->
                    where('infoblock_id',$this['id'])->
                    all();
        return $content;
    }
    
    public function get_parent_infoblock() {
        if ( !( $parent_ib_id = $this->get('parent_infoblock_id'))) {
            return;
        }
        return fx::data('infoblock', $parent_ib_id);
    }
    
    public function get_prop_inherited($path_str, $layout_id = null) {
        $own_result = null;
        $path = explode(".", $path_str);
        if ($path[0] == 'visual') {
            $c_i2l = $this->get_visual($layout_id);
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
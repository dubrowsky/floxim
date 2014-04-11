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
    
    public function is_layout() {
        return $this->get_prop_inherited('controller') == 'layout' && $this->get_prop_inherited('action') == 'show';
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
        $parent_result = null;
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
    
    public function init_controller() {
        $ctr = fx::controller(
                $this->get_prop_inherited('controller').'.'.
                $this->get_prop_inherited('action'),
                $this->get_prop_inherited('params')
        );
        $ctr->set_param('infoblock_id', $this['id']);
        return $ctr;
    }
    
    public function add_params($params) {
        $c_params = $this['params'];
        if (!is_array($c_params)) {
            $c_params = array();
        }
        $this['params'] = array_merge($c_params, $params);
        return $this;
    }
    
    public function is_available_on_page($page) {
        if ($this['site_id'] != $page['site_id']) {
            return;
        }
        
        $ids = $page->get_parent_ids();
        $ids []= $page['id'];
        $ids []= 0; // root
        
        if (!in_array($this['page_id'], $ids)) {
            return false;
        }
        
        // if page_id=0 blunt - all pages, ignored by the filter scope.pages
        if ($this['page_id'] != 0) {
            // scope - "this page only"
            if (fx::dig($this, 'scope.pages') == 'this' && $this['page_id'] != $page['id']) {
                return false;
            }
            // scope - "this level, and we look parent
            if (fx::dig($this, 'scope.pages') == 'children' && $this['page_id'] == $page['id']) {
                return false;
            }
        }
        // check for compliance with the filter type page
        $scope_page_type = fx::dig($this, 'scope.page_type');
        if ( $scope_page_type && $scope_page_type != $page['type'] ) {
            return false;
        }
        return true;
    }
    
    public function get_scope_string() {
        list($cib_page_id, $cib_pages, $cib_page_type) = array(
            $this['page_id'], 
            $this['scope']['pages'],
            $this['scope']['page_type']
        );
        
        if ($cib_page_id == 0) {
            $cib_page_id = fx::data('site', $this['site_id'])->get('index_page_id'); //$path[0]['id'];
        }
        if ($cib_pages == 'this') {
            $cib_page_type = '';
        } 
        if ($cib_pages == 'all' || empty($cib_pages)) {
            $cib_pages = 'descendants';
        }
        
        return $cib_page_id.'-'.$cib_pages.'-'.$cib_page_type;
    }
    
    public function read_scope_string($str) {
        list($scope_page_id, $scope_pages, $scope_page_type) = explode("-", $str);
        return array(
            'pages' => $scope_pages,
            'page_type' => $scope_page_type,
            'page_id' => $scope_page_id
        );
    }


    public function set_scope_string($str) {
        $ss = $this->read_scope_string($str);
        $new_scope = array(
            'pages' => $ss['pages'],
            'page_type' => $ss['page_type']
        );
        $this['scope'] = $new_scope;
        $this['page_id'] = $ss['page_id'];
    }
    
    /**
     * Returns number meaning "strength" (exactness) of infoblock's scope
     */
    public function get_scope_weight() {
        $s = $this['scope'];
        $pages = isset($s['pages']) ? $s['pages'] : 'all';
        $page_type = isset($s['page_type']) ? $s['page_type'] : '';
        if ($pages == 'this') {
            return 4;
        }
        if ($page_type) {
            if ($pages == 'children') {
                return 3;
            } else {
                return 2;
            }
        }
        if ($pages == 'children') {
            return 1;
        }
        return 0;
    }
}
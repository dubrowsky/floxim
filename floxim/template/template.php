<?php
class fx_template {
    
    protected $data = array();
    protected $meta = array();
    
    public function __construct($data = array()) {
        $this->data = $data;
    }
    
    public function print_var($var) {
        echo "<pre>" . htmlspecialchars(print_r($var, 1)) . "</pre>";
    }
    
    public function get_var($var_path) {
        return $this->_collection_get($this->data, $var_path);
    }
    
    public function set_var($var_path, $var_value) {
        $this->_collection_set($this->data, $var_path, $var_value);
    }
    
    public function add_var($var_path, $var_value) {
        $this->_collection_set($this->data, $var_path, $var_value, true);
    }
    
    public function set_var_meta($var_path, $meta_value) {
        $this->_collection_set($this->meta, $var_path, $meta_value);
    }
    
    public function add_var_meta($var_path, $meta_values) {
        $this->_collection_set($this->meta, $var_path, $meta_values, true);
    }
    
    public function get_var_meta($var_path, $meta_key = null) {
        if ($meta_key !== null) {
            $var_path .= '.'.$meta_key;
        }
        $var_match = $this->_collection_get($this->meta, $var_path);
        if ($var_match) {
            return $var_match;
        }
        // это костыль!!!
        $var_path = explode(".", $var_path);
        $root_path = $var_path[0].'.'.end($var_path);
        $root_match = $this->_collection_get($this->meta, $root_path);
        return $root_match;
    }
    
    protected function _collection_get($collection, $var_path) {
        $var_path = explode(".", $var_path);
        $arr = $collection;
        foreach ($var_path as $pp) {
            if (!isset($arr[$pp])) {
                return null;
            }
            $arr = $arr[$pp];
        }
        return $arr;
    }
    
    protected function _collection_set(&$collection, $var_path, $var_value, $merge = false) {
        $var_path = explode('.', $var_path);
        $arr =& $collection;
        foreach ($var_path as $pp) {
            if (!isset($arr[$pp])) {
                $arr[$pp]=array();
            }
            $arr =&  $arr[$pp];
        }
        if ($merge && is_array($arr) && is_array($var_value)) {
            $arr = array_merge_recursive($arr, $var_value);
        } else {
            $arr = $var_value;
        }
    }
    
    public function set_var_default($var_path, $value) {
         $c_val = $this->get_var($var_path);
         if ($c_val === NULL) {
             $this->set_var($var_path, $value);
             $c_val = $value;
         }
         return $c_val;
    }


    public function show_var($var_path, $context = null) {
        $val = $this->get_var($var_path);
        if ($context == 'attribute' || true) {
            return $val;
        }
        if ($this->get_var_meta($var_path, 'var_type') == 'param') {
            $val = "<!-- @".$var_path."-->".$val."<!-- //@".$var_path.'-->';
        } else {
            if (! ($var_meta = $this->get_var_meta($var_path)) ) {
                $var_meta = array();
            }
            $var_meta['id'] = $var_path;
            $var_meta['template'] = $this->_template_code.'.'.$this->_current_action;
            
            $var_meta = htmlentities(json_encode($var_meta));
            $val = '<span class="fx_template_var" data-fx_var="'.$var_meta.'">'.$val."</span>";
        }
        return $val;
    }
    public function render_area($area) {
        echo "<!-- area ".$area." -->\n";
        $area_blocks = $this->get_var('input.'.$area);
        if (!$area_blocks || !is_array($area_blocks)) {
            return;
        }
        foreach ($area_blocks as $ib) {
            if (! $ib instanceof fx_infoblock) {
                die();
            }
            $ib_res = $ib->render();
            if ( ($vis = $ib->get_infoblock2layout()) ) {
                if ($vis['wrapper_name']) {
                    $tpl_wrap = fx::template($this->_template_code);
                    $wrap_name = $vis['wrapper_name'];
                    $wrap_params = $vis['wrapper_visual'] ? $vis['wrapper_visual'] : array();
                    foreach ( $wrap_params as $wrap_param_key => $wrap_param_val) {
                        $tpl_wrap->set_var($wrap_param_key, $wrap_param_val);
                        $tpl_wrap->set_var_meta($wrap_param_key, array('var_type' => 'visual'));
                    }
                    //$wrap_params['content'] = $ib_res;
                    $tpl_wrap->set_var('content', $ib_res);
                    $tpl_wrap->set_var_meta('content', array('var_type' => 'param'));
                    $result = $tpl_wrap->render($wrap_name);//, $wrap_params);
                }
            }
            if (!$result) {
                $result = $ib_res;
            }
            $result = $this->_add_infoblock_meta($result, $ib);
            echo $result;
        }
        if (fx::env()->is_admin()) {
            echo "<a class='fx_infoblock_adder' data-fx_area='".$area."'>Добавить инфоблок</a>";
        }
        echo "<!-- // area ".$area." -->\n";
    }
    
    protected function _add_infoblock_meta($html_result, $infoblock) {
        $ib_info = array('id' => $infoblock['id']);
        if (($vis = $infoblock->get_infoblock2layout())) {
            $ib_info['visual_id'] = $vis['id'];
        }
        
        $ib_info = htmlentities(json_encode($ib_info));
        $html_result = '<div class="fx_infoblock" data-fx_infoblock="'.$ib_info.'">'.$html_result.'</div>';
        return $html_result;
    }
    
    protected $_current_action = null;

    public function render($action, $data = array()) {
        $this->_current_action = $action;
        //dev_log('rendering', $action, $data, get_class($this));
        foreach ($data as $dk => $dv) {
            $this->set_var($dk, $dv);
        }
        foreach (glob($this->_source_dir.'/*.{js,css}', GLOB_BRACE) as $f) {
            $file_http = str_replace(fx::config()->DOCUMENT_ROOT, '', $f);
            fx::page()->add_file($file_http);
        }
        $result = '<!-- template '.$this->_template_code.".".$action." -->\n";
        ob_start();
        $method = 'tpl_'.$action;
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            echo "<pre>".get_class($this)." has no action (".$action.") for data\n" . htmlspecialchars(print_r($data, 1)) . "</pre>";
        }
        $result .= ob_get_clean();
        if (fx::env()->is_admin()) {
            //$result = '<div class="fx_template_block">'.$result."</div>";
        }
        $result .= '<!-- // template'.$this->_template_code.".".$action." -->\n";
        $result = fx_template_field::replace_fields($result);
        return $result;
    }
}
?>
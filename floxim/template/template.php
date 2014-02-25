<?php
class fx_template {
    
    //protected $data = array();
    protected $action = null;
    protected $_parent = null;
    
    public function __construct($action, $data = array()) {
        $this->context_stack []= $data;
        $this->action = $action;
    }
    
    public function set_parent($parent_template) {
        $this->_parent = $parent_template;
        return $this;
    }


    public function set_var($var, $val) {
        if (count($this->context_stack) == 0) {
            $this->context_stack[]= array();
        }
        $this->context_stack[count($this->context_stack) - 1][$var] = $val;
    }
    /*
    public function get_var($var_path) {
        return fx::dig($this->data, $var_path);
    }
     * 
     */
    
    protected function print_var($val, $meta = null) {
        $tf = null;
        /*
        if ($meta && isset($meta['type']) && $meta['type'] == 'image' && is_numeric($val)) {
            $val = fx_filetable::get_path($val);
        }
         * 
         */
        if ($meta && isset($meta['var_type']) && fx::is_admin()) {
            $tf = new fx_template_field($val, $meta);
        }
        echo $tf ? $tf : $val;
    }
    
    protected function get_var_meta($var_name, $source = null) {
        if ($source && $source instanceof fx_content) {
            return $source->get_field_meta($var_name);
        }
        for ($i = count($this->context_stack) - 1; $i >= 0; $i--) {
            if ( !($this->context_stack[$i] instanceof fx_content) ) {
                continue;
            }
            if ( ($meta = $this->context_stack[$i]->get_field_meta($var_name))) {
                return $meta;
            }
        }
        if ($this->_parent) {
            return $this->_parent->get_var_meta($var_name);
        }
        return array();
    }
    
    protected $context_stack = array();
    
    public static $v_count = 0;
    public function v($name) {
        for ($i = count($this->context_stack) - 1; $i >= 0; $i--) {
            if (isset($this->context_stack[$i][$name])) {
                return $this->context_stack[$i][$name];
            }
        }
        if ($this->_parent) {
            return $this->_parent->v($name);
        }
    }
    
    public static function beautify_html($html) {
        $level = 0;
        $html = preg_replace_callback(
            '~\s*?<(/?)([a-z0-9]+)[^>]*?(/?)>\s*?~', 
            function($matches) use (&$level) {
                $is_closing = $matches[1] == '/';
                $is_single = in_array(strtolower($matches[2]), array('img', 'br', 'link')) || $matches[3] == '/';
                    
                if ($is_closing) {
                    $level = $level == 0 ? $level : $level - 1;
                }
                $tag = trim($matches[0]);
                $tag = "\n".str_repeat(" ", $level*4).$tag;
                
                if (!$is_closing && !$is_single) {
                    $level++;
                }
                return $tag;
                //fx::debug($matches);
            }, 
            $html
        );
        return $html;
    }
    
    /*
    public static function val($v) {
        return $v instanceof fx_template_field ? $v->get_value() : $v;
    }
    
    
    public function get_parent_var($var) {
        if (isset($this->data[$var])) {
            return $this->data[$var];
        }
        if (!$this->_parent) {
            return null;
        }
        return $this->_parent->get_parent_var($var);
    }
    
    
    
    public function set_data($data) {
        $this->data = $data;
    }
     * 
     */
    
    protected function _get_template_sign() {
        $template_name = preg_replace("~^fx_template_~", '', get_class($this));
        return $template_name.'.'.$this->action;
    }
    
    public static $area_replacements = array();

    /*
     * @param $mode - marker | data | both
     */
    public function render_area($area, $mode = 'both') {
    	$is_admin =  fx::is_admin();
        if ($mode != 'marker') {
            fx::trigger('render_area', array('area' => $area));
        }
        
        $area_blocks = fx::page()->get_area_infoblocks($area['id']);//$this->data['areas'][$area['id']];
        if (!$area_blocks || !is_array($area_blocks)) {
            $area_blocks = array();
        }
        usort($area_blocks, function($a, $b) {
            $a_pos = $a->get_prop_inherited('visual.priority');
            $b_pos = $b->get_prop_inherited('visual.priority');
            return $a_pos - $b_pos;
        });
        if ($is_admin) {
            ob_start();
        }
        if ($mode != 'marker') {
            foreach ($area_blocks as $ib) {
                if (! $ib instanceof fx_infoblock) {
                    die();
                }
                $result = $ib->render();
                echo $result;
            }
        }
        if ($is_admin) {
            $area_result = ob_get_clean();
            self::$area_replacements []= array($area, $area_result);
            $marker = '###fxa'.(count(self::$area_replacements)-1);
            if ($mode != 'both') {
                $marker .= '|'.$mode;
            }
            $marker .= '###';
            echo $marker;
        }
    }

    public function get_areas() {
        $areas = array();
        ob_start();
        fx::listen('render_area.get_areas', function($e) use (&$areas) {
            $areas[$e->area['id']]= $e->area;
        });
        $this->render(array('_idle' => true));
        fx::unlisten('render_area.get_areas');
        ob_get_clean();
        return $areas;
    }
    
    public function render(array $data = array()) {
        $this->context_stack[]= $data;
        /*
        set_error_handler(function() {
            fx::log(func_get_args());
        });
         * 
         */
        ob_start();
        $method = 'tpl_'.$this->action;
        if (method_exists($this, $method)) {
            try {
                $this->$method();
            } catch (Exception $e) {
                fx::log('template exception', $e);
            }
        } else {
           echo 'No tpl action: <code>'.get_class($this).".".$this->action.'</code>';
        }
        $result = ob_get_clean();
        
        if ($this->v('_idle')) {
            return $result;
        }
        
        if (fx::is_admin() && !$this->_parent) {
            self::$count_replaces++;
            $result = fx_template::replace_areas($result);
            $result = fx_template_field::replace_fields($result);
        }
        return $result;
    }
    public static $count_replaces = 0;
    
    // заполняется при компиляции
    protected $_templates = array();


    public function get_template_variants() {
        return $this->_templates;
    }
    
    public function get_info() {
        if (!$this->action) {
            throw new Exception('Specify template action/variant before getting info');
        }
        foreach ($this->_templates as $tpl) {
            if ($tpl['id'] == $this->action) {
                return $tpl;
            }
        }
    }
    
    public static function replace_areas($html) {
        if (!strpos($html, '###fxa')) {
            return $html;
        }
        $html = self::_replace_areas_wrapped_by_tag($html);
        $html = self::_replace_areas_in_text($html);
        return $html;
    }
    
    protected static function _replace_areas_wrapped_by_tag($html) {
    	//$html = preg_replace("~<!--.*?-->~s", '', $html);
    	$html = preg_replace_callback(
            /*"~(<[a-z0-9_-]+[^>]*?>)\s*###fxa(\d+)###\s*(</[a-z0-9_-]+>)~s",*/
            "~(<[a-z0-9_-]+[^>]*?>)\s*###fxa(\d+)\|?(.*?)###~s",
            function($matches) use ($html) {
                $replacement = fx_template::$area_replacements[$matches[2]];
                $mode = $matches[3];
                if ($mode == 'data') {
                    fx_template::$area_replacements[$matches[2]] = null;
                    return $replacement[1];
                }
                
                $tag = fx_template_html_token::create_standalone($matches[1]);
                $tag->add_meta(array(
                    'class' => 'fx_area',
                    'data-fx_area' => $replacement[0]
                ));
                $tag = $tag->serialize();
                
                if ($mode == 'marker') {
                    return $tag;
                }
                
                fx_template::$area_replacements[$matches[2]] = null;
                return $tag.$replacement[1].$matches[3]; 
            },
            $html
        );
        return $html;
    }
    
    protected static function _replace_areas_in_text($html) {
    	$html = preg_replace_callback(
            "~###fxa(\d+)\|?(.*?)###~",
            function($matches) {
                $mode = $matches[2];
                $replacement = fx_template::$area_replacements[$matches[1]];
                if ($mode == 'data') {
                    return $replacement[1];
                }
                $tag_name = 'div';
                $tag = fx_template_html_token::create_standalone('<'.$tag_name.'>');
                $tag->add_meta(array(
                    'class' => 'fx_area fx_wrapper',
                    'data-fx_area' => $replacement[0]
                ));
                $tag = $tag->serialize();
                fx_template::$area_replacements[$matches[1]] = null;
                return $tag.$replacement[1].'</'.$tag_name.'>';
            },
            $html
        );
        return $html;
    }
}
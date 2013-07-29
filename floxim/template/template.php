<?php
class fx_template {
    
    protected $data = array();
    protected $action = null;
    
    public function __construct($action, $data = array()) {
        $this->data = $data;
        $this->action = $action;
    }
    
    public function get_var($var_path) {
        return fx::dig($this->data, $var_path);
    }
    
    public function set_var($var_path, $var_value) {
        fx::dig_set($this->data, $var_path, $var_value);
    }
    
    protected function _get_template_sign() {
        $template_name = preg_replace("~^fx_template_~", '', get_class($this));
        return $template_name.'.'.$this->action;
    }
    
    public static $area_replacements = array();

    public function render_area($area) {
    	$is_admin =  fx::env('is_admin');
        fx::trigger('render_area', array('area' => $area));
        
        $area_blocks = $this->data['areas'][$area['id']];
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
        foreach ($area_blocks as $ib) {
            if (! $ib instanceof fx_infoblock) {
                die();
            }
            $result = $ib->render();
            echo $result;
        }
        if ($is_admin) {
        	$area_result = ob_get_clean();
        	self::$area_replacements []= array($area, $area_result);
        	echo '###fxa'.(count(self::$area_replacements)-1).'###';
        }
    }

    public function get_areas() {
        $areas = array();
        fx::listen('render_area.get_areas', function($e) use (&$areas) {
            $areas[$e->area['id']]= $e->area;
        });
        $this->render(array('_idle' => true));
        fx::unlisten('render_area.get_areas');
        return $areas;
    }
    
    public function render(array $data = array()) {
        foreach ($data as $dk => $dv) {
            $this->set_var($dk, $dv);
        }
        ob_start();
        $method = 'tpl_'.$this->action;
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            dev_log('tpl with no action called', get_class($this), $this->action, debug_backtrace());
            echo 'No tpl action: <code>'.get_class($this).".".$this->action.'</code>';
        }
        $result = ob_get_clean();
        
        if ($this->data['_idle']) {
            return $result;
        }
        
        if (fx::env('is_admin')) {
            $result = fx_template::replace_areas($result);
            $result = fx_template_field::replace_fields($result);
        }
        return $result;
    }
    
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
        $html = self::_replace_areas_wrapped_by_tag($html);
        $html = self::_replace_areas_in_text($html);
        return $html;
    }
    
    protected static function _replace_areas_wrapped_by_tag($html) {
    	$html = preg_replace("~<!--.*?-->~s", '', $html);
    	$html = preg_replace_callback(
    		"~(<[a-z0-9_-]+[^>]*?>)\s*###fxa(\d+)###\s*(</[a-z0-9_-]+>)~s",
    		function($matches) use ($html) {
    			$replacement = fx_template::$area_replacements[$matches[2]];
    			$tag = fx_template_html_token::create_standalone($matches[1]);
                $tag->add_meta(array(
                    'class' => 'fx_area',
                    'data-fx_area' => $replacement[0]
                ));
                $tag = $tag->serialize();
                fx_template::$area_replacements[$matches[2]] = null;
    			return $tag.$replacement[1].$matches[3]; 
    		},
    		$html
		);
		return $html;
    }
    
    protected static function _replace_areas_in_text($html) {
    	$html = preg_replace_callback(
    		"~###fxa(\d+)###~",
    		function($matches) {
    			$replacement = fx_template::$area_replacements[$matches[1]];
    			//$tag_name = preg_match("~<(?:div|ul|li|table|br|nav)~i", $content) ? 'div' : 'span';
                $tag_name = 'div';
    			$tag = fx_template_html_token::create_standalone('<'.$tag_name.'>');
                $tag->add_meta(array(
                    'class' => 'fx_area',
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
    
    protected function _apply_modifiers($var, $modifiers) {
    	$val = $var instanceof fx_template_field ? $var->get_value() : $var;
    	echo "~".$val."~";
    	foreach ($modifiers as $mod) {
    		$callback = array_shift($mod);
    		if (!is_callable($callback)) {
    			continue;
    		}
    		$self_key = array_keys($mod, "self");
    		if (isset($self_key[0])) {
    			$mod[$self_key[0]] = $val;
    		} else {
    			array_unshift($mod, $val);
    		}
    		echo '<br>'.$callback."(<pre>".htmlspecialchars(print_r($mod,1))."</pre>)";
    		$val = call_user_func_array($callback, $mod);
    		echo "<pre>".htmlspecialchars(print_r($val,1))."</pre>";
    		//echo "<pre>".htmlspecialchars(print_r($mod,1))."</pre>";
    	}
    	echo "<pre>".htmlspecialchars(print_r($val,1))."</pre>";
    	//echo "<pre>".htmlspecialchars(print_r($modifiers,1))."</pre>";
    	return $var;
    }
}
?>
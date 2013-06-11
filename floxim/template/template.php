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
        /*
        if ( ($tpl_files = glob($this->_source_dir.'/*.{js,css}', GLOB_BRACE)) ) {
			foreach ($tpl_files as $f) {
				if (!preg_match("~_[^/]+$~", $f)) {
					$file_http = str_replace(fx::config()->DOCUMENT_ROOT, '', $f);
					fx::page()->add_file($file_http);
				}
			}
        }*/
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
    
    public static function replace_areas($html) {
        $html = self::_replace_areas_wrapped_by_tag($html);
        $html = self::_replace_areas_in_text($html);
        return $html;
    }
    
    protected static $_area_regexp = "###fx_area\|([^\|]*?)\|(.+?)###(.+?)###fx_area_end###";
    
    public static $_adder_html = "<a class='fx_infoblock_adder'>Добавить инфоблок</a>";


    protected static function _replace_areas_wrapped_by_tag($html) {
    	$html = preg_replace("~<!--.*?-->~s", '', $html);
    	$html = preg_replace_callback(
    		"~(<[a-z0-9_-]+[^>]*?>)\s*###fxa(\d+)###\s*(</[a-z0-9_-]+>)~s",
    		function($matches) {
    			$replacement = fx_template::$area_replacements[$matches[2]];
    			$tag = fx_html_token::create_standalone($matches[1]);
                $tag->add_meta(array(
                    'class' => 'fx_area',
                    'data-fx_area' => $replacement[0]
                ));
                $tag = $tag->serialize();
                fx_template::$area_replacements[$matches[2]] = null;
    			return $tag.$replacement[1].fx_template::$_adder_html.$matches[3]; 
    		},
    		$html
		);
		return $html;
		/*
        if (!preg_match("~###fx_area~", $html)) {
            return $html;
        }
        $html = preg_replace("~<!--.*?-->~s", '', $html);

        $area_regexp = '~(<([a-z0-9]+)[^>]*?>)([\s]*?)'.self::$_area_regexp.'([\s]*?)(</\2>)~s';
        preg_match_all($area_regexp, $html, $areas);
        $html = preg_replace_callback(
            $area_regexp, 
            function($matches) {
                // если внутри есть еще маркер - значит, мы наловили лишнего
                if (preg_match("~###fx_area~", $matches[6])) {
                    return $matches[0];
                }
                $tag = fx_html_token::create_standalone($matches[1]);
                $tag->add_meta(array(
                    'class' => 'fx_area',
                    'data-fx_area' => htmlentities($matches[5])
                ));
                $tag = $tag->serialize();
                return $tag.$matches[3].$matches[6].$matches[7].$matches[8];
            },
            $html
        );
        return $html;
        */
    }
    
    protected static function _replace_areas_in_text($html) {
    	$html = preg_replace_callback(
    		"~###fxa(\d+)###~",
    		function($matches) {
    			$replacement = fx_template::$area_replacements[$matches[1]];
    			$tag_name = preg_match("~<(?:div|ul|li|table|br)~i", $content) ? 'div' : 'span';
    			$tag = fx_html_token::create_standalone('<'.$tag_name.'>');
                $tag->add_meta(array(
                    'class' => 'fx_area',
                    'data-fx_area' => $replacement[0]
                ));
                $tag = $tag->serialize();
                fx_template::$area_replacements[$matches[1]] = null;
                return $tag.$replacement[1].fx_template::$_adder_html.'</'.$tag_name.'>';
    		},
    		$html
		);
		return $html;
		/*
        $html = preg_replace_callback("~".self::$_area_regexp."~s", function($matches) {
            $content = $matches[3];
            $tag = preg_match("~<(?:div|ul|li|table|br)~i", $content) ? 'div' : 'span';
            return '<'.$tag.' class="fx_area" data-fx_area="'.htmlentities($matches[2]).'">'.
                    $matches[3].'</'.$tag.'>';
        }, $html);
        return $html;
        */
    }
}
?>
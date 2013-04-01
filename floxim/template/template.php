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

    public function render_area($area) {
        //echo "<!-- area ".$area." -->\n";
        if (fx::env('is_admin')) {
            echo "###fx_area|".$area['id']."|".json_encode($area)."###";
        }
        $area_blocks = $this->get_var('input.'.$area['id']);
        if (!$area_blocks || !is_array($area_blocks)) {
            $area_blocks = array();
        }
        foreach ($area_blocks as $ib) {
            if (! $ib instanceof fx_infoblock) {
                die();
            }
            $result = $ib->render();
            echo $result;
        }
        if (fx::env('is_admin')) {
            echo "<a class='fx_infoblock_adder' data-fx_area='".$area."'>Добавить инфоблок</a>";
            echo "###fx_area_end###";
        }
        //echo "<!-- // area ".$area." -->\n";
    }

    public function render(array $data = array()) {
        foreach ($data as $dk => $dv) {
            $this->set_var($dk, $dv);
        }
        foreach (glob($this->_source_dir.'/*.{js,css}', GLOB_BRACE) as $f) {
            if (!preg_match("~_[^/]+$~", $f)) {
                $file_http = str_replace(fx::config()->DOCUMENT_ROOT, '', $f);
                fx::page()->add_file($file_http);
            }
        }
        $result = '<!-- template '.$this->_get_template_sign()." -->\n";
        ob_start();
        $method = 'tpl_'.$this->action;
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            dev_log('tpl with no action called', get_class($this), $this->action, debug_backtrace());
            echo 'No tpl action: <code>'.get_class($this).".".$this->action.'</code>';
        }
        $result .= ob_get_clean();
        $result .= '<!-- // template'.$this->_get_template_sign()." -->\n";
        if (fx::env('is_admin')) {
            $result = fx_template_field::replace_fields($result);
            $result = fx_template::replace_areas($result);
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
    
    protected static function _replace_areas_wrapped_by_tag($html) {
        if (!preg_match("~###fx_area~", $html)) {
            return $html;
        }
        $html = preg_replace("~<!--.*?-->~s", '', $html);
        $area_regexp = '~(<([a-z0-9]+)[^>]*?>)([\s]*?)'.self::$_area_regexp.'([\s]*?)(</\2>)~s';
        preg_match_all($area_regexp, $html, $areas);
        dev_log('FOUNDAREAS', $areas, htmlspecialchars($html), $area_regexp);
        $html = preg_replace_callback(
            $area_regexp, 
            function($matches) {
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
    }
    
    protected static function _replace_areas_in_text($html) {
        $html = preg_replace_callback("~".self::$_area_regexp."~s", function($matches) {
            $content = $matches[3];
            $tag = preg_match("~<(?:div|ul|li|table|br)~i", $content) ? 'div' : 'span';
            return '<'.$tag.' class="fx_area" data-fx_area="'.htmlentities($matches[2]).'">'.
                    $matches[3].'</'.$tag.'>';
        }, $html);
        return $html;
    }
}
?>
<?php
class fx_template {
    
    protected $data = array();
    protected $meta = array();
    
    public function __construct($data = array(), $action = null) {
        $this->data = $data;
        if ($action) {
            $this->action = $action;
        }
    }
    
    public function get_var($var_path) {
        return fx::dig($this->data, $var_path);
    }
    
    public function set_var($var_path, $var_value) {
        fx::dig_set($this->data, $var_path, $var_value);
    }
    
    protected function _get_template_sign() {
        return $this->_template_code.'.'.$this->_current_action;
    }

    public function render_area($area) {
        echo "<!-- area ".$area." -->\n";
        $area_blocks = $this->get_var('input.'.$area);
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
        }
        echo "<!-- // area ".$area." -->\n";
    }
    
    protected $_current_action = null;

    public function render($action = null, $data = array()) {
        if ($action == null && $this->action) {
            $action = $this->action;
        }
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
            echo 'No tpl action: <code>'.get_class($this).".".$action.'</code>';
        }
        $result .= ob_get_clean();
        $result .= '<!-- // template'.$this->_template_code.".".$action." -->\n";
        $result = fx_template_field::replace_fields($result);
        return $result;
    }
    
    // заполняется при компиляции
    protected $_templates = array();


    public function get_template_variants() {
        return $this->_templates;
    }
}
?>
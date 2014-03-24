<?php
/*
 * Class collects the sources from different sources
 * Able to find sources on the template name
 * Stores the result will tell me where
 */
class fx_template_loader {
    protected $_source_files = array();
    protected $_template_name = null;
    
    public function __construct() {
        
    }
    
    public function add_source_file($source_file) {
        if (!file_exists($source_file)) {
            throw new Exception('Source file '.$source_file.' does not exist');
        }
        if (is_dir($source_file)) {
            $this->add_source_dir($source_file);
            return;
        }
        $this->_source_files[]= realpath($source_file);
    }
    
    public function add_source_dir($source_dir) {
        if (!file_exists($source_dir) || !is_dir($source_dir)) {
            throw new Exception('Source dir '.$source_dir.' does not exist');
        }
        
        $tpl_files = glob($source_dir.'/*.tpl');
        if (!$tpl_files) {
            return;
        }
        foreach ($tpl_files as $tpl_file) {
            // Do not include the template files that begin with "_"
            if (preg_match("~/_[^/]+$~", $tpl_file)) {
                continue;
            }
            $this->add_source_file($tpl_file);
        }
    }
    
    public function add_source($file_or_dir) {
        if (!file_exists($file_or_dir)) {
            throw new Exception('Source '.$file_or_dir.' does not exist');
        }
        is_dir($file_or_dir) 
            ? $this->add_source_dir($file_or_dir) 
            : $this->add_source_file($file_or_dir);
    }
    
    protected $_controller_type = null;
    protected $_controller_name = null;
    
    public function set_name($tpl_name) {
        $tpl_name_parts = null;
        if (preg_match("~^(layout|component|widget|helper)_([a-z0-9_]+)$~", $tpl_name, $tpl_name_parts)) {
            $this->_controller_type = $tpl_name_parts[1];
            $this->_controller_name = $tpl_name_parts[2];
        } else {
            $this->_controller_type = 'other';
            $this->_controller_name = $tpl_name;
        }
        $this->_template_name = $tpl_name;
    }

    public function add_default_source_dirs() {
        $dir_end = $this->_controller_type.'/'.$this->_controller_name;
        $root = fx::config()->DOCUMENT_ROOT;
        
        $dirs = array(
            $root.'/'.$dir_end,
            $root.'/floxim/std/'.$dir_end
        );
        
        foreach ($dirs as $dir) {
            try {
                $this->add_source_dir($dir);
            } catch (Exception $e) {

            }
        }
    }
    
    protected $_target_dir = null;
    protected $_target_file = null;
    
    public function set_target_dir($dir) {
        $this->_target_dir = $dir;
    }
    
    public function set_target_file($filename) {
        $this->_target_file = $filename;
    }
    
    public function get_target_path() {
        if (!$this->_target_dir) {
            $this->_target_dir = fx::config()->COMPILED_TEMPLATES_FOLDER;
        }
        if (!$this->_target_file) {
            $this->_target_file = $this->_template_name.'.php';
        }
        return $this->_target_dir.'/'.$this->_target_file;
    }


    /*
     * Automatically load the template by name
     * Standard scheme
     */
    public static function autoload($tpl_name) {
        $processor = new self();
        $processor->set_name($tpl_name);
        $processor->add_default_source_dirs();
        $processor->load();
    }
    
    public function is_fresh($target_path) {
        $ttl = fx::config('COMPILED_TEMPLATES_TTL');
        // special mode, templates are recompile every time
        if ($ttl < 0) {
            return false;
        }
        // file is not created yet
        if (!file_exists($target_path)) {
            return false;
        }
        $target_time = filemtime($target_path);
        // file is fresh enough
        if ((time() - $target_time) < $ttl) {
            return true;
        }
        // compare sources to compiled template
        foreach ($this->_source_files as $source) {
            if (filemtime($source) > $target_time) {
                // some source updated
                return false;
            }
        }
        // all sources are older than compiled
        return true;
    }
    
    public function load() {
        $target_path = $this->get_target_path();
        if ($this->is_fresh($target_path)) {
            require_once ($target_path);
            return;
        }
        $this->save();
        require_once ($target_path);
    }
    
    public function compile() {
        $source = $this->build_source();
        $parser = new fx_template_parser();
        $tree = $parser->parse($source);
        $compiler = new fx_template_compiler();
        $res = $compiler->compile($tree);
        return $res;
    }
    
    public function save() {
        $source = $this->compile();
        fx::files()->writefile($this->get_target_path(), $source);
    }
    /**
     * Convert files-source code in one big
     * @return string
     */
    public function build_source() {
        $res = '{templates name="'.$this->_template_name.'"';
        if (!empty($this->_controller_type)) {
            $res .= ' controller_type="'.$this->_controller_type.'"';
        }
        if (!empty($this->_controller_name)) {
            $res .= ' controller_name="'.$this->_controller_name.'"';
        }
        $res .= "}";
        foreach ($this->_source_files as $file) {
            $res .= '{templates source="'.$file.'"}';
            $res .= $this->read_file($file);
            $res .= '{/templates}';
        }
        $res .= '{/templates}';
        return $res;
    }
    
    public function read_file($file) {
        $file_data = file_get_contents($file);
        
        // convert fx::attributes to the canonical Smarty-syntax
        $T = new fx_template_html($file_data);
        try {
            $file_data = $T->transform_to_floxim();
        } catch (Exception $e) {
            fx::debug('Floxim html parser error', $e->getMessage(), $file);
        }

        // remove fx-comments
        $file_data = preg_replace("~\{\*.*?\*\}~s", '', $file_data);
        $file_data = trim($file_data);
        if (!preg_match("~^{template~", $file_data)) {
            $file_data = $this->wrap_file($file, $file_data);
        }
        return $file_data;
    }
    
    public function wrap_file($file, $file_data) {
        $is_layout = $this->_controller_type == 'layout';
        $tpl_of = 'false';
        if ($is_layout) {
            $tpl_id = '_layout_body';
        } else {
            $file_tpl_name = null;
            preg_match('~([a-z0-9_]+)\.tpl$~', $file, $file_tpl_name);
            $tpl_id = $file_tpl_name[1];
            if ($this->_controller_type == 'component' && $this->_controller_name) {
                $tpl_of = 'component_'.$this->_controller_name.'.'.$tpl_id;
            }
        }
        $file_data = 
            '{template id="'.$tpl_id.'" of="'.$tpl_of.'"}'.
               $file_data.
            '{/template}';
        return $file_data;
    }
}

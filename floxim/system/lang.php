<?php
class fx_lang {
    protected $loaded = array();
    protected $lang = null;
    
    const DEFAULT_DICT = 'system';
    
    public function __construct($lang=null) {
        if (!$lang)
            $this->lang = fx::config()->ADMIN_LANG;
        else
            $this->lang = $lang;
    }
    
    
    public function get_string($string, $dict = null, $lang = null) {
        if ($dict === null) {
            $dict = self::DEFAULT_DICT;
        }
        if ($lang === null) {
            $lang = $this->lang;
        }
        if (!isset($this->loaded[$dict][$lang])) {
            $this->load_dictionary($dict, $lang);
        }
        if (array_key_exists($string, $this->loaded[$dict][$lang])) {
            $res = $this->loaded[$dict][$lang][$string];
            return empty($res) ? $string : $res;
        }
        $this->add_string($string, $dict, $lang);
        return $string;
    }
    
    public function check_string($string, $dict) {
        
        $lang = $this->lang;
        if (!isset($this->loaded[$dict][$lang])) {
            $this->load_dictionary($dict, $lang);
        }
        return array_key_exists($string, $this->loaded[$dict][$lang]);
    }
    
    public function get_dict_file($dict, $lang) {
        return fx::config()->FILES_FOLDER.'php_dictionaries/'.$lang.'.'.$dict.'.php';
    }
    
    public function drop_dict_files($dict) {
        foreach (fx::config()->AVAILABLE_LANGUAGES as $lang) {
            $file = fx::alang()->get_dict_file($dict, $lang);
            if (file_exists($file)) {
                unlink($file);
            } 
        }
    }
    
    protected function load_dictionary($dict, $lang) {
        $dict_file = self::get_dict_file($dict, $lang);
        if (!file_exists($dict_file)) {
            $this->dump_dictionary($dict, $lang, $dict_file);
        }
        $this->loaded[$dict][$lang] = @ include_once($dict_file);
    }
    
    protected function dump_dictionary($dict, $lang, $file) {
        $data = fx::data('lang_string')->where('dict', $dict)->all();
        if (!file_exists(dirname($file))) {
            mkdir(dirname($file));
        }
        $res = array();
        foreach ($data as $s) {
            $res[$s['string']] = $s['lang_'.$lang];        
        }
        
        $fh = fopen($file, 'w');
        fputs($fh, "<?php\nreturn ".var_export($res,1).";?>");
        /*
        foreach ($data as $s) {
            $key = str_replace('"', '\"', $s['string']);
            $val = str_replace('"', '\"', $s['lang_'.$lang]);
            fputs(
                $fh, 
                '"'.$key.'" => '.($key == $val ? 'null' : '"'.$val.'"').",\n"
            );
        }
        fputs($fh, ");");
        */
        fclose($fh);
    }
    
    public function add_string($string, $dict, $lang) {
        fx::data('lang_string')->create(
                array(
                    'string' => $string,
                    'dict' => $dict,
                    'lang_'.$lang => $string
                )
        )->save();
    }
}
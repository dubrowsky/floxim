<?php
class fx_system_page {

    // title, keywords, description
    protected $metatags = array();
    
    protected $_macroconst = array();

    /**
     * Установить мета-тег для страницы
     * @param string title, keywords, description
     * @param string value
     */
    public function set_metatags($item, $value, $post = '') {
        $item = 'seo_'.$item;
        $this->metatags[$item] = $value;
        if ($post) {
            $this->metatags_post[$item] = $post;
        }
        return $this;
    }

    /**
     * Получить текущий мета-тег страницы
     * @param mixed title, keywords, description
     * @param mixed value or array
     */
    public function get_metatags($item = '') {
        $item = 'seo_'.$item;
        if ($item) {
            return isset($this->metatags[$item]) ? $this->metatags[$item] : null;
        }
        return $this->metatags;
    }

    public function add_file($file) {
        if (preg_match("~\.(?:less|css)$~", $file)) {
            return $this->add_css_file($file);
        }
        if (substr($file, strlen($file) - 3) == '.js') {
            return $this->add_js_file($file);
        }
    }
    
    public function add_css_file($file) {
        if (preg_match("~\.less$~", $file)) {
            $doc_root = fx::config()->DOCUMENT_ROOT;
            $target_path = fx::config()->HTTP_FILES_PATH.'asset_cache/'.md5($file).'.css';;
            
            $full_target_path = $doc_root.$target_path;
            $full_source_path = $doc_root.$file;

            if (!file_exists($full_source_path)) {
                return;
            }
            
            if (!file_exists($full_target_path) || filemtime($full_source_path) > filemtime($full_target_path)) {
                fx::profiler()->block('compile less '.$file);
                require_once $doc_root.'/floxim/lib/lessphp/lessc.inc.php';
                $fh = fopen($full_target_path, 'w');
                $http_base = preg_replace("~[^/]+$~", '', $file);
                $file_content = file_get_contents($full_source_path);
                $file_content = $this->_css_url_replace($file_content, $http_base);
                $less = new lessc();
                $file_content = $less->compile($file_content);
                fputs($fh, $file_content);
                fclose($fh);
                fx::profiler()->stop();
            }
            $this->_files_css[]= $target_path;
            $this->_all_css[] = $target_path;
            return;
        }
        $this->_files_css[] = $file;
    }

    public function add_css_bundle ($files, $params = array()) {

        if (fx::config()->IS_DEV_MODE) {
            foreach ($files as $f) {
                $this->add_css_file($f);
            }
            return;
        }
        if (!isset($params['name'])) {
            $params['name'] = md5(join($files));
        }
        $params['name'] .= '.cssgz';
        $doc_root = fx::config()->DOCUMENT_ROOT;
        $http_path = fx::config()->HTTP_FILES_PATH.'asset_cache/'.$params['name'];
        $full_path = $doc_root.$http_path;
        
        //$this->_all_css = array_merge($this->_all_css, $files);
        if (!file_exists($full_path)) {
            $less_flag = false;
            $file_content = '';
            foreach ($files as $file) {
                if (preg_match("~\.less$~", $file)) {
                    $less_flag = true;
                }
                //echo $file."<br />";
                
                if (preg_match("~^http://~i", $file)) {
                    $file_contents = file_get_contents($file);
                } else {
                    $http_base = preg_replace("~[^/]+$~", '', $file);
                    $file = $doc_root.$file;
                    $file_contents = file_get_contents($file);
                    $file_contents = $this->_css_url_replace($file_contents, $http_base);
                    /*$file_contents = preg_replace_callback(
                        '~(url\([\'\"]?)([^/][^\)]+)~i', 
                        function($matches) use ($http_base) {
                            //fx::debug($matches);
                            if (preg_match("~data\:~", $matches[0])) {
                                return $matches[0];
                            }
                            return $matches[1].$http_base.$matches[2];
                        }, 
                        $file_contents
                    );*/
                    //preg_match_all('~url\([\'\"]?[^/]~i', $file_contents, $urls);
                    //fx::debug($urls);
                }
                //fx::debug($file_contents);
                $file_content .= $file_contents."\n";
            }
            //die();

            if ($less_flag) {
                require_once $doc_root.'/floxim/lib/lessphp/lessc.inc.php';
                $less = new lessc();
                $file_content = $less->compile($file_content);
            }
            $fh = gzopen($full_path, 'wb5');
            gzwrite($fh, $file_content);
            gzclose($fh);
            $fh = fopen(preg_replace("~\.cssgz$~", ".css", $full_path), 'w');
            fputs($fh, $file_content);
            fclose($fh);
        }
        //die();
        if (!$this->_accept_gzip()) {
            $http_path = preg_replace("~\.cssgz$~", ".css", $http_path);
        }
        $this->_files_css[]= $http_path;
    }

    protected function _css_url_replace ($file, $http_base) {
        $file = preg_replace_callback(
            '~(url\([\'\"]?)([^/][^\)]+)~i', 
            function($matches) use ($http_base) {
                if (preg_match("~data\:~", $matches[0])) {
                    return $matches[0];
                }
                return $matches[1].$http_base.$matches[2];
            }, 
            $file
        );
        return $file;
    }

    // both simple scripts & scripts from bundles
    protected $_all_js = array();
    
    public function add_js_file($file) {
        if (!in_array($file, $this->_all_js)) {
            $this->_files_js[] = $file;
            $this->_all_js[]= $file;
        }
    }
    
    
    
    public function add_js_bundle($files, $params = array()) {
        // for dev mode
        if (fx::config()->IS_DEV_MODE) {
            foreach ($files as $f) {
                $this->add_js_file($f);
            }
            return;
        }
        if (!isset($params['name'])) {
            $params['name'] = md5(join($files));
        }
        $params['name'] .= '.jsgz';
        $doc_root = fx::config()->DOCUMENT_ROOT;
        $http_path = fx::config()->HTTP_FILES_PATH.'asset_cache/'.$params['name'];
        $full_path = $doc_root.$http_path;
        
        $this->_all_js = array_merge($this->_all_js, $files);
        
        if (!file_exists($full_path)) {
            require_once(fx::config()->INCLUDE_FOLDER.'JSMinPlus.php');
            $bundle_content = '';
            foreach ($files as $i => $f) {
                if (!preg_match("~^http://~i", $f)) {
                    $f = $doc_root.$f;
                }
                $file_content = file_get_contents($f);
                if (!preg_match("~\.min~", $f)) {
                    $minified = JSMinPlus::minify($file_content);
                    $file_content = $minified;
                }
                $bundle_content .= $file_content.";\n";
            }
            $fh = gzopen($full_path, 'wb5');
            gzwrite($fh, $bundle_content);
            gzclose($fh);
            $fh = fopen(preg_replace("~\.jsgz$~", ".js", $full_path), 'w');
            fputs($fh, $bundle_content);
            fclose($fh);
        }
        if (!$this->_accept_gzip()) {
            $http_path = preg_replace("~\.jsgz$~", ".js", $http_path);
        }
        $this->_files_js[]= $http_path;
    }
    
    protected function _accept_gzip() {
        if (!isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
            return false;
        }
        return in_array('gzip', explode(",", $_SERVER['HTTP_ACCEPT_ENCODING']));
    }

    public function add_data_js($keyword, $values) {
        $this->_data_js[$keyword] = $values;
    }

    public function get_data_js() {
        return $this->_data_js;
    }

    public function add_js_text($text) {
        $this->_js_text[] = $text;
    }

    public function get_js_text() {
        return $this->_js_text;
    }

    /**
     * Создает или изменяет значение макроконстанты
     * Можно вставить макроконстанту в любую часть страницы:
     * <?=fx::page()->set_macroconst('foo')?>
     * А потом поменять ее значение:
     * fx::page()->set_macroconst('foo', 'bar');
     */
    public function set_macroconst($name, $value = '') {
        if ( isset($this->_macroconst[$name]) ) {
            $this->_macroconst[$name]['value'] = $value;
            $hash  = $this->_macroconst[$name]['hash'];
        }
        else {
            $hash = strtoupper(md5(rand().$name.time()));
            $hash =  '%%FX_'.$hash.'%%';
            $this->_macroconst[$name] = array('hash' => $hash, 'value' => $value);
            
        }
        
        return $hash;
    }

    public function set_numbers($block_number = 1, $field_number = 1) {
        $this->block_number = intval($block_number);
        $this->field_number = intval($field_number);
    }

    public function set_after_body($txt) {
        $this->_after_body[] = $txt;
    }

    public function post_process($buffer) {
        /*
        if ( fx::core()->is_admin_mode() ) {
            return $buffer;
        }
         * 
         */

        if ($this->metatags['seo_title']) {
            $r = "<title>".strip_tags($this->metatags['seo_title'])."</title>".PHP_EOL;;
        }
        if ($this->metatags['seo_description']) {
            $r .= '<meta name="description" content="' 
                    . strip_tags($this->metatags['seo_description']) . '" />'.PHP_EOL;;
        }
        if ($this->metatags['seo_keywords']) {
            $r .= '<meta name="keywords" content="' 
                    . strip_tags($this->metatags['seo_keywords']) . '" />'.PHP_EOL;;
        }

        if ($this->_files_css) {
            $files_css = array_unique($this->_files_css);
            foreach ($files_css as $v) {
                $r .= '<link rel="stylesheet" type="text/css" href="'.$v.'" />'.PHP_EOL;
            }
        }
        if ($this->_files_js) {
            $files_js = array_unique($this->_files_js);
            
            foreach ($files_js as $v) {
                $r .= '<script type="text/javascript" src="'.$v.'" ></script>'.PHP_EOL;
            }
        }
        
        if (!preg_match("~<head(\s[^>]*?|)>~", $buffer)) {
            if (preg_match("~<html[^>]*?>~", $buffer)) {
                $buffer = preg_replace("~<html[^>]*?>~", '$0<head> </head>', $buffer);
            } else {
                $buffer = '<html><head> </head>'.$buffer.'</html>';
            }
        }
        
        $buffer = preg_replace("~<head(\s[^>]*?|)>~", '$0'.$r, $buffer);

        if ($this->_after_body) {
            $after_body = $this->_after_body;
            $buffer = preg_replace_callback(
                '~<body[^>]*?>~', 
                function($body) use ($after_body) {
                    return $body[0].join("\r\n", $after_body);
                },
                $buffer
            );
        }
        $buffer = str_replace("<body", "<body data-fx_page_id='".fx::env('page_id')."'", $buffer);

        
        if (fx::is_admin()) {
            $js = '<script type="text/javascript">'.PHP_EOL;
            if ( ($js_text = $this->get_js_text() )) {
                $js .= join(PHP_EOL, $js_text).PHP_EOL;
            }
            $js .= '</script>'.PHP_EOL;
            $buffer = str_replace('</body>', $js.'</body>', $buffer);
        }
        $buffer = $this->replace_macroconst($buffer);

        return $buffer;
    }
    
    protected function replace_macroconst ( $buffer ) {
        foreach ( $this->_macroconst  as $v ) {
            $buffer = str_replace($v['hash'], $v['value'], $buffer);
        }
       
        
        return $buffer;
    }
    
    protected $areas = array();
    public function set_infoblocks($areas) {
        $this->areas = $areas;
    }
    
    public function get_area_infoblocks($area_id) {
        return isset($this->areas[$area_id]) ? $this->areas[$area_id] : null;
    }
}
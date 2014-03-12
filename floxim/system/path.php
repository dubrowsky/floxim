<?php
class fx_path {
    
    protected $root = '';
    public function __construct() {
        $this->root = DOCUMENT_ROOT;
    }
    
    protected $registry = array();
    
    public function register($key, $path) {
        if (is_array($path)) {
            foreach ($path as &$p) {
                $p = $this->to_http($p);
            }
        } else {
            $path = $this->to_http($path);
        }
        if (isset($this->registry[$key]) && is_array($this->registry[$key])) {
            if (is_array($path)) {
                $this->registry[$key] = array_merge($this->registry[$key], $path);
            } else {
                $this->registry[$key][]= $path;
            }
        } else {
            $this->registry[$key] = $path;
        }
    }
    
    public function abs($key, $tale = null) {
        if (!isset($this->registry[$key])) {
            return null;
        }
        $path = $this->registry[$key];
        if (!is_null($tale)) {
            $tale = '/'.$tale;
        }
        if (is_array($path)) {
            foreach ($path as &$p) {
                $p = $this->to_abs($p.$tale);
            }
        } else {
            $path = $this->to_abs($path.$tale);
        }
        return $path;
    }
    
    public function http($key, $tale = null) {
        if (!isset($this->registry[$key])) {
            return null;
        }
        $path = $this->registry[$key];
        if (!is_null($tale)) {
            $path .= '/'.$tale;
        }
        $path = $this->to_http($path);
        return $path;
    }
    
    public function to_http($path) {
        if (!is_string($path)) {
            fx::debug(debug_backtrace());
            die();
        }
        $ds = "[".preg_quote('\/')."]";
        $path = preg_replace("~".$ds."~", DIRECTORY_SEPARATOR, $path);
        $path = preg_replace("~^".preg_quote($this->root)."~", '', $path);
        $path = preg_replace("~".$ds."~", '/', $path);
        if (!preg_match("~^/~", $path)) {
            $path = '/'.$path;
        }
        $path = preg_replace("~/+~", '/', $path);
        return $path;
    }
    
    public function to_abs($path) {
        $path = str_replace("/", DIRECTORY_SEPARATOR, $path);
        $ds = preg_quote(DIRECTORY_SEPARATOR);
        $path = preg_replace("~^".$ds."~", '', $path);
        $path = preg_replace("~".$ds."$~", '', $path);
        $path = preg_replace("~".$ds."+~", DIRECTORY_SEPARATOR, $path);
        $path = preg_replace("~^".preg_quote($this->root)."~", '', $path);
        $path = $this->root.DIRECTORY_SEPARATOR.$path;
        $path = preg_replace("~".$ds."+~", DIRECTORY_SEPARATOR, $path);
        return $path;
    }
    
    public function exists($path) {
        return file_exists($this->to_abs($path));
    }
}
<?php
class fx_http {
    
    protected $status_values = array(
        200 => 'OK',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily',
        403 => 'Forbidden',
        404 => 'Not Found'
    );
    
    public function status($code) {
        header("HTTP/1.1 ".$code." ".$this->status_values[$code]);
    }
    
    public function redirect($target_url, $status) {
        $this->status($status);
        header("Location: ".$target_url);
        die();
    }
    
    public function refresh() {
        $this->redirect($_SERVER['REQUEST_URI'], 200);
    }
    
    public function header($name, $value = null) {
        header($name.(!is_null($value) ? ": ".$value : ''));
    }
}
<?php
class fx_data_content_user extends fx_data {
    public function __construct() {
        $this->sql_function['password'] = 'MD5';
        parent::__construct();
    }
}
?>
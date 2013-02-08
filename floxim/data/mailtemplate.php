<?php

class fx_data_mailtemplate extends fx_data {

    public function __construct() {
        $this->table = 'mail_template';
        parent::__construct();
    }

    public function get_tpl($keyword) {
        $result = $this->get('keyword', $keyword);
        if (!$result) {
            $result = $this->create(array('keyword' => $keyword));
        }
        return $result;
    }

}

?>
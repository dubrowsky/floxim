<?php

class fx_permission {

    protected $_user;

    public function __construct(fx_content_user $user) {
        $this->_user = $user;
    }

    public function is_supervisor() {
        return true;
        //return ($this->_user['id'] == 1);
    }

}

?>
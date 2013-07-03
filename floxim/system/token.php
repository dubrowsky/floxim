<?php

class fx_system_token extends fx_system {

    public function get_token() {
        $fx_core = fx_core::get_object();
        $user = $fx_core->env->get_user();
        if (!$user) {
            return false;
        }

        $secret = $fx_core->get_settings('secret_key');
        $token = md5($user['id'] . md5(substr($secret, 0, 10)));

        return $token;
    }
    
    public function check ( $token ) {
        $real_token = $this->get_token();
        return ( !$real_token || $real_token == $token);
    }

}

?>

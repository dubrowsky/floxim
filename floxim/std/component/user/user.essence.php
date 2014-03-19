<?php
class fx_content_user extends fx_content {

    static public function load() {
        $db = fx::db();
        $session_id = fx::input()->fetch_cookie('fx_sid');
        $session_time = time() + (fx::config()->AUTHTIME ? fx::config()->AUTHTIME : 24 * 3600);

        if ($session_id) {
            $user_result = $db->get_results("
                SELECT u.*, s.`login_save`,s.`session_time`
                FROM `{{session}}` AS `s` ,`{{content_user}}` AS `u`
                WHERE
                s.`id` = '".$db->escape($session_id)."'
                AND s.`user_id` = u.`id`
                AND s.`session_time` > ".time());
        }

        $user_id = 0;
        if ($user_result) {
            $data = $user_result[0];
            $user_id = $data['id'];
            $user = fx::data('content_user', $user_id);
            $user->create_session('attempt');
        } elseif ($session_id) {
            self::drop_session_cookie();
            $user = fx::data('content_user')->create();
        }
        fx::env()->set_user($user);
        return $user;
    }

    public function authorize($auth_type = 1) {
        $fx_sid = $this->create_session('authorize', 0, $auth_type);
        fx::env()->set_user($this);
        return $fx_sid;
    }

    public function unauthorize() {
        $session_id = fx::input()->fetch_cookie('fx_sid');
        fx::db()->query("DELETE FROM `{{session}}` WHERE `id` = '".$session_id."' OR `session_time` < '".time()."'");

        // unset back-end and front-end cookies
        self::drop_session_cookie();
    }

    public static function drop_session_cookie() {
        setcookie("fx_sid", NULL, NULL, "/", fx::config()->HTTP_HOST);
        if (preg_match("~www\.~", fx::config()->HTTP_HOST)) {
            setcookie("fx_sid", NULL, NULL, "/", str_replace("www.", "", fx::config()->HTTP_HOST));
        }
    }
    
    public function is_admin() {
        return (bool) $this['is_admin'];
    }

    public function create_session($auth_phase = 'authorize', $login_save = 0, $auth_type = 1) {
        $db = fx::db();
        $user_id = $this['id'];

        // save authorization to transfer a check post in the calling method )
        $LoginSave = ( ($login_save || isset($_POST['loginsave']) ) ? 1 : 0);

        // to authorize on subdomains
        // then make settings
        $cookies_with_subdomain = 1;
        
        $UserIP = sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
        $session_id = fx::input()->fetch_cookie('fx_sid');
        if (!$session_id) {
            $session_id = md5(rand(0, 1000).$user_id.$UserIP);
        }
        $session_id = $db->escape($session_id);

        $SessionStart = time();
        $SessionTime = $SessionStart + (fx::config()->AUTHTIME ? fx::config()->AUTHTIME : 24 * 3600);

        if ($auth_phase == 'authorize') {
            $db->query("DELETE FROM `{{session}}` WHERE `session_time` < '".$SessionStart."'".($session_id ? " OR `id` = '".$session_id."'" : ""));
            $db->query("INSERT INTO `{{session}}` (`id`, `user_id`, `session_start`, `session_time`, `ip`, `login_save`, `site_id`, `auth_type`)
            VALUES ('".$session_id."', '".$user_id."', '".$SessionStart."', '".$SessionTime."', '".$UserIP."', '".$LoginSave."', '".$site_ID."', '".intval($auth_type)."')");
        } else {
            $db->query("UPDATE `{{session}}` SET `session_time` = '".$SessionTime."', `ip` = '".$UserIP."' WHERE `id` = '".$session_id."'");
        }


        if (!$LoginSave) $SessionTime = 0;
        $cookie_domain = ($cookies_with_subdomain && strpos($_SERVER['HTTP_HOST'], '.') !== false ? str_replace("www.", "", $_SERVER['HTTP_HOST']) : NULL);
        setcookie('fx_sid', $session_id, $SessionTime, "/", $cookie_domain);
        return $session_id;
    }

    public function get_groups() {
        static $ug = false;
        
        if ($ug === false) {
            $res = fx::db()->get_results("SELECT ug.`user_id`, ug.`group_id`, g.`name`
                FROM `{{user_group}}` AS `ug`, `{{group}}` AS `g`
                WHERE `ug`.`group_id` = `g`.`id` ");
            if ($res) {
                foreach ($res as $v) {
                    $ug[$v['user_id']][$v['group_id']] = $v['name'];
                }
            }
        }

        return isset($ug[$this['id']]) ? $ug[$this['id']] : array();
    }

    public function set_groups($groups) {
        $this->_groups = is_array($groups) ? $groups : array($groups);
        return $this;
    }

    protected function _after_insert() {
        $user_id = $this['id'];

        if ($this->_groups) {
            fx::db()->query("DELETE FROM {{user_group}} WHERE `user_id` = '".$user_id."' ");
            if ($this->_groups) {
                foreach ($this->_groups as $group_id) {
                    $sql[] = "('".$user_id."','".intval($group_id)."')";
                }
                fx::db()->query("INSERT INTO {{user_group}} (`user_id`,`group_id`) VALUES ".join(',', $sql));
            }
        }
    }

    protected function _after_delete() {
        fx::db()->query("DELETE FROM {{user_group}} WHERE `user_id` = '".$this['id']."' ");
        return false;
    }

    protected function _before_save () {
        if ($this->is_modified('password')) {
            $this['password'] = crypt($this['password'],  uniqid(mt_rand(), true));
        }
        if ($this->is_modified('email') && is_object(fx::data('content_user')->where('email', $this['email'])->one())) {
            throw new Exception("Ununique email");
            
        }
    }
}
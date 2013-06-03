<?php

/* $Id: content_user.php 8576 2012-12-27 15:14:06Z myasin $ */
defined("FLOXIM") || die("Unable to load file.");

class fx_content_user extends fx_content {

    protected $_perm;
    protected $_groups;

    static public function fields() {
        $fx_core = fx_core::get_object();
        $user_component_id = $fx_core->get_settings('user_component_id', 'auth');
        return fx::data('field')->get_all('component_id', $user_component_id);
    }

    static public function attempt_to_authorize() {
        $fx_core = fx_core::get_object();
        $db = $fx_core->db;

        $session_id = $fx_core->input->fetch_cookie('fx_sid');

        $session_time = time() + (fx::config()->AUTHTIME ? fx::config()->AUTHTIME : 24 * 3600);

        if ($session_id) {
            $user_result = $db->get_results("
                SELECT u.*, ug.`id` AS `groups_id`,  s.`login_save`,s.`session_time`
                FROM `{{session}}` AS `s` ,`{{content_user}}` AS `u`, `{{user_group}}` as `ug`
                WHERE
                s.`id` = '".$db->escape($session_id)."'
                AND s.`user_id` = u.`id`
                AND u.`id` = ug.`user_id`
                AND s.`session_time` > ".time());
        }

        $user_id = 0;
        if ($user_result) {
            $data = $user_result[0];
            $user_id = $data['id'];
            $user = new fx_content_user(array('data' => $data, 'finder' => fx::data('content_user')));
            $user->create_session('attempt');

            $fx_core->env->set_user($user);
        } elseif ($session_id) {
        	self::drop_session_cookie();
            /*
             * $session_id = session_id();
             *  if ( $module_auth ) {
              $update_res = $db->query("UPDATE {{Session}} SET SessionTime = IF (SessionTime=".$SessionTime.", SessionTime+1,".$SessionTime.") WHERE Session_ID = '".$db->escape($session_id)."'");
              if (!$update_res) {
              $db->query("INSERT INTO {{Session}} (Session_ID, User_ID, SessionStart, SessionTime, UserIP, site_ID) VALUES ('".$db->escape($session_id)."', 0, ".time().", ".$SessionTime.", ".sprintf("%u", ip2long($_SERVER['REMOTE_ADDR'])).", ".($site+0).")");
              // чистим гостевые сессии
              if (!rand(0,50)) $db->query("DELETE FROM {{Session}} WHERE User_ID = 0 AND SessionTime < ".($SessionTime-300));
              }
              }
             */
        }

        return $user_id;
    }

    static public function get_auth_form () {
        $fx_core = fx_core::get_object();
        $user = $fx_core->env->get_user();
        if ( $user ) {
            return "Вы уже и так пользователь!";
        }
        else {
            return "[Здесь будет] Форма авторизации";
        }
    }

    public function authorize($auth_type = 1) {
        $fx_core = fx_core::get_object();
        $fx_sid = $this->create_session('authorize', 0, $auth_type);
        $fx_core->env->set_user($this);
        return $fx_sid;
    }

    public function unauthorize() {

        $fx_core = fx_core::get_object();

        $session_id = $fx_core->input->fetch_cookie('fx_sid');
        $fx_core->db->query("DELETE FROM `{{session}}` WHERE `id` = '".$session_id."' OR `session_time` < '".time()."'");

        // unset back-end and front-end cookies
        self::drop_session_cookie();
    }

    public static function drop_session_cookie() {
        setcookie("fx_sid", NULL, NULL, "/", fx::config()->HTTP_HOST);
        if (preg_match("~www\.~", fx::config()->HTTP_HOST)) {
			setcookie("fx_sid", NULL, NULL, "/", str_replace("www.", "", fx::config()->HTTP_HOST));
        }
    }

    public function perm() {
        if (!$this->_perm) {
            $this->_perm = new fx_permission($this);
        }
        return $this->_perm;
    }

    public function create_session($auth_phase = 'authorize', $login_save = 0, $auth_type = 1) {
        $fx_core = fx_core::get_object();
        $db = $fx_core->db;
        $user_id = $this['id'];

        // сохранять авторизацию ( перенести проверку поста в вызывающий метод )
        $LoginSave = ( ($login_save || isset($_POST['loginsave']) || fx::config()->AUTHTYPE == "always") ? 1 : 0);

        // авторизировать на поддоменах
        $cookies_with_subdomain = 0;
        if ($fx_core->modules->get_by_keyword('auth') && $fx_core->get_settings('with_subdomain', 'auth')) {
            $cookies_with_subdomain = 1;
        }

        $UserIP = sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
        $session_id = $fx_core->input->fetch_cookie('fx_sid');
        if (!$session_id) $session_id = md5(rand(0, 1000).$user_id.$UserIP);
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
        $fx_core = fx_core::get_object();

        if ($ug === false) {
            $res = $fx_core->db->get_results("SELECT ug.`user_id`, ug.`group_id`, g.`name`
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

    // переделать полностью
    public function get_file_path($field = 'avatar') {
        $fx_core = fx_core::get_object();
        $fid = $this[$field];
        $path = '';
        if ($fid) {
            $path = fx::config()->HTTP_FILES_PATH;
            $path .= $fx_core->db->get_var("SELECT path from {{filetable}} where id = '".$fid."' ");
        }
        else $path = fx::config()->HTTP_FILES_PATH.'system/no_avatar.gif';

        return $path;
    }

    public function set_groups($groups) {
        $this->_groups = is_array($groups) ? $groups : array($groups);
        return $this;
    }

    protected function _after_insert() {
        $fx_core = fx_core::get_object();
        $user_id = $this['id'];

        if ($this->_groups) {
            $fx_core->db->query("DELETE FROM {{user_group}} WHERE `user_id` = '".$user_id."' ");
            if ($this->_groups) {
                foreach ($this->_groups as $group_id) {
                    $sql[] = "('".$user_id."','".intval($group_id)."')";
                }
                $fx_core->db->query("INSERT INTO {{user_group}} (`user_id`,`group_id`) VALUES ".join(',', $sql));
            }
        }
    }

    protected function _after_delete() {
        $fx_core = fx_core::get_object();
        $fx_core->db->query("DELETE FROM {{user_group}} WHERE `user_id` = '".$this['id']."' ");

        return false;
    }

    protected function _before_save() {

    }

}
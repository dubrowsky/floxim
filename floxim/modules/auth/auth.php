<?php

class fx_auth {
    /** @var fx_auth_user_mail */
    public $mail;

    protected function __construct() {
        $this->mail = new fx_auth_user_mail();
    }

    /**
     * Instance self object method
     *
     * @return fx_auth object
     */
    public static function get_object() {
        static $storage;
        if (!isset($storage)) {
            $storage = new self();
        }
        return is_object($storage) ? $storage : false;
    }

    static public function get_register_infoblock() {
        $fx_core = fx_core::get_object();
        return fx::data('infoblock')->get('essence_id', $fx_core->get_settings('user_component_id', 'auth'), 'default_action', 'add');
    }

    public function recovery_password($login, $subdivision) {
        $fx_core = fx_core::get_object();

        $user = fx::data('user')->get("checked = 1 AND  (".fx::config()->AUTHORIZE_BY." = ? OR `email` = ?)", array($login, $login));
        if (!$user) {
            return false;
        }
        $user->set('registration_code', md5(rand().time().$user['id']))->save();

        $this->mail->recover_passwd($user, $subdivision);

        return true;
    }

    /**
     *  Подтверждение регистрации пользователя
     * @return @fx_user
     */
    public function registration_confirm($user_id, $code) {
        $fx_core = fx_core::get_object();

        $user = fx::data('user')->get('id', intval($user_id), 'registration_code', md5($code));
        if (!$user) {
            return false;
        }

        $user['registration_code'] = '';
        if (!$fx_core->get_settings('registration_premoderation', 'auth')) {
            $user['checked'] = 1;
        }

        $user->save();
        return $user;
    }

    /**
     * пользователи online
     *
     * @return array user_id
     */
    public function users_online() {

        static $result;

        if ($result) return $result;

        $fx_core = fx_core::get_object();
        $site = $fx_core->env->get_site()->get('id');

        $time_left = time() + fx::config()->AUTHTIME - $fx_core->get_settings('online_timeleft', 'auth');
        $query_where_cat = $fx_core->get_settings('bind_to_site', 'auth') ? " AND `site_id` IN(0,'".$site."')" : "";

        $result = (array) $fx_core->db->get_col("SELECT `user_id`
				FROM `{{session}}`
				WHERE `user_id` != 0 AND `session_time` > ".$time_left.$query_where_cat."
				GROUP BY `user_id`");
        return $result;
    }

    /**
     * проверка статуса пользователя
     *
     * @param int $user_id Идентификатор пользователя
     */
    public function is_online($user_id) {
        static $online;

        if (!$online) $online = $this->users_online();

        return is_array($online) && in_array($user_id, $online) ? true : false;
    }

    /**
     * возвращает URL списка пользователей
     */
    public function get_user_list_url() {

        $fx_core = fx_core::get_object();

        static $url;
        if (!$url) {
            $component_id = $fx_core->get_settings('user_component_id', 'auth');

            $ib = fx::data('infoblock')->get('essence_id', $component_id, 'type', 'content');
            if (!$ib) return null;

            $sub = fx::data('subdivision')->get('id', $ib['subdivision_id']);
            if (!$sub) return null;

            $url = fx::config()->SUB_FOLDER.$sub['hidden_url'].$ib['url'].'.html';
        }

        return $url;
    }

    /**
     * возвращает URL профиля пользователя (по умолчанию - текущего пользователя)
     * @param type $user_id
     */
    static public function get_profile_url($user_id = 0) {
        $fx_core = fx_core::get_object();

        static $pre_url = false;
        if ($pre_url === false) {
            $pre_url = array();
            $component_id = $fx_core->get_settings('user_component_id', 'auth');

            $ib = fx::data('infoblock')->get('essence_id', $component_id, 'type', 'content', 'default_action', 'index');
            if (!$ib) return "";

            $sub = fx::data('subdivision')->get('id', $ib['subdivision_id']);
            if (!$sub) return "";

            $pre_url = array('sub' => $sub['hidden_url'], 'ib' => $ib['url']);
        }

        $user_id = intval($user_id);
        if (!$user_id) {
            $user_id = $fx_core->env->get_user('id');
        }

        if (!$pre_url) {
            $url = "";
        } else {
            $url = fx::config()->SUB_FOLDER.$pre_url['sub'].$pre_url['ib'].'_'.intval($user_id).'.html';
        }

        return $url;
    }

    static public function get_recovery_link() {
        return '#';
    }

    /**
     * возвращает URL формы регистрации пользователя
     */
    static public function get_registration_url() {
        $fx_core = fx_core::get_object();

        static $url = false;

        if ($url !== false) {
            return $url;
        }

        $component_id = $fx_core->get_settings('user_component_id', 'auth');
        $ib = fx::data('infoblock')->get('essence_id', $component_id, 'type', 'content', 'default_action', 'add');
        if (!$ib) {
            $ib = fx::data('infoblock')->get('essence_id', $component_id, 'type', 'content');
            $need_add_prefix = true;
        }

        if (!$ib) {
            $url = '';
            return '';
        }

        $sub = fx::data('subdivision')->get('id', $ib['subdivision_id']);
        if (!$sub) {
            $url = '';
            return '';
        }

        $url = fx::config()->SUB_FOLDER.$sub['hidden_url'].($need_add_prefix ? 'add_' : '').$ib['url'].'.html';

        return $url;
    }

    /**
     * возвращает URL личных сообщений
     * по умолчанию - просмотр личных сообщений, если передан $user_id - форма для написания сообщения
     * @param type $user_id адресат сообщения
     */
    public function get_user_pm_url($user_id = 0) {

        $fx_core = fx_core::get_object();

        static $pre_url;
        if (!$pre_url) {

            $component_id = $fx_core->get_settings('pm_component_id', 'auth');

            $ib = fx::data('infoblock')->get('essence_id', $component_id, 'type', 'content');
            if (!$ib) return null;

            $sub = fx::data('subdivision')->get('id', $ib['subdivision_id']);
            if (!$sub) return null;

            $pre_url = array('sub' => $sub['hidden_url'], 'ib' => $ib['url']);
        }

        if (!$user_id) return $pre_url['sub'];
        else
                return fx::config()->SUB_FOLDER.$pre_url['sub'].'add_'.$pre_url['ib'].'.html?user='.intval($user_id);
    }

    public static function get_logout_url() {
        return fx::config()->SUB_FOLDER.fx::config()->HTTP_ROOT_PATH."index.php?essence=module_auth&action=logout";
    }

    /**
     * Количество новых сообщений для текущего или заданного пользователя
     *
     * @param int $user_id Идентификатор пользователя (optional)
     * @return int
     */
    static public function messages_new($user_id = 0) {
        $fx_core = fx_core::get_object();

        $user_id = intval($user_id);
        if (!$user_id) $user_id = $fx_core->env->get_user('id');
        if (!$user_id) return false;

        static $result;
        if ($result && $result[$user_id]) return $result[$user_id];

        $component_id = intval($fx_core->get_settings('pm_component_id', 'auth'));
        $component = fx::data('component')->get_by_id($component_id);
        $result[$user_id] = $fx_core->db->get_var("SELECT COUNT(`id`)
			FROM `{{content_".$component['keyword']."}}`
			WHERE `status`=1 AND `to_user`='".$user_id."'");
        return $result[$user_id];
    }

}


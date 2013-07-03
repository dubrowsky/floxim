<?php

class fx_auth_facebook extends fx_auth_external {

    protected $tmhOAuth;
    protected $here;

    static public function enabled() {
        $fx_core = fx_core::get_object();
        $settings = $fx_core->get_settings('', 'auth');

        $curl = $fx_core->php_ext('curl');

        return ($settings['facebook_enabled'] && $settings['facebook_app_id'] && $settings['facebook_app_key'] && $curl);
    }
    static public function get_auth_url() {
        return fx::config()->HTTP_ROOT_PATH . '?essence=module_auth&action=auth_by_facebook&redirect=' . $_SERVER['REQUEST_URI'];
    }
    static public function proccess() {
        if (self::enabled()) {
            $self = new self();
            $self->dispatcher();
        } else {
            die( fx::lang('Авторизация через facebook запрещена','system'));
        }
    }

    public function __construct() {
        parent::__construct();

        $this->type = 'facebook';
    }

    protected function start($authurl) {

        echo '<meta http-equiv="refresh" content="0; url=' . $authurl . '" />';
        echo '<p>' . fx::lang('Сейчас вы будете переброшены на страницу авторизации.','system') . ' <a href="' . $authurl . '">' . fx::lang('Нажмите, если не хотите ждать.','system') . '</a></p>';
    }

    public function dispatcher() {
        try {
            $facebook = new Facebook(array(
                        'appId' => $this->settings['facebook_app_id'],
                        'secret' => $this->settings['facebook_app_key'],
                    ));
            $user = $facebook->getUser();
            if (!$user) {
                $this->start($facebook->getLoginUrl());
            } else {
                $user_profile = $facebook->api('/me');
                $user_profile['avatar'] = 'http://graph.facebook.com/'.$user_profile['id'].'/picture';
                $this->proccess_response($user_profile);
                $this->redirect($_REQUEST['redirect']);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
?>
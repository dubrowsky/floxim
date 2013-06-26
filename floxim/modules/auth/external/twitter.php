<?php

class fx_auth_twitter extends fx_auth_external {

    protected $tmhOAuth;
    protected $here;

    static public function enabled () {
        $fx_core = fx_core::get_object();
        $settings = $fx_core->get_settings('', 'auth');

        $curl = $fx_core->php_ext('curl');

        return ($settings['twitter_enabled'] && $settings['twitter_app_id'] && $settings['twitter_app_key'] && $curl);
    }

    static public function get_auth_url() {
        return fx::config()->HTTP_ROOT_PATH.'?essence=module_auth&action=auth_by_twitter&redirect='.$_SERVER['REQUEST_URI'];;
    }

    public function __construct() {
        parent::__construct();

        $this->type = 'twitter';
        $this->tmhOAuth = new tmhOAuth(array(
                        'consumer_key' => $this->settings['twitter_app_id'],
                        'consumer_secret' => $this->settings['twitter_app_key']
                ));
        $this->here = tmhUtilities::php_self(false);
    }

    static public function proccess() {
        if ( self::enabled() ) {
             $self = new self();
            $self->dispatcher();
        }
        else {
            die(fx::lang('Авторизация через twitter запрещена','system'));
        }
    }

    protected function output_error() {
        echo 'Error: '.$this->tmhOAuth->response['response'].PHP_EOL;
        tmhUtilities::pr($this->tmhOAuth);
    }

    protected function wipe() {
        session_destroy();
        header("Location: {$this->here}");
        exit;
    }

    protected function make_request() {
        $this->tmhOAuth->config['user_token'] = $_SESSION['access_token']['oauth_token'];
        $this->tmhOAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];
        $code = $this->tmhOAuth->request('GET', $this->tmhOAuth->url('1/account/verify_credentials'));
        if ($code == 200) {
            $resp = json_decode($this->tmhOAuth->response['response'], 1);
            $this->proccess_response($resp);
            $this->redirect($_REQUEST['redirect']);
        } else {
            $this->output_error();
        }
    }

    protected function twitter_callback() {
        $this->tmhOAuth->config['user_token'] = $_SESSION['oauth']['oauth_token'];
        $this->tmhOAuth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];
        $code = $this->tmhOAuth->request('POST', $this->tmhOAuth->url('oauth/access_token', ''), array(
                'oauth_verifier' => $_REQUEST['oauth_verifier']
                ));
        if ($code == 200) {
            $_SESSION['access_token'] = $this->tmhOAuth->extract_params($this->tmhOAuth->response['response']);
            unset($_SESSION['oauth']);
            header("Location: {$this->here}");
        } else {
            $this->output_error();
        }
    }

    protected function start() {
        $params = array('oauth_callback' => $this->here);
        $code = $this->tmhOAuth->request('POST', $this->tmhOAuth->url('oauth/request_token', ''), $params);
        if ($code == 200) {
            $_SESSION['oauth'] = $this->tmhOAuth->extract_params($this->tmhOAuth->response['response']);
            $authurl = $this->tmhOAuth->url("oauth/authorize", '')."?oauth_token={$_SESSION['oauth']['oauth_token']}";
            echo '<meta http-equiv="refresh" content="0; url='.$authurl.'" />';
            echo '<p>' . fx::lang('Сейчас вы будете переброшены на страницу авторизации.','system') . ' <a href="'.$authurl.'">' . fx::lang('Нажмите, если не хотите ждать.','system') . '</a></p>';
        } else {
            $this->output_error();
        }
    }

    public function dispatcher() {
        if (isset($_REQUEST['wipe'])) {
            $this->wipe();
        } elseif (isset($_SESSION['access_token'])) {
            $this->make_request();
        } elseif (isset($_REQUEST['oauth_verifier'])) {
            $this->twitter_callback();
        } else {
            $this->start();
        }
    }

}
?>
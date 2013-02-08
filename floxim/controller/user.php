<?php

class fx_controller_user extends fx_controller_content {

    protected function _load_data($input) {
        $fx_core = fx_core::get_object();

        $component_id = $fx_core->get_settings('user_component_id', 'auth');
        $this->component = $fx_core->component->get_by_id($component_id);

        $this->infoblock = $fx_core->infoblock->get_by_id($input['fx_infoblock']);

        if ($this->infoblock['essence_id'] != $this->component['id']) {
            die("Не найден инфоблок");
        }

        $this->subdivision = $fx_core->subdivision->get_by_id($this->infoblock['subdivision_id']);
        $this->user = $fx_core->env->get_user();
        $this->fields = $this->component->fields();
        $this->tpl = $this->component->load_tpl_object($this->infoblock['list_ctpl_id']);
        $this->ajax = (bool) $input['fx_ajax'];
    }

    public function register_save($input) {
        $fx_core = fx_core::get_object();
        $result = array('status' => 'ok');

        if (!$fx_core->get_settings('allow_registration', 'auth')) {
            $result['status'] = 'error';
            $result['text'][] = 'Самостоятельная регистрация запрещена.';
            return $result;
        }


        $this->_load_data($input);
        if (!$this->infoblock) {
            die("Инфоблок не существует");
        }

        $this->content = $fx_core->user->create();
        $this->content['password'] = $password;
        $this->content['checked'] = 0;
        $this->content['site_id'] = $fx_core->env->get_site('id');
        $this->content['created'] = date("Y-m-d H:i:s");
        $this->content->set_groups(unserialize($fx_core->get_settings('external_user_groups', 'auth')));

        $result = $this->do_cond('add', $input);

        if ($result['status'] == 'ok') {

            $auth_by = fx::config()->AUTHORIZE_BY;
            if ($fx_core->user->get($auth_by, $input['f_'.$auth_by])) {
                $result['status'] = 'error';
                $result['text'][] = 'Такой логин уже используется';
                $result['fields'][] = 'f_'.$auth_by;
            }

            $password = $input['password'];
            if (!$password) {
                $result['status'] = 'error';
                $result['text'][] = 'Введите пароль.';
                $result['fields'][] = 'password';
            } elseif (($passwd_len = $fx_core->get_settings('min_pasword_length', 'auth')) && (strlen($password) < $passwd_len)) {
                $result['status'] = 'error';
                $result['text'][] = 'Пароль слишком короткий. Минимальная длина пароля '.$passwd_len.' символ(ов).';
                $result['fields'][] = 'f_password';
            } elseif ($password != $input['password1']) {
                $result['status'] = 'error';
                $result['text'][] = 'Пароль и подтверждение не совпадают.';
                $result['fields'][] = 'password1';
            }
        }

        return $this->_after_action('add', $result, $input);
    }

}


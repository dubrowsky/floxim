<?php

class fx_controller_admin_module_auth extends fx_controller_admin_module {

    protected $mail_templates = array('register_confirm' => 'Подтверждение регистрации',
            'passwd_recovery' => 'Восстановление пароля');

	// LANG ERROR	
	/*	
	protected $mail_templates = array('register_confirm' => fx_lang('Подтверждение регистрации'),
            'passwd_recovery' => fx_lang('Восстановление пароля'));
	*/

    public function settings() {
        $this->admin_tabs($this->get_tabs());
        $this->response->add_field($this->ui->hidden('tab', $active_tab));
    }

    protected function get_active_tab() {
        return $this->input['params'][0];
    }

    protected function get_tabs() {
        $tabs = array();
        $tabs['common'] = fx_lang('Общие');
        $tabs['components'] = fx_lang('Компоненты');
        $tabs['mail_templates'] = fx_lang('Шаблоны писем');
        $tabs['external'] = fx_lang('Авторизация через внешние сервисы');

        return $tabs;
    }

    public function settings_save($input) {
        $result = array();

        if (!is_callable(array($this, 'tab_'.$input['tab']."_save"))) {
            $result['status'] = 'error';
            $result['text'] = fx_lang('Несуществующая вкладка!');
        } else {
            $result = call_user_func(array($this, 'tab_'.$input['tab']."_save"), $input);
        }

        return $result;
    }

    public function restore_template($input) {
        $fx_core = fx_core::get_object();
        $tpl = $input['name'];
        if (!isset($this->templates[$tpl])) {
            return array('status' => 'error');
        }

        $value = $fx_core->files->readfile(fx::config()->HTTP_MODULE_PATH.'auth/templates/'.$tpl.'.tpl.php');

        if ($value === null) {
            return array('status' => 'error');
        }

        return array('status' => 'ok', 'text' => fx_lang('Восстановлено'), 'value' => $value);
    }

    public function restore_mail_template($input) {
        $tpl = $input['name'];

        if (!isset($this->mail_templates[$tpl])) {
            return array('status' => 'error');
        }

        include (fx::config()->MODULE_FOLDER."auth/templates/mail_templates.php");

        if (!isset(${$tpl.'_subject'}) || !isset(${$tpl.'_body'})) {
            return array('status' => 'error');
        }

        return array('status' => 'ok', 'text' => fx_lang('Восстановлено'), 'value' => array(${$tpl.'_subject'}, ${$tpl.'_body'}, ${$tpl.'_html'}));
    }

    public function tab_common() {
        $fx_core = fx_core::get_object();
        $settings = $fx_core->get_settings('', 'auth');

        foreach (fx::data('group')->get_all() as $v) {
            $groups[$v['id']] = $v['name'];
        }

        $fields[] = $this->ui->checkbox(
                'common', fx_lang('Общие настройки'), array('incorrect_login_form_disable' => fx_lang('Не показывать форму при неудачной попытке авторизации'),
                'deny_recoverpasswd' => fx_lang('Запретить самостоятельно восстанавливать пароль'),
                'bind_to_site' => fx_lang('Привязывать пользователей к сайтам')), array($settings['incorrect_login_form_disable'] ? 'incorrect_login_form_disable' : null,
                $settings['deny_recoverpasswd'] ? 'deny_recoverpasswd' : null,
                $settings['bind_to_site'] ? 'bind_to_site' : null)
        );
        $fields[] = $this->ui->input('online_timeleft', fx_lang('Время, в течение которого пользователь считается online (в секундах)'), $settings['online_timeleft']);


        $fields[] = $this->ui->label('<br><br>' . fx_lang('Регистрация'));
        $fields[] = $this->ui->checkbox('allow_registration', fx_lang('Разрешить самостоятельную регистрацию'), null, $settings['allow_registration']);
        $fields[] = $this->ui->input('min_pasword_length', fx_lang('Минимальная длина пароля (в символах)'), $settings['min_pasword_length']);
        $fields[] = $this->ui->checkbox('external_user_groups', fx_lang('Группы, куда попадёт пользователь после регистрации'), $groups, array(unserialize($settings['external_user_groups'])));
        $fields[] = $this->ui->label('<br>');/** @todo */
        $fields[] = $this->ui->checkbox('registration_confirm', fx_lang('Требовать подтверждение через e-mail'), null, $settings['registration_confirm']);
        $fields[] = $this->ui->checkbox('registration_premoderation', fx_lang('Премодерация администратором'), null, $settings['registration_premoderation']);
        $fields[] = $this->ui->checkbox('registration_notify_admin', fx_lang('Отправлять письмо администратору при регистрации пользователя'), null, $settings['registration_notify_admin']);
        $fields[] = $this->ui->input('admin_notify_email', fx_lang('E-mail администратора для отсылки оповещений'), $settings['admin_notify_email'] ? $settings['admin_notify_email'] : $fx_core->get_settings('spam_from_email', 'system'));
        $fields[] = $this->ui->checkbox('autoauthorize', fx_lang('Авторизация пользователя сразу после подтверждения'), null, $settings['autoauthorize']);


        $fields[] = $this->ui->checkbox(
                'pm', '<br><br>'.fx_lang('Личные сообщения'), array('pm_allow' => fx_lang('Разрешить отправлять личные сообщения'),
                'pm_notify' => fx_lang('Оповещать пользователя по e-mail о новом сообщении')), array($settings['pm_allow'] ? 'pm_allow' : null,
                $settings['pm_notify'] ? 'pm_notify' : null)
        );

        $fields[] = $this->ui->checkbox(
                'friends', '<br><br>' . fx_lang('Друзья и враги'), array('friend_allow' => fx_lang('Разрешить добавлять пользователей в друзья'),
                'banned_allow' => fx_lang('Разрешить добавлять пользователей во враги')), array($settings['friend_allow'] ? 'friend_allow' : null,
                $settings['banned_allow'] ? 'banned_allow' : null)
        );

        $this->response->add_fields($fields, 'common');
    }

    public function tab_components() {

        $fx_core = fx_core::get_object();
        $settings = $fx_core->get_settings('', 'auth');
        $fields = array();

        $components_obj = fx::data('component')->get_all();
        if ($components_obj) {
            foreach ($components_obj as $v) {
                $components[$v->get('id')] = $v->get('name');
            }

            $fields[] = array('type' => 'select', 'name' => 'user_component_id',
                    'values' => $components, 'label' => fx_lang('Компонент "Пользователи"'),
                    'value' => $settings['user_component_id']);
            $fields[] = array('type' => 'select', 'name' => 'pm_component_id',
                    'values' => $components, 'label' => fx_lang('Компонент "Личные сообщения"'),
                    'value' => $settings['pm_component_id']);
        }

        $this->response->add_fields($fields, 'components');
    }

    public function tab_mail_templates() {

        $fx_core = fx_core::get_object();

        foreach ($this->mail_templates as $k => $v) {

            $tpl = fx::data('mailtemplate')->get('keyword', 'auth_'.$k);

            $fields[] = $this->ui->label($v);
            $fields[] = $this->ui->ajaxlink(fx_lang('Восстановить форму по умолчанию'), array($k.'_subject', $k.'_body', $k.'_html'), array('essence' => 'module_auth',
                    'action' => 'restore_mail_template',
                    'name' => $k));
            $fields[] = $this->ui->input($k.'_subject', fx_lang('Заголовок письма'), $tpl['subject']);
            $fields[] = $this->ui->text($k.'_body', fx_lang('Тело письма'), $tpl['body']);
            $fields[] = $this->ui->checkbox($k.'_html', fx_lang('HTML-письмо'), null, $tpl['html']);
        }

        $this->response->add_fields($fields, 'mail_templates');
    }

    public function tab_common_save($input) {
        $fx_core = fx_core::get_object();
        $result = array('status' => 'ok');

        if (!$input['external_user_groups']) {
            $result['status'] = 'error';
            $result['text'] = fx_lang('Вы не выбрали ни одной группы для зарегистрированных пользователей.');
            $result['fields'][] = 'external_user_groups';
            return $result;
        }

        if (!fx::util()->validate_email($input['admin_notify_email'])) {
            $result['status'] = 'error';
            $result['text'] = fx_lang('Неверный формат адреса e-mail.');
            var_dump(fx::util()->validate_email($input['admin_notify_email']));
            die;
            $result['fields'][] = 'admin_notify_email';
            return $result;
        }

        if (!is_numeric($input['online_timeleft']) || !$input['online_timeleft']) {
            $result['status'] = 'error';
            $result['text'] = fx_lang('Время, в течение которого пользователь считается online, должно быть целым числом больше 0.');
            $result['fields'][] = 'online_timeleft';
            return $result;
        }

        if (!is_numeric($input['min_pasword_length'])) {
            $result['status'] = 'error';
            $result['text'] = fx_lang('Минимальная длина пароля должна быть целым числом.');
            $result['fields'][] = 'min_pasword_length';
            return $result;
        }


        // общие настройки
        $fx_core->set_settings('incorrect_login_form_disable', $input['common'] && in_array('incorrect_login_form_disable', $input['common']), 'auth');
        $fx_core->set_settings('deny_recoverpasswd', $input['common'] && in_array('deny_recoverpasswd', $input['common']), 'auth');
        $fx_core->set_settings('bind_to_site', $input['common'] && in_array('bind_to_site', $input['common']), 'auth');
        $fx_core->set_settings('online_timeleft', $input['online_timeleft'], 'auth');

        // Регистрация
        $fx_core->set_settings('allow_registration', $input['allow_registration'], 'auth');
        $fx_core->set_settings('min_pasword_length', $input['min_pasword_length'], 'auth');
        $fx_core->set_settings('external_user_groups', serialize($input['external_user_groups']), 'auth');
        $fx_core->set_settings('registration_confirm', $input['registration_confirm'], 'auth');
        $fx_core->set_settings('registration_premoderation', $input['registration_premoderation'], 'auth');
        $fx_core->set_settings('registration_notify_admin', $input['registration_notify_admin'], 'auth');
        $fx_core->set_settings('autoauthorize', $input['autoauthorize'], 'auth');
        $fx_core->set_settings('admin_notify_email', $input['admin_notify_email'], 'auth');

        // личные сообщения
        $fx_core->set_settings('pm_allow', $input['pm'] && in_array('pm_allow', $input['pm']), 'auth');
        $fx_core->set_settings('pm_notify', $input['pm'] && in_array('pm_notify', $input['pm']), 'auth');

        // друзья-враги
        $fx_core->set_settings('friend_allow', $input['friends'] && in_array('friend_allow', $input['friends']), 'auth');
        $fx_core->set_settings('banned_allow', $input['friends'] && in_array('banned_allow', $input['friends']), 'auth');

        return $result;
    }

    public function tab_components_save($input) {
        $fx_core = fx_core::get_object();

        $result = array('status' => 'ok');

        $settings = array('user_component_id', 'pm_component_id');

        foreach ($settings as $v) {
            $fx_core->set_settings($v, $input[$v], 'auth');
        }

        return $result;
    }

    public function tab_mail_templates_save($input) {
        $fx_core = fx_core::get_object();

        $result = array('status' => 'ok');

        foreach ($this->mail_templates as $k => $v) {
            if (isset($input[$k.'_body'])) {
                $tpl = fx::data('mailtemplate')->get('keyword', 'auth_'.$k);

                $tpl['subject'] = $input[$k.'_subject'];
                $tpl['body'] = $input[$k.'_body'];
                $tpl['html'] = $input[$k.'_html'];

                $tpl->save();
            }
        }

        return $result;
    }

    public function tab_external() {
        $fx_core = fx_core::get_object();

        $user_fields = array();
        foreach (fx_user::fields() as $field) {
            $user_fields[$field->get_name()] = $field->get_description();
        }

        $user_groups = array();
        foreach (fx::data('group')->get_all() as $group) {
            $user_groups[$group['id']] = $group['name'];
        }

        $settings = $fx_core->get_settings('', 'auth');

        $fields[] = $this->ui->checkbox('twitter_enabled', fx_lang('включить авторизацию через твиттер'), null, $settings['twitter_enabled']);
        $fields[] = $this->ui->input('twitter_app_id', 'Consumer key', $settings['twitter_app_id']);
        $fields[] = $this->ui->input('twitter_app_key', 'Consumer secret', $settings['twitter_app_key']);

        $twitter_map = unserialize($settings['twitter_map']);
        if (!is_array($twitter_map)) $twitter_map = array();
        $twitter_fields = array('id' => 'ID', 'name' => 'Name', 'screen_name' => 'Screen name', 'profile_image_url' => 'avatar');
        $fields[] = array('name' => 'twitter_map', 'label' => fx_lang('Соответсвие полей'), 'type' => 'set',
                'labels' => array(fx_lang('Данные twiiter'), fx_lang('Поля пользователя')),
                'tpl' => array(
                        array('name' => 'external_field', 'type' => 'select', 'values' => $twitter_fields),
                        array('name' => 'user_field', 'type' => 'select', 'values' => $user_fields)),
                'values' => $twitter_map
        );
        $fields[] = $this->ui->checkbox('twitter_group', fx_lang('Группы, куда попадет пользователь после авторизации '), $user_groups, unserialize($settings['twitter_group']));

        $fields[] = $this->ui->text('twitter_addaction', fx_lang('Действие после первой авторизации'), $settings['twitter_addaction']);

        $fields[] = $this->ui->checkbox('facebook_enabled', fx_lang('включить авторизацию через facebook'), null, $settings['facebook_enabled']);
        $fields[] = $this->ui->input('facebook_app_id', 'Consumer key', $settings['facebook_app_id']);
        $fields[] = $this->ui->input('facebook_app_key', 'Consumer secret', $settings['facebook_app_key']);

        $facebook_map = unserialize($settings['facebook_map']);
        if (!is_array($facebook_map)) $facebook_map = array();
        $facebook_fields = array('id' => 'ID', 'name' => 'Name', 'first_name' => 'first name', 'last_name' => 'last name', 'email' => 'email', 'avatar' => 'avatar');
        $fields[] = array('name' => 'facebook_map', 'label' => fx_lang('Соответсвие полей'), 'type' => 'set',
                'labels' => array(fx_lang('Данные facebook'), fx_lang('Поля пользователя')),
                'tpl' => array(
                        array('name' => 'external_field', 'type' => 'select', 'values' => $facebook_fields),
                        array('name' => 'user_field', 'type' => 'select', 'values' => $user_fields)),
                'values' => $facebook_map
        );
        $fields[] = $this->ui->checkbox('facebook_group', fx_lang('Группы, куда попадет пользователь после авторизации '), $user_groups, unserialize($settings['facebook_group']));

        $fields[] = $this->ui->text('facebook_addaction', fx_lang('Действие после первой авторизации'), $settings['facebook_addaction']);



        $this->response->add_fields($fields, 'external');
    }

    public function tab_external_save($input) {
        $fx_core = fx_core::get_object();

        $result = array('status' => 'ok');

        $settings = array('twitter_enabled', 'twitter_app_id', 'twitter_app_key', 'twitter_addaction', 'facebook_enabled', 'facebook_app_id', 'facebook_app_key', 'facebook_addaction');

        foreach ($settings as $v) {
            $fx_core->set_settings($v, $input[$v], 'auth');
        }

        $twitter_map = $input['twitter_map'];
        if (!is_array($twitter_map)) $twitter_map = array();
        $twitter_map = serialize($twitter_map);
        $fx_core->set_settings('twitter_map', $twitter_map, 'auth');

        $fx_core->set_settings('twitter_group', serialize($input['twitter_group']), 'auth');


        $facebook_map = $input['facebook_map'];
        if (!is_array($facebook_map)) $facebook_map = array();
        $facebook_map = serialize($facebook_map);
        $fx_core->set_settings('facebook_map', $facebook_map, 'auth');

        $fx_core->set_settings('facebook_group', serialize($input['facebook_group']), 'auth');



        return $result;
    }

}


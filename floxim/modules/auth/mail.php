<?php

defined("FLOXIM") || die("Unable to load file.");

class fx_auth_user_mail {

    public function confirm_user($user_id, $password) {
        $fx_core = fx_core::get_object();
        $user = $fx_core->user->get('id', $user_id);

        if (!$user) {
            return false;
        }

        $confirm_code = sha1(uniqid(time() - rand()));

        $mail_info = $this->get_confirm_user_mail($user, $password, $confirm_code);
        if (!$mail_info) return false;

        $user->set('registration_code', md5($confirm_code))->save();

        return $this->send($user, $mail_info);
    }

    public function recover_passwd(fx_user $user, $subdivision) {
        $fx_core = fx_core::get_object();

        if (!$user) {
            return false;
        }

        $mail_info = $this->get_recover_passwd_mail($user, $subdivision);
        return $this->send($user, $mail_info);
    }

    protected function send(fx_user $user, $mail_info) {
        $fx_core = fx_core::get_object();
        $fx_core->mail->set_body(strip_tags($mail_info['body']), $mail_info['html'] ? $mail_info['body'] : '');
        $res = $fx_core->mail->send($user['email'], $mail_info['subject']);

        return $res;
    }

    protected function get_confirm_user_mail($user, $password, $confirm_code) {

        $fx_core = fx_core::get_object();

        $confirm_link = "http://".fx::config()->HTTP_HOST.fx::config()->HTTP_ROOT_PATH."?essence=module_auth&action=confirm&confirm_user=".$user['id']."&ucc=".$confirm_code;

        $mail_template = $fx_core->mailtemplate->get('keyword', 'auth_register_confirm');

        $macro = array('SITE_NAME' => $fx_core->env->get_site()->get('name'),
                'SITE_URL' => fx::config()->HTTP_HOST,
                'USER_LOGIN' => $user[fx::config()->AUTHORIZE_BY],
                'USER_NAME' => $user['name'],
                'CONFIRM_LINK' => $confirm_link,
                'PASSWORD' => $password
        );

        $subject = $mail_template['subject'];
        $body = $mail_template['body'];
        foreach ($macro as $k => $v) {
            $subject = str_replace('%'.$k, $v, $subject);
            $body = str_replace('%'.$k, $v, $body);
        }

        return array('subject' => $subject, 'body' => $body, 'html' => $mail_template['html']);
    }

    protected function get_recover_passwd_mail(fx_user $user, $subdivision) {
        $fx_core = fx_core::get_object();

        $sub = $fx_core->subdivision->get_by_id($subdivision);
        if (!$sub) {
            return false;
        }

        $confirm_link = "http://".fx::config()->HTTP_HOST.fx::config()->SUB_FOLDER."/floxim/?essence=module_auth&action=recovery_password&uid=".$user['id']."&ucc=".$user['registration_code']."&subdivision_id=".$sub['id'];

        $mail_template = $fx_core->mailtemplate->get('keyword', 'auth_passwd_recovery');

        $macro = array(
                'SITE_NAME' => $fx_core->env->get_site()->get('name'),
                'SITE_URL' => fx::config()->HTTP_HOST,
                'USER_NAME' => $user['name'],
                'CONFIRM_LINK' => $confirm_link
        );

        $subject = $mail_template['subject'];
        $body = $mail_template['body'];
        foreach ($macro as $k => $v) {
            $subject = str_replace('%'.$k, $v, $subject);
            $body = str_replace('%'.$k, $v, $body);
        }

        return array('subject' => $subject, 'body' => $body, 'html' => $mail_template['html']);
    }

}


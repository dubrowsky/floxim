<?php

class fx_controller_admin_user extends fx_controller_admin {

    public function all() {
        $users = fx::data('content_user')->all();

        $result = array('type' => 'list', 'filter' => true, 'tpl' => 'imgh');
        $result['labels'] = array();
        $result['values'] = array();
        foreach ($users as $v) {
            $text .= $v['name'].', <a href="mailto:'.$v['email'].'">'.$v['email'].'</a>';

            $header_text = isset($v[fx::config()->AUTHORIZE_BY]) ? $v[fx::config()->AUTHORIZE_BY] : '#'.$v['id'];

            $r = array(
                'id' => $v['id'],
                'header' => array('name' => $header_text, 'url' => 'user.edit('.$v['id'].')'),
                'text' => $text,
                'name' => $v['login']
            );
            
            if (!$v['checked']) {
                $r['unchecked'] = 1;
            }
            $result['values'][] = $r;
        }

        $res['fields'][] = $result;
        $res['buttons'] = array('add', 'on', 'off', 'delete');
        $this->response->submenu->set_menu('user')->set_subactive('user');
        return $res;
    }

    /**
     * Регистрация нового пользователя В АДМИНКЕ
     * @param type $input
     * @return type
     */
    public function add_save($input) {
        return $this->_save($input);
    }

    public function edit_save($input) {
        $info = fx::data('content_user', $input['id']);
        return $this->_save($input, $info);
    }

    protected function _save($input, $info = null) {
        $result = array('status' => 'ok');
        $auth_by = 'login';
        $login = trim($input['login']);

        if (!$login) {
            $result['status'] = 'error';
            $result['text'][] = fx::lang('Fill in with the login','system');
            $result['fields'][] = 'login';
        }

        if ($info && (empty($input['password']) || empty($input['password2']))) {
        	unset($input['password']);
        }

        if (!$info) {
            if (!$input['password']) {
                $result['status'] = 'error';
                $result['text'][] = fx::lang('Password can\'t be empty','system');
                $result['fields'][] = 'password';
            }

			if ($result['status'] != 'error') {
				$info = fx::data('cotent_user')->create(
                    array(
                        'checked' => 1,
                        'created' => date("Y-m-d H:i:s")
                    )
                );
			}
        }

        $fields = fx_user::fields();
        foreach ($fields as $v) {
            $name = $v->get_name();
            if (isset($input['f_'.$name])) {
                if ($v->validate_value($input['f_'.$name])) {
                    $v->set_value($input['f_'.$name]);
                    $data[$name] = $v->get_savestring($info);
                } else {
                    $result['status'] = 'error';
                    $result['text'][] = $v->get_error();
                    $result['fields'][] = 'f_'.$name;
                }
            }
        }
        $data[$auth_by] = $login;

        if (isset($input['password'])) {
			if ($input['password'] && $input['password'] != $input['password2']) {
				$result['status'] = 'error';
				$result['text'][] = fx::lang('Passwords do not match','system');
				$result['fields'][] = 'password';
				$result['fields'][] = 'password2';
			} else {
				$data['password'] = $input['password'];
			}
		}

        if ($result['status'] == 'ok') {
            $info->set($data);
            $info->set_groups($input['group']);
            $info->save();

            foreach ($fields as $v) {
                $v->post_save($info);
            }
        }


        return $result;
    }

    public function edit($input) {
        $info = fx::data('content_user', $input['id']);
        $fields = $this->_form($info);
        $fields[] = $this->ui->hidden('action', 'edit');
        $fields[] = $this->ui->hidden('id', $info['id']);

        $result['fields'] = $fields;
        return $result;
    }

    protected function _form($info) {
        $groups = fx::data('group')->get_all();
        foreach ($groups as $v) {
            $values[$v['id']] = $v['name'];
        }
        $gr = null;
        if ($info['id']) {
            $gr = array_keys($info->get_groups());
            $fields []= $this->ui->hidden('id', $info['id']);
        }

        $fields[] = $this->ui->checkbox('group', fx::lang('Groups','system'), $values, $gr, 1);
        $fields[] = $this->ui->input('login', fx::lang('Login','system'), $info['login']);

        //if (!$info['id']) {
            $fields[] = $this->ui->password('password', fx::lang('Password','system'));
            $fields[] = $this->ui->password('password2', fx::lang('Confirm password','system'));
        //}
        // временно
        $fields[] = $this->ui->input('f_email', 'Email', $info['email']);
        $fields[] = $this->ui->input('f_name', fx::lang('Nick','system'), $info['name']);
        $fields[] = $this->ui->file('f_avatar', fx::lang('Avatar','system'));
        $fields[] = $this->ui->hidden('posting');

        return $fields;
    }

    public function delete_save($input) {
        fx::data('content_user', $input['id'])->delete();
        return array('status' => 'ok');
    }
}
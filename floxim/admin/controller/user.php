<?php

class fx_controller_admin_user extends fx_controller_admin {

    public function all() {
        $users = fx::data('content_user')->all();
        $result = array('type' => 'list', 'filter' => true, 'tpl' => 'imgh');
        $result['labels'] = array();
        $result['labels']  = array(
            'name' => fx::lang('Name','system'), 
            'email' => fx::lang('Email','system'),
        );
        $result['values'] = array();
        foreach ($users as $v) {
            $r = array(
                'id' => $v->get_id(),
                'essence' => '',
                'name' => array(
                    'name' => $v->get('name'),
                    'url' => '#admin.user.edit('.$v->get_id().')'
                ),
                'email' => $v->get('email')
            );
            $result['values'][] = $r;
        }
        $result['essence'] = 'user';
        $res['fields'][] = $result;
        $this->response->add_buttons(
            array(
                array(
                    'key' => 'add', 
                    'title' => fx::lang('Add new user', 'system'),
                    'url' => '#admin.user.add()'
                ),
                "delete"
            )
        );
        $this->response->submenu->set_menu('user');
        $this->response->breadcrumb->add_item(
            fx::lang('Users', 'system'),
            '#admin.user.all'
        );
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
        //$auth_by = 'login';
        $email = trim($input['f_email']);
        $name = trim($input['f_name']);
        if (!$email || !preg_match('~^(\S+)@([a-z0-9-]+)(\.)([a-z]{2,4})(\.?)([a-z]{0,4})+$~', $email)) {
            $result['status'] = 'error';
            $result['text'][] = fx::lang('Fill in correct email','system');
            $result['fields'][] = 'email';
        }
        if (!$name) {
            $result['status'] = 'error';
            $result['text'][] = fx::lang('Fill in name','system');
            $result['fields'][] = 'name';
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
				$info = fx::data('content_user')->create(
                   array(
                        'checked' => 1,
                        'created' => date("Y-m-d H:i:s")
                   )
                );
			}
        }
        foreach ($input as $name => $value) {
            if(preg_match('~^f_[\w]+~', $name) === 1){
                $data[preg_replace('~^f_~', '', $name)]=$value;
            }
        }
        //$fields = fx_content_user::fields();
        //foreach ($fields as $v) {
        //    $name = $v->get_name();
        //    if (isset($input['f_'.$name])) {
        //        if ($v->validate_value($input['f_'.$name])) {
        //            $v->set_value($input['f_'.$name]);
        //            $data[$name] = $v->get_savestring($info);
        //        } else {
        //            $result['status'] = 'error';
        //            $result['text'][] = $v->get_error();
        //            $result['fields'][] = 'f_'.$name;
        //        }
        //    }
        //}
        //$data[$auth_by] = $login;

        if (isset($input['password']) && isset($input['password2'])) {
			if (!$input['password'] || !$input['password2'] || $input['password'] != $input['password2']) {
				$result['status'] = 'error';
				$result['text'][] = fx::lang('Passwords do not match','system');
				$result['fields'][] = 'password';
				$result['fields'][] = 'password2';
			} else {
				$data['password'] = $input['password'];
			}
		}
        try {
            if ($result['status'] == 'ok') {
                $info->set($data);
                //$info->set_groups($input['group']);
                $info->save();

                //foreach ($fields as $v) {
                //    $v->post_save($info);
                //}
            }
        } catch (Exception $e) {
            $result['status'] = 'error';
            $result['text'][] = fx::lang($e->getMessage(),'system');
            $result['fields'][] = 'email';
        }


        return $result;
    }

    public function edit($input) {
        //var_dump($input);
        $info = fx::data('content_user', $input['params'][0]);
        $fields = $this->_form($info);
        $fields[] = $this->ui->hidden('action', 'edit');
        $fields[] = $this->ui->hidden('id', $info['id']);

        $result['fields'] = $fields;
        $this->response->add_form_button('save');
        $this->response->submenu->set_menu('user');
        $this->response->breadcrumb->add_item(
            fx::lang('Users', 'system'),
            '#admin.user.all'
        );
        $this->response->breadcrumb->add_item(
            fx::lang('Edit user', 'system'),
            '#admin.user.edit('.$input['params'][0].')'
        );
        return $result;
    }
    public function add() {
        //var_dump($input);
        //$info = fx::data('content_user', $input['params'][0]);
        $fields = $this->_form(null);
        $fields[] = $this->ui->hidden('action', 'add');
        //$fields[] = $this->ui->hidden('id', $info['id']);

        $result['fields'] = $fields;
        $this->response->add_form_button('save');
        $this->response->submenu->set_menu('user');
        $this->response->breadcrumb->add_item(
            fx::lang('Users', 'system'),
            '#admin.user.all'
        );
        $this->response->breadcrumb->add_item(
            fx::lang('Add user', 'system'),
            '#admin.user.add()'
        );
        return $result;
    }

    protected function _form($info) {
        //$groups = fx::data('group')->get_all();
        //foreach ($groups as $v) {
        //    $values[$v['id']] = $v['name'];
        //}
        //var_dump($values);
        //$gr = null;
        //if ($info['id']) {
        //    $gr = array_keys($info->get_groups());
        //    $fields []= $this->ui->hidden('id', $info['id']);
        //}

        //$fields[] = $this->ui->checkbox('group', fx::lang('Groups','system'), $values, $gr, 1);
        //$fields[] = $this->ui->input('login', fx::lang('Login','system'), $info['login']);

        //if (!$info['id']) {
        //}
        // временно
        $fields[] = $this->ui->input('f_email', fx::lang('Email','system'), $info['email']);
        $fields[] = $this->ui->input('f_name', fx::lang('Name','system'), $info['name']);
        $fields[] = $this->ui->password('password', fx::lang('Password','system'));
        $fields[] = $this->ui->password('password2', fx::lang('Confirm password','system'));
        $fields[] = array('type' => 'checkbox', 'name' => 'f_is_admin', 'label' => fx::lang('Admin','system'), 'value' => $info['is_admin']);

        //$fields[] = $this->ui->input('f_name', fx::lang('Nick','system'), $info['name']);
        //$fields[] = $this->ui->file('f_avatar', fx::lang('Avatar','system'));
        $fields[] = $this->ui->hidden('posting');
        $fields[] = $this->ui->hidden('essence', 'user');

        return $fields;
    }
    
    public function delete_save($input) {
        fx::data('content_user', $input['id'])->delete();
        return array('status' => 'ok');
    }
    
}
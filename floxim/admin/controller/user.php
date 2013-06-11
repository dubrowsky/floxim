<?php

class fx_controller_admin_user extends fx_controller_admin {

    /**
     * Регистрация нового пользователя ВО ФРОНТЕ
     */
    public function register_save($input) {
        $fx_core = fx_core::get_object();
        $result = array('status' => 'ok');
        $only_check = (bool) $input['fx_ajax_check'];

        if (!$fx_core->get_settings('allow_registration', 'auth')) {
            $result['status'] = 'error';
            $result['text'][] = 'Самостоятельная регистрация запрещена.';
            return $result;
        }

        $auth_by = fx::config()->AUTHORIZE_BY;
        $password = $input['password'];
        $user_component_id = $fx_core->get_settings('user_component_id', 'auth');
        $infoblock = fx::data('infoblock')->get_by_id($input['infoblock']);

        if ($infoblock['essence_id'] != $user_component_id) {
            $result['status'] = 'error';
            $result['text'][] = 'Ошибка: не найден инфоблок с пользователями.';
            return $result;
        }

        $component = fx::data('component')->get_by_id($user_component_id);
        $fields = $component->fields();

        $fx_user = fx::data('user')->create();
        $fx_user['password'] = $password;
        $fx_user['checked'] = 0;
        $fx_user['site_id'] = $fx_core->env->get_site('id');
        $fx_user['created'] = date("Y-m-d H:i:s");
        $fx_user->set_groups(unserialize($fx_core->get_settings('external_user_groups', 'auth')));

        // условие добавления объекта
        $tpl = $component->load_tpl_object();
        $tpl->set_vars('fx_content', $fx_user);
        $tpl->set_vars('fx_infoblock', $infoblock);
        $tpl->set_vars('fx_component', $component);
        $tpl->set_vars('fx_fields', $fields);
        $tpl->add_cond();

        $err = $tpl->get_error();
        if ($err) {
            $result['status'] = 'error';
            $result = array_merge($result, $err);
            return $result;
        }

        foreach ($fields as $v) {
            $name = $v->get_name();

            $value = isset($fx_user[$name]) ? $fx_user[$name] : (
                    isset($input['f_'.$name]) ? $input['f_'.$name] : null );
            if ($value !== null) {
                if ($v->validate_value($value)) {
                    $v->set_value($value);
                    if (!$only_check) {
                        $fx_user[$name] = $v->get_savestring($fx_user);
                    }
                } else {
                    $result['status'] = 'error';
                    $result['text'][] = $v->get_error();
                    $result['fields'][] = 'f_'.$name;
                }
            }
        }

        if (fx::data('user')->get($auth_by, $input['f_'.$auth_by] )) {
            $result['status'] = 'error';
            $result['text'][] = 'Такой логин уже используется';
            $result['fields'][] = 'f_'.$auth_by;
        }

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

        if ($only_check) return $result;

        if ($result['status'] == 'ok') {
            $fx_user->save();

            foreach ($fields as $v) {
                $v->post_save($fx_user);
            }
        }

        if ($input['fx_admin']) {
            return $result;
        }

        ob_start();

        if ($result['status'] == 'error') {
            $tpl->begin_add_form();
            $tpl->add_form();
            $tpl->end_add_form();
        } else {
            $tpl->after_add();
        }

        $content = ob_get_clean();

        $page = new fx_controller_page();
        $page->set_main_content($content);
        $page->index();
    }

    public function all($input) {
        $fx_core = fx_core::get_object();


        $users = fx::data('user')->get_all();


        $result = array('type' => 'list', 'filter' => true, 'tpl' => 'imgh');
        $result['labels'] = array(); //array('name' => FX_ADMIN_NAME);


        $result['values'] = array();
        foreach ($users as $v) {
            // группы пользователя
            $groups = array();
            foreach ($v->get_groups() as $group_id => $group_name) {
                $groups[] = '<a href="#'.$group_id.'">'.$group_name.'</a>';
            }


            $text = join(', ', $groups)."<br/>";
            $text .= $v['name'].', <a href="mailto:'.$v['email'].'">'.$v['email'].'</a>';
            $text .= '<br/><a href="#admin.rights.all(user-'.$v['id'].')">Управление правами</a>';

            $header_text = isset($v[fx::config()->AUTHORIZE_BY]) ? $v[fx::config()->AUTHORIZE_BY] : '#'.$v['id'];

            $r = array(
                    'id' => $v['id'],
                    'header' => array('name' => $header_text, 'url' => 'user.full('.$v['id'].')'),
                    'text' => $text,
                    'name' => $v['login']);
            if (!$v['checked']) $r['unchecked'] = 1;
            $result['values'][] = $r;
        }

        $res['fields'][] = $result;
        $res['buttons'] = array('add', 'on', 'off', 'delete');
        $res['buttons_action'] = array('rights' => array('location' => 'user.rights.all(user-%id%)'));
        $this->response->submenu->set_menu('user')->set_subactive('user');
        return $res;
    }

    public function full($input) {
        $fx_core = fx_core::get_object();
        $user = fx::data('user')->get_by_id($input['params'][0]);

        $areas = array(
                'edit_form' => array('name' => 'Регистрационные данные', 'class' => 'fx_admin_user_edit_form'),
                'info' => array('name' => 'Активность', 'class' => 'fx_admin_user_info')
        );

        // регистрационные данные
        foreach ($this->_form($user) as $field) {
            $field['area'] = 'edit_form';
            $fields[] = $field;
        }

        // активность
        $fields[] = array('label' => 'зарегистрирован: '.$user['created'], 'type' => 'label');

        $fields[] = array('label' => 'друзья, отправить сообщение<br/>', 'type' => 'label');
        $fields[] = array('label' => 'надо подумать, может ли какой-нибудь модуль, кроме ЛК писать сюда что-нибудь<br/>', 'type' => 'label');

        $content_items = $this->get_used_components($user['id']);
        if ($content_items) {
            $fields[] = array('label' => '<br/>опубликовал: ', 'type' => 'label');
            foreach ($content_items as $content) {
                $fields[] = array('label' => '<a href="#admin.user.content('.$user['id'].','.$content['component_id'].')">'.$content['name'].' ('.$content['count'].')</a><br/>', 'type' => 'label');
            }
        }

        $result['fields'] = $fields;
        $result['form_button'] = array('save');
        $result['areas'] = $areas;

        $this->response->submenu->set_menu('user-'.$user['id'])->set_subactive('profile');

        $this->response->breadcrumb->add_item('Пользователи');
        $this->response->breadcrumb->add_item($user['name']);


        return $result;
    }

    public function full_save($input) {
    	$this->edit_save($input);
    }

    protected function get_used_components($user_id) {
        $fx_core = fx_core::get_object();
        $user_id = intval($user_id);

        $result = array();
        $components = fx::data('component')->get_all();
        foreach ($components as $component) {
            if ($component->is_user_component()) {
                continue;
            }
            $table = $component->get_content_table();
            $count = $fx_core->db->get_var("SELECT COUNT(`id`) FROM `{{".$table."}}` WHERE `user_id` = '".$user_id."' ");
            if ($count) {
                $result[] = array('component_id' => $component['id'], 'name' => $component['name'], 'count' => $count);
            }
        }

        return $result;
    }

    public function content($input) {
        $fx_core = fx_core::get_object();
        $user = fx::data('user')->get_by_id($input['params'][0]);
        $component = fx::data('component')->get_by_id($input['params'][1]);

        $values = fx_infoblock_content::objects_list('component'.$component['id'], 'output=array&ctpl=select&by_user_id='.$user['id']);
        $fields[] = array('name' => 'sort_objects', 'label' => '<h2>Выберите объекты</h2>', 'type' => 'itemselect', 'values' => $values, 'multiple' => 1);


        $result['fields'] = $fields;

        $result['buttons'] = array('edit', 'on', 'off', 'delete');
        $result['breadcrumbs'][] = array('name' => $user['name'], 'url' => '#admin.user.full('.$user['id'].')');
        $result['breadcrumbs'][] = array('name' => 'публикации в "'.$component['name'].'"');
        return $result;
    }

    public function add($input) {
        $fields = $this->_form(array());
        $fields[] = $this->ui->hidden('action', 'add');

        $result = array('fields' => $fields);
        $result['dialog_title'] = 'Добавление пользователя';

        return $result;
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
        $fx_core = fx_core::get_object();
        $info = fx::data('user')->get_by_id($input['id']);
        return $this->_save($input, $info);
    }

    protected function _save($input, $info = null) {
        $fx_core = fx_core::get_object();
        $result = array('status' => 'ok');
        $auth_by = 'login';
        $login = trim($input['login']);

        if (empty($input['group'])) {
            $result['status'] = 'error';
            $result['text'][] = 'Выберите хотя бы одну группу';
            $result['fields'][] = 'group';
        }

        if (!$login) {
            $result['status'] = 'error';
            $result['text'][] = 'Заполните поле с логином';
            $result['fields'][] = 'login';
        }

        if ($info && (empty($input['password']) || empty($input['password2']))) {
        	unset($input['password']);
        }

        if (!$info) {
            if (!$input['password']) {
                $result['status'] = 'error';
                $result['text'][] = 'Пароль не может быть пустым';
                $result['fields'][] = 'password';
            }

            $data_create['checked'] = 1;
			$data_create['created'] = date("Y-m-d H:i:s");
			if ($result['status'] != 'error') {
				$info = fx::data('user')->create($date_create);
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
				$result['text'][] = 'Пароли не совпадают';
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
        $fx_core = fx_core::get_object();
        $info = fx::data('user')->get_by_id($input['id']);
        $fields = $this->_form($info);
        $fields[] = $this->ui->hidden('action', 'edit');
        $fields[] = $this->ui->hidden('id', $info['id']);

        $result['fields'] = $fields;
        return $result;
    }

    protected function _form($info) {
        $fx_core = fx_core::get_object();
        $groups = fx::data('group')->get_all();
        foreach ($groups as $v) {
            $values[$v['id']] = $v['name'];
        }

        $gr = null;
        if ($info['id']) {
            $gr = array_keys($info->get_groups());
            $fields []= $this->ui->hidden('id', $info['id']);
        }

        $fields[] = $this->ui->checkbox('group', 'Группы', $values, $gr, 1);

        $fields[] = $this->ui->input('login', 'Логин', $info['login']);

        //if (!$info['id']) {
            $fields[] = $this->ui->password('password', 'Пароль');
            $fields[] = $this->ui->password('password2', 'Пароль еще раз');
        //}
        // временно
        $fields[] = $this->ui->input('f_email', 'Email', $info['email']);
        $fields[] = $this->ui->input('f_name', 'Имя на сайте', $info['name']);
        $fields[] = $this->ui->file('f_avatar', 'Аватар');

        $fields[] = $this->ui->hidden('posting');

        return $fields;
    }

    public function delete_save($input) {
        fx_core::get_object()->user->get_by_id($input['id'])->delete();
        return array('status' => 'ok');
    }

}

?>

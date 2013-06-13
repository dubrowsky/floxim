<?php

class fx_controller_admin_group extends fx_controller_admin {

    public function all($input) {
        $fx_core = fx_core::get_object();

        $items = $fx_core->group->get_all();
        
        $ar = array('type' => 'list', 'filter' => true, 'tab' => 'fields');
        $ar['labels'] = array('name' => FX_ADMIN_NAME);

        foreach ($items as $item) {
            $name = array('name' => $item['name'], 'url' => 'user.all(group-' . $item['id'] . ')');
            $el = array('id' => $item['id'], 'name' => $name);
            $ar['values'][] = $el;
        }

        $fields[] = $ar;

        $buttons = array("add", "edit", "rights");
        $result = array('fields' => $fields, 'select_node' => 'group', 'buttons' => $buttons);
        $result['buttons_action'] = array('rights' => array('location' => 'user.rights.all(group-%id%)'));

        $this->response->submenu->set_menu('user')->set_subactive('group');
        return   $result;
    }

    public function add($input) {
        $fx_core = fx_core::get_object();
        $info['name'] = fx_lang('Новая группа');

        $fields = $this->_form($info);
        $fields[] = $this->ui->hidden('action', 'add');

        $result = array('fields' => $fields);
        $result['dialog_title'] = fx_lang('Добавление группы пользователей');
        
        return $result;
    }

    public function edit($input) {
        $fx_core = fx_core::get_object();
        $info = $fx_core->group->get_by_id($input['id']);

        $fields = $this->_form($info);
        $fields[] = $this->ui->hidden('action', 'edit');
        $fields[] = $this->ui->hidden('id', $info['id']);

        $result = array('fields' => $fields);
        $result['dialog_title'] = fx_lang('Изменение группы пользователей') . ' "'.$info['name'].'"';
        
        return $result;
    }

    public function add_save($input) {
        return $this->_save($input);
    }

    public function edit_save($input) {
        $fx_core = fx_core::get_object();
        $info = $fx_core->group->get_by_id($input['id']);
        return $this->_save($input, $info);
    }



    public function delete_save($input) {
        fx_core::get_object()->group->get_by_id($input['id'])->delete();
        return array('status' => 'ok');
    }
    
    protected function _form($info) {
        $fields[] = $this->ui->input('name', fx_lang('Название'), $info['name']);
        $fields[] = $this->ui->hidden('posting');

        return $fields;
    }

    protected function _save($input, $info = null) {
        $fx_core = fx_core::get_object();

        $result = array('status' => 'ok');
        $name = trim($input['name']);

        if (!$name) {
            $result['status'] = 'error';
            $result['text'][] = fx_lang('Укажите название');
            $result['fields'][] = 'name';
        }


        if ($result['status'] == 'ok') {
            if (!$info) {
                $info = $fx_core->group->create();
            }
            $info['name'] = $name;
            $info->save();
        }


        return $result;
    }

}



?>
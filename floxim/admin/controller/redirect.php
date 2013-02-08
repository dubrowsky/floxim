<?php

class fx_controller_admin_redirect extends fx_controller_admin {
    
    public function all($input) {
        $items = fx::data('redirect')->get_all();

        $ar = array('type' => 'list', 'filter' => true, 'sortable' => true);
        $ar['labels'] = array('old' => 'Старый url', 'new' => 'Новый url', 'header' => 'Заголовок');

        foreach ($items as $item) {
            $el = array('id' => $item['id'], 'old' => $item['old_url'], 'new' => $item['new_url'], 'header' => $item['header']);
            if (!$item['checked']) $el['unchecked'] = 1;
            $ar['values'][] = $el;
        }

        $fields[] = $ar;

        $buttons = array("add", "edit", "on", "off", "delete");
        $result = array('fields' => $fields, 'buttons' => $buttons);

        $this->response->submenu->set_menu('tools')->set_subactive('redirect');
        return $result;
    }

    public function add($input) {
        $fields = $this->_form($info);
        $fields[] = $this->ui->hidden('action', 'add');

        $result = array('fields' => $fields);
        $result['dialog_title'] = 'Добавление правила переадресации';

        return $result;
    }

    public function edit($input) {
        $info = fx::data('redirect')->get_by_id($input['id']);

        $fields = $this->_form($info);
        $fields[] = $this->ui->hidden('action', 'edit');
        $fields[] = $this->ui->hidden('id', $info['id']);

        $result = array('fields' => $fields);
        $result['dialog_title'] = 'Изменение правила переадресации';

        return $result;
    }

    public function add_save($input) {
        return $this->_save($input);
    }

    public function edit_save($input) {
        $info = fx::data('redirect')->get_by_id($input['id']);
        return $this->_save($input, $info);
    }

    protected function _form($info) {
        $hs = array('301' => '301 Moved Permanently', '302' => '302 Found');
        $fields[] = $this->ui->input('old_url', 'Старый url', $info['old_url']);
        $fields[] = $this->ui->input('new_url', 'Новый url', $info['new_url']);
        $fields[] = $this->ui->select('header', 'Посылаемый заголовок', $hs);
        $fields[] = $this->ui->hidden('posting');

        return $fields;
    }

    protected function _save($input, fx_redirect $redirect = null) {
        $data['old_url'] = str_replace(array('http://', 'https://', 'www.'), '', trim($input['old_url']));
        $data['new_url'] = str_replace(array('http://', 'https://', 'www.'), '', trim($input['new_url']));
        $data['header'] = in_array($input['header'], array(301, 302)) ? $input['header'] : 301;

        if ($redirect) {
            $redirect->set($data);
        } else {
            $data['checked'] = 1;
            $redirect = fx::data('redirect')->create($data);
        }

        if (!$redirect->validate()) {
            $result['status'] = 'error';
            $result['errors'] = $redirect->get_validate_error();
            return $result;
        }

        $redirect->save();
        $result = array('status' => 'ok');
        return $result;
    }

   

}

?>
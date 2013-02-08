<?php

class fx_controller_admin_crontask extends fx_controller_admin {
    
    public function all($input) {
        $fx_core = fx_core::get_object();

        $items = $fx_core->crontask->get_all();

        $ar = array('type' => 'list', 'filter' => true, 'sortable' => true);
        $ar['labels'] = array('name' => 'Название', 'path' => 'Путь до скрипта', 'every_days' => 'Дни', 'every_hours' => 'Часы', 'every_minutes' => 'Минуты', 'last_launch' => 'Последний запуск');

        foreach ($items as $item) {
            $el = array('id' => $item['id'], 'name' => $item['name'], 'path' => $item['path'], 'every_days' => $item['every_days'], 'every_hours' => $item['every_hours'], 'every_minutes' => $item['every_minutes'], 'last_launch' => $item['last_launch'] ? date("d.m.Y H:i:s", $item['last_launch']) : 'нет'   );
            if (!$item['checked']) $el['unchecked'] = 1;
            $ar['values'][] = $el;
        }

        $fields[] = $ar;

        $buttons = array("add", "edit", "on", "off", "delete");
        $result = array('fields' => $fields, 'buttons' => $buttons);

        $this->response->submenu->set_menu('tools')->set_subactive('crontask');
        return $result;
    }

    public function add($input) {
        $fields = $this->_form($info);
        $fields[] = $this->ui->hidden('action', 'add');

        $result = array('fields' => $fields);
        $result['dialog_title'] = 'Добавление новой задачи';

        return $result;
    }

    public function edit($input) {
        $fx_core = fx_core::get_object();
        $info = $fx_core->crontask->get_by_id($input['id']);

        $fields = $this->_form($info);
        $fields[] = $this->ui->hidden('action', 'edit');
        $fields[] = $this->ui->hidden('id', $info['id']);

        $result = array('fields' => $fields);
        $result['dialog_title'] = 'Изменение задачи';

        return $result;
    }

    public function add_save($input) {
        return $this->_save($input);
    }

    public function edit_save($input) {
        $fx_core = fx_core::get_object();
        $info = $fx_core->crontask->get_by_id($input['id']);
        return $this->_save($input, $info);
    }

    protected function _form($info) {
        $email_send_types = array('0' => 'не отправлять', '1' => 'при наличии результата', '2' => 'отправлять всегда');
        
        $fields[] = $this->ui->input('name', 'Название', $info['name']);
        $fields[] = $this->ui->input('path', 'Путь до файла или url', $info['path']);
        $fields[] = $this->ui->input('every_days', 'Дни', $info['every_days']);
        $fields[] = $this->ui->input('every_hours', 'Часы', $info['every_hours']);
        $fields[] = $this->ui->input('every_minutes', 'Минуты', $info['every_minutes']);
        $fields[] = $this->ui->select('send_email_type', 'Отправлять отчет по email', $email_send_types, intval($info['send_email_type']));
        $fields[] = array('type' => 'input', 'name' => 'email', 'label' => 'Email', 'current' => 'x@x.ru', 'value' => $info['email']);
        $fields[] = $this->ui->hidden('posting');

        return $fields;
    }

    protected function _save($input, fx_crontask $crontask = null) {
        $fx_core = fx_core::get_object();

        $data = array();
        $data['name'] = trim($input['name']);
        $data['path'] = trim($input['path']);
        $data['email'] = trim($input['email']);
        $data['every_days'] = intval($input['every_days']);
        $data['every_hours'] = intval($input['every_hours']);
        $data['every_minutes'] = intval($input['every_minutes']);
        $data['send_email_type'] = intval($input['send_email_type']);

        if ($crontask) {
            $crontask->set($data);
        } else {
            $data['checked'] = 1;
            $crontask = $fx_core->crontask->create($data);
        }

        if (!$crontask->validate()) {
            $result['status'] = 'error';
            $result['errors'] = $crontask->get_validate_error();
            return $result;
        }

        $crontask->save();
        $result = array('status' => 'ok');
        return $result;
    }
    
    static public function run () {
        $fx_core = fx_core::get_object();
        
        $tasks = $fx_core->crontask->get_actual();
        foreach ( $tasks as $task ) {
            $task->run();
        }
    }

   

}

?>
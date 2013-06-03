<?php

class fx_controller_admin_classificator extends fx_controller_admin {

    public function all($input) {
        $fx_core = fx_core::get_object();

        $items = $fx_core->classificator->get_all();


        $ar = array('type' => 'list', 'filter' => true, 'tab' => 'fields');
        $ar['labels'] = array('name' => FX_ADMIN_NAME, 'table' => 'Таблица');

        foreach ($items as $item) {
            $name = array('name' => $item['name'], 'url' => 'classificator.items(' . $item['id'] . ')');
            $el = array('id' => $item['id'], 'name' => $name, 'table' => $item['table']);
            if (!$item['checked'])
                $el['unchecked'] = 1;
            $ar['values'][] = $el;
        }

        $fields[] = $ar;

        $buttons = array("add", "edit", "on", "off", "delete");
        $result = array('fields' => $fields, 'buttons' => $buttons);
        $this->response->submenu->set_menu('administrate')->set_subactive('classificator');
        return $result;
    }

    public function add($input) {
        $info['name'] = 'Новый список';
        $info['table'] = 'newclassificator';
        $info['sort_type'] = 'priority';
        $info['sort_direction'] = 'asc';

        $fields = $this->_form($info);
        $fields[] = $this->ui->hidden('action', 'add');

        $result['fields'] = $fields;
        $result['dialog_title'] = 'Добавление нового списка';
        return $result;
    }

    public function edit($input) {
        $fx_core = fx_core::get_object();
        $info = $fx_core->classificator->get_by_id($input['id']);

        $fields = $this->_form($info);
        $fields[] = $this->ui->hidden('action', 'edit');
        $fields[] = $this->ui->hidden('id', $info['id']);

        $result['fields'] = $fields;
        $result['dialog_title'] = 'Редактирование списка "' . $info['name'] . '"';
        return $result;
    }

    public function add_save($input) {
        return $this->_save($input);
    }

    public function edit_save($input) {
        $fx_core = fx_core::get_object();
        $info = $fx_core->classificator->get_by_id($input['id']);
        return $this->_save($input, $info);
    }

    public function items($input) {
        $fx_core = fx_core::get_object();

        $cl = $fx_core->classificator->get_by_id($input['params'][0]);
        $items = $cl->elements();

        $ar = array('type' => 'list', 'filter' => true, 'tab' => 'fields');
        $ar['labels'] = array('name' => FX_ADMIN_NAME);

        foreach ($items as $item) {
            $el = array('id' => $item['id'], 'name' => $item['name']);
            if (!$item['checked'])
                $el['unchecked'] = 1;
            $ar['values'][] = $el;
        }

        $fields[] = $ar;

        //$buttons = array("add", "edit", "on", "off", "delete");
        $buttons = array("add", "edit");

        $buttons_action['add']['options']['classificator_id'] = $cl['id'];
        $buttons_action['add']['options']['action'] = 'item_add';

        $buttons_action['edit']['options']['classificator_id'] = $cl['id'];
        $buttons_action['edit']['options']['action'] = 'item_edit';

        $result = array('fields' => $fields, 'select_node' => 'classificator-' . $cl['id'], 'buttons' => $buttons, 'buttons_action' => $buttons_action);
        
        $this->response->submenu->set_menu('classificator-'.$cl['id']);
        
        $this->response->breadcrumb->add_item('Списки');
        $this->response->breadcrumb->add_item($cl['name']);
        
        return $result;
    }

    protected function _form($info) {
        $fields[] = $this->ui->input('name', 'Название', $info['name']);
        $fields[] = $this->ui->input('table', 'Название таблицы', $info['table']);
        $fields[] = $this->ui->select('sort_type', 'Сортировать по', array('priority' => 'приоритету', 'id' => 'id', 'name' => 'имени'), $info['sort_type']);
        $fields[] = $this->ui->select('sort_direction', 'Порядок сортировки', array('asc' => 'по возрастанию', 'desc' => 'по убыванию'), $info['sort_direction']);

        $fields[] = $this->ui->hidden('action', 'add');
        $fields[] = $this->ui->hidden('posting');

        return $fields;
    }

    protected function _save($input, $info = null) {
        $fx_core = fx_core::get_object();

        $result = array('status' => 'ok');

        // все классификаторы
        $tables = array();
        foreach ($fx_core->classificator->get_all() as $v) {
            if ($info && $info['id'] == $v['id'])
                continue;
            $tables[] = $v['table'];
        }

        $name = trim($input['name']);
        $table = trim($input['table']);

        if (!$name) {
            $result['status'] = 'error';
            $result['text'][] = 'Укажите название';
            $result['fields'][] = 'name';
        }
        if (!$table) {
            $result['status'] = 'error';
            $result['text'][] = 'Укажите название таблицы';
            $result['fields'][] = 'table';
        }
        if ($table && !preg_match("/^([a-z][a-z0-9_]*)$/i", $table)) {
            $result['status'] = 'error';
            $result['text'][] = 'Название таблицы может содержать только латинские буквы, цифры и знак подчеркивания.';
            $result['fields'][] = 'table';
        }
        if ($table && in_array($table, $tables)) {
            $result['status'] = 'error';
            $result['text'][] = 'Такая таблица уже есть.';
            $result['fields'][] = 'table';
        }

        if ($result['status'] == 'ok') {
            if (!$info) {
                $info = $fx_core->classificator->create(array('checked' => 1));
            }
            $info['name'] = $name;
            $info['table'] = $table;
            $info['sort_type'] = $input['sort_type'];
            $info['sort_direction'] = $input['sort_direction'];

            $info->save();
        }


        return $result;
    }

    public function item_add($input) {
        $info['classificator_id'] = $input['classificator_id'];
        $fields = $this->_form_item($info);
        $fields[] = $this->ui->hidden('action', 'item_add');

        $result = array('fields' => $fields);

        return $result;
    }

    protected function _form_item($info) {
        $fields[] = $this->ui->input('name', 'Название', $info['name']);

        $fields[] = $this->ui->hidden('classificator_id', $info['classificator_id']);
        $fields[] = $this->ui->hidden('posting');

        return $fields;
    }

    public function item_add_save($input) {
        $fx_core = fx_core::get_object();

        $cl = $fx_core->classificator->get_by_id($input['classificator_id']);
        $item = $cl->get_item_finder()->create();

        $item['name'] = trim($input['name']);

        $item->save();

        return array('status' => 'ok');
    }

    public function item_edit($input) {
        $fx_core = fx_core::get_object();

        $cl = $fx_core->classificator->get_by_id($input['classificator_id']);
        $item = $cl->get_item_finder()->get_by_id($input['id']);

        $info['name'] = $item['name'];
        $info['classificator_id'] = $cl['id'];

        $fields = $this->_form_item($info);
        $fields[] = $this->ui->hidden('action', 'item_edit');
        $fields[] = $this->ui->hidden('id', $input['id']);

        $result = array('fields' => $fields);

        return $result;
    }

    public function item_edit_save($input) {
        $fx_core = fx_core::get_object();
        $cl = $fx_core->classificator->get_by_id($input['classificator_id']);
        $item = $cl->get_item_finder()->get_by_id($input['id']);

        $item['name'] = trim($input['name']);
        $item->save();

        return array('status' => 'ok');
    }

}

?>

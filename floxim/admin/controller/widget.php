<?php

class fx_controller_admin_widget extends fx_controller_admin_component {

    public function add($input) {
        $fields = array();

        $widgets = fx::data('widget')->get_all();

        $groups = array();
        foreach ($widgets as $v) {
            $groups[$v['group']] = $v['group'];
        }

        switch ($input['source']) {
            default:
                $input['source'] = 'new';
                $fields[] = $this->ui->hidden('action', 'add');
                $fields[] = array('label' => fx::lang('Название','system'), 'name' => 'name');
                $fields[] = array('label' => fx::lang('Ключевое слово','system'), 'name' => 'keyword');
                $fields[] = array('label' => fx::lang('Группа','system'), 'type' => 'select', 'values' => $groups, 'name' => 'group', 'extendable' => fx::lang('Другая группа','system'));
        }

        $fields[] = $this->ui->hidden('source', $input['source']);
        $fields[] = $this->ui->hidden('posting');

        return array('fields' => $fields);
    }

    public function store($input) {
        return $this->ui->store('widget', $input['filter'], $input['reason'], $input['position']);
    }

    public function add_save($input) {
        $fx_core = fx_core::get_object();
        $result = array('status' => 'ok');

        $data['name'] = trim($input['name']);
        $data['keyword'] = trim($input['keyword']);

        $data['group'] = $input['group'];
        if (($data['group'] == 'fx_new') && $input['fx_new_group']) {
            $data['group'] = $input['fx_new_group'];
        }

        $widget = fx::data('widget')->create($data);

        if (!$widget->validate()) {
            $result['status'] = 'error';
            $result['errors'] = $widget->get_validate_error();
            return $result;
        }

        try {
            $content_php = $this->_get_tpl($widget['keyword']);
            $fx_core->files->writefile(fx::config()->HTTP_WIDGET_PATH.$data['keyword'].'/main.tpl.php', $content_php);
            $widget->save();
        } catch (Exception $e) {
            $result['status'] = 'error';
            $result['text'][] = $e->getMessage();
            if ($widget['id']) {
                $widget->delete();
            }
        }

        return $result;
    }

     public function store_save($input) {
        $store = new fx_admin_store();
        $file = $store->get_file($input['store_id']);

        $result = array('status' => 'ok');
        try {
            $imex = new fx_import();
            $imex->import_by_content($file);
        } catch (Exception $e) {
            $result = array('status' => 'error');
            $result['text'][] = $e->getMessage();
        }

        return $result;
    }

    public function edit_save($input) {
        $fx_core = fx_core::get_object();
        $widget = fx::data('widget')->get_by_id($input['id']);

        $result['status'] = 'ok';

        // сохранение настроек
        if ($input['phase'] == 'settings') {
            $params = array('name', 'group', 'description', 'embed');

            if (!trim($input['name'])) {
                $result['status'] = 'error';
                $result['text'][] = fx::lang('Введите название виджета','system');
                $result['fields'][] = 'name';
            }

            if ($result['status'] == 'ok') {
                foreach ($params as $v) {
                    $widget->set($v, trim($input[$v]));
                }

                $widget->save();
            }
        }

        return $result;
    }

    public function export($input) {
        $widget = fx::data('widget')->get_by_id($input['id']);

        $fx_export = new fx_export();
        $fx_export->export_essence($widget);
    }

    public function import_save($input) {
        $file = $input['importfile'];
        if (!$file) {
            $result = array('status' => 'error');
            $result['text'][] = fx::lang('Ошибка при создании временного файла','system');
        }

        $result = array('status' => 'ok');
        try {
            $imex = new fx_import();
            $imex->import_by_file($file['tmp_name']);
        } catch (Exception $e) {
            $result = array('status' => 'ok');
            $result['text'][] = $e->getMessage();
        }

        return $result;
    }

    public function settings($widget) {

        $groups = fx::data('widget')->get_all_groups();

        $fields[] = array('label' => fx::lang('Ключевое слово:','system') . ' '.$widget['keyword'], 'type' => 'label');

        $fields[] = array('label' => fx::lang('Название','system'), 'name' => 'name', 'value' => $widget['name']);
        $fields[] = array('label' => fx::lang('Группа','system'), 'type' => 'select', 'values' => $groups, 'name' => 'group', 'value' => $widget['group'], 'extendable' => fx::lang('Другая группа','system'));

        $fields[] = array('label' => fx::lang('Описание','system'), 'name' => 'description', 'value' => $widget['description'], 'type' => 'text');

        $fields[] = array('type' => 'hidden', 'name' => 'phase', 'value' => 'settings');
        $fields[] = array('type' => 'hidden', 'name' => 'id', 'value' => $widget['id']);
        return array('fields' => $fields, 'form_button' => array('save'));
    }

    protected function get_functions() {
        $function = array();
        $function[] = array('name' => 'record', 'type' => 'html');
        $function[] = array('name' => 'settings', 'type' => 'php');
        return $function;
    }

}
?>
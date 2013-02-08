<?php

class fx_controller_admin_ctpl extends fx_controller_admin {

    public function add($input) {
        $fields[] = $this->ui->input('name', 'Название шаблона');
        $fields[] = $this->ui->input('keyword', 'Ключевое слово');
        $fields[] = $this->ui->hidden('component_id', $input['component_id']);
        $fields[] = $this->ui->hidden('action', 'add');
        $fields[] = $this->ui->hidden('posting');

        $result = array('fields' => $fields);
        return $result;
    }

    public function edit($input) {
        $fx_core = fx_core::get_object();

        $ctpl = fx::data('ctpl')->get_by_id($input['params'][0]);
        $component = $ctpl->get_component();
        
        $field_to_edit = false;
        if (isset($input['params'][1]) && $input['params'][1] == 'edit_field') {
        	// объект-поле, который будем редактировать
        	// загружаем, чтоб сунуть название в хлебные крошки (см. ниже)
			$field_to_edit = fx::data('field')->get_by_id( $input['params'][2] );
        	$field_controller = new fx_controller_admin_field();
        	$result = $field_controller->edit(array('id' => $field_to_edit['id']));
        	$result['form_button'] = array('save');
        } else {
			$tabs = $this->get_tabs();
			$active_tab = $this->get_active_tab();
			$result = $this->ui->admin_tabs($tabs, $active_tab, $this, $ctpl);
        }

        $this->response->submenu->set_menu('component-'.$component['id'])->set_subactive('ctpl-'.$ctpl['id']);

        fx_controller_admin_component::make_breadcrumb($component, 'ctpl', $this->response->breadcrumb);
        $this->response->breadcrumb->add_item('Шаблон "'.$ctpl['name'].'"', '#admin.ctpl.edit('.$ctpl['id'].',list)');
        // OMG!
        if ($field_to_edit) {
        	$this->response->breadcrumb->add_item('Визуальные настройки', '#admin.ctpl.edit('.$ctpl['id'].',visual)');
        	$this->response->breadcrumb->add_item($field_to_edit['name']);
        }
        return $result;
    }

    protected function get_tabs() {
        $tabs['list'] = array('name' => 'Список');
        $tabs['full'] = array('name' => 'Полный вывод');
        $tabs['add'] = array('name' => 'Добавление');
        $tabs['edit'] = array('name' => 'Изменение');
        $tabs['visual'] = array('name' => 'Визуальные настройки');
        $tabs['settings'] = array('name' => 'Настройки');

        return $tabs;
    }

    public function add_save($input) {
        $fx_core = fx_core::get_object();
        $result = array('status' => 'ok');
        
        $ctpl_data = array('component_id' => $input['component_id'],
                'keyword' => trim($input['keyword']), 'name' => trim($input['name']));
        $ctpl = fx::data('ctpl')->create($ctpl_data);
        
        if (!$ctpl->validate()) {
            $result['status'] = 'error';
            $result['errors'] = $ctpl->get_validate_error();
            return $result;
        }
        
        try {
            $ctpl->create_file();
            $ctpl->save();
        } catch (Exception $e) {
            $result['status'] = 'error';
            $result['text'][] = $e->getMessage();
        }

        return $result;
    }

    public function edit_save($input) {
        $result = array('status' => 'ok');

        $fx_core = fx_core::get_object();
        $ctpl = fx::data('ctpl')->get_by_id($input['id']);

        if ($input['step']) {
            return call_user_func(array($this, '_tab_'.$input['step'].'_save'), $input);
        }

        $filepath = $ctpl->get_path();
        $php_content = $fx_core->files->readfile($filepath);
        $functions = $this->get_functions($input['tab']);

        try {
            $parser = new fx_admin_parser($php_content);
            $content = $parser->replace_parts($functions, $input);
            $fx_core->files->writefile($filepath, $content);
        } catch (Exception $e) {
            $result['status'] = 'error';
            $result['text'][] = $e->getMessage();
        }

        return $result;
    }

    public function tab_list($ctpl) {
        return $this->show_part($ctpl, 'list');
    }

    public function tab_full($ctpl) {
        return $this->show_part($ctpl, 'full');
    }

    public function tab_add($ctpl) {
        return $this->show_part($ctpl, 'add');
    }

    public function tab_edit($ctpl) {
        return $this->show_part($ctpl, 'edit');
    }

    protected function show_part($ctpl, $tab) {
        $fx_core = fx_core::get_object();
        $functions = $this->get_functions($tab);
        
        try {
            $php_content = $fx_core->files->readfile($ctpl->get_path());
            $parser = new fx_admin_parser($php_content);
            $parts = $parser->get_parts($functions);
        }
        catch ( Exception $e ) {
            $fields[] = $this->ui->error("Невозможно открыть файл ".$ctpl->get_path());
        }
        
        foreach ($functions as $function) {
            $name = $function['name'];
            $fields[] = array('type' => 'text', 'label' => $name, 'name' => $name, 'value' => $parts[$name], 'code' => $function['type']);
        }

        $fields[] = $this->ui->hidden('id', $ctpl['id']);
        $fields[] = $this->ui->hidden('tab', $tab);

        return array('essence' => 'ctpl', 'fields' => $fields, 'form_button' => array('save'));
    }

    protected function get_functions($tab) {
        $function = array();
        switch ($tab) {
            case 'list' :
                $function[] = array('name' => 'prefix', 'type' => 'html');
                $function[] = array('name' => 'record', 'type' => 'html');
                $function[] = array('name' => 'suffix', 'type' => 'html');
                $function[] = array('name' => 'settings_index', 'type' => 'php');
                break;
            case 'full' :
                $function[] = array('name' => 'full', 'type' => 'html');
                $function[] = array('name' => 'title', 'type' => 'html');
                $function[] = array('name' => 'h1', 'type' => 'html');
                $function[] = array('name' => 'settings_full', 'type' => 'php');
                break;
            case 'add': case 'edit':
                $function[] = array('name' => 'begin_'.$tab.'_form', 'type' => 'html');
                $function[] = array('name' => $tab.'_form', 'type' => 'html');
                $function[] = array('name' => 'end_'.$tab.'_form', 'type' => 'html');
                $function[] = array('name' => $tab.'_cond', 'type' => 'php');
                $function[] = array('name' => 'after_'.$tab, 'type' => 'html');
                break;
        }

        return $function;
    }

    public function tab_visual($ctpl) {
        $controller = new fx_controller_admin_field();
        return $controller->items($ctpl);
    }

    public function tab_settings(fx_ctpl $ctpl) {
        $fields[] = $this->ui->input('name', 'Название', $ctpl['name']);
        $fields[] = array('type' => 'checkbox', 'name' => 'with_list', 'label' => 'Со списком объектов', 'value' => $ctpl['with_list']);
        $fields[] = array('type' => 'checkbox', 'name' => 'with_full', 'label' => 'С полным выводом', 'value' => $ctpl['with_full']);

        $fields[] = array('name' => 'rec_num', 'label' => 'Количество записей', 'value' => $ctpl['rec_num']);

        $actions = array('index', 'add', 'search');
        $fields[] = array('type' => 'label', 'label' => 'Доступные действия');
        foreach ($actions as $action) {
            $values[$action] = constant('FX_ACTION_'.strtoupper($action));
            $value = !is_array($ctpl['action']['enabled']) || in_array($action, $ctpl['action']['enabled']);
            $fields[] = array('name' => 'action_enabled['.$action.']', 'label' => $values[$action], 'type' => 'checkbox', 'value' => $value);
        }

        $value = $ctpl['action']['default'] ? $ctpl['action']['default'] : 'index';
        $fields[] = array('name' => 'action_default', 'label' => 'Действие по умолчанию', 'type' => 'select', 'values' => $values, 'value' => $value);

        $fields[] = array('type' => 'hidden', 'name' => 'id', 'value' => $ctpl['id']);
        $fields[] = array('type' => 'hidden', 'name' => 'step', 'value' => 'settings');

        // сортировка
        $values = array();
        foreach (array('manual', 'field', 'last', 'random') as $v) {
            $values[$v] = constant('FX_ADMIN_SORT_'.strtoupper($v));
        }
        $value = $ctpl['sort']['type'] ? $ctpl['sort']['type'] : 'manual';
        $fields[] = array('name' => 'sort[type]', 'label' => 'Сортировка', 'type' => 'select', 'values' => $values, 'value' => $value);
        $fields[] = array('name' => 'sort[unchangeable]', 'label' => 'Запретить изменение сортировки в инфоблоке', 'type' => 'checkbox', 'value' => $ctpl['unchangeable']);

        $embed = array('miniblock' => 'Миниблок', 'narrow' => 'Узкий', 'wide' => 'Широкий', 'narrow-wide' => 'Узко-широкий');
        $fields[] = $this->ui->radio('embed', 'Размер шаблона', $embed, $ctpl['embed']);
        $fields[] = $this->ui->checkbox('widget', 'Виджет', null, $ctpl['widget']);
        $fields[] = $this->ui->checkbox('notwidget', 'Невиджет', null, $ctpl['notwidget']);
        
        $access = $ctpl->get_access();
        
        $user_types= fx_rights::get_user_types();
        $rights_types = fx_rights::get_rights_types();
        
        $user_types_values = array();
        foreach ( $user_types as $type ) {
            $user_types_values[$type] = fx_rights::get_label($type);
        }
        foreach ( $rights_types as $right ) {
            $fields[] = $this->ui->radio( 'access['.$right.']', fx_rights::get_label($right), $user_types_values, $access[$right] );
        }
        
        return array('essence' => 'ctpl', 'fields' => $fields, 'form_button' => array('save'));
    }

    public function _tab_settings_save($input) {
        $fx_core = fx_core::get_object();
        $ctpl = fx::data('ctpl')->get_by_id($input['id']);

        $params = array('widget', 'notwidget', 'name');
        foreach ($params as $param) {
            $ctpl[$param] = $input[$param];
        }

        $ctpl['rec_num'] = $input['rec_num'];
        $ctpl['embed'] = $input['embed'];
        $ctpl['with_list'] = $input['with_list'];
        $ctpl['with_full'] = $input['with_full'];
        $ctpl['sort'] = $input['sort'];
        $ctpl['access'] = $input['access'];

        $action['default'] = $input['action_default'] ? $input['action_default'] : 'index';
        if ($input['action_enabled']['index']) $action['enabled'][] = 'index';
        if ($input['action_enabled']['add']) $action['enabled'][] = 'add';
        if ($input['action_enabled']['search']) $action['enabled'][] = 'search';
        if (!$action['enabled'])
                $action['enabled'] = array('index', 'add', 'search');
        $ctpl['action'] = $action;

        $ctpl->save();

        $result = array('status' => 'ok');
        return $result;
    }

}

?>

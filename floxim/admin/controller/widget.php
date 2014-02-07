<?php

class fx_controller_admin_widget extends fx_controller_admin_component {
    
    public function all() {
        $field = array('type' => 'list', 'filter' => true);
        $field['labels'] = array(
            'name' => fx::alang('Name', 'system'),
            'buttons' => array('type' => 'buttons')
        );
        $field['values'] = array();
        $field['essence'] = 'widget';
        $widgets = fx::data('widget')->all();
        foreach ($widgets as $widget) {
            $submenu = fx_controller_admin_component::get_component_submenu($widget);
            $submenu_first = current($submenu);
            $r = array(
                'id' => $widget['id'],
                'name' => array(
                    'name' => $widget['name'],
                    'url' => $submenu_first['url']
                )
            );

            $r['buttons'] = array();
            foreach ($submenu as $submenu_item) {
                //if (!$submenu_item['parent']) {
                    $r['buttons'] []= array(
                        'type' => 'button', 
                        'label' => $submenu_item['title'], 
                        'url' => $submenu_item['url']
                    );
                //}
            }

            $field['values'][] = $r;
        }
        $this->response->add_buttons(array(
            array(
                'key' => "add", 
                'title' => fx::alang('Add new widget', 'system'),
                'url' => '#admin.widget.add'
            ),
            "delete"
        ));
        
        $result = array('fields' => array($field));

        $this->response->breadcrumb->add_item(self::_essence_types('widget'), '#admin.widget.all');
        $this->response->submenu->set_menu('widget');
        return $result;
    }

    public function add($input) {
        $fields = array();

        $widgets = fx::data('widget')->all();

        $groups = array();
        foreach ($widgets as $v) {
            $groups[$v['group']] = $v['group'];
        }

        switch ($input['source']) {
            default:
                $input['source'] = 'new';
                $fields[] = $this->ui->hidden('action', 'add');
                $fields[] = array('label' => fx::alang('Name','system'), 'name' => 'name');
                $fields[] = array('label' => fx::alang('Keyword','system'), 'name' => 'keyword');
                $fields[] = array('label' => fx::alang('Group','system'), 'type' => 'select', 'values' => $groups, 'name' => 'group', 'extendable' => fx::alang('Another group','system'));
        }

        $fields[] = $this->ui->hidden('source', $input['source']);
        $fields[] = $this->ui->hidden('posting');
        
        $this->response->breadcrumb->add_item(
            self::_essence_types('widget'), 
            '#admin.widget.all'
        );
        $this->response->breadcrumb->add_item(
            fx::alang('Add new widget', 'system')
        );
        
        $this->response->submenu->set_menu('widget');

        return array('fields' => $fields);
    }

    public function add_save($input) {
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
            fx::files()->writefile(fx::config()->HTTP_WIDGET_PATH.$data['keyword'].'/main.tpl.php', $content_php);
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

    public function edit_save($input) {
        $widget = fx::data('widget')->get_by_id($input['id']);
        $result['status'] = 'ok';
        // сохранение настроек
        if ($input['phase'] == 'settings') {
            $params = array('name', 'group', 'description', 'embed');
            if (!trim($input['name'])) {
                $result['status'] = 'error';
                $result['text'][] = fx::alang('Enter the widget name','system');
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

    public function settings($widget) {

        $groups = fx::data('widget')->get_all_groups();

        $fields[] = array('label' => fx::alang('Keyword:','system') . ' '.$widget['keyword'], 'type' => 'label');

        $fields[] = array('label' => fx::alang('Name','system'), 'name' => 'name', 'value' => $widget['name']);
        $fields[] = array('label' => fx::alang('Group','system'), 'type' => 'select', 'values' => $groups, 'name' => 'group', 'value' => $widget['group'], 'extendable' => fx::alang('Another group','system'));

        $fields[] = array('label' => fx::alang('Description','system'), 'name' => 'description', 'value' => $widget['description'], 'type' => 'text');

        $fields[] = array('type' => 'hidden', 'name' => 'phase', 'value' => 'settings');
        $fields[] = array('type' => 'hidden', 'name' => 'id', 'value' => $widget['id']);
        
        $this->response->submenu->set_subactive('settings');
        $fields[] = $this->ui->hidden('essence', 'widget');
        $fields[] = $this->ui->hidden('action', 'edit_save');
        
        return array('fields' => $fields, 'form_button' => array('save'));
    }
}
?>
<?php

class fx_controller_admin_menu_item extends fx_controller_admin {

    public function settings($input) {
        $fx_core = fx_core::get_object();
        $menu = $fx_core->menu->get_by_id($input['menu_id']);
        if ($menu) {
            $id = intval($input['id']);
            $item = $menu['settings']['items'][$id];

            $fields[] = $this->ui->input('name', 'Name', $item['name']);
            $fields[] = $this->ui->input('url', 'URL', $item['url']);

            $fields[] = $this->ui->hidden('action', 'settings');
            $fields[] = $this->ui->hidden('essence', 'menu_item');
            $fields[] = $this->ui->hidden('menu_id', $menu['id']);
            $fields[] = $this->ui->hidden('id', $id);
            $fields[] = $this->ui->hidden('posting');
        } else {
            $fields[] = $this->ui->error('Меню не найдено');
        }

        return array('fields' => $fields);
    }

    public function settings_save($input) {
        $fx_core = fx_core::get_object();
        $menu = $fx_core->menu->get_by_id($input['menu_id']);

        if ($menu) {
            $settings = $menu['settings'];
            $id = intval($input['id']);
            $settings['items'][$id] = array('name' => $input['name'], 'url' => $input['url']);

            $menu->set('settings', $settings)->save();
        }

        return array('status' => 'ok');
    }

    public function delete_save($input) {
        $fx_core = fx_core::get_object();
        $menu = $fx_core->menu->get_by_id($input['menu_id']);

        if ($menu) {
            $settings = $menu['settings'];
            $id = intval($input['id']);
            unset($settings['items'][$id]);
            $menu->set('settings', $settings)->save();
        }

        return array('status' => 'ok');
    }

}

?>

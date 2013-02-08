<?php

class fx_controller_admin_patch extends fx_controller_admin {

    public function all() {
        $fx_core = fx_core::get_object();
        $success = fx_admin_checkpatch::check(true);

        $response = unserialize($fx_core->get_settings('last_response'));
        $next_patch = $fx_core->get_settings('next_patch');

        if ($success) {
            $fields[] = $this->ui->label("Последняя проверка осуществлялась: ".date("d.m.Y H:i:s", $fx_core->get_settings('last_check')));

            if ($next_patch) {
                $fields[] = $this->ui->label("Не установлено обновление ".$next_patch);

                if ($response['next_patch_fulllink']) {
                    $fields[] = $this->ui->label("<a href='".$response['next_patch_fulllink']."' target='_blank'>подробнее об обновлении</a>");
                }
                if ($response['next_patch_fulllink']) {
                    $fields[] = $this->ui->label("<a href='".$response['next_patch_link']."' >скачать</a>");
                }
                $show_button = true;
            } else {
                $fields[] = $this->ui->label("У вас установлены все обновления системы");
            }
        } else {
            $fields[] = $this->ui->error("Не удалось подключиться к серверу обновлений. Проверьте наличие новых обновлений на сайте.");
        }

        if ($show_button) {
            $fields[] = $this->ui->button_func('Установить с сайта', 'fx_admin_install_patch');
        }
        $fields[] = $this->ui->file('file', 'File:');
        $fields[] = $this->ui->hidden('action', 'install');

        $patches = fx_data::optional('patch')->get_all();
        if ($patches) {
            $table = array('type' => 'table');
            $table['labels'] = array('to', 'created', 'description');

            foreach ($patches as $patch) {
                $table['values'][] = array($patch['to'], $patch['created'], $patch['description']);
            }
            $fields[] = $table;
        }

        $result['fields'] = $fields;
        $result['form_button'] = array('save');

        $this->response->submenu->set_menu('administrate')->set_subactive('patch');
        return $result;
    }

    public function install_save($input) {
        $patch = new fx_admin_patch();
        $result = array('status' => 'ok');
        try {
            if ($input['from_site']) {
                $check = new fx_admin_checkpatch();
                $content = $check->get_file();
                $patch->install_by_content($content);
            } else {
                $patch->install_by_file($file['tmp_name']);
            }
        } catch (Exception $e) {
            $result = array('status' => 'error');
            $result['text'] = $e->getMessage();
        }

        return $result;
    }

}

?>

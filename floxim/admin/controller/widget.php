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
            case 'import':
                $fields[] = array('name' => 'importfile', 'type' => 'file', 'label' => 'Файл');
                $fields[] = $this->ui->hidden('action', 'import');
                break;
            case 'store':
                $fields[] = $this->ui->store('widget');
                break;
            default:
                $input['source'] = 'new';
                $fields[] = $this->ui->hidden('action', 'add');
                $fields[] = array('label' => 'Название', 'name' => 'name');
                $fields[] = array('label' => 'Ключевое слово', 'name' => 'keyword');
                $fields[] = array('label' => 'Группа', 'type' => 'select', 'values' => $groups, 'name' => 'group', 'extendable' => 'Другая группа');
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

        // сохранение шаблона
        if ($input['phase'] == 'tpl') {
            $filepath = $widget->get_path();
            $php_content = $fx_core->files->readfile($filepath);
            $functions = $this->get_functions();

            try {
                $parser = new fx_admin_parser($php_content);
                $content = $parser->replace_parts($functions, $input);
            } catch (Exception $e) {
                $result['status'] = 'error';
                $result['text'][] = $e->getMessage();
                return $result;
            }

            $fx_core->files->writefile($filepath, $content);
        }

        // сохранение настроек
        if ($input['phase'] == 'settings') {
            $params = array('name', 'group', 'description', 'embed');

            if (!trim($input['name'])) {
                $result['status'] = 'error';
                $result['text'][] = 'Введите название виджета';
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
            $result['text'][] = 'Ошибка при создании временного файла';
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

    public function tpl($widget) {
        $fx_core = fx_core::get_object();
        $functions = $this->get_functions();
        $php_content = $fx_core->files->readfile($widget->get_path());

        $parser = new fx_admin_parser($php_content);
        $parts = $parser->get_parts($functions);

        foreach ($functions as $function) {
            $name = $function['name'];
            $fields[] = array('type' => 'text', 'label' => $name, 'name' => $name, 'value' => $parts[$name], 'code' => $function['type']);
        }

        $fields[] = $this->ui->hidden('id', $widget['id']);
        $fields[] = $this->ui->hidden('tab', $tab);
        $fields[] = $this->ui->hidden('phase', 'tpl');

        $this->response->submenu->set_subactive('tpl');

        return array('essence' => 'widget', 'fields' => $fields, 'form_button' => array('save'));
    }

    /*
    public function tab_fields($widget) {
        $f = new fx_controller_admin_field();
        return $f->items($widget);
    }*/

    public function settings($widget) {

        $groups = fx::data('widget')->get_all_groups();


        $fields[] = $this->ui->label("<a href='/floxim/?essence=admin_widget&amp;action=export&amp;id=".$widget['id']."'>Экспортировать в файл</a>");
        $fields[] = array('label' => 'Ключевое слово: '.$widget['keyword'], 'type' => 'label');

        $fields[] = array('label' => 'Название', 'name' => 'name', 'value' => $widget['name']);
        $fields[] = array('label' => 'Группа', 'type' => 'select', 'values' => $groups, 'name' => 'group', 'value' => $widget['group'], 'extendable' => 'Другая группа');

        $fields[] = array('label' => 'Описание', 'name' => 'description', 'value' => $widget['description'], 'type' => 'text');

        // иконка

        $icon = '<img src="'.$widget->get_icon().'" /><br/>';

        if ($widget['icon']) { // выбранна
            $type = 1;
            $icon .= 'эта иконка выбрана из списка иконок';
        } else if (file_exists(fx::config()->WIDGET_FOLDER.$widget['keyword'].'/icon.png')) { // находится в директории
            $type = 2;
            $icon .= 'эта иконка находится в файле icon.png в директории виджета';
        } else { // по умолчанию
            $type = 3;
            $icon .= 'эта иконка используется по умолчанию';
        }

        $fields[] = array('label' => 'Используемая иконка', 'type' => 'label');

        $fields[] = $this->ui->label($icon);

        $embed = array('miniblock' => 'Миниблок', 'narrow' => 'Узкий', 'wide' => 'Широкий', 'narrow-wide' => 'Узко-широкий');
        $fields[] = $this->ui->radio('embed', 'Размер виджета', $embed, $widget['embed']);

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

    protected function _get_tpl($keyword) {
        return
                "<?php
class widget_".$keyword." extends fx_tpl_widget {

    public function record() {
        extract(\$this->get_vars());
         ?>
        <div>Новый виджет</div>
        <?php
    }

    public function settings() {
        extract(\$this->get_vars());



    }
}
?>";
    }

}

?>

<?php

class fx_controller_admin_layout extends fx_controller_admin_template {

    /*
    public function add($input) {
        $ar = array('inner' => 'Внутренная страница', 'index' => 'Титульная страница', 'map' => 'Карта сайта', 'e404' => 'Страница не найдена');
        $fields[] = $this->ui->select('type', 'Тип', $ar);

        $fields[] = $this->ui->input('name', 'Название', '');
        $fields[] = $this->ui->input('keyword', 'Keyword', '');

        $fields[] = $this->ui->hidden('parent_id', $input['parent_id']);
        $fields[] = $this->ui->hidden('action', 'add');
        $fields[] = $this->ui->hidden('essence', 'layout');
        $fields[] = $this->ui->hidden('posting');

        $result['fields'] = $fields;
        $result['dialog_title'] = 'Добавление новой страницы макета дизайна';
        return $result;
    }

    public function add_save($input) {
        $result = array('status' => 'ok');

        if (!$input['parent_id']) {
            $result['status'] = 'error';
            $result['errors'][] = 'Макет не найден';
            return $result;
        }

        $keyword = trim($input['keyword']);
        $name = trim($input['name']);

        $data = array('name' => $name, 'keyword' => $keyword, 'parent_id' => $input['parent_id'], 'type' => $input['type']);
        $template = fx::data('template')->create($data);

        if (!$template->validate()) {
            $result['status'] = 'error';
            $result['errors'] = $template->get_validate_error();
            return $result;
        }

        try {
            $html = $this->_get_other_tpl();
            fx_controller_admin_layout::compile($template, $html);
        } catch (Exception $e) {
            $result['status'] = 'error';
            $result['text'][] = $e->getMessage();
        }

        $template->save();
        return $result;
    }

    public function edit($input) {
        $fx_core = fx_core::get_object();

        $template = fx::data('template')->get_by_id($input['params'][0]);
        if (!$template) {
            $fields[] = $this->ui->error('Макет не найден');
            return array('fields' => $fields);
        }

        $parent = $template->get_parent();
        $html_path = $template->get_path_html();

        if (!$fx_core->files->file_exists($html_path)) {
            $fields[] = $this->ui->label('Файл со схемой не существует');
            $filecontent = '';
        } else {
            $filecontent = $fx_core->files->readfile($html_path);
        }

        $fields[] = $this->ui->code_editor('filecontent', 'Макет', $filecontent);
        $fields[] = $this->ui->hidden('id', $template['id']);

        $result = array('fields' => $fields, 'form_button' => array('save'));

        //$result['breadcrumbs'][] = array('name' => $parent['name'], 'url' => 'template.operating('.$parent['id'].')');
        //$result['breadcrumbs'][] = array('name' => $template['name']);

        $this->response->submenu->set_menu('template-'.$parent['id'])->set_subactive('layout-'.$template['id']);
        
        fx_controller_admin_template::make_breadcrumb($parent, 'layouts', $this->response->breadcrumb);
        
        $this->response->breadcrumb->add_item($template['name']);
        
        return $result;
    }
    
    
    public function edit_save($input) {
        $result = array('status' => 'ok', 'text' => 'Макет сохранен');
        try {
            $template = fx::data('template')->get_by_id($input['id']);
            fx_controller_admin_layout::compile($template, $input['filecontent']);
        } catch (Exception $e) {
            $result['status'] = 'error';
            $result['text'] = $e->getMessage();
        }

        return $result;
    }

    static public function compile(fx_template $template, $html, $save_html = true) {
        $fx_core = fx_core::get_object();

        $path_html = $template->get_path_html();
        $path_php = $template->get_path_php();

        if ($save_html) {
			$fx_core->files->writefile($path_html, $html);
        }

        $converter = new fx_html2php($html);
        $parts = $converter->get_functions();
        $classname = $template->get_tpl_classname();

        $php_content = '<?php'.PHP_EOL;
        $php_content .= 'class '.$classname.' extends fx_tpl_template {'.PHP_EOL;
        foreach ($parts as $name => $func) {
            if ($name == 'write') {
                $php_content .= ' public function write () {
    extract( $this->get_vars() ); ?>'.PHP_EOL;
            } else {
                $php_content .= 'public function _'.$name.' ( $vars = array() ) {
    extract( $vars );?>'.PHP_EOL;
            }
            $php_content .= $func.PHP_EOL;
            $php_content .= '<?php }'.PHP_EOL;
        }
        $php_content .= '}';

        $fx_core->files->writefile($path_php, $php_content);

        // css and js files
        $files = $converter->get_files();
        $parent = $template->get_parent();

        $parent_files = $parent['files'];
        $exists_file = array();
        if ($parent_files) {
            foreach ($parent_files as $file) {
                $exists_file[] = $file['file'];
            }
        }

        if ($files) {
            foreach ($files as $type => $files_array) {
                foreach ($files_array as $v) {
                    if (!in_array($v, $exists_file)) {
                        $file = array('type' => $type, 'checked' => 1);
                        $file['file'] = $v;
                        $parent_files[] = $file;
                        $exists_file[] = $v;
                    }
                }
            }
        }

        $parent->set('files', $parent_files)->save();
    }
*/
}
?>

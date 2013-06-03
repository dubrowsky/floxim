<?php

class fx_controller_admin_template_files extends fx_controller_admin_template {

    public function add($input) {
        $fields[] = $this->ui->input('file', 'Путь до файла ( например, css/main.css )');

        $fields[] = $this->ui->hidden('posting');
        $fields[] = $this->ui->hidden('action', 'add');
        $fields[] = $this->ui->hidden('essence', 'template_files');
        $fields[] = $this->ui->hidden('template_id', $input['template_id']);

        return array('fields' => $fields);
    }

    public function add_save($input) {
        $fx_core = fx_core::get_object();
        $result = array('status' => 'ok');
        $template = $fx_core->template->get_by_id($input['template_id']);


        if (!$input['file']) {
            $result['status'] = 'error';
            $result['text'][] = 'Введите путь до файла';
            return $result;
        }

        $path = $template->get_path().$input['file'];

        try {
            if (!$fx_core->files->file_exists($path)) {
                $fx_core->files->writefile($path, '');
            }
        } catch (Exception $e) {
            $result['status'] = 'error';
            $result['text'][] = 'Файл не существует и не удается его создать';
            return $result;
        }

        $file = array('file' => $input['file']);
        $file['checked'] = 1;
        preg_match('/(css|js)$/i', $input['file'], $match);
        if ($match[1]) {
            $file['type'] = strtolower($match[1]);
        }

        $files = $template['files'];
        $files[] = $file;

        $template->set('files', $this->unique_files($files))->save();

        return $result;
    }

    protected function unique_files($files) {
        $existing_files = array();
        $result = array();

        if (is_array($files)) {
            foreach ($files as $file) {
                if (!in_array($file['file'], $existing_files)) {
                    $result[] = $file;
                    $existing_files[] = $file['file'];
                }
            }
        }

        return $result;
    }

    public function delete_save($input) {
        $fx_core = fx_core::get_object();

        $template = $fx_core->template->get_by_id($input['template_id']);
        $files = $template['files'];
        
        $ids = $input['id'];
        if ( !is_array($ids) ) $ids = array($ids);
        foreach ( $ids as $id ) {
            unset($files[$id]);
        }
        
        $template->set('files', $files)->save();
        return array('status' => 'ok');
    }
    
    public function edit ( $input ) {
        $fx_core = fx_core::get_object();

        $template = $fx_core->template->get_by_id($input['params'][0]);
        $files = $template['files'];
        $file = $files[$input['params'][1]];
        
        $filepath = $template->get_path().$file['file']; 
        if ( $fx_core->files->file_exists($filepath) ) {
            $content = $fx_core->files->readfile($filepath);
        }
        else {
            $fields[] = $this->ui->label('Файл не существует');
        }
        
        $fields[] = $this->ui->code_editor('filecontent', 'Содержимое файла', $content, 'css');
        $fields[] = $this->ui->hidden('posting');
        $fields[] = $this->ui->hidden('template_id', $template['id']);
        $fields[] = $this->ui->hidden('id', $input['params'][1]);
        
        $result['fields'] = $fields;
        $result['form_button'] = array('save');
        $result['breadcrumb'][] = array('name' => $template['name'], 'url' => 'template.operating('.$template['id'].',files)');
        $result['breadcrumb'][] = array('name' => 'Редактирование файла');
        
        $this->response->submenu->set_menu('template-'.$template['id'])->set_subactive('files');
        return $result;
    }
    
    public function edit_save ($input) {
        $fx_core = fx_core::get_object();
        $result = array('status' => 'ok', 'text' => 'Файл сохранен');
        
        $template = $fx_core->template->get_by_id($input['template_id']);
        $files = $template['files'];
        $file = $files[$input['id']];
        
        $filepath = $template->get_path().$file['file']; 
        try {
            $fx_core->files->writefile($filepath, $input['filecontent']);
        }
        catch ( Exception $e ) {
            $result['ststus'] = 'error';
            $result['text'][] = $e->getMessage();
        }
        
        return $result;
    }

}

?>

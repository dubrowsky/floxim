<?php

class fx_admin_patch {

    protected $patch_dir, $current_version;

    public function install_by_file ( $file ) {
        if (file_exists($file) ) {
            $content = file_get_contents($file);
        }
        return $this->install_by_content($content);
    }
    public function install_by_content($content) {
        $fx_core = fx_core::get_object();

        if ( !$content ) {
            throw new fx_exception_patch("Файл не закачан");
        }

        $tmp_filename = $fx_core->files->create_tmp_file();
        $this->patch_dir = $fx_core->files->create_tmp_dir();
        $fx_core->files->writefile($tmp_filename, $content);
        $fx_core->files->tgz_extract($tmp_filename, $this->patch_dir);

        $xml = $fx_core->files->readfile($this->patch_dir.'patch.xml');
        $fxml = new fx_fxml();
        $xml_data = $fxml->read($xml);

        $this->current_version = $fx_core->get_settings('version');
        $this->check_requirement($xml_data->requirement);

        $files = $xml_data->files;
        if ( $files->count() ) {
            $this->check_rights($files);
        }

        $preinstall = $xml_data->preinstall;
        if ( $preinstall->count() ) {
            $this->process($preinstall);
        }

        if ( $files->count() ) {
            $this->copy_files($files);
        }

        $postinstall = $xml_data->postinstall;
        if ( $postinstall->count() ) {
            $this->process($postinstall);
        }

        $this->update_info($xml_data->version, $xml_data->description);
    }

    protected function check_requirement($requirement) {
        $requirement = (array)$requirement;
        $all_version = $requirement['version'];

        if ( !in_array($this->current_version, $all_version) ) {
            throw new fx_exception_patch("Патч не для этой версии системы. Текущая версия системы: ".$this->current_version.".Патч подходит для версий: ".join(', ', $all_version));
        }
        return true;
    }

    protected function check_rights(fx_xml_element $files) {
        $fx_core = fx_core::get_object();
        $not_rights = array();

        foreach ($files->children() as $file) {
            $file_path = fx::config()->HTTP_ROOT_PATH.$file;
            $dir_path = str_replace(basename($file_path), "", $file_path);

            if ($fx_core->files->file_exists($file_path)) {
                $path = $file_path;
            } else {
                $path = $dir_path;
            }
            if (!$fx_core->files->is_writable($path)) {
                $not_rights[] = $path;
            }
        }

        if ($not_rights) {
            throw new fx_exception_patch("Нет прав на: ".join(', ', array_unique($not_rights)));
        }

        return true;
    }

    protected function process(fx_xml_element $files) {
        foreach ($files->children() as $type => $file) {
            $this->process_file($type, $file);
        }
    }

    protected function process_file($type, $file) {
        $fx_core = fx_core::get_object();
        if (!$fx_core->files->file_exists($this->patch_dir.$file)) {
            throw new fx_exception_patch("Файл ".$file." не найден");
        }
        if ($type == 'php') {
            $this->process_php($file);
        } else if ($type == 'sql') {
            $this->process_sql($file);
        }
    }

    protected function process_php($file) {
        $fx_core = fx_core::get_object();
        $fx_core->files->file_include($this->patch_dir.$file);
    }

    protected function process_sql($file) {
        $fx_core = fx_core::get_object();

        $prefix = fx::config()->DB_PREFIX;
        if ($prefix) {
            $prefix .= '_';
        }
        $sql = $fx_core->files->readfile($this->patch_dir.$file);
        $sql = str_replace('%%FX_PREFIX%%', $prefix, $sql);
        if ($sql) {
            $fx_core->db->exec($sql);
        }
    }

    protected function copy_files ( $files ) {
        $fx_core = fx_core::get_object();

        foreach ($files->children() as $file) {
            $fx_core->files->copy( $this->patch_dir.'files/'.$file, fx::config()->HTTP_ROOT_PATH.$file);
        }
    }

    protected function update_info($version, $description = '') {
        $fx_core = fx_core::get_object();
        $version = (string)$version;
        $fx_core->set_settings('version', $version);
        fx_admin_checkpatch::check(true);

        $finder = fx_data::optional('patch');
        $data = array();
        $data['to'] = $version;
        $data['from'] = $this->current_version;
        $data['description'] = (string)$description;

        $finder->create($data)->save();
    }
}

class fx_exception_patch extends fx_exception {

}

?>

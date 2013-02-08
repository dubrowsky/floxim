<?php

class fx_export {

    protected $essence_with_file = array('component', 'widget', 'template');

    /**
     * Полный цикл экспорта сущности ( компонента/виджета, итд)
     */
    public function export_essence($essence) {
        $fx_core = fx_core::get_object();

        try {
            $dir = $fx_core->files->create_tmp_dir();
            $self = new self();
            $self->export($essence, $dir);
            $file = $this->pack_file($dir);
            $name = $essence->get_type().'.'.$essence['keyword'].'.tgz';
            $this->send_file($file, $name);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Созданние файла-экспорта сущности в заданную директорию
     */
    public function export($essence, $dir, $xml_name = 'export.xml') {
        $fx_core = fx_core::get_object();

        if (!is_array($essence)) {
            $essence = array($essence);
        }

        $fxml_class = new fx_fxml();
        $fxml_str = $fxml_class->create($essence, $dir);
        $fx_core->files->writefile($dir.$xml_name, $fxml_str);

        foreach ($essence as $ess) {
            $keyword = $ess->get('keyword');
            $type = $ess->get_type();
            if (!in_array($type, $this->essence_with_file)) {
                continue;
            }
            $ess_dir = $fx_core->{'HTTP_'.strtoupper($type).'_PATH'}.$keyword;
            $fx_core->files->copy($ess_dir, $dir.$type.'/'.$keyword, true);
        }
    }

    /**
     * Полный цикл экспорта конфигурации
     */
    public function export_configure($sites, $requirements = null) {
        $fx_core = fx_core::get_object();
        $dir = $fx_core->files->create_tmp_dir();

        $sites_id = array();
        foreach ( $sites as $site ) {
            $sites_id[] = $site['id'];
        }
        
        $subdivision_ids = array();
        $subdivisions = $fx_core->subdivision->get_all("site_id IN(".join(',',$sites_id).")");
        foreach ($subdivisions as &$v) {
            $subdivision_ids[] = $v['id'];
        }
        $this->export($subdivisions, $dir, 'subdivision.xml');
        unset($subdivisions);

        $component_ids = array();
        $widget_ids = array();
        //$infoblocks = $fx_core->infoblock->get_all("individual = 1 and subdivision_id in (".join(',', $subdivision_ids).") ");
        $infoblocks = $fx_core->infoblock->get_all("essence_id > 0 ");
        $content_infoblocks = array();
        if ( $infoblocks ) {
            foreach ($infoblocks as &$v) {
                if ($v['essence_id'] && $v['type'] == 'content') {
                    $component_ids[] = $v['essence_id'];
                    $content_infoblocks[] = $v['id'];
                }
                if ($v['essence_id'] && $v['type'] == 'widget') {
                    $widget_ids[] = $v['essence_id'];
                }
            }
            $this->export($infoblocks, $dir, 'infoblock.xml');
        }
        unset($infoblocks);

        $messages = array();
        if ( $component_ids ) {
            $components = $fx_core->component->get_all("id in (".join(',', array_unique($component_ids)).") ");
            $this->export($components, $dir, 'component.xml');
            
            foreach ( $components as &$component ) {
                if ( $component->is_user_component() ) continue;
                $mes = $fx_core->message->get_all($component['id'], "infoblock_id in (".join(',', array_unique($content_infoblocks)).") ");
                $messages = array_merge($messages, $mes);
            }
            unset($components);
        }
        
        if ( $widget_ids ) {
            $widgets = $fx_core->widget->get_all("id in (".join(',', array_unique($widget_ids)).") ");
            $this->export($widgets, $dir, 'widget.xml');
            unset($widgets);
        }
        
        if ( $messages ) {
            $this->export($messages, $dir, 'content.xml');
            unset($messages);
        }
        
        
        $menu = $fx_core->menu->get_all();
        $this->export($menu, $dir, 'menu.xml');

        if ( !$requirements ) {
            $fx_admin_configure = new fx_admin_configure();
            $requirements = $fx_admin_configure->get_requirements($ess['id']);
        }
        
        
        $fxml_class = new fx_fxml();
        $fxml_class->set_requirements($requirements);
        $fxml_str = $fxml_class->create($sites);
        $fx_core->files->writefile($dir.'export.xml', $fxml_str);

        $file = $this->pack_file($dir);
        $name = 'configure.tgz';
        $this->send_file($file, $name);
    }

    protected function pack_file($dir) {
        $fx_core = fx_core::get_object();

        $tgz_filename = $fx_core->files->create_tmp_file();
        $fx_core->files->tgz_create($tgz_filename, $dir);

        return $tgz_filename;
    }

    protected function send_file($file, $name = 'export.tgz') {
        $fx_core = fx_core::get_object();

        while (ob_get_level()) {
            @ob_end_clean();
        }

        header("Content-type: application/x-tar");
        header('Content-Disposition: attachment; filename="'.$name.'"');
        header("Content-Transfer-Encoding: Binary");
        header("Content-length: ".$fx_core->files->filesize($file));

        echo $fx_core->files->readfile($file);
        die;
    }

}

?>
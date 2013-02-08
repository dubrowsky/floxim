<?php

/**
 *@todo place_* уже есть в fx_suitable 
 */
class fx_import {
    protected $params;
    
    protected $rollback_files = array();
    protected $rollback_objects = array();
    
    protected $uuid_to_id = array();
    protected $req_infoblock = array(), $infoblocks = array();
    protected $req_menu = array(), $menu = array();

    public function __construct( $params = array() ) {
        if (is_string($params) ) {
            parse_str($params, $params);
        }
        $this->params = $params;
    }
    
    public function import_by_file($tmp_filename) {
        $content = file_get_contents($tmp_filename);
        return $this->import_by_content($content);
    }

    public function import_by_content($content) {
        $fx_core = fx_core::get_object();

        $tmp_filename = $fx_core->files->create_tmp_file();
        $dir = $fx_core->files->create_tmp_dir();
        $fx_core->files->writefile($tmp_filename, $content);
        $fx_core->files->tgz_extract($tmp_filename, $dir);

        try {
            $result = $this->import($dir);
        } catch (Exception $e) {
            $this->_rollback();
            throw $e;
        }
        
        return $result;
    }

    public function import($dir, $file = 'export.xml') {
        $fx_core = fx_core::get_object();
        $result = array();

        if ( !$fx_core->files->file_exists($dir.$file) ) {
            return false;
        }
        $xml = $fx_core->files->readfile($dir.$file);
        $fxml = new fx_fxml();
        $xml_data = $fxml->read($xml);

        foreach ($xml_data->children()as $element) {
            $attributes = $element->attributes();
            $type = $attributes['type'];

            $method = '_import_'.$type;
            $result[] = $this->$method($dir, $element);
        }

        return $result;
    }

    protected function _import_widget($dir, fx_xml_element $element) {
        $fx_core = fx_core::get_object();
        $data = $this->_obj_to_string_arr($element->data);
        
        $existed_widget = $fx_core->widget->get('store_id', $data['store_id']);
        if ( $existed_widget ) {
            $this->link_with_existed_widget($existed_widget, $element);
            return $existed_widget;
        }
        
        $widget = $fx_core->widget->create($data);

        if (!$widget->validate()) {
            throw new Exception(join(', ', $widget->get_validate_error()));
        }
        $this->_save($widget);
        $uuid = (string) $element->attributes()->uuid;
        $this->uuid_to_id[$uuid] = $widget['id'];

        if ($element->fields) {
            foreach ($element->fields->children() as $v) {
                $data = $this->_obj_to_string_arr($v->data);
                $data['widget_id'] = $widget['id'];

                $field = $fx_core->field->create($data);
                $this->_save($field);

                $uuid = (string) $v->attributes()->uuid;
                $this->uuid_to_id[$uuid] = $field['id'];
            }
        }

        $keyword = $widget['keyword'];
        $fx_core->files->copy($dir.'widget/'.$keyword, $widget->get_folder_path());
        $this->rollback_files[] = $widget->get_path();

        $path = $dir.'widget/'.$keyword.'/install.php';
        if ( $fx_core->files->file_exists($path) ) {
            $vars = array('fx_widget' => $widget);
            $fx_core->files->file_include($path, $vars);
        }
        
        return $widget;
    }

    protected function _import_component($dir, fx_xml_element $element) {
        $fx_core = fx_core::get_object();
        $data = $this->_obj_to_string_arr($element->data);
        
        $existed_component = $fx_core->component->get('store_id', $data['store_id']);
        if ( $existed_component ) {
            $this->link_with_existed_component($existed_component, $element);
            return $existed_component;
        }
        
        $component = $fx_core->component->create($data);

        if (!$component->validate()) {
            foreach ( $component->get_validate_error() as $error ) {
                $msg .= $error['text'].'<br/>';
            }
            throw new Exception($msg);
        }
        $this->_save($component);
        $uuid = (string) $element->attributes()->uuid;
        $this->uuid_to_id[$uuid] = $component['id'];

        if ($element->ctpls) {
            foreach ($element->ctpls->children() as $v) {
                $data = $this->_obj_to_string_arr($v->data);
                $data['component_id'] = $component['id'];
                $ctpl = $fx_core->ctpl->create($data);
                $this->_save($ctpl);

                $uuid = (string) $v->attributes()->uuid;
                $this->uuid_to_id[$uuid] = $ctpl['id'];
            }
        }

        if ($element->fields) {
            foreach ($element->fields->children() as $v) {
                $data = $this->_obj_to_string_arr($v->data);

                $component_uuid = (string) $v->attributes()->component_uuid;
                if ($component_uuid) {
                    $data['component_id'] = $component['id'];
                }
                $ctpl_uuid = (string) $v->attributes()->ctpl_uuid;
                if ($ctpl_uuid) {
                    $data['ctpl_id'] = $this->uuid_to_id[$ctpl_uuid];
                }

                $field = $fx_core->field->create($data);
                $this->_save($field);

                $uuid = (string) $v->attributes()->uuid;
                $this->uuid_to_id[$uuid] = $field['id'];
            }
        }

        $keyword = $component['keyword'];
        $fx_core->files->copy($dir.'component/'.$keyword, $component->get_path());
        $this->rollback_files[] = $component->get_path();

        $path = $dir.'component/'.$keyword.'/install.php';
        if ( $fx_core->files->file_exists($path) ) {
            $vars = array('fx_component' => $component);
            $fx_core->files->file_include($path, $vars);
        }
        
        return $component;
    }
    
    protected function link_with_existed_component ( fx_component $component, fx_xml_element $element ) {
        $fx_core = fx_core::get_object();
        
        $uuid = $element->get_attr('uuid');
        $this->uuid_to_id[$uuid] = $component['id'];
        
        $ctpls = $fx_core->ctpl->get_by_component($component);
        
         if ($element->ctpls) {
            foreach ($element->ctpls->children() as $v) {
                $data = $this->_obj_to_string_arr($v->data);
                $uuid = $v->get_attr('uuid');
                
                foreach ( $ctpls as $ctpl ) {
                    if ( $ctpl['keyword'] == $data['keyword'] ) {
                        $this->uuid_to_id[$uuid] = $ctpl['id'];
                    }
                }
            }
         }
         
         $fields = $component->fields();
         if ($element->fields) {
            foreach ($element->fields->children() as $v) {
                $data = $this->_obj_to_string_arr($v->data);
                $uuid = $v->get_attr('uuid');
                
                foreach ( $fields as $field ) {
                    if ( $field['name'] == $data['name'] ) {
                        $this->uuid_to_id[$uuid] = $field['id'];
                    }
                }
            }
         }        
    }
    
    protected function link_with_existed_widget ( fx_widget $widget, fx_xml_element $element ) { 
        $uuid = $element->get_attr('uuid');
        $this->uuid_to_id[$uuid] = $widget['id'];      
    }
    
    protected function _save($obj) {
        $obj->save();
        $this->rollback_objects[] = $obj;
    }

    protected function _import_template($dir, fx_xml_element $element) {
        $fx_core = fx_core::get_object();
        $data = $this->_obj_to_string_arr($element->data);
        
        $existed_template = $fx_core->template->get('store_id', $data['store_id']);
        if ( $existed_template ) {
            return $existed_template;
        }
        
        
        $template = $fx_core->template->create($data);
        
        if (!$template->validate()) {
             foreach ( $template->get_validate_error() as $error ) {
                $msg .= $error['text'].'<br/>';
            }
            throw new Exception($msg);
        }
        $this->_save($template);
        $uuid = $element->get_attr('uuid');
        $this->uuid_to_id[$uuid] = $template['id'];
        
        if ($element->layouts) {
            foreach ($element->layouts->children() as $v) {
                $data = $this->_obj_to_string_arr($v->data);
                $data['parent_id'] = $template['id'];
                $layout = $fx_core->template->create($data);
                $this->_save($layout);

                $uuid = $v->get_attr('uuid');
                $this->uuid_to_id[$uuid] = $layout['id'];
            }
        }
        
        $keyword = $template['keyword'];
        $fx_core->files->copy($dir.'template/'.$keyword, $template->get_path());
        $this->rollback_files[] = $template->get_path();

        return $template;

    }

    protected function _import_configure($dir, fx_xml_element $element) {
        $fx_core = fx_core::get_object();
        
        $this->import($dir, 'component.xml');
        $this->import($dir, 'widget.xml');

        $this->_import_configure_requirements($element);
        $this->_import_configure_sites($element);
        $this->_import_configure_subdivisions($dir);
        $this->_import_configure_designate_subs($element);

        $this->import($dir, 'infoblock.xml');
        $this->place_infoblock();
        
        $this->import($dir, 'menu.xml');
        $this->place_menu();

        $this->_import_configure_content($dir);
    }

    protected function _import_configure_requirements(fx_xml_element $element) {
        $requirements = $element->requirements[0];
        foreach ($requirements->page as $page) {
            $page_type = $page->get_attr('type');
            foreach ($page->unit as $unit) {
                $unit_data = $this->_obj_to_string_arr($unit);
                $type = $unit->get_attr('type');
                
                if ( $type == 'infoblock' ) {
                    $infoblock_uuid = $unit->get_attr('infoblock_uuid');
                    
                    $embed = $unit_data['embed'];

                    $this->req_infoblock[$infoblock_uuid]['pages'][] = $page_type;
                    $this->req_infoblock[$infoblock_uuid]['embed'] = $embed;
                }
                else if ( $type == 'menu' ) {
                    $menu_uuid = $unit->get_attr('menu_uuid');
                    $this->req_menu[$menu_uuid]['pages'][] = $page_type;
                    if ( $unit_data['direct'] ) {
                        $this->req_menu[$menu_uuid]['direct'] = $unit_data['direct'];
                    }
                    if ( $unit_data['kind'] ) {
                        $this->req_menu[$menu_uuid]['kind'] = $unit_data['kind'];
                    }
                }
               
            }
        }
    }

    protected function _import_configure_sites(fx_xml_element $element) {
        $fx_core = fx_core::get_object();

        foreach ($element->sites as $site_xml) {
            $site_xml = $site_xml->site;
            $uuid = (string) $site_xml->attributes()->uuid;

            $data = $this->_obj_to_string_arr($site_xml->data);
            $site = $fx_core->site->create($data);

            $site['template_id'] = $this->params['template_id'];
            $this->_save($site);

            $this->uuid_to_id[$uuid] = $site['id'];
        }
    }

    protected function _import_configure_subdivisions($dir) {
        $fx_core = fx_core::get_object();

        $subdivision = $this->import($dir, 'subdivision.xml');

        /**
         * Разделы надо вставлять от корня до последнего потомка последовательно, т.е.
         * сначала вставляет разделы без родителей или те разделы, родителей которых мы уже вставили, итд
         */
        $max_level = 100;
        while ($max_level--) {
            if ($subdivision) {
                foreach ($subdivision as $k => $sub) {
                    $parent_uuid = $sub['__parent_uuid'];
                    if (!$parent_uuid || $this->uuid_to_id[$parent_uuid]) {
                        $uuid = $sub['__uuid'];

                        $parent_id = $parent_uuid ? $this->uuid_to_id[$parent_uuid] : 0;
                        $sub['parent_id'] = $parent_id;

                        unset($sub['__uuid'], $sub['__parent_uuid']);

                        $sub_obj = $fx_core->subdivision->create($sub);
                        $this->_save($sub_obj);
                        $this->uuid_to_id[$uuid] = $sub_obj['id'];

                        unset($sub_obj, $subdivision[$k]);
                    }
                }
            } else {
                break;
            }
        }
        if ($subdivision) {
            $link_uuids_error = array();
            foreach ($subdivision as &$sub) {
                $link_uuids_error[] = $sub['__uuid'];
            }
            throw new fx_exception_import("Не удалось найти родительский раздел для разделов:  ".join(', ', $link_uuids_error));
        }
    }

    protected function _import_configure_content($dir) {
        $data = $this->import($dir, 'content.xml');

        $max_level = 100;
        while ($max_level--) {
            if ($data) {
                foreach ($data as $k => $essence) {
                    $parent_uuid = $essence['__parent_uuid'];
                    if (!$parent_uuid || $this->uuid_to_id[$parent_uuid]) {
                        $uuid = $essence['__uuid'];

                        $parent_id = $parent_uuid ? $this->uuid_to_id[$parent_uuid] : 0;
                        $essence['parent_id'] = $parent_id;

                        unset($essence['__uuid'], $essence['__parent_uuid']);

                        $this->_save($essence);
                        $this->uuid_to_id[$uuid] = $essence['id'];

                        unset($essence, $data[$k]);
                    }
                }
            } else {
                break;
            }
        }
        if ($data) {
            $link_uuids_error = array();
            foreach ($data as &$essence) {
                $link_uuids_error[] = $essence['__uuid'];
            }
            throw new fx_exception_import("Не удалось найти родителей для:  ".join(', ', $link_uuids_error));
        }
    }

    protected function _import_subdivision($dir, fx_xml_element $element) {
        $data = $this->_obj_to_string_arr($element->data);

        $data['__uuid'] = $element->get_attr('uuid');
        $parent_uuid = $element->get_attr('parent_uuid');
        if ($parent_uuid) {
            $data['__parent_uuid'] = $parent_uuid;
        }

        $site_uuid = $uuid = (string) $element->attributes()->site_uuid;
        $site_id = $this->uuid_to_id[$site_uuid];
        if (!$site_id) {
            throw new fx_exception_import("Не найден сайт для раздела ".$data['__uuid']);
        }
        $data['site_id'] = $site_id;

        return $data;
    }

    protected function _import_configure_designate_subs(fx_xml_element $element) {
        $fx_core = fx_core::get_object();

        foreach ($element->sites as $site_xml) {
            $site_xml = $site_xml->site;
            $uuid = (string) $site_xml->attributes()->uuid;
            $site = $fx_core->site->get_by_id($this->uuid_to_id[$uuid]);

            $title_sub_uuid = $site_xml->get_attr('title_sub_uuid');
            $title_sub_id = $this->uuid_to_id[$title_sub_uuid];
            $site['title_sub_id'] = $title_sub_id;

            $e404_sub_uuid = $site_xml->get_attr('e404_sub_uuid');
            $e404_sub_id = $this->uuid_to_id[$e404_sub_uuid];
            $site['e404_sub_id'] = $e404_sub_id;

            $site->save();
        }
    }

    protected function _import_infoblock($dir, fx_xml_element $element) {
        $fx_core = fx_core::get_object();

        $data = $this->_obj_to_string_arr($element->data);
        $uuid = $element->get_attr('uuid');

        $infoblock = $fx_core->infoblock->create($data);

        $site_uuid = $element->get_attr('site_uuid');
        $infoblock['site_id'] = $this->uuid_to_id[$site_uuid];
        
        $subdivision_uuid = $element->get_attr('subdivision_uuid');
        if ($subdivision_uuid) {
            $infoblock['subdivision_id'] = $this->uuid_to_id[$subdivision_uuid];
        }

        $essence_uuid = $element->get_attr('essence_uuid');
        if ($essence_uuid) {
            $infoblock['essence_id'] = $this->uuid_to_id[$essence_uuid];
        }

        $list_ctpl_uuid = $element->get_attr('list_ctpl_uuid');
        if ($list_ctpl_uuid) {
            $infoblock['list_ctpl_id'] = $this->uuid_to_id[$list_ctpl_uuid];
        }
        
        $parent_uuid = $element->get_attr('parent_uuid');
        if ($parent_uuid) {
            $infoblock['__parent_uuid'] = $parent_uuid;
        }
        
        $field_uuid = $element->get_attr('field_uuid');
        if ($field_uuid) {
            $infoblock['field_id'] = $this->uuid_to_id[$field_uuid];
        }
       
        if ($data['main_content']) {
            $infoblock['keyword'] = 'main_content';
            $this->_save($infoblock);
            $this->uuid_to_id[$uuid] = $infoblock['id'];
            
        } else {
            $infoblock['__uuid'] = $uuid;
            $infoblock['__embed'] = $this->req_infoblock[$uuid]['embed'];
            $infoblock['__pages'] = $this->req_infoblock[$uuid]['pages'];
            $this->infoblocks[] = $infoblock;
        }

    }
    
    protected function _import_menu ( $dir, fx_xml_element $element ) {
        $fx_core = fx_core::get_object();
        
        $data = $this->_obj_to_string_arr($element->data);
        $uuid = $element->get_attr('uuid');
        $site_uuid = $element->get_attr('site_uuid');
        $data['site_id'] = $this->uuid_to_id[$site_uuid];
        
        $settings = unserialize($data['settings']);
        if ( $settings['sub'] ) {
            $settings['sub'] = $this->uuid_to_id[$settings['sub']];
        }
        
        $data['settings'] = $settings;

        $menu = $fx_core->menu->create($data);
        $this->menu[$uuid] = $menu;        
    }
    
    protected function get_all_layouts () {
        $fx_core = fx_core::get_object();
        $template = $fx_core->template->get_by_id( $this->params['template_id'] );
        $layouts = $template->get_layouts();
        return $layouts;
    }
  
    protected function place_infoblock ( ) {
        $fx_core = fx_core::get_object();

        $all_targets = array();
        $layouts = $this->get_all_layouts();
        foreach ($layouts as $layout) {
            $units = $layout->get_units();
            if ($units['infoblock']) {
                foreach ($units['infoblock'] as $unit_keyword => $unit) {
                    if ( $unit['main'] ) {
                        continue;
                    }
                    $all_targets[$unit_keyword]['pages'][] = $layout['type'];
                    $all_targets[$unit_keyword]['embed'] = $unit['embed'];
                    $all_targets[$unit_keyword]['max_repeat'] = $unit['max_repeat'] ? $unit['max_repeat'] : 100;
                }
            }
        }
        
        foreach ( $this->infoblocks as $infoblock ) {
            if ( $infoblock['__parent_uuid'] ) {
                continue;
            }
            $best_keyword = '';
            $best_embed = '';
            $best_repeat = 0;
            $embed = $infoblock['__embed'];

            foreach ( $all_targets as $keyword  => $target ) {
                if ( !$target['max_repeat'] ) {
                    continue;
                }

                if( array_diff($infoblock['__pages'], $target['pages'])) {
                    continue;
                }
                
                $t_embed = $target['embed'];

                if ( $embed == 'miniblock' && $t_embed  == 'miniblock' ) {
                    
                    if ( $target['max_repeat'] > $best_repeat ) {
                        $best_keyword = $keyword;
                        $best_embed = 'miniblock';
                        $best_repeat = $target['max_repeat'];
                    }
                }
                
                if ( $embed == 'miniblock' && $t_embed  == 'vertical' && $best_embed != 'miniblock' ) {
                    if ( $target['max_repeat'] > $best_repeat ) {
                        $best_keyword = $keyword;
                        $best_embed = 'vertical';
                        $best_repeat = $target['max_repeat'];
                    }
                }
                if ( $embed == 'narrow' && $t_embed  == 'vertical' ) {
                    if ( $target['max_repeat'] > $best_repeat ) {
                        $best_keyword = $keyword;
                        $best_repeat = $target['max_repeat'];
                    }
                } 
            }
            
            
            if ( $best_keyword ) {
                $infoblock['keyword'] = $best_keyword;
                $infoblock['template_id'] = $this->params['template_id']; 
                $uuid = $infoblock['__uuid'];
                unset($infoblock['__uuid'], $infoblock['__pages'], $infoblock['__embed']);
                $all_targets[$keyword]['max_repeat']--;
                $this->_save($infoblock);
                $this->uuid_to_id[$uuid] = $infoblock['id'];
            }
        }
        
        
        foreach ( $this->infoblocks as $infoblock ) {
            if ( $infoblock['id'] ) {
                continue;
            }
            if ( $infoblock['__parent_uuid'] ) {
                continue;
            }
            
            $best_keyword = '';
            $best_embed = '';
            $best_repeat = 0;
            $embed = $infoblock['__embed'];

            foreach ( $all_targets as $keyword  => $target ) {
                if ( !$target['max_repeat'] ) {
                    continue;
                }
                
                if( array_diff($infoblock['__pages'], $target['pages'])) {
                    continue;
                }
                
                $t_embed = $target['embed'];

                if ( $embed == 'narrow-wide' && $t_embed  == 'vertical' ) {  
                    if ( $target['max_repeat'] > $best_repeat ) {
                        $best_keyword = $keyword;
                        $best_embed = 'vertical';
                        $best_repeat = $target['max_repeat'];
                    }
                }
                
                if ( $embed == 'narrow-wide' && $t_embed  == 'large' && $best_embed != 'vertical' ) {
                    if ( $target['max_repeat'] > $best_repeat ) {
                        $best_keyword = $keyword;
                        $best_embed = 'large';
                        $best_repeat = $target['max_repeat'];
                    }
                }
                if ( $embed == 'wide' && $t_embed  == 'large' ) {
                    if ( $target['max_repeat'] > $best_repeat ) {
                        $best_keyword = $keyword;
                        $best_repeat = $target['max_repeat'];
                    }
                }   
            }
            
            
            if ( $best_keyword ) {
                $infoblock['keyword'] = $best_keyword;
                $infoblock['template_id'] = $this->params['template_id']; 
                $uuid = $infoblock['__uuid'];
                unset($infoblock['__uuid'], $infoblock['__pages'], $infoblock['__embed']);
                $all_targets[$keyword]['max_repeat']--;
                $this->_save($infoblock);
                $this->uuid_to_id[$uuid] = $infoblock['id'];
            }
        }
        
        foreach ( $this->infoblocks as $infoblock ) {
            if ( $infoblock['id'] || !$infoblock['__parent_uuid'] ) {
                continue;
            }
            $infoblock['parent_id'] = $this->uuid_to_id[ $infoblock['__parent_uuid'] ];
            unset($infoblock['__parent_uuid']);
            $uuid = $infoblock['__uuid'];
            unset($infoblock['__uuid'], $infoblock['__pages'], $infoblock['__embed']);
            $this->_save($infoblock);
            $this->uuid_to_id[$uuid] = $infoblock['id'];
        }
        
    }
    
    protected function place_menu () {
       $fx_core = fx_core::get_object();

        $all_targets = array();
        $layouts = $this->get_all_layouts();
        foreach ($layouts as $layout) {
            $units = $layout->get_units();
            if ($units['menu']) {
                foreach ($units['menu'] as $unit_keyword => $unit) {
                    if ( $unit['type'] == 'path' || $unit['type'] == 'force' ) {
                        continue;
                    }
                    if ( $unit['necessary'] ) {
                        $all_targets[$unit_keyword]['necessary'] = true;
                    }
                    if ( $unit['kind'] ) {
                        $all_targets[$unit_keyword]['kind'] = $unit['kind'];
                    }
                    if ( $unit['direct'] ) {
                        $all_targets[$unit_keyword]['direct'] = $unit['direct'];
                    }
                    $all_targets[$unit_keyword]['pages'][] = $layout['type'];
                }
            }
        }
        
        foreach( $this->req_menu as $uuid => $req_menu ) {
            foreach ( $all_targets as $keyword => $target ) {
                if ( $target['direct'] != $req_menu['direct'] ) {
                    continue;
                }
                
                unset($all_targets[$keyword]);
                if ( $uuid ) {
                    $this->menu[$uuid]->set('keyword', $keyword)->set('template_id', $this->params['template_id'])->save();
                }
            }
        }
    }

    protected function _import_message($dir, fx_xml_element $element) {
        $fx_core = fx_core::get_object();

        $data = $this->_obj_to_string_arr($element->data);
        $data['__uuid'] = $element->get_attr('uuid');
        $parent_uuid = $element->get_attr('parent_uuid');
        if ($parent_uuid) {
            $data['__parent_uuid'] = $parent_uuid;
        }
        
        $component_uuid = $element->get_attr('component_uuid');
        $component_id = $this->uuid_to_id[$component_uuid];
        if (!$component_id) {
            throw new fx_exception_import("Не удалось найти компонент для объекта ".$uuid);
        }
        
        $infoblock_uuid = $element->get_attr('infoblock_uuid');
        $infoblock_id = $this->uuid_to_id[$infoblock_uuid];
        $data['infoblock_id'] = $infoblock_id;
        
        $message = $fx_core->message->create($component_id, $data);
        
        $fields = $fx_core->component->get_by_id($component_id)->fields();
        foreach ($fields as $field) {
            $name = $field->get_name();
            $message[$name] = $field->get_import_value($message, $data[$name], $dir.'files/');
        }
        
        return $message;
    }

    private function _obj_to_string_arr($obj) {
        $res = array();
        if ($obj) { 
            foreach ((array) $obj as $i => $val) { 
                if ( $val && is_object($val) ) {
                    $res[$i] = $this->_obj_to_string_arr($val);
                }
                else if ($val) {
                    $res[$i] = (string) $val;
                }
            }
        }
        return $res;
    }

    protected function _rollback() {
        $fx_core = fx_core::get_object();

        foreach (array_reverse($this->rollback_objects) as $v) {
            try {
                $v->delete();
            } catch (Exception $e) {
                // ничего не делаем
            }
        }
        foreach (array_reverse($this->rollback_files) as $v) {
            try {
                $fx_core->files->rm($v);
            } catch (Exception $e) {
                // ничего не делаем
            }
        }
    }

}

class fx_exception_import extends fx_exception {
    
}

?>
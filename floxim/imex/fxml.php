<?php

class fx_xml_element extends SimpleXMLElement {

    public function get_name() {
        return $this->getName();
    }

    public function get_attr($item) {
        return (string) $this->attributes()->$item;
    }

}

class fx_fxml {

    private $fxml_version = "1.0";
    private $xml;
    private $fxml;
    private $dir;
    
    protected $requirements;

    /**
     * создает файл export.xml
     * @param mixed $essence - массив объектов типа fx_essence
     * @return string - итоговый XML
     */
    public function create($essence, $dir = '') {
        $this->dir = $dir;
        $this->xml = new DomDocument('1.0', 'utf-8');
        $this->fxml = $this->xml->appendChild($this->xml->createElement('fxml'));

        // пишем версию fxml
        $attribute = $this->xml->createAttribute('version');
        $attribute->value = $this->fxml_version;
        $this->fxml->appendChild($attribute);

        foreach ($essence as $ess) {
            $type = $ess->get_type();
            $res = false;
            $method = '_create_'.$type;
            $res = $this->$method($ess);

            if (!$res) {  // ошибка при создании fxml для сущности или неизвестная сущность
                return null;
            }
        }

        $this->xml->formatOutput = true;
        return $this->xml->saveXML();
    }

    /**
     *
     * @return fx_xml_element 
     */
    public function read($xml_str) {
        $fxml = simplexml_load_string($xml_str, 'fx_xml_element');
        return $fxml;
    }

    protected function _create_component($ess) {
        $fx_core = fx_core::get_object();

        $data = $ess->get();
        $id = $data['id'];
        $fields = $ess->fields();

        $el = $this->_make_main_element('component', $id);

        //пишем данные самого компонента
        $item = $el->appendChild($this->xml->createElement("data"));
        $this->_add_data_to_node($item, $data, 'id');


        // пишем шаблоны компонента
        $ctpl_objs = $fx_core->ctpl->get_by_component($id);
        $item = $el->appendChild($this->xml->createElement("ctpls"));
        foreach ($ctpl_objs as $ctpl_obj) {
            $ctpl = $ctpl_obj->get();
            $ctpl_item = $item->appendChild($this->xml->createElement("ctpl"));
            $uuid = $this->_get_uuid('ctpl', $ctpl['id']);
            $this->_make_attr($ctpl_item, 'uuid', $uuid);

            // пишем parent_id
            if ($ctpl['parent_id']) {
                $uuid = $this->_get_uuid('ctpl', $ctpl['parent_id']);
                $this->_make_attr($ctpl_item, 'parent_uuid', $uuid);
            }

            $data_ctpl_item = $ctpl_item->appendChild($this->xml->createElement("data"));
            $this->_add_data_to_node($data_ctpl_item, $ctpl, 'id,component_id,parent_id');

            $fields = array_merge($fields, $ctpl_obj->fields());
        }

        $this->_make_fields_node($el, $fields);
        return true;
    }

    protected function _make_fields_node($parent_node, $fields) {
        $item = $parent_node->appendChild($this->xml->createElement("fields"));
        foreach ($fields as $field) {
            $field_item = $item->appendChild($this->xml->createElement("field"));
            $uuid = $this->_get_uuid('field', $field['id']);
            $this->_make_attr($field_item, 'uuid', $uuid);

            if ($field['ctpl_id']) {
                $uuid = $this->_get_uuid('ctpl', $field['ctpl_id']);
                $this->_make_attr($field_item, 'ctpl_uuid', $uuid);
            } else if ($field['component_id']) {
                $uuid = $this->_get_uuid('component', $field['component_id']);
                $this->_make_attr($field_item, 'component_uuid', $uuid);
            } else if ($field['widget_id']) {
                $uuid = $this->_get_uuid('widget', $field['widget_id']);
                $this->_make_attr($field_item, 'widget_uuid', $uuid);
            }

            $data_field_item = $field_item->appendChild($this->xml->createElement("data"));
            $this->_add_data_to_node($data_field_item, $field->get(), 'id,component_id,widget_id,ctpl_id');
        }
    }

    protected function _create_widget($ess) {
        $data = $ess->get();
        $el = $this->_make_main_element('widget', $data['id']);

        $item = $el->appendChild($this->xml->createElement("data"));
        $this->_add_data_to_node($item, $data, 'id');

        $fields = $ess->fields();
        $this->_make_fields_node($el, $fields);

        return true;
    }

    protected function _create_template(fx_template $ess) {
        $data = $ess->get();
        if ($data['type'] != 'parent') {
            return null;  // нам передили не сам макет дизайна, а каую-то его страницу
        }
        $el = $this->_make_main_element('template', $data['id']);

        //пишем данные самого макета
        $item = $el->appendChild($this->xml->createElement("data"));
        $this->_add_data_to_node($item, $data, 'id,parent_id');

        $layouts = $ess->get_layouts();
        $item = $el->appendChild($this->xml->createElement("layouts"));
        foreach ($layouts as $layout) {
            $layout_data = $layout->get();
            $layout_item = $item->appendChild($this->xml->createElement("layout"));
            $data_item = $layout_item->appendChild($this->xml->createElement("data"));
            $this->_add_data_to_node($data_item, $layout_data, 'id,parent_id,colors,files,store_id');

            $units_item = $layout_item->appendChild($this->xml->createElement("units"));
            $units = $layout->get_units();
            foreach ($units as $type => $unit) {
                foreach ($unit as $params) {
                    $unit_item = $units_item->appendChild($this->xml->createElement("unit"));
                    $this->_make_attr($unit_item, 'type', $type);
                    $this->_add_data_to_node($unit_item, $params);
                }
            }
        }

        return true;
    }

    protected function _create_subdivision($ess) {
        $data = $ess->get();
        $id = $data['id'];

        $el = $this->_make_main_element('subdivision', $id);

        if ($data['parent_id']) {
            $uuid = $this->_get_uuid('subdivision', $data['parent_id']);
            $this->_make_attr($el, 'parent_uuid', $uuid);
        }

        $uuid = $this->_get_uuid('site', $data['site_id']);
        $this->_make_attr($el, 'site_uuid', $uuid);

        $item = $el->appendChild($this->xml->createElement("data"));
        $this->_add_data_to_node($item, $data, 'id,parent_id,template_id,site_id');

        return true;
    }

    protected function _create_infoblock($ess) {
        $data = $ess->get();
        $id = $data['id'];

        $el = $this->_make_main_element('infoblock', $id);

        $uuid = $this->_get_uuid('site', $data['site_id']);
        $this->_make_attr($el, 'site_uuid', $uuid);

        if ($data['subdivision_id']) {
            $uuid = $this->_get_uuid('subdivision', $data['subdivision_id']);
            $this->_make_attr($el, 'subdivision_uuid', $uuid);
        }

        if ($data['essence_id']) {
            $essence_type = $data['type'] == 'content' ? 'component' : 'widget';
            $uuid = $this->_get_uuid($essence_type, $data['essence_id']);
            $this->_make_attr($el, 'essence_uuid', $uuid);
        }

        if ($data['list_ctpl_id']) {
            $uuid = $this->_get_uuid('ctpl', $data['list_ctpl_id']);
            $this->_make_attr($el, 'list_ctpl_uuid', $uuid);
        }

        if ($data['full_ctpl_id']) {
            $uuid = $this->_get_uuid('ctpl', $data['full_ctpl_id']);
            $this->_make_attr($el, 'full_ctpl_uuid', $uuid);
        }

        if ($data['parent_id']) {
            $uuid = $this->_get_uuid('infoblock', $data['parent_id']);
            $this->_make_attr($el, 'parent_uuid', $uuid);
        }

        if ($data['field_id']) {
            $uuid = $this->_get_uuid('field', $data['field_id']);
            $this->_make_attr($el, 'field_uuid', $uuid);
        }

        $item = $el->appendChild($this->xml->createElement("data"));
        $this->_add_data_to_node($item, $data, 'id,keyword,subdivision_id,essence_id,list_ctpl_id,full_ctpl_id,field_id,parent_id');

        return true;
    }

    protected function _create_message(fx_message $ess) {
        $fx_core = fx_core::get_object();
        $data = $ess->get();
        $id = $data['id'];
        $component_id = $ess->get_component_id();

        $el = $this->_make_main_element('message', $component_id.'-'.$id);
        $this->_make_attr($el, 'component_uuid', $this->_get_uuid('component', $component_id));

        if ($data['infoblock_id']) {
            $uuid = $this->_get_uuid('infoblock', $data['infoblock_id']);
            $this->_make_attr($el, 'infoblock_uuid', $uuid);
        }

        if ($data['parent_id']) {
            $infoblock = $fx_core->infoblock->get_by_id($data['infoblock_id']);
            $parent_component_id = $fx_core->infoblock->get_by_id($infoblock['parent_id'])->get('essence_id');
            $this->_make_attr($el, 'parent_uuid', $this->_get_uuid('message', $parent_component_id.'-'.$data['parent_id']));
        }

        $fields = $fx_core->component->get_by_id($component_id)->fields();
        foreach ($fields as $field) {
            $name = $field->get_name();
            $data[$name] = $field->get_export_value($data[$name], $this->dir.'files/');
        }

        $item = $el->appendChild($this->xml->createElement("data"));
        $this->_add_data_to_node($item, $data, 'id,parent_id,user_id,infoblock_id,created,last_updated');

        return true;
    }

    public function set_requirements ( $requirements ) {
        $this->requirements = $requirements;
    }
    
    protected function _create_site($ess) {
        $data = $ess->get();

        $requirements = $this->requirements;
        $el = $this->_make_main_element('configure');

        $sites_item = $this->xml->createElement('sites');
        $el->appendChild($sites_item);

        $site_item = $this->xml->createElement('site');
        $sites_item->appendChild($site_item);
        $uuid = $this->_get_uuid('site', $data['id']);
        $this->_make_attr($site_item, 'uuid', $uuid);

        $title_sub_id_uuid = $this->_get_uuid('subdivision', $data['title_sub_id']);
        $this->_make_attr($site_item, 'title_sub_uuid', $title_sub_id_uuid);
        $e404_sub_id_uuid = $this->_get_uuid('subdivision', $data['e404_sub_id']);
        $this->_make_attr($site_item, 'e404_sub_uuid', $e404_sub_id_uuid);

        $item = $site_item->appendChild($this->xml->createElement("data"));
        $this->_add_data_to_node($item, $data, 'id,template_id,title_sub_id,e404_sub_id,domain,created,last_updated,parent_id');


        $req = $this->xml->createElement('requirements');
        $el->appendChild($req);

        foreach ($requirements as &$page) {
            $page_tag = $this->xml->createElement('page');
            $attribute = $this->xml->createAttribute('type');
            $attribute->value = $page['type'];
            $page_tag->appendChild($attribute);

            foreach ($page['units'] as &$unit) {
                $unit_tag = $this->xml->createElement('unit');
                $this->_make_attr($unit_tag, 'type', $unit['type']);
                foreach ($unit as $k => $v) {
                    if ( !$v ) continue;
                    if ($k == 'type' || $k == 'name') continue;
                    if ($k == 'id' && $unit['type'] == 'infoblock') {
                        $infoblock_uuid = $this->_get_uuid('infoblock', $v);
                        $this->_make_attr($unit_tag, 'infoblock_uuid', $infoblock_uuid);
                        continue;
                    }
                    if ($k == 'id' && $unit['type'] == 'menu') {
                        $menu_uuid = $this->_get_uuid('menu', $v);
                        $this->_make_attr($unit_tag, 'menu_uuid', $menu_uuid);
                        continue;
                    }
                    $unit_tag->appendChild($this->xml->createElement($k))->
                            appendChild($this->xml->createTextNode($v));
                }
                $page_tag->appendChild($unit_tag);
            }

            $req->appendChild($page_tag);
        }

        return true;
    }

    protected function _create_menu($ess) {
        $data = $ess->get();

        if ($data['settings']['sub']) {
            $data['settings']['sub'] = $this->_get_uuid('subdivision', $data['settings']['sub']);
        }

        $el = $this->_make_main_element('menu', $data['id']);

        $site_uuid = $this->_get_uuid('site', $data['site_id']);
        $this->_make_attr($el, 'site_uuid', $site_uuid);

        $item = $el->appendChild($this->xml->createElement("data"));
        $this->_add_data_to_node($item, $data, 'id,subdivision_id,keyword,site_id');

        return true;
    }

    protected function _make_main_element($type, $id = 0) {
        $el = $this->fxml->appendChild($this->xml->createElement('essence'));
        $this->_make_attr($el, 'type', $type);

        if ($id) {
            $uuid = $this->_get_uuid($type, $id);
            $this->_make_attr($el, 'uuid', $uuid);
        }

        return $el;
    }

    protected function _make_attr($node, $name, $value = null) {
        $attribute = $this->xml->createAttribute($name);
        if ($value !== null) {
            $attribute->value = $value;
        }
        $node->appendChild($attribute);
    }

    protected function _add_data_to_node($node, $data, $exclude = '') {
        if (is_string($exclude)) {
            $exclude = explode(',', $exclude);
        }
        if (!$exclude) $exclude = array();

        foreach ($data as $i => $v) {
            if (in_array($i, $exclude)) continue;


            if (is_object($v)) {
                $obj_node = $this->xml->createElement($i);
                foreach ($v as $obj_key => $obj_value) {
                    $item = $this->xml->createElement($obj_key);
                    $item->appendChild( $this->xml->createTextNode($obj_value) );
                    $obj_node->appendChild($item);
                }
                $node->appendChild($obj_node);
            } else {
                if (is_array($v)) {
                    $v = serialize($v);
                }

                $node->appendChild($this->xml->createElement($i))->
                        appendChild($this->xml->createTextNode($v));
            }
        }
    }

    /**
     * @todo сделать проверку связанности
     */
    protected function _get_uuid($essence, $id) {
        static $uuids = array();

        if (!isset($uuids[$essence][$id])) {
            $uuids[$essence][$id] = fx_core::get_object()->util->gen_uuid();
        }

        return $uuids[$essence][$id];
    }

}

?>

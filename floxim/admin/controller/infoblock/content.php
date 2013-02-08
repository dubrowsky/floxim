<?php

class fx_controller_admin_infoblock_content extends fx_controller_admin_infoblock {

    public function settings($input) {
        return self::add($input);
    }

    /**
     * Возвращает список компонентов для выбора типа инфоблока и
     * список шаблонов для виджетов
     */
    public function get_widget_list($infoblock_info, $available_components = null) {
        $fx_core = fx_core::get_object();
        $result = array();

        $components = $fx_core->component->get_all();
        $ctpls = array();
        foreach ($fx_core->ctpl->get_all() as $ctpl) {
            $ctpls[$ctpl['component_id']][] = $ctpl;
        }

        foreach ($components as $component) {
            $id = $component->get_id();
            
            if ( $available_components && !in_array($id, $available_components) ) {
                continue;
            }
            
            // поиск в компоненте подходящего шаблона
            $suitable_component = false;
            foreach ($ctpls[$id] as $ctpl) {
                if ( !fx_suitable::is_suitable($infoblock_info, $ctpl) ) {
                    continue;
                }
                if ($ctpl['notwidget']) {
                    $suitable_component = true;
                }
                
                // шаблон компонента выступает как виджет
                if ($ctpl['widget']) {
                    $result[] = array('id' => 'content---mirror---'.$id.'---'.$ctpl['id'], 'real_id' => $id, 'name' => $component['name'].'-'.$ctpl['name'], 'group' => FX_ADMIN_WIDGETS, 'icon' => $component->get_icon());
                }
            }

            if ($suitable_component) {
                $result[] = array('id' => 'content---block---'.$id, 'real_id' => $id, 'name' => $component['name'], 'group' => FX_ADMIN_COMPONENTS, 'icon' => $component->get_icon());
            }
        }
        
        return $result;
    }

    public function save($infoblock, $input) {
        $sort = array();
        if (isset($input['sort_type'])) {
            $sort['type'] = $input['sort_type'];
            if ($sort['type'] == 'field') {
                $sort['fields'] = $input['sort_fields'] ? $input['sort_fields'] : array('priority' => 'desc');
            }
        }

        $infoblock->set('sort', $sort);  
    }

    protected function find_name_and_keyword($name, $url, $subdivision_id) {
        $fx_core = fx_core::get_object();

        $exists_infoblocks = $fx_core->infoblock->get_all('subdivision_id', $subdivision_id);

        $exists_name = array();
        $exists_url = array();
        foreach ($exists_infoblocks as $exists_infoblock) {
            $exists_name[] = $exists_infoblock['name'];
            $exists_url[] = $exists_infoblock['url'];
        }

        $n = '';
        $found = false;
        while (!$found) {
            $maybe_name = $name.( $n ? " ($n)" : "");
            $maybe_url = $url.$n;
            if (in_array($maybe_name, $exists_name) || in_array($maybe_url, $exists_url)) {
                $n++;
            } else {
                $found = true;
            }
        }

        return array('name' => $maybe_name, 'url' => $maybe_url);
    }

    protected function get_list_parametrs(fx_ctpl $ctpl, $infoblock = null) {
        $fields = array();
        if (!$ctpl['with_list']) {
            return $fields;
        }
        
        // значения по умолчанию
        if ($infoblock) {
            $rec_num = $infoblock['rec_num'];
            $sort_type = $infoblock['sort']['type'];
            $sort_fields = $infoblock['sort']['fields'];
        } else {
            $rec_num = '';
        }

        if (!$sort_type) {
            $sort_type = 0;
        }
        if (!$sort_fields) {
            $sort_fields = array(array('field' => 'priority', 'order' => 'desc'), array('field' => 'id', 'order' => 'desc'));
        }

        $fields[] = array('name' => 'rec_num', 'label' => FX_ADMIN_REC_NUM, 'value' => $rec_num, 'current' => $ctpl['rec_num']);

        if (!$ctpl['sort']['unchangeable']) {
            $values = array(0 => 'наследовать от компонента ('.constant('FX_ADMIN_SORT_'.strtoupper($ctpl->get_sort_type())).')');
            foreach (array('manual', 'field', 'last', 'random') as $v) {
                $values[$v] = constant('FX_ADMIN_SORT_'.strtoupper($v));
            }

            $fields[] = array('id' => 'sort_type', 'name' => 'sort_type', 'type' => 'radio', 'label' => FX_ADMIN_SORT, 'value' => $sort_type, 'values' => $values, 'layer' => 'more');

            $sortable_fields = $ctpl->get_component()->get_sortable_fields();
            $order = array('asc' => FX_ADMIN_SORT_ASC, 'desc' => FX_ADMIN_SORT_DESC);
            $fields[] = array('name' => 'sort_fields', 'label' => '', 'layer' => 'more', 'type' => 'set', 'parent' => array('sort_type', 'field'),
                    'unactive' => true,
                    'labels' => array(FX_ADMIN_SORT_BY, FX_ADMIN_SORT_ORDER),
                    'tpl' => array(
                            array('name' => 'field', 'type' => 'select', 'values' => $sortable_fields),
                            array('name' => 'order', 'type' => 'select', 'values' => $order)),
                    'values' => $sort_fields
            );
        }
        
        return $fields;
    }
    
    protected function get_visual_settings ( fx_ctpl $ctpl, $infoblock = null ) {
        $fields = array();
        foreach ($ctpl->fields() as $field) {
            $fields[] = $field->get_js_field($infoblock['visual'], 'visual[%name%]');
        }
        return $fields;
    }
      

}

?>

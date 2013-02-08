<?php

class fx_controller_admin_infoblock_content_mirror extends fx_controller_admin_infoblock_content {

    public function add_main($input) {
        $fx_core = fx_core::get_object();

        if ( $input['id'] ) {
            $infoblock = $fx_core->infoblock->get_by_id($input['id']);
            $content_selection_type_value = $infoblock['content_selection']['type'];
        }
        if ( !$content_selection_type_value ) {
            $content_selection_type_value = 'auto';
        }
        $component = $fx_core->component->get_by_id($input['essence_id']);
        $ctpl = $fx_core->ctpl->get_by_id($input['additional_essence']);
        $fields[] = $this->ui->hidden('list_ctpl_id', $ctpl['id']);

        $contents_infoblocks = $fx_core->infoblock->get_content_infoblocks($component['id']);
        foreach ($contents_infoblocks as $content_infoblock) {
            $sub_id = $content_infoblock['subdivision_id'];
            $subdivision = $fx_core->subdivision->get_by_id($sub_id);
            if ($subdivision) {
				$sub_name = $subdivision->get('name');
				$values[$content_infoblock['id']] = $content_infoblock['name']." (раздел $sub_name)";
            } else {
            	dev_log('subdiv not found', $sub_id);
            }
        }

        $source_types = array('all' => 'все разделы', 'select' => 'выбранные разделы');
        $fields[] = array('type' => 'radio', 'name' => 'source_type', 'id' => 'source_type', 'label' => 'Источник данных', 'value' => 'all', 'values' => $source_types);
        $fields[] = array('name' => 'source_infoblocks', 'label' => 'Выберите разделы', 'type' => 'checkbox', 'values' => $values, 'parent' => array('source_type', 'select'), 'unactive' => true);


        $obj_select = array('auto' => 'автоматически', 'manual' => 'вручную');
        $fields[] = array('type' => 'radio', 'id' => 'content_selection_type', 'name' => 'content_selection_type', 'label' => 'Выбриать объекты', 'values' => $obj_select, 'value' => $content_selection_type_value);

        $fields[] = array('name' => 'rec_num', 'label' => 'Количество', 'parent' => array('content_selection_type', 'auto'), 'unactive' => true);

        $values = array(0 => 'наследовать от компонента');
        foreach (array('manual', 'field', 'last', 'random') as $v) {
            $values[$v] = constant('FX_ADMIN_SORT_'.strtoupper($v));
        }
        $fields[] = array('id' => 'sort', 'name' => 'sort_type', 'type' => 'radio', 'label' => FX_ADMIN_SORT, 'value' => $sort, 'values' => $values, 'parent' => array('content_selection_type', 'auto'));
        // поля для сортировки
        $sortable_fields = $component->get_sortable_fields();
        $order = array('asc' => FX_ADMIN_SORT_ASC, 'desc' => FX_ADMIN_SORT_DESC);
        $fields[] = array('name' => 'sort_fields', 'label' => '', 'layer' => 'more', 'type' => 'set', 'parent' => array('sort_type', 'field'),
                'unactive' => true,
                'labels' => array(FX_ADMIN_SORT_BY, FX_ADMIN_SORT_ORDER),
                'tpl' => array(
                        array('name' => 'field', 'type' => 'select', 'values' => $sortable_fields),
                        array('name' => 'order', 'type' => 'select', 'values' => $order)),
                'values' => $sort_fields
        );

        //$fields = array_merge($fields, $this->get_visual_settings($ctpl, $infoblock) );
        
        $fields[] = $this->ui->hidden('posting');
        return $fields;
    }

    public function save($infoblock, $input) {
        parent::save($infoblock, $input);
        $source_type = $input['source_type'];
        if (!in_array($source_type, array('all', 'select'))) {
            $source_type = 'all';
        }
        $source = array('type' => $source_type);

        if ($input['source_infoblocks']) {
            foreach ($input['source_infoblocks'] as $v) {
                $source['infoblocks'][] = intval($v);
            }
        }
        
        $content_selection_type = $input['content_selection_type'];
        if (!in_array($content_selection_type, array('auto', 'manual'))) {
            $content_selection_type = 'auto';
        }
        $content_selection = array('type' => $content_selection_type);

        $infoblock->set('source', $source);
        $infoblock->set('content_selection', $content_selection);
    }

}

?>

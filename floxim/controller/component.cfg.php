<?php
$sort_fields = $this
            ->get_component()
            ->all_fields()
            ->find('type', fx_field::FIELD_MULTILINK, '!=')
            ->find('type', fx_field::FIELD_LINK, '!=')
            ->get_values('description', 'name');

$component = $this->get_component();

return array(
    'actions' => array(
        '*list*' => array(
            'settings' => array(
                'limit' => array(
                    'label' => fx::lang('Count entries','controller_component')
                ),
                'pagination' => array(
                    'label' => fx::lang('Show pagination?','controller_component'),
                    'type' => 'checkbox',
                    'parent' => array('limit' => '!=0')
                ),
                'sorting' => array(
                    'name' => 'sorting',
                    'label' => fx::lang('Sorting','controller_component'),
                    'type' => 'select',
                    'values' => $sort_fields
                ),
                'sorting_dir' => array(
                    'name' => 'sorting_dir',
                    'label' => fx::lang('Order','controller_component'),
                    'type' => 'select',
                    'values' => array('asc' => fx::lang('Ascending','controller_component'), 'desc' => fx::lang('Descending','controller_component')),
                    'parent' => array('sorting' => '!=manual')
                )
            )
        ),
        '*list' => array(
            'disabled' => true
        ),
        '*list_infoblock' => array(
            'name' => $component['name'],
            'settings' => array(
                'sorting' => array(
                    'values' => array( array('manual', 'Manual' ) ) + $sort_fields
                ),
                'parent_type' => array(
                    'label' => fx::lang('Parent','controller_component'),
                    'type' => 'select',
                    'values' => array(
                        'current_page_id' => fx::lang('Current page','controller_component'),
                        'mount_page_id' => fx::lang('Infoblock page','controller_component')
                    ),
                    'parent' => array('scope[pages]' => '!=this')
                )
            ),
            'defaults' => array(
                '!pagination' => true
            )
        ),
        '*list_filtered' => array(
            'name' => $component['name'].' by filter',
            'settings' => $this->_config_conditions()
        ),
        '*list_selected' => array(
            'name' => $component['name'].' selected',
            'settings' => array(
                'selected' => array (
                    'name' => 'selected', 
                    'label' => fx::lang('Selected','controller_component'),
                    'type' => 'livesearch',
                    'is_multiple' => true,
                    'ajax_preload' => true,
                    'params' => array(
                        'content_type' => 'content_'.$this->_content_type
                    ),
                ),
                'sorting' => array(
                    'values' => array( array('manual', 'Manual' ) ) + $sort_fields
                ),
            ),
        )
    )
);
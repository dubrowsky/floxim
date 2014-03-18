<?php
$sort_fields = $this
            ->get_component()
            ->all_fields()
            ->find('type', fx_field::FIELD_MULTILINK, '!=')
            ->find('type', fx_field::FIELD_LINK, '!=')
            ->get_values('description', 'name');

$component = $this->get_component();

$content_exists = fx::data('content_'.$component['keyword'])
                            ->where('site_id', fx::env('site')->get('id'))
                            ->one();

$is_new_infoblock = !$this->get_param('infoblock_id');

return array(
    'actions' => array(
        '*.*' => array(
            'icon' => self::_get_abbr($component['name'])
        ),
        '*list*' => array(
            'settings' => array(
                'limit' => array(
                    'label' => fx::alang('Count entries','controller_component')
                ),
                'pagination' => array(
                    'label' => fx::alang('Show pagination?','controller_component'),
                    'type' => 'checkbox',
                    'parent' => array('limit' => '!=0')
                ),
                'sorting' => array(
                    'name' => 'sorting',
                    'label' => fx::alang('Sorting','controller_component'),
                    'type' => 'select',
                    'values' => $sort_fields
                ),
                'sorting_dir' => array(
                    'name' => 'sorting_dir',
                    'label' => fx::alang('Order','controller_component'),
                    'type' => 'select',
                    'values' => array('asc' => fx::alang('Ascending','controller_component'), 'desc' => fx::alang('Descending','controller_component')),
                    'parent' => array('sorting' => '!=manual')
                )
            )
        ),
        '*list' => array(
            'disabled' => true
        ),
        '*list_infoblock' => array(
            'name' => $component['name'],
            // ! APC fatal error occured here sometimes
            'install' => function() {
            	return false;
            },
            'settings' => array(
                'sorting' => array(
                    'values' => array( array('manual', 'Manual' ) ) + $sort_fields
                ),
                'parent_type' => array(
                    'label' => fx::alang('Parent','controller_component'),
                    'type' => 'select',
                    'values' => array(
                        'current_page_id' => fx::alang('Current page','controller_component'),
                        'mount_page_id' => fx::alang('Infoblock page','controller_component')
                    ),
                    'parent' => array('scope[pages]' => '!=this')
                )
            ) + $this->get_target_config_fields(),
            'defaults' => array(
                '!pagination' => true
            )
        ),
        '*list_filtered' => array(
            'name' => $component['name'].' by filter',
            'icon_extra' => 'fil',
            'settings' => $this->_config_conditions()
        ),
        '*list_selected' => array(
            'name' => $component['name'].' selected',
            'icon_extra' => 'sel',
            'settings' => array(
                'selected' => array (
                    'name' => 'selected', 
                    'label' => fx::alang('Selected','controller_component'),
                    'type' => 'livesearch',
                    'is_multiple' => true,
                    'ajax_preload' => true,
                    'params' => array(
                        'content_type' => 'content_'.$this->_content_type
                    ),
                    'value'=> $this->_get_selected_values(),
                ),
                'sorting' => array(
                    'values' => array( array('manual', 'Manual' ) ) + $sort_fields
                ),
            ),
            'defaults' => array(
                '!pagination' => false,
                '!limit' => 0
            )
        ),
        '*list_filtered*, *list_selected*, *listing_by*' => array(
            'check_context' => function() use ($content_exists) {
                return $content_exists;
            }
        ),
        '*listing_by' => array(
            'disabled' => 1
        )
    )
);
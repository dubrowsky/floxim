<?

$source_ibs = fx::data('infoblock')
    ->get_content_infoblocks('section')
    ->find('site_id', fx::env('site')->get('id'))
    ->get_values('name', 'id');
return array(
    'actions' => array(
        '*list*' => array(
            'icon' => 'Nav',
            'defaults' => array(
            	'!parent_type' => 'mount_page_id',
                '!limit' => 0,
                '!create_record_ib' => false,
                '!sorting' => 'manual',
                '!sorting_dir' => 'asc',
                '!pagination' => false
            ),
            'settings' => array(
            	'submenu' => array(
                    'name' => 'submenu',
                    'label' => fx::lang('Subsections','component_section'),
                    'type' => 'select',
                    'values' => array(
                        'none' => fx::lang('Don\'t show','component_section'),
                        'active' => fx::lang('Show for the active item','component_section'),
                        'all' => fx::lang('Show for all items','component_section')
                    )
                ),
            ),
        ),
        '*list_submenu*' => array(
            'name' => 'Submenu',
            'icon_extra' => 'sub',
            'settings' => array(
                'source_infoblock_id' => array(
                    'label' => fx::lang('Source infoblock','component_section'),
                    'type' => 'select',
                    'values' => $source_ibs,
                ),
            ),
        ),
        'breadcrumbs' => array(
            'icon' => 'Nav',
            'icon_extra' => 'bre',
        ),
    )
);
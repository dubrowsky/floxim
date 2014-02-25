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
                    'label' => fx::alang('Subsections','component_section'),
                    'type' => 'select',
                    'values' => array(
                        'none' => fx::alang('Don\'t show','component_section'),
                        'active' => fx::alang('Show for the active item','component_section'),
                        'all' => fx::alang('Show for all items','component_section')
                    )
                ),
            ),
        ),
        '*list_submenu*' => array(
            'name' => 'Submenu',
            'icon_extra' => 'sub',
            'settings' => array(
                'source_infoblock_id' => array(
                    'label' => fx::alang('Source infoblock','component_section'),
                    'type' => 'select',
                    'values' => $source_ibs,
                ),
            ),
            'check_context' => function() {
                return count(fx::env('page')->get_parent_ids()) > 0;
            }
        ),
        'breadcrumbs' => array(
            'icon' => 'Nav',
            'icon_extra' => 'bre',
            'settings' => array(
                'header_only' => array(
                    'name' => 'header_only',
                    'type' => 'checkbox',
                    'label' => fx::alang('Show only header?', 'component_section'),
                ),
                'hide_on_index' => array(
                    'name' => 'hide_on_index',
                    'type' => 'checkbox',
                    'label' => fx::alang('Hide on the index page', 'component_section')
                )
            )
        ),
    )
);
<?
return array(
    'actions' => array(
        '*list*' => array(
            'icon' => 'Nav',
            'defaults' => array(
            	'!parent_type' => 'mount_page_id',
                'limit' => 0,
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
                ),
            ),
        ),
        'breadcrumbs' => array(
            'icon' => 'Nav',
            'icon_extra' => 'bre',
        ),
    )
);
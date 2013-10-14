<?php
return array(
    'actions' => array(
        'list' => array(
            'disabled' => true,
            'settings' => array(
                'limit' => array(
                    'label' => fx::lang('How many entries to display','controller_component')
                ),
                'pagination' => array(
                    'label' => fx::lang('Show pagination?','controller_component'),
                    'type' => 'checkbox',
                    'parent' => array('limit' => '!=0')
                ),
                'sorting' => array(
                    'label' => fx::lang('Sorting','controller_component'),
                    'type' => 'select'
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
        'list_infoblock' => array(
            'settings' => array(
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
            ),
            'name' => 'List',
            'description' => 'List of entries'
        ),/*
        'list_infoblock_children' => array(
            'defaults' => array(
                '!parent_type'=>'current_page_id'
            )
        ),*/
        'list_filtered' => array(
            
        )
    )
);
?>
<?
return array(
    'actions' => array(
        'list' => array(
            'defaults' => array(
                'limit' => 10
            )
        ),
        'list_filtered_featured' => array(
        	'defaults' => array(
        		'!conditions' => array(
        			'new_1' => array(
        				'name' => 'on_main',
        				'operator' => '=',
        				'value' => '1'
        			),
        		)
        	)
        )
    )
);
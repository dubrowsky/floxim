<?
return array(
    'actions' => array(
        'list' => array(
            'defaults' => array(
                'limit' => 10
            ),
            'after_insert' => function() {
                return 'olo';
            }
        ),
        'list_filtered_featured' => array(
            'defaults' => array(
                '!conditions' => array(
                    'new_1' => array(
                        'name' => 'on_main',
                        'operator' => '=',
                        'value' => '1'
                    ),
                ),
                '!pagination' => false,
                '!sorting' => 'publish_date',
                '!sorting_dir' => 'desc'
            )
        )
    )
);
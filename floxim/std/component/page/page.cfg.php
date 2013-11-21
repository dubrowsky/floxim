<?
return array(
    'actions' => array(
        '*record' => array(
            'name' => $component['name'].' record',
            'check_context' => function($page) use ($component) {
                //fx::log('checking record context', $page, $component);
                return $page['type'] === $component['keyword'];
            }
        ),
        'record, list*' => array(
            'disabled' => true
        )
    )
);
<?php
return array(
    'actions' => array(
        '*listing_by_tag' => array(
            'name' => $component['name'].' by tag',
            'check_context' => function($page) {
                return $page->is_instanceof('tag');
            }
        )
    )
);
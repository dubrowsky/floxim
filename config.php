<?php
define("FX_JQUERY_PATH", '/floxim/lib/js/jquery-1.9.1.min.js');
define("FX_JQUERY_UI_PATH", '/floxim/lib/js/jquery-ui-1.10.3.custom.min.js');
$config = array(
    'default' =>  array(
        'DB_DSN' => 'mysql:dbname=floxim_loc;host=127.0.0.1',
        'DB_USER' => 'root',
        'DB_PASSWORD' => '',
        'IS_DEV_MODE' => true,
        'COMPILED_TEMPLATES_TTL' => 10
    )
);

return $config['default'];
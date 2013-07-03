<?php

define("FX_JQUERY_PATH", '/floxim/lib/js/jquery-1.9.1.js');
$db_config = array(
    'default' =>  array(
        'DB_DSN' => 'mysql:dbname=floxim;host=localhost',
        'DB_USER' => 'root',
        'DB_PASSWORD' => ''
    )
);

return $db_config['default'];
?>
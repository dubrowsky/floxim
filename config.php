<?php

define("FX_JQUERY_PATH", '/floxim/lib/js/jquery-1.9.1.js');
$db_config = array(
    'default' =>  array(
        'DB_DSN' => 'mysql:dbname=floxim_dev;host=localhost',
        'DB_USER' => 'webmaster',
        'DB_PASSWORD' => 'rikoJetti82'
    )
);

return $db_config['default'];
?>
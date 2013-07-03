<?
define("FX_JQUERY_PATH", '/floxim/lib/js/jquery-1.9.1.js');
$db_config = array(
    'ilya_local' =>  array(
        'DB_DSN' => 'mysql:dbname=floxim;host=localhost',
        'DB_USER' => 'root',
        'DB_PASSWORD' => ''
    ),
    'remote' =>  array(
        'DB_DSN' => 'mysql:dbname=floxim;host=81.177.142.25',
        'DB_USER' => 'floxim',
        'DB_PASSWORD' => 'floxim12345'
    )
);

$config = $db_config['ilya_local'];
return $config;
?>
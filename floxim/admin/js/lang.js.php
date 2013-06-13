<?php
header("Content-type: text/javascript; charset=utf-8;");
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
$lang_file = $_SERVER['DOCUMENT_ROOT'].'/floxim/admin/lang/'.$lang.'.php';
if (!file_exists($lang_file)) {
    die();
}
require_once ($lang_file);
$constants = get_defined_constants();
$res = array();
echo "\$fx_lang = {};\n";
foreach ($constants as $cn => $cv) {
    if (preg_match("~^(FX|NC)_~", $cn)) {
        echo "\$fx_lang.".$cn." = '".$cv."';\n";
    }
}
//echo "<pre>" . htmlspecialchars(print_r($res, 1)) . "</pre>";
?>
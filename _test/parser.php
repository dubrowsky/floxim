<?php
if (!defined('FLOXIM'))
    require_once('../boot.php');


require_once ('../floxim/template/template_attr_parser.php');


$parser = new fx_template_attr_parser();

$string = '<div fx:each="{$items->group(\'publish_date | fx::date : "F Y"\') as $date => $news}" style="display: none;" class="month-container">';

fx::debug($parser->split_string($string));


fx::debug($parser->parse($string));

?>
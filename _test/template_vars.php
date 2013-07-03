<?php
require_once '../boot.php';
$ctr = fx::controller('component_section');
$actions = $ctr->get_actions();
echo fx_debug($actions);
/*
$ctr = fx::controller('component_section.listing');
$tpls = $ctr->get_available_templates('supernova');
echo fen_debug($tpls);
 * 
 */
?>
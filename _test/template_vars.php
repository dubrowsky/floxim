<?php
require_once '../boot.php';
$data = fx::controller('component_section.listing')->process();
$res = fx::template('component_section')->render(
        'listing', 
        array('input' => $data)
);
echo "<pre>" . htmlspecialchars(print_r($res, 1)) . "</pre>";
?>
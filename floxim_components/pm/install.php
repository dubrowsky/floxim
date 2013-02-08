<?php
defined("FLOXIM") || die("Unable to load file.");

if ( $fx_component instanceof fx_component ) {
    $fx_core->set_settings('pm_component_id', $fx_component['id'], 'auth');
}
?>

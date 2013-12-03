<?php
class fx_controller_widget extends fx_controller {
    protected $_action_prefix = 'do_';
    protected function _get_config_sources() {
        $sources = array();
        $c_name = preg_replace("~^widget_~", '', $this->get_controller_name());
        $std_conf = fx::config()->DOCUMENT_ROOT.'/floxim/std/widget/'.$c_name."/".$c_name.'.cfg.php';
        $custom_conf = fx::config()->DOCUMENT_ROOT.'/widget/'.$c_name."/".$c_name.'.cfg.php';
        if (file_exists($std_conf)) {
            $sources []= $std_conf;
        }
        if (file_exists($custom_conf)) {
            $sources []= $custom_conf;
        }
        return $sources;
    }
}
?>
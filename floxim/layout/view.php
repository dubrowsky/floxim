<?php

class fx_layout_view implements fx_layout {

    public function place_infoblock($keyword, $params) {
        $ib = $this->get_unit_obj('infoblock');
        $ib_result = $ib->show($keyword, $params);
        dev_log('placing ib', $keyword, $params, $ib);
        return $ib_result;
    }

    public function place_infoblock_simple($keyword, $params) {
        $unit_obj = $this->get_unit_obj('infoblock_simple');
        return $unit_obj->show($keyword, $params);
    }

    public function place_menu($keyword, $params, $template = array()) {
        return $this->get_unit_obj('menu')->show($keyword, $params, $template);
    }

    protected function get_unit_obj($type) {
        if (!$this->units[$type]) {
            $classname = $this->get_unit_classname($type);
            $this->units[$type] = new $classname();
        }

        return $this->units[$type];
    }

    protected function get_unit_classname($type) {
        return 'fx_unit_'.$type;
    }
}
?>
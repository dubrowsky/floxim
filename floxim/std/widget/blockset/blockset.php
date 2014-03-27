<?php
class fx_controller_widget_blockset extends fx_controller_widget {
    
    public function do_show() {
        $area_name = 'blockset_'.$this->input['infoblock_id'];
        $blocks = fx::page()->get_area_infoblocks($area_name);
        $res = parent::do_show();
        $res += array('items' => $blocks, 'area' => $area_name);
        //fx::log($res);
        return $res;
    }
}
<?php
class fx_infoblock_visual extends fx_essence {
    protected  function _before_save() {
        parent::_before_save();
        unset($this['is_stub']);
        if (!$this['priority'] && $this['layout_id']) {
            //fx::debug('no pr', $this);
            //$ib = fx::data('infoblock', $this['infoblock_id']);
            $last_vis = fx::data('infoblock_visual')
                            ->where('layout_id', $this['layout_id'])
                            ->where('area', $this['area'])
                            ->order(null)
                            ->order('priority', 'desc')
                            ->one();
            //fx::debug($last_vis);
            //$max = fx::db()->get_col('selec')
            $this['priority'] = $last_vis['priority']+1;
            //fx::debug($this);
        }
        //die();
    }
}
?>
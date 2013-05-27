<?php
class fx_template_suitable {
    public function suit($infoblocks, $layout_id) {
        echo fen_debug('let it suit!', $infoblocks);
        
        $layout_ib = null;
        $stub_ibs = new fx_collection();
        // Собираем все инфоблоки без визуальной части
        // И находим инфоблок-лейаут
        foreach ($infoblocks as $ib) {
            if ($ib->get_visual()->get('is_stub')) {
                $stub_ibs[]= $ib;
            }
            if ($ib->get_prop_inherited('controller') == 'layout') {
                $layout_ib = $ib;
            }
        }
        $all_visual = fx::data('infoblock_visual')->get_for_infoblocks($stub_ibs, false);
        echo fen_debug($all_visual);
        
        $layout = fx::data('layout', $layout_id);
        echo fen_debug($layout);
        $layout_tpl = fx::template('layout_'.$layout['keyword']);
        echo fen_debug($layout_tpl);
        foreach ($layout_tpl->get_template_variants() as $tplv) {
            if ($tplv['for'] == 'layout.show') {
                fx::listen('render_area.suitable', function($e) use (&$tplv) {
                    if (!isset($tplv['real_areas'])) {
                        $tplv['real_areas'] = array();
                    }
                    $tplv['real_areas'][]= $e->area;
                });
                $test_layout_tpl = fx::template('layout_'.$layout['keyword'].'.'.$tplv['id']);
                $test_layout_tpl->render();
                fx::unlisten('render_area.suitable');
                echo fen_debug($tplv['id'], $tplv);
            }
        }
        
        /*
        $layout_ib = $infoblocks->find_one( function($ib) {
            return $ib->get_prop_inherited('controller') == 'layout';
        });
        //echo fen_debug($layout_ib);
        if ($layout_ib->get_visual()->get('is_stub')) {
            echo fen_debug($layout_ib, 'it is stub');
        }
         * 
         */
        
    }
}
?>
<?php
class fx_template_suitable {
    
    public function unsuit($layout_id) {
        fx::data('infoblock_visual')->where('layout_id', $layout_id)->all()->apply(function($v) {
            $v->delete();
        });
    }
    
    public function suit(fx_collection $infoblocks, $layout_id) {
        //echo fen_debug('let it suit!', $infoblocks);
        
        $layout = fx::data('layout', $layout_id);
        $layout_ib = null;
        $stub_ibs = new fx_collection();
        // Collect all Infoblox without the visual part
        // Find the InfoBlock-layout
        foreach ($infoblocks as $ib) {
            if ($ib->get_visual()->get('is_stub')) {
                $stub_ibs[]= $ib;
            }
            if ($ib->get_prop_inherited('controller') == 'layout') {
                $layout_ib = $ib;
            }
        }
        $layout_rate = array();
        $all_visual = fx::data('infoblock_visual')->get_for_infoblocks($stub_ibs, false);
        //$infoblocks->attache_many($all_visual, 'infoblock_id', 'all_visual');
        foreach ($all_visual as $c_vis) {
            $c_layout_id = $c_vis['layout_id'];
            $infoblocks->
                    find_one('id', $c_vis['infoblock_id'])->
                    set_visual($c_vis, $c_layout_id);
            if (!isset($layout_rate[$c_layout_id])) {
                $layout_rate[$c_layout_id] = 0;
            }
            $layout_rate[$c_layout_id]++;
        }
        
        $source_layout_id = $c_layout_id;
        //fx::debug($layout_ib);
        if ($layout_ib->get_visual()->get('is_stub')) {
            $this->_adjust_layout_visual($layout_ib, $layout_id, $source_layout_id);
        }
        $layout_visual = $layout_ib->get_visual();
        $area_map = $layout_visual['area_map'];
        
        $c_areas = fx::template($layout_ib->get_prop_inherited('visual.template'))->get_areas();
        
        foreach ($infoblocks as $ib) {
            $ib_visual = $ib->get_visual($layout_id);
            if (!$ib_visual['is_stub'] ) {
                continue;
            }
            //$old_visual = $ib['all_visual'][0];
            $old_area = $ib->get_prop_inherited('visual.area', $source_layout_id);
            if ($old_area && isset($area_map[$old_area])) {
                $ib_visual['area'] = $area_map[$old_area];
                $ib_visual['priority'] = $ib->get_prop_inherited('visual.priority', $source_layout_id);
                //$old_visual['priority'];
            } else {
                //fx::debug($c_areas);
                //die();
            }
            $ib_controller = fx::controller(
                    $ib->get_prop_inherited('controller'),
                    $ib->get_prop_inherited('params'),
                    $ib->get_prop_inherited('action')
            );
            $controller_templates = $ib_controller->get_available_templates($layout['keyword']);
            $old_template = $ib->get_prop_inherited('visual.template', $source_layout_id);
            $used_template_props = null;
            foreach ($controller_templates as $c_tpl) {
                if ($c_tpl['full_id'] == $old_template) {
                    $ib_visual['template'] = $c_tpl['full_id'];
                    $used_template_props = $c_tpl;
                    break;
                }
            }
            if (!isset($ib_visual['template'])) {
                $ib_visual['template'] = $controller_templates[0]['full_id'];
                $used_template_props = $controller_templates[0];
            }
            
            if (!$ib_visual['area']) {
                //fx::debug($used_template_props);
                $block_size = self::get_size( $used_template_props['size']);
                //fx::debug($c_areas);
                foreach ($c_areas as $ca) {
                    $area_size = self::get_size($ca['size']);
                    //fx::debug($block_size, $area_size);
                    if (self::check_sizes($block_size, $area_size)) {
                        $ib_visual['area'] = $ca['id'];
                        break;
                    }
                }
            }
            
            unset($ib_visual['is_stub']);
            //fx::debug('ibv red', $ib_visual);
            //die();
            $ib_visual->save();
        }
    }
    
    protected function _adjust_layout_visual($layout_ib, $layout_id, $source_layout_id) {
        $layout = fx::data('layout', $layout_id);
        
        
        $layout_tpl = fx::template('layout_'.$layout['keyword']);
        $template_variants = $layout_tpl->get_template_variants();
        
        if ($source_layout_id) {
            $source_template = $layout_ib->get_prop_inherited('visual.template', $source_layout_id);
            $old_areas = fx::template($source_template)->get_areas();
            $c_relevance = 0;
            $c_variant = null;
            foreach ($template_variants as $tplv) {
                if ($tplv['of'] == 'layout.show') {
                    $test_tpl_name = 'layout_'.$layout['keyword'].'.'.$tplv['id'];
                    $test_layout_tpl = fx::template($test_tpl_name);
                    $tplv['real_areas'] = $test_layout_tpl->get_areas();
                    if ( !($map = $this->_map_areas($old_areas, $tplv['real_areas'])) ) {
                        continue;
                    }
                    if ($map['relevance'] > $c_relevance) {
                        $c_relevance = $map['relevance'];
                        $c_variant = $map + array(
                            'full_template_id' => $test_tpl_name,
                            'areas' => $tplv['real_areas']
                        );
                    }
                }
            }
        }
        
        if (!$source_layout_id || !$c_variant) {
            foreach ($template_variants as $tplv) {
                if ($tplv['of'] == 'layout.show') {
                    $layout_vis = $layout_ib->get_visual();
                    $layout_vis['template'] = $tplv['full_id'];
                    unset($layout_vis['is_stub']);
                    $layout_vis->save();
                    return;
                }
            }
            echo fx_debug($template_variants, $source_layout_id);
            die();
        }
        
        $layout_vis = $layout_ib->get_visual();
        $layout_vis['template'] = $c_variant['full_template_id'];
        $layout_vis['areas'] = $c_variant['areas'];
        $layout_vis['area_map'] = $c_variant['map'];
        unset($layout_vis['is_stub']);
        $layout_vis->save();
        //echo "<pre>" . htmlspecialchars(print_r($c_variant, 1)) . "</pre>";
    }
    
    /*
     * Compares two sets of fields
     * Considers the relevance of size, title and employment
     * Returns an array with the keys in the map and relevance
     */
    protected function _map_areas($old_set, $new_set) {
        $total_relevance = 0;
        foreach ($old_set as &$old_area) {
            $old_size = $this->_get_size($old_area);
            $c_match = false;
            $c_match_index = 1;
            foreach ($new_set as $new_area_id => $new_area) {
                $new_size = $this->_get_size($new_area);
                $area_match = 0;
                
                // if one of the areas arbitrary width - existent, 1
                if ($new_size['width'] == 'any' || $old_size['width'] == 'any') {
                    $area_match += 1;
                } 
                // if the width is the same as - good, 2
                elseif ($new_size['width'] == $old_size['width']) {
                    $area_match += 2;
                } 
                // if no width is matched, no good
                else {
                    continue;
                }
                
                // if one of the areas of arbitrary height - existent, 1
                if ($new_size['height'] == 'any' || $old_size['height'] == 'any') {
                    $area_match += 1;
                } 
                // if the height voityla - good, 2
                elseif ($new_size['height'] == $old_size['height']) {
                    $area_match += 2;
                } 
                // new area - high, old - low, you can replace, 1
                elseif ($new_size['height'] == 'high') {
                    $area_match += 1;
                } 
                // a new low, old - high, no good
                else {
                    continue;
                }
                // if the names coincide areas: 2
                if ($old_area['id'] == $new_area['id']) {
                    $area_match += 2;
                }
                
                // if the field is already another: -2
                if ($new_area['used']) {
                    $area_match -= 2;
                }
                
                // if the current index is larger than the previous - remember
                if ($area_match > $c_match_index) {
                    $c_match = $new_area_id;
                    $c_match_index = $area_match;
                }
            }
            if ($c_match_index == 0) {
                return false;
            }
            $old_area['analog'] = $c_match;
            $old_area['relevance'] = $c_match_index;
            $new_set[$c_match]['used'] = true;
            $total_relevance += $c_match_index;
        }
        // for each unused lower the score 2
        foreach ($new_set as $new_area) {
            if (!isset($new_area['used'])) {
                $total_relevance -= 2;
            }
        }
        $map = array();
        foreach ($old_set as $old_area) {
            $map[$old_area['id']] = $old_area['analog'];
        }
        return array('relevance' => $total_relevance, 'map' => $map);
    }
    
    public static function get_size($size) {
        $res = array('width' => 'any', 'height' => 'any');
        if (empty($size)) {
            return $res;
        }
        $width = null;
        $height = null;
        if (preg_match('~wide|narrow~', $size, $width)) {
            $res['width'] = $width[0];
        }
        if (preg_match('~high|low~', $size, $height)) {
            $res['height'] = $height[0];
        }
        return $res;
    }
    
    public static function check_sizes($block, $area) {
        if ($area['width'] === 'narrow' && $block['width'] ==='wide') {
            return false;
        }
        if ($area['height'] === 'low' && $block['height'] === 'high') {
            return false;
        }
        return true;
    }
    
    protected function _get_size($block) {
        $res = array('width' => 'any', 'height' => 'any');
        if (!isset($block['size'])) {
            return $res;
        }
        if (preg_match('~wide|narrow~', $block['size'], $width)) {
            $res['width'] = $width[0];
        }
        if (preg_match('~high|low~', $block['size'], $height)) {
            $res['height'] = $height[0];
        }
        return $res;
    }
}

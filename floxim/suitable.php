<?php

/**
 * Класс для поиска/проверки совместимости инфоблоков и шаблонов/виджетов 
 */
class fx_suitable {

    protected $template_id;
    protected $infoblocks = array(), $menu = array();
    protected $site;

    static public function is_suitable($target = 'large', $source = 'narrow-wide') {
        if (is_array($target)) {
            $target = $target['embed'];
        }
        if (!$target) {
            $target = 'large';
        }

        if (is_object($source) || is_array($source)) {
            $source = $source['embed'];
        }
        if (!$source) {
            $target = 'large';
        }

        $suitables = array();
        $suitables['miniblock'] = array('miniblock');
        $suitables['vertical'] = array('miniblock', 'narrow', 'narrow-wide');
        $suitables['large'] = array('wide', 'narrow-wide');

        return in_array($source, $suitables[$target]);
    }

    protected function already_applied($site_id, $template_id) {
        //пока без магии
        return false;
        $ex_infoblock = fx::data('infoblock')->get('site_id', $site_id, 'template_id', $template_id);
        $ex_menu = fx::data('menu')->get('site_id', $site_id, 'template_id', $template_id);

        return (bool) $ex_infoblock || (bool) $ex_menu;
    }

    public function apply_design(fx_site $site, $template_id) {
        $fx_core = fx_core::get_object();
        $this->template_id = intval($template_id);

        if ($this->already_applied($site['id'], $this->template_id)) {
        	dev_log('combi found', $site['id'], $this->template_id);
            return false;
        }
        $this->site = $site;

        $configure = new fx_admin_configure();
        //$reqs = $configure->get_requirements($site['id']);
        $reqs = array(); // !!! stub
        

        foreach ($reqs as $page) {
            foreach ($page['units'] as $unit) {
				dev_log('doing ', $unit);
                if ($unit['type'] == 'infoblock') {
                    $id = $unit['id'];
                    if (!isset($this->infoblocks[$id])) {
                        $data = fx::data('infoblock')->get_by_id($id)->get();
                        unset($data['id'], $data['keyword']);
                        $data['template_id'] = $template_id;
                        $data['__embed'] = $unit['embed'];
                        $data['__pages'] = array();
                        $this->infoblocks[$unit['id']] = fx::data('infoblock')->create($data);
                        dev_log('created', $data, $this->infoblocks);
                    }
                    $pages = array_merge($this->infoblocks[$id]['__pages'], array($page['type']));
                    $this->infoblocks[$id]['__pages'] = $pages;
                    dev_log($this->infoblocks);
                } else if ($unit['type'] == 'menu') {
                    $keyword = $unit['keyword'];
                    if (!isset($this->menu[$keyword])) {
                        $this->menu[$keyword] = $unit;
                        if ($unit['id']) {
                            $data = fx::data('menu')->get_by_id($unit['id'])->get();
                            unset($data['id'], $data['keyword']);
                            $this->menu[$keyword]['obj'] = fx::data('menu')->create($data);
                        }
                    }

                    $this->menu[$keyword]['pages'][] = $page['type'];
                    $this->menu[$keyword]['pages'] = array_unique($this->menu[$keyword]['pages']);
                }
            }
        }

        $this->place_infoblock();
        foreach ($this->infoblocks as &$infoblock) {
            $infoblock->save();
        }

        $this->place_menu();
        foreach ($this->menu as &$menu) {
            if ($menu['obj'] && $menu['obj']['keyword']) {
                $menu['obj']->save();
            }
        }
    }

    protected function place_infoblock() {
        $all_targets = $this->get_targets_for_infoblock();

        /**
         * Сначала размещаются миниблоки и узкие ($i = 0),
         * затем - все остальные ( $i = 1 ) 
         */
        for ($i = 0; $i <= 1; $i++) {
            foreach ($this->infoblocks as $k => $infoblock) {
                if ($infoblock['__parent_uuid'] || !$infoblock['__embed']) {
                    continue;
                }
                $best_keyword = '';
                $best_embed = '';
                $best_repeat = 0;
                $embed = $infoblock['__embed'];

                foreach ($all_targets as $keyword => $target) {
                    if (!$target['max_repeat']) {
                        continue;
                    }

                    if (array_diff($infoblock['__pages'], $target['pages'])) {
                        continue;
                    }

                    $t_embed = $target['embed'];

                    if ($i == 0) {
                        $cond1 = ($embed == 'miniblock' && $t_embed == 'miniblock');
                        $cond2 = ( $embed == 'miniblock' && $t_embed == 'vertical' && $best_embed != 'miniblock' );
                        $cond3 = ( $embed == 'narrow' && $t_embed == 'vertical' );
                        $cond = ($cond1 || $cond2 || $cond3);
                    } else {
                        $cond1 = ( $embed == 'narrow-wide' && $t_embed == 'vertical' );
                        $cond2 = ( $embed == 'narrow-wide' && $t_embed == 'large' && $best_embed != 'vertical' );
                        $cond3 = ( $embed == 'wide' && $t_embed == 'large' );
                        $cond = ($cond1 || $cond2 || $cond3);
                    }
                    if ($cond && ( $target['max_repeat'] > $best_repeat )) {
                        $best_keyword = $keyword;
                        $best_embed = $t_embed;
                        $best_repeat = $target['max_repeat'];
                    }
                }

                if ($best_keyword) {
                    $this->infoblocks[$k]['keyword'] = $best_keyword;
                    $this->infoblocks[$k]['template_id'] = $this->template_id;
                    unset($this->infoblocks[$k]['__pages'], $this->infoblocks[$k]['__embed']);
                }
            }
        }

        foreach ($this->infoblocks as $k => $infoblock) {
            if ($infoblock['__embed']) {
                unset($this->infoblocks[$k]);
            }
        }
    }

    protected function place_menu() {
        $fx_core = fx_core::get_object();
        $all_targets = $this->get_targets_for_menu();

        foreach ($this->menu as &$menu) {
            foreach ($all_targets as $keyword => $target) {
                if ($target['direct'] != $menu['direct']) {
                    continue;
                }
                if (array_diff($menu['pages'], $target['pages'])) {
                    continue;
                }

                unset($all_targets[$keyword]);
                if ($menu['obj']) {
                    $menu['obj']->set('keyword', $keyword);
                    $menu['obj']->set('template_id', $this->template_id);
                }
            }
        }

        foreach ($all_targets as $keyword => $target) {
            $menu = fx::data('menu')->create();
            $menu['keyword'] = $keyword;
            $menu['deleted'] = 1;
            $menu['template_id'] = $this->template_id;
            $menu['site_id'] = $this->site['id'];
        }
    }

    protected function get_targets_for_infoblock() {
        $all_targets = array();
        $layouts = $this->get_all_layouts();
        foreach ($layouts as $layout) {
            $units = $layout->get_units();
            if ($units['infoblock']) {
                foreach ($units['infoblock'] as $unit_keyword => $unit) {
                    if ($unit['main']) {
                        continue;
                    }
                    $all_targets[$unit_keyword]['pages'][] = $layout['type'];
                    $all_targets[$unit_keyword]['embed'] = $unit['embed'];
                    $all_targets[$unit_keyword]['max_repeat'] = $unit['max_repeat'] ? $unit['max_repeat'] : 100;
                }
            }
        }

        return $all_targets;
    }

    protected function get_targets_for_menu() {
        $all_targets = array();
        $layouts = $this->get_all_layouts();
        foreach ($layouts as $layout) {
            $units = $layout->get_units();
            if ($units['menu']) {
                foreach ($units['menu'] as $unit_keyword => $unit) {
                    if ($unit['type'] == 'path' || $unit['type'] == 'force') {
                        continue;
                    }
                    if ($unit['necessary']) {
                        $all_targets[$unit_keyword]['necessary'] = true;
                    }
                    if ($unit['kind']) {
                        $all_targets[$unit_keyword]['kind'] = $unit['kind'];
                    }
                    if ($unit['direct']) {
                        $all_targets[$unit_keyword]['direct'] = $unit['direct'];
                    }
                    $all_targets[$unit_keyword]['pages'][] = $layout['type'];
                }
            }
        }

        return $all_targets;
    }

    protected function get_all_layouts() {
        $template = fx::data('template')->get_by_id($this->template_id);
        $layouts = $template->get_layouts();
        return $layouts;
    }
}
?>
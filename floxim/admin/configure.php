<?php

class fx_admin_configure {

    public function get_requirements($site_id) {
        $site = fx::data('site')->get_by_id($site_id);
        $title_sub_id = $site['title_sub_id'];

        $subdivisions = fx::data('subdivision')->get_all('site_id', $site_id);
        $infoblocks = fx::data('infoblock')->get_all('checked', 1, 'parent_id', 0, 'main_content', 0, 'site_id', $site['id'], 'template_id', $site['template_id']);
        $all_menu = fx::data('menu')->get_all();

        $pages = array();
        $found_no_own_sub = false;
        foreach ($subdivisions as &$subdivision) {
            $id = $subdivision['id'];
            $own_design = $subdivision['own_design'];
            $is_title = ($title_sub_id == $id); 

            if (!($is_title || $own_design || !$found_no_own_sub)) {
                continue;
            }

            if (!$own_design && !$is_title ) {
                $found_no_own_sub = true;
            }

            $page = array();
            $page['type'] = $is_title ? 'index' : 'inner';

            $layout = $this->get_layout_by_subdivision($subdivision);
            
            if (!$layout) {
            	continue;
            }
            
            $layout_units = $layout->get_units();

            foreach ($infoblocks as $infoblock) {
                if (!$infoblock['essence_id']) {
                    continue;
                }

                if (!isset($layout_units['infoblock'][$infoblock['keyword']])) {
                    continue;
                }

                $cond_own_design = ($own_design && $infoblock['individual'] && $infoblock['subdivision_id'] == $id);
                $cond_no_own_design = (!$own_design && !$infoblock['individual'] );

                if ($cond_own_design || $cond_no_own_design) {
                    $embed = false;
                    if ($infoblock['type'] == 'widget') {
                        $embed = fx::data('widget')->get_by_id($infoblock['essence_id'])->get('embed');
                    } else if ($infoblock['list_ctpl_id']) {
                        $embed = fx::data('ctpl')->get_by_id($infoblock['list_ctpl_id'])->get('embed');
                    }
                    if ($embed) {
                        $page['units'][] = array('type' => 'infoblock', 'embed' => $embed, 'nessesary' => 'yes', 'id' => $infoblock['id']);
                    }
                }
            }

            if ($layout_units['menu']) {
                foreach ($layout_units['menu'] as $menu) {
                    if ($menu['type'] == 'path') continue;
                    $param = array('type' => 'menu');
                    if ($menu['direct']) $param['direct'] = $menu['direct'];
                    if ($menu['necessary'])
                            $param['necessary'] = $menu['necessary'];

                    foreach ($all_menu as $one_menu) {
                        if ($menu['keyword'] == $one_menu['keyword']) {
                            $param['id'] = $one_menu['id'];
                        }
                    }
                    $param['keyword'] = $menu['keyword'];
                    $page['units'][] = $param;
                }
            }

            $pages[] = $page;
        }

        return $pages;
    }

    /**
     * @todo: объединить с fx_controller_page::load_tpl
     * @return fx_template 
     */
    protected function get_layout_by_subdivision(fx_subdivision $subdivision) {

        $template_id = $subdivision->get_data_inherit('template_id');
        $template = fx::data('template')->get_by_id($template_id);

        if (!$template['parent_id']) {
            $site = $subdivision->get_site();
            $index = $subdivision['id'] == $site->get_title_sub_id();
            $type = $index ? 'index' : 'inner';
            $template = fx::data('template')->get('parent_id', $site['template_id'], 'type', $type);
        }
        return $template;
    }

}

?>

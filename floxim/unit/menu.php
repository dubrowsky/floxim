<?php

/**
 * @todo если меню зависимое, то оно становится меню первого уровня
 * а надо выводить меню в зависимости от parent 
 */
class fx_unit_menu {

    public function show($keyword, $params, $template = array()) {
        $fx_core = fx_core::get_object();
        $site = $fx_core->env->get_site();

        $params = unserialize($params);
        $template = unserialize($template);
        $kind = $params['kind'] ? $params['kind'] : fx_menu::KIND_INDEPENDENT;
        
        $preview_template_id = $fx_core->input->get_service_session('preview_template_id');
        if ($preview_template_id) {
            $template_id = $preview_template_id;
        } else {
            $template_id = $site['template_id'];
        }

        $user_menu = fx::data('menu')->get('keyword', $keyword, 'site_id', $site['id'], 'template_id', $template_id);
        if ( $kind == fx_menu::KIND_SECONDARY && !$user_menu) {
            $type = 'manual';
        } else if ( $user_menu['deleted'] ) {
            $type = 'empty';
        } else if ($user_menu['type']) {
            $type = $user_menu['type'];
        } else if ($params['type']) {
            $type = $params['type'];
        } else if ($params['kind'] == fx_menu::KIND_DEPENDENT || $params['kind'] == fx_menu::KIND_BOTH) {
            $type = 'level';
            $params['level'] = 1;
        } else {
            $type = 'level';
        }
        
        

        $settings = $user_menu['settings'] ? $user_menu['settings'] : array();
        if ($params)
                foreach ($params as $k => $v) {
                if (!isset($settings[$k])) {
                    $settings[$k] = $v;
                }
            }
            
        $settings['deleted'] = (bool)$user_menu['deleted'];
        $settings['keyword'] = $keyword;
        $settings['id'] = $user_menu['id'];
        $settings['kind'] = $params['kind'];

        return $this->get_menu_obj($type, $settings, $template)->show();
    }

    protected function get_menu_obj($type, $settings, $template) {
        $classname = 'fx_menu_'.$type;
        return new $classname($settings, $template);
    }

}

?>

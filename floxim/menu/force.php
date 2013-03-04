<?php

/**
 * Класс для работы и вывода "принудительного" меню подразделов текущего раздела,
 * если они нигде не выводятся
 */
class fx_menu_force extends fx_menu {

    static protected $shown_subs = array();
    static protected $def_template = array(0 => array('prefix' => '<ul class="fx_force_menu">', 'unactive' => '<li><a href="%url%">%name%</a></li>', 'suffix' => '</ul>'));


     static public function add_shown_sub($sub) {
        if ($sub instanceof fx_subdivision) {
            self::$shown_subs[] = intval($sub['id']);
        }
    }
    
    static public function place_menu() {
        return fx_core::get_object()->page->set_macroconst('force_menu', '');
    }
    
    public function __construct($params = '', $template = array()) {
        self::$def_template = $template;
    }

    public function show () {
        return '';
    }
   

    static public function attempt_to_show(fx_subdivision $current_sub) {
        $fx_core = fx_core::get_object();
        $is_admin = ($fx_core->env->get_user() && $fx_core->env->get_user()->perm()->is_supervisor());
        $sub_pages = fx::data('subdivision')->get_all('parent_id', $current_sub['id'], 'checked', 1);
        $pages_id = array();
        foreach ($sub_pages as $sub) {
            $pages_id[] = $sub['id'];
        }
        $shown = array_unique(self::$shown_subs);
        if (!$pages_id || count(array_intersect($shown, $pages_id)) == count($pages_id)) {
            return false;
        }
        
        $force_menu = $current_sub['force_menu'];
        if ( !$is_admin && !$force_menu  ) {
            return false;
        }

        $sub_menu = new fx_menu_sub('sub='.$current_sub['id'], self::$def_template);
        $content = $sub_menu->show();
        
        if ( $is_admin ) {
            $hash = $fx_core->page->add_block('essence=menu&subdivision_id='.$current_sub['id'].'&force_menu=1', 'on,off', 'edit');
            $hash .= $force_menu ? '' : ' fx_page_block fx_admin_unchecked fx_admin_unchecked_edit';
            $content = '<div class="'.$hash.'">'.$content.'</div>';
        }
        
        
        $fx_core->page->set_macroconst('force_menu',$content );
    }

}
?>

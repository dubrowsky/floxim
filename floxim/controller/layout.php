<?php
class fx_controller_layout extends fx_controller {
    
    public function default_action() {
        if (! ($page_id = $this->param('page_id')) ) {
            $page_id = fx::env('page');
            $this->input['page_id'] = $page_id;
        }
        if (! ($layout_id = $this->param('layout_id'))) {
            $layout_id = fx::env('layout');
        }
        $page_infoblocks = fx::router('front')->get_page_infoblocks(
                $page_id, 
                $layout_id
        );
        //dev_log('ibs to rnd', $page_infoblocks);
        return $page_infoblocks;
    }
    
    public function postprocess($html) {
        if ($this->param('ajax_mode')) {
            $html = preg_replace("~^.+?<body[^>]*?>~is", '', $html);
            $html = preg_replace("~</body>.+?$~is", '', $html);
        } else {
            $page = fx::data('content_page', $this->param('page_id'));
            $meta_title = empty($page['title']) ? $page['name'] : $page['title'];
            $this->_show_admin_panel();
            $html = fx::page()->set_metatags('title',$meta_title)
                                ->set_metatags('description',$page['description'])
                                ->set_metatags('keywords',$page['keywords'])
                                ->post_proccess($html);
        }
        return $html;
    }
    
    protected $_layout = null;


    protected function _get_layout() {
        if ($this->_layout) {
            return $this->_layout;
        }
        $page = fx::data('content_page', $this->param('page_id'));
        if ($page['layout_id']) {
            $layout_id = $page['layout_id'];
        } else {
            $site = fx::data('site', $page['site_id']);
            $layout_id = $site['layout_id'];
        }
        $this->_layout = fx::data('layout', $layout_id);
        return $this->_layout;
    }
    
    public function find_template() {
        $layout = $this->_get_layout();
        $tpl_name = 'layout_'.$layout['keyword'];
        return fx::template($tpl_name);
    }
    
    protected function _show_admin_panel() {
        /*
        if (!$user = fx::env()->get_user()) {
            return;
        }
        if (!$user->perm()->is_supervisor()) {
            return;
        }*/
        if (!fx::env('is_admin')) {
            return;
        }
        // инициализация админ панели
        //fx::page()->add_data_js('sub', $fx_core->env->get_sub()->get('id'));
        //$fx_core->page->add_data_js('url', $_SERVER['REQUEST_URI']);

        $js_config = new fx_admin_configjs();
        $js_config->add_main_menu(fx_controller_admin_adminpanel::get_main_menu());
        $js_config->add_more_menu(fx_controller_admin_adminpanel::get_more_menu());
        $js_config->add_buttons(fx_controller_admin_adminpanel::get_buttons());
        
        /*if ($text) {
            $js_config->add_additional_text($text);
        }*/
        
        $p = fx::page();
        $p->add_js_text("fx_adminpanel.init(".$js_config->get_config().");");
        
        $p->add_js_file('/floxim/lib/js/jquery-1.7.1.js');
        $p->add_js_file('/floxim/lib/js/jquery-ui-1.8.21.custom.js');
        $p->add_js_file('/floxim/lib/js/jquery.nestedSortable.js');
        $p->add_js_file('/floxim/lib/js/jquery.ba-hashchange.min.js');
        $p->add_js_file('/floxim/lib/js/ajaxfileupload.js'); // ajax upload files
        $p->add_js_file('/floxim/lib/js/jquery.json-2.3.js');
        $p->add_js_file('/floxim/admin/js-templates/jstx.js');
        $p->add_js_file('/floxim/admin/js-templates/compile.php');
        $p->add_js_file('/floxim/admin/js/lib.js');
        $p->add_js_file('/floxim/admin/js/adminpanel.js');
        $p->add_js_file('/floxim/admin/js/front.js');
        $p->add_js_file('/floxim/admin/js/buttons.js');
        $p->add_js_file('/floxim/admin/js/form.js');
        $p->add_js_file('/floxim/admin/js/dialog.js');
        $p->add_js_file('/floxim/admin/js/fields.js');
        $p->add_js_file('/floxim/admin/js/edit-in-place.js');
        $p->add_js_file('/floxim/admin/js/store.js');
        $p->add_js_file('/floxim/admin/js/dialog_file.js');
        $p->add_js_file('/floxim/admin/js/admin.js');
        $p->add_js_file('/floxim/admin/js/sort.js');
        $p->add_js_file('/floxim/admin/js/menu/main.js');
        $p->add_js_file('/floxim/admin/js/menu/more.js');
        $p->add_js_file('/floxim/admin/js/menu/additional.js');
        $p->add_js_file('/floxim/admin/js/menu/mode.js');
        $p->add_js_file('/floxim/admin/js/menu/submenu.js');
        $p->add_js_file('/floxim/admin/js/menu/breadcrumb.js');
        $p->add_js_file('/floxim/lib/editors/elrte/elrte.full.js');
        $p->add_js_file('/floxim/lib/editors/elrte/i18n/elrte.ru.js');
        $p->add_js_file('/floxim/lib/js/jquery.form.js');
        $p->add_js_file('/floxim/lib/js/jquery.jstree.js');
        $p->add_js_file('/floxim/lib/js/jquery-gp-gallery.js');
        $p->add_js_file('/floxim/lib/js/jquery.tipTip.minified.js');
        $p->add_js_file('/floxim/lib/js/jquery-ui-timepicker-addon.js');
        $p->add_css_file('/floxim/lib/css/elrte/elrte.min.css');
        $p->add_css_file('/floxim/admin/skins/default/jquery-ui/main.css');
        $p->add_css_file('/floxim/admin/skins/default/css/main.css');
        $p->set_after_body(fx_controller_admin_adminpanel::panel_html());        
    }

    protected $save_history = false;
    
    protected function get_default_metatags(fx_subdivision $subdivision) {
        $divider = ' - ';
        if ($subdivision['seo_title']) {
            $title = $subdivision['seo_title'];
        } else {
            $title = $subdivision['name'];
            foreach ($subdivision->get_parents() as $parent) {
                $title .= $divider.$parent['name'];
            }
            $title .= $divider.$subdivision->get_site()->get('name');
        }

        return $title;
    }

    // Возвращает экземпляр класса-шаблона
    // Если файл с компилированным шаблоном отсутствует,
    // или его дата модификации меньше, чем у исходника,
    // перекомпилирует шаблон заново
    protected function load_layout(fx_template $template) {
    	$fx_core = fx_core::get_object();

    	$tpl_file = $fx_core->files->get_full_path($template->get_path_php());
    	$tpl_source_file = $fx_core->files->get_full_path($template->get_path_html());

    	$recompilation_needed = false;


    	if (!file_exists($tpl_file)) {
    		$recompilation_needed = true;
    	} elseif (filemtime($tpl_file) < filemtime($tpl_source_file)) {
    		$recompilation_needed = true;
    	}

    	if ($recompilation_needed) {
    		$source_html = $fx_core->files->readfile($tpl_source_file);
    		fx_controller_admin_layout::compile($template, $source_html, false);
    	}

    	$parent = $template->get_parent();
    	$classname = 'template__'.$parent['keyword'].'__'.$template['keyword'];

    	require_once($tpl_file);
    	return new $classname();
    }

    protected function load_tpl($template_id) {
        $fx_core = fx_core::get_object();
        $template = fx::data('template')->get_by_id($template_id);

        if (!$template) {
        	die("NO TPL: ".$template_id);
        }

        if (!$template['parent_id']) {
            $index = $fx_core->env->get_sub()->get('id') == $fx_core->env->get_site()->get_title_sub_id();
            $type = $index ? 'index' : 'inner';
            $template = $template->get_default_layout($type);
        }


        $tpl = $this->load_layout($template);

        $parent = $template->get_parent();
        $layouts = $parent->get_layouts();


        foreach ($layouts as $layout) {
            if ($layout['id'] == $template['id']) {
                continue;
            }

            $keyword = $layout['keyword'];
            $layout_instance = $this->load_layout($layout);
            $tpl->set_vars('fx_tpl_'.$keyword, $layout_instance);
        }

        $tpl->set_vars('page', $fx_core->page);
        $tpl->set_vars('fx_core', $fx_core);

        $this->_load_files($tpl, $template);

        return $tpl;
    }

    protected function _load_files(fx_tpl_template $tpl, fx_template $template) {
        $fx_core = fx_core::get_object();
        $parent = $template->get_parent();

        $path = $parent->get_path();
        $tpl->set_vars('fx_path', $path);

        $fx_core->page->add_js_file('/floxim/lib/js/jquery-1.7.1.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery-ui-1.8.21.custom.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery.nestedSortable.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery.ba-hashchange.min.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery.json-2.3.js');
        $fx_core->page->add_js_file('/floxim/admin/js-templates/jstx.js');
        $fx_core->page->add_js_file('/floxim/admin/js-templates/compile.php');
        $fx_core->page->add_js_file('/floxim/admin/js/lib.js');
        $fx_core->page->add_js_file('/floxim/admin/js/adminpanel.js');
        $fx_core->page->add_js_file('/floxim/admin/js/front.js');
        $fx_core->page->add_js_file('/floxim/admin/js/buttons.js');
        $fx_core->page->add_js_file('/floxim/admin/js/form.js');
        $fx_core->page->add_js_file('/floxim/admin/js/dialog.js');
        $fx_core->page->add_js_file('/floxim/admin/js/fields.js');
        $fx_core->page->add_js_file('/floxim/admin/js/edit-in-place.js');
        $fx_core->page->add_js_file('/floxim/admin/js/store.js');
        $fx_core->page->add_js_file('/floxim/admin/js/dialog_file.js');
        $fx_core->page->add_js_file('/floxim/admin/js/admin.js');
        $fx_core->page->add_js_file('/floxim/admin/js/sort.js');
        $fx_core->page->add_js_file('/floxim/admin/js/menu/main.js');
        $fx_core->page->add_js_file('/floxim/admin/js/menu/more.js');
        $fx_core->page->add_js_file('/floxim/admin/js/menu/additional.js');
        $fx_core->page->add_js_file('/floxim/admin/js/menu/mode.js');
        $fx_core->page->add_js_file('/floxim/admin/js/menu/submenu.js');
        $fx_core->page->add_js_file('/floxim/admin/js/menu/breadcrumb.js');
        $fx_core->page->add_js_file('/floxim/lib/editors/elrte/elrte.full.js');
        $fx_core->page->add_js_file('/floxim/lib/editors/elrte/i18n/elrte.ru.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery.form.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery.jstree.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery-gp-gallery.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery.tipTip.minified.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery-ui-timepicker-addon.js');

        if ($fx_core->env->get_user() && $fx_core->env->get_user()->perm()->is_supervisor()) {
            $fx_core->page->add_css_file('/floxim/lib/css/elrte/elrte.min.css');
            $fx_core->page->add_css_file('/floxim/admin/skins/default/jquery-ui/main.css');
            $fx_core->page->add_css_file('/floxim/admin/skins/default/css/main.css');

            $fx_core->page->set_after_body(fx_controller_admin_adminpanel::panel_html());
        }


        // пользовательские css и js
        $files = $parent['files'];
        if ($files) {
            foreach ($files as $file) {
                $fx_core->page->add_file($path.$file['file']);
            }
        }

        // цветовая раскраска

        if ( $preview = fx_controller_admin_template::get_preview_data()) {
        	$color_id = $preview['color_id'];
        } else {
			$color_id = $fx_core->env->get_site()->get('color');
		}
        $colors = $parent['colors'];
        if ($colors) {
            foreach ($colors as $key => $color) {
                if ($color_id == $key || (!$color_id && $color['default'])) {
                    $fx_core->page->add_file($path.$color['file']);
                }
            }
        }
    }

    public function load_subdivision($subdivision = null) {
        $fx_core = fx_core::get_object();

        if (!$subdivision) {
            $subdivision = $fx_core->env->get_sub();
        }

        if (is_int($subdivision) || is_string($subdivision)) {
            $subdivision = fx::data('subdivision')->get_by_id($subdivision);
        }

        if (!$subdivision) {
            $site = $fx_core->env->get_site();
            $subdivision = $site->get_title_sub_id();
            $subdivision = fx::data('subdivision')->get_by_id($subdivision);
        }

        $fx_core->env->set_sub($subdivision);

        return $subdivision;
    }

    public function index($input = array()) {
        $fx_core = fx_core::get_object();

        $site = $fx_core->env->get_site();

        $subdivision = $this->load_subdivision();

        //$preview_template_id = $fx_core->input->get_service_session('preview_template_id_site'.$site['id']);
        if ( ($preview = fx_controller_admin_template::get_preview_data()) ) {
            $template = $preview['template_id'];
        } else {
            $template = $subdivision->get_data_inherit('template_id');
        }
        $fx_core->env->set_template($template);

        $metatags = $this->get_default_metatags($subdivision);
        $fx_core->page->set_metatags('title', $metatags);

        // h1
        $h1 = $subdivision['seo_h1'] ? $subdivision['seo_h1'] : $subdivision['name'];
        $fx_core->page->set_metatags('h1', $h1, 'essence=subdivision&action=edit&id='.$subdivision['id']);


        $template_id = $fx_core->env->get_template();
        $tpl = $this->load_tpl($template_id);

        $tpl->set_vars('fx_layout', new fx_layout_view());

        $fx_core->env->set_tpl($tpl);

        $tpl->settings();
        $tpl->write();

        fx_menu_force::attempt_to_show($subdivision);

        if ($fx_core->env->get_user() && $fx_core->env->get_user()->perm()->is_supervisor()) {
            // инициализация админ панели
            $fx_core->page->add_data_js('sub', $fx_core->env->get_sub()->get('id'));
            $fx_core->page->add_data_js('url', $_SERVER['REQUEST_URI']);

            $js_config = new fx_admin_configjs();

            if ($preview) {
            	$tpl_controller = new fx_controller_admin_template(array(), 'preview_panel', true);
                $panel_data = $tpl_controller->process();
                $js_config->add_additional_panel($panel_data);
            }

            $js_config->add_main_menu(fx_controller_admin_adminpanel::get_main_menu());
            $js_config->add_more_menu(fx_controller_admin_adminpanel::get_more_menu());
            $js_config->add_buttons(fx_controller_admin_adminpanel::get_buttons());
            if ($text) {
                $js_config->add_additional_text($text);
            }
            $fx_core->page->add_js_text("fx_adminpanel.init(".$js_config->get_config().");");
        }
    }

    public function set_main_content($content = '') {
        fx_core::get_object()->env->set_main_content($content);
    }

    /**
     * Скопировал в fx_controller_admin
     * @param type $input
     */
    public function admin_auth($input) {
        $fx_core = fx_core::get_object();
        $db = $fx_core->db;
        $AUTH_USER = $input['AUTH_USER'];
        $AUTH_PW = $input['AUTH_PW'];

        // попытка авторизации
        $user = fx::data('content_user')->get("`".fx::config()->AUTHORIZE_BY."` = '".$db->escape($AUTH_USER)."'
        AND `password` = ".fx::config()->DB_ENCRYPT."('".$db->escape($AUTH_PW)."')
        AND `checked` = 1");
        if ($user) {
            $user->authorize();
        }
        $this->admin();
    }

    /**
     * Скопировал в fx_controller_admin
     */
    public function _admin() {
        $fx_core = fx_core::get_object();

        $fx_core->page->add_js_file('/floxim/lib/js/jquery-1.7.1.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery-ui-1.8.21.custom.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery.nestedSortable.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery.ba-hashchange.min.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery.json-2.3.js');
        $fx_core->page->add_js_file('/floxim/admin/js-templates/jstx.js');
        $fx_core->page->add_js_file('/floxim/admin/js-templates/compile.php');
        $fx_core->page->add_js_file('/floxim/admin/js/lib.js');
        $fx_core->page->add_js_file('/floxim/admin/js/adminpanel.js');
        $fx_core->page->add_js_file('/floxim/admin/js/front.js');
        $fx_core->page->add_js_file('/floxim/admin/js/buttons.js');
        $fx_core->page->add_js_file('/floxim/admin/js/form.js');
        $fx_core->page->add_js_file('/floxim/admin/js/dialog.js');
        $fx_core->page->add_js_file('/floxim/admin/js/fields.js');
        $fx_core->page->add_js_file('/floxim/admin/js/edit-in-place.js');
        $fx_core->page->add_js_file('/floxim/admin/js/store.js');
        $fx_core->page->add_js_file('/floxim/admin/js/dialog_file.js');
        $fx_core->page->add_js_file('/floxim/admin/js/admin.js');
        $fx_core->page->add_js_file('/floxim/admin/js/sort.js');
        $fx_core->page->add_js_file('/floxim/admin/js/menu/main.js');
        $fx_core->page->add_js_file('/floxim/admin/js/menu/more.js');
        $fx_core->page->add_js_file('/floxim/admin/js/menu/submenu.js');
        $fx_core->page->add_js_file('/floxim/admin/js/menu/additional.js');
        $fx_core->page->add_js_file('/floxim/admin/js/menu/breadcrumb.js');
        $fx_core->page->add_js_file('/floxim/editors/elrte/elrte.full.js');
        $fx_core->page->add_js_file('/floxim/editors/elrte/i18n/elrte.ru.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery.form.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery.jstree.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery-gp-gallery.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery.tipTip.minified.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery-ui-timepicker-addon.js');
        
        if ($fx_core->env->get_user() && $fx_core->env->get_user()->perm()->is_supervisor()) {
            $fx_core->page->add_css_file('/floxim/lib/css/elrte/elrte.min.css');
            $fx_core->page->add_css_file('/floxim/admin/skins/default/jquery-ui/main.css');
            $fx_core->page->add_css_file('/floxim/admin/skins/default/css/main.css');

            $panel = '
            <div id="fx_admin_panel">
                <div id="fx_admin_panel_logo"></div>
                <div id="fx_admin_main_menu"></div>
                <div id="fx_admin_additional_menu"></div>
                <div id="fx_admin_clear"></div>
            </div>
            <div id="fx_admin_left">
                <div id="fx_admin_submenu"></div>
            </div>
            <div id="fx_admin_right">
                <div id="fx_admin_control" class="fx_admin_control_admin">
                    <div id="fx_admin_buttons"></div>
                    <div id="fx_admin_status_block"></div>
                 </div>
                 <div id="fx_admin_breadcrumb"></div>
                 <div id="fx_admin_content">'.$auth_form.'</div>
            </div>
            <div id="fx_dialog"></div>
            <div id="fx_dialog_file"></div>';
            $fx_core->page->set_after_body($panel);
        }

        $auth_form = '';
        if (!$fx_core->env->get_user()) {
            $auth_form = '<div>
                <form method="post" action="/floxim/">
                <input type="hidden" name="action" value="admin_auth" />
                <input type="hidden" name="essence" value="layout" />
                <input name="AUTH_USER" />
                <input type="password" name="AUTH_PW" />
                <input type="submit" value="Вход" class="auth_submit">
                </form></div>';
        }

        echo '<html class="fx_admin_html"><head><title>Floxim</title></head><body> '.$auth_form.'</body></html>';

        if ($fx_core->env->get_user() && $fx_core->env->get_user()->perm()->is_supervisor()) {
            $js_config = new fx_admin_configjs();
            $js_config->add_main_menu(fx_controller_admin_adminpanel::get_main_menu());
            $js_config->add_more_menu(fx_controller_admin_adminpanel::get_more_menu());
            $js_config->add_buttons(fx_controller_admin_adminpanel::get_buttons());
            $fx_core->page->add_js_text("fx_adminpanel.init(".$js_config->get_config().");");
        }
         
    }

    public function blank() {

    }

}

?>
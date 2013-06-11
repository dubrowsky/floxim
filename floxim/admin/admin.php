<?php

class fx_controller_admin extends fx_controller {

    /** @var string стандартное действие для контроллера - вернуть html верстку  */
    protected $action = 'admin_office';
    
    /** @var bool метод process() должен возвращать результат? */
    protected $process_do_return = false;
    
    protected $essence_type;
    protected $save_history = true;

    /** @var fx_admin_response */
    protected $response;

    /** @var fx_admin_ui */
    protected $ui;

    public function __construct($input = array(), $action = null, $do_return = false) {
        parent::__construct($input, $action);
        
        $this->essence_type = str_replace('fx_controller_admin_', '', get_class($this));
        $this->ui = new fx_admin_ui();
        
        $this->process_do_return = $do_return;
    }

    public function process() {

        $input = $this->input;
        $action = $this->action;
        
        if (!fx::env('is_admin')) {
            $result = $this->admin_auth($input);
            
            if (is_string($result)) {
                return $result;
            }    
        }
        
        if (!$action || !is_callable(array($this, $action))) {
            die("Error! Class:".get_class($this).", action:".htmlspecialchars($action));
        }
        
        $this->response = new fx_admin_response($input);
        
        //fx_admin_checkpatch::check();

        if ($this->save_history && $input['posting']) {
            $history = fx::data('history')->create(array('user_id' => 1));
            $history['name'] = $this->get_history_name($action);
            $history['date'] = date("Y-m-d H:i:s");
            $history->save();
            fx_history::set_history_obj($history);
            fx_history::delete_old();
        }

        $result = $this->$action($input);
        if (is_string($result)) {
            return $result;
        }

        if ($input['posting']) {
            if (!$result['text']) $result['text'] = $this->get_status_text();

            $undo = fx_controller_admin_history::get_undo_obj();
            $redo = fx_controller_admin_history::get_redo_obj();
            if ($undo) $result['history']['undo'] = $undo['name'];
            if ($redo) $result['history']['redo'] = $redo['name'];
        }

        if ($this->response) {
            $result = $result ? $result : array();
            $result = array_merge($result, $this->response->to_array());
        }
        
        if ($this->process_do_return) {
            return $result;
        }

        echo json_encode($result);
    }

    protected function get_history_name($action) {
        $essence = str_replace('fx_controller_', '', get_class($this));
        $action = str_replace('_save', '', $action);
        $constant = "FX_HISTORY_".strtoupper($essence)."_".strtoupper($action);

        return defined($constant) ? constant($constant) : $constant;
    }

    protected function get_status_text() {
        return "Сохранено";
    }

    public function admin_tabs($tabs, $callback_param = null) {
        $tabs_key = array_keys($tabs);
        $active_tab = $this->get_active_tab();
        if (!in_array($active_tab, $tabs_key)) {
            $active_tab = $tabs_key[0];
        }

        foreach ($tabs as $tab_key => $tab_name) {
            $this->response->add_tab($tab_key, $tab_name, ($active_tab == $tab_key));
        }
        $this->response->set_change_tab_url();

        call_user_func(array($this, 'tab_'.$active_tab), $callback_param);
    }
    
    protected function get_active_tab() {
        return $this->input['params'][1];
    }
    
    ///// ACTIONS ////
            
    /**
     * Возвращает строку с базовой разметкой и
     * собирает все сопутсвующие файлы в fx_core::get_object()->page'е
     * @return string
     */
    public function admin_office()
    {   
        $js_files = array(
            FX_JQUERY_PATH,
            '/floxim/lib/js/jquery-ui-1.8.21.custom.js',
            '/floxim/lib/js/jquery.nestedSortable.js',
            '/floxim/lib/js/jquery.ba-hashchange.min.js',
            '/floxim/lib/js/jquery.json-2.3.js',
            '/floxim/lib/js/ajaxfileupload.js',                                            
            '/floxim/admin/js-templates/jstx.js',
            '/floxim/admin/js-templates/compile.php',
            '/floxim/admin/js/lib.js',
            '/floxim/admin/js/adminpanel.js',
            '/floxim/admin/js/front.js',
            '/floxim/admin/js/buttons.js',                                     
            '/floxim/admin/js/form.js',
            '/floxim/admin/js/dialog.js',
            '/floxim/admin/js/fields.js',
            '/floxim/admin/js/edit-in-place.js',
            '/floxim/admin/js/store.js',
            '/floxim/admin/js/dialog_file.js',
            '/floxim/admin/js/admin.js',
            '/floxim/admin/js/sort.js',
            '/floxim/admin/js/menu/main.js',
            '/floxim/admin/js/menu/mode.js',
            '/floxim/admin/js/menu/more.js',
            '/floxim/admin/js/menu/submenu.js',
            '/floxim/admin/js/menu/additional.js',
            '/floxim/admin/js/menu/breadcrumb.js',
            '/floxim/lib/editors/elrte/elrte.full.js',
            '/floxim/lib/editors/elrte/i18n/elrte.ru.js',
            '/floxim/lib/js/jquery.form.js',
            '/floxim/lib/js/jquery.jstree.js',
            '/floxim/lib/js/jquery-gp-gallery.js',
            '/floxim/lib/js/jquery.tipTip.minified.js',
            '/floxim/lib/js/jquery-ui-timepicker-addon.js'
        );
        $page = fx::page();
        foreach ($js_files as $file) {
            $page->add_js_file($file);
        }
        $css_files = array(
            '/floxim/lib/css/elrte/elrte.min.css',
            '/floxim/admin/skins/default/jquery-ui/main.css',
            '/floxim/admin/skins/default/css/main.css'
        );
        foreach ($css_files as $file) {
            $page->add_css_file($file);
        }
        
        $auth_form = '';
        if (fx::env('is_admin')) {
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
            $page->set_after_body($panel);
        } else {
            $auth_form = '<div>
                <form method="post" action="/floxim/">
                <input type="hidden" name="action" value="admin_auth" />
                <input type="hidden" name="essence" value="admin" />
                <input name="AUTH_USER" />
                <input type="password" name="AUTH_PW" />
                <input type="submit" value="Вход" class="auth_submit">
                </form></div>';
        }

        if (fx::env('is_admin')) {
            $js_config = new fx_admin_configjs();
            $js_config->add_main_menu(fx_controller_admin_adminpanel::get_main_menu());
            $js_config->add_more_menu(fx_controller_admin_adminpanel::get_more_menu());
            $js_config->add_buttons(fx_controller_admin_adminpanel::get_buttons());
            $page->add_js_text("fx_adminpanel.init(".$js_config->get_config().");");
        }
        
        $html = '<html class="fx_admin_html"><head><title>Floxim</title></head><body> '.$auth_form.'</body></html>';
        $html = $page->post_process($html);
        return $html;
    }
    
    /**
     * Авторизовывает и делает admin_office()
     * @return string
     */
    public function admin_auth($input) {
        
        $db = fx::db();
        $AUTH_USER = $input['AUTH_USER'];
        $AUTH_PW = $input['AUTH_PW'];

        // попытка авторизации
        $user = fx::data('content_user')->get(
                "`".fx::config()->AUTHORIZE_BY."` = '".$db->escape($AUTH_USER)."'
        AND `password` = ".fx::config()->DB_ENCRYPT."('".$db->escape($AUTH_PW)."')"
        );
        
        
        if ($user) {
            $user->authorize();
        }
        return $this->admin_office();
    }
    
    public function move_save($input) {
        
        $essence = $this->essence_type;

        $positions = $input['positions'] ? $input['positions'] : $input['pos'];
        if ($positions) {
            $priority = 0;
            foreach ($positions as $id) {
                $item = fx::data($essence)->get_by_id($id);
                if ($item) {
                    $item->set('priority', $priority++)->save();
                }
            }
        }

        return array('status' => 'ok');
    }

    public function on_save($input) {

        $es = $this->essence_type;
        $result = array('status' => 'ok');

        $ids = $input['id'];
        if (!is_array($ids)) $ids = array($ids);

        foreach ($ids as $id) {
            try {
                fx::data($es)->get_by_id($id)->checked();
            } catch (Exception $e) {
                $result['status'] = 'error';
                $result['text'][] = $e->getMessage();
            }
        }

        return $result;
    }

    public function off_save($input) {
        
        $es = $this->essence_type;
        $result = array('status' => 'ok');

        $ids = $input['id'];
        if (!is_array($ids)) $ids = array($ids);

        foreach ($ids as $id) {
            try {
                fx::data($es)->get_by_id($id)->unchecked();
            } catch (Exception $e) {
                $result['status'] = 'error';
                $result['text'][] = $e->getMessage();
            }
        }

        return $result;
    }

    public function delete_save($input) {
        
        $es = $this->essence_type;
        $result = array('status' => 'ok');

        $ids = $input['id'];
        if (!is_array($ids)) $ids = array($ids);

        foreach ($ids as $id) {
            try {
                fx::data($es)->get_by_id($id)->delete();
            } catch (Exception $e) {
                $result['status'] = 'error';
                $result['text'][] = $e->getMessage();
            }
        }

        return $result;
    }
    
}

class fx_controller_admin_module extends fx_controller_admin {

    protected $menu_items = array();

    public function basesettings($input) {
        $module_keyword = str_replace('fx_controller_admin_module_', '', get_class($this));
        $this->response->submenu->set_menu('settings')->set_subactive('settings-'.$module_keyword);
        $this->response->breadcrumb->add_item('Настройка модуля '.$module_keyword);
        $this->response->add_form_button('save');
        $this->settings();
    }

    public function settings() {
        $this->response->add_field($this->ui->label('Переопределите метод settings в своем классе'));
    }

    public function basesettings_save($input) {
        $this->settings_save($input);
    }

    public function settings_save($input) {
        ;
    }

    public function add_node($id, $name, $href = '') {
        $this->menu_items[] = array('id' => $id, 'name' => $name, 'href' => $href);
    }

    public function get_menu_items() {
        $this->init_menu();
        return $this->menu_items;
    }

    public function init_menu() {
        
    }

}

?>
<?php

class fx_controller_admin extends fx_controller {

    protected $essence_type;
    protected $save_history = true;

    /** @var fx_admin_response */
    protected $response;

    /** @var fx_admin_ui */
    protected $ui;

    public function __construct() {
        $this->essence_type = str_replace('fx_controller_admin_', '', get_class($this));
        $this->ui = new fx_admin_ui();
    }

    public function process($input, $action = null, $do_return = false) {
        $fx_core = fx_core::get_object();
        $user = $fx_core->env->get_user();

        if (!$user || !$user->perm()->is_supervisor()) {
            die("Нет прав!");
        }

        if (!$action || !is_callable(array($this, $action))) {
            die("Error! Class:".get_class($this).", action:".htmlspecialchars($action));
        }

        $this->input = $input;
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
        
        if ($do_return) {
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
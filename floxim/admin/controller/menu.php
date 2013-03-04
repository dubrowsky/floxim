<?php

class fx_controller_admin_menu extends fx_controller_admin {

    /**
     * Добавление пункта меню
     */
    public function add($input) {
        //if ($input['type'] == 'sub' || $input['type'] == 'level') {
        $parent_id = $input['params']['sub'] + 0;
        $s = new fx_controller_admin_subdivision();
        return $s->add(array('parent_id' => $parent_id));
        //}
    }

    public function settings($input) {
        $fields = array();

        $fields[] = $this->ui->hidden('essence', 'menu');
        $fields[] = $this->ui->hidden('keyword', $input['keyword']);
        $fields[] = $this->ui->hidden('id', +$input['id']);

        if ($input['type'] == 'level') {
            $type = 'level';
            $type .= $input['level'] <= 2 ? $input['level'] : 'N';
        } else if ($input['type'] == 'sub') {
            $type = 'sub';
            if ($input['sub'] != fx_menu::CURRENT_SUB) $type .= 'N';
        }
        else if ($input['type'] == 'path') {
            $type = 'path';
        }
        else {
            $type = 'manual';
        }

        $types = array();
        $types['level0'] = 'Меню верхнего уровня';
        $types['level1'] = 'Меню первого уровня';
        $types['level2'] = 'Меню второго уровня';
        $types['levelN'] = 'Меню заданного уровня';
        $types['sub'] = 'Оглавление текущего раздела';
        $types['subN'] = 'Оглавление произвольного раздела';
        $types['manual'] = 'Произвольное меню';

        $post = $input;
        $post['action'] = 'specific_settings';
        
        if ( $type == 'path' || $input['kind'] == fx_menu::KIND_DEPENDENT ) {
            $fields[] = array('type' => 'hidden', 'name' => 'menutype', 'value' => $type, 'whole' => true, 'post' => $post );
        }
        else  {
            $fields[] = array('label' => 'Тип меню', 'type' => 'select', 'name' => 'menutype', 'values' => $types, 'whole' => true, 'post' => $post, 'value' => $type);
        }
        

        return array('fields' => $fields);
    }

    public function specific_settings($input) {
        if (strpos($input['menutype'], 'level') !== false) {
            $type = 'level';
        } else if (strpos($input['menutype'], 'sub') !== false) {
            $type = 'sub';
        } else {
            $type = $input['menutype'];
        }

        $func = 'specific_settings_'.$type;
        $fields = call_user_func(array($this, $func), $input);

        $fields[] = $this->ui->hidden('action', 'settings');
        $fields[] = $this->ui->hidden('posting');
        return array('fields' => $fields);
    }

    protected function specific_settings_level($input) {
        $fields = array();
        $fields[] = $this->ui->hidden('type', 'level');

        $level = str_replace('level', '', $input['menutype']);
        if ($level == 'N') {
            $fields[] = $this->ui->input('settings[level]', 'Уровень меню:', intval($input['level']));
        } else {
            $fields[] = $this->ui->hidden('settings[level]', intval($level));
        }

        $fields = array_merge($fields, $this->sub_page_settings($input));
        return $fields;
    }

    protected function specific_settings_sub($input) {
        $fields = array();
        $fields[] = $this->ui->hidden('type', 'sub');

        if (str_replace('sub', '', $input['menutype']) == 'N') {
            $fields[] = $this->ui->label('Придумать крутой выбор раздела');
            $fields[] = $this->ui->input('settings[sub]', 'Номер раздела:', intval($input['sub']));
        } else {
            $fields[] = $this->ui->hidden('settings[sub]', fx_menu::CURRENT_SUB);
        }

        $fields = array_merge($fields, $this->sub_page_settings($input));
        return $fields;
    }

    protected function specific_settings_manual($input) {
        $fields = array();
        $fields[] = $this->ui->hidden('type', 'manual');

        if ($input['id']) {
            $menu = fx_core::get_object()->menu->get_by_id($input['id']);
            $values = $menu['settings']['items'];
        }
        if (!$values) {
            $values = array();
        }

        $fields[] = array('name' => 'settings[items]', 'type' => 'set', 'tpl' => array(
                        array('name' => 'name'),
                        array('name' => 'url')
                ),
                'values' => $values,
                'labels' => array('Name', 'URL'));

        return $fields;
    }
    
    protected function specific_settings_path($input) {
        $fields = array();
        $fields[] = $this->ui->input('settings[from]', 'Сколько пунктов пропустить с начала', intval($input['from']));
        $fields[] = $this->ui->input('settings[end]', 'Сколько пунктов пропустить с конца', intval($input['end']));
        $fields[] = $this->ui->checkbox('settings[reverse]', 'Выводить в обратном порядке', null, intval($input['reverse']));
        
        return $fields;
    }

    protected function sub_page_settings($input) {
        $submenu_count = intval($input['submenu_count']);
        
        $fields = array();
        $values = array('none' => 'не показывать', 'active' => 'только подразделы активного раздела', 'all' => 'все');
        $show_sub_pages = $input['show_sub_pages'] ? $input['show_sub_pages'] : 'active';
        
        if ($submenu_count) {
            $fields[] = $this->ui->select('settings[show_sub_pages]', 'Показывать подразделы', $values, $show_sub_pages);
        }
        else {
            $fields[] = $this->ui->hidden('settings[show_sub_pages]', $show_sub_pages);
        }

        
        $sub_page_level = $input['sub_page_level'] ? intval($input['sub_page_level']) : 1;
        if ($submenu_count > 1) {
            $values = array();
            $values[1] = 'раскрывать на следующий уровень вложенности';
            for ($i = 2; $i <= $submenu_count; $i++) {
                $values[$i] = 'раскрывать на следующий уровень вложенности + '.($i - 1);
            }
            $fields[] = $this->ui->select('settings[sub_page_level]', 'На какой уровень вложенности расскрывать', $values, $sub_page_level);
        } else {
            $fields[] = $this->ui->hidden('settings[sub_page_level]', 1);
        }

        return $fields;
    }

    public function delete_save($input) {
        $fx_core = fx_core::get_object();
        $menu = $fx_core->menu->get_by_id($input['id']);
        if ( $menu ) {
            $menu->set('deleted',1)->save();
        }
        return array('status' => 'ok');
        //$s = new fx_controller_subdivision();
        //return $s->delete_save($input);
    }

    public function settings_save($input) {
        $fx_core = fx_core::get_object();

        if ($input['id']) {
            $menu = $fx_core->menu->get_by_id($input['id']);
        } else {
            $menu = $fx_core->menu->create();
            $menu['site_id'] = $fx_core->env->get_site('id');
        }

        $input['settings']['reverse'] = intval($input['settings']['reverse']);
        // важно не потерять "старые" значения, например, пункты ручного меню
        $old_settings = $menu['settings'] ? $menu['settings'] : array();
        $new_settings = $input['settings'] ? $input['settings'] : array();
        $input['settings'] = array_merge($old_settings, $new_settings);

        foreach (array('keyword', 'type', 'settings') as $v) {
            if (isset($input[$v])) $menu[$v] = $input[$v];
        }

        $menu->save();

        return array('status' => 'ok');
    }

    public function move_save($input) {
        $fx_core = fx_core::get_object();
        $menu = $fx_core->menu->get_by_id($input['id']);

        if ($menu) {
            $items = $menu['settings']['items'];
            $new_items = array();

            if ($input['pos']) {
                foreach ($input['pos'] as $id) {
                    $new_items[] = $items[$id];
                }
            }

            $menu->set('settings', array('items' => $new_items))->save();
        }

        return array('status' => 'ok');
    }
    
    public function off_save( $input ) {
        // выключение принудительного показа подразделов текущего раздела
        if ( $input['force_menu'] && $input['subdivision_id'] ) {
            fx_core::get_object()->subdivision->get_by_id($input['subdivision_id'])->set('force_menu', 0)->save();
        }
        
        return array('status' => 'ok');
    }
    
     public function on_save( $input ) {
        // выключение принудительного показа подразделов текущего раздела
        if ( $input['force_menu'] && $input['subdivision_id'] ) {
            fx_core::get_object()->subdivision->get_by_id($input['subdivision_id'])->set('force_menu', 1)->save();
        }
        
        return array('status' => 'ok');
    }
    

}

?>
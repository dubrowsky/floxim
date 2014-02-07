<?php

class fx_controller_admin_lang extends fx_controller_admin {

    public function all() {
        $langs = fx::data('lang')->all();

        $list = array('type' => 'list', 'filter' => true, 'tpl' => 'imgh', 'sortable' => true, 'essence'=> 'lang');
        $list['labels'] = array();

        $list['values'] = array();
        foreach ($langs as $v) {
            $text = '';
            if ($v['native_name']) {
                $text .= $v['native_name'].' <span style="font-size:10px; color:#777;">&middot;</span> ';
            }
            $text .= $v['lang_code'];
            $r = array(
                    'id' => $v['id'],
                    'header' => array('name' => $v['en_name'], 'url' => 'lang.edit('.$v['id'].')'),
                    'text' => $text
            );
            $list['values'][] = $r;
        }

        $this->response->add_field($list);

        $this->response->add_buttons(
            array(
                array(
                    'key' => 'add', 
                    'title' => fx::alang('Add new language','system'),
                    'url' => '#admin.administrate.lang.add'
                ),
                'delete'
            )
        );
        $this->response->breadcrumb->add_item( fx::alang('Languages','system') );
        $this->response->submenu->set_menu('lang');
    }

    public function add($input) {
        $fields = array();

        $fields[] = $this->ui->hidden('action', 'add_save');
        $fields[] = $this->ui->hidden('essence', 'lang');
        $fields[] = $this->ui->input('en_name', fx::alang('Language name','system'), fx::alang('Enter english language name','system'));
        $fields[] = $this->ui->input('native_name', fx::alang('Native language name','system'), fx::alang('Enter native language name','system'));
        $fields[] = $this->ui->input('lang_code', fx::alang('Language code','system'), fx::alang('Enter language code','system'));

        $this->response->add_fields($fields);
        $this->response->dialog->set_title( fx::alang('Create a new language','system') );
        $this->response->breadcrumb->add_item( 
            fx::alang('Languages','system'),
            '#admin.administrate.lang.all'
        );
        $this->response->breadcrumb->add_item(
            fx::alang('Add new language','system')
        );
        $this->response->add_form_button('save');
        $this->response->submenu->set_menu('lang');
    }

    public function add_save($input) {
        $result = array('status' => 'ok');

        $lang = fx::data('lang')->create(
            array(
                'en_name' => $input['en_name'],
                'native_name' => $input['native_name'],
                'lang_code' => $input['lang_code']
            )
        );

        if (!$lang->validate()) {
            $result['status'] = 'error';
            $result['errors'] = $lang->get_validate_error();
            return $result;
        }
        try {
            $lang->save();
        } catch (Exception $e) {
            $result['status'] = 'error';
            $result['text'][] = $e->getMessage();
        }

        return $result;
    }

    public function edit($input) {
        $lang_id = isset($input['id']) ? $input['id'] : isset($input['params'][0]) ? $input['params'][0] : null;
        
        $lang = fx::data('lang', $lang_id);

        $main_fields = array();
        $main_fields[] = $this->ui->input('en_name', fx::alang('Language name','system'), $lang['en_name']);
        $main_fields[] = $this->ui->input('native_name', fx::alang('Naitive name','system'), $lang['native_name']);
        $main_fields[] = $this->ui->input('lang_code', fx::alang('Language code','system'), $lang['lang_code']);
        $this->response->add_fields($main_fields);

        $fields = array();
        $fields[] = $this->ui->hidden('essence', 'lang');
        $fields[] = $this->ui->hidden('action', 'edit');
        $fields[] = $this->ui->hidden('posting');
        $fields [] = $this->ui->hidden('id', $lang['id']);
        $this->response->add_fields($fields);
        $this->response->add_form_button('save');

        $this->response->breadcrumb->add_item( fx::alang('Languages','system'), '#admin.lang.all');
        $this->response->breadcrumb->add_item($lang['en_name'], '#admin.lang.edit('.$lang['id'].')');
        $this->response->submenu->set_menu('lang');
        //$this->_set_layout('settings', $lang);
    }

    public function edit_save($input) {
        
        $lang = fx::data('lang', $input['id']);
        $result = array('status' => 'ok');
        $params = array('en_name', 'native_name', 'lang_code');

        foreach ($params as $v) {
            if (isset($input[$v])) {
                $lang[$v] = $input[$v];
            }
        }

        $lang->save();
        return $result;
    }

}
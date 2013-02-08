<?php

class fx_controller_admin_subdivision extends fx_controller_admin {

    public function page_settings($input) {
        return $this->edit($input);
    }

    public function design_settings($input) {
        $info = fx::data('subdivision')->get_by_id($input['id']);
        $result['fields'][] = array('name' => 'essence', 'type' => 'hidden', 'value' => 'subdivision');
        $result['fields'][] = array('name' => 'id', 'type' => 'hidden', 'value' => $input['id']);
        $result['fields'][] = array('name' => 'action', 'type' => 'hidden', 'value' => 'design_settings');
        $result['fields'][] = array('name' => 'posting', 'type' => 'hidden', 'value' => '1');
        $result['fields'][] = array('name' => 'own_design', 'type' => 'checkbox', 'value' => (bool) $info['own_design'], 'label' => 'Страница имеет индивидуальный дизайн');

        if ($info['own_design']) {
            $text = "Страница имеет индивидуальный дизайн, это значит, что все инфоблоки, добавленные в режиме Дизайн на этой странице будут показываться только на этой странице";
        } else {
            $text = "Страница не имеет индивидуального дизайна, это значит, что все инфоблоки, добавленные в режиме Дизайн на этой странице будут показываться и на других страницах";
        }

        $result['fields'][] = $this->ui->label($text);
        return $result;
    }

    public function design_settings_save($input) {
        $sub = fx::data('subdivision')->get_by_id($input['id']);

        // снятие свойства "Индивидувльный дизайн" - удаляем все "индивидуальные" инфоблоки
        if ($sub['own_design'] && !$input['own_design']) {
            $infoblocks = fx::data('infoblock')->get_all("`subdivision_id`='".intval($input['id'])."' and `subtype` <> 'block' ");
            foreach ($infoblocks as $infoblock) {
                $infoblock->delete();
            }
            $sub->set('own_design', 0)->save();
        }
        // включение свойства "Индивидувльный дизайн" - копируем инфоблоки
        else if (!$sub['own_design'] && $input['own_design']) {
            $infoblocks = fx::data('infoblock')->get_all("`subdivision_id`=0 and (`subtype` <> 'block'  or `subtype` is null)");
            foreach ($infoblocks as $infoblock) {
                $infoblock = $infoblock->get();
                unset($infoblock['id']);
                $infoblock['subdivision_id'] = $input['id'];
                $infoblock['individual'] = 1;
                $n = fx::data('infoblock')->create($infoblock);
                $n->save();
            }

            $sub->set('own_design', 1)->save();
        }

        return array('status' => 'ok');
    }

    public function edit($input) {
        $info = fx::data('subdivision')->get_by_id($input['id']);

        $res = $this->_form($info);

        $res['fields'][] = array('name' => 'action', 'value' => 'edit', 'type' => 'hidden');
        $res['fields'][] = array('name' => 'essence', 'value' => 'subdivision', 'type' => 'hidden');
        $res['fields'][] = array('name' => 'posting', 'value' => '1', 'type' => 'hidden');
        $res['fields'][] = array('name' => 'id', 'value' => $input['id'], 'type' => 'hidden');

        $res['dialog_title'] = 'Редактирование раздела "'.$info['name'].'"';

        return $res;
    }

    public function settings($input) {
        return $this->edit($input);
    }

    public function add($input) {
        $info = array();

        $info['name'] = 'Новая страница';
        $info['keyword'] = 'newpage';
        $info['parent_id'] = $input['parent_id'] ? $input['parent_id'] : ( $input['id'] ? $input['id'] : 0);
        $info['site_id'] = $input['site_id'];
        $info['checked'] = 1;

        $result = $this->_form($info);

        $result['fields'][] = array('name' => 'action', 'value' => 'add', 'type' => 'hidden');
        $result['fields'][] = array('name' => 'essence', 'value' => 'subdivision', 'type' => 'hidden');
        $result['fields'][] = array('name' => 'posting', 'value' => '1', 'type' => 'hidden');

        $result['dialog_title'] = 'Добавление нового раздела';

        return $result;
    }

    public function edit_save($input) {
        $sub_id = $input['subdivision_id'];
        if (!$sub_id) $sub_id = $input['id'];
        $result = array('status' => 'ok');

        if ($result['status'] == 'ok') {
            $sub = fx::data('subdivision')->get_by_id($sub_id);

            if ($input['name']) $sub['name'] = $input['name'];
            if ($input['keyword']) $sub['keyword'] = $input['keyword'];
            if ($input['h1']) $sub['seo_h1'] = $input['h1'];
            if ($input['title']) $sub['title'] = $input['title'];
            if (isset($input['template_id']))
                    $sub['template_id'] = $input['template_id'];
            //для edit-in-place
            if ($input['field'] && $input['f_'.$input['field']]) {
                $sub[$input['field']] = $input['f_'.$input['field']];
            }



            if ($input['keyword']) {
                //$sub['hidden_url'] = $this->get_hidden_url($sub);
            }

            $sub->save();
        }

        return $result;
    }

    public function add_save($input) {
        $result = array('status' => 'ok');

        $data = array();
        $data['name'] = $input['name'];
        $data['keyword'] = $input['keyword'];
        $data['parent_id'] = $input['parent_id'];
        $data['checked'] = $input['checked'];
        $data['site_id'] = $input['site_id'];
        $data['template_id'] = intval($input['template_id']);

        $sub = fx::data('subdivision')->create($data);
        $sub->save();

        return $result;
    }

    public function move_save($input) {
        $result = array('status' => 'ok');

        // сортировка в рамках одного родительского раздела
        if ($input['pos']) {
            $priority = 0;
            foreach ($input['pos'] as $id) {
                $sub = fx::data('subdivision')->get_by_id($id);
                $sub['priority'] = $priority++;
                $sub->save();
            }
        }
        // перенос между разделами
        else if ($input['darged']) {
            $darged = fx::data('subdivision')->get_by_id($input['darged']);
            $target = $input['target'] ? fx::data('subdivision')->get_by_id($input['target']) : $input['target'];
            // after|before|last
            $type = $input['type'];

            // новый подраздел
            if ($type == 'last' || $type == 'first') {
                $darged['parent_id'] = $target['id'];
                $darged['priority'] = 0;
                $darged->save();
                $darged->update_hidden_url();
            } else if ($type == 'before' || $type == 'after') {
                $new_parent = $target['parent_id'];
                if ($new_parent != $darged['parent_id']) {
                    $darged['parent_id'] = $new_parent;
                }
                $inner_subs = fx::data('subdivision')->get_all('parent_id', $new_parent, 'site_id', $darged['site_id']);
                $target_priority = $target['priority'];
                if ($type == 'after') {
                    $darged['priority'] = ($type == 'after') ? $target_priority + 1 : $target_priority - 1;
                    $darged->save();
                    $darged->update_hidden_url();
                    foreach ($inner_subs as $inner_sub) {
                        if ($inner_sub['id'] == $darged['id'] || $inner_sub['id'] == $target['id']) {
                            continue;
                        }
                        if ($inner_sub['priority'] <= $target_priority) {
                            continue;
                        }
                        $inner_sub['priority'] = $inner_sub['priority'] + 1;
                        $inner_sub->save();
                        $inner_sub->update_hidden_url();
                    }
                } else {
                    $darged['priority'] = $target_priority - 1;
                    $darged->save();
                    $darged->update_hidden_url();
                }
            }
        }
        return $result;
    }

    protected function _form($info) {
        $res = array();

        $tabs['main'] = array('name' => 'Основные');
        $tabs['seo'] = array('name' => 'SEO');
        $tabs['design'] = array('name' => 'Дизайн');

        if ($info['id']) {
            $url = 'http://'.$_SERVER['HTTP_HOST'].$info['hidden_url'];
            $current_values = fx_core::get_object()->util->get_meta_tags($url);
        }

        $fields[] = array('name' => 'parent_id', 'type' => 'hidden', 'value' => $info['parent_id']);

        $fields[] = array('name' => 'checked', 'type' => 'checkbox', 'value' => $info['checked'], 'label' => 'Включить страницу', 'tab' => 'main');
        $fields[] = array('name' => 'name', 'value' => $info['name'], 'label' => 'Название', 'tab' => 'main');
        $fields[] = array('name' => 'keyword', 'value' => $info['keyword'], 'label' => 'Ключевое слово', 'tab' => 'main');

        $fields[] = array('name' => 'title', 'value' => $info['title'], 'label' => 'Заголовок страницы (Title)', 'tab' => 'seo');
        $field = array('name' => 'h1', 'value' => $info['h1'], 'label' => 'H1', 'tab' => 'seo');
        if ($current_values['h1']) $field['current'] = $current_values['h1'];
        $fields[] = $field;
        $fields[] = array('name' => 'keywords', 'value' => $info['keywords'], 'label' => 'Ключевые слова (Keywords)', 'tab' => 'seo');
        $fields[] = array('name' => 'description', 'type' => 'textarea', 'value' => $info['description'], 'label' => 'Описание страницы (Description)', 'extendable' => true, 'tab' => 'seo');

        if ($info['site_id']) {
            $site = fx::data('site')->get_by_id($info['site_id']);
            $template = fx::data('template')->get_by_id($site['template_id']);
            $layouts = $template->get_layouts();
            $values = array(0 => 'определяется системой');
            foreach ($layouts as $layout) {
                $values[$layout['id']] = $layout['name'];
            }
            $fields[] = array('name' => 'template_id', 'type' => 'select', 'values' => $values, 'value' => $info['template_id'], 'label' => 'Layout', 'tab' => 'design');
        }

        $fields[] = $this->ui->hidden('site_id', $info['site_id']);
        $res['tabs'] = $tabs;
        $res['fields'] = $fields;

        return $res;
    }

    protected function get_hidden_url($sub) {
        $url = '/';
        while ($sub['parent_id']) {
            $url = '/'.$sub['keyword'].$url;
            $sub = fx::data('subdivision')->get_by_id($sub['parent_id']);
        }
        $url = '/'.$sub['keyword'].$url;

        return $url;
    }

    public function page_rights($input) {
        $subdivision = fx::data('subdivision')->get_by_id($input['id']);

        if (!$subdivision) {
            return array('status' => 'error', 'text' => 'Раздел не существует');
        }

        $fields = array();
        $infoblocks = fx::data('infoblock')->get_all("type='content' and subtype in('block','user') and  subdivision_id = ?", array($subdivision['id']));

        if (!$infoblocks) {
            $fields[] = $this->ui->label("На странице нет инфоблоков");
        } else {
            $user_types = fx_rights::get_user_types(true);
            $user_types_values = array();
            foreach ($user_types as $type) {
                $user_types_values[$type] = fx_rights::get_label($type);
            }

            $rights_types = fx_rights::get_rights_types();

            foreach ($infoblocks as $infoblock) {
                $infoblock_id = $infoblock['id'];
                $fields[] = $this->ui->label("Права инфоблока ".$infoblock['name']);

                $access = $infoblock->get_access();
                $access_real = $infoblock->get_access(null, false);

                foreach ($rights_types as $right) {
                    $user_types_values['inherit'] = fx_rights::get_label('inherit').' ('.fx_rights::get_label($access[$right]).')';
                    $fields[] = $this->ui->radio('access['.$infoblock_id.']['.$right.']', fx_rights::get_label($right), $user_types_values, $access_real[$right]);
                }
            }
        }

        $fields[] = $this->ui->hidden('essence', 'subdivision');
        $fields[] = $this->ui->hidden('action', 'page_rights');
        $fields[] = $this->ui->hidden('posting');

        $result = array('fields' => $fields);
        return $result;
    }

    public function page_rights_save($input) {
        if ($input['access']) {
            foreach ($input['access'] as $id => $access) {
                $infoblock = fx::data('infoblock')->get_by_id($id);
                $infoblock->set('access', $access)->save();
            }
        }

        return array('status' => 'ok');
    }

}

?>
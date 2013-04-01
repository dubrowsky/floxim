<?php

class fx_controller_admin_content extends fx_controller_admin {

    public function add_edit($input) {
        
        $content_type = $input['content_type'];
        $component = fx::data('component', $content_type);
        
        if ($input['content_id']) {
            // редактирование
            $content = fx::data('content_'.$content_type, $input['content_id']);
            if ($component['has_page']) {
                $page = $content->get_page();
            }
        } else {
            // создание
            $parent_page = fx::data('content_page', $input['parent_id']);
            $content = fx::data('content_'.$content_type)->create(array(
                'parent_id' => $input['parent_id'],
                'infoblock_id' => $input['infoblock_id'],
                'checked' => 1
            ));
            if ($component['has_page']) {
                $page = fx::data('content_page')->create(array(
                    'parent_id' => $input['parent_id'],
                    'content_type' => $content_type,
                    'site_id' => $parent_page['site_id'],
                    'infoblock_id' => $input['infoblock_id']
                ));
            }
        }
                
        $fields = array(
            $this->ui->hidden('content_type',$content_type),
            $this->ui->hidden('parent_id', $content['parent_id']),
            $this->ui->hidden('essence', 'content'),
            $this->ui->hidden('action', 'add_edit'),
            $this->ui->hidden('data_sent', true),
            $this->ui->hidden('fx_admin', true)
        );
        if (isset($input['content_id'])) {
            $fields []= $this->ui->hidden('content_id', $input['content_id']);
        } else {
            $fields []= $this->ui->hidden('infoblock_id', $input['infoblock_id']);
        }
        
        $this->response->add_fields($fields);

        $c_fields = array();
        $content_fields = $component->fields();
        foreach ($content_fields as $field) {
            if ($field['type_of_edit'] != 3) {
                $c_fields[] = $field->get_js_field($content);
            }
        }
        
        $this->response->add_tab('content', $component['name']);
        $this->response->add_fields($c_fields, 'content', 'content');

        if ($component['has_page']) {
            $this->response->add_tab('page', 'Страница');
            $page_component = fx::data('component', 'page');
            $p_fields = array();
            $page_component_fields = $page_component->fields();
            foreach ($page_component_fields as $p_field) {
                if ($p_field['type_of_edit'] != 3) {
                    $p_fields []= $p_field->get_js_field($page);
                }
            }
            $this->response->add_fields($p_fields, 'page', 'page');
        }
        
        if ($input['data_sent']) {
            $content->set_field_values($content_fields, $input['content']);
            $content->save();
            if ($component['has_page']) {
                $page->set_field_values($page_component_fields, $input['page']);
                $page['content_id'] = $content['id'];
                $page->save();
            }
        }
        return array('status' => 'ok');
    }

    public function add_save($input) {
        $fx_core = fx_core::get_object();
        $user = $fx_core->env->get_user();
        $result = array('status' => 'ok');
        $only_check = (bool) $input['fx_ajax_check'];

        $infoblock_id = $input['fx_infoblock'];
        $infoblock = fx::data('infoblock')->get_by_id($infoblock_id);
        if ( !$infoblock ) {
            die("Инфоблок не существует");
        }

        $subdivision = fx::data('subdivision')->get_by_id($infoblock['subdivision_id']);

        $component_id = $infoblock->get('essence_id');
        $component = fx::data('component')->get_by_id($component_id);
        $fields = $component->fields();

        $fx_content = fx::data('content')->create($component_id);
        $fx_content['parent_id'] = intval($input['parent_id']);
        $fx_content['infoblock_id'] = $infoblock_id;
        
        if ( $user && $user->perm()->is_supervisor() ) {
            // todo: seo и спец поля
        }

        // условие добавления объекта
        $tpl = $component->load_tpl_object( $infoblock['list_ctpl_id']);
        $tpl->set_vars('input', $input);
        $tpl->set_vars('fx_content', $fx_content);
        $tpl->set_vars('fx_infoblock', $infoblock);
        $tpl->set_vars('fx_subdivision', $subdivision);
        $tpl->set_vars('fx_component', $component);
        $tpl->set_vars('fx_fields', $fields);

        $tpl->add_cond();

        $err = $tpl->get_error();
        // условие добавления объекта не прошло
        if ($err) {
            $result = $err;
            $result['status'] = 'error';
        }

        // системные проверки
        if ( $result['status'] == 'ok' ) { 
            $result = $fx_content->set_field_values($fields, $input, $only_check);
        }
        
        if ($only_check) {
            return $result;
        } 

        if ($result['status'] == 'ok') {
            $fx_content->save();
            foreach ($fields as $v) {
                $v->post_save($fx_content);
            }
        }

        if ($input['fx_admin']) {
            return $result;
        }

        ob_start();
        if ($result['status'] == 'error') {
            $tpl->set_vars('result', $result);
            $tpl->begin_add_form();
            $tpl->add_form();
            $tpl->end_add_form();
        } else {
            $tpl->after_add();
        }
        
        $content = ob_get_clean();

        if ( $input['fx_naked'] ) {
            echo $content;
        }
        else {
            $page = new fx_controller_page();
            $page->set_main_content($content);
            $page->index();
        }
        
    }

    public function checked_save($input) {
        
        $ids = $input['id'];
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        foreach ($ids as $id) {
            if (preg_match("/(\d+)-(\d+)/", $id, $match)) {
                $class_id = $match[1];
                $content_id = $match[2];
            }

            $content = fx::data('content')->get_by_id($class_id, $content_id);
            $content->checked();
        }

        $result['status'] = 'ok';
        return $result;
    }

    public function on_save($input) {
        return $this->checked_save($input);
    }

    public function unchecked_save($input) {
        
        $ids = $input['id'];
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        foreach ($ids as $id) {
            if (preg_match("/(\d+)-(\d+)/", $id, $match)) {
                $class_id = $match[1];
                $content_id = $match[2];
            }

            $content = fx::data('content')->get_by_id($class_id, $content_id);
            $content->unchecked();
        }

        $result['status'] = 'ok';
        return $result;
    }

    public function off_save($input) {
        return $this->unchecked_save($input);
    }

    public function delete_save($input) {
        if (!isset($input['content_type']) || !isset($input['content_id'])) {
            return;
        }
        $content = fx::data('content_'.$input['content_type'], $input['content_id']);
        $content->delete();
        return array('status' => 'ok');
        if ($input['infoblock_id']) {
            $infoblock = fx::data('infoblock')->get_by_id($input['infoblock_id']);
        }
        if ($infoblock && $infoblock->is_manual_content_selection()) {
            $infoblock->delete_content($input['id']);
        } else {
            $ids = $input['id'];
            if (!is_array($ids)) {
                $ids = array($ids);
            }

            foreach ($ids as $id) {
                if (preg_match("/(\d+)-(\d+)/", $id, $match)) {
                    $class_id = $match[1];
                    $content_id = $match[2];
                }

                $content = fx::data('content')->get_by_id($class_id, $content_id);
                $content->delete();
            }
        }



        $result['status'] = 'ok';
        return $result;
    }
    
    /*
     * Переместить контент-запись среди соседей внутри одного родителя и одного инфоблока
     * В инпуте должны быть content_type и content_id
     * Если есть next_id - ставит перед ним
     * Если нет - ставит в конец
     */
    public function move($input) {
        $ctype = $input['content_type'];
        $content = fx::data('content_'.$ctype, $input['content_id']);
        $old_priority = $content['priority'];
        if (!$content) {
            return;
        }
        $ib_id = $content['infoblock_id'];
        $parent_id = $content['parent_id'];
        $next_content = null;
        if ($input['next_id']) {
            $next_content = fx::data('content_'.$ctype, $input['next_id']);
        }
        
        if ($next_content) {
            $new_priority = $next_content['priority']-1;
        } else {
            $last_priority = fx::db()->get_col(
                'SELECT MAX(priority) FROM {{content_'.$ctype.'}} 
                 WHERE parent_id = '.$parent_id.' AND infoblock_id = '.$ib_id
            );
            $new_priority = isset($last_priority[0]) ? $last_priority[0] : 1;
        }
        
        $q = "UPDATE {{content_".$ctype.'}} 
                SET priority = priority'.($new_priority > $old_priority ? '-1' : '+1').
                ' WHERE 
                    parent_id = '.$parent_id.' AND 
                    infoblock_id = '.$ib_id.' AND 
                    priority >= '.min($old_priority, $new_priority).  ' AND 
                    priority <='.max($old_priority, $new_priority);
        fx::db()->query($q);
        fx::db()->query('UPDATE {{content_'.$ctype.'}} 
                    SET priority = '.$new_priority.'
                    WHERE id = '.$content['id']);
    }
}

class fx_exception_controller_content extends fx_exception {
    
}

?>

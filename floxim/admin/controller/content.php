<?php

class fx_controller_admin_content extends fx_controller_admin {

    public function add($input) {
        $infoblock = fx::data('infoblock')->get_by_id($input['fx_infoblock']);
        if (!$infoblock) {
            throw new fx_exception_controller_content("Infoblock not found");
        }
        $component = fx::data('component')->get_by_id($infoblock['essence_id']);

        $component_fields = $component->fields();


        // ручной выбор объектов
        if ($infoblock->is_manual_content_selection()) {
            $infoblock_controller = new fx_controller_infoblock();
            return $infoblock_controller->choose_content($input);
        } else {
            $fields[] = array('name' => 'fx_infoblock', 'type' => 'hidden', 'value' => $infoblock['id'], 'tab' => 'main');
            $fields[] = array('name' => 'action', 'type' => 'hidden', 'value' => 'add', 'tab' => 'main');
            $fields[] = array('name' => 'essence', 'type' => 'hidden', 'value' => 'content', 'tab' => 'main');
            $fields[] = array('name' => 'posting', 'type' => 'hidden', 'value' => 1, 'tab' => 'main');
            $fields[] = array('name' => 'parent_id', 'type' => 'hidden', 'value' => intval($input['parent_id']));
            foreach ($component_fields as $field) {
                $fields[] = $field->get_js_field(array());
            }

            if ($component['with_full']) {
                $tabs['main'] = array('name' => 'Основные');
                $tabs['seo'] = array('name' => 'SEO');
                $fields[] = array('label' => 'H1', 'name' => 'seo_h1', 'value' => '', 'tab' => 'seo');
                $fields[] = array('label' => 'Title', 'name' => 'seo_title', 'value' => '', 'tab' => 'seo');
                $fields[] = array('label' => 'Keywords', 'name' => 'seo_keywords', 'value' => '', 'tab' => 'seo');
                $fields[] = array('label' => 'Description', 'name' => 'seo_description', 'value' => '', 'tab' => 'seo');
            }

            if ($tabs) $result['tabs'] = $tabs;

            $result['dialog_title'] = 'Добавление объекта в "'.$component['name'].'"';
        }


        $result['fields'] = $fields;

        return $result;
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

    public function edit($input) {
        if (preg_match("/(\d+)-(\d+)/", $input['id'], $match)) {
            $class_id = $match[1];
            $content_id = $match[2];
        } else if ( $input['class_id']) {
            $class_id = $input['class_id'];
            $content_id = ($input['content_id'] ? $input['content_id'] : $input['id']);
        }
        
        
        $content = fx::data('content')->set_component($class_id)->get_by_id($content_id);
        
        dev_log($content);

        $component = fx::data('component')->get_by_id($class_id);
        $component_fields = $component->fields();

        //$fields[] = array('name' => 'infoblock', 'type' => 'hidden', 'value' => $input['infoblock'] );
        $fields[] = array('name' => 'action', 'type' => 'hidden', 'value' => 'edit');
        $fields[] = array('name' => 'essence', 'type' => 'hidden', 'value' => 'content');
        $fields[] = array('name' => 'class_id', 'type' => 'hidden', 'value' => $class_id);
        $fields[] = array('name' => 'content_id', 'type' => 'hidden', 'value' => $content_id);
        $fields[] = array('name' => 'posting', 'type' => 'hidden', 'value' => 1);
        $fields[] = array('name' => 'fx_admin', 'type' => 'hidden', 'value' => 1);


        foreach ($component_fields as $field) {
            $fields[] = $field->get_js_field($content);
        }

        if (0) {
            $tabs['main'] = array('name' => 'Основные');
            $tabs['seo'] = array('name' => 'SEO');
            $fields[] = array('label' => 'H1', 'name' => 'seo_h1', 'value' => '', 'tab' => 'seo');
            $fields[] = array('label' => 'Title', 'name' => 'seo_title', 'value' => '', 'tab' => 'seo');
            $fields[] = array('label' => 'Keywords', 'name' => 'seo_keywords', 'value' => '', 'tab' => 'seo');
            $fields[] = array('label' => 'Description', 'name' => 'seo_description', 'value' => '', 'tab' => 'seo');
        }


        $res['dialog_title'] = 'Изменение объекта';
        $res['fields'] = $fields;
        //$res['tabs'] = $tabs;
        return $res;
    }

    public function edit_save($input) {
        $fx_core = fx_core::get_object();
        $user = $fx_core->env->get_user();
        $result = array('status' => 'ok');
        $only_check = (bool) $input['fx_ajax_check'];
        

        if (preg_match("/(\d+)-(\d+)/", $input['id'], $match)) {
            $class_id = $match[1];
            $content_id = $match[2];
        } else if ($input['class_id']) {
            $class_id = $input['class_id'];
            $content_id = ($input['content_id'] ? $input['content_id'] : $input['id']);
        }
        else {
            $infoblock = fx::data('infoblock')->get_by_id($input['fx_infoblock']);
            $class_id = $infoblock['essence_id'];
            $content_id = $input['content_id'];
            $subdivision = fx::data('subdivision')->get_by_id($infoblock['subdivision_id']);
        }

        $component = fx::data('component')->get_by_id($class_id);
        $fx_content = fx::data('content')->set_component($class_id)->get_by_id($content_id);
        dev_log($fx_content);

        $fields = $component->fields();

        
        if ( $user && $user->perm()->is_supervisor() ) {
            // todo: seo и спец поля
        }

        // условие изменения объекта
        $tpl = $component->load_tpl_object( $infoblock['list_ctpl_id']);
        $tpl->set_vars('input', $input);
        $tpl->set_vars('fx_content', $fx_content);
        $tpl->set_vars('fx_infoblock', $infoblock);
        $tpl->set_vars('fx_subdivision', $subdivision);
        $tpl->set_vars('fx_component', $component);
        $tpl->set_vars('fx_fields', $fields);

        $tpl->edit_cond();

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
            $tpl->begin_edit_form();
            $tpl->edit_form();
            $tpl->end_edit_form();
        } else {
            $tpl->after_edit();
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

    public function move_save($input) {
        $result = array('status' => 'ok');

        if (!$input['pos']) {
                return false;
        }

        $content = fx::data('content')->get_all($input['component_id'], '`id` IN ('.join(',', array_map('intval', $input['content'])).')');

        $low_pr = false;
        foreach ($content as $mes) {
            if ($low_pr === false) $low_pr = $mes['priority'];
            if ($mes['priority'] < $low_pr) $low_pr = 0;
        }

        foreach (array_reverse($input['pos']) as $id) {
            if (preg_match("/(\d+)-(\d+)/", $id, $match)) {
                $component_id = $match[1];
                $content_id = $match[2];
            }
            if ($component_id != $input['component_id']) continue;
            fx::data('content')->get_by_id($input['component_id'], $content_id)->set('priority', $low_pr++)->save();
        }

        return $result;
    }

}

class fx_exception_controller_content extends fx_exception {
    
}

?>

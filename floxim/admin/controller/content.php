<?php

class fx_controller_admin_content extends fx_controller_admin {

    public function add_edit($input) {
        // получаем редактируемый объект
        if ($input['content_id']) {
            $content = fx::data('content', $input['content_id']);
            $content_type = $content['type'];
        } else {
            $content_type = $input['content_type'];
            $parent_page = fx::data('content_page', $input['parent_id']);
            $content = fx::data('content_'.$content_type)->create(array(
                'parent_id' => $input['parent_id'],
                'infoblock_id' => $input['infoblock_id'],
                'checked' => 1,
                'site_id' => $parent_page['site_id']
            ));
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
        $this->response->add_fields($content->get_form_fields(), false, 'content');

        if ($input['data_sent']) {
            $content->set_field_values($input['content']);
            $content->save();
        }
        //$this->response->add_form_button('save');
        return array(
            'status' => 'ok', 
            'dialog_title' => 
            	($input['content_id'] ? 
                	fx::lang('Editing ', 'system') :
                	fx::lang('Adding new ', 'system')
				). ' '.fx::data('component', $content_type)->get('item_name')
        );
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
            if (!isset($input['id']) || !is_numeric($input['id'])) {
                return;
            }
            $content = fx::data('content', $input['id']);
        } else {
            $content = fx::data('content_'.$input['content_type'], $input['content_id']);
        }
        if (!$content) {
            return;
        }
        $content->delete();
        return array('status' => 'ok');
    }
    
    public function livesearch($input) {
        if (!isset($input['content_type'])) {
            return;
        }
        $content_type = $input['content_type'];
        $finder = fx::data($content_type);
        if (preg_match("~^content_~", $content_type) && $content_type !== 'content_user'){
            $finder->where('site_id', fx::env('site')->get('id'));
        }
        if (isset($input['skip_ids']) && is_array($input['skip_ids'])) {
            $finder->where('id', $input['skip_ids'], 'NOT IN');
        }
        $term = explode(" ", $_POST['term']);
        if (count($term) > 0) {
            foreach ($term as $tp) {
                $finder->where('name', '%'.$tp.'%', 'LIKE');
            }
        }
        if (isset($input['ids'])) {
            $finder->where('id', $input['ids']);
        }

        $items = $finder->all();
        $res = array('meta' => array(), 'results' => array());
        foreach ($items as $i) {
            $res['results'][]= array(
                'name' => $i['name'],
                'id' => $i['id']
            );
        }
        echo json_encode($res);
        die();
    }
    
    /*
     * Переместить контент-запись среди соседей внутри одного родителя и одного инфоблока
     * В инпуте должны быть content_type и content_id
     * Если есть next_id - ставит перед ним
     * Если нет - ставит в конец
     */
    public function move($input) {
        $content_type = 'content_'.$input['content_type'];
        //$content = fx::data($content_type, $input['content_id']);
        $content = fx::data($content_type)->with('tag')->where('id', $input['content_id'])->one();
        $next_id = isset($input['next_id']) ? $input['next_id'] : false;
        
        $neighbours = fx::data($content_type)->
                        where('parent_id', $content['parent_id'])->
                        where('infoblock_id', $content['infoblock_id'])->
                        where('id', $content['id'], '!=')->
                        with('tag')->
                        order('priority')->all();
        $nn = $neighbours->find('id', $next_id);
        
        $c_priority = 1;
        $next_found = false;
        foreach ($neighbours as $n) {
            if ($n['id'] == $next_id) {
                $content['priority'] = $c_priority;
                $content->save();
                $c_priority++;
                $next_found = true;
            }
            $n['priority'] = $c_priority;
            $n->save();
            $c_priority++;
        }
        if (!$next_found) {
            $content['priority'] = $c_priority;
            $content->save();
        }
    }
}

class fx_exception_controller_content extends fx_exception {
    
}
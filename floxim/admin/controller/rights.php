<?php

class fx_controller_admin_rights extends fx_controller {

    public function all($input) {
        $fx_core = fx_core::get_object();

        $items = array();
        if (preg_match('/(user|group)-([0-9]+)/i', $input['params'][0], $match)) {
            $essence_type = $match[1];
            $id = $match[2];
            $items = $fx_core->rights->get_all($essence_type . '_id', $id);
            $essence = $fx_core->$essence_type->get_by_id($id);
        }

        $ar = array('type' => 'list', 'filter' => true, 'tab' => 'fields');
        $ar['labels'] = array('name' => 'Право');
        if (empty($items)) {
            $fields[] = $this->ui->label('Нет никак прав');
            $ar['filter'] = false;
            unset($ar['labels']);
        }
      //  } else {
            
            
            $ar['values'] = array();

            foreach ($items as $item) {
                $name = constant("FX_ADMIN_PERMISSION_" . $item['type']);
                $el = array('id' => $item['id'], 'name' => $name);
                $ar['values'][] = $el;
            }

            $fields[] = $ar;
       // }




        $buttons = array("add", "delete");
        $buttons_action['add']['options']['to'] = $essence_type . '-' . $id;
        $result = array('fields' => $fields, 'buttons' => $buttons, 'buttons_action' => $buttons_action);
        $result['tree']['mode'] = 'user';
        $result['breadcrumbs'][] = array('name' => $essence['name'], 'url' => '#admin.user.full('.$essence['id'].')');
        $result['breadcrumbs'][] = array('name' => 'Права');
        
        return $result;
    }

    public function add($input) {

        $fields[] = $this->ui->label('В первой версии есть толькт право Директор');
        $fields[] = $this->ui->checkbox('director', 'Присовить право директора');

        $fields[] = $this->ui->hidden('to', $input['to']);
        $fields[] = $this->ui->hidden('posting');
        $fields[] = $this->ui->hidden('action', 'add');
        return array('fields' => $fields);
    }

    public function add_save($input) {
        $fx_core = fx_core::get_object();
        
        if ( $input['director'] && preg_match('/(user|group)-([0-9]+)/i', $input['to'], $match)) {
            $essence_type = $match[1];
            $id = $match[2];
            
            $data = array('type' => 1);
            $data[$essence_type.'_id'] = $id;
            $r = $fx_core->rights->create($data)->save();
        }
        
        return array('status' => 'ok');
    }

}

?>

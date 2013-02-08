<?php

class fx_controller_admin_settings extends fx_controller_admin {

    public function system() {
        $ar = array('1' => 'a', '2' => 'b', '3' => 'c');
        
        $fields[] = $this->ui->label('<h2>Простые элементы</h2>');
        $fields[] = $this->ui->input('t', 'Просто текст');
        
        $fields[]= array('value' => 'OO', 'label' => 'Текст с дефолтом', 'current' => 'трололо');
        
        $fields[] = $this->ui->text('ta', 'Много текста');
        $fields[] = $this->ui->hidden('myhid', 'очень тайное поле');
        $fields[] = $this->ui->checkbox('taa', 'Один единственный чекбокс');
        $fields[] = $this->ui->checkbox('taa', 'Группа чекбоксов', $ar, '2');
        $fields[] = $this->ui->radio('taa', 'Радиобаттоны', array('1' => 'a', '2' => 'b'), '2');
        $select = $this->ui->select('taa', 'Селект', array('1' => 'a', '2' => 'b'), '2');
        $select['extendable'] = true;
        $fields[] = $select;
        
        //$fields []= array('type' => 'iconselect', 'name' => 'istest'
        
        $fields[]= array('type' => 'radio_facet', 'values' => $ar, 'name' => 'rfac', 'value' => '2', 'label' => 'RadiofaceT');
        
        $fields[] = array('type' => 'select', 'name' => 'onehid', 'hidden_on_one_value' => true, 'values' => array('2' => 'first-and-last'));
        
        $fields[] = $this->ui->select('taa', 'Селект с возможностью выбора нескольких', $ar, array('1', '3'), 1);
        $fields[] = $this->ui->file('taa', 'Файл');
        $fields[] = $this->ui->color('taa', 'Цвет');
        
        $fields[] = $this->ui->label('<h2>Сложные элементы</h2>');
        $fields[] = array('name' => 'sort_fields', 'label' => 'Каждая настройка - это целый сет',  'type' => 'set',
                    'labels' => array('ввод раз', 'выбор два'),
                    'tpl' => array(
                        array('name' => 'field', 'type' => 'input'),
                        array('name' => 'order', 'type' => 'select', 'values' => $ar )),
                    'values' => array( array('field' => 'xxx', 'order' => '2'),array('field' => 'yyy', 'order' => '1')  )
                );
        
        
        $fields[] = array('id' => '_type', 'label' => 'А он с ребенком', 'name' => '_type', 'type' => 'radio', 'values' => array('new' => 'Этот без ребенка', 'copy' => 'Этот с ребенком'), 'value' => 'new' );
        $fields[] = array('name' => '_source', 'type' => 'select', 'values' => $ar, 'parent' => array('_type', 'copy'));

        $this->response->submenu->set_menu('settings');
        $result =array('fields' => $fields);
        return $result;
    }
    
    
    public function module ( $input ) {
        $fx_core = fx_core::get_object();
        $module_name = $input['params'][0];
        $result = $this->get_module_controller($module_name)->set_input($input)->settings($input);
        
        $result['tree']['mode'] = 'settings';
        $result['fields'][] = $this->ui->hidden('essence', 'admin_module_'.$module_name);
        $result['fields'][] = $this->ui->hidden('action', 'settings');
        $module = $fx_core->modules->get_by_keyword($module_name);
        $name = defined($module['name']) ? constant($module['name']) : $module['name'];
        $result['breadcrumbs'] = array('name' => $name);
        return $result;
        
    }
    
    protected function get_module_controller ( $name ) {
        $classname = 'fx_controller_admin_module_'.$name;
        $obj = new $classname;
        return $obj;
    }

}

?>

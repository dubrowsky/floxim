<?php

class fx_controller_admin_infoblock_widget extends fx_controller_admin_infoblock {

    public function get_widget_list($infoblock_info, $available_widgets) {
        $fx_core = fx_core::get_object();
        $result = array();

        $widgets = $fx_core->widget->get_all();
        foreach ($widgets as $widget) {
            $id = $widget->get_id();
            
            if (!fx_suitable::is_suitable($infoblock_info, $widget)) {
                continue;
            }

            $result[] = array('id' => 'widget------'.$id, 'name' => $widget['name'], 'group' => FX_ADMIN_WIDGETS, 'icon' => $widget->get_icon());
        }

        return $result;
    }

    public function add_main($input) {
        $fx_core = fx_core::get_object();

        $widget_id = $input['essence_id'] ? $input['essence_id'] : $input['essence_id_real'];
        $widget = $fx_core->widget->get_by_id($widget_id);

        $infoblock = false;
        if ($input['id']) {
            $infoblock = $fx_core->infoblock->get_by_id($input['id']);
        }

        $form = $widget->load_tpl_object()->set_vars('widget', $widget)->set_vars('infoblock', $infoblock)->add_form();

        $fields = array();
        if ($form)
                foreach ($form as $v) {
                if (strpos($v['name'], 'visual[') === false)
                        $v['name'] = 'visual['.$v['name'].']';
                $fields[] = $v;
            }
        $fields[] = $this->ui->hidden('posting');

        return $fields;
    }

}

?>

<?php

class fx_controller_admin_infoblock_content_block extends fx_controller_admin_infoblock_content {

    /**
     * Выбор шаблона компонента
     * @param type $input
     * @return array 
     */
    public function add_main($input) {
        $fx_core = fx_core::get_object();

        if ( $input['id'] ) {
            $infoblock = $fx_core->infoblock->get_by_id($input['id']);
            $first_ctpl = $infoblock['list_ctpl_id'];
        }
        else {
            $first_ctpl = false;
        }
        
        $infoblock_info = unserialize($input['infoblock_info']);
        $ctpls = $fx_core->ctpl->get_by_component($input['essence_id']);

        // выбор шаблона компонента
        foreach ($ctpls as $ctpl) {
            if (!$first_ctpl) {
                $first_ctpl = $ctpl['id'];
            }

            if ( $ctpl['notwidget'] &&  fx_suitable::is_suitable($infoblock_info,$ctpl )  ) {
                $values[$ctpl['id']] = $ctpl['name'];
            }
        }

        $fields[] = array('name' => 'list_ctpl_id', 'label' => FX_ADMIN_INFOBLOCK_CTPL, 'value' => $first_ctpl, 'values' => $values, 'type' => 'select', 'hidden_on_one_value' => true, 'post' => array('settings_step' => 'more'), 'whole' => true);

        return $fields;
    }

    public function add_more($input) {
        $fx_core = fx_core::get_object();
        $ctpl = $fx_core->ctpl->get_by_id($input['list_ctpl_id']);
        $fields = array();
        
        if ($input['id']) {
            $infoblock = $fx_core->infoblock->get_by_id($input['id']);
        } else {
            $infoblock = null;
        }

        $fields = array_merge($fields, $this->get_list_parametrs($ctpl, $infoblock) );
        $fields = array_merge($fields, $this->get_visual_settings($ctpl, $infoblock) );
        $fields = array_merge($fields, $this->get_content_block_settings($input) );

        $fields[] = array('name' => 'posting', 'type' => 'hidden', 'value' => 1);

        $result['fields'] = $fields;
        $result['layers'] = array('more' => array('on' => FX_ADMIN_LAYER_MORE_SHOW, 'off' => FX_ADMIN_LAYER_MORE_HIDE));
        return $result;
    }

    /**
     * Дополнительные параметры собственного контент-блока:
     * ключевое слово и действие по умолчанию
     * @param type $input
     * @return type 
     */
    protected function get_content_block_settings($input) {
        $fx_core = fx_core::get_object();

        // используемые компонент и шаблон
        $component = $fx_core->component->get_by_id($input['essence_id']);
        $ctpl = $fx_core->ctpl->get_by_id($input['list_ctpl_id']);

        // добавление/изменение
        if ($input['id']) {
            $ib = $fx_core->infoblock->get_by_id($input['id']);
            $name = $ib['name'];
            $url = $ib['url']; // ключевое слово
            $first_action = $ib['default_action']; // действие по умолчанию
        } else {
            $name_and_url = $this->find_name_and_keyword($component['name'], $component['keyword'], $input['subdivision_id']);
            $name = $name_and_url['name'];
            $url = $name_and_url['url'];
            $first_action = $ctpl->get_default_action();
        }


        $fields[] = array('name' => 'name', 'value' => $name, 'label' => "Название инфоблока", 'layer' => 'more');
        if ($ctpl['with_full']) {
            $fields[] = array('name' => 'url', 'value' => $url, 'label' => FX_ADMIN_KEYWORD.' (используется в URL)', 'layer' => 'more');
        } else {
            $fields[] = array('name' => 'url', 'value' => $url, 'type' => 'hidden', 'layer' => 'more');
        }


        // Доступные действия
        foreach ($ctpl->get_available_actions() as $action) {
            $values[$action] = constant('FX_ACTION_'.strtoupper($action));
        }
        $fields[] = array('name' => 'default_action', 'type' => 'select', 'hidden_on_one_value' => true, 'values' => $values, 'value' => $first_action, 'label' => FX_ADMIN_DEFAULT_ACTION, 'layer' => 'more');

        return $fields;
    }

}

?>

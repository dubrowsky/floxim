<?php
class fx_controller_component extends fx_controller {
    
    public function process($input = null, $action = null, $do_return = false) {
        $result = parent::process($input, $action, $do_return);
        $content_type = $this->get_content_type();
        $component = fx::data('component', $content_type);
        if ($this->action == 'listing') {
            $c_ib = fx::data('infoblock', $this->param('infoblock_id'));
            $adder_title = $component['name'].' &rarr; '.($c_ib['name'] ? $c_ib['name']:$c_ib['id']);
            $result['_meta'] = array(
                'accept_content' => array(array(
                    'title' => $adder_title,
                    'parent_id' => $this->_get_parent_id(),
                    'type' => $content_type,
                    'infoblock_id' => $this->param('infoblock_id')
                ))
            );
        }
        return $result;
    }
    
    /*
     * Общие настройки для списков - mirror | listing
     */
    public function get_action_settings_list_common() {
        $fields = array();
        $fields['limit'] = array(
            'name' => 'limit',
            'label' => 'Сколько выводить',
            'value' => 10
        );

        $sortings = array('manual' => 'Ручная', 'created'=> 'Дата создания');
        $sortings += $this->get_component()->fields()->get_values('description', 'name');
        $fields['sorting'] = array(
            'name' => 'sorting',
            'label' => 'Сортировка',
            'type' => 'select',
            'values' => $sortings
        );
        $fields['sorting_dir'] = array(
            'name' => 'sorting_dir',
            'label' => 'Порядок',
            'type' => 'select',
            'values' => array('asc' => 'По возрастанию', 'desc' => 'По убыванию'),
            'parent' => array('sorting' => '!=manual')
        );
        return $fields;
    }
    
    public function get_action_settings_listing() {
        $fields = $this->get_action_settings_list_common();
        $fields['parent_type'] = array(
            'name' => 'parent_type',
            'label' => 'Родитель',
            'type' => 'select',
            'values' => array(
                'current_page_id' => 'Текущая страница', 
                'mount_page_id' => 'Страница, куда прицеплен инфоблок',
                'custom' => 'Произвольный'
            )
        );
        $fields['parent_id']= array(
            'name' => 'parent_id',
            'label' => 'Выбрать родителя',
            'parent' => array('parent_type' => 'custom')
        );
        return $fields;
    }
    
    public function get_action_settings_mirror() {
        $fields = $this->get_action_settings_list_common();
        $fields['from_all'] = array(
            'name' => 'from_all',
            'label' => 'Из любого раздела',
            'type' => 'checkbox',
            'parent' => array('is_mirror' => '1'),
            'value' => 1
        );
        $possible_infoblocks = fx::data('infoblock')->get_content_infoblocks($this->get_content_type());
        $source_values =  array();
        foreach ($possible_infoblocks as $ib) {
            $source_values[$ib['id']] = $ib['name'];
        }
        if (false && count($source_values) > 0) {
            $fields['source_infoblocks'] = array(
                'name' => 'source_infoblocks', 
                'label' => '', 
                'type' => 'checkbox', 
                'values' => $source_values, 
                'value' => array(),
                'parent' => array('from_all'=>'0')
            );
        }
        $fields['parent_id'] = array(
            'name' => 'parent_id',
            'label' => 'Указать раздел явно',
            'parent' => array('from_all' => '0')
        );
        return $fields;
    }


    public function listing() {
        $f = $this->_finder();
        $params = array();
        $params['infoblock_id']= $this->param('infoblock_id');
        $infoblock = fx::data('infoblock', $this->param('infoblock_id'));
        $params['parent_id']= $this->_get_parent_id();
        $items = $f->get_all($params);
        fx::data('content_page')->attache_to_content($items);
        return array('items' => $items);
    }
    
    protected function _get_parent_id() {
        $ib = fx::data('infoblock', $this->param('infoblock_id'));
        $parent_id = null;
        switch($this->param('parent_type')) {
            case 'mount_page_id':
                $parent_id = $ib['page_id'];
                break;
            case 'current_page_id':
                $parent_id = fx::env('page');
                break;
            case 'custom':
                $parent_id = $this->param('parent_id');
                break;
        }
        return $parent_id;
    }
    
    public function mirror() {
        $f = $this->_finder();
        $params = array();
        if ( ($parent_id = $this->param('parent_id')) ) {
            $params['parent_id'] = $parent_id;
        }
        $items = $f->get_all($params);
        fx::data('content_page')->attache_to_content($items);
        dev_log('mirror component', $this, $items);
        return array('items' => $items);
    }
    
    public function item() {
        
    }
    
    protected $_content_type = null;
    
    public function get_content_type() {
        return $this->_content_type;
    }
    
    public function set_content_type($content_type) {
        $this->_content_type = $content_type;
    }
    
    public function get_component() {
        $ct = $this->get_content_type();
        $component = fx::data('component')->get('keyword', $ct);
        return $component;
    }
    
    /**
     * @return fx_data_content data finder
     */
    protected function _finder() {
        return fx::data('content_'.$this->get_content_type());
    }
}
?>
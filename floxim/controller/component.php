<?php
class fx_controller_component extends fx_controller {
    
    public function get_action_settings($action) {
        $fields = array();
        switch ($action) {
            case 'listing':
                $fields ['is_mirror']= array('name' => 'is_mirror', 'type' => 'bool', 'label' => 'Mirror?');
                $fields ['url']= array(
                    'name' => 'url', 
                    'label' => 'Часть URL', 
                    'type' => 'input', 
                    'value' => $this->get_content_type(), 
                    'parent' => array('is_mirror' => '0')
                );
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
                if (count($source_values) > 0) {
                    $fields['source_infoblocks'] = array(
                        'name' => 'source_infoblocks', 
                        'label' => '', 
                        'type' => 'checkbox', 
                        'values' => $source_values, 
                        'value' => array(),
                        'parent' => array('from_all'=>'0', 'is_mirror' => '1')
                    );
                }
                break;
        }
        
        
        return $fields;
    }
    
    public function listing() {
        $f = $this->_finder();
        $params = array();
        if ($infoblock_id = $this->param('infoblock_id')) {
            $params ['infoblock_id']= $infoblock_id;
        }
        if ($parent_id = $this->param('parent_id')) {
            $params['parent_id']= $parent_id;
        }
        $items = $f->get_all($params);
        fx::data('content_page')->attache_to_content($items);
        return $items;
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
        $component = fx::data('component')->get_all('keyword', $ct);
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
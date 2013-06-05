<?php
class fx_controller_component extends fx_controller {
    
    protected $_meta = array();
    
    protected $_action_prefix = 'do_';


    public function process() {
        $result = parent::process();
        if (!isset($result['_meta'])) {
            $result['_meta'] = array();
        }
        $result['_meta'] = array_merge_recursive($result['_meta'], $this->_meta);
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
        $fields['show_pagination'] = array(
            'name' => 'show_pagination',
            'label' => 'Разбивать на страницы?',
            'type' => 'checkbox',
            'value' => true,
            'parent' => array('limit' => '!=0')
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

    public function get_action_settings_list_parrent()
    {
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
    
    public function get_action_settings_listing() {
        $fields = $this->get_action_settings_list_common();
        $fields = array_merge($fields,$this->get_action_settings_list_parrent());
        return $fields;
    }
    
    public function get_action_settings_listing_mirror() {
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
    
    protected $_bound = array();
    public function listen($event, $callback) {
        if (!isset($this->_bound[$event])) {
            $this->_bound[$event] = array();
        }
        $this->_bound[$event][]= $callback;
    }
    
    public function trigger($event, $data = null) {
        if (isset($this->_bound[$event]) && is_array($this->_bound[$event])) {
            foreach ( $this->_bound[$event] as $cb) {
                call_user_func($cb, $data);
            }
        }
    }

    public function info_record() {
        return array(
            'name' => 'Запись',
            'description' => 'Выводит отдельную запись'
        );
    }
    
    public function do_record() {
        $page = fx::data('content_page', fx::env('page'));
        return array('items' => $page);
    }
    
    public function info_listing() {
        return array(
            'name' => 'Список',
            'description' => 'Выводит список записей из указанного раздела'
        );
    }

    public function do_listing() {
        $f = $this->_get_finder();
        
        $content_type = $this->get_content_type();
        if ( ($infoblock_id = $this->param('infoblock_id'))) {
            $c_ib = fx::data('infoblock', $infoblock_id);
            $f->where('infoblock_id', $c_ib->get_root_infoblock()->get('id'));
        }
        $f->where('parent_id', $this->_get_parent_id());
        $this->trigger('build_query',$f);

        if ( ($sorting = $this->param('sorting')) ) {
            if ($sorting == 'manual') {
                $f->order('priority');
            } else {
                $f->order($sorting, $this->param('sorting_dir'));
            }
        }
        $this->trigger('query_ready', $f);
        $items = $f->all();
        $this->trigger('items_ready', $items);
        

        if (fx::env('is_admin') && $infoblock_id) {
            $c_ib_name = $c_ib->get_prop_inherited('name');
            $component = fx::data('component', $content_type);
            $adder_title = $component['item_name'].' &rarr; '.($c_ib_name ? $c_ib_name:$c_ib['id']);
            $this->_meta['accept_content'] = array(
                array(
                    'title' => $adder_title,
                    'parent_id' => $this->_get_parent_id(),
                    'type' => $content_type,
                    'infoblock_id' => $this->param('infoblock_id')
                )
            );
        }
        return array('items' => $items, 'pagination' => $this->_get_pagination());
    }
    
    protected function _get_pagination() {
        if (!$this->param('show_pagination')){
            return null;
        }
        $total_rows = $this->_get_finder()->get_found_rows();
        $limit = $this->param('limit');
        $total_pages = ceil($total_rows / $limit);
        $result = array();
        foreach (range(1, $total_pages) as $page_num) {
            $result[]= array(
                'active' => $page_num == 3, //$page_num == $_GET['page'],
                'page' => $page_num,
                'url' => '#page_'.$page_num
            );
        }
        return $result;
    }
    
    protected function _get_parent_id() {
        $ib = fx::data('infoblock', $this->param('infoblock_id')); 
        if (!$ib) {
            return $this->param('parent_id');
        }
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
    
    
    public function info_listing_mirror() {
        return array(
            'name' => 'Mirror',
            'description' => 'Выводит записи по произвольному фильтру'
        );
    }
    
    public function do_listing_mirror() {
        $f = $this->_get_finder();
        ///$params = array();
        if ( ($parent_id = $this->param('parent_id')) ) {
            //$params['parent_id'] = $parent_id;
            $f->where('parent_id', $parent_id);
        }
        $items = $f->all();
        //fx::data('content_page')->attache_to_content($items);
        return array('items' => $items);
    }
    

     /**
     * $_content_type может быть одним из значений
     * в таблице fx_component в поле keyword
     * @var string 
     */
    protected $_content_type = null;
    
    /**
     * @return string
     */
    public function get_content_type() {
        if (!$this->_content_type) {
            if (preg_match("~fx_controller_component_(.+)$~", get_class($this), $cc)) {
                $this->_content_type = $cc[1];
            }
        }
        return $this->_content_type;
    }
    
    /**
     * @param string $content_type
     */
    public function set_content_type($content_type) {
        $this->_content_type = $content_type;
    }
    
    /**
     * Возвращает компонент по значение свойства _content_type
     * @return fx_data_component
     */
    public function get_component() {
        $ct = $this->get_content_type();
        $component = fx::data('component')->get('keyword', $ct);
        return $component;
    }
    
    
    protected $_finder = null;
    /**
     * @return fx_data_content data finder
     */
    protected function _get_finder() {
        if ($this->_finder) {
            return $this->_finder;
        }
        $finder = fx::data('content_'.$this->get_content_type());
        if ($this->param('show_pagination')) {
            $finder->calc_found_rows();
        }
        if ( ($limit = $this->param('limit'))) {
            $finder->limit($limit);
        }
        $this->_finder = $finder;
        return $finder;
    }
    
    public function find_template() {
        $tpl_name = 'component_'.$this->get_content_type().".".$this->action;
        return fx::template($tpl_name);
    }
    
    protected function _get_controller_variants() {
        $vars = parent::_get_controller_variants();
        $chain = array_reverse($this->get_component()->get_chain());
        foreach ($chain as $chain_item) {
            $vars []= 'component_'.$chain_item['keyword'];
        }
        return array_unique($vars);
    }
}
?>
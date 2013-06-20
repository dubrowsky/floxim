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
            'label' => fx_lang('Сколько выводить'),
            'value' => 10
        );
        $fields['show_pagination'] = array(
            'name' => 'show_pagination',
            'label' => fx_lang('Разбивать на страницы?'),
            'type' => 'checkbox',
            'value' => true,
            'parent' => array('limit' => '!=0')
        );

        $sortings = array('manual' => fx_lang('Ручная'), 'created'=> fx_lang('Дата создания'));
        $sortings += $this->get_component()->fields()->get_values('description', 'name');
        $fields['sorting'] = array(
            'name' => 'sorting',
            'label' => fx_lang('Сортировка'),
            'type' => 'select',
            'values' => $sortings
        );
        $fields['sorting_dir'] = array(
            'name' => 'sorting_dir',
            'label' => fx_lang('Порядок'),
            'type' => 'select',
            'values' => array('asc' => fx_lang('По возрастанию'), 'desc' => fx_lang('По убыванию')),
            'parent' => array('sorting' => '!=manual')
        );
        return $fields;
    }

    public function get_action_settings_list_parent()
    {
        $fields['parent_type'] = array(
            'name' => 'parent_type',
            'label' => fx_lang('Родитель'),
            'type' => 'select',
            'values' => array(
                'current_page_id' => fx_lang('Текущая страница'),
                'mount_page_id' => fx_lang('Страница, куда прицеплен инфоблок'),
                'custom' => fx_lang('Произвольный')
            )
        );
        $fields['parent_id']= array(
            'name' => 'parent_id',
            'label' => fx_lang('Выбрать родителя'),
            'parent' => array('parent_type' => 'custom')
        );
        return $fields;
    }
    
    public function get_action_settings_listing() {
        $fields = $this->get_action_settings_list_common();
        $fields = array_merge($fields,$this->get_action_settings_list_parent());
        return $fields;
        /*
         * Ниже код, который добывает допустимые инфоблоки для полей-ссылок
         * и предлагает выбрать, откуда брать/куда добавлять значения-ссылки
         * временно не используем из-за непонятного гуя 
         */
        $link_fields = $this->
                            get_component()->
                            all_fields()->
                            find('type', array(fx_field::FIELD_LINK, fx_field::FIELD_MULTILINK))->
                            find('type_of_edit', fx_field::EDIT_NONE, fx_collection::FILTER_NEQ);
        
        foreach ($link_fields as $lf) {
            if ($lf['type'] == fx_field::FIELD_LINK) {
                $target_com_id = $lf['format']['target'];
            } else {
                $target = explode(".", $lf['format']['target']);
                $target_com_id = fx::data('field', $target[0])->get('component_id');
            }
            $target_com = fx::data('component', $target_com_id);
            $com_infoblocks = fx::data('infoblock')->
                    where('site_id', fx::env('site')->get('id'))->
                    get_content_infoblocks($target_com['keyword']);
            $ib_values = $com_infoblocks->get_values('name', 'id') + array('new' => fx_lang('Новый инфоблок'));
            $fields ['field_'.$lf['id'].'_infoblock']= array(
                'type' => 'select',
                'values' => $ib_values,
                'name' => 'field_'.$lf['id'].'_infoblock',
                'label' => fx_lang('Инфоблок для поля ').$lf['description']
            );/*
            echo fen_debug(
                    'we are '.  get_class($this), 
                    'field: '.$lf['name'], 
                    'com: '.$target_com['name'],
                    $com_infoblocks
            );*/
        }
        return $fields;
    }
    
    public function get_action_settings_listing_mirror() {
        $fields = $this->get_action_settings_list_common();
        $fields['from_all'] = array(
            'name' => 'from_all',
            'label' => fx_lang('Из любого раздела'),
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
            'label' => fx_lang('Указать раздел явно'),
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
            'name' => fx_lang('Запись'),
            'description' => fx_lang('Выводит отдельную запись')
        );
    }
    
    public function do_record() {
        $page = fx::data('content_page', fx::env('page'));
        return array('items' => $page);
    }
    
    public function info_listing() {
        return array(
            'name' => fx_lang('Список'),
            'description' => fx_lang('Выводит список записей из указанного раздела')
        );
    }

    public function do_listing() {
        $f = $this->_get_finder();
        
        $content_type = $this->get_content_type();
        if ( ($infoblock_id = $this->param('infoblock_id'))) {
            $c_ib = fx::data('infoblock', $infoblock_id);
            $f->where('infoblock_id', $c_ib->get_root_infoblock()->get('id'));
        }
        if ( ($parent_id = $this->_get_parent_id()) ) {
            $f->where('parent_id', $this->_get_parent_id());
        }
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
            
            $this->accept_content(array(
                'title' => $adder_title,
                'parent_id' => $this->_get_parent_id(),
                'type' => $content_type,
                'infoblock_id' => $this->param('infoblock_id')
            ));
        }
        $res = array('items' => $items);
        if ( ($pagination = $this->_get_pagination()) ) {
            $res ['pagination'] = $pagination;
        }
        return $res;
    }
    
    public function accept_content($params) {
        $params = array_merge(
            array(
                'infoblock_id' => $this->param('infoblock_id'),
                'type' => $this->get_content_type()
            ), $params
        );
        if (!isset($this->_meta['accept_content'])) {
            $this->_meta['accept_content'] = array();
        }
        $this->_meta['accept_content'] []= $params;
    }
    
    protected function _get_pagination_url_template() {
        $url = $_SERVER['REQUEST_URI'];
        $url = preg_replace("~[\?\&]page=\d+~", '', $url);
        return $url.'##'.(preg_match("~\?~", $url) ? '&' : '?').'page=%d##';
    }
    
    protected function _get_current_page() {
        return isset($_GET['page']) ? $_GET['page'] : 1;
    }

    protected function _get_pagination() {
        if (!$this->param('show_pagination')){
            return null;
        }
        $total_rows = $this->_get_finder()->get_found_rows();
        if ($total_rows == 0) {
            return null;
        }
        $limit = $this->param('limit');
        if ($limit == 0) {
            return null;
        }
        $total_pages = ceil($total_rows / $limit);
        if ($total_pages == 1) {
            return null;
        }
        $result = array();
        $url_tpl = $this->_get_pagination_url_template();
        $base_url = preg_replace('~##.*?##~', '', $url_tpl);
        $url_tpl = str_replace("##", '', $url_tpl);
        $c_page = $this->_get_current_page();
        foreach (range(1, $total_pages) as $page_num) {
            $result[]= array(
                'active' => $page_num == $c_page,
                'page' => $page_num,
                'url' => 
                    $page_num == 1 ? 
                    $base_url : 
                    sprintf($url_tpl, $page_num)
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
            'description' => fx_lang('Выводит записи по произвольному фильтру')
        );
    }
    
    public function do_listing_mirror() {
        $f = $this->_get_finder();
        if ( ($parent_id = $this->param('parent_id')) ) {
            $f->where('parent_id', $parent_id);
        }
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
        $show_pagination = $this->param('show_pagination');
        $c_page = $this->_get_current_page();
        $limit = $this->param('limit');
        if ( $show_pagination && $limit) {
            $finder->calc_found_rows();
        }
        if ( $limit ) {
            if ($show_pagination && $c_page != 1) {
                $finder->limit(
                        $limit*($c_page-1),
                        $limit
                );
            } else {
                $finder->limit($limit);
            }
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
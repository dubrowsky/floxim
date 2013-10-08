<?php
class fx_controller_component extends fx_controller {
    
    protected $_meta = array();
    
    protected $_action_prefix = 'do_';

    public function process() {
        $result = parent::process();
        if (is_string($result)) {
            return $result;
        }
        if (!isset($result['_meta'])) {
            $result['_meta'] = array();
        }
        $result['_meta'] = array_merge_recursive($result['_meta'], $this->_meta);
        return $result;
    }
    
    /*
     * Общие настройки для списков - mirror | listing
     */
    protected function _settings_list_common() {
        $fields = array();
        $fields['limit'] = array(
            'name' => 'limit',
            'label' => fx::lang('How many entries to display','controller_component'),
            'value' => 10
        );
        $fields['show_pagination'] = array(
            'name' => 'show_pagination',
            'label' => fx::lang('Show pagination?','controller_component'),
            'type' => 'checkbox',
            'value' => true,
            'parent' => array('limit' => '!=0')
        );

        $sortings = array(
            'manual' => '-'.fx::lang('Manual','controller_component').'-', 
            'created'=> fx::lang('Created','controller_component')
        );
        $sortings += $this
            ->get_component()
            ->all_fields()
            ->find('type', fx_field::FIELD_MULTILINK, '!=')
            ->get_values('description', 'name');
        $fields['sorting'] = array(
            'name' => 'sorting',
            'label' => fx::lang('Sorting','controller_component'),
            'type' => 'select',
            'values' => $sortings
        );
        $fields['sorting_dir'] = array(
            'name' => 'sorting_dir',
            'label' => fx::lang('Order','controller_component'),
            'type' => 'select',
            'values' => array('asc' => fx::lang('Ascending','controller_component'), 'desc' => fx::lang('Descending','controller_component')),
            'parent' => array('sorting' => '!=manual')
        );
        return $fields;
    }

    protected function _settings_list_parent()
    {
        $fields['parent_type'] = array(
            'name' => 'parent_type',
            'label' => fx::lang('Parent','controller_component'),
            'type' => 'select',
            'values' => array(
                'current_page_id' => fx::lang('Current page','controller_component'),
                'mount_page_id' => fx::lang('The infoblock owner section','controller_component')
            ),
            'parent' => array('scope[pages]' => '!=this')
        );
        return $fields;
    }
    
    public function settings_listing() {
        $fields = array_merge(
            $this->_settings_list_common(),
            $this->_settings_list_parent()
        );
        //return $fields;
        /*
         * Ниже код, который добывает допустимые инфоблоки для полей-ссылок
         * и предлагает выбрать, откуда брать/куда добавлять значения-ссылки
         * возможно, откажемся из-за непонятного гуя 
         */
        $link_fields = $this->
                            get_component()->
                            all_fields()->
                            find('type', array(fx_field::FIELD_LINK, fx_field::FIELD_MULTILINK))->
                            find('type_of_edit', fx_field::EDIT_NONE, fx_collection::FILTER_NEQ);
        
        foreach ($link_fields as $lf) {
            //dev_log('lf', $lf);
            //continue;
            if ($lf['type'] == fx_field::FIELD_LINK) {
                $target_com_id = $lf['format']['target'];
            } else {
                $target_com_id = isset($lf['format']['mm_datatype']) 
                                    ? $lf['format']['mm_datatype']
                                    : $lf['format']['linking_datatype'];
            }
            $target_com = fx::data('component', $target_com_id);
            if (!$target_com) {
                dev_log('no tcom', $lf);
                continue;
            }
            $com_infoblocks = fx::data('infoblock')->
                    where('site_id', fx::env('site')->get('id'))->
                    get_content_infoblocks($target_com['keyword']);
            //$ib_values = $com_infoblocks->get_values('name', 'id'); // + array('new' => fx::lang('New infoblock', 'controller_component'));
            $ib_values = array();
            foreach ($com_infoblocks as $ib) {
                $ib_values []= array($ib['id'], $ib['name']);
            }
            if (count($ib_values) === 0) {
                continue;
            }
            $c_ib_field = array(
                'name' => 'field_'.$lf['id'].'_infoblock'
            );
            if (count($ib_values) === 1) {
                $c_ib_field += array(
                    'type' => 'hidden',
                    'value' => $ib_values[0][0]
                );
            } else {
                $c_ib_field += array(
                    'type' => 'select',
                    'values' => $ib_values,
                    'label' => fx::lang('Infoblock for the field', 'controller_component')
                                .' "'.$lf['description'].'"'
                );
            }
            $fields []= $c_ib_field;
        }
        return $fields;
    }
    
    public function get_action_settings_listing_mirror() {
        $fields = $this->get_action_settings_list_common();
        $fields['from_all'] = array(
            'name' => 'from_all',
            'label' => fx::lang('From all sections','controller_component'),
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
            'label' => fx::lang('From specified section','controller_component'),
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
                call_user_func($cb, $data, $this);
            }
        }
    }

    public function info_record() {
        return array(
            'name' => fx::lang('Entry','controller_component'),
            'description' => fx::lang('Show single entry','controller_component')
        );
    }
    
    public function do_record() {
        $page = fx::data('content_page', fx::env('page'));
        return array('items' => $page);
    }
    
    public function info_listing() {
        return array(
            'name' => fx::lang('List','controller_component'),
            'description' => fx::lang('Show entries from the specified section','controller_component')
        );
    }
    
    public function defaults_listing() {
        return array(
            'scope' => array(
                'page_id' => fx::env('page'),
                'pages' => 'this'
            )
        );
    }
    
    protected function _list() {
        $f = $this->_get_finder();
        $this->trigger('query_ready', $f);
        $items = $f->all();
        if (count($items) === 0) {
            $this->_meta['hidden'] = true;
        }
        $this->trigger('items_ready', $items);
        $res = array('items' => $items);
        if ( ($pagination = $this->_get_pagination()) ) {
            $res ['pagination'] = $pagination;
        }
        return $res;
    }

    public function do_listing() {
        $f = $this->_get_finder();
        
        $content_type = $this->get_content_type();
        if ( ($infoblock_id = $this->get_param('infoblock_id'))) {
            $c_ib = fx::data('infoblock', $infoblock_id);
            if ($c_ib && !$this->get_param('skip_infoblock_filter')) {
                $f->where('infoblock_id', $c_ib->get_root_infoblock()->get('id'));
            }
        }
        if ( ($parent_id = $this->_get_parent_id()) && !($this->get_param('skip_parent_filter')) ) {
            $f->where('parent_id', $this->_get_parent_id());
        }
        $this->trigger('build_query',$f);

        if ( ($sorting = $this->get_param('sorting')) ) {
            if ($sorting == 'manual') {
                $f->order('priority');
            } else {
                $f->order($sorting, $this->get_param('sorting_dir'));
            }
        }
        $this->trigger('query_ready', $f);
        if ($this->get_param('is_fake')) {
            // dirty hack
            $f->where('id', -1);
        }
        $items = $f->all();
        $this->trigger('items_ready', $items);
        
        if ($this->get_param('is_fake')) {
            foreach (range(0, 3) as $cnt) {
                $items[]= $f->fake();
            }
        }

        if (count($items) == 0) {
            $this->_meta['hidden'] = true;
        }
        if (fx::env('is_admin') && $c_ib) {
            $c_ib_name = $c_ib->get_prop_inherited('name');
            $c_ib_name = $c_ib_name ? $c_ib_name : $c_ib['id'];
            $component = fx::data('component', $content_type);
            $adder_title = $component['item_name'].' &rarr; '.$c_ib_name;
            
            $this->accept_content(array(
                'title' => $adder_title,
                'parent_id' => $this->_get_parent_id(),
                'type' => $content_type,
                'infoblock_id' => $this->get_param('infoblock_id')
            ));
            
            if (count($items) == 0) {
                $this->_meta['hidden_placeholder'] = 'Infoblock "'.$c_ib_name.'" is empty. '.
                                                'You can add '.$component['item_name'].' here';
            }
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
                'infoblock_id' => $this->get_param('infoblock_id'),
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
        if (!$this->get_param('show_pagination')){
            return null;
        }
        $total_rows = $this->_get_finder()->get_found_rows();
        if ($total_rows == 0) {
            return null;
        }
        $limit = $this->get_param('limit');
        if ($limit == 0) {
            return null;
        }
        $total_pages = ceil($total_rows / $limit);
        if ($total_pages == 1) {
            return null;
        }
        $links = array();
        $url_tpl = $this->_get_pagination_url_template();
        $base_url = preg_replace('~##.*?##~', '', $url_tpl);
        $url_tpl = str_replace("##", '', $url_tpl);
        $c_page = $this->_get_current_page();
        foreach (range(1, $total_pages) as $page_num) {
            $links[$page_num]= array(
                'active' => $page_num == $c_page,
                'page' => $page_num,
                'url' => 
                    $page_num == 1 ? 
                    $base_url : 
                    sprintf($url_tpl, $page_num)
            );
        }
        $res = array(
            'links' => fx::collection($links),
            'total_pages' => $total_pages,
            'total_items' => $total_rows,
            'current_page' => $c_page
        );
        if ($c_page != 1) {
            $res['prev'] = $links[$c_page-1]['url'];
        }
        if ($c_page != $total_pages) {
            $res['next'] = $links[$c_page+1]['url'];
        }
        return $res;
    }
    
    protected function _get_parent_id() {
        $ib = fx::data('infoblock', $this->get_param('infoblock_id')); 
        if (!$ib) {
            return $this->get_param('parent_id');
        }
        $parent_id = null;
        switch($this->get_param('parent_type')) {
            case 'mount_page_id':
                $parent_id = $ib['page_id'];
                break;
            case 'current_page_id':
            default:
                $parent_id = fx::env('page');
                break;
            case 'custom':
                $parent_id = $this->get_param('parent_id');
                break;
        }
        return $parent_id;
    }
    
    
    public function info_listing_mirror() {
        return array(
            'name' => 'Mirror',
            'description' => fx::lang('Show entries by filter','controller_component')
        );
    }
    
    public function do_listing_mirror() {
        $f = $this->_get_finder();
        if ( ($parent_id = $this->get_param('parent_id')) ) {
            $f->where('parent_id', $parent_id);
        }
        $this->trigger('build_query',$f);

        if ( ($sorting = $this->get_param('sorting')) ) {
            if ($sorting == 'manual') {
                $f->order('priority');
            } else {
                $f->order($sorting, $this->get_param('sorting_dir'));
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
        $show_pagination = $this->get_param('show_pagination');
        $c_page = $this->_get_current_page();
        $limit = $this->get_param('limit');
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
        dev_log(debug_backtrace());
        $chain = array_reverse($this->get_component()->get_chain());
        foreach ($chain as $chain_item) {
            $vars []= 'component_'.$chain_item['keyword'];
        }
        return array_unique($vars);
    }
}
?>
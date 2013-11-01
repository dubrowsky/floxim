<?php
class fx_controller_component extends fx_controller {
    
    protected $_meta = array();
    
    protected $_action_prefix = 'do_';

    protected function _count_parent_id() {
        if (preg_match("~^list_infoblock~", $this->action)) {
            $this->set_param('parent_id', $this->_get_parent_id());
        }
    }
    
    public function process() {
        $this->listen('before_action_run', array($this, '_count_parent_id'));
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
    
    protected function _get_config_sources() {
        $sources = array();
        $com_dir = fx::config()->DOCUMENT_ROOT.'/component/';
        $sources []= fx::config()->DOCUMENT_ROOT.'/floxim/controller/component.cfg.php';
        $chain = $this->get_component()->get_chain();
        foreach ($chain as $com) {
            $com_file = fx::config()->DOCUMENT_ROOT.'/floxim/std/component/'.$com['keyword'].'/'.$com['keyword'].'.cfg.php';
            if (file_exists($com_file)) {
                $sources[]= $com_file;
            }
            $com_file = $com_dir.$com['keyword'].'/'.$com['keyword'].'.cfg.php';
            if (file_exists($com_file)) {
                $sources[]= $com_file;
            }
        }
        return $sources;
    }
    
    public function after_save() {
        if (isset($this->action)) {
            switch ($this->action) {
                case 'list_selected':
                /*
                        $linkers = fx::data('content_select_linker')
                                    ->all();
                        foreach ($linkers as $linker) {
                            $linker->delete();
                        }
                */
                    if (isset($this->input['params']['selected']) && is_array($this->input['params']['selected'])) {
                        $linkers = fx::data('content_select_linker')
                                    ->where('infoblock_id', $this->input['id'])
                                    ->where('parent_id', $this->input['page_id'])
                                    ->all();
                        foreach ($linkers as $linker) {
                            $linker->delete();
                        }
                        foreach ($this->input['params']['selected'] as $value) {
                            $linker = fx::data('content_select_linker')->create();
                            $linker['parent_id'] = $this->input['page_id'];
                            $linker['infoblock_id'] = $this->input['id'];
                            $linker['linked_id'] = $value[0];
                            $linker->save();
                        }
                    } else {
                        $linkers = fx::data('content_select_linker')
                                    ->where('infoblock_id', $this->input['id'])
                                    ->all();
                        foreach ($linkers as $linker) {
                            $linker->delete();
                        }
                    }
                    break;
                
                default:
                    break;
            }
        }
    }
    public function after_delete() {
        if (isset($this->action)) {
            switch ($this->action) {
                case 'list_selected':
                    $linkers = fx::data('content_select_linker')
                                ->where('infoblock_id', $this->input['id'])
                                ->all();
                    foreach ($linkers as $linker) {
                        $linker->delete();
                    }
                    break;
                default:
                    break;
            }
        }
    }

    public function config_list($config) {
        $sortings = array(
            'created'=> fx::lang('Created','controller_component')
        ) + $this
            ->get_component()
            ->all_fields()
            ->find('type', fx_field::FIELD_MULTILINK, '!=')
            ->get_values('description', 'name');
        
        $config['settings']['sorting']['values'] = $sortings;
        return $config;
    }
    
    
    public function config_list_filtered($config) {
        $config['settings'] += $this->_config_conditions();
        return $config;
    }

    public function config_list_selected($config) {
        $field['selected'] = array (
            'name' => 'selected', 
            'label' => fx::lang('Selected','controller_component'),
            'type' => 'livesearch',
            'is_multiple' => true,
            'ajax_preload' => true,
            'params' => array(
                'content_type' => 'content_'.$this->_content_type
            ),
        );
        $config['settings'] += $field;
        return $config;
    }
    
    protected function _config_conditions () {
        $fields['conditions'] = array(
            'name' => 'conditions',
            'label' => fx::lang('Conditions','controller_component'),
            'type' => 'set', 
            'is_cond_set' => true,  
            'tpl' => array(
                array(
                    'id' => 'name',
                    'name' => 'name',
                    'type' => 'select'
                ), 
            ),
            'operators_map' => array (
                'string' => array(
                    'contains' => 'contains',
                    '=' => '='
                ),
                'int' => array(
                    '=' => '=',
                    '>' => '>',
                    '<' => '<',
                    '>=' => '>=',
                    '<=' => '<=',
                    '!=' => '!=',
                ),
                'datetime' => array(
                    '=' => '=',
                    '>' => '>',
                    '<' => '<',
                    '>=' => '>=',
                    '<=' => '<=',
                    '!=' => '!=',
                    'next' => 'next',
                    'last' => 'last',
                    'in_future' => 'in future',
                    'in_past' => 'in past'
                ),
                'multilink' => array(
                    '=' => '=',
                    '!=' => '!=',
                ),
                'link' => array(
                    '=' => '=',
                    '!=' => '!=',
                ),
            ),
            'labels' => array(
                'Field',
                'Operator',
                'Value'
            ),
        );

        $searchable_fields =  $this
                ->get_component()
                ->all_fields()
                //->find('type', fx_field::FIELD_MULTILINK, '!=')
                //->find('type', fx_field::FIELD_LINK, '!=')
                ->find('type', fx_field::FIELD_IMAGE, '!=');
                //->get_values(array('description', 'type'), 'name');
        foreach ($searchable_fields as $field) {
            $res = array(
                'description' => $field['description'],
                'type' => fx_field::get_type_by_id($field['type'])
            );
            if ($field['type'] == fx_field::FIELD_LINK) {
                $res['content_type'] = $field->get_target_name();
            }
            if ($field['type'] == fx_field::FIELD_MULTILINK) {
                $relation = $field->get_relation();
                $res['content_type'] = $relation[0] == fx_data::MANY_MANY ? $relation[4] : $relation[1] ;
            }
            $fields['conditions']['tpl'][0]['values'][$field['name']] = $res;
        }
        dev_log('fields', $fields);
        return $fields;
    }  
    
    public function config_list_infoblock($config) {
        
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
            $config['settings'][$c_ib_field['name']]= $c_ib_field;
        }
        // добавляем ручную сортировку для инфоблок-листинга
        $config['settings']['sorting']['values']['manual'] = 
                        '-'.fx::lang('Manual','controller_component').'-';
        return $config;
    }
    
    public function do_record() {
        $page = fx::env('page');
        return array('items' => $page);
    }
    
    public function do_list() {
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
    
    public function do_list_infoblock() {
        $items = $this->do_list();
        if (!$this->get_param('is_fake') && fx::env('is_admin')) {
            $infoblock = fx::data('infoblock', $this->get_param('infoblock_id'));
            $real_ib_name = $infoblock->get_prop_inherited('name');
            $ib_name = $real_ib_name ? $real_ib_name : $infoblock['id'];
            $component = $this->get_component();
            $adder_title = $component['item_name'].' &rarr; '.$ib_name;
            
            $this->accept_content(array(
                'title' => $adder_title,
                'parent_id' => $this->_get_parent_id(),
                'type' => $component['keyword'],
                'infoblock_id' => $this->get_param('infoblock_id')
            ));
            
            if (count($items) == 0) {
                $this->_meta['hidden_placeholder'] = 'Infoblock "'.$ib_name.'" is empty. '.
                                                'You can add '.$component['item_name'].' here';
            }
        }
        return $items;
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
    
    protected function _get_current_page_number() {
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
        $c_page = $this->_get_current_page_number();
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
        $parent_id = null;
        switch($this->get_param('parent_type')) {
            case 'mount_page_id':
                $parent_id = $ib['page_id'];
                if ($parent_id === 0) {
                    $parent_id = fx::env('site')->get('index_page_id');
                }
                break;
            case 'current_page_id': default:
                $parent_id = fx::env('page')->get('id');
                break;
        }
        return $parent_id;
    }
    
    public function do_list_selected() {
        $this->set_param('skip_parent_filter', true);
        $this->set_param('skip_infoblock_filter', true);
        $parent_id = fx::env('page')->get('id');
        $linkers = fx::data('content_select_linker')
                    ->select('linked_id')
                    ->where('infoblock_id', $this->input['infoblock_id'])
                    ->where('parent_id', $parent_id)
                    ->get_data()
                    ->get_values('linked_id');
        dev_log('linkers', $linkers);
        if (is_array($linkers)) {
            $this->listen('query_ready', function($q, $ctr) use ($linkers) {
                $q->where('id', $linkers, 'IN');
                dev_log('selected query', $q->show_query());
            });
        }
        $res = $this->do_list();
        dev_log('selected res',$res);
        return $res;
    }
    
    public function do_list_filtered() {
        $this->set_param('skip_parent_filter', true);
        $this->set_param('skip_infoblock_filter', true);
        $this->listen('query_ready', function($q, $ctr) {
            $fields = $ctr->get_component()->all_fields();
            $conditions = $ctr->get_param('conditions');
            if (isset($conditions) && is_array($conditions)) {
                foreach ($conditions as $condition) {
                    $field = $fields->find_one('name', $condition['name']);
                    $error = false;
                    switch ($condition['operator']) {
                        case 'contains':
                            $condition['value'] = '%'.$condition['value'].'%'; 
                            $condition['operator'] = 'LIKE';
                            break;
                        case 'next': 
                            if (isset($condition['value']) && !empty($condition['value'])) {
                                $q->where(
                                    $condition['name'], 
                                    '> NOW()', 
                                    'RAW'
                                );
                                $condition['value'] = '< NOW() + INTERVAL '.$condition['value'].' '.$condition['interval']; 
                                $condition['operator'] = 'RAW';
                            } else {
                                $error = true;
                            }
                            break;
                        case 'last': 
                            if (isset($condition['value']) && !empty($condition['value'])) {
                                $q->where(
                                    $condition['name'], 
                                    '< NOW()', 
                                    'RAW'
                                );
                                $condition['value'] = '> NOW() - INTERVAL '.$condition['value'].' '.$condition['interval']; 
                                $condition['operator'] = 'RAW';
                            } else {
                                $error = true;
                            }
                            break;
                        case 'in_future': 
                            $condition['value'] = '> NOW()'; 
                            $condition['operator'] = 'RAW';
                            break;
                        case 'in_past': 
                            $condition['value'] = '< NOW()'; 
                            $condition['operator'] = 'RAW';
                            break;
                    }
                    
                    if ($field['type'] == fx_field::FIELD_LINK){
                        if (!isset($condition['value'])) {
                            $error = true;
                        } else {
                            $ids = array();
                            foreach ($condition['value'] as $v) {
                                $ids[]= $v[0];
                            }
                            $condition['value'] = $ids;
                            if ($condition['operator'] === '!=') {
                                $condition['operator'] = 'NOT IN';
                            } elseif ($condition['operator'] === '=') {
                                $condition['operator'] = 'IN';
                            }
                        }
                    }
                    if ($field['type'] == fx_field::FIELD_MULTILINK) {
                        if (!isset($condition['value']) || !is_array($condition['value'])) {
                            $error = true;
                        } else {
                            foreach ($condition['value'] as $v) {
                                $ids[]= $v[0];
                            }
                            $relation = $field->get_relation();
                            dev_log('rels', $relation, $condition);
                            if ($relation[0] === fx_data::MANY_MANY){
                                $content_ids = fx::data($relation[1])->
                                    where($relation[5], $ids)->
                                    select('content_id')->
                                    get_data()->get_values('content_id');
                            } else {
                                $content_ids = fx::data($relation[1])->
                                    where('id', $ids)->
                                    select($relation[2])->get_data()->get_values($relation[2]);
                            }
                            $condition['name'] = 'id';    
                            $condition['value'] = $content_ids;
                            if ($condition['operator'] === '!=') {
                                $condition['operator'] = 'NOT IN';
                            } elseif ($condition['operator'] === '=') {
                                $condition['operator'] = 'IN';
                            }
                        }
                    }
                    if (!$error) {
                        $q->where(
                            $condition['name'], 
                            $condition['value'], 
                            $condition['operator']
                        );
                    }
                }
            }
        });
        $res = $this->do_list();
        return $res;
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
        $show_pagination = $this->get_param('pagination');
        $c_page = $this->_get_current_page_number();
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
        if ( ($parent_id = $this->get_param('parent_id')) && !($this->get_param('skip_parent_filter')) ) {
            $finder->where('parent_id', $parent_id);
        }
        if ( ( $infoblock_id = $this->get_param('infoblock_id')) && !($this->get_param('skip_infoblock_filter')) ) {
            $finder->where('infoblock_id', $infoblock_id);
        }
        if ( ($sorting = $this->get_param('sorting'))) {
            $dir = $this->get_param('sorting_dir');
            if ($sorting === 'manual') {
                $sorting = 'priority';
                $dir = 'ASC';
            }
            if (!$dir) {
                $dir = 'ASC';
            }
            $finder->order($sorting, $dir);
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
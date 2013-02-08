<?php

/**
 * Инфоблок, выводящий контент в разделе
 */
class fx_infoblock_content extends fx_infoblock {

    protected $site, $subdivision;
    protected $component_id, $component;
    // информация о шаблоне компонента
    protected $ctpl;
    // шаблонизатор
    protected $tpl;
    // массив с переданными параметрами функции
    protected $func_param;
    // массив параметров для изменения основного запроса
    protected $sql_param = array();
    // части sql-запроса
    protected $sql;
    // сам запрос
    protected $content_select;
    // все id показываемых объектов
	protected $all_content_ids, $db_result;
    // url раздела
    protected $url;
    // показываемые объекты
    protected $content_ids = false;
    // возможность сортировки drag&drop
    protected $_manual_sort = false;
    // режим редактрования
    protected $edit_mode = false;
    // текущий режим ( index, full, )
    protected $current_action;
    protected $search_param = array();

    static public function objects_list($source, $param = '') {
        $fx_core = fx_core::get_object();

        if (!is_array($param)) parse_str($param, $param);

        if (preg_match("/component([0-9]+)/i", $source, $match)) {
            $component_id = $match[1];
            $ib = fx::data('infoblock')->get('essence_id', $component_id, 'subtype', 'block');
            $param['ignore_site'] = 1;
        } else {
            $ib = fx::data('infoblock')->get_by_id($source);
        }

        return $ib->show_index($param);
    }

    /**
     *
     * @param type $func_param, ключи page, fxsrh
     */
    public function show_index($func_param = '') {
        $fx_core = fx_core::get_object();
        $db = $fx_core->db;
        $this->current_action = 'index';

        if (!is_array($func_param)) {
            parse_str($func_param, $func_param);
        }
        $this->func_param = $func_param;

        $this->_init_show($func_param);
        if ($this->tpl->is_canceled()) {
            return $this->tpl->get_cancel_message();
        }

        // параметры
        $ignore_prefix = $this->sql_param['ignore_prefix'] || $func_param['ignore_prefix'];
        $ignore_suffix = $this->sql_param['ignore_suffix'] || $func_param['ignore_suffix'];
        $output_type = $func_param['output'] === 'array' ? 'array' : 'string';

        // выполнение запроса
        $this->tpl->set_vars('fx_content_select', $this->content_select);
        $res = $db->get_results($this->content_select);
        $this->tpl->set_vars('fx_db_result', $res);
        $this->db_result = $res;
        //$db->debug();

        if ($db->is_error) {
            return $this->content_select."<br/>".$db->last_error;
        }

        // количество объктов
        $row_mum = $db->row_count();
        // общее количество записей
        $this->total_rows = intval(!$this->sql_param['ignore_calc'] ? $db->get_var("SELECT FOUND_ROWS()") : $row_mum);
        $this->tpl->set_vars('fx_total_rows', $this->total_rows);


        // надо узнать id всех объектов ДО префикса
        // и до основной обработки каждого объекта
        $this->all_content_ids = array();
        for ($i = 0; $i < $row_mum; $i++) {
            $this->all_content_ids[] = $res[$i]['id'];
        }
        $this->tpl->set_vars('fx_all_content_ids', $this->all_content_ids);

        if ($row_mum) $keys = array_keys($res[0]);

        // начинаем собриать данные
        ob_start();

        if (!$ignore_prefix) {
            $this->tpl->prefix();
        }

        for ($i = 0; $i < $row_mum; $i++) {
            $content = $this->get_essence_obj($res[$i]);
            $this->_process_content($content, $keys);
            $this->tpl->set_vars('f_num', $i + 1);
            $this->tpl->set_vars('fx_content', $content);

            // сообственно, сам вывод объекта
            if ($output_type === 'array') {
                ob_start();
                $this->tpl->record();
                $content[$res[$i]['id']] = ob_get_contents();
                ob_end_clean();
            } else {
                if ($this->edit_mode) {
                    ob_start();
                    $this->tpl->record();
                    $content = ob_get_contents();
                    ob_end_clean();

                    if ($this->ctpl['with_list']) {
                        $content = $this->wrap_object($content, $this->tpl->get_vars('f_id_hash'));
                    }
                    echo $content;
                } else {
                    $this->tpl->record();
                }
            }
        }

        // суффикс
        if (!$ignore_suffix) {
            $this->tpl->suffix();
        }

        // собирем контент и возвращаем его
        if ($output_type === 'string') {
            $content = ob_get_contents();
        }
        ob_end_clean();

        if ($this->func_param['block_hash'] && $this['subtype'] == 'block' && $this->_manual_sort && count($this->all_content_ids) > 1) {
            $fx_core->page->add_sortable(
                    array(
                            'parent' => '.'.$this->func_param['block_hash'],
                            'mode' => 'edit',
                            'post' => array('essence' => 'content', 'action' => 'move', 'component_id' => $this->component_id, 'content' => $this->all_content_ids)));
        }
        return $content;
    }

    public function show_full($func_param = '') {
        $fx_core = fx_core::get_object();
        $db = $fx_core->db;
        $this->current_action = 'full';

        if (!is_array($func_param)) {
            parse_str($func_param, $func_param);
        }
        $this->func_param = $func_param;

        $this->content_ids = $fx_core->env->get_content();

        $this->_init_show($func_param, 'full');

        if ($this->tpl->is_canceled()) {
            return $this->tpl->get_cancel_message();
        }

        // выполнение запроса
        $this->tpl->set_vars('fx_content_select', $this->content_select);
        $res = $db->get_row($this->content_select);
        $this->tpl->set_vars('fx_db_result', $res);
        $this->db_result = array($res);

        if ($db->is_error) {
            return $this->content_select."<br/>".$db->last_error;
        }
        if (!$res) return '';

        $content = $this->get_essence_obj($res);
        $this->all_content_ids[] = $content['id'];

        $this->_process_content($content, array_keys($res));

        $this->tpl->set_vars('fx_content', $content);

        //title,h1
        $this->_set_metatags($content);

        // сообственно, сам вывод
        ob_start();
        $this->tpl->full();
        $content = ob_get_contents();
        ob_end_clean();

        if ($this->edit_mode) {
            $content = '<div class="'.$this->tpl->get_vars('f_id_hash').'">'.$content.'</div>';
        }

        return $content;
    }

    protected function _init_show($func_param = array(), $show = 'index') {
        $fx_core = fx_core::get_object();

        $this->edit_mode = (bool) ($this->func_param['edit_mode'] );

        $this->component_id = $this['essence_id'];
        $this->component = fx::data('component')->get_by_id($this->component_id);
        $this->subdivision = fx::data('subdivision')->get_by_id($this['subdivision_id']);
        if ($this->subdivision) {
            $this->site = $this->subdivision->get_site();
        }

        if ($this->func_param[fx::config()->SEARCH_KEY]) {
            $this->search_param = $this->func_param[fx::config()->SEARCH_KEY];
        }


        $this->url = $this->subdivision['hidden_url'];

        // шаблон компонента
        $type = $this->func_param['ctpl'] ? $this->func_param['ctpl'] : $this['list_ctpl_id'];
        $this->ctpl = fx::data('ctpl')->get_by_id($type);
        $this->tpl = $this->component->load_tpl_object($type);

        $this->tpl->set_vars('fx_visual', $this['visual']);
        $this->tpl->set_vars('fx_tpl', $fx_core->env->get_tpl());
        $this->tpl->set_vars('fx_infoblock', $this);
        $this->tpl->set_vars('fx_component', $this->component);
        $this->tpl->set_vars('fx_user', $fx_core->env->get_user());
        $this->tpl->set_vars('fx_add_link', $this->url.'add_'.$this['url'].'.html');
        $this->tpl->set_vars($this->func_param);

        // выполнение системных настроек
        if ($show == 'full') {
            $this->tpl->settings_full();
        } else {
            $this->tpl->settings_index();
        }

        $this->sql_param = $this->tpl->get_vars('query_param');

        // составление запроса
        if ($this->sql_param['query']) {
            $this->content_select = $this->sql_param['query'];
        } else {
            if ($this->sql_param['ignore_all']) {
                $this->_make_user_query($func_param);
            } else {
                $this->_make_system_query($func_param, $show);
            }

            // main query
            $this->content_select = "SELECT ".$this->sql['calc']." ".$this->sql['select']."
                FROM (".$this->sql['from'].")
                ".$this->sql['join']."
                WHERE ".$this->sql['where']."
                ".$this->sql['group']."
                ".$this->sql['having']."
                ".$this->sql['order']."
                ".$this->sql['limit'];
        }
    }

    protected function _set_metatags($content) {
        $fx_core = fx_core::get_object();

        $title = $this->_get_metatag($content, 'title');
        if ($title) {
            $fx_core->page->set_metatags('title', $title);
        }

        $h1 = $this->_get_metatag($content, 'h1');
        if ($h1) {
            $fx_core->page->set_metatags('h1', $h1, "essence=content&class_id=".$this->component_id."&id=".$content['id']);
        }
    }

    protected function _get_metatag($content, $name) {
        if ($content['seo_'.$name]) {
            $result = $content['seo_'.$name];
        } else {
            ob_start();
            $this->tpl->$name();
            $result = ob_get_contents();
            ob_end_clean();
        }

        return trim($result, " \n\r\t");
    }

    public function show_add($func_param = '') {
        $fx_core = fx_core::get_object();

        if (!is_array($func_param)) parse_str($func_param, $func_param);

        $component_id = $this['essence_id'];
        $component = fx::data('component')->get_by_id($component_id);

        $type = $func_param['ctpl'] ? $func_param['ctpl'] : $this['list_ctpl_id'];
        $tpl = $component->load_tpl_object($type);

        $fields = array();
        foreach ($component->fields() as $v) {
            $fields[$v->get_name()] = $v;
        }
        $tpl->set_vars('fx_fields', $fields);
        $tpl->set_vars('fx_infoblock', $this);
        $tpl->set_vars('fx_subdivision', $this->subdivision);
        $tpl->set_vars('fx_component', $this->component);

        if ($func_param['parent_id']) {
            $parent_id = $func_param['parent_id'];
        } else if ($fx_core->input->fetch_get_post('parent_id')) {
            $parent_id = $fx_core->input->fetch_get_post('parent_id');
        } else {
            $parent_id = 0;
        }
        $tpl->set_vars('parent_id', intval($parent_id));

        foreach ($func_param as $k => $v) {
            $tpl->set_vars($k, $v);
        }

        ob_start();
        $tpl->begin_add_form();
        $tpl->add_form();
        $tpl->end_add_form();
        // собирем контент и возвращаем его
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function show_edit($func_param = '') {
        $fx_core = fx_core::get_object();

        if (!is_array($func_param)) {
            parse_str($func_param, $func_param);
        }
        $this->func_param = $func_param;

        $content_id = $this->func_param['content_id'] ? $this->func_param['content_id'] : $fx_core->env->get_message();

        $component_id = $this['essence_id'];
        $component = fx::data('component')->get_by_id($component_id);

        $content = fx::data('content')->get_by_id($component_id, $content_id);
        $fields = array();
        foreach ($component->fields() as $v) {
            $name = $v->get_name();
            $fields[$name] = $v;
            $fields[$name]->set_value($content[$name]);
        }

        $type = $func_param['ctpl'] ? $func_param['ctpl'] : $this['list_ctpl_id'];
        $tpl = $component->load_tpl_object($type);

        $tpl->set_vars('fx_content_id', $content_id);
        $tpl->set_vars('fx_content', $content);
        $tpl->set_vars('fx_fields', $fields);
        $tpl->set_vars('fx_infoblock', $this);
        $tpl->set_vars('fx_subdivision', $this->subdivision);
        $tpl->set_vars('fx_component', $component);

        $tpl->set_vars($func_param);

        ob_start();
        $tpl->begin_edit_form();
        $tpl->edit_form();
        $tpl->end_edit_form();
        // собирем контент и возвращаем его
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function get_total_rows() {
        return $this->total_rows;
    }

    public function get_max_rows() {
        return $this->max_rows;
    }

    public function get_url() {
        return $this->url;
    }

    protected function _get_order_string($query_order = '') {
        if ($query_order) return $query_order;

        $sort = $this['sort'] ? $this['sort'] : $this->ctpl['sort'];

        $ret = "`a`.`priority` DESC, `a`.`id`";
        switch ($sort['type']) {
            case 'field':
                if ($sort['fields'])
                        foreach ($sort['fields'] as $v) {
                        $order[] = "`a`.`".$v['field']."` ".strtoupper($v['order'])." ";
                    }
                $ret = $order ? join(', ', $order) : '`a`.`priority` DESC, `a`.`id`';
                break;
            case 'rand': case 'random':
                $ret = "RAND()";
                break;
            case 'last':
                $ret = "`a`.`created` DESC, `a`.`id`";
                break;
            case 'manual':
                $ret = "`a`.`priority` DESC, `a`.`id`";
                break;
        }

        if ($ret == '`a`.`priority` DESC, `a`.`id`') $this->_manual_sort = true;
        return $ret;
    }

    protected function _process_content(fx_content $content, $keys = array()) {
        $fx_core = fx_core::get_object();

        $content_id = $content['id'];

        foreach ($keys as $key) {
            $this->tpl->set_vars('f_'.$key, $content[$key]);
        }

        $full_link = $content->get_link();
        $this->tpl->set_vars('full_link', $full_link);



        $this->tpl->set_vars('f_created', new fx_field_vars_datetime($content['created']));
        $this->tpl->set_vars('f_last_updated', new fx_field_vars_datetime($content['last_updated']));

        if ( $this->edit_mode ) {
            $hash_obj = $this->get_object_hash($content);
            $f_id_hash = $hash_obj.' fx_page_block';
              if ($this->_manual_sort) {
                $f_id_hash .= ' fx_sortable_'.str_replace('fx_page_block_', '', $this->func_param['block_hash']);
            }
        }


        $this->tpl->set_vars('f_id_hash', $f_id_hash);


        foreach ($this->component->fields() as $field) {
            $this->tpl->set_vars($field->content_procces($content, $this, $this->edit_mode ? $hash_obj : null));
        }
    }

    protected function _get_select_fields() {
        $fl = $this->_get_base_select_field();

        foreach ($this->component->fields() as $field) {
            $fl[] = "a.".$field['name']." AS `".$field['name']."`";
        }

        return $fl;
    }

    protected function _get_base_select_field() {
        $fx_core = fx_core::get_object();
        return array('a.`id` AS `id`', 'a.`user_id` AS `user_id`',
                'a.`priority` AS `priority`', 'a.`seo_h1` AS `seo_h1`', 'a.`seo_title` AS `seo_title`', 'a.`seo_keywords` AS `seo_keywords`',
                'a.`seo_description` AS `seo_description`', 'sub.`id` AS `sub_id`',
                'CONCAT( \''.fx::config()->SUB_FOLDER.'\', sub.`hidden_url`) AS `hidden_url`',
                '`infoblock`.`id` AS `infoblock_id`', '`infoblock`.`url` AS `url`', 'a.`checked`', 'a.`created`', 'a.`last_updated`', 'a.`keyword`');
    }

    protected function _make_system_query_from() {
        $this->sql['from'] = '{{'.$this->get_main_table().'}} AS `a`';
        if ($this->sql_param['query_from']) {
            $this->sql['from'] .= ", ".$this->sql_param['query_from'];
        }
    }

    protected $_main_table  = null;
    
    protected function get_main_table() {
    	if (!$this->_main_table) {
			$component = fx::data('component')->get_by_id($this->component_id);
			$this->_main_table = 'content_'.$component['keyword'];
		}
		return $this->_main_table;
    }

    protected function _make_system_query_join() {
        $this->sql['join'] = "LEFT JOIN `{{infoblock}}` AS `infoblock` ON `infoblock`.`id` = a.`infoblock_id`
        LEFT JOIN `{{subdivision}}` AS `sub` ON `sub`.`id` = `infoblock`.`subdivision_id`";
        if ($this->sql_param['query_join']) {
            $this->sql['join'] .= " ".$this->sql_param['query_join'];
        }
    }

    protected function _make_system_query_where($func_param) {
        $fx_core = fx_core::get_object();

        $ignore_site = ($func_param ['ignore_site'] || $this->sql_param['ignore_site']);
        $ignore_sub = ($func_param ['ignore_sub'] || $this->sql_param['ignore_sub'] || $ignore_site);
        $ignore_ib = ($func_param ['ignore_ib'] || $this->sql_param['ignore_ib'] || $ignore_sub);
        $ignore_check = ($fx_core->is_admin_mode() || $this->sql_param['ignore_check']);
        $by_user_id = $func_param ['by_user_id'] ? $func_param ['by_user_id'] : ( $this->sql_param['by_user_id'] ? $this->sql_param['by_user_id'] : 0);
        $by_user_id = intval($by_user_id);
        $parent_id = intval($func_param['parent_id']);

        if ($func_param['only_own'] || $this->sql_param['only_own']) {
            $user = $fx_core->env->get_user();
            $by_user_id = $user ? $user['id'] : '-1';
        }
        $this->sql['where'] = '1';

        // показ явно заданных объектов
        if ($this->content_ids) {
            $ignore_site = $ignore_sub = $ignore_ib = true;
            $this->sql['where'] .= " AND ".( is_array($this->content_ids) ? " a.`id` IN (".join(',', array_map('intval', $this->content_ids)).") " : "a.`id` = '".intval($this->content_ids)."' " );
        }

        $this->sql['where'] .= $ignore_site ? "" : " AND sub.`site_id` = '".$this->site['id']."'";
        $this->sql['where'] .= $ignore_sub ? "" : " AND sub.`id` = '".$this->subdivision['id']."'";
        $this->sql['where'] .= $ignore_ib ? "" : " AND a.`infoblock_id` = '".$this['id']."'";
        $this->sql['where'] .= $ignore_check ? "" : " AND a.`checked` = 1";
        $this->sql['where'] .= $by_user_id ? " AND a.`user_id` = '".$by_user_id."'" : "";
        if ($this->current_action == 'index') {
            $this->sql['where'] .= " AND a.`parent_id` = '".$parent_id."'";
        }

        $this->sql['where'] .= $this->sql_param['query_where'] ? " AND ".$this->sql_param['query_where']." " : "";
    }

    protected function _make_system_query($func_param, $show = 'index') {
        // select
        $this->sql['select'] = join(', ', $this->_get_select_fields());
        if ($this->sql_param['query_select'])
                $this->sql['select'] .= ", ".$this->sql_param['query_select'];

        // SQL_CALC_FOUND_ROWS
        if ($show == 'index' && !$this->sql_param['ignore_calc']) {
            $this->sql['calc'] = 'SQL_CALC_FOUND_ROWS';
        }

        $this->_make_system_query_from();
        $this->_make_system_query_join();
        $this->_make_system_query_where($func_param);

        // group, having
        $this->sql['group'] = $this->sql_param['query_group'] ? "GROUP BY ".$this->sql_param['query_group']." " : '';
        $this->sql['having'] = $this->sql_param['query_having'] ? "HAVING ".$this->sql_param['query_having']." " : '';

        // order
        $this->sql['order'] = ($show == 'index') ? "ORDER BY ".$this->_get_order_string($this->sql_param['query_order'])." " : '';

        // limit
        $maxRows = intval($this->sql_param['max_rows'] ? $this->sql_param['max_rows'] : ($this['rec_num'] ? $this['rec_num'] : $this->ctpl['rec_num'] ) );
        $this->max_rows = $maxRows;
        $page = intval(!$func_param['page'] ? 1 : $func_param['page']);
        $ignore_limit = (!$maxRows || $this->sql_param['ignore_limit']);
        $this->sql['limit'] = $ignore_limit ? '' : 'LIMIT '.( $this->sql_param['query_limit'] ? $this->sql_param['query_limit'] : ($page - 1) * $maxRows.','.$maxRows);
        if ($show == 'full') $this->sql['limit'] = 'LIMIT 1';

        $this->apply_search_cond();
    }

    protected function _make_user_query() {
        $this->sql['select'] = $this->sql_param['query_select'] ? $this->sql_param['query_select'] : '*';
        $this->sql['from'] = $this->sql_param['query_from'];
        $this->sql['join'] = $this->sql_param['query_join'] ? $this->sql_param['query_join'] : '';
        $this->sql['where'] = $this->sql_param['query_where'] ? $this->sql_param['query_where'] : '';
        $this->sql['group'] = $this->sql_param['query_group'] ? 'GROUP BY '.$this->sql_param['query_group'] : '';
        $this->sql['having'] = $this->sql_param['query_having'] ? 'HAVING '.$this->sql_param['query_having'] : '';
        $this->sql['order'] = $this->sql_param['query_order'] ? 'ORDER BY '.$this->sql_param['query_order'] : '';
        $this->sql['limit'] = $this->sql_param['query_limit'] ? 'LIMIT  '.$this->sql_param['query_limit'] : '';
    }

    protected function apply_search_cond() {
        foreach ($this->component->fields() as $field) {
            $cond = $this->search_param[$field->get_name()];
            if ($cond) {
                $res = $field->get_search_cond($cond);
                if ($res && is_string($res)) {
                    $this->sql['where'] .= " AND ".$res." ";
                }
            }
        }
    }

    protected function get_essence_obj($data) {
        return new fx_content(array('data' => $data, 'finder' => fx::data('content'), 'component_id' => $this->component_id));
    }

    protected function wrap_object($content, $hash) {
        if (strpos($content, $hash) === false) {
            $content = '<div class="'.$hash.'">'.$content.'</div>';
        }

        return $content;
    }

    public function is_manual_content_selection() {
        return (bool) ($this['content_selection']['type'] == 'manual');
    }

    public function get_current_action() {
        return $this->current_action;
    }

    public function get_db_result() {
        return $this->db_result;
    }

    public function check_rights($action) {
        $fx_core = fx_core::get_object();

        if ($action == 'show' || $action == 'index' || $action == 'full') {
            $action = 'read';
        }

        $access = $this->get_access($action);

        if ($access == 'all') {
            return true;
        }

        $user = $fx_core->env->get_user();
        $is_admin = ($user && $user->perm()->is_supervisor() );

        $own_object = 0;
        if ( !$is_admin && ($action == 'edit' || $action == 'checked' || $action == 'delete') ) {
            $content_id = $fx_core->env->get_message();
            $content = fx::data('content')->get_by_id( $this['essence_id'], $content_id);
            if ( $content && $user && $content['user_id'] == $user['id'] ) {
                $own_object = 1;
            }
        }
        else {
            $own_object = 1;
        }

        if ($access == 'reg') {
            return (bool) $user && $own_object;
        }

        if ($access == 'auth') {
            return ($user && $user->perm()->is_supervisor() );
        }
    }

}

?>

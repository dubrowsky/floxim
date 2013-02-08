<?php

/**
 *@todo перенести часть методов в fx_admin_configjs 
 */
class fx_system_page extends fx_system {

    // title, keywords, description
    protected $metatags = array();
    // post, который пошлется при edit-in-place (например, при изменении h1)
    protected $metatags_post = array();
    protected $block_number = 1, $field_number = 1000;
    
    protected $_macroconst = array();

    /**
     * Установить мета-тег для страницы
     * @param string title, keywords, description
     * @param string value
     */
    public function set_metatags($item, $value, $post = '') {
        $item = 'seo_'.$item;
        $this->metatags[$item] = $value;
        if ($post) {
            $this->metatags_post[$item] = $post;
        }
        return $this;
    }

    /**
     * Получить текущий мета-тег страницы
     * @param mixed title, keywords, description
     * @param mixed value or array
     */
    public function get_metatags($item = '') {
        $item = 'seo_'.$item;
        if ($item) {
            return isset($this->metatags[$item]) ? $this->metatags[$item] : null;
        }

        return $this->metatags;
    }

    public function get_h1($id = '', $class = '') {
        $this->h1['id'] = $id;
        $this->h1['class'] = $class;

        return '%FX_H1%';
    }

    public function add_file($file) {
        if (substr($file, strlen($file) - 4) == '.css') {
            return $this->add_css_file($file);
        }
        if (substr($file, strlen($file) - 3) == '.js') {
            return $this->add_js_file($file);
        }
    }

    public function add_css_file($file) {
        $this->_files_css[] = $file;
    }

    public function add_js_file($file) {
        $this->_files_js[] = $file;
    }

    public function add_infoblock($keyword, $params = array()) {
        $this->infoblocks[$keyword] = $params;
    }

    public function get_infoblocks() {
        return $this->infoblocks ? $this->infoblocks : array();
    }

    public function add_block($post, $buttons = '', $mode = null, $parent = null, $checked = 1) {
        if (is_string($post)) parse_str($post, $post);
        
        $buttons = explode(",", $buttons);
        $res = array('post' => $post, 'buttons' => $buttons);
        if ( $mode ) $res['mode'] = $mode;
        if ( $parent ) $res['parent'] = $parent;
        $res['checked'] = intval($checked);
        
        $this->blocks[$this->block_number] = $res;

        return 'fx_page_block_'.$this->block_number++;
    }
    
    public function update_block ( $hash, $data ) {
        if (is_string($data)) parse_str($data, $data);
        $block_num = str_replace('fx_page_block_', '', $hash);
        if ( $this->blocks[$block_num] ) {
            $this->blocks[$block_num] = array_merge($this->blocks[$block_num], $data);
        }
        
    }

    public function get_blocks() {
        return $this->blocks ? $this->blocks : array();
    }
    
    /**
     * делает блок, в который можно добавлять.
     * Ключи массива:
     * mode - режим, когда в этот блок можно добавить
     * post 
     * name - имя для меню
     * key, parent_key
     * decent_parent
     * preview
     * preview_parent
     */
    public function addition_block ( $data ) {
        $num = md5(serialize($data));
        $this->addition_block[$num] = $data;
    }
    
    public function get_addition_block() {
        return $this->addition_block ? $this->addition_block : array();
    }
    

    public function add_edit_field($field, $type, $post = array(), $parent = '', $mode = '') {

        if (is_string($post)) parse_str($post, $post);
        $ar['field'] = $field;

        if (is_array($type)) {
            foreach ($type as $k => $v) {
                $ar[$k] = $v;
            }
        } else {
            $ar['type'] = $type;
        }
        if (!$ar['type']) $ar['type'] = 'string';

        if ($post) $ar['post'] = $post;
        if ($parent) $ar['parent'] = $parent;
        if ($mode) $ar['mode'] = array($mode);

        $this->edit_fields[$this->field_number] = $ar;

        return 'fx_page_field fx_page_field_'.$this->field_number++;
    }

    public function get_edit_fields() {
        return $this->edit_fields ? $this->edit_fields : array();
    }

    public function add_sortable($params) {
        $num = md5(serialize($params));
        $this->sortable[$num] = $params;
    }

    public function get_sortable() {
        return $this->sortable ? $this->sortable : array();
    }

    public function add_data_js($keyword, $values) {
        $this->_data_js[$keyword] = $values;
    }

    public function get_data_js() {
        return $this->_data_js;
    }

    public function add_js_text($text) {
        $this->_js_text[] = $text;
    }

    public function get_js_text() {
        return $this->_js_text;
    }

    /**
     * Создает или изменяет значение макроконстанты
     * Можно вставить макроконстанту в любую часть страницы:
     * <?=$fx_core->page->set_macroconst('foo')?>
     * А потом поменять ее значение:
     * $fx_core->page->set_macroconst('foo', 'bar');
     */
    public function set_macroconst($name, $value = '') {
        if ( isset($this->_macroconst[$name]) ) {
            $this->_macroconst[$name]['value'] = $value;
            $hash  = $this->_macroconst[$name]['hash'];
        }
        else {
            $hash = strtoupper(md5(rand().$name.time()));
            $hash =  '%%FX_'.$hash.'%%';
            $this->_macroconst[$name] = array('hash' => $hash, 'value' => $value);
            
        }
        
        return $hash;
    }

    public function set_numbers($block_number = 1, $field_number = 1) {
        $this->block_number = intval($block_number);
        $this->field_number = intval($field_number);
    }

    public function set_after_body($txt) {
        $this->_after_body[] = $txt;
    }

    public function post_proccess($buffer) {
        $fx_core = fx_core::get_object();
        if ($fx_core->is_admin_mode()) {
            return $buffer;
        }

        if ($this->metatags['seo_title']) {
            $r = "<title>".$this->metatags['seo_title']."</title>";
        }
        if ($this->_files_css) {
            foreach ($this->_files_css as $v) {
                $r .= '<link rel="stylesheet" type="text/css" href="'.$v.'" />'.PHP_EOL;
            }
        }
        if ($this->_files_js) {
            foreach ($this->_files_js as $v) {
                $r .= '<script type="text/javascript" src="'.$v.'" ></script>'.PHP_EOL;
            }
        }
        $buffer = str_replace('<head>', '<head>'.$r, $buffer);

        //h1
        $h1_post = $this->metatags_post['seo_h1'];
        if ($h1_post) {
            $h1_class = $this->add_edit_field('h1', 'string', $h1_post);
        }
        $buffer = str_replace('%FX_H1%', '<h1 class="'.$h1_class.'">'.$this->get_metatags('h1').'</h1>', $buffer);


        if ($this->_after_body) {
            $buffer = str_replace('<body>', '<body>'.join("\r\n", $this->_after_body), $buffer);
        }

        $user = fx_core::get_object()->env->get_user();
        if ($user && $user->perm()->is_supervisor()) {
            $this->add_data_js('block_number', $this->block_number);
            $this->add_data_js('field_number', $this->field_number);

            $js = '<script type="text/javascript">'.PHP_EOL;
            $js .= '$("body").data('.json_encode($this->get_data_js()).');'.PHP_EOL;
            if ( ($js_text = $this->get_js_text() )) {
                $js .= join(PHP_EOL, $js_text).PHP_EOL;
            }
            $js .= '$fx.set_data('.json_encode(array('main' => array('url' => $_SERVER['REQUEST_URI']))).');'.PHP_EOL;
            $js .= '$fx.set_data('.json_encode(array('infoblock' => $this->get_infoblocks())).');'.PHP_EOL;
            $js .= '$fx.set_data('.json_encode(array('blocks' => $this->get_blocks())).');'.PHP_EOL;
            $js .= '$fx.set_data('.json_encode(array('fields' => $this->get_edit_fields())).');'.PHP_EOL;
            $js .= '$fx.set_data('.json_encode(array('sortable' => $this->get_sortable())).');'.PHP_EOL;
            $js .= '$fx.set_data('.json_encode(array('addition_block' => $fx_core->page->get_addition_block())).');';
            $js .= '</script>'.PHP_EOL;

            $buffer = str_replace('</body>', $js.'</body>', $buffer);
        }

        $buffer = $this->replace_macroconst($buffer);

        return $buffer;
    }
    
    protected function replace_macroconst ( $buffer ) {
        foreach ( $this->_macroconst  as $v ) {
            $buffer = str_replace($v['hash'], $v['value'], $buffer);
        }
       
        
        return $buffer;
    }

}

?>
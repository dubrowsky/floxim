<?php

class fx_html2php {

    protected $html;
    protected $functions = array();
    // информация о текущем блоксете
    protected $blockset_context = false;
    // информация о текущем блоке
    protected $block_context = false;
    // информация о текущем меню
    protected $menu_context = false;
    // файлы стилей и js
    protected $files = array();
    protected $units = array();
    protected $current_inherit_id = false;
    protected $simple_block_context = false;

    public function __construct($html = '') {
        $this->html = $html;
    }

    public function convert($html = '') {
        if ($html) $this->html = $html;

        return preg_replace_callback("/<fx_([a-z_]+-\d+)(.*?)>(.*?)<\/fx_\\1>/iums", array($this, 'process'), $this->html);
    }

    protected function convert_attr($text) {
        $text = preg_replace_callback('/<\s*([a-z0-9]+)\s+((.*?)fx_replace\s*=(.*?))>/iums', array($this, 'convert_attr_process'), $text);
        return $text;
    }

    public function get_functions() {
        $this->html = $this->replace_tag($this->html, 'head');
        $this->html = $this->replace_tag($this->html, 'h1');
        $this->html = $this->_mark_paired_tags($this->html);

        $this->html = $this->replace_attr($this->html);

        $this->functions['write'] = $this->convert($this->html);

        return $this->functions;
    }

    public function replace_attr($html) {
        $re_pair = '<([a-z0-9]+)([^<>]*fx_replace[^<>]*)>(.*?)<\/\\1>';
        $re_one = '<([a-z0-9]+)([^<>]*fx_replace[^<>]*)\/>';
        $html = preg_replace_callback('/'.$re_pair.'|'.$re_one.'/iums', array($this, 'process_attr'), $html);
        return $html;
    }

    public function process_attr($match) {

        $pair_tag = (count($match) == 4);
        if ($this->simple_block_context === false) {
            $this->simple_block_context = array();
            if ( $pair_tag ) {
                $tag = $match[1];
                $new_attr = $this->get_new_attr($match[2]);
                $inner_template = $this->replace_attr($match[3]);
                $template = "<$tag ".$new_attr.">".$inner_template."</$tag>";
            }
            else {
                $tag = $match[4];
                $new_attr = $this->get_new_attr($match[5]);
                $template = "<$tag ".$new_attr." />";
            }
            
            $block['template'] = $template;
            $block['params'] = $this->simple_block_context['params'];
            $this->blockset_context = $block;

            $attr['one_block'] = 1;
            $attr['simple'] = 1;
            if (preg_match("~\skeyword=[\'\"]([^\'\"]+)~", $match[0], $simple_keyword)) {
            	$attr['keyword'] = $simple_keyword[1];
            } else {
            	dev_log('no key', $simple_keyword);
            }

            $ret = $this->infoblock_place($attr['keyword'], $attr);
            dev_log($attr, $ret, $match);
            $this->blockset_context = false;
            $this->simple_block_context = false;
            return $ret;
        } else {
            if ($pair_tag) {
                
            } else {
                $tag = $match[4];
                $new_attr = $this->get_new_attr($match[5]);
                return "<$tag $new_attr />";
            }
        }
    }

    protected function get_new_attr($attr_str) {
        $fx_core = fx_core::get_object();

        $img_attrs = array('src');
        
        $result = array();

        $attrs = $fx_core->util->parse_attr($attr_str);
        $fx_replace = explode(',', $attrs['fx_replace']);
        $repalced = array();

        foreach ($attrs as $name => $value) {
            if (in_array($name, $fx_replace)) {
                $i = count($this->simple_block_context['params']);
                $param = array();
                $param['default'] = $this->_trim($value);
                $param['name'] = $name;
                if ( in_array($name, $img_attrs) ) {
                    $param['type'] = 'image';
                }
                $this->simple_block_context['params'][] = $param;
                $value = "%FX_REPLACE_".$i."%";
                $repalced[] = $name;
            }

            $result[$name] = $value;
        }


        foreach ($fx_replace as $v) {
            if (in_array($v, $repalced)) {
                continue;
            }
            $i = count($this->simple_block_context['params']);
            $param = array();
            $param['default'] = '';
            $param['name'] = $v;
            if ( in_array($name, $img_attrs) ) {
                $param['type'] = 'image';
            }
            $this->simple_block_context['params'][] = $param;
            $result[$v] = "%FX_REPLACE_".$i."%";
        }

        unset($result['fx_replace']);
        $ret = '';
        foreach ($result as $k => $v) {
            $ret .= ' '.$k.' = "'.$v.'" ';
        }
        return $ret;
    }

    public function get_files() {
        return $this->files;
    }

    public function get_units() {
        $this->get_functions();
        return $this->units;
    }

    protected function replace_tag($html, $tag) {
        $html = preg_replace_callback("/<($tag)(.*?)>(.*?)<\/$tag>/iums", array($this, 'process'), $html);
        $html = preg_replace_callback("/<($tag)(.*?)\/>/iums", array($this, 'process'), $html);
        return $html;
    }

    protected function process($arguments) {
        $tag_name = preg_replace('/-\d+/', '', $arguments[1]);
        $attr = $this->parse_attr($arguments[2]);
        $content = $arguments[3];

        $callback = 'process_'.$tag_name;

        if (!method_exists($this, $callback)) {
            throw new fx_exception_html2php("Ошибочный тег fx_".$tag_name);
        }

        return $this->$callback($content, $attr, $arguments[0]);
    }

    protected function process_blockset($content, $attr) {
        $this->blockset_context = array();
        $this->convert($content);

        $ret = $this->infoblock_place($attr['keyword'], $attr);
        $this->blockset_context = false;

        return $ret;
    }

    protected function process_block($content, $attr) {
        $block = array('name' => $attr['name']);

        // fx_block находится вне fx_blockset
        // это равносильно fx_blockset c одним блоком
        $one_block = ( $this->blockset_context === false );
        $this->block_context = $block;

        // замена fx_replace
        $this->block_context['template'] = $this->_trim($this->convert($content));

        $this->blockset_context['blocks'][] = $this->block_context;

        // остальная обработка будет в process_blockset
        if (!$one_block) return "";

        $attr['one_block'] = 1;
        $ret = $this->infoblock_place($attr['keyword'], $attr);
        $this->blockset_context = false;

        return $ret;
    }

    protected function process_divider($content, $attr) {
        if ($this->menu_context) {
            return $this->process_menu_item('divider', $content);
        }
        if ($this->blockset_context === false) {
            throw new fx_exception_html2php("Не ожидается fx_divider");
        }

        $this->blockset_context['divider'] = $this->_trim($content);

        return "";
    }

    protected function process_replace($content, $attr) {
        // fx_replace вне fx_block
        if ($this->block_context === false) {
            $this->blockset_context['template'] = '%FX_REPLACE_0%';
            
            $param['name'] = $attr['name'] ? $attr['name'] : 'Parametr';
            $param['default'] = $this->_trim($content);
            $this->blockset_context['params'][] = $param;
            $attr['one_block'] = 1;
            $attr['simple'] = 1;
            $ret = $this->infoblock_place($attr['keyword'], $attr);
            $this->blockset_context = false;
        } else {
            $i = count($this->block_context['params']);
            $param['default'] = $this->_trim($content);
            $param['type'] = $attr['type'] ? $attr['type'] : 'string';
            if ($attr['name']) $param['name'] = $attr['name'];
            $this->block_context['params'][] = $param;
            $ret = "%FX_REPLACE_".$i."%";
        }


        return $ret;
    }

    protected function process_content($content, $attr) {
        if ($this->block_context === false) {
            throw new fx_exception_html2php("Не ожидается fx_content");
        } else {
            $ret = '%FX_CONTENT%';
        }
        return $ret;
    }

    /**
     * Обработчик тега <fx_menu>
     */
    protected function process_menu($content, $attr) {
        $this->check_menu_attr($attr);

        if ($this->menu_context !== false) {
            throw new fx_exception_html2php("fx_menu не может содержаться внутри fx_menu");
        }
        $this->menu_context = array();
        $this->convert($content);

        $template = $this->menu_context['submenu'];
        $template[0] = $this->menu_context['template'];

        $ret = "<?=\$fx_layout->place_menu('".$attr['keyword']."', ".$this->array_to_str($attr).", ".$this->array_to_str($template).")?>";

        if ($this->current_inherit_id) {
            $attr['source_id'] = $this->current_inherit_id;
        }
        $this->units['menu'][$attr['keyword']] = $attr;

        $this->menu_context = false;
        return $ret;
    }

    protected function array_to_str($array) {
        return "'".addcslashes(serialize($array), "'")."'";
    }

    protected function check_menu_attr($attr) {
        static $keywords = array();
        if (!$attr['keyword']) {
            throw new fx_exception_html2php("Не задан keyword у меню");
        }
        /* if ( in_array($attr['keyword'], $keywords) ) {
          throw new fx_exception_html2php("Ошибка. Меню с keyword ".$attr['keyword']." уже существует");
          } */

        $allow_kinds = array(fx_menu::KIND_INDEPENDENT, fx_menu::KIND_DEPENDENT, fx_menu::KIND_BOTH, fx_menu::KIND_SECONDARY);
        if ($attr['kind'] && !in_array($attr['kind'], $allow_kinds)) {
            throw new fx_exception_html2php("Ошибочный атрибут kind ".$attr['kind']." у меню. Возможные значения: ".join(', ', $allow_kinds));
        }
        if ($attr['kind'] && ($attr['kind'] == fx_menu::KIND_DEPENDENT || $attr['kind'] == fx_menu::KIND_BOTH ) && !$attr['parent']) {
            throw new fx_exception_html2php("Ошибка. При использовании зависимого меню необходимо указать атрибут parent");
        }

        $keywords[] = $attr['keyword'];
    }

    protected function process_prefix($content, $attr) {
        return $this->process_menu_item('prefix', $content);
    }

    protected function process_active_link($content, $attr) {
        return $this->process_menu_item('active_link', $content);
    }

    protected function process_active($content, $attr) {
        return $this->process_menu_item('active', $content);
    }

    protected function process_unactive($content, $attr) {
        return $this->process_menu_item('unactive', $content);
    }

    protected function process_suffix($content, $attr) {
        return $this->process_menu_item('suffix', $content);
    }

    protected function process_menu_item($type, $content) {
        $content = $this->convert($content);
        if ($this->submenu_current_level) {
            $this->menu_context['submenu'][$this->submenu_current_level][$type] = $content;
        } else {
            $this->menu_context['template'][$type] = $content;
        }

        return $content;
    }

    protected function process_func($content, $attr) {
        $this->menu_context['function'][] = $attr;
        return '%func_0%';
    }

    protected function process_submenu($content, $attr) {
        $this->submenu_current_level++;
        $this->convert($content);
        $this->submenu_current_level--;

        return '%submenu%';
    }

    protected function process_inherit($content, $attr) {
        if (isset($attr['source'])) {
            $this->units['inherit'][] = $attr;
            return '<?=$fx_tpl_'.$attr['source'].'->_'.$attr['keyword'].'($this->get_vars());?>';
        } else {
            $this->current_inherit_id = $attr['keyword'];
            $this->functions[$attr['keyword']] = $this->convert($content);
            $this->current_inherit_id = false;
            return '<?=$this->_'.$attr['keyword'].'( $this->get_vars() );?>';
        }
    }

    protected function process_h1($content, $attr, $all) {
        return '<?=$fx_core->page->get_h1()?>';
    }

    protected function process_head($content, $attr, $all) {
        $result = $this->replace_tag($all, 'title');
        $result = $this->replace_tag($result, 'meta');
        $result = $this->replace_tag($result, 'link');
        $result = $this->replace_tag($result, 'script');
        return $result;
    }

    protected function process_title($content, $attr) {
        return '';
    }

    protected function process_meta($content, $attr, $all) {
        if ($attr['http-equiv'] == 'Content-Type') return '';
        if ($attr['name'] == 'Keywords' || $attr['name'] == 'Description')
                return '';
        return $all;
    }

    protected function process_link($content, $attr, $all) {
        if ($attr['type'] == 'text/css' && $attr['href']) {
            $this->files['css'][] = $attr['href'];
            return '';
        }
        return $all;
    }

    protected function process_script($content, $attr, $all) {
        if ($attr['src']) {
            $this->files['js'][] = $attr['src'];
            return '';
        }
        return $all;
    }

    protected function infoblock_place($keyword = '', $params = array()) {
        static $i = 0;

        $func = $params['simple'] ? 'place_infoblock_simple' : 'place_infoblock';

        if (is_array($params)) {
            unset($params['keyword']);
        }

        if (preg_match("/[^a-z0-9_]/i", $keyword)) {
            $keyword = '';
        }

        if (!$keyword) {
            $keyword = 'infoblock'.$i++;
        }

        $param = array_merge($this->blockset_context, $params);
        $ret = '<?=$fx_layout->'.$func.'(';
        $ret .= "'".$keyword."', ".$this->array_to_str($param)." )?>";

        if (!$params['simple']) {
            if ($this->current_inherit_id) {
                $param['source_id'] = $this->current_inherit_id;
            }
            $this->units['infoblock'][$keyword] = $param;
        }

        $this->block_context = false;
        return $ret;
    }

    protected function parse_attr($attr = "") {
        return fx_core::get_object()->util->parse_attr($attr);
    }


    protected function _trim($str) {
        return trim(str_replace(array("\r\n"), '', $str), " \t");
    }

    protected function _mark_paired_tags($text) {
        return preg_replace_callback("/<(\/?)fx_([a-z_]+)/", array($this, '_mark_tag'), $text);
    }

    protected function _mark_tag($args) {
        static $tags = array();
        $tag = $args[2];
        // open tag
        if (!$args[1]) {
            $ret = ++$tags[$tag];
        } else { // close tag
            $ret = $tags[$tag]--;
        }

        return $args[0].'-'.$ret;
    }

}

class fx_exception_html2php extends fx_exception {
    
}

?>
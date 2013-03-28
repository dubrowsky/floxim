<?php
//require_once '../../boot.php';
class fx_template_processor {
    /**
     * Преобразовать шаблон в php-код
     * @param string $source исходник шаблона
     * @param string $code код для класса
     * @return string php-код
     */
    public function process($source, $code) {
        $source = str_replace("{php}", '<?', $source);
        $source = str_replace("{/php}", '?>', $source);
        
        $tokens = $this->tokenize($source);
        $tree = $this->make_tree($tokens);
        $code = $this->make_code($tree, $code);
        return $code;
    }
    /**
     * Преобразовать директорию с шаблономи в php-код
     * @param string $source_dir путь к директории
     * @return string php-код
     */
    public function process_dir($source_dir) {
        preg_match("~([a-z]+)[\/\\\]([a-z0-9_]+)$~", $source_dir, $tpl_name_parts);
        $this->_controller_type = $tpl_name_parts[1];
        $this->_controller_name = $tpl_name_parts[2];
        if ($tpl_name_parts[1] == 'other') {
            $tpl_name = $tpl_name_parts[2];
        } else {
            $tpl_name = $tpl_name_parts[1].'_'.$tpl_name_parts[2];
        }
        $source = '{templates source="'.$source_dir.'"}';
        foreach (glob($source_dir.'/*.tpl') as $file) {
            // Не включаем файлы шаблонов, начинающиеся на "_"
            if (preg_match("~/_[^/]+$~", $file)) {
                continue;
            }
            $file_data = trim(file_get_contents($file));
            if (!preg_match("~^{template~", $file_data)) {
                preg_match("~/([^/]+)\.tpl~", $file, $file_tpl_name);
                $file_data = 
                    '{template id="'.$file_tpl_name[1].'"}'.
                       trim($file_data).
                    '{/template}';
            }
            // Проверяем наличие fx-аттрибутов в разметке файла
            if (fx_template_html::has_floxim_atts($file_data)) {
                $T = new fx_template_html($file_data);
                $file_data = $T->transform_to_floxim();
            }
            $source .= trim($file_data);
        }
        $source .= '{/templates}';
        dev_log(htmlspecialchars($source));
        $code = $this->process($source, $tpl_name);
        $target = fx::config()->COMPILED_TEMPLATES_FOLDER .'/'.$tpl_name.'.php';
        if ( !is_writable(fx::config()->COMPILED_TEMPLATES_FOLDER) ) die ('Can not write to directory' . fx::config()->COMPILED_TEMPLATES_FOLDER);
        $fh = fopen($target, 'w');
        fputs($fh, $code);
        fclose($fh);
        //die();
    }
    
    public function tokenize($source) {
        $source = preg_replace("~\{\*.*?\*\}~s", '', $source);
        $parts = preg_split(
                '~(\{[\$\%\/a-z0-9]+[^\{]*?\})~', 
                $source, 
                -1, 
                PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );
        $tokens = array();
        foreach ($parts as $p) {
            $tokens []= self::create_token($p);
        }
        return $tokens;
    }
        
    protected static $_token_info = array(
        'template' => array(
            'type' => 'double',
            'contains' => array('code', 'template', 'area', 'var', 'call', 'render','if')
        ),
        'code' => array(
            'type' => 'single'
        ),
        'area' => array(
            'type' => 'both',
            'contains' => array('code', 'template', 'var')
        ),
        'var' => array(
            'type' => 'both',
            'contains' => array('code', 'var', 'call', 'area', 'template', 'render','if')
        ),
        'call' => array(
            'type' => 'both',
            'contains' => array('var', 'render', 'if')
        ),
        'templates'=> array(
            'type' => 'double',
            'contains' => array('template')
        ),
        'render' => array(
            'type' => 'both',
            'contains' => array('code', 'template', 'area', 'var', 'call', 'render', 'if')
        ),
        'if' => array(
            'type' => 'double',
            'contains' => array('code', 'template', 'area', 'var', 'call', 'render', 'elseif', 'else')
        )
    );
    
    public static function get_token_info($name) {
        $info = isset(self::$_token_info[$name]) ? self::$_token_info[$name] : array();
        if (!isset($info['contains'])) {
            $info['contains'] = array();
        }
        return $info;
    }
    
    protected $sl = 0;
    /**
     * Определить тип токена (открывающий/единичный) на основе следующих за ним токенов
     * @param fx_template_token $token токен с неизвестным типом
     * @param array $tokens следующие за ним токены
     * @return null
     */
    protected  function solve_unclosed($token, $tokens) {
        if (!$token  || $token->type != 'unknown') {
            return;
        }
        $token_info = self::get_token_info($token->name);
        $stack = array();
        while ($next_token = array_shift($tokens)) {
            if ($next_token->type == 'unknown') {
                $this->solve_unclosed($next_token, $tokens);
            }
            switch ($next_token->type) {
                case 'open':
                    if (count($stack) == 0) {
                        if (!in_array($next_token->name, $token_info['contains'])) {
                            $token->type = 'single';
                            return;
                        }
                    }
                    $stack[]= $token;
                    break;
                case 'close':
                    if (count($stack) == 0) {
                        if ($next_token->name == $token->name) {
                            $token->type = 'open';
                            return;
                        } else {
                            $token->type = 'single';
                            return;
                        }
                    }
                    array_pop($stack);
                    break;
            }
        }
        echo "solving ".$token->name.
                " | stack has ".count($stack). 
                'items at the end of the method<br />';
    }


    protected function make_tree($tokens) {
        dev_log($tokens);
        $stack = array();
        $root = $tokens[0];
        while ($token = array_shift($tokens)) {
            if ($token->type == 'unknown') {
                $this->solve_unclosed($token, $tokens);
            }
            switch ($token->type) {
                case 'open':
                    if (count($stack) > 0) {
                        end($stack)->add_child($token);
                    }
                    $stack []= $token;
                    break;
                case 'close':
                    array_pop($stack);
                    break;
                case 'single': default:
                    $stack_last = end($stack);
                    if (!$stack_last) {
                        echo "Template error: stack empty, trying to add: ";
                        echo "<pre>" . htmlspecialchars(print_r($token, 1)) . "</pre>";
                        die();
                    }
                    $stack_last->add_child($token);
                    break;
            }
        }
        dev_log($stack);
        return $root;
    }
    
    protected $templates = array();
    
    protected $_code_context = 'text';
    
    protected function _token_code_to_code($token) {
        return $token->get_prop('value');
    }
    
    protected function _token_call_to_code(fx_template_token $token) {
        $code = "<?\n";
        $tpl_id = $token->get_prop('id');
        if (!preg_match("~\.~", $tpl_id)) {
            $tpl_id = $this->_class_code.".".$tpl_id;
        }
        list($tpl_name, $tpl_variant) = explode('.', $tpl_id);
        $code .= '$tpl_to_call = fx::template("'.$tpl_name.'");'."\n";
        $call_children = $token->get_children();
        /*
         * Преобразуем:
         *  {call id="wrap"}<div>Something</div>{/call}
         * вот в такое:
         *  {call id="wrap"}{var id="content"}<div>Something</div>{/var}{/call}
         */
        $has_content_param = false;
        foreach ($call_children as $call_child) {
            if ($call_child->name == 'var') {
                break;
            }
            if ($call_child->name == 'code' && preg_match("~[^\s]+~", $call_child->get_prop('value'))) {
                $has_content_param = true;
                break;
            }
        }
        if ($has_content_param) {
            $token->clear_children();
            $var_token = new fx_template_token('var', 'single', array('id' => 'content'));
            foreach ($call_children as $call_child) { 
                $var_token->add_child($call_child);
            }
            $token->add_child($var_token);
        }
        foreach ($token->get_children() as $param_var_token) {
            // внутри call обрабатываем только var
            if ($param_var_token->name != 'var') {
                continue;
            }
            $value_to_set = 'null';
            if ($param_var_token->has_children()) {
                // передаем вложенный html-код
                $code .= "ob_start();\n";
                $code .= $this->_token_to_code($param_var_token);
                $code .= "\n";
                $value_to_set = 'ob_get_clean()';
            } elseif ( ($select_att = $param_var_token->get_prop('select') ) ) {
                // передаем результат выполнения php-кода
                $value_to_set = $select_att;
            }
            $code .= "\$tpl_to_call->set_var(
                '".$param_var_token->get_prop('id')."', 
                ".$value_to_set.");\n";
        }
        $code .= 'echo $tpl_to_call->render("'.$token->get_prop('id').'", $this->data);?>';
        $current_template =& $this->templates[$this->_get_current_template_id()];
        if (!isset($current_template['calls'])) {
            $current_template['calls'] = array();
        }
        $current_template['calls'] []= $token->get_prop('id');
        return $code;
    }
    
    protected function _token_var_to_code(fx_template_token $token) {
        $var_id = $token->get_prop('id');
        $var_type = $token->get_prop('var_type');
        $code .= "<?\n";
        
        /*
         * 1. Проверить наличие в локальном scope
         * 2. Проверить наличие в $this->data
         * 3. Если нигде нет, использовать default и установить его в $this->data
         * 4. Если есть мета-инфа о переменной, проверить instanceof fx_template_field
         * 5. При необходимости конвертировать
         * 6. Отобразить
         * 
         */
        $var_tmp = preg_match("~^[a-z0-9_]+$~i", $var_id) ? '$'.$var_id.'_tmp' : '${"'.$var_id.'"."_tmp"}';
        
        $var_parts = explode(".", $var_id, 2);
        $code .= $var_tmp ." = null;\n";
        $code .= 'if (isset(${"'.$var_parts[0].'"})) {'."\n";
        $code .= "\t".$var_tmp.' = ';
        if (isset($var_parts[1])) {
            $code .= 'fx::dig(${"'.$var_parts[0].'"}, "'.$var_parts[1].'")';
        } else {
            $code .= '${"'.$var_parts[0].'"}';
        }
        $code .= ";\n";
        $code .= "} else {\n";
        $code .= "\t".$var_tmp.' = fx::dig($this->data, "'.$var_id.'");'."\n";
        $code .= "}";
        if ($token->get_prop('default') || count($token->get_children()) > 0) {
            $code .= "\nif (is_null(".$var_tmp.")) {\n";
            if (!($default = $token->get_prop('default')) ) {
                $code .= "\tob_start();\n";
                $code .= "\t".$this->_token_to_code($token);
                $default = "ob_get_clean()";
            }
            $code .= "\n\t".$var_tmp .' = '.$default.";\n";
            $code .= "\tfx::dig_set(\$this->data, \"".$var_id."\", ".$var_tmp.");\n";
            $code .= "}\n";
        }
        if ($token->get_prop('var_type') == 'visual' && !($token->get_prop('editable') == 'false')) {
            $code .= 'if (!(fx::env("is_admin") && '.$var_tmp." instanceof fx_template_field)) {\n";
            $code .= "\t".$var_tmp." = new fx_template_field(".$var_tmp.", ";
            $code .= 'array("id" => "'.$var_id.'", ';
            $code .= '"var_type" => "visual", ';
            $code .= '"infoblock_id" => fx::dig($this->data, "infoblock.id"), ';
            $code .= '"template" => $this->_get_template_sign(), ';
            if ( ( $var_title = $token->get_prop('title')) ) {
                $code .= '"title" => "'.$var_title.'", ';
            }
            $code .= '"editable" => true))'.";\n";
            $code .= "}\n";
        }
        $code .= "\necho ".$var_tmp.";\n";
        $code .= "unset(".$var_tmp.");\n";
        $code .= "\n?>";
        return $code;
    }
    
    protected function _token_render_to_code(fx_template_token $token) {
        $code = "<?\n";
        if (! ($arr_id = $token->get_prop('select')) || $arr_id == '.') {
            $arr_id = '$this->get_var("input.items")';
        }
        if (! ($item_alias = $token->get_prop('as') ) ) {
            $item_alias = '$item';
        }
        if (! ($item_key = $token->get_prop('key'))) {
            $item_key = '$item_key';
        }
        if (! ($extract = $token->get_prop('extract'))) {
            $extract = true;
        }
        $counter_id = $item_alias."_index";
        $code .= "if (".$arr_id." instanceof Traversable) {\n";
        $code .= $counter_id." = 0;\n";
        $code .= $item_alias."_total = count(".$arr_id.");\n";
        $code .= "\nforeach (".$arr_id." as ".$item_key." => ".$item_alias.") {\n";
        $code .= $counter_id."++;\n";
        $code .= $item_alias."_is_last = ".$item_alias."_total == ".$counter_id.";\n";
        $code .= $item_alias."_is_odd = ".$counter_id." % 2 != 0;\n";
        if ($extract) {
            $code .= "\tif (is_array(".$item_alias.")) {\n";
            $code .= "\t\textract(".$item_alias.");\n";
            $code .= "\t} elseif (is_object(".$item_alias.")) {\n";
            $code .= "\t\textract(".$item_alias." instanceof fx_content ? ".$item_alias."->get_fields_to_show() : get_object_vars(".$item_alias."));\n";
            $code .= "\t}\n";
        }
        $meta_test = "\tif (fx::env('is_admin') && (".$item_alias." instanceof fx_essence) ) {\n";
        $code .= $meta_test;
        $code .= "\t\tob_start();\n";
        $code .= "\t}\n";
        $code .= $this->_token_to_code($token);
        $code .= $meta_test;
        $code .= "\t\techo ".$item_alias."->add_template_record_meta(ob_get_clean());\n";
        $code .= "\t}\n";
        $code .= "}\n"; // close foreach
        $code .= "}\n?>"; // close if
        return $code;
    }
    
    protected function _token_template_to_code($token) {
        $this->add_template($token);
    }
    
    protected function _token_area_to_code($token) {
        return '<?=$this->render_area("'.$token->get_prop('id').'")?>';
    }
    
    protected function _token_if_to_code($token) {
        $code  = '<?';
        $code .= 'if ('.$token->get_prop('test').") {\n";
        $code .= $this->_token_to_code($token)."\n";
        $code .= "}\n?>";
        return $code;
    }


    protected function _token_to_code(fx_template_token $token) {
        $code = '?>';
        foreach ($token->get_children() as $child) {
            $method_name = '_token_'.$child->name.'_to_code';
            if (method_exists($this, $method_name)) {
                $code .= call_user_func(array($this, $method_name), $child, $token);
                continue;
            } else {
                $code .= '/'."* no method for ".$method_name." *".'/';
            }
        }
        $code .= "<?";
        return $code;
    }
    
    protected $_template_stack = array();
    
    protected function add_template(fx_template_token $token) {
        $this->_template_stack []= $token->get_prop('id');
        $this->templates [$token->get_prop('id')]= array('id' => $token->get_prop('id'));
        $code = $this->_token_to_code($token);
        if ( !($name = $token->get_prop('name'))) {
            $name = $token->get_prop('id');
        }
        if (! ($for = $token->get_prop('for'))) {
            if ($this->_controller_type == 'layout') {
                $for = 'layout.show';
            } else {
                $for = $this->_controller_type."_".$this->_controller_name.".".$token->get_prop('id');
            }
        }
        $this->templates [$token->get_prop('id')] += array(
            'code' => $code,
            'name' => $name,
            'for' => $for
        );
        array_pop($this->_template_stack);
    }
    
    protected function _get_current_template_id() {
        return end($this->_template_stack);
    }
    
    protected $_class_code = null;
    protected function  make_code(fx_template_token $tree, $class_code) {
        $this->_class_code = preg_replace('~[^a-z0-9]+~', '_', $class_code);
        foreach ($tree->get_children() as $token) {
            $this->add_template($token);
        }
        ob_start();
        echo '<'."?\n";
        echo 'class fx_template_'.$this->_class_code." extends fx_template {\n";
        if ( ($source_dir = $tree->get_prop('source') ) ) {
            echo 'protected $_source_dir = "'.$source_dir.'";'."\n";
        }
        echo 'protected $_template_code = "'.$this->_class_code."\";\n";
        
        $tpl_var = array();
        foreach ( $this->templates as $tpl_name => $tpl) {
            echo $this->pad()."public function tpl_".$tpl_name.'() {'."\n";
            echo $this->pad(2).$tpl["code"];
            echo "\n".$this->pad()."}\n";
            $tpl_meta = $tpl;
            unset($tpl_meta['code']);
            $tpl_var []= $tpl_meta;
        }
        echo 'protected $_templates = '.var_export($tpl_var,1).";\n";
        echo "}\n?".'>';
        $code = ob_get_clean();
        return $code;
    }
    
    protected function pad($count = 1) {
        return str_repeat(' ', 4*$count);
    }
    
    /**
     * создать токен из исходника
     * @param string $source
     * @return fx_template_token
     */
    public static function create_token($source) {
        $props = array();
        if (preg_match('~^\{~', $source)) {
            $input_source = $source;
            $source = preg_replace("~^\{|\}$~", '', $source);
            $is_close = preg_match('~^\/~', $source);
            preg_match("~^\/?([^\s\/\}]+)~", $source, $name);
            $source = substr($source, strlen($name[0]));
            $name = $name[1];
            $type_info = self::get_token_info($name);
            if (preg_match("~^[\\\$%]~", $name, $var_marker)) {
                $props['id'] = preg_replace("~^[\\\$%]~", '', $name);
                $props['var_type'] = $var_marker[0] == '%' ? 'visual' : 'data';
                $name = 'var';
            }
            if (preg_match("~\/$~", $source)) {
                $type = 'single';
            } elseif ($is_close) {
                $type = 'close';
            } elseif ($type_info['type'] == 'single') {
                $type = 'single';
            } elseif ($type_info['type'] == 'double') {
                $type = 'open';
            } else {
                $type = 'unknown';
            }
            // добавляем отсутствующие кавычки атрибутов
            $source  = preg_replace("~\s([a-z]+)\s*?=\s*?([^\'\\\"\s]+)~", ' $1="$2"', $source);
            preg_match_all('~([a-z]+)="([^\"]+)"~', $source, $atts);
            foreach ($atts[1] as $att_num => $att_name) {
                $props[$att_name] = $atts[2][$att_num];
            }
        } else {
            $type = 'single';
            $name = 'code';
            $props['value'] = $source;
        }
        return new fx_template_token($name, $type, $props);
    }
}

class fx_template_token {
    public $name = null;
    public $type = null;
    public $props = array();
    
    /**
     * 
     * @param type $name название токена, e.g. "template"
     * @param type $type тип - open/close/single
     * @param type $props атрибуты токена
     */
    public function __construct($name, $type, $props) {
        $this->name = $name;
        $this->type = $type;
        $this->props = $props;
    }
    
    public function add_child(fx_template_token $token) {
        if (!isset($this->children)) {
            $this->children = array();
        }
        $this->children []= $token;
    }
    
    public function clear_children() {
        $this->children = array();
    }
    
    public function get_children() {
        return isset($this->children) ? $this->children : array();
    }
    
    public function has_children() {
        return isset($this->children) && count($this->children) > 0;
    }
    
    public function get_prop($name) {
        return isset($this->props[$name]) ? $this->props[$name] : null;
    }
    
    public function show() {
        $r = '['.($this->type == 'close' ? 
                '/' : 
                ($this->type == 'unknown' ? '?' : '')).$this->name.' ';
        foreach ($this->props as $pk => $pv) {
            $r .= $pk.'="'.$pv.'" ';
        }
        $r .= ']';
        return $r;
    }
}
?>
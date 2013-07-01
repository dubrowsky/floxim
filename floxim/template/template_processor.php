<?php
class fx_template_processor {
    /**
     * Преобразовать шаблон в php-код
     * @param string $source исходник шаблона
     * @param string $code код для класса
     * @return string php-код
     */
    public function process($source, $code) {
        //dev_log('processing fx tpl');
        $source = str_replace("{php}", '<?', $source);
        $source = str_replace("{/php}", '?>', $source);
        
        $tokens = $this->tokenize($source);
        $tree = $this->_make_tree($tokens);
        $code = $this->_make_code($tree, $code);
        return $code;
    }
    /**
     * Преобразовать директорию с шаблономи в php-код
     * @param string $source_dir путь к директории
     * @return string php-код
     */
    public function process_dir($source_dir) {
        $this->source_dir = $source_dir;
        $this->template_dir = str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->source_dir).'/';
        $tpl_name_parts = null;
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
            $file_data = preg_replace("~\{\*.*?\*\}~s", '', $file_data);
            
            // удаляем пробелы в начале строки, 
            // если она начинается с {...}
            $file_data = preg_replace(
                "~\s*?[\n\r]+\s*?(\{.+?\})~s", 
                '\1', 
                $file_data
            );
            // или заканчивается на {...}
            $file_data = preg_replace(
                "~(\{.+?\}\s*?)[\n\r]+\s*~s",
                '\1',
                $file_data
            );
            
            // Проверяем наличие fx-атрибутов в разметке файла
            if (fx_template_html::has_floxim_atts($file_data)) {
                $T = new fx_template_html($file_data);
                $file_data = $T->transform_to_floxim();
            }
            
            if (!preg_match("~^{template~", $file_data)) {
                $file_tpl_name = null;
                preg_match("~/([^/]+)\.tpl~", $file, $file_tpl_name);
                $file_data = 
                    '{template id="'.$file_tpl_name[1].'"}'.
                       trim($file_data).
                    '{/template}';
            }
            
            $source .= trim($file_data);
        }
        $source .= '{/templates}';
        $code = $this->process($source, $tpl_name);
        $tpl_dir = fx::config()->COMPILED_TEMPLATES_FOLDER;
        if ( !is_writable($tpl_dir) ) {
            die ('Can not write to directory' . fx::config()->COMPILED_TEMPLATES_FOLDER);
        }
        $target = $tpl_dir.'/'.$tpl_name.'.php';
        $fh = fopen($target, 'w');
        fputs($fh, $code);
        fclose($fh);
    }
    
    public function tokenize($source) {
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
            'contains' => array('code', 'template', 'area', 'var', 'call', 'each','if')
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
            'contains' => array('code', 'var', 'call', 'area', 'template', 'each','if')
        ),
        'call' => array(
            'type' => 'both',
            'contains' => array('var', 'each', 'if')
        ),
        'templates'=> array(
            'type' => 'double',
            'contains' => array('template')
        ),
        'each' => array(
            'type' => 'both',
            'contains' => array('code', 'template', 'area', 'var', 'call', 'each', 'if')
        ),
        'if' => array(
            'type' => 'double',
            'contains' => array('code', 'template', 'area', 'var', 'call', 'each', 'elseif', 'else')
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


    protected function _make_tree($tokens) {
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
                    $closed_token = array_pop($stack);
                    if ($closed_token->name =='template') {
                        $this->_template_to_each($closed_token);
                    }
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
        return $root;
    }
    
    protected function _template_to_each(fx_template_token $token) {
        $children = $token->get_children();
        $subtemplates = array();
        $each_token = false;
        $tpl_id = $token->get_prop('id');
        $each_select = '.';
        if (substr($tpl_id, 0, 1) == '$') {
            $token->name = 'if';
            $token->set_prop('test', $tpl_id);
            $each_select = $tpl_id;
        }
        foreach ($children as $child_num => $child) {
            if (
                    $child->name == 'template' && 
                    in_array($child->get_prop('id'), array(
                        'active', 
                        'inactive',
                        'active_link',
                        'separator',
                        'item'
                    ))
                ) {
                $subtemplates[$child->get_prop('id')] = $child;
                if (!$each_token) {
                    $each_token = new fx_template_token('each', 'open', array('select' => $each_select));
                    $token->set_child($each_token, $child_num);
                } else {
                    $token->set_child(NULL, $child_num);
                }
            }
        }
        if (count($subtemplates) == 0) {
            return;
        }
        
        //echo fen_debug($subtemplates, $each_token);
        
        // выбираем дефолтный шаблон - либо inactive, либо item
        $basic_tpl = null;
        $basic_cond = null;
        if (isset($subtemplates['inactive'])) {
            $basic_tpl = $subtemplates['inactive'];
        } elseif (isset($subtemplates['item'])) {
            $basic_tpl = $subtemplates['item'];
        }
        
        $conds = array();
        if ($basic_tpl) {
            
            // собираем дефолтное условие
            // если есть шаблоны для active | active_link, 
            // дефолтный для таких не используем
            if (isset($subtemplates['active'])) {
                $conds['active']= '$item["active"]';
            }
            if (isset($subtemplates['active_link'])) {
                $conds ['active_link']= '$item["active_link"]';
            }
            $basic_cond = count($conds) == 0 ? null : '!('.join(" && ", $conds).')';
        }
        
        // есть варианты
        if ($basic_cond) {
            $basic_cond_token = new fx_template_token('if', 'open', array('test' => $basic_cond));
            $basic_cond_token->add_children($basic_tpl->get_children());
            $each_token->add_child($basic_cond_token);
            if (isset($subtemplates['active_link'])) {
                $active_link_cond = new fx_temlate_token(
                    'if', 'open', array('test' => $conds['active_link'])
                );
                $active_link_cond->add_children($subtemplates['active_link']->get_children());
                $each_token->add_child($active_link_cond);
            }
            if (isset($subtemplates['active'])) {
                $active_cond_test = $conds['active'];
                if (isset($conds['active_link'])) {
                    $active_cond_test .= ' && !'.$conds['active_link'];
                } else {
                    $active_cond_test .= ' || $item["active_link"]';
                }
                $active_cond = new fx_template_token('if', 'open', 
                        array('test' => $active_cond_test)
                );
                $active_cond->add_children($subtemplates['active']->get_children());
                $each_token->add_child($active_cond);
            }
        }
        // только один подшаблон
        else {
            /*foreach ($subtemplates as $subtpl) {
                $each_token->add_children($subtpl->get_children());
            }*/
            $each_token->add_children($basic_tpl->get_children());
        }
        
        if (isset($subtemplates['separator'])) {
            $each_token->add_child($subtemplates['separator']);
        }
        
    }
    
    protected $templates = array();
    
    protected $_code_context = 'text';
    
    protected function _token_code_to_code($token) {
        return $token->get_prop('value');
    }
    
    protected function _token_call_to_code(fx_template_token $token) {
        $code = "<?\n";
        $tpl_name = $token->get_prop('id');
        if (!preg_match("~\.~", $tpl_name)) {
            $tpl_name = $this->_class_code.".".$tpl_name;
        }
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
        $code .= 'echo $tpl_to_call->render($this->data);?>';
        $current_template =& $this->templates[$this->_get_current_template_id()];
        if (!isset($current_template['calls'])) {
            $current_template['calls'] = array();
        }
        $current_template['calls'] []= $token->get_prop('id');
        return $code;
    }
    
    protected function _token_var_to_code(fx_template_token $token) {
        $var_id = $token->get_prop('id');
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
            if ($token->get_prop('type') == 'image') {
                $code .= 'fx_filetable::get_path(${"'.$var_parts[0].'"})';
            } else {
                $code .= '${"'.$var_parts[0].'"}';
            }
        }
        $code .= ";\n";
        $code .= "}\n";
        /*
        $code .= "} else {\n";
        $code .= "\t".$var_tmp.' = fx::dig($this->data, "'.$var_id.'");'."\n";
        $code .= "}";
         * 
         */
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
            $code .= 'if (fx::is_admin() && !('.$var_tmp." instanceof fx_template_field)) {\n";
            $code .= "\t".$var_tmp." = new fx_template_field(".$var_tmp.", ";
            $code .= 'array("id" => "'.$var_id.'", ';
            $code .= '"var_type" => "visual", ';
            $code .= '"type" => "'.$token->get_prop('type').'", ';
            $code .= '"infoblock_id" => fx::dig($this->data, "infoblock.id"), ';
            $code .= '"template" => $this->_get_template_sign(), ';
            if ( ( $var_title = $token->get_prop('title')) ) {
                $code .= '"title" => "'.$var_title.'", ';
            }
            if($token->get_prop('type') =='image') {
                $code .= '"filetable_id" => ${"'.$var_parts[0].'"}, ';
            }
            $code .= '"editable" => true))'.";\n";
            $code .= "}\n";
        }
        if ( ( $modifiers = $token->get_prop('modifiers') ) ) {
        	//$code .= $var_tmp.' = $this->_apply_modifiers('.$var_tmp.', '.var_export($modifiers,1).");\n";
        	$code .= '$val = '.$var_tmp.' instanceof fx_template_field ? '.$var_tmp.'->get_value() : '.$var_tmp.";\n";
        	foreach ($modifiers as $mod) {
        		$callback = array_shift($mod);
        		//$code .= 'if (is_callable("'.$callback.'")) {'."\n"; 		// test callable
        			if ($callback == 'fx_default') {
        				$code .= 'if ('.$var_tmp.' instanceof fx_template_field) {'."\n";
        				$code .= 'switch ('.$var_tmp.'->get_meta("type")) {'."\n";
        				$code .= "case 'datetime':\n";
        				$code .= '$callback = "fx_template_field::format_date";'."\nbreak;\n";
        				$code .= "case 'image':\n";
        				$code .= '$callback = "fx_template_field::format_image";'."\nbreak;\n";
        				$code .= "}\n";
        				$code .= "}\n";
        			} else {
						if (!is_callable($callback)) {
							continue;
						}
					}
					$self_key = array_keys($mod, "self");
					if (isset($self_key[0])) {
						$mod[$self_key[0]] = '$val';
					} else {
						array_unshift($mod, '$val');
					}
					if ($callback == 'fx_default') {
						$code .= '$val = call_user_func($callback, ';
					} else {
						$code .= '$val = '.$callback.'(';
					}
					$code .= join(", ", $mod);
					$code .= ");\n";
				//$code .= "}\n";											// end test callable
        	}
        	$code .= 'if ('.$var_tmp.' instanceof fx_template_field) {'."\n";
			$code .= $var_tmp.'->set_meta("display_value", $val)'.";\n";
			$code .= "} else {\n";
			$code .= $var_tmp.' = $val'.";\n";
			$code .= "}\n";
        }
        $code .= "\necho ".$var_tmp.";\n";
        $code .= "unset(".$var_tmp.");\n";
        $code .= "\n?>";
        return $code;
    }
    
    protected $_loop_depth = 0;
    protected function _token_each_to_code(fx_template_token $token) {
        $this->_loop_depth++;
        $code = "<?\n";
        $arr_id = $token->get_prop('select');
        $item_key = false;
        $item_alias = false;
        $arr_id_parts = null;
        if (preg_match("~(.+?)\sas\s(.+)$~", $arr_id, $arr_id_parts)) {
            $arr_id = trim($arr_id_parts[1]);
            $as_parts = explode("=>", $arr_id_parts[2]);
            if (count($as_parts) == 2) {
                $item_key = trim($as_parts[0]);
                $item_alias = trim($as_parts[1]);
            } else {
                $item_alias = trim($as_parts[0]);
            }
        }
        if (! $arr_id || $arr_id == '.') {
            //$arr_id = '$this->get_var("input.items")';
            $arr_id = '$items';
        }
        $arr_hash_name = '$arr_'.md5($arr_id);
        $code .= $arr_hash_name .'= '.$arr_id.";\n";
        $arr_id = $arr_hash_name;
        
        if (!$item_alias && ! ($item_alias = $token->get_prop('as') ) ) {
            $item_alias = '$item';
            if ($this->_loop_depth > 1) {
                $item_alias .= '_'.$this->_loop_depth;
            }
        }
        if (!$item_key && !($item_key = $token->get_prop('key'))) {
            $item_key = $item_alias.'_key';
        }
        if (! ($extract = $token->get_prop('extract'))) {
            $extract = true;
        }
        $separator = null;
        foreach ($token->get_children() as $each_child_num => $each_child) {
            if ($each_child->name == 'template' && $each_child->get_prop('id') == 'separator') {
                $separator = $each_child;
                $token->set_child(null, $each_child_num);
                break;
            }
        }
        $counter_id = $item_alias."_index";
        $code .= "if( " . $arr_id . " instanceof fx_content ) {\n ";
        $code .= $arr_id . " = array(" . $arr_id . ");\n";
        $code .= "}\n";
        $code .= "if (is_array(".$arr_id.") || ".$arr_id." instanceof Traversable) {\n";
        $code .= $counter_id." = 0;\n";
        $code .= $item_alias."_total = count(".$arr_id.");\n";
        $code .= "\$_is_admin = fx::is_admin();\n";
        $code .= "\nforeach (".$arr_id." as ".$item_key." => ".$item_alias.") {\n";
        $code .= $counter_id."++;\n";
        $code .= $item_alias."_is_first = ".$counter_id." === 1;\n";
        $code .= $item_alias."_is_last = ".$item_alias."_total == ".$counter_id.";\n";
        $code .= $item_alias."_is_odd = ".$counter_id." % 2 != 0;\n";
        if ($extract) {
            $code .= "\tif (is_array(".$item_alias.")) {\n";
            $code .= "\t\textract(".$item_alias.");\n";
            $code .= "\t} elseif (is_object(".$item_alias.")) {\n";
            $code .= "\t\textract(".$item_alias." instanceof fx_content ? ".$item_alias."->get_fields_to_show() : get_object_vars(".$item_alias."));\n";
            $code .= "\t}\n";
        }
        $meta_test = "\tif (\$_is_admin && (".$item_alias." instanceof fx_essence) ) {\n";
        $code .= $meta_test;
        $code .= "\t\tob_start();\n";
        $code .= "\t}\n";
        $code .= $this->_token_to_code($token);
        $code .= $meta_test;
        $code .= "\t\techo ".$item_alias."->add_template_record_meta(ob_get_clean());\n";
        $code .= "\t}\n";
        if ($separator) {
            $code .= 'if (!'.$item_alias."_is_last) {\n";
            $code .= $this->_token_to_code($separator);
            $code .= "}\n";
        }
        $code .= "}\n"; // close foreach
        $code .= "}\n";  // close if
        $code .= "\n?>";
        $this->_loop_depth--;
        return $code;
    }
    
    protected function _token_template_to_code($token) {
        $this->add_template($token);
    }
    
    protected function _token_area_to_code($token) {
        $res = '<?=$this->render_area('.var_export($token->get_all_props(),1).')?>';
        foreach ($token->get_children() as $child) {
            if ($child->name == 'template') {
                $child->set_prop('area', $token->get_prop('id'));
                $this->add_template($child);
            }
        }
        return $res;
    }
    
    protected function _token_if_to_code($token) {
        $code  = '<?';
        $code .= 'if ('.$token->get_prop('test').") {\n";
        $code .= $this->_token_to_code($token)."\n";
        $code .= "}\n?>";
        return $code;
    }
    
    protected function _token_js_to_code($token) {
        return $this->_token_headfile_to_code($token, 'js');
    }
    
    protected function _token_css_to_code($token) {
        return $this->_token_headfile_to_code($token, 'css');
    }
    
    protected function _token_headfile_to_code($token, $type) {
        $code .= "<?\n";
        $tpl_path = $this->template_dir;
        foreach ($token->get_children() as $js_set) {
            $js_set = preg_split("~[\n\r]~", $js_set->get_prop('value'));
            foreach ($js_set as $js_file) {
                $js_file = trim($js_file);
                if (empty($js_file)) {
                    continue;
                }
                $res_string = '';
                // constant
                if (preg_match("~^[A-Z0-9_]+$~", $js_file)) {
                    $res_string = $js_file;
                } elseif (
                        !preg_match("~^/~", $js_file) && 
                        !preg_match("~^https?://~", $js_file)) {
                    $res_string = '"'.$tpl_path.$js_file.'"';
                } else {
                    $res_string = '"'.$js_file.'"';
                }
                $code .= 'fx::page()->add_'.$type.'_file('.$res_string.");\n";
            }
        }
        $code .= "\n?>";
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
        if ($token->name != 'template') {
            return;
        }
        $this->_template_stack []= $token->get_prop('id');
        $this->templates [$token->get_prop('id')]= array('id' => $token->get_prop('id'));
        $code = $this->_token_to_code($token);
        if ( !($name = $token->get_prop('name'))) {
            $name = $token->get_prop('id');
        }
        if (! ($of = $token->get_prop('of'))) {
            if ($this->_controller_type == 'layout') {
                $of = 'layout.show';
            } else {
                $of = $this->_controller_type."_".$this->_controller_name.".".$token->get_prop('id');
            }
        }
        $this->templates [$token->get_prop('id')] += array(
            'code' => $code,
            'name' => $name,
            'of' => $of
        );
        array_pop($this->_template_stack);
    }
    
    protected function _get_current_template_id() {
        return end($this->_template_stack);
    }
    
    protected $_class_code = null;
    protected function  _make_code(fx_template_token $tree, $class_code) {
        $this->_class_code = preg_replace('~[^a-z0-9]+~', '_', $class_code);
        foreach ($tree->get_children() as $token) {
            $this->add_template($token);
        }
        ob_start();
        echo '<'."?\n";
        echo 'class fx_template_'.$this->_class_code." extends fx_template {\n";
        if ( ($source_dir = $this->source_dir ) ) {
            $template_dir = $this->template_dir;
            echo 'protected $_source_dir = "'.$source_dir.'";'."\n";
        }
        
        $tpl_var = array();
        foreach ( $this->templates as $tpl_name => $tpl) {
            echo $this->pad()."public function tpl_".$tpl_name.'() {'."\n";
            if (isset($template_dir)) {
                echo $this->pad(2)."\$template_dir = '".$template_dir."';\n";
            }
            echo $this->pad(2).'extract($this->data);'."\n";
            echo $this->pad(2).$tpl["code"];
            echo "\n".$this->pad()."}\n";
            $tpl_meta = $tpl;
            unset($tpl_meta['code']);
            $tpl_meta['full_id'] = $this->_class_code.'.'.$tpl['id'];
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
        
        if (!preg_match('~^\{~', $source)) {
        	$type = 'single';
            $name = 'code';
            $props['value'] = $source;
            return new fx_template_token($name, $type, $props);
        }
        $props = array();
		$source = preg_replace("~^\{|\}$~", '', $source);
		$is_close = preg_match('~^\/~', $source);
		preg_match("~^\/?([^\s\/\\|}]+)~", $source, $name);
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
			$source = preg_replace("~/$~", '', $source);
		} elseif ($is_close) {
			$type = 'close';
		} elseif ($type_info['type'] == 'single') {
			$type = 'single';
		} elseif ($type_info['type'] == 'double') {
			$type = 'open';
		} else {
			$type = 'unknown';
		}
		if ($name == 'if' && $type == 'open' && !preg_match('~test=~', $source)) {
			$props['test'] = $source;
		} else {
			// добавляем отсутствующие кавычки атрибутов
			$source  = preg_replace("~\s([a-z]+)\s*?=\s*?([^\'\\\"\s]+)~", ' $1="$2"', $source);
			
			$source = preg_replace_callback(
				'~([a-z]+)="([^\"]+)"~',
				function ($matches) use (&$props) {
					$props[$matches[1]] = $matches[2];
					return '';
				},
				$source
			);
			
			if ($name == 'var' && preg_match("~^\s*\|~", $source)) {
				$source = preg_replace("~^\s*\|~", '', $source);
				$source = trim($source);
				$source .= '|';
				$source = preg_split("~(\s*\|\s*|\s*:\s*|[\'\"])~", $source, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
				if (count($source) > 0) {
					$modifiers = array();
					$c_modifier = array();
					$c_state = 'default';
					$c_arg_quote = '';
					$c_arg = '';
					foreach ($source as $chunk_num => $chunk) {
						$is_separator = preg_match("~^\s*\|\s*$~", $chunk);
						$is_arg_separator = preg_match("~^\s*:\s*$~", $chunk);
						if ($c_state == 'default') {
							// нормальное название модификатора
							if (preg_match("~^[a-z0-9_-]+$~i", $chunk)) {
								$c_modifier['name'] = $chunk;
								$c_state = 'modifier';
								$c_arg_quote = '';
								continue;
							}
							// вместо модификатора - аргумент в кавычках
							if ($chunk == '"' || $chunk = "'") {
								$c_modifier['name'] = 'fx_default';
								$c_state = 'arg';
								$c_arg_quote = $chunk;
								$c_arg = $chunk;
								continue;
							}
						}
						if ($c_state == 'arg') {
							if ( $c_arg_quote == '' && ($chunk == '"' || $chunk == "'")) {
								$c_arg_quote = $chunk;
								$c_arg .= $chunk;
								continue;
							}
							// закрылась кавычка аргумента
							if ( $c_arg_quote != '' && $chunk == $c_arg_quote ) {
								$c_arg .= $chunk;
								$c_arg_quote = '';
								continue;
							}
							// начался новый аргумент
							if ($c_arg_quote == '' && $is_arg_separator) {
								$c_modifier['args'][]= $c_arg;
								$c_arg = '';
								continue;
							}
							// конец аргумента и модификатора
							if ($c_arg_quote == '' && $is_separator) {
								$c_state = 'default';
								$c_modifier['args'] []= $c_arg;
								$modifiers []= $c_modifier;
								$c_modifier = array('args' => array());
								$c_arg = '';
								continue;
							}
							$c_arg .= $chunk;
							continue;
						}
						// конец модификатора
						if ($c_state == 'modifier' && $is_separator) {
							$c_state = 'default';
							$modifiers []= $c_modifier;
							$c_modifier = array('args' => array());
							//echo "Save mod<br />";
							//echo $c_state.'/'.htmlspecialchars($chunk)."<hr />";
							continue;
						}
						if ($c_state == 'modifier' && $is_arg_separator) {
							$c_state = 'arg';
							$c_arg = '';
							//echo $c_state.'/'.htmlspecialchars($chunk)."<hr />";
							continue;
						}
						
					}
					if (count($modifiers) > 0) {
						$props['modifiers'] = array();
						foreach ($modifiers as $modifier) {
							$c_mod = array($modifier['name']);
							if (isset($modifier['args'])) {
								$c_mod = array_merge($c_mod, $modifier['args']);
							}
							$props['modifiers'] []= $c_mod;
						}
					}
				}
			}
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
    
    public function add_children(array $children) {
        foreach ($children as $child) {
            $this->add_child($child);
        }
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
    
    public function set_child($child, $index) {
        if ($child === null) {
            unset($this->children[$index]);
        } else {
            $this->children[$index] = $child;
        }
    }


    public function set_prop($name, $value) {
        $this->props[$name] = $value;
    }
    
    public function get_prop($name) {
        return isset($this->props[$name]) ? $this->props[$name] : null;
    }
    
    public function get_all_props() {
        return $this->props;
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
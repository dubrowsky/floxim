<?php
/*
 * Класс превращает дерево токенов в готовый php-код
 */
class fx_template_compiler {
    protected $template_set_name = null;
    
    /**
     * Преобразовать дерево токенов в php-код
     * @param string $source исходник шаблона
     * @return string php-код
     */
    public function compile($tree) {
        $code = $this->_make_code($tree);
        $code = self::add_tabs($code);
        $is_correct = self::is_php_syntax_correct($code);
        if ($is_correct !== true) {
            $lines = explode("\n", $code);
            $lined = '';
            foreach ($lines as $ln => $l) {
                $lined .= $ln."\t".$l."\n";
            }
            fx::debug('Syntax error', $is_correct, $lined, $code);
            throw new Exception('Syntax error');
        }
        
        return $code;
    }
    
    public static function add_tabs($code) {
        $res = '';
        $level = 0;
        $code = preg_split("~[\n\r]+~", $code);
        foreach ($code as $s) {
            $s = trim($s);
            if (preg_match("~^[\}\)]~", $s)) {
                $level--;
            }
            $res .= str_repeat("    ", $level).$s."\n";
            if (preg_match("~[\{\(]$~", $s)) {
                $level++;
            }
        }
        return $res;
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
            $tpl_name = $this->_template_set_name.".".$tpl_name;
        }
        
        $code .= '$tpl_to_call = fx::template("'.$tpl_name.'"';
        /*if ( $token->get_prop('include') == 'true') {
            $code .= ', $this->data';
        }*/
        $code .= ');'."\n";
        $code .= '$tpl_to_call->set_parent($this)'.";\n";
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
                $code .= $this->_children_to_code($param_var_token);
                $code .= "\n";
                $value_to_set = 'ob_get_clean()';
            } elseif ( ($select_att = $param_var_token->get_prop('select') ) ) {
                // передаем результат выполнения php-кода
                $value_to_set = $select_att;
            }
            $code .= "\$tpl_to_call->set_var(".
                "'".$param_var_token->get_prop('id')."', ".
                $value_to_set.");\n";
        }
        $code .= 'echo $tpl_to_call->render();?>';
        return $code;
    }
    
    public function parse_expression($str) {
        static $expression_parser = null;
        if ($expession_parser === null) {
            require_once (dirname(__FILE__).'/template_expression_parser.php');
            $expression_parser = new fx_template_expression_parser();
        }
        return $expression_parser->compile($expression_parser->parse($str));
    }
    
    public function parse_var($var) {
        
    }
    
    protected function _token_var_to_code(fx_template_token $token) {
        $code = "<?\n";
        // parse var expression and store token 
        // to create correct expression for get_var_meta()
        $ep = new fx_template_expression_parser();
        $expr_token = $ep->parse('$'.$token->get_prop('id'));
        $expr = $ep->compile($expr_token);
        $var_token = $expr_token->last_child;
        
        $modifiers = $token->get_prop('modifiers');
        $token->set_prop('modifiers', null);
        
        if (!$token->get_prop('type')) {
            $token_type = 'string';
            foreach ($token->get_children() as $child) {
                if (preg_match("~<[a-z]+.*?>~", $child->get_prop('value'))) {
                    $token_type = 'html';
                    break;
                }
            }
            $token->set_prop('type', $token_type);
        }
        
        // e.g. "name" or "image_".$this->v('id')
        $var_id = preg_replace('~^\$this->v\(~', '', preg_replace("~\)$~", '', $expr));
        
        $has_default = $token->get_prop('default') || count($token->get_children()) > 0;
        
        // if var has default value or there are some modifiers
        // store real value for editing
        $real_val_defined = false;
        if ($modifiers || $has_default || $token->get_prop('inatt')) {
            $real_val_var = '$'.$this->varialize($var_id).'_real_val';
            $code .= $real_val_var . ' = '.$expr.";\n";
            $real_val_defined = true;
        }
        if ($has_default) {
            $code .= "\nif (!".$real_val_var.") {\n";
            if (!($default = $token->get_prop('default')) ) {
                $code .= "\tob_start();\n";
                $code .= "\t".$this->_children_to_code($token);
                $default = "ob_get_clean()";
            }
            if ($token->get_prop('var_type') == 'visual') {
                $code .= "\n".'$this->set_var('.$var_id.',  '.$default.");\n";
            } else {
                $code .= "\n".$real_val_var.' = '.$default.";\n";
            }
            $code .= "}\n";
        }
        
        // Expression to get var meta
        $var_meta_expr = '$this->get_var_meta(';
        // if var is smth like $item['parent']['url'], 
        // it should be get_var_meta('url', fx::dig( $this->v('item'), 'parent'))
        if ($var_token->last_child) {
            $last_index = $var_token->pop_child();
            $var_meta_expr .= $ep->compile($last_index).', ';
            $var_meta_expr .= $ep->compile($var_token).')';
        } else {
            $var_meta_expr .= '"'.$token->get_prop('id').'")';
        }
        
        $var_meta_defined = false;
        // if the var is not visual and there is 'default' modifier
        // we shoud find and store var type first
        if (!$token->get_prop('type') && $modifiers) {
            $has_default_modifier = false;
            foreach ($modifiers as $mod) {
                if ($mod[0] == 'fx_default') {
                    $has_default_modifier = true;
                    break;
                }
            }
            if ($has_default_modifier) {
                $code .= '$var_meta = '.$var_meta_expr.";\n";
                $code .= '$var_type = $var_meta["type"]'.";\n";
                $var_meta_defined = true;
            }
        }
        $code .= '$this->print_var('."\n";
        if (!$modifiers || count($modifiers) == 0) {
            $code .= $expr;
        } else {
            $modified = $expr;
            foreach ($modifiers as $mod) {
                //$modified = $mod
                $mod_callback = array_shift($mod);
                if ($mod_callback == 'fx_default') {
                    if ($token->get_prop('type')) {
                        $mod_callback = $token->get_prop('type') == 'image' ? 'fx::image' : 'fx::date';
                        $mod_callback .= '(';
                    } else {
                        $mod_callback = 'call_user_func(';
                        $mod_callback .= '($var_type == "image" ? "fx::image" : ';
                        $mod_callback .= '($var_type == "datetime" ? "fx::date" : "fx::cb")), ';
                    }
                } else {
                    $mod_callback .= '(';
                }
                $args = array();
                $self_used = false;
                foreach ($mod as $arg) {
                    if ($arg == 'self') {
                        $args []= $modified;
                        $self_used = true;
                    } else {
                        $args []= self::parse_expression($arg);
                    }
                }
                if (!$self_used) {
                    array_unshift($args, $modified);
                }
                $modified = $mod_callback.join(', ', $args).')';
            }
            $code .= $modified;
        }
        $code .= ", \n";
        $code .= ' !$_is_admin ? null : ';
        if ($token->get_prop('var_type') == 'visual') {
            $token_props = $token->get_all_props();
            $code .= " array(";
            $tp_parts = array('"template" => $this->_get_template_sign()');
            
            foreach ($token_props as $tp => $tpval) {
                $tp_parts[]= "'".$tp."' => ". ($tp == 'id' ? $var_id : "'".addslashes($tpval)."'");
            }
            $code .= join(", ", $tp_parts);
            $code .= ")";
        } else {
            $code .= $var_meta_defined ? '$var_meta' : $var_meta_expr;
        }
        if ($token->get_prop('editable') == 'false') {
            $code .= ' + array("editable"=>false)';
        }
        if ($real_val_defined) {
            $code .= ' + array("real_value" => '.$real_val_var.')';
        }
        $code .= "\n);\n";
        $code .= "?>";
        return $code;
    }
    
    protected function __token_var_to_code(fx_template_token $token) {
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
            $code .= 'fx::dig(${"'.$var_parts[0].'"}, "'.$var_parts[1].'")'.";\n";
        } else {
            if ($token->get_prop('type') == 'image' && $token->get_prop('var_type') == 'visual') {
                $code .= 'fx_filetable::get_path(${"'.$var_parts[0].'"})'.";\n";
            } else {
                $code .= '${"'.$var_parts[0].'"}'.";\n";
            }
        }
        $code .= "}\n";
        if ($token->get_prop('var_type') == 'visual') {
            $code .= " else {\n";
            $code .= "\t".$var_tmp.' = $this->get_parent_var("'.$var_parts[0].'");'."\n";
            $code .= "}\n";
        }
        
        if ($token->get_prop('default') || count($token->get_children()) > 0) {
            // default values for template/visual vars
            if ($token->get_prop('var_type') == 'visual') {
                $code .= "\nif (is_null(".$var_tmp.")) {\n";
                if (!($default = $token->get_prop('default')) ) {
                    $code .= "\tob_start();\n";
                    $code .= "\t".$this->_children_to_code($token);
                    $default = "ob_get_clean()";
                }
                $code .= "\n\t".$var_tmp .' = '.$default.";\n";
                $code .= '${"'.$var_parts[0].'"} = '.$var_tmp.";\n";
                $code .= "}\n";
            } 
            // default values for content vars
            else {
                $code .= 'if ('.
                            '!isset('.$var_tmp.') || '.
                            '('.$var_tmp.' instanceof fx_template_field && '.
                                '!'.$var_tmp.'->get_value() || !'.$var_tmp.')) {'."\n";
                
                if (!($default = $token->get_prop('default')) ) {
                    $code .= "\tob_start();\n";
                    $code .= "\t".$this->_children_to_code($token);
                    $default = "ob_get_clean()";
                }
                $code .= "if (".$var_tmp." instanceof fx_template_field) {\n";
                $code .= $var_tmp."->set_value(".$default.");\n";
                $code .= "} else {\n";
                $code .= "\n\t".$var_tmp .' = '.$default.";\n";
                $code .= "}\n";
                $code .= "}\n";
            }
        }
        if ($token->get_prop('var_type') == 'visual' && !($token->get_prop('editable') == 'false')) {
            if (!$token->get_prop('type')) {
                $token_type = 'string';
                foreach ($token->get_children() as $child) {
                    if (preg_match("~<[a-z]+.*?>~", $child->get_prop('value'))) {
                        $token_type = 'html';
                        break;
                    }
                }
                $token->set_prop('type', $token_type);
            }
            $code .= 'if ($_is_admin && !('.$var_tmp." instanceof fx_template_field)) {\n";
            $code .= "\t".$var_tmp." = new fx_template_field(".$var_tmp.", ";
            $code .= 'array("id" => "'.$var_id.'", ';
            $code .= '"var_type" => "visual", ';
            $code .= '"type" => "'.$token->get_prop('type').'", ';
            //$code .= '"infoblock_id" => fx::dig($this->data, "infoblock.id"), ';
            $code .= '"infoblock_id" => $infoblock["id"], ';
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
            $code .= '$val = '.$var_tmp.' instanceof fx_template_field ? '.$var_tmp.'->get_value() : '.$var_tmp.";\n";
            $code .= "\$callback = null;\n";
            foreach ($modifiers as $mod) {
                $callback = array_shift($mod);
                if ($callback == 'fx_default') {
                    $code .= 'if ('.$var_tmp.' instanceof fx_template_field) {'."\n";
                    $code .= 'switch ('.$var_tmp.'->get_meta("type")) {'."\n";
                    $code .= "case 'datetime':\n";
                    $code .= '$callback = "fx::date";'."\nbreak;\n";
                    $code .= "case 'image':\n";
                    $code .= '$callback = "fx::image";'."\nbreak;\n";
                    $code .= "}\n";
                    $code .= "}\n";
                }
                $self_key = array_keys($mod, "self");
                if (isset($self_key[0])) {
                    $mod[$self_key[0]] = '$val';
                } else {
                    array_unshift($mod, '$val');
                }
                // check if we found a real callback for this type
                if ($callback == 'fx_default') {
                    $code .= "if (\$callback) {\n";
                }
                if ($callback == 'fx_default') {
                    $code .= '$val = call_user_func($callback, ';
                } elseif (preg_match("~\.~", $callback)) {
                    $tpl_parts = explode(".", $callback);
                    $code .= '$callback_tpl = new fx_template_'.$tpl_parts[0]."(\"".$tpl_parts[1]."\");\n";
                    $code .= '$callback_tpl->_parent = $this;'."\n";
                    $code .= '$val = $callback_tpl->render(';
                    //$code .= '$val = fx::template("'.$callback.'")->render(';
                } elseif (preg_match("~\$~", $callback)) {
                    $code .= '$val = call_user_func("'.$callback.'" ,';
                } else {
                    $code .= '$val = '.$callback.'(';
                }
                $code .= join(", ", $mod);
                $code .= ");\n";
                // end of callback's if
                if ($callback == 'fx_default') {
                    $code .= "}\n";
                }
            }
            $code .= 'if ('.$var_tmp.' instanceof fx_template_field && $_is_admin) {'."\n";
            $code .= $var_tmp.'->set_meta("display_value", $val)'.";\n";
            $code .= "} else {\n";
            $code .= $var_tmp.' = $val'.";\n";
            $code .= "}\n";
        }
        $restore_editable = false;
        if ($token->get_prop('editable') == 'false') {
            $restore_editable = true;
            $code .= 'if ('.$var_tmp.' instanceof fx_template_field) {'."\n";
            $code .= "\$stored_editable = ".$var_tmp."->get_meta('editable');\n";
            $code .= $var_tmp."->set_meta('editable', false);\n";
            $code .= "}\n";
        }
        if ($token->get_prop('type') == 'image') {
            $code .= 'if ($_is_admin && '.$var_tmp.' instanceof fx_template_field)'."{\n";
            $code .= "if (!".$var_tmp."->get_value()) {\n";
            $code .= $var_tmp."->set_meta('display_value', '/floxim/admin/images/0.gif');\n";
            $code .= "}\n";
            $code .= "} else {\n";
            $code .= "if (!".$var_tmp.") {\n";
            $code .= $var_tmp." = '/floxim/admin/images/0.gif';\n";
            $code .= "}\n";
            $code .= "}\n";
        }
        $code .= "\necho ".$var_tmp.";\n";
        if ($restore_editable) {
            $code .= "if (isset(\$stored_editable)) {\n";
            $code .= $var_tmp."->set_meta('editable', \$stored_editable);\n";
            $code .= "unset(\$stored_editable);\n";
            $code .= "}\n";
        }
        $code .= "unset(".$var_tmp.");\n";
        $code .= "\n?>";
        return $code;
    }
    
    protected function varialize($var) {
        return preg_replace("~^_+|_+$~", '', 
                preg_replace(
            '~[^a-z0-9_]+~', '_', 
            preg_replace('~(?:\$this\->v|fx\:\:dig)~', '', $var)
        ));
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
            $arr_id = '$items';
        }
        $arr_id = self::parse_expression($arr_id);
        if (!preg_match('~^\$[a-z0-9_]+$~', $arr_id)) {
            
            $arr_hash_name = '$arr_'.$this->varialize($arr_id);
            $code .= $arr_hash_name .'= '.$arr_id.";\n";
        } else {
            $arr_hash_name = $arr_id;
        }
        $arr_id = $arr_hash_name;
        
        if (!$item_alias && ! ($item_alias = $token->get_prop('as') ) ) {
            $item_alias = '$item';
            if ($this->_loop_depth > 1) {
                $item_alias .= '_'.$this->_loop_depth;
            }
        }
        $item_alias = preg_replace('~^\$~', '', $item_alias);
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
        $code .= '$'.$counter_id." = 0;\n";
        $code .= '$'.$item_alias."_total = count(".$arr_id.");\n";
        $code .= "\nforeach (".$arr_id." as \$".$item_key." => \$".$item_alias.") {\n";
        $code .= '$'.$counter_id."++;\n";
        $code .= '$this->context_stack[]= array('."\n";
        $code .= "'".$item_alias."' => \$".$item_alias.",\n";
        $code .= "'".$counter_id."' => \$".$counter_id.",\n";
        $code .= "'".$item_alias."_is_first' => \$".$counter_id." === 1,\n";
        $code .= "'".$item_alias."_is_last' => \$".$item_alias."_total == \$".$counter_id.",\n";
        $code .= "'".$item_alias."_total' => \$".$item_alias."_total\n";
        $code .= ");\n";
        $is_essence = '$'.$item_alias."_is_essence";
        $code .=  $is_essence ." = \$".$item_alias." instanceof fx_essence;\n";
        //$code .= $item_alias."_is_odd =45 ".$counter_id." % 2 != 0;\n";
        $use_extract = false;
        if ($use_extract && $extract && $extract !== 'false') {
            if ( ($e_prefix = $token->get_prop('prefix')) ) {
                $e_flags = ", EXTR_PREFIX_ALL, '".$e_prefix."'"; 
            } else {
                $e_flags = '';
            }
            $code .= "\tif (".$is_essence.") {\n";
            $code .= "\t\textract(".$item_alias."->get_fields_to_show() ".$e_flags.");\n";
            $code .= "} elseif (is_array(".$item_alias.")) {\n";
            $code .= "\t\textract(".$item_alias." ".$e_flags.");\n";
            $code .= "\t} elseif (is_object(".$item_alias.")) {\n";
            $code .= "\t\textract(get_object_vars(".$item_alias.") ".$e_flags.");\n";
            $code .= "\t}\n";
        }
        if (!$use_extract) {
            //$code .= '$this->context_stack[]= '.$is_essence.' ? '.$item_alias.'->get_fields_to_show() : '.$item_alias.";\n";
            $code .= '$this->context_stack[]= $'.$item_alias.";\n";
        }
        $meta_test = "\tif (\$_is_admin && ".$is_essence." ) {\n";
        $code .= $meta_test;
        $code .= "\t\tob_start();\n";
        $code .= "\t}\n";
        $code .= $this->_children_to_code($token);
        $code .= $meta_test;
        $code .= "\t\techo \$".$item_alias."->add_template_record_meta(".
                    "ob_get_clean(), ".
                    $arr_id.", ".
                    '$'.$counter_id." - 1, ".
                    ($token->get_prop('subroot') ? 'true' : 'false').
                ");\n";
        //$code .= "echo ob_get_clean();\n";
        $code .= "\t}\n";
        if ($separator) {
            $code .= 'if (!'.$item_alias."_is_last) {\n";
            $code .= $this->_children_to_code($separator);
            $code .= "\n}\n";
        }
        if (!$use_extract) {
            $code .= 'array_pop($this->context_stack);'."\n";
        }
        $code .= 'array_pop($this->context_stack);'."\n";
        $code .= "}\n"; // close foreach
        $code .= "}\n";  // close if
        $code .= "\n?>";
        $this->_loop_depth--;
        return $code;
    }
    
    protected function _token_template_to_code($token) {
        $this->_register_template($token);
    }
    
    protected function _token_area_to_code($token) {
        $token_props = var_export($token->get_all_props(),1);
        $res = '';
        $render_called = false;
        foreach ($token->get_children() as $child_num => $child) {
            if ($child->name == 'template') {
                $child->set_prop('area', $token->get_prop('id'));
                if (!$render_called) {
                    if ($child_num > 0) {
                        $res = 
                            "<?\n".
                            'if ($_is_admin) {'."\n".
                            'echo $this->render_area('.$token_props.', \'marker\');'."\n".
                            '}'."\n?>\n".
                            $res.
                            '<?=$this->render_area('.$token_props.', \'data\');?>';
                    } else {
                        $res .= '<?=$this->render_area('.$token_props.');?>';
                    }
                    $render_called = true;
                }
                $this->_register_template($child);
            } else {
                $res .= $this->_get_token_code($child, $token);
            }
        }
        if (!$render_called) {
            $res = $res .= '<?=$this->render_area('.$token_props.');?>'.$res;
        }
        return $res;
    }
    
    protected function _token_if_to_code($token) {
        $code  = "<?";
        $cond = $token->get_prop('test');
        $cond = trim($cond);
        /*
        $var_rex = '~(\$[a-z0-9_]+)~i';
        $parts = preg_split(
                $var_rex, 
                $cond, 
                -1, 
                PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );
        $cond = '';
        foreach ($parts as $pi => $p) {
            if (
                    preg_match($var_rex, $p) && 
                    ( !isset($parts[$pi+1]) || !preg_match("~^(\[|\->)~", $parts[$pi+1]) )
                ) {
                $p = "(".$p." instanceof fx_template_field ? ".$p."->get_value() : ".$p.")";
            }
            $cond .= $p;
        }
        */
        $cond = self::parse_expression($cond);
        $code .= 'if ('.$cond.") {\n";
        $code .= $this->_children_to_code($token)."\n";
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
                } elseif (!preg_match("~^(/|https?://)~", $js_file)) {
                    $res_string = '$template_dir."'.$js_file.'"';
                } else {
                    $res_string = '"'.$js_file.'"';
                }
                $code .= 'fx::page()->add_'.$type.'_file('.$res_string.");\n";
            }
        }
        $code .= "\n?>";
        return $code;
    }

    protected function _get_token_code($token, $parent) {
        $method_name = '_token_'.$token->name.'_to_code';
        if (method_exists($this, $method_name)) {
            return call_user_func(array($this, $method_name), $token, $parent);
        }
        return '';
    }

    protected function _children_to_code(fx_template_token $token) {
        $code = '?>';
        foreach ($token->get_children() as $child) {
            $code .= $this->_get_token_code($child, $token);
        }
        $code .= "<?";
        return $code;
    }
    
    protected function _register_template(fx_template_token $token) {
        if ($token->name != 'template') {
            return;
        }
        $tpl_props = array(
            'id' => $token->get_prop('id'),
            'file' => $this->_current_source_file
        );
        if ( ($offset = $token->get_prop('offset')) ) {
            $tpl_props['offset'] = $offset;
        }
        if ( ($size = $token->get_prop('size'))) {
            $tpl_props['size'] = $size;
        }
        if ( ($suit=  $token->get_prop('suit'))) {
            $tpl_props['suit'] = $suit;
        }
        if (  ($area_id = $token->get_prop('area'))) {
            $tpl_props['area'] = $area_id;
        }
        $this->templates [$token->get_prop('id')]= $tpl_props;
        
        $is_subroot = $token->get_prop('subroot') ? 'true' : 'false';
        
        // generate method
        $code = "public function tpl_".$token->get_prop('id').'() {'."\n";
        
        $template_path = str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', $this->_current_source_file);
        $template_path = str_replace('\\', '/', $template_path);
        $template_dir = preg_replace("~/[^/]+$~", '', $template_path).'/';
        
        $code .= "\$template_dir = '".$template_dir."';\n";
        $code .= "\$_is_admin = fx::is_admin();\n";
        //$code .= 'extract($this->data);'."\n";
        $code .= "\t\$this->is_subroot = ".($is_subroot).";\n";
        $code .= $this->_children_to_code($token);
        $code .= "\n}\n";
        if ( !($name = $token->get_prop('name'))) {
            $name = $token->get_prop('id');
        }
        $of = $token->get_prop('of');
        if ($of == 'menu') {
            $of = 'section.list';
        }
        
        $is_magic_of = in_array($of, array('block', 'menu'));
        
        
        if (!$of) {
            if ($this->_controller_type == 'layout') {
                $of = 'layout.show';
            } else {
                $of = $this->_controller_type."_".$this->_controller_name.".".$token->get_prop('id');
            }
        } elseif (!$is_magic_of && !preg_match("~\.~", $of ) ) {
            $of = $this->_controller_type."_".$this->_controller_name.".".$of;
        }
        
        if (!$is_magic_of && !preg_match("~^(layout|component|widget)_~", $of)) {
            $of = 'component_'.$of;
        }
        $this->templates [$token->get_prop('id')] += array(
            'code' => $code,
            'name' => $name,
            'of' => $of
        );
    }
    
    /*
     * Проходит по верхнему уровню, запуская сбор шаблонов вглубь
     */
    protected function _collect_templates($root) {
        foreach ($root->get_children() as $template_file_token) {
            $this->_current_source_file = $template_file_token->get_prop('source');
            foreach ($template_file_token->get_children() as $template_token) {
                $this->_register_template($template_token);
            }
        }
    }
    
    protected function  _make_code(fx_template_token $tree) {
        // Название класса/группы шаблонов
        $this->_template_set_name = $tree->get_prop('name');
        $this->_collect_templates($tree);
        ob_start();
        echo '<'."?\n";
        echo 'class fx_template_'.$this->_template_set_name." extends fx_template {\n";
        
        $tpl_var = array();
        foreach ( $this->templates as $tpl) {
            echo $tpl['code'];
            unset($tpl['code']);
            $tpl['full_id'] = $this->_template_set_name.'.'.$tpl['id'];
            $tpl_var []= $tpl;
        }
        echo 'protected $_templates = '.var_export($tpl_var,1).";\n";
        echo "}\n?".'>';
        $code = ob_get_clean();
        return $code;
    }
    
    /*
     * From comments: http://php.net/manual/en/function.php-check-syntax.php
     */
    public static function is_php_syntax_correct($code) {
        $braces = 0;
        $inString = 0;
        $code = preg_replace("~^\s*\<\?(php)?~", '', $code);
        $code = preg_replace("~\?>\s*$~", '', $code);
        // First of all, we need to know if braces are correctly balanced.
        // This is not trivial due to variable interpolation which
        // occurs in heredoc, backticked and double quoted strings
        foreach (token_get_all('<?php '.$code) as $token) {
            if (is_array($token)) {
                switch ($token[0])  {
                    case T_CURLY_OPEN:
                    case T_DOLLAR_OPEN_CURLY_BRACES:
                    case T_START_HEREDOC: ++$inString; break;
                    case T_END_HEREDOC:   --$inString; break;
                }
            } else if ($inString & 1) {
                switch ($token) {
                    case '`':
                    case '"': --$inString; break;
                }
            } else {
                switch ($token) {
                    case '`':
                    case '"': ++$inString; break;
                    case '{': ++$braces; break;
                    case '}':
                        if ($inString) {
                            --$inString;
                        } else {
                            --$braces;
                            if ($braces < 0) {
                                break 2;
                            }
                        }
                        break;
                }
            }
        }

        // Display parse error messages and use output buffering to catch them
        $prev_ini_log_errors = @ini_set('log_errors', false);
        $prev_ini_display_errors = @ini_set('display_errors', true);
        ob_start();

        // If $braces is not zero, then we are sure that $code is broken.
        // We run it anyway in order to catch the error message and line number.

        // Else, if $braces are correctly balanced, then we can safely put
        // $code in a dead code sandbox to prevent its execution.
        // Note that without this sandbox, a function or class declaration inside
        // $code could throw a "Cannot redeclare" fatal error.

        $braces || $code = "if(0){{$code}\n}";

        if (false === eval($code)) {
            if ($braces) {
                $braces = PHP_INT_MAX;
            } else {
                // Get the maximum number of lines in $code to fix a border case
                false !== strpos($code, "\r") && $code = strtr(str_replace("\r\n", "\n", $code), "\r", "\n");
                $braces = substr_count($code, "\n");
            }

            $code = ob_get_clean();
            $code = strip_tags($code);

            // Get the error message and line number
            if (preg_match("'syntax error, (.+) in .+ on line (\d+)$'s", $code, $code)) {
                $code[2] = (int) $code[2];
                $code = $code[2] <= $braces
                    ? array($code[1], $code[2])
                    : array('unexpected $end' . substr($code[1], 14), $braces);
            }
            $result = array('syntax error', $code);
        } else {
            ob_end_clean();
            $result = true;
        }

        @ini_set('display_errors', $prev_ini_display_errors);
        @ini_set('log_errors', $prev_ini_log_errors);

        return $result;
    }
}
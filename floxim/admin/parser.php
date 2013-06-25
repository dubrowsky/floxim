<?php

class fx_admin_parser {

    protected $php_code, $code_lines, $functions;
    protected $need_reparce = 1;

    public function __construct($php_code) {
        $this->php_code = $php_code;
    }

    public function get_parts($func_names) {
        
        $this->parse_functions();
        
        $parts = array();
        
        foreach ($func_names as $f) {
            $name = $f['name'];
            $content = $this->get_function_code($name);
            $parts[$name] = $this->get_function_content($content, $f['type']);
        }

        return $parts;
    }

    public function replace_parts($functions, $values) {
        
        $this->parse_functions();
        $content = $this->php_code;

        foreach ($functions as $function) {
            $name = $function['name'];
            $type = $function['type'];

            $new_function = $this->make_function_full($name, $type, $values[$name]);
            if ($new_function) {
                if (!$this->check_syntax($new_function))
                    throw new Exception( fx::lang('Синтаксическая ошибка в функции','system') . " " . $name . "()");
            }
            $old_function = $this->get_function_full($name);
            // функции в файле нет
            if (!$old_function) {
                if ($new_function) {
                    $brace_pos = strrpos($content, '}');
                    $content = substr_replace($content, $new_function."\n\n\n", $brace_pos, 0);
                }
            }
            else {
                $content = str_replace($old_function, $new_function, $content);
            }
        }
        
        if (!$this->check_syntax($this->get_class_full($content)))
            throw new Exception( fx::lang('Синтаксическая ошибка в классе компонента','system') );
        return $content;
    }
    
    /*
     * Проврка кода на синтаксические ошибки
     * @param type $code 
     */
    protected function check_syntax($code) {
        return @eval("return true; ".$code);
    }
    
    /**
     * Получает код класса (фактически получает код между <?php и ?>)
     * @param type $code 
     */
    protected function get_class_full($code) {
        
        $opentag_len = strlen('<?php');
        $opentag_pos = strpos($code, '<?php');
        if ($opentag_pos===false) {
            $opentag_len = strlen('<?');
            $opentag_pos = strpos($code, '<?');
        }
        if ($opentag_pos===false)
            throw new Exception( fx::lang('Не могу найти <?php в файле класса','system') );
        
        $brace_pos = strrpos($code, '}');
        
        $result = substr($code, $opentag_pos+$opentag_len, $brace_pos-$opentag_pos-$opentag_len+1);
        return $result;
        
    }

    /*
     * заполняет массив $this->functions номерами строк функций
     */
    protected function parse_functions() {
        $expected_method_name = false;
        $expected_method_begin = false;
        
        $current_method_name = '';
        $want_line_num = 0; // хотим узнать номер строки
        $expected_method_start = 0;
        
        $brace_level = 0;  // текущий уровень вложенности внутри ф-ции
        
        $functions = array();
        $tokens = token_get_all($this->php_code);

        for ($i = 0, $count = sizeof($tokens); $i < $count; $i++) {
            
            if (is_array($tokens[$i])) {
                list($token, $data, $line) = $tokens[$i];
            } else {
                $token = $tokens[$i];
                $data = '';
                $line = 0;
            }
            
            if ($want_line_num && $line) {
                if ($current_method_name)
                    $functions[$current_method_name]['code_start'] = $line;
                else {
                    end($functions);
                    $functions[key($functions)]['end'] = $line;
                }
                $want_line_num = 0;
            }

            switch ($token) {
                case T_STRING:
                    if ($expected_method_name) {
                        $expected_method_name = false;
                        $current_method_name = $data;
                        $functions[$current_method_name]['start'] = $expected_method_start;
                        $expected_method_begin = true;
                        $brace_level = 0;
                    }
                    break;
                    
                case '{':
                    if ($current_method_name)
                        $brace_level++;
                    
                    if ($expected_method_begin) {
                        $expected_method_begin = false;
                        $want_line_num = 1;
                    }
                    break;
                    
                case '}':
                    if ($current_method_name) {
                        $brace_level--;
                    
                        if ($brace_level == 0) {
                            $want_line_num = 1;
                            $current_method_name = '';
                        }
                    }
                    break;
                    
                case T_FUNCTION:
                    if (!$current_method_name)
                        $expected_method_name = true;
                        $expected_method_start = $line;
                    break;
            }
        }
        
        $this->code_lines = explode("\n", $this->php_code);
        $this->functions = $functions;
    }
    
    /*
     * Возвращает целиком всю функцию (начиная с function и кончая } )
     */
    protected function get_function_full($name) {

        if (!$this->functions[$name])
            return false;
        
        $start = $this->functions[$name]['start'];
        $end = $this->functions[$name]['end'];
        
        $func_lines = array_slice($this->code_lines, $start-1, $end-$start+1);
        $func_code = implode("\n", $func_lines);
        return $func_code;
    }
    
    /*
     * Возвращает только код функции (то, что находится между { и } )
     */
    protected function get_function_code($name) {

        if (!$this->functions[$name])
            return false;
        
        $start = $this->functions[$name]['code_start'];
        $end = $this->functions[$name]['end'];
        
        $func_lines = array_slice($this->code_lines, $start, $end-$start);
        $func_code = implode("\n", $func_lines);
        $func_code = rtrim($func_code, "\r\n");
        $func_code = rtrim($func_code, "}");
        return $func_code;
    }

    /*
     * Возвращает "полезное" содержимое функции (то, что будем выводить в админке)
     */
    protected function get_function_content($code, $type) {
        
        $s = '\s*';
        $content = preg_replace('/'.$s.'extract'.$s.'\('.$s.'\$this->get_vars\('.$s.'\)'.$s.'\)'.$s.';'.$s.'/ium', '', $code);
        
        if ($type == 'html') {
            $closetag_pos = strpos($content, '?>');
            $opentag_pos = strrpos($content, '<?php');
            if (!$opentag_pos)
                $opentag_pos = strrpos($content, '<?');
            
            if ( ($closetag_pos !==false) && ($opentag_pos!==false) )
                $content = substr($content, $closetag_pos+2, $opentag_pos-$closetag_pos-2);
        }

        return trim($content, " \t\r\n");
    }
    
    /*
     * Создает новую функцию (целиком, с function) с заданным контентом
     */
    protected function make_function_full($name, $type, $code) {
        if (!$code)
            return '';
        
        $result = "function ".$name." () {\n";
                
        $result .= "extract(\$this->get_vars());\n";
        
        if ($type == 'html')
            $result .= "?".">\n";
        
        $result .= $code;
        
        if ($type == 'html')
            $result .= "\n<"."?php";
        
        $result .= "\n}";
        
        return $result;
        
    }

}

?>

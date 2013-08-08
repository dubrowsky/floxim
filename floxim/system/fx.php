<?php

/**
"Static" class, just a helpers collection
*/ 

class fx {
    protected function __construct() {

    }

    /* Get config data */
    static public function config() {
        static $config = false;
        if ($config === false) {
            $config = new fx_config();
        }
        return $config;
    }
    
    /**
     * Получить объект базы данных
     * @return fx_db
     */
    public static function db() {
        static $db = false;
    	if ($db === false) {
            $db = new fx_db();
            $db->query("SET NAMES '".fx::config()->DB_CHARSET."'");
    	}
    	return $db;
    }
    
    protected static $data_cache = array();
    /* Получить дата-файндер для указанного типа content_id данных, либо объект(ы) по id
     * @param string $datatype название типа данных - 'component', 'content_news'
     * @param mixed [$id] id или массив ids
    */
    public static function data($datatype, $id = null) {
		if (is_array($datatype)) {
            $datatype = join("_", $datatype);
        }
        if (
            !is_null($id) && 
            !is_array($id) && 
            isset(self::$data_cache[$datatype]) &&  
            isset(self::$data_cache[$datatype][$id])
        ) {
                return self::$data_cache[$datatype][$id];
        }
        
        $data_finder = null;
        
        try {
            $classname = 'fx_data_'.$datatype;
            $data_finder = new $classname();
            if ($datatype == 'content') {
                $component = fx::data('component', 'content');
                $data_finder->set_component($component['id']);
            }
        } catch (Exception $e) {
            // Файндер для контента, класс не определен
            if (preg_match("~^content_~", $datatype)) {
                $component = fx::data(
                    'component', 
                    preg_replace("~^content_~", '', $datatype)
                );
                if ($component) {
                    $data_finder = new fx_data_content();
                    $data_finder->set_component($component['id']);
                }
            } elseif (preg_match("~^field_~", $datatype)) {
                $data_finder = new fx_data_field();
            }
        }
        if (is_null($data_finder)) {
            dev_log("NO DATATYPE", func_get_args(), debug_backtrace());
            die("Unable to create Finder for datatype '".$datatype."'");
    	}
        if (func_num_args() == 2) {
            if (is_numeric($id) || is_string($id)) {
                $res = $data_finder->get_by_id($id);
                self::$data_cache[$datatype][$id] = $res;
                return $res;
            }
            if (is_array($id)) {
                return $data_finder->get_by_ids($id);
            }
            return null;
        }
    	return $data_finder;
    }
    
    protected static $router = null;
    /**
     * Получить основной роутинг-менеджер, либо роутер $router_name
     * @param $router_name = null
     * @return fx_router_manager
     */
    public static function router($router_name = null) {
    	if (self::$router === null) {
            self::$router = new fx_router_manager();
    	}
        if (func_num_args() == 1) {
            return self::$router->get_router($router_name);
        }
    	return self::$router;
    }
    
    protected static $is_admin = null;
    public static function is_admin() {
        if (is_null(self::$is_admin)) {
            self::$is_admin = self::env('is_admin');
        }
        return self::$is_admin;
    }
    
    /**
     * @todo Пока держим env внутри fx_core, позже надо тоже перетащить 
     * Вызов без параметров - вернуть объект, с параметрами - получить/установить свойство
     * @param string $prop_name свойство
     * @param mixed $value установить значение
     */
    public static function env() {
        static $env = false;
        if ($env === false) {
            $env = new fx_system_env();
        }
    	
    	//$env = fx_core::get_object()->env;
        $args = func_get_args();
    	if (count($args) == 0) {
            return $env;
    	}
    	if (count($args) == 1) {
            if ($args[0] == 'is_admin') {
                $method = array($env, 'is_admin');
            } else {
                $method = array($env, 'get_'.$args[0]);
            }
            if (is_callable($method)) {
                return call_user_func($method);
            }
    	}
    	if (count($args) == 2) {
            $method = array($env, 'set_'.$args[0]);
            if (is_callable($method)) {
                return call_user_func($method, $args[1]);
            }
    	}
    	return null;
    }
    
    /**
     * создать контроллер, установить параметры
     * @param string $controller 'controller_name' или 'controller_name.action_name'
     * @param array $input
     * @param string $action
     * @return fx_controller инициализированный контроллер
     */
    public static function controller($controller, $input = null, $action = null) {
    	$c_parts = explode(".", $controller);
        if (count($c_parts) == 2) {
            $controller = $c_parts[0];
            $action = $c_parts[1];
    	}
    	$c_class = 'fx_controller_'.$controller;
    	try {
            $controller_instance = new $c_class($input, $action);
            return $controller_instance;
    	} catch (Exception $e) {
            if (preg_match("~^(component|widget|layout)_(.+)$~", $controller, $c_parts)) {
                $ctr_type = $c_parts[1];
                $ctr_name = $c_parts[2];
                $c_class = 'fx_controller_'.$ctr_type;
                try {
                    $controller_instance = new $c_class($input, $action);
                    switch ($ctr_type) {
                        case 'component':
                            $controller_instance->set_content_type($ctr_name);
                            break;
                    }
                    return $controller_instance;
                } catch (exception $e) {
                    dev_log('general controller loading failed also');
                }
            }
            dev_log("Failed loading controller class ".$c_class, debug_backtrace());
            die("Failed loading controller class ".$c_class);
    	}
    }

    public static function template($template, $data = array()) {
        if (is_numeric($template)) {
            // ...
        }
        $parts= explode(".", $template);
        if (count($parts) == 2) {
            $template = $parts[0];
            $action = $parts[1];
        } else {
            $action = null;
        }

        $class_name = 'fx_template_'.$template;
        try {
            return new $class_name($action, $data);
        } catch (Exception $e) {
            return new fx_template($action, $data);
        }
    }
    
    protected static $page = null;
    /*
     * @return fx_system_page page instance
     */
    public static function page() {
        if (!self::$page) {
            self::$page = new fx_system_page();
        }
        return self::$page;
    }
    
    public static function dig($collection, $var_path) {
        $var_path = explode(".", $var_path);
        $arr = $collection;
        foreach ($var_path as $pp) {
            if (is_array($arr) || $arr instanceof ArrayAccess) {
                if (!isset($arr[$pp])) {
                    return null;
                }
                $arr = $arr[$pp];
            } else {
                return null;
            }
        }
        return $arr;
    }
    
    public static function dig_set(&$collection, $var_path, $var_value, $merge = false) {
        //static $cnt = 0;
        //$cnt++;
        //echo fx_debug($var_path, $var_value, $cnt);
        $var_path = explode('.', $var_path);
        $arr =& $collection;
        foreach ($var_path as $pp) {
            if (!is_array($arr)) {
                return null;
            }
            if (empty($pp)) {
                $arr[]= $var_value;
                return;
            }
            if (!array_key_exists($pp, $arr)) {
                $arr[$pp]=array();
            }
            $arr =&  $arr[$pp];
        }
        if ($merge && is_array($arr) && is_array($var_value)) {
            $arr = array_merge_recursive($arr, $var_value);
        } else {
            $arr = $var_value;
        }
    }
    
    public static function collection($data = array()) {
        return $data instanceof fx_collection ? $data : new fx_collection($data);
    }
    
    /*
     * @return fx_system_input
     */
    public static function input() {
        static $input = false;
        if ($input === false) {
            $input = new fx_system_input();
        }
        return $input;
    }
    
    /*
     * @return fx_core
     */
    public static function core() {
        static $core = false;
        if ($core === false) {
            $core = new fx_core();
        }
        return $core;
    }

    public static function lang ( $string, $dict_key ) {
        // add file cache
        $dict_key = empty($dict_key) ? 'content' : $dict_key;
        $cur_lang = fx::config()->LANGUAGE;

        // TODO: заполнить русские строки в базе и убрать временный костыль для русских фраз чтобы не таскать портянку
        if ( $cur_lang == 'ru' ) return $string;

        $dict_file = fx::config()->DOCUMENT_ROOT . '/floxim_files/php_dictionaries/' . $cur_lang . '.' . $dict_key . '.php';

        // если файл-кэша не существует создаем его
        if (!file_exists($dict_file)) {
            self::createDictFile($cur_lang,$dict_key);
        }
        $res = self::dictCacheGet($cur_lang,$dict_key,$string);
        if ( $res ) return $res;

        $str = fx::db()->prepare($string);
        $dc = fx::db()->prepare($dict_key);
        $db_str = fx::db()->get_results('SELECT * FROM {{dictionary}} WHERE lang_string = "' . $str . '" AND dict_key = "' . $dc . '"');
        $db_str = $db_str[0];
        if ( empty($db_str) ) {
            $q = 'INSERT INTO {{dictionary}} (dict_key,lang_string) VALUES ("' . $dc . '","' . $str .'")';
            fx::db()->query($q);
            @unlink($dict_file);
        }
        return empty($db_str['lang_'.$cur_lang]) ? $string : $db_str['lang_'.$cur_lang];
    }

    private static function dictCacheGet( $lang, $key, $string ) {
        $dict_file = fx::config()->DOCUMENT_ROOT . '/floxim_files/php_dictionaries/' . $lang . '.' . $key . '.php';
        try {
            require_once($dict_file);
        } catch (Exception $e) {
            return false;
        }
        $string = addslashes($string);
        return $dictionary[$lang][$key][$string];
    }

    private static function createDictFile ( $lang, $key) {
        $dictionary = fx::db()->get_results('SELECT lang_string, lang_' . $lang . ' FROM {{dictionary}}');
        $output = '<?php ';
        $output .= '$dictionary["' . $lang . '"]["' . $key . '"] = array(';
        foreach ( $dictionary as $phrase ) {
            $output .= '"' . addslashes($phrase['lang_string']) .'" => "' . addslashes($phrase['lang_'.$lang]) . '",';
        }
        $output = substr($output,0,strlen($output)-1);
        $output .= ');';
        $dict_file = fx::config()->DOCUMENT_ROOT . '/floxim_files/php_dictionaries/' . $lang . '.' . $key . '.php';
        @ $dfh = fopen($dict_file, 'w');
        if ($dfh) {
            fputs($dfh, $output);
            fclose($dfh);
        }
    }

    protected static $http = null;
    public static function http() {
        if (!self::$http) {
            self::$http = new fx_http();
        }
        return self::$http;
    }
    
    public static function user() {
        return fx::env()->get_user();
    }
    
    protected static $_event_manager = null;
    
    protected static function _get_event_manager() {
        if (!self::$_event_manager) {
            self::$_event_manager = new fx_system_eventmanager();
        }
        return self::$_event_manager;
    }
    public static function listen($event_name, $callback) {
        self::_get_event_manager()->listen($event_name, $callback);
    }
    
    public static function unlisten($event_name) {
        self::_get_event_manager()->unlisten($event_name);
    }
    
    public static function trigger($event, $params = null) {
        self::_get_event_manager()->trigger($event, $params);
    }
    
    
    protected static $_cache = null;
    /*
     * пока - очень тупой локальный кэш, 
     * чтобы не доставать из бд одно и то же за одно выполнение
     */
    public static function cache($key = null, $value = null) {
        if (!self::$_cache) {
            self::$_cache = new fx_cache();
        }
        $count_args = func_num_args();
        switch ($count_args) {
            case 0:
                return self::$_cache;
                break;
            case 1:
                return self::$_cache->get($key);
                break;
            case 2:
                self::$_cache->set($key, $value);
                break;
        }
    }
    
    public static function files() {
        static $files = false;
        if ($files === false) {
            $files = new fx_system_files();
        }
        return $files;
    }
    
    public static function util() {
        static $util = false;
        if ($util === false) {
            $util = new fx_system_util();
        }
        return $util;
    }
    
    public static function date($value, $format) {
        if (!is_numeric($value)) {
			$value = strtotime($value);
		}
		return date($format, $value);
    }
    
    public static function image($value, $format) {
        try {
            $thumber = new fx_thumb($value, $format);
            $value = $thumber->get_result_path();
        } catch (Exception $e) {
            $value = '';
        }
        return $value;
    }
}
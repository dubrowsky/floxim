<?php

/**
"Static" class, just a helpers collection
*/ 

class fx {
    static protected $config = null;

    protected function __construct() {

    }

    /* Get config data */
    static public function config() {
        if (self::$config === null) {
            self::$config = new fx_config();
        }
        return self::$config;
    }
    
    protected static $db = null;
    /**
     * Получить объект базы данных
     * @return fx_db
     */
    public static function db() {
    	if (is_null(self::$db)) {
            self::$db = new fx_db();
            self::$db->query("SET NAMES '".fx::config()->DB_CHARSET."'");
    	}
    	return self::$db;
    }
    
    /* Data finder instances collection */
    protected static $data_finders = array();
    
    /* Получить дата-файндер для указанного типа content_id данных, либо объект(ы) по id
     * @param string $datatype название типа данных - 'component', 'content_news'
     * @param mixed [$id] id или массив ids
    */
    public static function data($datatype, $id = null) {
        if (is_array($datatype)) {
            $datatype = join("_", $datatype);
        }
    	if (!isset(self::$data_finders[$datatype])) {
            try {
                $classname = 'fx_data_'.$datatype;
                //dev_log('data lurk', $classname);
                $data_finder = new $classname();
                self::$data_finders[$datatype] = $data_finder;
            } catch (Exception $e) {
                // Файндер для контента, класс не определен
                if (preg_match("~^content_~", $datatype)) {
                    $component = fx::data('component', preg_replace("~^content_~", '', $datatype));
                    if ($component) {
                        $data_finder = new fx_data_content();
                        $data_finder->set_component($component['id']);
                        self::$data_finders[$datatype] = $data_finder;
                    }
                } else {
                    //dev_log('class not found', $datatype);
                }
            }
    	}
    	if (!isset(self::$data_finders[$datatype])) {
            dev_log("NO DATATYPE", func_get_args(), debug_backtrace());
            die("NO DATATYPE: ".$datatype);
    	}
        $df = self::$data_finders[$datatype];
        if (func_num_args() == 2) {
            if (is_numeric($id) || is_string($id)) {
                return $df->get_by_id($id);
            }
            if (is_array($id)) {
                return $df->get_by_ids($id);
            }
            return null;
        }
    	return self::$data_finders[$datatype];
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
    
    /**
     * @todo Пока держим env внутри fx_core, позже надо тоже перетащить 
     * Вызов без параметров - вернуть объект, с параметрами - получить/установить свойство
     * @param string $prop_name свойство
     * @param mixed $value установить значение
     */
    public static function env() {
    	$args = func_get_args();
    	$env = fx_core::get_object()->env;
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
            //dev_log('controller class not loaded', $c_class);
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
            dev_log('template class not found', $class_name, func_get_args(), debug_backtrace());
            die();
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
                //dev_log('dig not an arr', $collection, $arr, $pp, debug_backtrace());
                return null;
            }
        }
        return $arr;
    }
    
    public static function dig_set(&$collection, $var_path, $var_value, $merge = false) {
        $var_path = explode('.', $var_path);
        $arr =& $collection;
        foreach ($var_path as $pp) {
            if (!is_array($arr)) {
                return null;
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
}
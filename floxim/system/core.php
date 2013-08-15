<?php
class fx_core extends fx_system {

    /** @var fx_system_page */
    public $page;

    /** @var fx_system_eventmanager */
    public $event;

    /** @var fx_system_modules */
    public $modules;

    /** @var fx_system_env */
    public $env;

    /** @var fx_system_util */
    public $util;
    protected $settings; // значение настроек
    protected $admin_mode = false;
    protected $data_classes = array();

    public function __construct() {
        parent::__construct();
        spl_autoload_register(array($this, 'load_class'));
    }

    public function db_init() {
        $this->db = new fx_db();
        $this->db->query("SET NAMES '".fx::config()->DB_CHARSET."'");
    }

    /**
     * Load system extension
     *
     */
    public function load($object) {
        $class_name = "fx_system_".$object;
        $this->$object = new $class_name();
        return $this->$object;
    }

    /**
     * Получить значение параметра из настроек
     * @param string ключ
     * @param string имя модуля ( system - ядро )
     * @return mixed значение параметра
     */
    public function get_settings($item = '', $module = '') {

        if (empty($this->settings)) {
            $res = fx::db()->get_results("SELECT `key`, `module`, `value` FROM `{{settings}}`");
            $count = fx::db()->row_count();
            for ($i = 0; $i < $count; $i++) {
                $this->settings[$res[$i]['module']][$res[$i]['key']] = $res[$i]['value'];
            }
        }

        // по умолчанию - ядро ( 1 и true нужно для обратной совместимости )
        if (!$module || $module === 1 || $module === true) {
            $module = 'system';
        }


        // if item requested return item value
        if ($item && is_array($this->settings[$module])) {
            return array_key_exists($item, $this->settings[$module]) ? $this->settings[$module][$item] : false;
        }

        // return all settings
        return $this->settings[$module];
    }

    /**
     * Установить значние параметра
     * @param string ключ
     * @param string значние параметра
     * @param string модуль
     * @return bool
     */
    public function set_settings($key, $value, $module = 'system') {
        // по умолчанию - ядро системы
        if (!$module) $module = 'system';
        // обновляем состояние
        $this->settings[$module][$key] = $value;
        // подготовка записи в БД
        $db = fx::db();
        $key = $db->escape($key);
        $value = $db->prepare($value);
        $module = $db->escape($module);

        $id = $db->get_var("SELECT `id` FROM `{{settings}}` WHERE `key` = '".$key."' AND `module` = '".$module."' ");
        if ($id) {
            $db->query("UPDATE `{{settings}}` SET `value` = '".$value."' WHERE `id` = '".$id."' ");
        } else {
            $db->query("INSERT INTO `{{settings}}`(`key`, `module`, `value`)
                        VALUES('".$key."','".$module."','".$value."') ");
        }

        return true;
    }

    /**
     * Удаление параметра
     * @param string ключ
     * @param string модуль
     * @return int
     */
    public function drop_settings($key, $module = 'system') {
        // по умолчанию - ядро системы
        if (!$module) $module = 'system';
        // обновляем состояние
        unset($this->settings[$module][$key]);
        // подготовка запроса к БД
        $db = fx::db();
        $key = $db->escape($key);
        $module = $db->escape($module);

        $db->get_query("DELETE FROM `{{settings}}` WHERE `key` = '".$key."' AND `module` = '".$module."' ");

        return $db->affected_rows;
    }

    public function load_default_extensions() {
        static $loaded = false;

        if (!$loaded) {
            $this->load("files");
            $this->load("token");
            $this->load("eventmanager");
            $this->event = $this->eventmanager;
            $this->load("util");
            $this->load("input");
            $this->load("url");
            $this->load("page");
            $this->load("lang");
            $this->load("modules");
            $this->load("env");
            $this->load("mail");

            $loaded = true;
        }
    }

    /**
     * @return fx_core object
     */
    public static function get_object() {
        static $storage;

        if (!isset($storage)) {
            $storage = new self();
        }

        return $storage;
    }

    /**
     * Метод провреят, установлено ли расширение php
     * @param string имя расширения
     * @return bool
     */
    public function php_ext($name) {
        static $ext = array();
        if (!array_key_exists($name, $ext)) {
            $ext[$name] = extension_loaded($name);
        }

        return $ext[$name];
    }

    public function is_admin_mode() {
        return $this->admin_mode;
    }

    public function set_admin_mode() {
        $this->admin_mode = 1;
    }
    
    public function __get($name) {
    	// объект загружен
        if (isset($this->data_classes[$name])) {
            return $this->data_classes[$name];
        }

        // попытка загрузить объект для работы с данными
        try {
            $classname = 'fx_data_'.$name;
            $data_obj = new $classname();
            $this->data_classes[$name] = $data_obj;
            return $data_obj;
        } catch (Exception $e) {
            $trace = debug_backtrace();
            trigger_error('Undefined class property fx_core->'.$name.
                    ' in '.$trace[0]['file'].
                    ' on line '.$trace[0]['line'], E_USER_NOTICE);
            return null;
        }
    }

    public function __isset($name) {
        return isset($this->{$name});
    }

    
    protected static $classes_with_no_file = array();
    
    /**
     * @todo привести в номральный вид
     */
    static public function load_class($classname) {
        if (substr($classname, 0, 3) != 'fx_') {
            return false;
        }

        if (in_array($classname, self::$classes_with_no_file)) {
            throw new fx_exteption_classload('AGAIN: Unable to load class '.$classname);
        }
        $file = self::get_class_file($classname);
        if (!$file) {
            return false;
        }

        if (!file_exists($file)) {
            $e = new fx_exteption_classload('Unable to load class '.$classname);
            $e->class_file = $file;
            self::$classes_with_no_file[]= $classname;
            throw $e;
        }
        require_once $file;
    }

    public static function get_class_file($classname) {
      	$root = fx::config()->ROOT_FOLDER;
        $doc_root = fx::config()->DOCUMENT_ROOT.'/';

        $libs = array();
        $libs['FB'] = 'firephp/fb';
        $libs['tmhOAuth'] = 'tmhoAuth/tmhoauth';
        $libs['tmhUtilities'] = 'tmhoAuth/tmhutilities';
        $libs['Facebook'] = 'facebook/facebook';

        $essences = array(
            'classificator', 
            'component', 
            'crontask', 
            'ctpl', 
            'field', 
            'group', 
            'history', 
            'history_item', 
            'infoblock', 
            'infoblock_visual',
            'layout',
            'menubaze', 
            'content', 
            'redirect', 
            'rights', 
            'simplerow', 
            'site', 
            'subdivision', 
            'template', 
            'widget',
            'filetable'
        ); //'user'

        $classname = str_replace(array('nc_', 'fx_'), '', $classname);

        do {
            if ( $classname == 'collection') {
                $file = $root.'system/collection';
                break;
            }
            if (preg_match("~^template(|_processor|_field|_html|_suitable|_html_token|_token|_html_tokenizer)$~", $classname)) {
                $file = $root.'template/'.$classname;
                break;
            }
            if (preg_match('~controller_(component|widget|layout)$~', $classname, $ctr_type)) {
                $file = $root.'controller/'.$ctr_type[1];
                break;
            }
            if (preg_match("~^template_(.+)$~", $classname, $tpl_name)) {
                $file = fx_template_processor::get_template_file($tpl_name);
                break;
                //echo "<pre>" . htmlspecialchars(print_r($file, 1)) . "</pre>";
                //die();
            }
            
            if (in_array($classname, $essences)) {
                $file = $root."essence/".$classname;
                break;
            }
            
            if (preg_match("~^router~", $classname)) {
            	$file = $root.'routing/'.$classname;
            	break;
            }
            
            if (preg_match('~^content_~', $classname)) {
                $com_name = preg_replace("~^content_~", '', $classname);
                $file = $doc_root.'component/'.$com_name.'/'.$com_name.'.essence';
                break;
            }
            
            if (in_array($classname, array('http', 'event', 'cache', 'thumb'))) {
                $file = $root.'system/'.$classname;
                break;
            }
            
            if (preg_match("~^controller_(.+)~", $classname, $controller_name)) {
                $controller_name = $controller_name[1];
                if (preg_match("~^(layout|component|widget)_(.+)$~", $controller_name, $name_parts)) {
                    $ctr_type = $name_parts[1];
                    $ctr_name = $name_parts[2];
                } else {
                    $ctr_type = 'other';
                    $ctr_name = $controller_name;
                }
                $test_file = $doc_root.$ctr_type.'/'.$ctr_name.'/'.$ctr_name;
                if (file_exists($test_file.'.php')) {
                    dev_log('exist ctr', $test_file);
                    $file = $test_file;
                    break;
                }
            }

            if ($classname == 'controller_layout' || $classname == 'controller_admin_layout') {
                $file = $root.'admin/controller/layout';
                break;
            }

            if ($classname == 'controller_admin' || $classname == 'controller_admin_module') {
                $file = $root."admin/admin";
                break;
            }

            if (preg_match("/^controller_admin_module_([a-z]+)/", $classname, $match)) {
                $file = $root."modules/".$match[1]."/admin";
                break;
            }

            if (preg_match("/^controller_admin_([a-z_]+)/", $classname, $match)) {
                $file = $root.'admin/controller/'.str_replace('_', '/', $match[1]);
                break;
            }

            if (preg_match("/^controller_(administrate|site|template_files|template_colors|template|component|field|settings|widget)$/", $classname, $match)) {
                $file = $root.'/admin/controller/'.str_replace('_', '/', $match[1]);
                break;
            }

            if (preg_match("/^controller_module_([a-z]+)/", $classname, $match)) {
                $file = $root."modules/".$match[1]."/controller";
                break;
            }


            if (preg_match("/^controller_admin_module_([a-z]+)/", $classname, $match)) {
                $file = $root."modules/".$match[1]."/admin";
                break;
            }
            
            if (preg_match("~^data_(.+)$~", $classname, $match)) {
                $data_name = $match[1];
                if (preg_match("~^content_~", $data_name)) {
                    $com_name = preg_replace("~^content_~", '', $data_name);
                    $file = $doc_root.'component/'.$com_name.'/'.$com_name.'.data';
                } else {
                    $file = $root.'data/'.$match[1];
                }
                break;
            }
            
            if (preg_match("/^(admin|controller|event|field|infoblock|layout|system)_([a-z0-9_]+)/", $classname, $match)) {
                $file = $root.$match[1]."/".str_replace('_', '/', $match[2]);
                break;
            }

            if (preg_match("/(ctpl_)([a-z][a-z0-9]*)_([a-z][a-z0-9_]+)/i", $classname, $match)) {
                $file = fx::config()->COMPONENT_FOLDER.$match[2].'/'.$match[3].'.tpl';
                break;
            }

            if (preg_match("/template__([a-z][a-z0-9]*)__([a-z][a-z0-9]+)/i", $classname, $match)) {
                $file = fx::config()->TEMPLATE_FOLDER.$match[1].'/'.$match[2].'.tpl';
                break;
            }

            if ($classname == 'fxml' || $classname == 'export' || $classname == 'import') {
                $file = $root.'imex/'.$classname;
                break;
            }

            if (isset($libs[$classname])) {
                $file = fx::config()->INCLUDE_FOLDER.$libs[$classname];
                break;
            }

            $file = $root.$classname;
        } while (false);
		return $file.".php";
    }

}

class fx_exception extends Exception {

}

class fx_exteption_classload extends fx_exception {
	public $class_file = false;
	public function get_class_file() {
		return $this->class_file;
	}
}

?>

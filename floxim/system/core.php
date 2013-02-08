<?php

/* $Id: fx_core.class.php 4714 2011-05-20 10:48:13Z denis $ */

/**
 * For IDE autocomplete:
 * @property fx_data_site $site
 * @property fx_data_classificator $classificator
 * @property fx_data_component $component
 * @property fx_data_crontask $crontask
 * @property fx_data_ctpl $ctpl
 * @property fx_data_field $field
 * @property fx_data_filetable $filetable
 * @property fx_data_group $group
 * @property fx_data_history $history
 * @property fx_data_infoblock $infoblock
 * @property fx_data_mailtemplate $mailtemplate
 * @property fx_data_menu $menu
 * @property fx_data_content $content
 * @property fx_data_redirect $redirect
 * @property fx_data_rights $rights
 * @property fx_data_subdivision $subdivision
 * @property fx_data_template $template
 * @property fx_data_user $user
 * @property fx_data_widget $widget
 *
 * @property string $SUB_FOLDER
 * @property string $AUTHORIZE_BY
 * @property string $HTTP_ROOT_PATH
 * @property string $HTTP_MODULE_PATH
 * @property string $HTTP_FILES_PATH
 * @property string $HTTP_ACTION_LINK
 * @property string $HTTP_COMPONENT_PATH
 * @property string $HTTP_WIDGET_PATH
 * @property string $DOCUMENT_ROOT
 * @property string $HTTP_HOST
 * @property string $FLOXIM_FOLDER
 * @property string $ROOT_FOLDER
 * @property string $SYSTEM_FOLDER
 * @property string $FILES_FOLDER
 * @property string $DUMP_FOLDER
 * @property string $TEMPLATE_FOLDER
 * @property string $COMPONENT_FOLDER
 * @property string $WIDGET_FOLDER
 * @property string $MODULE_TPL_FOLDER
 * @property string $INCLUDE_FOLDER
 * @property string $TMP_FOLDER
 * @property string $MODULE_FOLDER
 * @property string $ADMIN_FOLDER
 * @property string $NC_JQUERY_PATH
 *
 */
class fx_core extends fx_system {

    /** @var fx_Db */
    public $db;
    public $beta = true;

    /** @var fx_system_page */
    public $page;

    /** @var fx_system_eventmanager */
    public $event;

    /** @var fx_system_files */
    public $files;

    /** @var fx_system_input */
    public $input;

    /** @var fx_system_input */
    public $lang;

    /** @var fx_system_mail */
    public $mail;

    /** @var fx_system_modules */
    public $modules;

    /** @var fx_system_url */
    public $url;

    /** @var fx_system_env */
    public $env;

    /** @var fx_system_token */
    public $token;

    /** @var fx_system_util */
    public $util;
    protected $settings; // значение настроек
    protected $admin_mode = false;
    protected $data_classes = array();

    public function __construct() {
        parent::__construct();

        spl_autoload_register(array($this, 'load_class'));

        //$this->beta = true;
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
            $res = $this->db->get_results("SELECT `key`, `module`, `value` FROM `{{settings}}`");
            $count = $this->db->row_count();
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
        $key = $this->db->escape($key);
        $value = $this->db->prepare($value);
        $module = $this->db->escape($module);

        $id = $this->db->get_var("SELECT `id` FROM `{{settings}}` WHERE `key` = '".$key."' AND `module` = '".$module."' ");
        if ($id) {
            $this->db->query("UPDATE `{{settings}}` SET `value` = '".$value."' WHERE `id` = '".$id."' ");
        } else {
            $this->db->query("INSERT INTO `{{settings}}`(`key`, `module`, `value`)
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
        $key = $this->db->escape($key);
        $module = $this->db->escape($module);

        $this->db->get_query("DELETE FROM `{{settings}}` WHERE `key` = '".$key."' AND `module` = '".$module."' ");

        return $this->db->affected_rows;
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
    	dev_log($name, debug_backtrace());
    	die();
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

    /**
     * @todo привести в номральный вид
     */
    static public function load_class($classname) {
        $file = self::get_class_file($classname);

        if (!file_exists($file)) {
            $e = new fx_exteption_classload('Unable to load class '.$classname);
            $e->class_file = $file;
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
            'infoblock2layout',
            'layout',
            'menubaze', 
            'content', 
            'redirect', 
            'rights', 
            'simplerow', 
            'site', 
            'subdivision', 
            'template', 
            'widget'
        ); //'user'

        $classname = str_replace(array('nc_', 'fx_'), '', $classname);
        
        do {
            if (preg_match("~^template(|_processor|_field)$~", $classname)) {
                $file = $root.'template/'.$classname;
                break;
            }
            if ($classname == 'controller_component') {
                $file = $root.'controller/component';
                break;
            }
            if (preg_match("~^template_(.+)$~", $classname, $tpl_name)) {
                $tpl_name = $tpl_name[1];
                $tpl_file = fx::config()->COMPILED_TEMPLATES_FOLDER.'/'.$tpl_name;
                if (false && file_exists($tpl_file.'.php')) {
                    $file = $tpl_file;
                    break;
                }
                
                if (preg_match("~^(layout|component|widget)_([a-z0-9_]+)$~", $tpl_name, $tpl_name_parts)) {
                    $ctr_type = $tpl_name_parts[1];
                    $ctr_name = $tpl_name_parts[2];
                } else {
                    $ctr_type = 'other';
                    $ctr_name = $tpl_name;
                }
                
                $source_dir = $doc_root.'controllers/'.$ctr_type.'/'.$ctr_name;
                if (is_dir($source_dir)) {
                    $processor = new fx_template_processor();
                    $processor->process_dir($source_dir);
                    $file = $tpl_file;
                    break;
                }
            }
            
            if (in_array($classname, $essences)) {
                $file = $root."essence/".str_replace('_', '/', $classname);
                break;
            }
            
            if (preg_match("~^router~", $classname)) {
            	$file = $root.'routing/'.$classname;
            	break;
            }
            
            if (preg_match('~^content_~', $classname)) {
                $file = $root.'essence/'.$classname;
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
                $test_file = fx::config()->DOCUMENT_ROOT.'/controllers/'.$ctr_type.'/'.$ctr_name.'/'.$ctr_name;
                if (file_exists($test_file.'.php')) {
                    $file = $test_file;
                    break;
                }
            }

            if ($classname == 'controller_layout' || $classname == 'controller_admin_layout') {
                $file = $root.'admin/controller/template/layout';
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
                $file = $root.'/admin/controller/'.str_replace('_', '/', $match[1]);
                break;
            }

            if (preg_match("/^controller_(administrate|site|template_files|template_colors|template|component|ctpl|field|devtools|menu|menu_item|settings|widget|patch|redirect|crontask)$/", $classname, $match)) {
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
            
            if (preg_match("~^data_(content_.+)$~", $classname, $match)) {
                $file = $root.'data/'.$match[1];
                break;
            }
            
            if (preg_match("/^(admin|controller|data|event|field|infoblock|layout|menu|tpl|system|unit)_([a-z0-9_]+)/", $classname, $match)) {
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

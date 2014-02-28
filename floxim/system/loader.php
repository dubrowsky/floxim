<?php
class fx_loader {

    public function __construct() {
        spl_autoload_register(array($this, 'load_class'));
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
            throw new fx_exception_classload('AGAIN: Unable to load class '.$classname);
        }
        $file = self::get_class_file($classname);
        if (!$file) {
            return false;
        }

        if (!file_exists($file)) {
            $e = new fx_exception_classload('Unable to load class '.$classname);
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
            'component', 
            'field', 
            'group', 
            'history', 
            'history_item', 
            'infoblock', 
            'infoblock_visual',
            'layout',
            'content', 
            'redirect', 
            'simplerow', 
            'site', 
            'widget',
            'filetable',
            'patch',
            'lang_string',
            'lang'
        );

        $classname = str_replace('fx_', '', $classname);

        do {
            if ( $classname == 'collection') {
                $file = $root.'system/collection';
                break;
            }
            if (preg_match("~^template(|_processor|_field|_html|_suitable|_html_token|_token|_html_tokenizer|_fsm|_compiler|_loader|_parser|_expression_parser|_loop|_attr_parser|_attrtype_parser|_modifier_parser)(?:_dev)?$~", $classname)) {
                $file = $root.'template/'.$classname;
                break;
            }
            if (preg_match('~controller_(component|widget|layout)$~', $classname, $ctr_type)) {
                $file = $root.'controller/'.$ctr_type[1];
                break;
            }
            if (preg_match("~^template_(.+)$~", $classname, $tpl_name)) {
                fx_template_loader::autoload($tpl_name[1]);
                return;
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
                if (file_exists($file.'.php')) {
                    break;
                } elseif(file_exists($doc_root.'floxim/std/component/'.$com_name.'/'.$com_name.'.essence'.'.php')) {
                    $file = $doc_root.'floxim/std/component/'.$com_name.'/'.$com_name.'.essence';
                    break;
                }
            }
            
            if (in_array($classname, array('http', 'event', 'cache', 'thumb', 'lang'))) {
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
                    $file = $test_file;
                    break;
                } elseif (file_exists($doc_root.'floxim/std/'.$ctr_type.'/'.$ctr_name.'/'.$ctr_name.'.php')) {
                    $file = $doc_root.'floxim/std/'.$ctr_type.'/'.$ctr_name.'/'.$ctr_name;
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

            if (preg_match("/^controller_(site|template_files|template_colors|template|component|field|settings|widget)$/", $classname, $match)) {
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
                    if (file_exists($file.'.php')){
                        break;
                    } elseif(file_exists($doc_root.'floxim/std/component/'.$com_name.'/'.$com_name.'.data'.'.php')) {
                        $file = $doc_root.'floxim/std/component/'.$com_name.'/'.$com_name.'.data';
                        break;
                    }
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

class fx_exception_classload extends fx_exception {
	public $class_file = false;
	public function get_class_file() {
		return $this->class_file;
	}
}
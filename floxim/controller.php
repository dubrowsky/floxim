<?php

/**
 * Базовый класс для всех контроллеров
 * Конструктор принимает параметры и экшн
 * Отработка - через метод process()
 */
class fx_controller {
    
    protected $input = array();
    protected $action = null;
    
    /**
     * Конструктор контроллеров. Лучше использовать fx::controller('controller.action', $params).
     * @param array $input = 'array()' параметры контроллера
     * @param string $action = 'null' название экшна
     */
    public function __construct($input = array(), $action = null) {
    	$this->set_input($input);
    	$this->set_action($action);
    }
    
    /**
     * Получить один из параметров по имени
     * @param type $name
     */
    public function get_param($name) {
        return isset($this->input[$name]) ? $this->input[$name] : null;
    }
    
    public function set_param($name, $value) {
        $this->input[$name] = $value;
    }

    public function set_input($input) {
        $this->input = $input;
        return $this;
    }
    
    public function default_action() {
        return array();
    }
    
    public function set_action($action) {
        if (is_null($action)) {
            return $this;
        }
    	
    	$this->action = $action;
    	return $this;
    }

    /**
     * Возвращает результат выполнения действия контроллером
     * @return array|string массив с результатами работы контроллера
     * $input = null, $action = null, $do_return = false
     */
    public function process() {
    	$action = $this->get_action_method();
        $cfg = $this->get_config();
        if (isset($cfg['actions'][$this->action]['force'])) {
            foreach ($cfg['actions'][$this->action]['force'] as $param => $value) {
                $this->set_param($param, $value);
            }
        }
        $this->trigger('before_action_run');
        return $this->$action($this->input);
    }
    
    protected $_action_prefix = '';


    public function get_action_method() {
        $action = $this->_action_prefix.$this->action;
        return is_callable(array($this, $action)) ? $action : 'default_action';
    }


    public function find_template() {
        $tpl = str_replace('fx_controller_', '', get_class($this));
        return fx::template($tpl.'.'.$this->action);
    }
    
    /*
     * Возвращает массив с вариантами контроллера, которые можно использовать для поиска шаблона
     * По умолчанию - только сам контроллер,
     * Для компонентов переопределяется с добавлением цепочки наследования
     */
    protected function _get_controller_variants() {
        return array(str_replace('fx_controller_', '', get_class($this)));
    }
    
    /*
     * Возвращает массив шаблонов, которые можно использовать для контроллер-экшна
     * Вызывать после инициализации контроллера (с экшном)
     */
    public function get_available_templates( $layout_name = null ) {
        if (is_numeric($layout_name)) {
            $layout_names = array(fx::data('layout', $layout_name)->get('keyword'));
        } elseif (is_null($layout_name)) {
            $layout_names = fx::data('layout')->all()->get_values('keyword');
        } elseif (is_string($layout_name)) {
            $layout_names = array($layout_name);
        } elseif (is_array($layout_name)) {
            $layout_names = $layout_name;
        }
        
        // получаем допустимые варианты контроллера
        $controller_variants = $this->_get_controller_variants();
        $template_variants = array();
        // сначала вытаскиваем все варианты шаблонов из лейаутов
        foreach ($layout_names as $layout_name) {
            if (($layout_tpl = fx::template('layout_'.$layout_name)) ) {
                $template_variants = array_merge(
                    $template_variants, 
                    $layout_tpl->get_template_variants()
                );
            }
        }
        // теперь - все варианты шаблонов из шаблона от контроллера
        foreach ($controller_variants as $controller_variant) {
            if (($controller_template = fx::template($controller_variant))) {
                $template_variants = array_merge(
                        $template_variants, 
                        $controller_template->get_template_variants()
                );
            }
        }
        // а теперь - фильтруем
        $result = array();
        foreach ($template_variants as $k => $tplv) {
            foreach (explode(",", $tplv['of']) as $tpl_of) {
                $of_parts = explode(".", $tpl_of);
                if (count($of_parts) != 2) {
                    continue;
                }
                list($tpl_of_controller, $tpl_of_action) = $of_parts;
                if ( !in_array($tpl_of_controller, $controller_variants)) {
                    continue;
                }
                if (strpos($this->action, $tpl_of_action) !== 0) {
                    continue;
                }
                $result []= $tplv;
            }
        }
        return $result;
    }

    /*
     * Пост-обработка, вызывается из fx_controller_infoblock->render()
     */
    public function postprocess($html) {
        return $html;
    }
    
    public function get_action_settings($action) {
        $cfg = $this->get_config();
        if (!isset($cfg['actions'][$action])) {
            return;
        }
        $params = $cfg['actions'][$action];
        if (!isset($params['settings'])) {
            return;
        }
        $settings = $params['settings'];
        if (!isset($params['force'])) {
            return $settings;
        }
        foreach ($params['defaults'] as $param => $val) {
            $settings[$param]['value'] = $val;
        }
        foreach (array_keys($params['force']) as $forced_key) {
            unset($settings[$forced_key]);
        }
        return $settings;
    }
    
    public function get_config() {
        $sources = $this->_get_config_sources();
        $config = array('actions' => $this->_get_real_actions());
        foreach ($sources as $source) {
            $level_config = include $source;
            if (!is_array($level_config)) {
                continue;
            }
            if (isset($level_config['actions']) && is_array($level_config['actions'])) {
                $level_config['actions'] = $this->_prepare_action_config($level_config['actions']);
                foreach (array_keys($config['actions']) as $parent_action) {
                    if (!isset($level_config['actions'][$parent_action])) {
                        $level_config['actions'][$parent_action] = array();
                    }
                }
                $level_config['actions'] = self::_merge_actions($level_config['actions']);
            }
            $config = array_replace_recursive($config, $level_config);
        }
        foreach ($config['actions'] as $action => &$params) {
            $method_name = 'config_'.$action;
            if(method_exists($this, $method_name)) {
                $config['actions'][$action] = $this->$method_name($params);
            }
            if (!isset($params['settings']) || !is_array($params['settings'])) {
                continue;
            }
            foreach ($params['settings'] as $param_key => &$param_field) {
                if (!isset($param_field['name'])) {
                    $param_field['name'] = $param_key;
                }
            }
        }
        $config['actions'] = self::_merge_actions($config['actions']);
        dev_log($config);
        return $config;
    }

    protected function _prepare_action_config($actions) {
        foreach ($actions as &$params) {
            if(!isset($params['defaults'])) {
                continue;
            }
            foreach ($params['defaults'] as $key => $value) {
                if (preg_match('~^!~', $key) !== 0) {
                    $params['force'][substr($key, 1)] =$value;
                    $params['defaults'][substr($key, 1)] =$value;
                    unset($params['defaults'][$key]);
                }
            }
        }
        return $actions;
    }
    
    protected function _get_config_sources() {
        return array();
    }
    
    protected static function _merge_actions($actions) {
        ksort($actions);
        $key_stack = array();
        foreach ($actions as $key => $params) {
            // не наследуем горизонтально флаг disabled 
            $no_disabled = !isset($params['disabled']);
            
            foreach ($key_stack as $prev_key_index => $prev_key) {
                if (substr($key, 0, strlen($prev_key)) === $prev_key) {
                    $actions[$key] = array_replace_recursive(
                        $actions[$prev_key], $params
                    );
                    break;
                }
                unset($key_stack[$prev_key_index]);
            }
            array_unshift($key_stack, $key);
            if ($no_disabled) {
                unset($actions[$key]['disabled']);
            }
        }
        return $actions;
    }
    

    protected function _get_real_actions() {
        $class = new ReflectionClass(get_class($this));
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        $props = $class->getDefaultProperties();
        $prefix = isset($props['_action_prefix']) ? $props['_action_prefix'] : '';
        $actions = array();
        foreach ($methods as $method) {
            $action_name = null;
            if (preg_match("~^".$prefix."(.+)$~", $method->name, $action_name)) {
                $action_name = $action_name[1];
                $actions[$action_name]= array();
            }
        }
        return $actions;
    }
    
    
    protected $_bound = array();
    public function listen($event, $callback) {
        if (!isset($this->_bound[$event])) {
            $this->_bound[$event] = array();
        }
        $this->_bound[$event][]= $callback;
    }
    
    public function trigger($event, $data = null) {
        if (isset($this->_bound[$event]) && is_array($this->_bound[$event])) {
            foreach ( $this->_bound[$event] as $cb) {
                call_user_func($cb, $data, $this);
            }
        }
    }
    
    public function get_actions() {
        $cfg = $this->get_config();
        $res = array();
        foreach ($cfg['actions'] as $action => $info) {
            if (isset($info['disabled']) && $info['disabled']) {
                continue;
            }
            if (!isset($info['name'])) {
                $info['name'] = $action;
            }
            $res[$action] = $info;
        }
        return $res;
    }
}
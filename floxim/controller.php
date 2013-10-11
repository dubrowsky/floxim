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
        if (!method_exists($this, 'settings_'.$action)) {
            return array();
        }
        $settings = call_user_func(array($this, 'settings_'.$action));
        $defaults = $this->get_action_defaults($action);
        if (!isset($defaults['params'])) {
            return $settings;
        }
        $forced = array();
        $force_all = false;
        if (isset($defaults['force'])) {
            if ($defaults['force'] === true || $defaults['force']=== '*') {
                $force_all = true;
            } else {
                $force_parts = preg_split("~,\s*~", trim($defaults['force']));
                foreach ($force_parts as $fp) {
                    $fp = explode(".", $fp);
                    if (count($fp) !== 2 || $fp[0] !== 'params') {
                        continue;
                    }
                    $forced [trim($fp[1])] = true;
                }
            }
        }
        foreach ($defaults['params'] as $pk => $pv) {
            if (isset($settings[$pk])) {
                $settings[$pk]['value'] = $pv;
                if ($force_all | isset($forced[$pk]) || isset($forced['*'])) {
                    $settings[$pk]['type'] = 'hidden';
                }
            }
        }
        return $settings;
    }
    
    
    
    public function get_action_defaults($action) {
        if (method_exists($this, 'defaults_'.$action)) {
            return call_user_func(array($this, 'defaults_'.$action));
        }
        return array();
    }
    
    public final function get_action_info($action = null) {
        if ($action === null) {
            if (!$this->action) {
                return null;
            }
            $action = $this->action;
        }
        $info_method = 'info_'.$action;
        if (method_exists($this, $info_method)) {
            return call_user_func(array($this, $info_method));
        }
        return array('name' => $action);
    }
    
    public function get_config() {
        $sources = $this->_get_config_sources();
        $config = array();
        foreach ($sources as $source) {
            $level_config = include_once $source;
            if (isset($level_config['actions'])) {
                $level_config['actions'] = self::_merge_actions($level_config['actions']);
            }
            $config = array_merge_recursive($config, $level_config);
        }
        return $config;
    }
    
    protected function _get_config_sources() {
        return array();
    }
    
    protected static function _merge_actions($actions) {
        ksort($actions);
        $key_stack = array();
        foreach (array_keys($actions) as $key) {
            foreach ($key_stack as $prev_key_index => $prev_key) {
                if (substr($key, 0, strlen($prev_key)) === $prev_key) {
                    $actions[$key] = array_merge_recursive(
                        $actions[$prev_key], $actions[$key]
                    );
                    break;
                }
                unset($key_stack[$prev_key_index]);
            }
            array_unshift($key_stack, $key);
        }
        return $actions;
    }
    
    
    public function get_actions() {
        $class = new ReflectionClass(get_class($this));
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        $props = $class->getDefaultProperties();
        $prefix = isset($props['_action_prefix']) ? $props['_action_prefix'] : '';
        $actions = array();
        foreach ($methods as $method) {
            $action_name = null;
            if (preg_match("~^".$prefix."(.+)$~", $method->name, $action_name)) {
                $action_name = $action_name[1];
                $action_meta = $this->get_action_info($action_name);
                if (isset($action_meta['disabled']) && $action_meta['disabled']) {
                    continue;
                }
                $actions[$action_name]= $action_meta;
            }
        }
        return $actions;
    }
    
    
    
    /*
    public function render() {
        $res = array('input' => $this->process());
        $template = $this->find_template();
        return $template->render($res, $this->action);
    }
     * 
     */
}
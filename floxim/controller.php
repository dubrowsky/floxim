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
        if (isset($defaults['force'])) {
            $force_parts = preg_split("~,\s*~", trim($defaults['force']));
            foreach ($force_parts as $fp) {
                $fp = explode(".", $fp);
                if (count($fp) !== 2 || $fp[0] !== 'params') {
                    continue;
                }
                $forced [trim($fp[1])] = true;
            }
        }
        foreach ($defaults['params'] as $pk => $pv) {
            if (isset($settings[$pk])) {
                $settings[$pk]['value'] = $pv;
                if (isset($forced[$pk]) || isset($forced['*'])) {
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
    
    public function get_action_info($action) {
        $info_method = 'info_'.$action;
        if (method_exists($this, $info_method)) {
            return call_user_func(array($this, $info_method));
        }
        return array('name' => $action);
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
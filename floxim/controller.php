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
    public function param($name) {
        return isset($this->input[$name]) ? $this->input[$name] : null;
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
     * Пост-обработка, вызывается из fx_controller_infoblock->render()
     */
    public function postprocess($html) {
        return $html;
    }
    
    public function get_action_settings($action) {
        if (method_exists($this, 'get_action_settings_'.$action)) {
            return call_user_func(array($this, 'get_action_settings_'.$action));
        }
        return array();
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
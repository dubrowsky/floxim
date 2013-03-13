<?php

/**
 * Базовый класс для всех контроллеров
 * Конструктор принимает параметры и экшн
 * Отработка - через метод process()
 * @property fx_admin_ui $ui  
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
     * 
     * @param type $input
     * @param type $action
     * @param type $do_return временная штука, потом будет единственным возможным поведением
     * @return array массив с результатами работы контроллера
     */
    public function process($input = null, $action = null, $do_return = false) {
    	if (!is_null($input)) {
            $this->set_input($input);
    	}
    	$this->set_action($action);
    	$action = is_callable(array($this, $this->action)) ? $this->action : 'default_action';
        return $this->$action($this->input);
    }
    
    public function find_template() {
        $tpl = str_replace('fx_controller_', '', get_class($this));
        return fx::template($tpl);
    }
    
    public function find_template_variant() {
        return $this->action;
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
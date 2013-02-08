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
    	
    	if (!is_callable(array($this, $action))) {
            // default action should be here...
            dev_log('no action for controller', $this, $action);
            //die("Error! No action! Class: ".get_class($this).", action:".htmlspecialchars($action)) ;
    	} else {
            $this->action = $action;
        }
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
    	$action = $this->action ? $this->action : 'default_action';
        dev_log('call action '.get_class($this).'.'.$action);
        return $this->$action($input);
    }
    
    public function find_template() {
        $tpl = str_replace('fx_controller_', '', get_class($this));
        return fx::template($tpl);
    }
    
    public function find_template_variant() {
        return $this->action;
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
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


    static protected function _get_abbr($name) {
        $vowels = array('a', 'e', 'i', 'o', 'u', 'y');
        $head = substr($name,0,1);
        $words = explode(" ", $name);
        if (count($words) > 1) {
            $tail = substr($name, 1, 1).'.'.substr($words[1], 0, 1);
        } else {
            $tail = substr(str_replace($vowels, '', strtolower(substr($name,1))), 0, 2);
            if (strlen($name) > 2 && strlen($tail) < 2) {
                $tail = substr($name, 1, 2);
            }
        }
        return $head.$tail;
    }

    public function get_action_method() {
        $actions = explode('_', $this->action);
        while($actions){
            $action = $this->_action_prefix.implode('_', $actions);
            array_pop($actions);
            if (is_callable(array($this, $action))) {
                return $action;
            }
        }
        return  'default_action';
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
    public function get_available_templates( $layout_name = null , $area_size = null) {
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
                if ($area_size && isset($tplv['size'])) {
                    $size = fx_template_suitable::get_size($tplv['size']);
                    if (!fx_template_suitable::check_sizes($size, $area_size)) {
                        continue;
                    }
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
        if (isset($params['defaults']) && is_array($params['defaults'])) {
            foreach ($params['defaults'] as $param => $val) {
                $settings[$param]['value'] = $val;
            }
        }
        if (!isset($params['force'])) {
            return $settings;
        }
        foreach (array_keys($params['force']) as $forced_key) {
            unset($settings[$forced_key]);
        }
        return $settings;
    }
    
    public function get_config() {
        $sources = $this->_get_config_sources();
        $actions = $this->_get_real_actions();
        $blocks = array();
        $meta = array();
        $my_name = $this->get_controller_name();
        foreach ($sources as $src) {
            $src_name = null;
            preg_match("~/([^/]+?)/[^/]+$~", $src, $src_name);
            $is_own = $src_name && $my_name && $src_name[1] === $my_name;
            $src = include $src;
            if (!isset($src['actions'])) {
                continue;
            }
            $src_actions = $this->_prepare_action_config($src['actions']);
            foreach ($src_actions as $k => $props) {
                $action_codes = preg_split("~\s*,\s*~", $k);
                foreach ($action_codes as $ak) {
                    $inherit_vertical = preg_match("~^\*~", $ak);
                    // родительские блоки без вертикального наследования не используем
                    if (!$is_own && !$inherit_vertical) {
                        continue;
                    }
                    $inherit_horizontal = preg_match("~\*$~", $ak);
                    $action_code = trim($ak, '*');
                    $blocks []= $props;
                    $meta []= array($inherit_horizontal, $action_code);
                    if (!isset($actions[$action_code])) {
                        $actions[$action_code] = array();
                    }
                }
            }
        }
        
        foreach ($blocks as $bn => $block) {
            list($inherit, $bk) = $meta[$bn];
            foreach ($actions as $ak => &$action_props) {
                if (
                        $ak === $bk || 
                        (
                            $inherit && 
                            ($bk === '.' || substr($ak, 0, strlen($bk)) === $bk)
                        )
                ) {
                    $action_props = array_replace_recursive($action_props, $block);
                    if (isset($action_props['settings'])) {
                        foreach ($action_props['settings'] as $s_key => $s) {
                            if (!isset($s['name'])) {
                                $action_props['settings'][$s_key]['name'] = $s_key;
                            }
                        }
                    }
                }
            }
        }
        unset($actions['.']);
        return array('actions' => $actions);
    }

    public function get_controller_name($with_type = false){
        $name = preg_replace('~^[^\W_]+_[^\W_]+_~', '', get_class($this));
        if (!$with_type) {
            $name = preg_replace('~^[^\W_]+_~', '', $name);
        }
        return $name;
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
    

    public function _get_real_actions() {
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
            $res[$action] = $info;
        }
        return $res;
    }
    
    public function after_save_infoblock($is_new) {
        if (! ($ib_id = $this->get_param('infoblock_id')) ) {
            return;
        }
        $infoblock = fx::data('infoblock', $ib_id);
        $action = $infoblock['action'];
        $full_config = $this->get_config();
        if (!isset($full_config['actions'][$action])) {
            return;
        }
        $config = $full_config['actions'][$action];
        if ($is_new && isset($config['install'])) {
            if (is_callable($config['install'])) {
                call_user_func($config['install'], $infoblock, $this);
            }
        }
    }
    
    public function before_delete_infoblock() {
        if (! ($ib_id = $this->get_param('infoblock_id')) ) {
            return;
        }
        $infoblock = fx::data('infoblock', $ib_id);
        $action = $infoblock['action'];
        $full_config = $this->get_config();
        if (!isset($full_config['actions'][$action])) {
            return;
        }
        $config = $full_config['actions'][$action];
        if (isset($config['delete'])) {
            call_user_func($config['delete'], $infoblock, $this);
        }
    }
}
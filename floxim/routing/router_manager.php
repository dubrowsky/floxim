<?
/**
$controller = fx::routers()->route('/news/news_10.html');
fx::routers()->register($callback, 1);
*/

class fx_router_manager {

	protected $routers = array();
	
	public function register(fx_router $router, $name = null, $priority = null) {
		if (is_null($name)) {
			$name = preg_replace("~^fx_router_~", '', get_class($router)); 
		}
		if (is_null($priority)) {
			$priority = count($this->routers) + 1;
		}
		$this->routers[$name] = array('router' => $router, 'priority' => $priority);
		$this->_reorder_routers();
	}
	
	public function register_system() {
		foreach (array('admin', 'front') as $r_name) {
			try {
				$classname = 'fx_router_'.$r_name;
				$router = new $classname;
				$this->register($router);
			} catch (Exception $e) {
				// no file
			}
		}
	}
	
	protected function _reorder_routers() {
		usort($this->routers, function($a, $b) {
			return $a['priority'] - $b['priority'];	
		});
	}
	
        /**
         * Выполнить все зарегистрированые роутеры, вернуть наиболее подходящий контроллер
         * @param type $url
         * @param type $context
         * @return fx_controller
         */
	public function route($url = null, $context = null) {
		if (is_null($url)) {
			$url = getenv('REQUEST_URI');
		}
		foreach ($this->routers as $r) {
			$result = $r['router']->route($url, $context);
			if ($result) {
				return $result;
			}
		}
	}
}
?>
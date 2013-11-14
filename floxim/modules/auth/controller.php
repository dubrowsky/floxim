<?php

class fx_controller_module_auth extends fx_controller_module {

    public function auth($input) {
        $AUTH_USER = $input['AUTH_USER'];
        $AUTH_PW = $input['AUTH_PW'];

        // попытка авторизации
        $user = fx::data('content_user')
                ->where(fx::config()->AUTHORIZE_BY, $AUTH_USER)
                ->one();
        if (!$user || !$user['password'] || crypt($AUTH_PW, $user['password'])!==$user['password']) {
            $this->redirect();
            return;
        }
        
        $fx_sid = $user->authorize();
        if (fx::is_admin()) {
            self::_cross_site_forms(array(
                "essence" => "module_auth",
                "action" => "init_session",
                "sid" => $fx_sid
            ));
            die();
        }
    }

    public static function _cross_site_forms($fields) {
    	$sites = fx::data('site')->where('id', fx::env('site')->get('id'), '!=')->all();
    	$next_location = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : '/';
    	if (preg_match("~/floxim/$~i", $next_location) || empty($next_location)) {
            $next_location = '/';
        }
    	?>
        <script type="text/javascript" src="<?=FX_JQUERY_PATH?>"></script>
        <script type="text/javascript">
			function js_next() {
				alert('d');
				//document.location.href = "<?=$next_location?>";
			}
			var count_sites = <?=count($sites)?>;
			var data = <?=json_encode($fields)?>;
			var sites = <?=json_encode($sites->get_values('domain', 'id'))?>;
        	for (var i in sites) {
        		$.ajax({
        			type:'post',
        			url:'http://'+sites[i]+'/floxim/',
        			data:data,
        			xhrFields: {
					   withCredentials: true
					},
					crossDomain: true,
        			complete: (function(i) { return function(res) {
						count_sites--;
						if (count_sites === 0) {
							js_next();
						}
        			}}) (i)
        		});
        	}
        </script>
        <?
    }

    public function init_session($input) {
    	if (isset($_SERVER['HTTP_ORIGIN'])) {
			$origin = $_SERVER['HTTP_ORIGIN'];
			$site = fx::data('site')->get_by_host_name($origin);
			if (!$site) {
				return;
			}
			header("Access-Control-Allow-Origin: ".$origin);
			header("Access-Control-Allow-Credentials: true");
		}
    	fx::input()->_COOKIE['fx_sid'] = $input['sid'];
    	$u = new fx_content_user();
    	$u->create_session('cross_authorize', 0, 1);
    	die();
    }

    public function logout() {
        $user = fx::env()->get_user();
        if ($user) {
            $user->unauthorize();
        }
        $this->redirect();
    }
    
    public static function redirect() {
        ob_end_clean();
        $refer = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : '/';
        header("Location: " . $refer);
        die();
    }

}
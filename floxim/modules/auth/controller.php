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
    	$sites = fx::data('site')->all();
		$current_site = fx::env('site');
		foreach ($sites as $site) {

			if ($site['id'] == $current_site['id']) {
				continue;
			}

			?>
			<form method="POST" action="http://<?=$site['domain']?>/floxim/" target="ifr_<?=$site['id']?>" style="width:5px; height:5px; overflow:hidden;">
				<?foreach ($fields as $k => $v) {?>
					<input type="hidden" name="<?=$k?>" value="<?=$v?>" />
				<?}?>
				<input type="submit" value="Go" style="position:relative; left:100px;" />
				<iframe style="border:0;" name="ifr_<?=$site['id']?>" id="ifr_<?=$site['id']?>"></iframe>
			</form>
			<?
		}
        $next_location = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : '/';
        if (preg_match("~/floxim/$~i", $next_location)) {
            $next_location = '/';
        }
		?>
		<script type="text/javascript">
		function js_next() {
			document.location.href = "<?=$next_location?>";
		}
		var forms = document.getElementsByTagName('form');
		for (var i = 0; i< forms.length; i++) {
			var c_form = forms[i];
			c_form.getElementsByTagName('iframe')[0].onload = (function(f) {
				return function() {
					f.parentNode.removeChild(f);
					if (document.getElementsByTagName('form').length == 0) {
						js_next();
					}
				}
			})(c_form);
			c_form.submit();
		}
        if (forms.length == 0) {
            js_next();
        }

		</script>
		<?
    }

    public function init_session($input) {
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
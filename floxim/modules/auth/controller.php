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
    	?>
        <script type="text/javascript" src="<?=FX_JQUERY_PATH?>"></script>
        <script type="text/javascript">
			function js_next() {
				document.location.href = "<?=$next_location?>";
			}
			var count_sites = <?=count($sites)?>;
			var data = <?=json_encode($fields)?>;
			var sites = <?=json_encode($sites->get_values('domain', 'id'))?>;
        	for (var i in sites) {
        		$.ajax({
        			type:'post',
        			url:'http://'+sites[i]+'/floxim/',
        			data:data,
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
        return;
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
        if (preg_match("~/floxim/$~i", $next_location) || empty($next_location)) {
            $next_location = '/';
        }
        ?>
        <script type="text/javascript">
        function js_next() {
            alert("<?=$next_location?>");
            document.location.href = "<?=$next_location?>";
        }
        var forms = document.getElementsByTagName('form');
        for (var i = 0; i< forms.length; i++) {
                var c_form = forms[i];
                var handler = (function(f) {
                    return function() {
                        console.log('loaded', f.getAttribute('action'));
                        f.parentNode.removeChild(f);
                        if (document.getElementsByTagName('form').length === 0) {
                            js_next();
                        }
                    };
                })(c_form);
                var iframe = c_form.getElementsByTagName('iframe')[0];
                iframe.onload = handler;
                iframe.onerror = handler;
                c_form.submit();
        }
        //if (forms.length == 0) {
        setTimeout(js_next, 10000);
        //}

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
		}
    	fx::input()->_COOKIE['fx_sid'] = $input['sid'];
    	$u = new fx_content_user();
    	$u->create_session('cross_authorize', 0, 1);
    	dev_log('authr', $u, $input, $_SERVER);
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
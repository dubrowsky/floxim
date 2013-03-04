<?php

class fx_controller_module_auth extends fx_controller_module {

    public function auth($input) {

        $fx_core = fx_core::get_object();
        $db = $fx_core->db;
        $AUTH_USER = $input['AUTH_USER'];
        $AUTH_PW = $input['AUTH_PW'];

        // попытка авторизации
        $user = fx::data('user')->get("`" . fx::config()->AUTHORIZE_BY . "` = '" . $db->escape($AUTH_USER) . "'
        AND `password` = " . fx::config()->DB_ENCRYPT . "('" . $db->escape($AUTH_PW) . "')
        AND `checked` = 1");
        if ($user) {
            $fx_sid = $user->authorize();
            if ($user->perm()->is_supervisor()) {
				self::_cross_site_forms(array(
					"essence" => "module_auth",
					"action" => "init_session",
					"sid" => $fx_sid
				));
				die();
            }
        }
        $this->redirect();
    }

    protected static function _cross_site_forms($fields) {
    	$fx_core = fx_core::get_object();
    	$sites = fx::data('site')->get_all();
		$current_site = $fx_core->env->get_site();
		foreach ($sites as $site) {

			if ($site['id'] == $current_site['id']) {
				continue;
			}

			?>
			<form method="POST" action="http://<?=$site['domain']?>/floxim/" target="ifr_<?=$site['id']?>" style="width:300px; height:100px; overflow:hidden;">
				<?foreach ($fields as $k => $v) {?>
					<input type="text" name="<?=$k?>" value="<?=$v?>" />
				<?}?>
				<input type="submit" value="Go" />
				<iframe name="ifr_<?=$site['id']?>" id="ifr_<?=$site['id']?>"></iframe>
			</form>
			<?
		}
		?>
		<script type="text/javascript">
		function js_next() {
			document.location.href = "<?=$_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : '/'?>";
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

		</script>
		<?
    }

    public function init_session($input) {
    	$fx_core = fx_core::get_object();
    	$fx_core->input->_COOKIE['fx_sid'] = $input['sid'];
    	$u = new fx_user();
    	$u->create_session('cross_authorize', 0, 1);
    	die();
    }

    public function auth_by_twitter($input) {
        fx_auth_twitter::proccess($input);
    }

    public function auth_by_facebook ( $input ) {
        fx_auth_facebook::proccess($input);
    }

    public function logout($input) {
        $fx_core = fx_core::get_object();
        $user = $fx_core->env->get_user();
        if ($user) {
            $user->unauthorize();
        }

        $this->redirect();
    }

    public function confirm($input) {
        $fx_core = fx_core::get_object();

        $confirm_user = intval($input['confirm_user']);
        $confirm_code = $input['ucc'];

        $result = "";
        if (!$confirm_user || !$confirm_code) {
            $result = "Не передан код подтверждения регистрации.";
        }

        $fx_auth = fx_auth::get_object();
        $user = $fx_auth->registration_confirm($confirm_user, $confirm_code);

        if (!$user) {
            $result = "Неверный код подтверждения регистрации.";
        } else {
            if ($fx_core->get_settings('registration_premoderation', 'auth')) {
                $result = "Ваш адрес e-mail подтвержден. Дожитесь проверки и активации учетной записи администратором.";
            } else {
                $result = "Ваш аккаунт активирован.";
                if ($fx_core->get_settings('autoauthorize', 'auth')) {
                    $user->authorize();
                }
            }
        }

        $ib = fx_auth::get_register_infoblock();
        $subdivision = fx::data('subdivision')->get_by_id($ib['subdivision_id']);
        $page = new fx_controller_page();
        $page->set_main_content($result);
        $page->load_subdivision($subdivision);
        $page->index();
    }

    public function recovery_password ( $input ) {
        $fx_core = fx_core::get_object();
        if ( $input['uid'] && $input['ucc'] ) {
            $user = fx::data('user')->get('id', $input['uid'], 'registration_code', $input['ucc'], 'checked', 1);
            if ( $user ) {
                $user->authorize();

                $component_id = $fx_core->get_settings('user_component_id', 'auth');
                $change_password_ctpl = fx::data('ctpl')->get('component_id', $component_id, 'keyword', 'passwordchange');
                $infoblock = fx::data('infoblock')->get('list_ctpl_id', $change_password_ctpl['id']);
                $subdivision_id = $infoblock['subdivision_id'];
            }
            else {
                $content = "Неверный код";
                $subdivision_id = $input['subdivision'];
            }

            $page = new fx_controller_page();
            if ( $content ) {
                $page->set_main_content($result);
            }

            $page->load_subdivision($subdivision_id);
            $page->index();

        }
        else {
            $fx_auth = fx_auth::get_object();
            $res = $fx_auth->recovery_password ($input['login'], $input['subdivision']);
            $result = array('result' => $res ? 'ok' : 'error');
            return $result;
        }

    }

    public function change_relation($input) {
        $do = $input['do'];
        $id = intval($input['id']);

        if (!$id) {
            echo "Неверный id пользователя";
            return;
        }

        $fx_auth = fx_auth::get_object();
        switch ($do) {
            case 'add_friend' :
                $fx_auth->relation->add_friend($id);
                break;
            case 'add_banned' :
                $fx_auth->relation->add_banned($id);
                break;
            case 'delete_relation' :
                $fx_auth->relation->delete_relation($id);
                break;
            default :
                echo "Неверное действие";
                return;
        }

        $this->redirect();
    }

    public static function redirect() {
        ob_end_clean();
        $refer = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : '/';
        header("Location: " . $refer);
        echo "Location " . $refer;
    }

}

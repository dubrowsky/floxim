<?
class fx_controller_widget_authform extends fx_controller_widget {
    public function do_show() {
        if (fx::user()) {
            return array('_meta' => array('disabled' => true));
        }
        return;
        if (count($_POST) > 0) {
            $login = $_POST['login'];
            $password = $_POST['password'];
            $user = fx::data('content_user')->get(
                    "`".fx::config()->AUTHORIZE_BY."` = '".fx::db()->escape($login)."'
                        AND `password` = ".fx::config()->DB_ENCRYPT."('".fx::db()->escape($password)."')"
            );
            if ($user) {
                $user->authorize();
                fx::http()->refresh();
            }
        }
    }
}
?>
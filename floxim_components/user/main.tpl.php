<?php
class ctpl_user_main extends fx_tpl_component {
function prefix () {
extract($this->get_vars());
?>
<table class='fx_user_list'>
<tr>
   <th width='1%'>Аватара</th>
   <th>Имя</th>
    <?= $pm_allow  ? "<th width='1%'></th>" : "" ?>
    <?= $rel_allow ? "<th width='15%'>Отношение</th>" : "" ?>
    <th width='1%'>Статус</th>
    <th width='1%'>Зарегистрирован</th>
</tr>
<?php
}


function record () {
extract($this->get_vars());
?>
<?php
if (!$f_name) {
    $f_name = $f_login;
}
$user_avt = $fx_core->user->get_by_id($f_id)->get_file_path();
?>

<tr>
   <td align='center' valign='center'><img width="64px" src="<?=$user_avt?>" alt="<?=$f_name?>" /></td>
   <td><a href="<?=$full_link?>"><?=$f_name?></a></td>

   <?php
   if ($pm_allow) {
       if ($f_id != $user['id'])
          echo "<td><a href='".$fx_auth->get_user_pm_url($f_id)."'>сообщение</a></td>";
       else
           echo "<td></td>";
   }
   if ($rel_allow) {
       echo "<td>";
       if ($f_id != $user['id']) {

           if ( $relation[$f_id] == $fx_auth->relation->TYPE_FRIEND )
               echo "<a href='".$fx_auth->relation->delete_relation_url($f_id)."'>Убрать из друзей</a>";
           elseif ( $relation[$f_id] == $fx_auth->relation->TYPE_BANNED )
               echo "<a href='".$fx_auth->relation->delete_relation_url($f_id)."'>Убрать из врагов</a>";
           else {
               if ($friend_allow)
                  echo "<a href='".$fx_auth->relation->add_friend_url($f_id)."'>Добавить в друзья</a><br/>";
               if ($banned_allow)
                  echo "<a href='".$fx_auth->relation->add_banned_url($f_id)."'>Добавить во враги</a>";
           }
       }
       echo "</td>";
   }
   ?>

  <td><?= $fx_auth->is_online($f_id) ? "<span class='online'>online</span>" : "<span class='offline'>offline</span>" ?></td>
  <td><?= $f_created ?></td>
</tr>
<?php
}


function suffix () {
extract($this->get_vars());
?>
</table>
<br>
<?= $fx_tpl ? $fx_tpl->listing($fx_infoblock) : null ?>
<?php
}


function settings_index () {
extract($this->get_vars());
$fx_core->page->add_file($fx_path_css . 'user.css');

// текущий пользователь
$fx_auth = $this->m['fx_auth'] = fx_auth::get_object();
$user = $this->m['user'] = $fx_core->env->get_user();
$online = $this->m['online'] = $fx_auth->users_online();

// доступны личные сообщения, друзья-враги
$this->m['pm_allow'] = $user && $fx_core->get_settings('pm_allow', 'auth');
$this->m['friend_allow'] = $fx_core->get_settings('friend_allow', 'auth');
$this->m['banned_allow'] = $fx_core->get_settings('banned_allow', 'auth');
$allow_rel = $this->m['rel_allow'] = $user && ( $this->m['friend_allow'] || $this->m['banned_allow']);

if ($allow_rel) {
    $this->m['relation'] = $fx_auth->relation->get_all_relation();
}

if ( $fx_core->get_settings('bind_to_catalogue', 'auth') ) {
    $query_where[] = "a.`catalogue_id` IN (0, ".$fx_core->env->get_catalogue('id').")";
}

if ($fx_core->input->GET('online') !== null) {
    $query_where[]  = "a.`id` IN (".($online ? join(", ", $online) : "").")";
}

if ($search_login = $fx_core->input->GET('login')) {
    $query_where[]  = "(a.`login` LIKE '%".$search_login ."%' OR a.`name` LIKE '%".$search_login ."%')";
}


if ($query_where) {
    $query_param['query_where'] = join(" AND ", $query_where);
}
}

function h1 () {
extract($this->get_vars());
?>
Профиль пользователя <?=$f_name?>
<?php
}


function full () {
extract($this->get_vars());
?>
<?php
if (!$f_name) {
    $f_name = $f_login;
}
$user_avt = $fx_core->user->get_by_id($f_id)->get_file_path();
?>
<table class='fx_user_full'>
  <tr>
    <td>
      <img width="64px" src="<?=$user_avt?>" alt="<?=$f_name?>" />
    </td>
    <td>
      <table class='fx_user_table'>
        <tr class='grey'>
          <td>Имя пользователя:</td>
          <td><b><?=$f_name?></b>
        </tr>
        <tr>
          <td>Статус:</td>
          <td><?= $fx_auth->is_online($f_id) ? "<span class='online'>online</span>" : "<span class='offline'>offline</span>" ?></td>
        </tr>
        <tr class='grey'>
          <td>Дата регистрации:</td>
          <td><?=$f_created?></td>
        </tr>
        <tr>
          <td>Подпись на форуме:</td>
          <td><?=$f_forum_signature?></td>
        </tr>
        <?php if ($friend_allow) { ?>
            <tr class='grey'>
              <td>Список друзей:</td>
              <td>
                  <?php
                  $friends = $fx_auth->relation->get_all_friend($f_id);
                  if ($friends) {
                      foreach ($friends as $k=>$v) {
                          $u = $fx_core->user->get('id', $k);
                          $u_name = $u['name'] ? $u['name'] : $u['login'];
                          $friends_arr[] = "<a href='".fx_auth::get_profile_url($u['id'])."'>".$u_name."</a>";
                      }
                      echo join(", ", $friends_arr);
                  }
                  else {
                      echo "Список пуст";
                  }

                  ?>
              </td>
            </tr>
        <?php
        }
        ?>
      </table>
      <br />

       <?php
       if ($pm_allow && ($f_id != $user['id']))
               echo "<a class='fx_user_link_btn' href='".$fx_auth->get_user_pm_url($f_id)."'>Личное сообщение</a>";

       if ($rel_allow && ($f_id != $user['id'])) {
           if ( $relation[$f_id] == $fx_auth->relation->TYPE_FRIEND )
               echo "<a class='fx_user_link_btn' href='".$fx_auth->relation->delete_relation_url($f_id)."'>Убрать из друзей</a>";
           elseif ( $relation[$f_id] == $fx_auth->relation->TYPE_BANNED )
               echo "<a class='fx_user_link_btn' href='".$fx_auth->relation->delete_relation_url($f_id)."'>Убрать из врагов</a>";
           else {
               if ($friend_allow)
                  echo "<a class='fx_user_link_btn' href='".$fx_auth->relation->add_friend_url($f_id)."'>Добавить в друзья</a>";
               if ($banned_allow)
                  echo "<a class='fx_user_link_btn' href='".$fx_auth->relation->add_banned_url($f_id)."'>Добавить во враги</a>";
           }
       }
       ?>

    </td>
  </tr>
</table>
<?php
}

function settings_full () {
extract($this->get_vars());
$this->settings_index();

}





function begin_add_form () {
extract($this->get_vars());
?>
<?php
    if ( ! $fx_core->get_settings('allow_registration', 'auth') ) {
        $this->cancel('Самостоятельная регистрация запрещена.');
        return;
    }
?>

<?= parent::begin_add_form() ?>
<input type="hidden" name="essence" value="user">
<input type="hidden" name="action" value="register">
<?php
}


function after_add () {
extract($this->get_vars());
?>
<?php
$fx_auth = fx_auth::get_object();
$settings = $fx_core->get_settings('', 'auth');


// подтверждение через почту
if ( $settings['registration_confirm'] ) {
    $res = $fx_auth->mail->confirm_user($fx_message['id'], $fx_core->input->POST('password'));
    if ($res)
        echo "Письмо с подтверждением регистрации успешно выслано вам на e-mail.<br/>";
    else
        echo "Произошла ошибка при отправке письма с подтверждением регистрации. Обратитесь к администратору сайта.<br/>";
}

// премодерация администратором
if ( $settings['registration_premoderation'] ) {
    echo "Ваша учетная запись будет активирована после проверки администратора.<br/>";
}

// подтверждение не нужно
if ( !$settings['registration_premoderation'] && !$settings['registration_confirm'] ) {
  echo "Регистрация прошла успешно.<br/>";
  // авторизация после регистрации
  if ( $settings['autoauthorize'] ) {
    $user = $fx_core->user->get_by_id($fx_message['id']);
    $user['checked'] = 1;
    $user->authorize();
    fx_controller_module_auth::redirect();
  }
}

// оповещение администратора
if ( $settings['registration_notify_admin'] )
    $res = $fx_auth->mail->registration_notify_admin($fx_message['id']);

?>
<?php
}


function title () {
extract($this->get_vars());
?>
Личный кабинет пользователя <?=$f_name?>
<?php
}


function add_form () {
	extract($this->get_vars());
  $login_field = fx::config()->AUTHORIZE_BY;
  if (isset($fx_fields[$login_field])) {
	  echo $fx_fields[$login_field]->get_html();
  }
  echo fx_field_string::show_optional('password','Пароль', 'password');
  echo fx_field_string::show_optional('password1','Повторите пароль', 'password');

  foreach ( $fx_fields as $field_name => $field ) {
    if ( $field_name != $login_field ) {
       echo $fx_fields[$field_name]->get_html();
    }
  }
?>
<?php
}


}
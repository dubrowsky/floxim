<?php
class ctpl_pm_main extends fx_tpl_component {

    public function prefix() {

extract($this->get_vars());
?>

<div>
    <div style="float: right; padding-bottom: 10px;"><a href="<?= fx_auth::get_object()->get_user_list_url() ?>">Написать сообщение</a></div>
<div style="clear: both;"></div>

<?php

if (!$fx_total_rows)
    echo "<center><br><br>Нет сообщений</center>";

?>

<?php
}


public function record() {

extract($this->get_vars());
?>
<?php
$message = fx_bbcode( $fx_core->util->get_text_preview($f_message) );
$is_new = ($f_status == 1) && ($f_usr_id != $curr_user);

$this->show_dialoglist_item($f_usr_id, $f_usr_name, $f_created, $message, $full_link, $fx_core->util->is_even($f_num), $is_new);
?>
<?php
}


public function suffix() {

extract($this->get_vars());
?>
<?= $fx_tpl ? $fx_tpl->listing($fx_infoblock) : null ?>
</div>
<?php
}

public function settings_index() {
extract($this->get_vars());

if ($fx_core->input->GET('ajax') !== null) {
    $this->ajax_request();
    die;
}

if ( ! $fx_core->get_settings('pm_allow', 'auth') )
    $this->cancel('Личные сообщения отключены.');

$curr_user = $this->m['curr_user'] = $fx_core->env->get_user('id');
if (!$curr_user)
    $this->cancel ("Вы не авторизованы.");

if ($this->is_canceled())
    return;

$res = $fx_core->db->get_results("SELECT `user_id` as 'user_id' , MAX(`id`) as 'id' FROM `{{message".$fx_component->get('id')."}}` WHERE `to_user` = '".$curr_user."' AND `checked`=1 GROUP BY `user_id`");
if ($res) {
    foreach ($res as $v) {
        $users[$v['user_id']] = $v['id'];
    }
}

$res = $fx_core->db->get_results("SELECT `to_user` as 'user_id' , MAX(`id`) as 'id' FROM `{{message".$fx_component->get('id')."}}` WHERE `user_id` = '".$curr_user."' AND `checked`=1 GROUP BY `to_user`");
if ($res) {
    foreach ($res as $v) {
        if ( (!$users[$v['user_id']]) || ($users[$v['user_id']] < $v['id']) )
                $users[$v['user_id']] = $v['id'];
    }
}

if (!$users)
    return;


$query_param['query_where'] = "a.`id` IN (". join(', ', $users) .")";
$query_param['query_join'] = "LEFT JOIN `{{user}}` AS usr ON usr.`id` = IF(a.`user_id`<>'".$curr_user."', a.`user_id`, a.`to_user`)";
$query_param['query_select'] = "usr.`id` AS 'usr_id', IF(usr.`name`<>'',usr.`name`,usr.`login`) AS 'usr_name'";
$query_param['query_order'] = "a.`created` DESC";

}

public function full() {
extract($this->get_vars());
?>

<?php

if ($f_user_id == $curr_user)
    $interlocutor_id = $f_to_user;
else if ($f_to_user == $curr_user)
    $interlocutor_id = $f_user_id;
else {
    echo "Это чужое сообщение.";
    return;
}

$messages = $fx_core->db->get_results("SELECT a.`id` as 'message_id',
    a.`message` as 'message',
    a.`created` as 'created',
    a.`status` as 'status',
    a.`user_id` as 'usr_id',
    IF(usr.`name`<>'',usr.`name`,usr.`login`) AS 'usr_name'
FROM `{{message".$fx_component->get('id')."}}` as a
LEFT JOIN `{{user}}` AS usr ON usr.`id` = a.`user_id`
WHERE (a.`user_id` = '".$interlocutor_id."' OR a.`to_user` = '".$interlocutor_id."') AND
    (a.`user_id` = '".$curr_user."' OR a.`to_user` = '".$curr_user."')
ORDER BY a.`id`");

if (!$messages)
    return;

$last_id = $messages[count($messages)-1]['message_id'];
?>

<script type="text/javascript">

    var pm_last_id = <?= $last_id ?>;

    function pm_check_messages() {

        var last = $('#pm_dialog table:last');

        $.ajax({
            url: '<?= fx::config()->HTTP_HOST . fx::config()->SUB_FOLDER . fx::config()->HTTP_ROOT_PATH ?>?essence=infoblock&id=<?=$fx_infoblock->get('id')?>&ajax&do=check_new&interlocutor=<?=$interlocutor_id?>&last_id='+pm_last_id+'&even='+last.hasClass('fx_background_even'),
            dataType: 'json',
            success: function(data){
                if (!data || (data['status'] != 'ok')) {
                    if (data && data['message'])
                        alert ('Ошибка! ' + data['message']);
                    else
                        alert ('Неизвестная ошибка при получении новых сообщений с сервера.');
                    return;
                }

                if (data['data'] && data['last_id']) {
                    $(data['data']).insertAfter(last);
                    pm_last_id = data['last_id'];
                    $('#pm_dialog').scrollTop($('#pm_dialog')[0].scrollHeight);  // прокручиваем в самый низ
                }
            },
            error: function() {
                alert('Неизвестная ошибка при получении новых сообщений с сервера.');
            }
        });
    }

    function pm_new_msg_timeout(id, style) {

        var f = function(e) {  //помечаем сообщение прочитанным через 2 сек
                setTimeout(function() {
                    document.getElementById(id).setAttribute('class', style);
                    },
                    2000);

               $('body').unbind('mousemove', f);
               $('body').unbind('keypress', f);

        };

        $('body').bind('mousemove', f);  // начнем отсчет 3 сек после первого движения мышью...
        $('body').bind('keypress', f);  // ... или нажатия клавиши
    }

    function pm_send_msg() {

       var msg = $('#pm_new_msg_area').val();

       if (!msg)
           return;

        $.ajax({
            url: '<?= fx::config()->HTTP_HOST . fx::config()->SUB_FOLDER . fx::config()->HTTP_ROOT_PATH ?>?essence=infoblock&id=<?=$fx_infoblock->get('id')?>&ajax&do=send&interlocutor=<?=$interlocutor_id?>&ib=<?=$fx_infoblock->get('id')?>',
            dataType: 'json',
            type: 'POST',
            data: 'message=' + msg, // todo url_encode
            success: function(data){
                if (!data || (data['status'] != 'ok')) {
                    if (data && data['message'])
                        alert ('Ошибка! ' + data['message']);
                    else
                        alert ('Неизвестная ошибка при отправке сообщения.');
                    return;
                }

                $('#pm_new_msg_area').val('');
                pm_check_messages();
            },
            error: function() {
                alert('Неизвестная ошибка при отправке сообщения.');
            }
        });

    }

</script>


<div id="pm_dialog" style="height: 250px; overflow-y: scroll">
<?php

foreach ($messages as $m) {
    $is_even = $fx_core->util->is_even($i++);
    $is_new = ($m['status'] == 1) && ($m['usr_id'] != $curr_user);
    $this->show_dialog_item($m['usr_id'], $m['usr_name'], $m['created'], $m['message_id'], $m['message'], $is_even, $is_new);
}

?>
</div>

<textarea class="fx_form_field fx_form_field_text" type="text" name="f_message" value="" id="pm_new_msg_area"></textarea>
<br>
<input type="button" value="Отправить" onclick="pm_send_msg()">

<script type="text/javascript">

    setInterval(pm_check_messages, 10000);  // проверяем новые сообщения раз в 10 сек

    $('#pm_dialog').scrollTop($('#pm_dialog')[0].scrollHeight);  // прокручиваем диалог в самый низ

    $('#pm_new_msg_area').bind('keydown', function(e) {
        if((e.ctrlKey) && ((e.keyCode == 0xA)||(e.keyCode == 0xD))) { // отправка сообщения по Ctrl+Enter
          pm_send_msg();
        }
    });

</script>

<?php
}

public function settings_full() {
extract($this->get_vars());

if ( ! $fx_core->get_settings('pm_allow', 'auth') )
    $this->cancel('Личные сообщения отключены.');

$curr_user = $this->m['curr_user'] = $fx_core->env->get_user('id');
if (!$curr_user)
    $this->cancel ("Вы не авторизованы.");

if ($this->is_canceled())
    return;

}



function add_form () {
extract($this->get_vars());
?>
<?php

if ( ! $fx_core->get_settings('pm_allow', 'auth') )
    $this->cancel('Личные сообщения отключены.');

$to_user = $user ? $user : intval($fx_core->input->GET('user'));
if (!$to_user)
    $this->cancel('Не передан id пользователя-адресата');

$usr_obj = $fx_core->user->get_by_id($to_user);
if (!$usr_obj)
    $this->cancel('Пользователя с id='.$to_user.' не существует.');

if ($this->is_canceled())
    return;

$usr_name = $usr_obj['name'] ? $usr_obj['name'] : $usr_obj['login'];

?>
Сообщение пользователю <a href="<?= fx_auth::get_profile_url($to_user) ?>"><?= $usr_name ?></a>
<br>
<input name="f_to_user" value="<?= $to_user ?>" type="hidden">
<input name="f_status" value="1" type="hidden">
<br>
<br>
<label>Сообщение(*):</label>
<br>
<textarea class="fx_form_field fx_form_field_text" type="text" name="f_message" value=""></textarea><br>
<?php
}

/**
 * Показываем элемент из списка диалогов
 */
protected function show_dialoglist_item($usr_id, $usr_name, $created, $message, $full_link, $is_even, $is_new) {
    extract($this->get_vars());


    $usr = $fx_core->user->get_by_id($usr_id);
    $usr_avt = $usr ? $usr->get_file_path() : null;

    if ($is_new)
        $bg_class = "fx_background_selected";
    else
        $bg_class = "fx_background_" . ($is_even ? "even" : "odd");
?>

<table width="100%" id="pm_usr<?=$usr_id?>" class="<?=$bg_class?>" border="0" style="padding: 5px"
       onmouseover="this.setAttribute('class', 'fx_background_selected')"
       onmouseout="this.setAttribute('class', '<?=$bg_class?>')">

    <tr><td style="cursor: pointer;"onclick="document.location.href='<?=$full_link?>'">
        <img style="float:left;" src="<?=$usr_avt?>" alt="<?=$usr_name?>" width="50px">
        <div style="float:left; margin-left: 10px;">
            <a href="<?= fx_auth::get_profile_url($usr_id) ?>"><?=$usr_name?></a>
            <div style="padding: 5px">
                <?=$message?>
            </div>
        </div>
        <div style="float:right;" class="fx_date"><?=$created?></div>
    </td></tr>

</table>
<?php
}



/**
 * Показываем сообщение в диалогах
 */
protected function show_dialog_item($usr_id, $usr_name, $created, $message_id, $message, $is_even, $is_new) {
    extract($this->get_vars());


    $usr = $fx_core->user->get_by_id($usr_id);
    $usr_avt = $usr ? $usr->get_file_path() : null;

    $bg_class = "fx_background_" . ($is_even ? "even" : "odd");
?>

<table width="100%" id="pm_msg<?=$message_id?>" class="<?= $is_new ? 'fx_background_selected' : $bg_class ?>" border="0" style="padding: 5px">
    <tr><td style="cursor: pointer;"onclick="document.location.href='<?=$full_link?>'">
        <img style="float:left;" src="<?=$usr_avt?>" alt="<?=$usr_name?>" width="50px">
        <div style="float:left; margin-left: 10px;">
            <a href="<?= fx_auth::get_profile_url($usr_id) ?>"><?=$usr_name?></a>
            <div style="padding: 5px">
                <?=$message?>
            </div>
        </div>
        <div style="float:right;" class="fx_date"><?=$created?></div>
    </td></tr>

</table>
<?php

    if ($is_new) {
        ?>
        <script type="text/javascript">
            pm_new_msg_timeout('pm_msg<?=$message_id."', '".$bg_class?>');
        </script>
        <?php

        // помечаем прочитанным в БД
        $msg = $fx_core->message->get_by_id($fx_component->get('id'), $message_id);
        if ($msg) {
            $msg['status'] = 0;
            $msg->save();
        }

    }


}

public function ajax_request() {

    extract($this->get_vars());

    if ( ! $fx_core->get_settings('pm_allow', 'auth') ) {
        echo json_encode( array('status' => 'error', 'message' => 'Личные сообщения отключены.') );
        die;
    }

    if ($fx_core->input->GET('do') == 'check_new') {
        $last_id = intval($fx_core->input->GET('last_id'));
        $interlocutor_id = intval($fx_core->input->GET('interlocutor'));
        $curr_user = $fx_core->env->get_user('id');
        $is_even = $fx_core->input->GET('even') != 'false';

        if (!$last_id || !$interlocutor_id || !$curr_user) {
            echo json_encode( array('status' => 'error', 'message' => 'Неверный запрос') );
            die;
        }

        $result = array('status' => 'ok');

        $messages = $fx_core->db->get_results("SELECT a.`id` as 'message_id',
            a.`message` as 'message',
            a.`created` as 'created',
            a.`status` as 'status',
            a.`user_id` as 'usr_id',
            IF(usr.`name`<>'',usr.`name`,usr.`login`) AS 'usr_name'
        FROM `{{message".$fx_component->get('id')."}}` as a
        LEFT JOIN `{{user}}` AS usr ON usr.`id` = a.`user_id`
        WHERE (a.`user_id` = '".$interlocutor_id."' OR a.`to_user` = '".$interlocutor_id."') AND
            (a.`user_id` = '".$curr_user."' OR a.`to_user` = '".$curr_user."') AND
            a.`id` > '".$last_id."'");

        if ($messages) {

            $result['last_id'] = $messages[count($messages)-1]['message_id'];

            ob_start();

            foreach ($messages as $m) {
                $is_even = ! $is_even;
                $is_new = ($m['usr_id'] != $curr_user) && ($m['status'] == 1);
                $this->show_dialog_item($m['usr_id'], $m['usr_name'], $m['created'], $m['message_id'], fx_bbcode($m['message']), $is_even, $is_new);
            }

            $result['data'] = ob_get_clean();

        }
        echo json_encode($result);
        die;

    }
    elseif ($fx_core->input->GET('do') == 'send') {
        $interlocutor_id = intval($fx_core->input->GET('interlocutor'));
        $ib_id = intval($fx_core->input->GET('ib'));
        $message = $fx_core->input->POST('message');
        $message = str_replace('\n', '<br>', $message);
        $curr_user = $fx_core->env->get_user('id');

        if (!$ib_id || !$interlocutor_id || !$curr_user) {
            echo json_encode( array('status' => 'error', 'message' => 'Неверный запрос') );
            die;
        }

        $m = $fx_core->message->create($fx_component->get('id'));
        $m['infoblock_id'] = $ib_id;
        $m['to_user'] = $interlocutor_id;
        $m['user_id'] = $curr_user;
        $m['message'] = $message;
        $m['status'] = 1;
        $m['created'] = date('Y-m-d H:i:s');
        $m->save();

        echo json_encode( array('status' => 'ok') );
    }
}


function after_add () {
extract($this->get_vars());
?>
<?php
ob_end_clean();
$refer = $full_link;
header("Location: ".$refer);
echo "Location ".$refer;
?>
<?php
}


}
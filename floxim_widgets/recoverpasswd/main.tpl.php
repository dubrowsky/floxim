<?php
class widget_recoverpasswd extends fx_tpl_widget {

function record () {
extract($this->get_vars());
?>
<div id="password_recovery_success" style="display:none">
  Ссылка для смены пароля отправлена Вам на почту
</div>

<div id="password_recovery_error" style="display:none">
  Такой пользователь не найден
</div>

<form id='password_recovery_form'>
  <input type='hidden' name='essence' value='module_auth' />
  <input type='hidden' name='action' value='recovery_password' />
  <input type='hidden' name='fx_naked' value='1' />
  <input type='hidden' name='fx_ajax_check' value='1' />
  <input type='hidden' name='subdivision' value='<?=$fx_core->env->get_sub('id')?>' />
  <label>E-mail или логин:</label><br/>
  <input name='login' /><br/>
  <input type='submit' value='Отправить запрос' />
</form>



<script type="text/javascript">
  $("#password_recovery_form").submit( function() { 
    $(this).ajaxSubmit({
     dataType: 'json',
     success: function ( data ) {
        if (  data.result == 'ok' ) {
           $("#password_recovery_success").show();
           $("#password_recovery_form").hide();
           $("#password_recovery_error").hide();
        }
        else {
           $("#password_recovery_error").show();
         }
     }
   }); 
    return false;
  });
</script>
<?php
}


}
?>
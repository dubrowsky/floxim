<?php

class widget_authform extends fx_tpl_widget {

function record () {
extract($this->get_vars());
?>
<div class='fx_auth_block'>
            <? if ($user) : ?>
            <table width="100%">
                <tr>
                    <td>Здравствуйте, <a href="<?=$profile_link?>"><?=$user['login']?></a>! </td>
                <?=( $f_view == 'vertical' ? '</tr><tr>' : '')?>
                <? if ( $f_show_pm ) : ?>
                    <td><?=( $messages_new ? 'Новых сообщений: '.$messages_new : 'Новых сообщений нет')?></td> 
                    <?=( $f_view == 'vertical' ? '</tr><tr>' : '')?>
                <? endif;?>
                
                <td><a href="<?=$logout_link?>">Выйти</a></td>
                </tr>
            </table>
               
        <? else : ?>
                <form id='fx_auth_form' action='/floxim/' method='post'>
                    <input type='hidden' name='essence' value='module_auth' />
                    <input type='hidden' name='action' value='auth' />
                    <table> 
                        <tr><td>Логин:</td><td><input type='text' name='AUTH_USER' /></td></tr>
                        <tr><td>Пароль:</td><td><input type='password' name='AUTH_PW' /></td></tr>
                        <tr><td /><td ><input type='hidden' name='loginsave' value='1' /></td></tr> 
                        <tr><td /><td><input  type='submit' value='Вход'></td></tr>
                        <tr><td colspan="2">
                            <?=($reg_url ? '<a href="'.$reg_url.'">регистрация</a>' : '')?> 
                            <?=($recovery_link ? '<a href="'.$recovery_link.'">забыли пароль</a>' : '')?> 
                        </td></tr>
            <?= ($twitter ? '<tr><td /><td><a href="'.$twitter.'"><img src="/netcat_files/system/twitter_small.png" alt="twitter" /></a></td></tr>' : '') ?>
<?= ($facebook ? '<tr><td /><td><a href="'.$facebook.'"><img src="/netcat_files/system/twitter_small.png" alt="facebook" /></a></td></tr>' : '') ?>
                    </table>
                </form>
        <? endif; ?>

        </div>
<?php
}

function settings () {
extract($this->get_vars());
$this->m['user'] = $fx_core->env->get_user();

        $this->m['profile_link'] = fx_auth::get_profile_url();
        $this->m['messages_new'] = fx_auth::messages_new();
        $this->m['logout_link'] = fx_auth::get_logout_url();
  
        $this->m['reg_url'] = $f_show_reg_link ? fx_auth::get_registration_url() : '';
        $this->m['recovery_link'] = $f_show_recovery_link ? fx_auth::get_recovery_link() : '';
        
        $this->m['twitter'] = fx_auth_twitter::enabled() ? fx_auth_twitter::get_auth_url() : false;
        $this->m['facebook'] = fx_auth_facebook::enabled() ? fx_auth_facebook::get_auth_url() : false;
}

}
?>
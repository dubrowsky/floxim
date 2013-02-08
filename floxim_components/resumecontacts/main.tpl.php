<?php
class ctpl_resumecontacts_main extends fx_tpl_component {
function record () {
extract($this->get_vars());
?>
<div class="fx_row">
<?=($f_phone_none ? '<span class="fx_key">Телефон: </span><span class="fx_value">'.$f_phone.'</span><br/>' : '')?>
<?=($f_email_none ? '<span class="fx_key">Email: </span><span class="fx_value">'.$f_email.'</span><br/>' : '')?>
<?=($f_site_none ? '<span class="fx_key">Сайт / блог: </span><span class="fx_value">'.$f_site.'</span><br/>' : '')?>
<?=($f_jabber_none ? '<span class="fx_key">Jabber: </span><span class="fx_value">'.$f_jabber.'</span><br/>' : '')?>
<?=($f_icq_none ? '<span class="fx_key">ICQ: </span><span class="fx_value">'.$f_icq.'</span><br/>' : '')?>
<?=($f_twitter_none ? '<span class="fx_key">Twitter: </span><span class="fx_value">'.$f_twitter.'</span><br/>' : '')?>
<?=($f_fb_none ? '<span class="fx_key">Facebook: </span><span class="fx_value">'.$f_fb.'</span><br/>' : '')?>
<?=($f_address_none ? '<span class="fx_key">Адрес: </span><span class="fx_value">'.$f_address.'</span><br/>' : '')?>
<?=($f_postal_none? '<span class="fx_key">Почтовый адрес: </span><span class="fx_value">'.$f_postal.'</span><br/>' : '')?>
<?=($f_extra_none ? '<span class="fx_key">Дополнительно:</span><div>'.$f_extra.'</div><br/>' : '')?>
</div>
<?php
}


}

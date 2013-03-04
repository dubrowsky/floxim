<?php
class ctpl_person_imagedesc extends ctpl_person_main {
function prefix () {
extract($this->get_vars());
?>
<div class="fx_items">
<?php
}


function record () {
extract($this->get_vars());
?>
<?
$width = 100;
$height = 100;
?>

<div class="fx_item">
  <h3><a href="<?= $full_link ?>"><?= $f_name ?></a></h3>
  <img src="<?= $f_photo->resize($width, $height) ?>" title="<?= $f_name_none ?>" alt="<?= $f_name_none ?>" width="<?= $width ?>" height="<?= $height ?>"/>
  <p><b><?= $f_job ?></b><?=$f_debt ? ', '.$f_dept : '' ?></p>
  <p><?= $f_shorttext ?></p>
</div>
<?php
}


function suffix () {
extract($this->get_vars());
?>
</div>
<?php
}


function full () {
extract($this->get_vars());
?>
<?
  $width = 300;
?>
<div class="fx_row_full">
<img src=<?= $f_photo->resize($width) ?> alt="<?= $f_name_none ?>" title="<?= $f_name_none ?>" width="<?=  $width ?>" height="<?= $height ?>"/><br /><br />
<? if ($f_company) { ?><span class="fx_key">Компания: </span><span class="fx_value"><?= $f_company ?></span><br><? } ?>
<? if ($f_dept) { ?><span class="fx_key">Отдел / подразделение: </span><span class="fx_value"><?= $f_dept ?></span><br><? } ?>
<? if ($f_job) { ?><span class="fx_key">Должность: </span><span class="fx_value"><?= $f_job ?></span><br><? } ?>

<? if ($f_birthday && $fx_visual['date_view'] != "none") {
$date_key = $fx_visual['date_view'] != "age" ? "Дата рождения: " : "Возраст: ";

switch ($fx_visual['date_view']) {
case 'year':
  $date_value = $f_birthday->get_year();
break;
case 'md':
  $date_value = $f_birthday->format('d M');
break;
case 'age':
  $date_value = date('Y', time()) - $f_birthday->get_year();
break;
case 'full': default:
  $date_value = $f_birthday->get_date();
break;
} ?>
  <span class="fx_key"><?= $date_key ?></span>
  <span class="fx_value"><?= $date_value ?></span><br>
<? } ?>

<? if ($f_phone) { ?><span class="fx_key">Телефон: </span><span class="fx_value"><?= $f_phone ?></span><br><? } ?>
<? if ($f_email) { ?><span class="fx_key">Email: </span><span class="fx_value"><?= $f_email ?></span><br><? } ?>
<? if ($f_icq) { ?><span class="fx_key">ICQ: </span><span class="fx_value"><?= $f_icq ?></span><br><? } ?>
<? if ($f_skype) { ?><span class="fx_key">Skype: </span><span class="fx_value"><?= $f_skype ?></span><br><? } ?>
<? if ($f_site) { ?><span class="fx_key">Сайты: </span><span class="fx_value"><?= $f_site ?></span><br><? } ?>
<? if ($f_email) { ?><span class="fx_key">Email: </span><span class="fx_value"><?= $f_email ?></span><br><? } ?>
<? if ($f_text ) { ?><span class="fx_key">Описание: </span><span class="fx_value"><?= $f_text ?></span><? } ?>
</div>
<?php
}


function title () {
extract($this->get_vars());
?>
<?= $f_name ?>
<?php
}


function h1 () {
extract($this->get_vars());
?>
<?= $f_name ?>
<?php
}


}

<?php
class ctpl_person_shortperson extends ctpl_person_main {
function prefix () {
extract($this->get_vars());
?>
<div class="fx_items">
<?php
}





function suffix () {
extract($this->get_vars());
?>
</div>
<?php
}


function settings_index () {
extract($this->get_vars());
// кол-во колонок
$col = $this->m['col'] = $fx_visual['col'] ? $fx_visual['col'] : 1;

// группировка
if ($fx_visual['group']) {
  $query_param['query_order'] = "a.".$fx_visual['view'];
}
}


function full () {
extract($this->get_vars());
?>
<? if ($f_photo->get_size()) { ?><img src=<?= $f_photo_none ?> style="float: left;" alt="<?= $f_name_none ?>" title="<?= $f_name_none ?>" width="<?=  $f_photo->get_width() ?>" height="<?= $f_photo->get_height() ?>"/> <? } ?>
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


function record () {
extract($this->get_vars());
?>
<div class="fx_item">
  <?
  $group = "f_".$fx_visual['view'];
  $groupHeader = ( $this->m['group_by'] == $$group ? NULL : $this->m['group_by'] = $$group );
  echo ( $fx_visual['group'] && $groupHeader  ? "<div class='fx_divider'></div><div class='fx_group'>".$groupHeader."</div>" : "" ); 
  ?>
  <h3><a href="<?= $full_link ?>"><?= $f_name ?></a></h3>
  <? if ($f_job) { ?><span class="fx_key">Должность: </span><span class="fx_value"><?= $f_job ?></span><br><? } ?>
  <? if ($fx_visual['view'] == "dept" && $f_dept && !$fx_visual['group']) { ?><span class="fx_key">Отдел: </span><span class="fx_value"><?= $f_dept ?></span><br><? } ?>
  <? if ($fx_visual['view'] == "company" && $f_company && !$fx_visual['group']) { ?><span class="fx_key">Компания: </span><span class="fx_value"><?= $f_company ?></span><br><? } ?>
</div>
<?= (!($f_num%$col) ? "<div class='fx_divider'></div>" : "") ?>
<?php
}





}

<?php
class ctpl_resume_resumelist extends ctpl_resume_main {
function record () {
extract($this->get_vars());
?>
<div class="fx_row">
  <h2><?= $f_job ?></h2>
  <span class="fx_key">ФИО: </span><span class="fx_value"><?= $f_name ?></span><br>
  <span class="fx_key"><?= ( $fx_visual['date_view'] != "age" ? "Дата рождения: " : "Возраст: ") ?></span>
  <span class="fx_value"><?= ( $fx_visual['date_view'] == "age" ? date('Y', time())-$f_birthday->get_year() : $f_birthday->get_date().( $fx_visual['date_view'] == "date_age" ? " (".(date('Y', time())-$f_birthday->get_year()).")" : "") )?></span><br>
  <? if ($f_salary_from) { ?><span class="fx_key">Зарплата от: </span><span class="fx_value"><?= $f_salary_from?> <?= $f_currency ?></span><br><? } ?>
  <? if ($f_country || $f_city) { ?><span class="fx_key">Страна, город, район: </span><span class="fx_value"><?= $f_country.($f_city ? ", ".$f_city.($f_district ? ", ".$f_district : "") : "")?>  </span><br><? } ?>
</div>
<?php
}


function full () {
extract($this->get_vars());
?>
<span class="fx_key">ФИО: </span><span class="fx_value"><?= $f_name ?></span><br>
<span class="fx_key"><?= ( $fx_visual['date_view'] != "age" ? "Дата рождения: " : "Возраст: ") ?></span>
<span class="fx_value"><?= ( $fx_visual['date_view'] == "age" ? date('Y', time())-$f_birthday->get_year() : $f_birthday->get_date().( $fx_visual['date_view'] == "date_age" ? " (".(date('Y', time())-$f_birthday->get_year()).")" : "") )?></span><br>
<? if ($f_country || $f_city) { ?><span class="fx_key">Страна, город, район: </span><span class="fx_value"><?= $f_country.($f_city ? ", ".$f_city.($f_district ? ", ".$f_district : "") : "")?>  </span><br><? } ?>
<? if ($f_salary_from) { ?><span class="fx_key">Зарплата от: </span><span class="fx_value"><?= $f_salary_from?> <?= $f_currency ?></span><br><? } ?>
<span class="fx_key">Кратко о себе:</span>
<div><?= $f_text ?></div>
<?php
}


function title () {
extract($this->get_vars());
?>
<?= $f_job ?>
<?php
}


function h1 () {
extract($this->get_vars());
?>
<?= $f_job ?>
<?php
}


}

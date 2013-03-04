<?php
class ctpl_resume_main extends fx_tpl_component {












function record () {
extract($this->get_vars());
?>
<span class="fx_key">ФИО: </span><span class="value"><?= $f_name ?></span><br>
<span class="fx_key"><?= ( $fx_visual['date_view'] != "age" ? "Дата рождения: " : "Возраст: ") ?></span>
<span class="fx_value"><?= ( $fx_visual['date_view'] == "age" ? date('Y', time())-$f_birthday->get_year() : $f_birthday->get_date().( $fx_visual['date_view'] == "date_age" ? " (".(date('Y', time())-$f_birthday->get_year()).")" : "") )?></span><br>
<? if ($f_country || $f_city) { ?><span class="fx_key">Город, район: </span><span class="fx_value"><?= $f_city.($f_district ? ", ".$f_district : "")?>  </span><br><? } ?>
<span class="fx_key">Позиция / должность: </span><span class="fx_value"><?= $f_job ?></span><br>
<? if ($f_salary_from) { ?><span class="key">Зарплата от: </span><span class="fx_value"><?= $f_salary_from?> <?= $f_currency ?></span><br><? } ?>
<span class="fx_key">Кратко о себе:</span>
<div><?= $f_text ?></div>
<?=($f_experience->count() ? "<h2>Профессиональный опыт</h2>".$f_experience : "") ?>
<?=($f_education->count() ? "<h2>Образование</h2>".$f_education : "") ?>
<?=($f_links->count() ? "<h2>Ссылки на проекты</h2>".$f_links : "") ?>
<?=($f_contact->count() ? "<h2>Контакты</h2>".$f_contact : "") ?>
<?php
}


}

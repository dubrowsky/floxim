<?php
class ctpl_resumeexperience_main extends fx_tpl_component {



function record () {
extract($this->get_vars());
?>
<div class="fx_row">
<strong class="fx_marked">
  <?=$f_date_start->format("m.Y")?> - <?=($f_date_end->format() ? $f_date_end->format("m.Y") : "н.в." )?>, <?=($f_url ? "<a href='http://".$f_url."'>$f_company</a>" : $f_company)?>
</strong><br/>
<strong class="fx_marked"><?=$f_job?></strong><br/>
<?=$f_responsibilities?>
<?=($f_advice_contact ? "<p>Контакты для рекомендаций: ".$f_advice_contact."</p>" : "")?>
</div>
<?php
}


function settings_index () {
extract($this->get_vars());
$query_param['query_order'] = "a.date_start desc";
}


}

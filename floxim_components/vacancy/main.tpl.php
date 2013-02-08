<?php
class ctpl_vacancy_main extends fx_tpl_component {
function prefix () {
extract($this->get_vars());
?>
<? if($fx_visual['view'] == "layer") { ?>
<script>
$(document).ready(function(){
    $('.fx_ajax').click( function() {
        $(this).next('.fx_box').toggle();
    });
});
</script>
<? } ?>
<?php
}


function record () {
extract($this->get_vars());
?>
<div class="fx_row fx_row_list">
    <h3><a href='<?=$full_link?>'><?=$f_job?></a></h3>
    <?= ($fx_visual['view'] == "layer" ? "<div class='fx_box'>" : "") ?>
      <span class="fx_key">Оклад: </span><span class="fx_value">
        <?=( $f_pay_from ? "от ".$f_pay_from : "") ?> 
        <?=( $f_pay_to ? "до ".$f_pay_to : "") ?>
        <?=( !($f_pay_to && f_pay_from) ? "не указан" : "") ?>  </span><br>
      <span class="fx_value"><?=$f_term?></span><br>
      
      <div class="fx_value"><?=$f_requirement?></div>
    <?= ($fx_visual['view'] == "layer" ? "</div>" : "") ?>
</div>
<?php
}


function suffix () {
extract($this->get_vars());
?>
<?= ( $fx_tpl ? $fx_tpl->listing($fx_infoblock) : '') ?>
<?php
}


function full () {
extract($this->get_vars());
?>
<div class="fx_row_full">
<span class="fx_key">Оклад: </span><span class="fx_value">
  <?=( $f_pay_from ? "от ".$f_pay_from : "") ?> 
  <?=( $f_pay_to ? "до ".$f_pay_to : "") ?>
  <?=( !($f_pay_to && f_pay_from) ? "не указан" : "") ?>  </span><br>
<span class="fx_key">Условия: </span><span class="fx_value"><?=$f_term?></span><br>
<? if($f_address) { ?><span class="fx_key">Адрес: </span><span class="fx_value"><?=$f_address?></span><br> <? } ?>
<? if($f_phone) { ?><span class="fx_key">Телефон: </span><span class="fx_value"><?=$f_phone?></span><br><? } ?>
<? if($f_email) { ?><span class="fx_key">Email: </span><span class="fx_value"><?=$f_email?></span><br><? } ?>
<? if($f_contacts) { ?><span class="fx_key">Контактное лицо: </span><span class="fx_value"><?=$f_contacts?></span><br><? } ?>
<span class="fx_key">Требования: </span>    
  <span class="fx_value"><?=$f_requirement?></span><br />
<? if($f_contacts) { ?><span class="fx_key">Обязанности: </span>    
<span class="fx_value"><?=$f_respons?></span><? } ?>
</div>
<?php
}


function title () {
extract($this->get_vars());
?>
<?=$f_job?>
<?php
}


function h1 () {
extract($this->get_vars());
?>
<?=$f_job?>
<?php
}


}

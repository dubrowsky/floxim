<?php
class ctpl_companyshort_main extends fx_tpl_component {
function record () {
extract($this->get_vars());
?>
<div class="fx_row">
  <h3><?= ( $f_description ? "<a href=".$full_link.">".$f_name."</a>" : $f_name ) ?></h3>
  <? if ($f_logo->get_size()) { ?><img src="<?= $f_logo_none ?>" title="<?= $f_name_none ?>" alt="<?= $f_name_none ?>" width="<?= $f_logo->get_width() ?>" height="<?= $f_logo->get_height() ?>"/> <? } ?>
  <? if ($f_url) { ?><br><a href="<?= $f_url_none ?>" target="_blank"><?= $f_url ?></a> <? } ?>
  <? if ($f_brief) { ?><?= $f_brief ?><? } ?>
</div>
<?php
}


function full () {
extract($this->get_vars());
?>
<h3><?= $f_name ?></h3>
<? if ($f_logo->get_size()) { ?><img src="<?= $f_logo_none ?>" title="<?= $f_name_none ?>" alt="<?= $f_name_none ?>" width="<?= $f_logo->get_width() ?>" height="<?= $f_logo->get_height() ?>"/> <? } ?>
<? if ($f_url) { ?><br><a href="<?= $f_url_none ?>" target="_blank"><?= $f_url ?></a> <? } ?>
<?= $f_description ?>
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

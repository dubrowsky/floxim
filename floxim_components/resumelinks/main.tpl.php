<?php
class ctpl_resumelinks_main extends fx_tpl_component {
function record () {
extract($this->get_vars());
?>
<div class="fx_row">
<?=( $f_url ? "<a href='$f_url_none' target='_blank'>$f_title</a>" : $f_title)?>
<p><?=$f_description?></p>
</div>
<?php
}


function full () {
extract($this->get_vars());
?>
<span class="fx_key">Ссылка на проект:</span>
<span class="fx_value"><a href="<?= $f_url ?>" target="_blank"><?= $f_url ?></a></span>
<br>
<span class="fx_key">Описание проекта:</span>
<span class="fx_value"><?= $f_description ?></span>
<?php
}


function title () {
extract($this->get_vars());
?>
<?= $f_title ?>
<?php
}


function h1 () {
extract($this->get_vars());
?>
<?= $f_title ?>
<?php
}


}

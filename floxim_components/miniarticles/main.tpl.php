<?php
class ctpl_miniarticles_main extends fx_tpl_component {
function prefix () {
extract($this->get_vars());
?>
<? if ($fx_visual['view'] == "layer") { ?>

<script>
$(document).ready(function(){
    $('.fx_ajax').click( function() {
        $(this).next('.fx_box').toggle();
        return false;
    });
});
</script>
<? } ?>
<?php
}


function record () {
extract($this->get_vars());
?>
<div class="fx_row">
    <h3><a href="<?= $full_link ?>"<?=($fx_visual['view'] == "layer" ? " class='fx_ajax'" : NULL)?>><?= $f_title ?></a></h3>
    <div><?= $f_shorttext ?></div>
    <a href="<?= $full_link ?>"<?=($fx_visual['view'] == "layer" ? " class='fx_ajax'" : NULL)?>>Подробнее</a>
    <div class='fx_box'><?=$f_text?></div>
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
<?= $f_text ?>
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

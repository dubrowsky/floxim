<?php

class ctpl_newsblog_title extends ctpl_newsblog_main {

function record () {
extract($this->get_vars());
?>
<?= ($fx_visual['view'] == 'list' ? '<li>' : '') ?>
    <h3><a href="<?= $full_link ?>" ><?= $f_title ?></a></h3>
    <?= ( $fx_visual['show_date'] ? '<time>'.$f_created->get_date().'</time>' : '') ?>
<?= ($fx_visual['view'] == 'list' ? '</li>' : ( $fx_visual['view'] == 'br' && $f_num != $fx_total_rows ? '<br/>' : ', ' ) ) ?>
<?php
}

function prefix () {
extract($this->get_vars());
?>
<?= ($fx_visual['view'] == 'list' ? '<ul>' : '') ?>
<?php
}


function suffix () {
extract($this->get_vars());
?>
<?= ($fx_visual['view'] == 'list' ? '</ul>' : '') ?>
<?php
}


function full () {
extract($this->get_vars());
?>
<?= ( $fx_visual['date_place'] == 'after_title' && $fx_visual['group'] != 'day' ? '<time>'.$f_created->get_date().'</time>' : '') ?>
<?= ( $fx_visual['show_announce'] ? "<div".($fx_visual['indent_announce'] ? " style='padding-left:".$fx_visual['indent_announce']."'" : "").">".$f_announce."</div>"  : "" ) ?>
<div><?=$f_text?></div>
<?= ( $fx_visual['date_place'] == 'after_text' && $fx_visual['group'] != 'day'? '<time>'.$f_created->get_date().'</time>' : '') ?>
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
?>
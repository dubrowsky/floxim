<?php
class ctpl_newsblog_blog extends ctpl_newsblog_main {
function record () {
extract($this->get_vars());
?>
<?
  switch ($fx_visual['group']) {
    case 'year':
      $groupHeader = ( $this->m['curDep'] == $f_created->get_year() ? NULL : $this->m['curDep'] = $f_created->get_year() );
    break;
    case 'month':
      $groupHeader = ( $this->m['curDep'] == $f_created->format('m.Y') ? NULL : $this->m['curDep'] = $f_created->format('m.Y') );
    break;
    case 'day':
      $groupHeader = ( $this->m['curDep'] == $f_created->format('d M') ? NULL : $this->m['curDep'] = $f_created->format('d M') );
    break;
    default:
      $groupHeader = false;
    break;
  }
  echo (  $groupHeader  ? "<div class='fx_group'>".$groupHeader."</div>" : "" ); 
  ?>
<div class="fx_row">
  <h3><a href="<?= $full_link ?>" ><?= $f_title ?></a></h3>
  <?= ( $fx_visual['date_place'] == 'after_title' && $fx_visual['group'] != 'day' ? '<time>'.$f_created->get_date().'</time>' : '') ?>
  <div><?= $f_announce ?></div>
  <?= ( $fx_visual['date_place'] == 'after_text' && $fx_visual['group'] != 'day'? '<time>'.$f_created->get_date().'</time>' : '') ?>
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
<time><?=$f_created->get_date()?></time>
<?= ( $fx_visual['show_announce'] ? "<div>".$f_announce."</div>"  : "" ) ?>
<div><?=$f_text?></div>
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

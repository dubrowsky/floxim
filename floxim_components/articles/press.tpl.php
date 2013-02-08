<?php
class ctpl_articles_press extends ctpl_articles_main {
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
  echo ( $fx_visual['group']!='none' && $groupHeader  ? "<div class='fx_group'>".$groupHeader."</div>" : "" ); 
  ?>
<div class="fx_row">
  <h3><a href='<?= $full_link ?>'><?= $f_title ?></a></h3>
  <time><?= $f_created->get_date() ?></time>
  <?= ($f_note ? "<div>".$f_note."</div>" : "") ?>
</div>
<?php
}


function full () {
extract($this->get_vars());
?>
<time><?= $f_created->get_date() ?></time>
<?= ($f_text ? "<div>".$f_text."</div>" : "") ?>
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


function settings_index () {
extract($this->get_vars());
// группировка
if ($fx_visual['group'] != 'none') {
  $query_param['query_order'] = "a.created DESC";
}
}


}

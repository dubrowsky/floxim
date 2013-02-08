<?php
class ctpl_articles_main extends fx_tpl_component {
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
  <? 
  $width = $fx_visual['width'] ? $fx_visual['width'] : $f_pic->get_width();
  $height = $fx_visual['height'] ? $fx_visual['height'] : $f_pic->get_height();
  ?>  
  <?= ($f_pic->get_size() ? "<img src='".$f_pic->resize($width, $height)."'  width='".$width."' height='".$height."' style='".$border."' />" : NULL) ?>
  <?= ($f_note && $fx_visual['show_note'] ? "<div>".$f_note."</div>" : "") ?>
  <?= ($f_file->get_size() ? "<div><a href='".$f_file_none."' class='download'>Скачать файл</a></div>" : "") ?>
  <?= ($f_author ? "<br><span class='fx_key'>Автор: </span><span class='fx_value'>".$f_author."</span>" : "") ?>
  <?= ($f_created ? "<br><span class='fx_key'>Дата: </span><span class='fx_value'>".$f_created."</span>" : "") ?>
  <?= ($f_issue ? "<br><span class='fx_key'>Выпуск: </span><span class='fx_value'>".$f_issue."</span>" : "") ?>
</div>
<?php
}


function full () {
extract($this->get_vars());
?>
<? 
$width = $fx_visual['width'] ? $fx_visual['width'] : $f_pic->get_width();
$height = $fx_visual['height'] ? $fx_visual['height'] : $f_pic->get_height();
?>
<?= ($f_pic->get_size() ? "<img src='".$f_pic->resize($width, $height)."' width='".$width."' height='".$height."' style='".$border."' />" : NULL) ?>
<?= ($f_created ? "<br><span class='fx_key'>Дата: </span><span class='fx_value'>".$f_created."</span>" : "") ?>
<?= ($f_author ? "<br><span class='fx_key'>Автор: </span><span class='fx_value'>".$f_author."</span>" : "") ?>
<?= ($f_author_link ? "<br><span class='fx_key'>Сайт или email автора: </span><span class='fx_value'><a href='".$f_author_link_none."' target='_blank'>".$f_author_link."</a></span>" : "") ?>
<?= ($f_issue ? "<br><span class='fx_key'>Выпуск: </span><span class='fx_value'>".$f_issue."</span>" : "") ?>
<?= ($f_publisher ? "<br><span class='fx_key'>Название издания: </span><span class='fx_value'>".$f_publisher."</span>" : "") ?>
<?= ($f_file->get_size() ? "<div><a href='".$f_file."' class='download'>Скачать файл</a></div>" : "") ?>
<?= ($f_text ? "<div>".$f_text."</div>" : "") ?>
<?= ($f_source ? "<br><span class='fx_key'>Ссылка на оригинал: </span><span class='fx_value'><a href='".$f_source_none."' target='_blank'>".$f_source_none."</a></span>" : "") ?>
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
// рамка
$border_thickness = $fx_visual['border_thickness'] ? $fx_visual['border_thickness'] : 1;
$border_color = $fx_visual['border_color'] ? $fx_visual['border_color'] : 'gray';
$this->m['border'] = $fx_visual['border'] ? ' border: '.$border_thickness.'px solid '.$border_color.';' : '';
// группировка
if ($fx_visual['group'] != 'none') {
  $query_param['query_order'] = "a.created DESC";
}
}


function settings_full () {
extract($this->get_vars());
// рамка
$border_thickness = $fx_visual['border_thickness'] ? $fx_visual['border_thickness'] : 1;
$border_color = $fx_visual['border_color'] ? $fx_visual['border_color'] : 'gray';
$this->m['border'] = $fx_visual['border'] ? ' border: '.$border_thickness.'px solid '.$border_color.';' : '';
}


}

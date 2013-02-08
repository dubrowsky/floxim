<?php
class ctpl_articles_shortview extends ctpl_articles_main {
function record () {
extract($this->get_vars());
?>
<div class="fx_row">
  <h3><?= $f_title ?></h3>
  <?= ($f_created && $fx_visual['show_date'] ? "<span class='fx_key'>Дата: </span><span class='fx_value'>".$f_created->get_date()."</span>" : "") ?>
  <?= ($f_author && $fx_visual['show_author'] ? "<br><span class='fx_key'>Автор: </span><span class='fx_value'>".$f_author."</span>" : "") ?>
  <?= ($f_issue && $fx_visual['show_issue'] ? "<br><span class='fx_key'>Выпуск: </span><span class='fx_value'>".$f_issue."</span>" : "") ?>
  <?= ($f_publisher && $fx_visual['show_publisher'] ? "<br><span class='fx_key'>Название издания: </span><span class='fx_value'>".$f_publisher."</span>" : "") ?>
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


}

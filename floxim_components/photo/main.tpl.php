<?php

class ctpl_photo_main extends fx_tpl_component {

function settings_index () {
extract($this->get_vars());
// рамка
$this->m['border'] = '';
if ($fx_visual['border']) {
  $border_thickness = $fx_visual['border_thickness'] ? $fx_visual['border_thickness'] : 1;
  $border_color = $fx_visual['border_color'] ? $fx_visual['border_color'] : 'gray';
  $this->m['border'] = $fx_visual['border'] ? 'style="border: '.$border_thickness.'px solid '.$border_color.';"' : '';
}


$fx_core->page->add_file($fx_path_css.'colorbox.css');
$fx_core->page->add_file($fx_path_js.'jquery.colorbox-min.js');
}

function record () {
extract($this->get_vars());
?>
<div class="fx_item <?=$f_id_hash?>" >
<? 
$width = $fx_visual['width'] ? $fx_visual['width'] : $f_pic->get_width();
$height = $fx_visual['height'] ? $fx_visual['height'] : $f_pic->get_height();
$img = '<img src="'.$f_pic->resize($width, $height).'" alt="'.$f_caption_none.'" title="'.$f_caption_none.'" width="'.$width.'" height="'.$height.'" '.$border.' />'; 
?>
<?= ( $fx_visual['label'] == 'up' ? "<div class='fx_label'>".$f_caption."</div>" : '') ?>
<? if ($fx_visual['open'] == 'layer') {
  echo '<a href="'.$f_pic_none.'" rel="gallery">'.$img.'</a>';
}
else if ($fx_visual['open'] == 'full') {
    echo '<a href="'.$full_link.'" >'.$img.'</a>';
} 
else {
  echo $img;
}
?>
<?= ( $fx_visual['label'] == 'down' ? "<div class='fx_label'>".$f_caption."</div>" : '') ?>
</div>
<?php
}

function suffix () {
extract($this->get_vars());
?>
</div>
<? if ($fx_visual['open'] == 'layer') : ?>
<script type="text/javascript">
  $(document).ready(function(){ $("a[rel='gallery']").colorbox(); });
</script>
<? endif; ?>
<?php
}

function prefix () {
extract($this->get_vars());
?>
<div class="fx_items">
<?php
}


function full () {
extract($this->get_vars());
?>
<img src="<?=$f_pic?>" alt="'.$f_caption_none.'" title="'.$f_caption_none.'" />
<?php
}


function title () {
extract($this->get_vars());
?>
<?=$f_caption?>
<?php
}


function h1 () {
extract($this->get_vars());
?>
<?=$f_caption?>
<?php
}


}
?>
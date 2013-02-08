<?php
class ctpl_companyshort_companylogo extends ctpl_companyshort_main {
function record () {
extract($this->get_vars());
?>
<? 
$width = $fx_visual['image_width'] ? $fx_visual['image_width'] : $f_logo->get_width();
$height = $fx_visual['image_height'] ? $fx_visual['image_height'] : $f_logo->get_height();
?>
<div class="fx_item">
  <?= ( $fx_visual['caption'] == 'up' ? "<div class='fx_label'>".$f_name."</div>" : '') ?>
  <?= ($f_url ? "<a href='".$f_url_none."' target='_blank'>" : "") ?>
  <img src="<?= $f_logo->resize($width, $height) ?>" title="<?= $f_name_none ?>" alt="<?= $f_name_none ?>" width="<?= $width ?>" height="<?= $height ?>"/>
  <?= ($f_url ? "</a>" : "") ?>
  <?= ( $fx_visual['caption'] == 'down' ? "<div class='fx_label'>".$f_name."</div>" : '') ?>
</div>
<?= ($f_num%$col ? "<div class='fx_divider'></div>" : "") ?>
<?php
}





function prefix () {
extract($this->get_vars());
?>
<div class="fx_items">
<?php
}


function suffix () {
extract($this->get_vars());
?>
</div>
<?php
}


function settings_index () {
extract($this->get_vars());
$col = $this->m['col'] = $fx_visual['col'] ? $fx_visual['col'] : 1;
}


}

<?php
class ctpl_quotes_main extends fx_tpl_component {
function prefix () {
extract($this->get_vars());
?>
<div class="fx_items">
<?php
}


function record () {
extract($this->get_vars());
?>
<div class="fx_item">
  <blockquote<?= $style ?>><?= $f_quote ?></blockquote>
  <a href="<?= $f_link ?>" target="_blank"><?= $f_author ?></a>
</div>
<?= ( !($f_num%$col) ? "<div class='fx_divider'><div>" : "" ) ?>
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
// рамка
if ($fx_visual['border']) {
  $border_thickness = $this->m['border_thickness'] = $fx_visual['border_thickness'] ? $fx_visual['border_thickness'] : 1;
  $border_color = $this->m['border_color'] = $fx_visual['border_color'];
  $style = $this->m['style'] = "style='border: solid ".$border_thickness."px ".$border_color."'";
}
// колонки
$col = $this->m['col'] = $fx_visual['col'] ? $fx_visual['col'] : 1;
}


}

<?php
class ctpl_pricelist_main extends fx_tpl_component {
function prefix () {
extract($this->get_vars());
?>
<table class="goods_price">
<? if($fx_visual['show_caption'] || true) { ?>
<tr>
  <th>Название</th>
  <?= ($fx_visual['show_unit'] ? "<th>Единица измерения</th>" : "") ?>
  <th>Цена</th>
</tr>
<? } ?>
<?php
}


function record () {
extract($this->get_vars());
?>
<tr class='<?=$f_id_hash?>'>
  <td><a href="<?=$full_link?>"><?=$f_name?></a></td>
  <?= ($fx_visual['show_unit'] ? "<td>".$f_unit."</td>" : "") ?>
  <td><span class="fx_price"><?= $f_price ?>&nbsp;р.</span></td>
  <td><img src="<?= $f_image->resize(25, 25) ?>" style="height:25px;" /></td>
</tr>
<?php
}


function suffix () {
extract($this->get_vars());
?>
</table>
<?php
}


function full () {
extract($this->get_vars());
?>
<div class="full_good">
  <img style="float:left; margin:0 10px 10px 0;" src="<?=$f_image?>" alt="" />
  <div class="title">Продается <?=$f_name?></div>
  <div class="price">Цена: <b><?=$f_price?></b> рублей</div>
  <div class="text"><?=$f_description?></div>
</div>
<?php
}


function h1 () {
extract($this->get_vars());
?>
<?=$f_name?>
<?php
}


}

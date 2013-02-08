<?php
class ctpl_catalog_random extends ctpl_catalog_main {
function record () {
extract($this->get_vars());
?>
<div>
  <h4><?=$f_name?></h4>
  <?=($f_price ? "<b>Цена $f_price</b>" : "")?>
  <img src='<?=$f_picture_none?>' style='max-width:150px; max-height:150px; margin-right: 20px;' />
</div>
<?php
}


}

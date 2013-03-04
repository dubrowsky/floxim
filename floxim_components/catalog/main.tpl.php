<?php
class ctpl_catalog_main extends fx_tpl_component {
function record () {
extract($this->get_vars());
?>
<div>
  <img src='<?=$f_picture_none?>' style='float:left; max-width:150px; max-height:150px; margin-right: 20px;' />
  <h4><a href="<?=$full_link?>"><?=$f_name?></a></h4>
  <?=($f_price ? "<b>Цена $f_price</b>" : "")?>
<br/>
Комментарии: <?=$f_comm_count?><br/>
<?=($f_comm_count ? "Последний комментарий <i>$f_comm</i>" : "")?>
 <br style='clear:both;' />

</div>
<?php
}


function full () {
extract($this->get_vars());
?>
<div>
  <img src='<?=$f_picture_none?>' style='float:left; max-width:150px; max-height:150px; margin-right: 20px;' />
  <?=($f_price ? "<b>Цена $f_price</b>" : "")?>
<br/>
 <br style='clear:both;' />
<br/>
<?=( $f_comm_count ? "<h4>Комментарии:</h4>$f_comm" : "" )?>
<br/>

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

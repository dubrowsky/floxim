<?php
class ctpl_pricelist_salehits extends ctpl_pricelist_main {
function prefix () {
extract($this->get_vars());
?>
<div class="salehits">
<?php
}


function record () {
extract($this->get_vars());
?>
<div class="hit">
  <div class="title"><a href="<?=$full_link?>"><?=$f_name?></a></div>
  <div class="pic"><a href="<?=$full_link?>"><img src="<?=$f_image->resize(100, 100)?>" alt="" /></a></div>
  <div class="price">
    а цена &mdash; всего <b><?=$f_price?></b> р.!
  </div>
</div>
<?php
}


function suffix () {
extract($this->get_vars());
?>
</div>
<?php
}


}

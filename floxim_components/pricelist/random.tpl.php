<?php
class ctpl_pricelist_random extends ctpl_pricelist_main {
function record () {
extract($this->get_vars());
?>
<div class="random_good">
  <div class="title"><b><a href="<?=$full_link?>"><?=$f_name?></a></b></div>
  <div class="price">за <b><?=$f_price?></b> рублей</div>
  <a href="<?=$full_link?>"><img src="<?=$f_image->resize(70,70)?>" alt="" /></a>
</div>
<?php
}


}

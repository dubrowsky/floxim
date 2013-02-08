<?php
class ctpl_resumeeducation_main extends fx_tpl_component {
function record () {
extract($this->get_vars());
?>
<div class="fx_row">
  <strong class="fx_marked"><?=$f_name?></strong>, <?=$f_year_start?> - <?=($f_year_end ? $f_year_end : "н.в.")?><br/>
  <?=$f_department?>, <?=$f_degree?>
</div>
<?php
}


}

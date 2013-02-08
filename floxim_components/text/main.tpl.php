<?php
class ctpl_text_main extends fx_tpl_component {
function record () {
extract($this->get_vars());
?>
<div class="fx_row">
  <?=$f_text?>
</div>
<?php
}


}

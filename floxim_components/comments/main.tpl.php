<?php
class ctpl_comments_main extends fx_tpl_component {
function record () {
extract($this->get_vars());
?>
<div style='padding: 5px 0px;'>
<?=$f_text?>
</div>
<?php
}


}

<?php
class ctpl_comments_last extends ctpl_comments_main {
function record () {
extract($this->get_vars());
?>
<div style='padding: 5px 0px;'>
<?=$f_text?>
</div>
<?php
}


}

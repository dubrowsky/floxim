<?php
class ctpl_faq_main extends fx_tpl_component {
function prefix () {
extract($this->get_vars());
?>
<div>
  <span class="fx_ajax" id="ask_button">Задать вопрос</span>
</div>
<div id="ask" style="display:none;">
<?=$fx_infoblock->show_add()?>
</div>
<script type="text/javascript">
 $("#ask_button").click( function() {
    $("#ask").show();
    $(this).remove();
 });
</script>
<?php
}


function record () {
extract($this->get_vars());
?>
<div class="fx_row">
  <p><b>Вопрос:</b> <?=$f_question?></p>
  <?=( $f_answer ? "<p><b>Ответ:</b>$f_answer</p>" : "")?>
  <br/>
</div>
<?php
}


function settings_index () {
extract($this->get_vars());
if ( !$fx_user || !$fx_user->perm()->is_supervisor()  ) {
  if ( $fx_visual['with_answers'] ) {
    $query_param['query_where'] = "a.answer <> ''";
  }
}

}


}

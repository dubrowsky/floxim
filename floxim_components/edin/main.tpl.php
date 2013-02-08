<?php
class ctpl_edin_main extends fx_tpl_component {
function record () {
extract($this->get_vars());
?>
<?=$f_fieldstring?> 
<b><?=$f_fieldint?></b> <i><?=$f_fieldtext?></i>
<?=$f_fieldselect?> <?=$f_fieldwy?> 
<?=$f_fieldfloat?> лет и приехал туда приблизительно
<?=$f_fielddate?>. <br /><br />Также хорошие редакторы могут происходить из следующих регионов: <?=$f_fieldmul?>.
<br /><br />
Кому-нибудь может понадобиться цвет в RGB-формате: <?=$f_fieldcolor?>.
<?php
}


function begin_add_form () {
extract($this->get_vars());
?>
<b>Test</b>
<?php
}


}

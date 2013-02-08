<?php
class ctpl_catalog_pricelist extends ctpl_catalog_main {
function prefix () {
extract($this->get_vars());
?>
<table>
<tr>
  <th>Название</th>
  <th>Цена</th>
</tr>
<?php
}


function record () {
extract($this->get_vars());
?>
<tr class='<?=$f_id_hash?>'>
  <td><?=$f_name?></td>
  <td><?=$f_price?></td>
</tr>
<?php
}


function suffix () {
extract($this->get_vars());
?>
</table>
<?php
}


}

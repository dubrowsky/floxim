<?
class fx_template_component_text extends fx_template {
protected $_source_dir = "Z:/floxim/controllers/component/text";
protected $_template_code = "component_text";
    public function tpl_listing() {
        ?><div class="text">  
    <?
if ($this->get_var("input.items") instanceof Traversable) {
$item_index = 0;
$item_total = count($this->get_var("input.items"));

foreach ($this->get_var("input.items") as $item_key => $item) {
$item_index++;
$item_is_last = $item_total == $item_index;
$item_is_odd = $item_index % 2 != 0;
	if (is_array($item)) {
		extract($item);
	} elseif (is_object($item)) {
		extract($item instanceof fx_content ? $item->get_fields_to_show() : get_object_vars($item));
	}
?>
        <?
$f_text_tmp = null;
if (isset(${"f_text"})) {
	$f_text_tmp = ${"f_text"};
} else {
	$f_text_tmp = fx::dig($this->data, "f_text");
}
echo $f_text_tmp;
unset($f_text_tmp);

?>
    <?}
}
?>
</div><?
    }
protected $_templates = array (
  0 => 
  array (
    'id' => 'listing',
    'name' => 'listing',
    'for' => 'component_text.listing',
  ),
);
}
?>
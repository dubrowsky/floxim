<?
class fx_template_test extends fx_template {
protected $_source_dir = "Z:/floxim/controllers/other/test";
protected $_template_code = "test";
    public function tpl_show() {
        ?><div class="test_data">
Test template says: <b><?
${"test_data"."_tmp"} = null;
if (isset(${"test_data"})) {
	${"test_data"."_tmp"} = ${"test_data"};
} else {
	${"test_data"."_tmp"} = fx::dig($this->data, "test_data");
}
if (is_null(${"test_data"."_tmp"})) {
	ob_start();
	?>default data<?
	${"test_data"."_tmp"} = ob_get_clean();
	fx::dig_set($this->data, "test_data", ${"test_data"."_tmp"});
}
if (!(${"test_data"."_tmp"} instanceof fx_template_field)) {
	${"test_data"."_tmp"} = new fx_template_field(${"test_data"."_tmp"}, array("id" => "test_data", "var_type" => "visual", "infoblock_id" => fx::dig($this->data, "infoblock.id"), "template" => $this->_get_template_sign(),"editable" => true));
}

echo ${"test_data"."_tmp"};
unset(${"test_data"."_tmp"});

?></b>
</div><?
    }
    public function tpl_side() {
        ?><div class="side_test">
    <?
if ($this->get_var("input") instanceof Traversable) {
$item_index = 0;
$item_total = count($this->get_var("input"));

foreach ($this->get_var("input") as $item_key => $item) {
$item_index++;
$item_is_last = $item_total == $item_index;
$item_is_odd = $item_index % 2 != 0;
	if (is_array($item)) {
		extract($item);
	} elseif (is_object($item)) {
		extract($item instanceof fx_content ? $item->get_fields_to_show() : get_object_vars($item));
	}
?>
        <div class="test_q">
            <?
${"q$item_index"."_tmp"} = null;
if (isset(${"q$item_index"})) {
	${"q$item_index"."_tmp"} = ${"q$item_index"};
} else {
	${"q$item_index"."_tmp"} = fx::dig($this->data, "q$item_index");
}
if (is_null(${"q$item_index"."_tmp"})) {
	ob_start();
	?>q#<?=$item_index?><?
	${"q$item_index"."_tmp"} = ob_get_clean();
	fx::dig_set($this->data, "q$item_index", ${"q$item_index"."_tmp"});
}
if (!(${"q$item_index"."_tmp"} instanceof fx_template_field)) {
	${"q$item_index"."_tmp"} = new fx_template_field(${"q$item_index"."_tmp"}, array("id" => "q$item_index", "var_type" => "visual", "infoblock_id" => fx::dig($this->data, "infoblock.id"), "template" => $this->_get_template_sign(),"editable" => true));
}

echo ${"q$item_index"."_tmp"};
unset(${"q$item_index"."_tmp"});

?>:<br />
            <b><?=$item?></b>
        </div>
    <?}
}
?>
</div><?
    }
protected $_templates = array (
  0 => 
  array (
    'id' => 'show',
    'name' => 'show',
    'for' => 'other_test.show',
  ),
  1 => 
  array (
    'id' => 'side',
    'name' => 'side',
    'for' => 'other_test.side',
  ),
);
}
?>
<?
class fx_template_component_section extends fx_template {
protected $_source_dir = "Z:/floxim/controllers/component/section";
protected $_template_code = "component_section";
    public function tpl_listing() {
        ?>
    
<div class="menu">

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
            
<div class="menu_item" 
                 <?if ($item_is_odd) {
?>
                     style="
                        background:<?
$odd_bg_tmp = null;
if (isset(${"odd_bg"})) {
	$odd_bg_tmp = ${"odd_bg"};
} else {
	$odd_bg_tmp = fx::dig($this->data, "odd_bg");
}
if (is_null($odd_bg_tmp)) {
	ob_start();
	?>#C00<?
	$odd_bg_tmp = ob_get_clean();
	fx::dig_set($this->data, "odd_bg", $odd_bg_tmp);
}
if (!($odd_bg_tmp instanceof fx_template_field)) {
	$odd_bg_tmp = new fx_template_field($odd_bg_tmp, array("id" => "odd_bg", "var_type" => "visual", "infoblock_id" => fx::dig($this->data, "infoblock.id"), "template" => $this->_get_template_sign(),"editable" => true));
}

echo $odd_bg_tmp;
unset($odd_bg_tmp);

?>; 
                        color:<?
$odd_color_tmp = null;
if (isset(${"odd_color"})) {
	$odd_color_tmp = ${"odd_color"};
} else {
	$odd_color_tmp = fx::dig($this->data, "odd_color");
}
if (is_null($odd_color_tmp)) {
	ob_start();
	?>#FF0<?
	$odd_color_tmp = ob_get_clean();
	fx::dig_set($this->data, "odd_color", $odd_color_tmp);
}
if (!($odd_color_tmp instanceof fx_template_field)) {
	$odd_color_tmp = new fx_template_field($odd_color_tmp, array("id" => "odd_color", "var_type" => "visual", "infoblock_id" => fx::dig($this->data, "infoblock.id"), "template" => $this->_get_template_sign(),"editable" => true));
}

echo $odd_color_tmp;
unset($odd_color_tmp);

?>;"
                 <?
}
?>>
<a href="<?
$f_url_tmp = null;
if (isset(${"f_url"})) {
	$f_url_tmp = ${"f_url"};
} else {
	$f_url_tmp = fx::dig($this->data, "f_url");
}
echo $f_url_tmp;
unset($f_url_tmp);

?>">
<?
$f_name_tmp = null;
if (isset(${"f_name"})) {
	$f_name_tmp = ${"f_name"};
} else {
	$f_name_tmp = fx::dig($this->data, "f_name");
}
if (is_null($f_name_tmp)) {
	ob_start();
	?>
Раздел
<?
	$f_name_tmp = ob_get_clean();
	fx::dig_set($this->data, "f_name", $f_name_tmp);
}

echo $f_name_tmp;
unset($f_name_tmp);

?>
</a>
<?if (!$item_is_last) {
?>
<span>
<?
$separator_tmp = null;
if (isset(${"separator"})) {
	$separator_tmp = ${"separator"};
} else {
	$separator_tmp = fx::dig($this->data, "separator");
}
if (is_null($separator_tmp)) {
	ob_start();
	?>
&bull;
<?
	$separator_tmp = ob_get_clean();
	fx::dig_set($this->data, "separator", $separator_tmp);
}
if (!($separator_tmp instanceof fx_template_field)) {
	$separator_tmp = new fx_template_field($separator_tmp, array("id" => "separator", "var_type" => "visual", "infoblock_id" => fx::dig($this->data, "infoblock.id"), "template" => $this->_get_template_sign(),"editable" => true));
}

echo $separator_tmp;
unset($separator_tmp);

?>
</span>
<?
}
?>
</div>

        <?}
}
?>
    
</div>

<?
    }
    public function tpl_() {
        ?><?
    }
protected $_templates = array (
  0 => 
  array (
    'id' => 'listing',
    'name' => 'listing',
    'for' => 'component_section.listing',
  ),
  1 => 
  array (
    'id' => NULL,
    'name' => NULL,
    'for' => 'component_section.',
  ),
);
}
?>
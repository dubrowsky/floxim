<?
class fx_template_component_section extends fx_template {
protected $_source_dir = "Z:/home/floxim/www/controllers/component/section";
protected $_template_code = "component_section";
    public function tpl_listing() {
        ?>
    <div class="menu">
        <?
            $arr = array('nums' => '123');
            $test = 'woo';
        ?>
        [<?
if (isset($arr)) {
echo fx::dig($arr, "nums");
}
?><?=$this->show_var("arr.nums")?>, <?
if (isset($test)) {
echo $test;
}
?><?=$this->show_var("test")?>]
        <?
            foreach ($this->get_var('input') as $item) {
                extract($item->get_fields_to_show());
                dev_log('vars in tpl', get_defined_vars(), $item->get_page());
                ?>
                <div class="menu_item">
                    <a title="<?=$f_name?>" href="<?=$item->get_page()->get_field_to_show('url')?>"><?=$f_name?></a>
                </div>
                <?
            }
        ?>
        
    </div>
<?
    }
protected $_templates = array (
  0 => 
  array (
    'id' => 'listing',
    'vars' => 
    array (
      0 => 'arr.nums',
      1 => 'test',
    ),
    'name' => 'listing',
    'for' => 'component.section.listing',
  ),
);
}
?>
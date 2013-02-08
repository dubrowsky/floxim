<?
class fx_template_test extends fx_template {
protected $_source_dir = "Z:/floxim/controllers/other/test";
protected $_template_code = "test";
    public function tpl_show() {
        ?><div class="test_data">
Test template says: <b><?
if (isset($test_data)) {
echo $test_data;
}
?><?ob_start();?><?$this->set_var_default(
                        "test_data", 
                        ob_get_clean());?><?=$this->show_var("test_data")?></b>
</div><?
    }
    public function tpl_side() {
        ?><div class="side_test">
    <?
    foreach ($this->get_var('input') as $i => $q){
        ?>
        <div class="test_q">
        <?
if (isset($q$i)) {
echo $q$i;
}
?><?ob_start();?><?$this->set_var_default(
                        "q$i", 
                        ob_get_clean());?><?=$this->show_var("q$i")?>:<br />
        <b><?=$q?></b>
        </div>
        <?
    }
    ?>
</div><?
    }
protected $_templates = array (
  0 => 
  array (
    'id' => 'show',
    'vars' => 
    array (
      0 => 'test_data',
    ),
    'name' => 'show',
    'for' => 'other.test.show',
  ),
  1 => 
  array (
    'id' => 'side',
    'vars' => 
    array (
      0 => 'q$i',
    ),
    'name' => 'side',
    'for' => 'other.test.side',
  ),
);
}
?>
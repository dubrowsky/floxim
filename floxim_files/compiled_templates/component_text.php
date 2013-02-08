<?
class fx_template_component_text extends fx_template {
protected $_source_dir = "Z:/home/floxim/www/controllers/component/text";
protected $_template_code = "component_text";
    public function tpl_listing() {
        ?><div class="text">  
    <?foreach ($this->get_var('input') as $item) {?>
        <?
$tpl_to_call = fx::template("component_text");
$tpl_to_call->set_var(
                'record', 
                $item);
$tpl_to_call->set_var_meta(
                'record', 
                array('var_type' => 'param'));
echo $tpl_to_call->render("record", $this->data);?>
        <hr />
        <?=$this->render('record', array('record' => $item));?>
    <?}?>
</div>

<?
    }
    public function tpl_record() {
        ?>
    Meta: <?=$this->get_var_meta('record')?>;<br />
    Record: <?=$this->show_var("record.text")?>
<?
    }
protected $_templates = array (
  0 => 
  array (
    'id' => 'listing',
    'calls' => 
    array (
      0 => 'record',
    ),
    'name' => 'listing',
    'for' => 'component.text.listing',
  ),
  1 => 
  array (
    'id' => 'record',
    'vars' => 
    array (
      0 => 'record.text',
    ),
    'name' => 'record',
    'for' => 'component.text.record',
  ),
);
}
?>
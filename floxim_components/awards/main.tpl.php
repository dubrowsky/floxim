<?php
class ctpl_awards_main extends fx_tpl_component {
function prefix () {
extract($this->get_vars());
?>
<div class="fx_items">
<?php
}


function record () {
extract($this->get_vars());
?>
<? 
$width = $fx_visual['width'] ? $fx_visual['width'] : $f_pic->get_width();
$height = $fx_visual['height'] ? $fx_visual['height'] : $f_pic->get_height();

$groupHeader = ( $this->m['curDep'] == $f_year->get_year() ? NULL : $this->m['curDep'] = $f_year->get_year() );
echo ( $fx_visual['group']!='none' && $groupHeader  ? "<div class='fx_divider'></div><div class='fx_group'>".$groupHeader."</div>" : "" ); 
?>

<div class="fx_item">
    <?= ( $fx_visual['label'] == 'up' ? "<div class='fx_label'>".$f_caption."</div>" : '') ?>
    <img src="<?= $f_pic->resize($width, $height) ?>" alt="<?= $f_caption_none ?>" title="<?= $f_caption_none ?>"   width="<?= $width ?>" height="<?= $height?>" />
    <?= ( $fx_visual['label'] == 'down' ? "<div class='fx_label'>".$f_caption."</div>" : '') ?>
</div>
<?= ( !$f_num%$col ? "<div class='fx_divider'></div>" : "" ) ?>
<?php
}


function suffix () {
extract($this->get_vars());
?>
</div>
<?php
}


function settings_index () {
extract($this->get_vars());
// кол-во колонок
$this->m['col']  = $fx_visual['col'] ? $fx_visual['col'] : 1;

// группировка
if ($fx_visual['group'] != 'none') {
  $query_param['query_order'] = "a.year ".($fx_visual['group'] == 'up' ? "DESC" : "ASC");
}
}


}

<?php
class ctpl_photo_random extends ctpl_photo_main {
function prefix () {
extract($this->get_vars());
?>
<div class="fx_items">
<?php
}


function suffix () {
extract($this->get_vars());
?>
</div>
<?php
}


function record () {
extract($this->get_vars());
?>
<div class="item">
<img  src="<?=$f_pic->resize($width,$height,1)?>" title="<?=$f_caption_none?>" alt="<?=$f_caption_none?>"  />
</div>
<?php
}


function settings_index () {
extract($this->get_vars());
$this->m['width'] = $fx_visual['width'] ? $fx_visual['width'] : 150;
$this->m['height'] = $fx_visual['height'] ? $fx_visual['height'] : 150;
}


}

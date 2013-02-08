<?php
class ctpl_photo_slider extends ctpl_photo_main {
function settings_index () {
extract($this->get_vars());
$fx_core->page->add_file($fx_path_css.'themes/default/default.css');
$fx_core->page->add_file($fx_path_css.'nivo-slider.css');
$fx_core->page->add_file($fx_path_js.'jquery.nivo.slider.pack.js');

$this->m['width'] = $fx_visual['width'] ? $fx_visual['width'] : 150;
$this->m['height'] = $fx_visual['height'] ? $fx_visual['height'] : 150;
}


function prefix () {
extract($this->get_vars());
?>
<style>
  .nivoSlider {
    width:<?=$width?>px; /* Change this to your images width */
    height:<?=$height?>px;
}
</style>
<div class="slider-wrapper theme-default">
  <div class="ribbon"></div>
            <div id="slider" class="nivoSlider">
<?php
}


function record () {
extract($this->get_vars());
?>
<img  src="<?=$f_pic->resize($width,$height,1)?>" title="<?=$f_caption_none?>" alt="<?=$f_caption_none?>"  />
<?php
}


function suffix () {
extract($this->get_vars());
?>
</div>
</div>

<script type="text/javascript">
    $(window).load(function() {
        $('#slider').nivoSlider({
           pauseTime: <?=( $fx_visual['pause'] ? $fx_visual['pause'] : 3)*1000?>,
           controlNav: <?=+$fx_visual['control_nav']?>, 
        });
    });
    </script>
<?php
}


}

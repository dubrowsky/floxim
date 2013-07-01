<?php
class template__demo8__index extends fx_tpl_template {
 public function write () {
    extract( $this->get_vars() ); ?>
<?=$fx_tpl_inner->_header($this->get_vars());?>
<!--блок слева-->
<?=$fx_tpl_inner->_sidebar($this->get_vars());?>
<!--//блок слева-->
<!--content, если есть блок слева то добавляется class="is_left"-->
	<div id="content" class="is_left">
    <?=$fx_tpl_inner->_content($this->get_vars());?>
	</div>
<!--//content-->
	<div class="sep"></div>

<!--логотипы компаний, партнеры, клиенты и т.п.-->
	<div id="partners">
            <?=$fx_layout->place_infoblock('bottom', 'a:2:{s:6:"blocks";a:1:{i:0;a:2:{s:4:"name";N;s:8:"template";s:12:"%FX_CONTENT%";}}s:9:"one_block";i:1;}' )?>
        <div class="sep"></div>         
	</div>
<!--//логотипы компаний, партнеры, клиенты и т.п.-->

</div></div>
<!--footer-->
<?=$fx_tpl_inner->_footer($this->get_vars());?>
<?php }
}
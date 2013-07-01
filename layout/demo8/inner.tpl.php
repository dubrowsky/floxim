<?php
class template__demo8__inner extends fx_tpl_template {
public function _header ( $vars = array() ) {
    extract( $vars );?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta name="keywords" content="" />
<meta name="description" content="" />



</head>

<body>

<div id="main"><div class="content">
<!--header-->
	<div id="header">
	<!--логотип, слоган-->
		<div id="logo">
			<?=$fx_layout->place_infoblock_simple('infoblock0', 'a:4:{s:8:"template";s:163:"<a  href = "%FX_REPLACE_0%"  keyword = "logo" ><img  src = "%FX_REPLACE_1%"  width = "103"  height = "75"  alt = "%FX_REPLACE_2%"  title = "%FX_REPLACE_3%"  /></a>";s:6:"params";a:4:{i:0;a:2:{s:7:"default";s:1:"/";s:4:"name";s:4:"href";}i:1;a:3:{s:7:"default";s:19:"css/images/logo.gif";s:4:"name";s:3:"src";s:4:"type";s:5:"image";}i:2;a:2:{s:7:"default";s:0:"";s:4:"name";s:3:"alt";}i:3;a:2:{s:7:"default";s:0:"";s:4:"name";s:5:"title";}}s:9:"one_block";i:1;s:6:"simple";i:1;}' )?>
			<?=$fx_layout->place_infoblock_simple('company_name', 'a:4:{s:8:"template";s:14:"%FX_REPLACE_0%";s:6:"params";a:1:{i:0;a:2:{s:4:"name";s:8:"Parametr";s:7:"default";s:47:"<span class="name">YOUR<span>LOGO</span></span>";}}s:9:"one_block";i:1;s:6:"simple";i:1;}' )?>
			<?=$fx_layout->place_infoblock_simple('company_slogan', 'a:4:{s:8:"template";s:14:"%FX_REPLACE_0%";s:6:"params";a:1:{i:0;a:2:{s:4:"name";s:8:"Parametr";s:7:"default";s:34:"<span>Sampletext under logo</span>";}}s:9:"one_block";i:1;s:6:"simple";i:1;}' )?>
		</div>
	<!--//логотип, слоган-->
	<!--поиск-->
		<div class="search">
			<?=$fx_layout->place_infoblock('right_up', 'a:4:{s:6:"blocks";a:1:{i:0;a:2:{s:4:"name";N;s:8:"template";s:12:"%FX_CONTENT%";}}s:10:"max_repeat";s:1:"3";s:5:"embed";s:9:"miniblock";s:9:"one_block";i:1;}' )?>
		</div>
	<!--//поиск-->
		<div class="sep"></div>
	<!--menu, отступы внутри пунктов меню сделаны через padding, что не совсем правильно, если мало или наоборот много пунктов меню, выглядеть будет не очень, такое меню надо делать на таблице-->
		<div id="menu"><div class="left"><div class="right">
                <?=$fx_layout->place_menu('main', 'a:2:{s:7:"keyword";s:4:"main";s:9:"necessary";s:3:"yes";}', 'a:1:{i:0;a:4:{s:6:"prefix";s:4:"<ul>";s:6:"active";s:55:"<li><a href="%url%" class="menu-active">%name%</a></li>";s:8:"unactive";s:35:"<li><a href="%url%">%name%</a></li>";s:6:"suffix";s:5:"</ul>";}}')?>
		</div></div></div>
	<!--//menu-->
	<!--верхний баннер, фоновое изображение меняется через background в general.css-->
		<div id="banner_top">
                    <?=$fx_layout->place_infoblock_simple('infoblock1', 'a:4:{s:8:"template";s:113:"<img  src = "%FX_REPLACE_0%"  width = "834"  height = "360"  alt = "%FX_REPLACE_1%"  title = "%FX_REPLACE_2%"  />";s:6:"params";a:3:{i:0;a:3:{s:7:"default";s:25:"css/images/top_banner.jpg";s:4:"name";s:3:"src";s:4:"type";s:5:"image";}i:1;a:2:{s:7:"default";s:0:"";s:4:"name";s:3:"alt";}i:2;a:2:{s:7:"default";s:0:"";s:4:"name";s:5:"title";}}s:9:"one_block";i:1;s:6:"simple";i:1;}' )?>
		</div>
	<!--//верхний баннер-->
	</div>
<!--//header-->

<?php }
public function _sidebar ( $vars = array() ) {
    extract( $vars );?>

	<div id="left_content">
        <?=$fx_layout->place_menu('submenu', 'a:5:{s:7:"keyword";s:7:"submenu";s:9:"necessary";s:3:"yes";s:4:"kind";s:21:"independent,dependent";s:6:"direct";s:8:"vertical";s:6:"parent";s:4:"main";}', 'a:1:{i:0;a:4:{s:6:"prefix";s:25:"<div class="submenu"><ul>";s:6:"active";s:55:"<li><a href="%url%" class="menu-active">%name%</a></li>";s:8:"unactive";s:35:"<li><a href="%url%">%name%</a></li>";s:6:"suffix";s:11:"</ul></div>";}}')?>
	</div>
    
<?php }
public function _content ( $vars = array() ) {
    extract( $vars );?>

		<?=$fx_core->page->get_h1()?>
        <?=$fx_layout->place_infoblock('main_content', 'a:3:{s:6:"blocks";a:4:{i:0;a:2:{s:4:"name";s:12:"Пустой";s:8:"template";s:37:"<div class="block">%FX_CONTENT%</div>";}i:1;a:3:{s:4:"name";s:27:"C заголовком (h2)";s:6:"params";a:1:{i:0;a:3:{s:7:"default";s:38:"Заголовок раздела (h2)";s:4:"type";s:6:"string";s:4:"name";s:18:"Заголовок";}}s:8:"template";s:119:"<div class="block">                    <h2>%FX_REPLACE_0%</h2>                    <p>%FX_CONTENT%</p>            </div>";}i:2;a:3:{s:4:"name";s:73:"Список статей по горизонтали в 2 колонки";s:6:"params";a:1:{i:0;a:3:{s:7:"default";s:74:"Наши преимущества в отличии от других (h4)";s:4:"type";s:6:"string";s:4:"name";s:18:"Заголовок";}}s:8:"template";s:189:"<div class="block columns_2">                    <div class="col">                    <h4>%FX_REPLACE_0%</h4>                    %FX_CONTENT%                    </div>                </div>";}i:3;a:3:{s:4:"name";s:27:"C заголовком (h3)";s:6:"params";a:1:{i:0;a:3:{s:7:"default";s:38:"Заголовок раздела (h3)";s:4:"type";s:6:"string";s:4:"name";s:18:"Заголовок";}}s:8:"template";s:58:"<h3>%FX_REPLACE_0%</h3>                <p>%FX_CONTENT%</p>";}}s:7:"divider";s:23:"<div class="sep"></div>";s:4:"main";s:3:"yes";}' )?>
    
<?php }
public function _footer ( $vars = array() ) {
    extract( $vars );?>

	<div id="footer">
		<div class="left"><?=$fx_layout->place_infoblock_simple('copyright', 'a:4:{s:8:"template";s:14:"%FX_REPLACE_0%";s:6:"params";a:1:{i:0;a:2:{s:4:"name";s:8:"Parametr";s:7:"default";s:104:"&copy; 2010 группа компаний &laquo;Netcat&raquo;.<br />Все права защищены.";}}s:9:"one_block";i:1;s:6:"simple";i:1;}' )?></div>
		<div class="right"><?=$fx_layout->place_infoblock_simple('developer', 'a:4:{s:8:"template";s:14:"%FX_REPLACE_0%";s:6:"params";a:1:{i:0;a:2:{s:4:"name";s:8:"Parametr";s:7:"default";s:107:"&copy; 2010 Хороший пример <br />сайтостроения &mdash; <a href="#">WebSite.pu</a>";}}s:9:"one_block";i:1;s:6:"simple";i:1;}' )?></div>
		<div class="sep"></div>
	</div>
<!--//footer-->
</body>
</html>

<?php }
 public function write () {
    extract( $this->get_vars() ); ?>
<?=$this->_header( $this->get_vars() );?>
<!--блок слева-->
	
    <?=$this->_sidebar( $this->get_vars() );?>
	
<!--//блок слева-->
<!--content, если есть блок слева то добавляется class="is_left"-->
	<div id="content" class="is_left">
    <?=$this->_content( $this->get_vars() );?>
	</div>
<!--//content-->
	<div class="sep"></div>

</div></div>
<!--footer-->
<?=$this->_footer( $this->get_vars() );?>
<?php }
}
<?
class fx_template_layout_supernova extends fx_template {
protected $_source_dir = "Z:/floxim/controllers/layout/supernova";
protected $_template_code = "layout_supernova";
    public function tpl_index() {
        ?><?
$tpl_to_call = fx::template("layout_supernova");
ob_start();
?>
    <div class="index_data">
        <?=$this->render_area("content")?>  
    </div>
<?
$tpl_to_call->set_var(
                'content', 
                ob_get_clean());
echo $tpl_to_call->render("wrap", $this->data);?><?
    }
    public function tpl_inner() {
        ?><?
$tpl_to_call = fx::template("layout_supernova");
ob_start();
?>
	<div>
	<?=$this->render_area("content")?>
	</div>
<?
$tpl_to_call->set_var(
                'content', 
                ob_get_clean());
echo $tpl_to_call->render("wrap", $this->data);?><?
    }
    public function tpl_wrap() {
        ?>
<!DOCTYPE html>
<html>
<head>
<title>
My Super Template
</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</meta>
<body>
<div id="main">
<!--верхняя цветная полоса-->
<div class='color_line'>
<div>
<div>
<div>
<div>
<div>
<div>
<div>
<div>
<div>
<div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<!--//верхняя цветная полоса--><!--шапка сайта, логотип, поиск, меню, языковая версия-->
<div id="header">
<!--логотип, слоган-->
<div id="logo">
<a href="/">
<img src="<?
$replace_src_0_tmp = null;
if (isset(${"replace_src_0"})) {
	$replace_src_0_tmp = ${"replace_src_0"};
} else {
	$replace_src_0_tmp = fx::dig($this->data, "replace_src_0");
}
if (is_null($replace_src_0_tmp)) {
	ob_start();
	?>/controllers/layout/supernova/images/logo.gif<?
	$replace_src_0_tmp = ob_get_clean();
	fx::dig_set($this->data, "replace_src_0", $replace_src_0_tmp);
}
if (!(fx::env("is_admin") && $replace_src_0_tmp instanceof fx_template_field)) {
	$replace_src_0_tmp = new fx_template_field($replace_src_0_tmp, array("id" => "replace_src_0", "var_type" => "visual", "infoblock_id" => fx::dig($this->data, "infoblock.id"), "template" => $this->_get_template_sign(), "title" => "Картинка", "editable" => true));
}

echo $replace_src_0_tmp;
unset($replace_src_0_tmp);

?>" fx_replace="src" />
</a>
<div>
<?
$slogan_tmp = null;
if (isset(${"slogan"})) {
	$slogan_tmp = ${"slogan"};
} else {
	$slogan_tmp = fx::dig($this->data, "slogan");
}
if (is_null($slogan_tmp)) {
	ob_start();
	?>
название или слоган 
<br />
вашей компании
<?
	$slogan_tmp = ob_get_clean();
	fx::dig_set($this->data, "slogan", $slogan_tmp);
}
if (!(fx::env("is_admin") && $slogan_tmp instanceof fx_template_field)) {
	$slogan_tmp = new fx_template_field($slogan_tmp, array("id" => "slogan", "var_type" => "visual", "infoblock_id" => fx::dig($this->data, "infoblock.id"), "template" => $this->_get_template_sign(), "editable" => true));
}

echo $slogan_tmp;
unset($slogan_tmp);

?>
</div>
</div>
<!--//логотип, слоган-->
<div id="header_nav">
<!--//поиск-->
<div class="sep">
</div>
<!--горизонтальное меню-->
			<?=$this->render_area("header")?>
			

<!--//горизонтальное меню-->
</div>
<div class="sep">
</div>
</div>
<!--//шапка сайта, логотип, поиск, меню, языковая версия--><!--центральная часть, контентная область и правый вспомогательный блок-->
<div id="center">
<!--контент-->
<div id="content">

			<?
$content_tmp = null;
if (isset(${"content"})) {
	$content_tmp = ${"content"};
} else {
	$content_tmp = fx::dig($this->data, "content");
}
echo $content_tmp;
unset($content_tmp);

?>
			
			
			<!--//заголовок-->
</div>
<!--//контент--><!--правый блок-->
<div id="right_content">
<?=$this->render_area("sidebar")?>
<!--вертикальное меню-->

<!--//вертикальное меню-->
</div>
<!--//правый блок-->
</div>
<div class="sep">
</div>
<!--//центральная часть, контентная область и правый вспомогательный блок--><!--баннеры-->
<div id="banners">
<div class="sep">
</div>
</div>
<!--//баннеры-->
<div id="footer">
<div class="left">
<?
$copy_tmp = null;
if (isset(${"copy"})) {
	$copy_tmp = ${"copy"};
} else {
	$copy_tmp = fx::dig($this->data, "copy");
}
if (is_null($copy_tmp)) {
	ob_start();
	?>
© 2010 группа компаний «Netcat».
<br />
Все права защищены.
<?
	$copy_tmp = ob_get_clean();
	fx::dig_set($this->data, "copy", $copy_tmp);
}
if (!(fx::env("is_admin") && $copy_tmp instanceof fx_template_field)) {
	$copy_tmp = new fx_template_field($copy_tmp, array("id" => "copy", "var_type" => "visual", "infoblock_id" => fx::dig($this->data, "infoblock.id"), "template" => $this->_get_template_sign(), "editable" => true));
}

echo $copy_tmp;
unset($copy_tmp);

?>
</div>
<div class="middle">
<?
$contacts_tmp = null;
if (isset(${"contacts"})) {
	$contacts_tmp = ${"contacts"};
} else {
	$contacts_tmp = fx::dig($this->data, "contacts");
}
if (is_null($contacts_tmp)) {
	ob_start();
	?>
Адрес: г. Москва, ул. Мануфактурная, д. 14
<br />
Телефон и факс: (831) 220-80-18
<?
	$contacts_tmp = ob_get_clean();
	fx::dig_set($this->data, "contacts", $contacts_tmp);
}
if (!(fx::env("is_admin") && $contacts_tmp instanceof fx_template_field)) {
	$contacts_tmp = new fx_template_field($contacts_tmp, array("id" => "contacts", "var_type" => "visual", "infoblock_id" => fx::dig($this->data, "infoblock.id"), "template" => $this->_get_template_sign(), "editable" => true));
}

echo $contacts_tmp;
unset($contacts_tmp);

?>
</div>
<div class="right">
<?
$developa_tmp = null;
if (isset(${"developa"})) {
	$developa_tmp = ${"developa"};
} else {
	$developa_tmp = fx::dig($this->data, "developa");
}
if (is_null($developa_tmp)) {
	ob_start();
	?>
© 2010 Хороший пример 
<br />
сайтостроения — 
<a href="#">
WebSite.pu
</a>
<?
	$developa_tmp = ob_get_clean();
	fx::dig_set($this->data, "developa", $developa_tmp);
}
if (!(fx::env("is_admin") && $developa_tmp instanceof fx_template_field)) {
	$developa_tmp = new fx_template_field($developa_tmp, array("id" => "developa", "var_type" => "visual", "infoblock_id" => fx::dig($this->data, "infoblock.id"), "template" => $this->_get_template_sign(), "editable" => true));
}

echo $developa_tmp;
unset($developa_tmp);

?>
</div>
<div class="sep">
</div>
</div>
<!--нижняя цветная полоса-->
<div class='color_line'>
<div>
<div>
<div>
<div>
<div>
<div>
<div>
<div>
<div>
<div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<!--//нижняя цветная полоса-->
</div>
</body>
</head>

<?
    }
    public function tpl_demo_menu() {
        ?>
<div id="menu">
<ul>
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
	if (fx::env('is_admin') && ($item instanceof fx_essence) ) {
		ob_start();
	}
?>
<li>
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
<span class="mw">
<span class="mw">
<?
$f_name_tmp = null;
if (isset(${"f_name"})) {
	$f_name_tmp = ${"f_name"};
} else {
	$f_name_tmp = fx::dig($this->data, "f_name");
}
echo $f_name_tmp;
unset($f_name_tmp);

?>
</span>
</span>
</a>
</li>
<?	if (fx::env('is_admin') && ($item instanceof fx_essence) ) {
		echo $item->add_template_record_meta(ob_get_clean());
	}
}
}
?>
</ul>
</div>
<?
    }
    public function tpl_wrap_simple() {
        ?>
				
<div class="block">

					<?
$content_tmp = null;
if (isset(${"content"})) {
	$content_tmp = ${"content"};
} else {
	$content_tmp = fx::dig($this->data, "content");
}
echo $content_tmp;
unset($content_tmp);

?>
				
</div>

			<?
    }
    public function tpl_wrap_titled() {
        ?>
				
<div class="block" >
<div class="title" >
<h1 style="color:<?
$color_tmp = null;
if (isset(${"color"})) {
	$color_tmp = ${"color"};
} else {
	$color_tmp = fx::dig($this->data, "color");
}
if (is_null($color_tmp)) {
	ob_start();
	?>#000<?
	$color_tmp = ob_get_clean();
	fx::dig_set($this->data, "color", $color_tmp);
}
if (!(fx::env("is_admin") && $color_tmp instanceof fx_template_field)) {
	$color_tmp = new fx_template_field($color_tmp, array("id" => "color", "var_type" => "visual", "infoblock_id" => fx::dig($this->data, "infoblock.id"), "template" => $this->_get_template_sign(), "editable" => true));
}

echo $color_tmp;
unset($color_tmp);

?>">
<?
$title_tmp = null;
if (isset(${"title"})) {
	$title_tmp = ${"title"};
} else {
	$title_tmp = fx::dig($this->data, "title");
}
if (is_null($title_tmp)) {
	ob_start();
	?>
Заголовок
<?
	$title_tmp = ob_get_clean();
	fx::dig_set($this->data, "title", $title_tmp);
}
if (!(fx::env("is_admin") && $title_tmp instanceof fx_template_field)) {
	$title_tmp = new fx_template_field($title_tmp, array("id" => "title", "var_type" => "visual", "infoblock_id" => fx::dig($this->data, "infoblock.id"), "template" => $this->_get_template_sign(), "editable" => true));
}

echo $title_tmp;
unset($title_tmp);

?>
</h1>
</div>
<div class="data">
<?
$content_tmp = null;
if (isset(${"content"})) {
	$content_tmp = ${"content"};
} else {
	$content_tmp = fx::dig($this->data, "content");
}
echo $content_tmp;
unset($content_tmp);

?>
</div>
</div>

			<?
    }
    public function tpl_supermenu() {
        ?>
<div id="menu_vert">
<h2>
<?
$menu_title_tmp = null;
if (isset(${"menu_title"})) {
	$menu_title_tmp = ${"menu_title"};
} else {
	$menu_title_tmp = fx::dig($this->data, "menu_title");
}
if (is_null($menu_title_tmp)) {
	ob_start();
	?>
Заголовок меню
<?
	$menu_title_tmp = ob_get_clean();
	fx::dig_set($this->data, "menu_title", $menu_title_tmp);
}
if (!(fx::env("is_admin") && $menu_title_tmp instanceof fx_template_field)) {
	$menu_title_tmp = new fx_template_field($menu_title_tmp, array("id" => "menu_title", "var_type" => "visual", "infoblock_id" => fx::dig($this->data, "infoblock.id"), "template" => $this->_get_template_sign(), "editable" => true));
}

echo $menu_title_tmp;
unset($menu_title_tmp);

?>
</h2>
<ul>
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
	if (fx::env('is_admin') && ($item instanceof fx_essence) ) {
		ob_start();
	}
?>
<li>
<a class="menu-active" href="<?
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
echo $f_name_tmp;
unset($f_name_tmp);

?>
</a>
</li>
<?	if (fx::env('is_admin') && ($item instanceof fx_essence) ) {
		echo $item->add_template_record_meta(ob_get_clean());
	}
}
}
?>
</ul>
</div>
<?
    }
    public function tpl_() {
        ?><?
    }
protected $_templates = array (
  0 => 
  array (
    'id' => 'index',
    'calls' => 
    array (
      0 => 'wrap',
    ),
    'name' => 'index',
    'for' => 'layout.show',
  ),
  1 => 
  array (
    'id' => 'inner',
    'calls' => 
    array (
      0 => 'wrap',
    ),
    'name' => 'inner',
    'for' => 'layout.show',
  ),
  2 => 
  array (
    'id' => 'wrap',
    'name' => 'wrap',
    'for' => 'false',
  ),
  3 => 
  array (
    'id' => 'demo_menu',
    'name' => 'demo_menu',
    'for' => 'component_section.listing',
  ),
  4 => 
  array (
    'id' => 'wrap_simple',
    'name' => 'Простой блок',
    'for' => 'wrap',
  ),
  5 => 
  array (
    'id' => 'wrap_titled',
    'name' => 'Блок с заголовком',
    'for' => 'wrap',
  ),
  6 => 
  array (
    'id' => 'supermenu',
    'name' => 'supermenu',
    'for' => 'component_section.listing',
  ),
  7 => 
  array (
    'id' => NULL,
    'name' => NULL,
    'for' => 'layout.show',
  ),
);
}
?>
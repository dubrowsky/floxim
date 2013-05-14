<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
</head>
<body>
<div id="main">
	<!--верхняя цветная полоса-->
	<div class='color_line'><div><div><div><div><div><div><div><div><div><div></div></div></div></div></div></div></div></div></div></div></div>
	<!--//верхняя цветная полоса-->
	<!--шапка сайта, логотип, поиск, меню, языковая версия-->
	<div id="header">
		<!--логотип, слоган-->
		<div id="logo">
			<a href="/">
				<img src="css/images/logo.gif"  height="55" alt="" title="" keyword="logo_image" fx_replace="src,alt,title" />
			</a>
            {%slogan}название или слоган <br />вашей компании{/%slogan}
		</div>
		<!--//логотип, слоган-->
		<div id="header_nav">
			<!-- -->
			<div id="lang">
				<a href="#" class="cms-lang-active"><img src="css/images/lang_rus.jpg" alt="" title="" /> Русский</a>
				<a href="#" class="cms-lang-unctive"><img src="css/images/lang_eng.jpg" alt="" title="" /> English</a>
			</div>
			<!-- //языковая версия-->
			<!--поиск-->
			<fx_block keyword="right_up" embed="miniblock" max_repeat="3">
				<div class="search">
					<fx_content>Поиск</fx_content>
				</div>
			</fx_block>

			
			<!--//поиск-->
			<div class="sep"></div>
			<!--горизонтальное меню-->
			<div id="menu" fx_template="main_menu" fx_template_for="component_section.listing">
                <ul>
                	<li fx_render="">
                		<a class="menu-active" href="{$url}"><span><span>{$name}</span></span></a>
                	</li>
                </ul>
			</div>
		<!--//горизонтальное меню-->
		</div>
		<div class="sep"></div>
	</div>
	<!--//шапка сайта, логотип, поиск, меню, языковая версия-->
	<!--центральная часть, контентная область и правый вспомогательный блок-->
	<div id="center">
	<!--контент-->
		<div id="content">
		<!--заголовок-->
			<h1>Заголовок раздела сайта (h1)</h1>
		<!--//заголовок-->
            <fx_blockset keyword="main_content" main="yes">
                <fx_block name="Пустой">
                    <div class="block"><fx_content>Текст блока</fx_content></div>
                </fx_block>
                <fx_divider>
                    <div class="sep"></div>
                </fx_divider>
                <fx_block name="C заголовком">
                    <div class="block">
                        <h2><fx_replace name="Заголовок">Заголовок раздела</fx_replace></h2>
                        <p><fx_content>Мифопоэтическое пространство, не учитывая количества слогов, стоящих между ударениями, аннигилирует мифопоэтический хронотоп, но известны случаи прочитывания содержания приведённого отрывка иначе.</fx_content></p>
                </div>
                </fx_block>  
                <div class="sep"></div>
                <fx_block name="C заголовком и примечанием">
                    <div class="block">
                        <h2><fx_replace name="Заголовок">Эпическая медлительность</fx_replace></h2>
                        <p><fx_content>Мифопоэтическое пространство, не учитывая количества слогов, стоящих между ударениями, аннигилирует мифопоэтический хронотоп, но известны случаи прочитывания содержания приведённого отрывка иначе.</fx_content></p>
                    </div>
                    <div class="content_note"><fx_replace name="Примечание">Примечание на странице</fx_replace></div>
                </fx_block>
                <div class="sep"></div>
            </fx_blockset>
		</div>
	<!--//контент-->
	<!--правый блок-->
	<div id="right_content">
	<!--вертикальное меню-->
		<div id="menu_vert">
            <fx_menu keyword="right" kind="independent,dependent" parent="main" direct="v">
                <fx_prefix><ul></fx_prefix>
                <fx_active>
					<li>
						<a class="menu-active" href="%url%">%name%</a>
						<fx_submenu>
							<fx_prefix><ul></fx_prefix>
							<fx_unactive><li><a href="%url%">%name%</a></li></fx_unactive>
							<fx_suffix></ul></fx_suffix>
						</fx_submenu>
					</li>
				</fx_active>
                <fx_unactive><li><a href="%url%">%name%</a> %submenu% </li></fx_unactive>
                <fx_suffix></ul></fx_suffix>
            </fx_menu>
		</div>
	<!--//вертикальное меню-->
    <fx_inherit keyword="sidebar">
        <fx_blockset keyword="right_block" embed="vertical">
            <fx_block name="Простой текст">
                <div class="block"><fx_content>Текст</fx_content></div>
            </fx_block>
            <fx_divider>
                <div class="sep"></div>
            </fx_divider>
            <div class="sep"></div>
            <fx_block name="C заголовком и списком">
                <div class="block">
                    <h2><fx_replace>Новости</fx_replace></h2>
                    <fx_content>Список новостей</fx_content>
                </div>
            </fx_block>
        </fx_blockset>
    </fx_inherit>
	</div>
	<!--//правый блок-->
	</div>
	<div class="sep"></div>
<!--//центральная часть, контентная область и правый вспомогательный блок-->
<fx_inherit keyword="footer">
<!--баннеры-->
	<div id="banners">
            <fx_block keyword="center_bottom">
                <div class="block"><fx_content>Блок</fx_content></div>
            </fx_block>
		<div class="sep"></div>
	</div>
<!--//баннеры-->
	<div id="footer">
		<div class="left"><fx_replace keyword="copyright">© 2010 группа компаний «Netcat».<br />Все права защищены.</fx_replace></div>
		<div class="middle"><fx_replace keyword="address">Адрес: г. Москва, ул. Мануфактурная, д. 14<br />Телефон и факс: (831) 220-80-18</fx_replace></div>
		<div class="right"><fx_replace keyword="developa">© 2010 Хороший пример <br />сайтостроения — <a href="#">WebSite.pu</a></fx_replace></div>
		<div class="sep"></div>
	</div>
<!--нижняя цветная полоса-->
	<div class='color_line'><div><div><div><div><div><div><div><div><div><div></div></div></div></div></div></div></div></div></div></div></div>
<!--//нижняя цветная полоса-->
</div>

</body>
</html>
</fx_inherit>
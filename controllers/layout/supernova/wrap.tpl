{template id="wrap" for="false"}
<!DOCTYPE html>
<html>
    <head>
        <title>My Super Template</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
				<img src="/controllers/layout/supernova/images/logo.gif" fx_replace="src" />
			</a>
			<div fx_var="slogan">название или слоган <br />вашей компании</div>
		</div>
        <!--//логотип, слоган-->
		<div id="header_nav">
			
            <!--//поиск-->
			<div class="sep"></div>
            <!--горизонтальное меню-->
			{area id="header"}
			<div id="menu" fx_template="demo_menu" fx_template_for="component_section.listing" fx_template_name="Горизонтальное меню (главное)">
            	<ul>
                    <li fx_render=".">
                            <a href="{$url}">
                                <span class="mw"><span class="mw">
                                    {$name}
                                </span></span>
                            </a>
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
			{$content}
			{template id="wrap_simple" name="Простой блок" for="wrap"}
				<div class="block">
					{$content}
				</div>
			{/template}
			{template id="wrap_titled" name="Блок с заголовком" for="wrap"}
				<div class="block">
					 <div class="title">
						<h1 style="color:{%color}#000{/%color}" fx_var="title">Заголовок</h1>
					 </div>
					 <div class="data">{$content}</div>
				</div>
			{/template}
			<!--//заголовок-->
            
		</div>
	<!--//контент-->
	<!--правый блок-->
	<div id="right_content" fx_area="sidebar">
	<!--вертикальное меню-->
		<div id="menu_vert" fx_template="supermenu" fx_template_for="component_section.listing">
			<h2 fx_var="menu_title">Заголовок меню</h2>
            <ul>
                <li fx_render=".">
					<a class="menu-active" href="{$url}">{$name}</a>
				</li>
			</ul>
		</div>
	<!--//вертикальное меню-->
	</div>
	<!--//правый блок-->
	</div>
	<div class="sep"></div>
<!--//центральная часть, контентная область и правый вспомогательный блок-->
<!--баннеры-->
	<div id="banners">
		<div class="sep"></div>
	</div>
<!--//баннеры-->
	<div fx_area="footer" id="footer">
		<div class="left" fx_var="copy">© 2010 группа компаний «Netcat».<br />Все права защищены.</div>
		<div class="middle" fx_var="contacts">Адрес: г. Москва, ул. Мануфактурная, д. 14<br />Телефон и факс: (831) 220-80-18</div>
		<div class="right" fx_var="developa">© 2010 Хороший пример <br />сайтостроения — <a href="#">WebSite.pu</a></div>
		<div class="sep"></div>
	</div>
<!--нижняя цветная полоса-->
	<div class='color_line'><div><div><div><div><div><div><div><div><div><div></div></div></div></div></div></div></div></div></div></div></div>
<!--//нижняя цветная полоса-->
</div>

</body>
</html>
{/template}
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
			<div>{%slogan}название или слоган <br />вашей компании{/%slogan}</div>
		</div>
	<!--//логотип, слоган-->
		<div id="header_nav">
			<!--
			<div id="lang">
				<a href="#" class="cms-lang-active"><img src="css/images/lang_rus.jpg" alt="" title="" /> Русский</a>
				<a href="#" class="cms-lang-unctive"><img src="css/images/lang_eng.jpg" alt="" title="" /> English</a>
			</div>
			//языковая версия-->
		
			
		<!--//поиск-->
			<div class="sep"></div>
		<!--горизонтальное меню-->
			{area id="header"}
			<div id="menu" fx_template="demo_menu" fx_template_for="component_section.listing">
            	<ul>
                    <li fx_render="."><a href="{$f_url}"><span class="mw"><span class="mw">{$f_name}</span></span></a></li>
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
				<div class="block" {*style="border-color:{%color editable="false"}#900{/%color};"*}>
					 <div class="title" {*style="background:{%color};"*}>
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
					<a class="menu-active" href="{$f_url}">{$f_name}</a>
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
	<div id="footer">
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
{*
<!DOCTYPE html>
<html>
    <head>
        <title>My Super Template</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <div class="main_wrap">
            <div class="header">
                <a class="home" href="/">
                   <img src="{%logo}/floxim_templates/demo1/css/images/logo.gif{/%logo}" />
                </a>
                {area id="header"}
            </div>
            <div class="content">
                {$content}
                {template id="wrap_simple" name="Простой блок" for="wrap"}
                    <div class="block">
                        {$content}
                    </div>
                {/template}
                {template id="wrap_titled" name="Блок с заголовком" for="wrap"}
                    <div class="block titled_block" style="border-color:{%color editable="false"}#900{/%color};">
                         <div class="title" style="background:{%color};">
                            {%title}Заголовок{/%title}
                         </div>
                         <div class="data">{$content}</div>
                    </div>
                {/template}
            </div>
            <div class="footer">
                <div class="footer_left">
                    <?
                        $mytest = array('one' => array('x' => 'Olo', 'y' => 'trolo'));
                    ?>
                    {%copy}&copy; 2010-<?=date('Y')?>{/%copy}<br />
                    <div fx_var="company">My Company Name</div>
                    <div fx_var="$mytest.one.x">hm...</div>
                </div>
                <div class="footer_right" fx_area="footer">
                    <div class="supermenu" fx_template="supermenu" fx_template_for="component_section.listing">
                        <span class="title">{%title}Менюшечка:{/%title}&nbsp;</span>
                        {render}
                            <span class="menu_item">
                                <a href="{$f_url}">{$f_name}</a>
                            </span>
                            <?if (!$item_is_last){?>
                                <span class="sep">{%separator}&nbsp;&bull;&nbsp;{/%separator}</span>
                            <?}?>
                        {/render}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>*}
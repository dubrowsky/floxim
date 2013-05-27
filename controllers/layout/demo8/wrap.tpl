{template id="wrap" for="false"}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Demo8</title>
</head>
<body>
<div id="main">
    <div class="content">
        <!--header-->
        <div id="header">
            <!--логотип, слоган-->
            <div id="logo">
                <a href="/">
                    <img src="controllers/layout/supernova/images/logo.gif" width="103" height="75" alt="" title="" />
                </a>
                <span class="name">YOUR<span>LOGO</span></span>
                <span>Sampletext under logo</span>
            </div>
            <!--//логотип, слоган-->

            <!--поиск, если в блоке содержится только поиск, добавляется class="search top_space", если в блоке присутствуют еще какие-либо элементы то вид class="search"-->
            <div class="search top_space">
                <form action="#">
                    <input type="text" value="Что будем искать?" name="" onblur="if(this.value==''){this.value='Что будем искать?'}" onfocus="if(this.value=='Что будем искать?'){this.value=''}" />
                    <input type="submit" value="Поиск" name="" class="submit" />
                </form>
            </div>

            <!--//поиск-->
            <div class="sep"></div>

            <!--menu, отступы внутри пунктов меню сделаны через padding, что не совсем правильно, если мало или наоборот много пунктов меню, выглядеть будет не очень, такое меню надо делать на таблице-->
            <div id="menu">
                <div class="left">
                    <div class="right">
                        {area id="header"}
                        {template id="demo_menu" name="Горизонтальное меню (главное)" for="component_section.listing"}
                            <ul>
                                <li><a href="{$url}" {if test="$active"}class="menu-active"{/if}>{$name}</a></li>
                            </ul>
                        {/template}
                    </div>
                </div>
            </div>
            <!--//menu-->
        </div>
        <!--//header-->

        <!--блок слева-->
        <div id="left_content">
            {area id="sidebar"}

            <div class="submenu" fx_template="supermenu" fx_template_for="component_section.listing">
                <ul>
                    <li fx_render="."><a href="{$url}">{$name}</a>
                    {if test="$submenu"}
                        <ul>
                            <li><a href="#">Брелоки</a></li>
                            <li><a href="#">Игрушки</a></li>
                            <li><a href="#">Статуэтки</a></li>
                        </ul>
                    {/if}
                    </li>
                    <li {if test="$last"}class="last"{/if}><a href="{$url}">{$name}</a></li>
                </ul>
            </div>

        </div>

<!--//блок слева-->
<!--content, если есть блок слева то добавляется class="is_left"-->
<div id="content" class="is_left">

    {area id="content"}

        {template id="wrap_titled"}
            <h1>{$title}</h1>
            {$content}
        {/template}
        <!--banner-->
        <div class="banner">
            {area id="banner"}
            {*<a href="#"><img src="css/images/content_banner.jpg" width="621" height="91" alt="" /></a>*}
        </div>
        <!--banner-->

        <!--блок с двумя колонками на главной-->
        {$too_blocks}
        <!--//блок с двумя колонками-->
</div>
<!--//content-->
<div class="sep"></div>

<!--логотипы компаний, партнеры, клиенты и т.п.-->
{$index_block}
<!--//логотипы компаний, партнеры, клиенты и т.п.-->

</div></div>
<!--footer-->
<div id="footer">
    <div class="left">&copy; 2010 группа компаний &laquo;Netcat&raquo;.<br />Все права защищены.</div>
    <div class="right">&copy; 2010 Хороший пример <br />сайтостроения &mdash; <a href="#">WebSite.pu</a></div>
    <div class="sep"></div>
</div>
<!--//footer-->
</body>
</html>
{/template}
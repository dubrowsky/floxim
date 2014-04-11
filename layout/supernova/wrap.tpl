<!DOCTYPE html>
<html>
<head>
    <title>My Super Template</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta fx:layout="inner" content="" />
    <meta fx:layout="index" content="index" />
    {css}
        main.css
        color.css
    {/css}
</head>
<body>
<div id="main">
    <div class='color_line'><div><div><div><div><div><div><div><div><div><div></div></div></div></div></div></div></div></div></div></div></div>
    <div id="header">
        <div id="logo">
            <a href="/">
                <img src="{%logo}<?=$template_dir?>images/logo.gif{/%}" />
            </a>
            <div>{%slogan}your company name<br />or slogan{/%}</div>
        </div>
        <div id="header_nav">

            <div class="sep"></div>
            {area id="header" size="low,wide"}
            <div 
                fx:template="demo_menu" 
                fx:of="component_section.listing" 
                fx:name="Main horizontal menu"
                class="supernova_menu" id="menu" >
                <ul>
                    <li fx:item>
                        <a href="{$url}">
                            <span class="mw"><span class="mw">
                                {$name}
                            </span></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="sep"></div>
    </div>
    <div class="index_data" fx:if="$index">
        <div id="top_banner">
            <div class="bottom"><div class="left"><div class="right"><div class="ltc"><div class="rtc"><div class="lbc"><div class="rbc"><div class="color_bg"><div class="content_bg">
            <div class="banner_content">
                <img src="{%top_banner | 'w:223,h:268'}<?=$template_dir?>images/the_cat.jpg{/%}" align="right" width="223" height="268" alt="" />
                <p class="slogan">
                    {%banner_slogan type="html"}&laquo;Simplicity of sitebuilder, functionality of CMS,
                        flexibility of framework. And it's free!&raquo;
                    {/%}
                </p>
                <p>
                    {%banner_text}
                        If you like Floxim, help us make it more popular, tell us about Floxim!
                    {/%}
                </p>
                <a href="{%banner_url}http://floxim.org/{/%}" class="more"><span><span>{%banner_more}Read more...{/%}</span></span></a>
                <div class="sep"></div>
            </div>
            </div></div></div></div></div></div></div></div></div>
        </div>
    </div>
    <div id="center">
        <div id="content">
            {area id="content"}
            {template id="wrap_simple" name="Simple block" of="wrapper"}
                <div class="block">
                    {$content}
                </div>
            {/template}
            {template id="wrap_titled" name="Block with a header" of="wrapper"}
                <div class="block">
                    <div class="title">
                        <h1 style="color:{%color}#000{/%}">{%title}Header{/%}</h1>
                    </div>
                    <div class="data">{$content}</div>
                </div>
            {/template}
        </div>
        <div id="right_content">
            {area id="sidebar" size="high,narrow"}
            <div 
	fx:template="supermenu" 
	fx:of="component_section.listing"
	fx:name="Vertical menu"
	id="menu_vert">
                <h2>{%menu_title}Menu title{/%}</h2>
                <ul>
                    <li fx:item>
                        <a class="menu-active" href="{$url}">{$name}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="sep"></div>
    <div id="banners">
        <div class="sep"></div>
    </div>
    <div id="footer">
        {area id="footer" size="wide,low"}
        <div class="left">
            {%copy}&copy; 2010-<?=date('Y')?> FloxiGroup Ltd.<br />All rights reserved.{/%}
        </div>
        <div class="middle">
            {%contacts}Address: 14, Manufacture street, Moscow, Russia<br />Phone/fax: (831) 220-80-18{/%}
        </div>
        <div class="right">
            {%developer_copy}&copy; 2010 Developed by<br />the <a href="#">WebSite.ru</a> studio{/%}
        </div>
        <div class="sep"></div>
    </div>
    <div class='color_line'><div><div><div><div><div><div><div><div><div><div></div></div></div></div></div></div></div></div></div></div></div>
</div>
</body>
</html>
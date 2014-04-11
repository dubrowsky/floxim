<!DOCTYPE html>
<html>
    <head>
        <title>MoscowPools</title>
        <meta charset="utf-8">
        {css}
            css/reset.css
            css/tabs.less
            css/style.less
        {/css}
        {js}
            js/jquery-1.7.min.js
            js/script.js
            js/superfish.js
            js/jquery.flexslider.js
            js/tabs.js
        {/js}
        <meta fx:layout="one_col" fx:name="Full width"  />
        <meta fx:layout="two_cols" fx:name="Right column" content="right" />
        <meta fx:layout="left_col" fx:name="Left column" content="left" />
        <meta fx:layout="three_cols" fx:name="Three columns" content="left,right" />
    </head>
    <body>
        <div class="wrapper">
            <header class="main_header" fx:area="main_header">
                <div class="logo">
                    <a href="/">
                        <img src="{%logo}img/sample_logo.png{/%}" alt="{%company_name}Company name{/%}" />
                        <span class="tagline">{%logo_tagline}Whatever you do{/%}</span>
                    </a>
                </div>
                <nav class="top_links" fx:template="top_links" fx:of="menu">
                    <ul>
                        <li fx:item><a href="{$url}" class="tl">{$name}</a></li>
                    </ul>
                </nav>
                <div class="contacts">
                    <div class="phone">
                        {%phone}<strong>+7 (495)</strong> 649-61-68{/%}
                    </div>
                    <div class="call_time">{%call_time}10:00 — 22:00{/%}</div>
                </div>
            </header>
            <section fx:area="main_menu_area">
                <nav class="main_menu" fx:template="main_menu" fx:of="menu">
                    {$items | .main_menu_level}
                    <ul fx:template="main_menu_level" fx:with-each='$'>
                        {default $level = 1}
                        <li fx:item>
                            <a href="{$url}"><span fx:omit="$level != 2">{$name}</span></a>
                            {$submenu | .main_menu_level with $level+1 as $level}
                        </li>
                    </ul>
                </nav>
            </section>
            <section 
                class="columns{if $left} with_left{/if} {if $right} with_right{/if}" fx:omit="!$left && !$right">
                <section class="content" fx:area="content" fx:suit="default_wrapper:content_padded">
                    <section 
                        fx:template="content_section_titled" 
                        fx:of="wrapper" 
                        fx:suit="local,sidebar"
                        class="shadowed">
                        <h2>{%header}Block header{/%}</h2>
                        {$content}
                    </section>
                    <section 
                        fx:template="content_padded"
                        fx:suit="local,sidebar"
                        fx:of="wrapper"
                        class="padded">
                            {$content}
                    </section>
                </section>
                <section class="sidebar left" fx:area="left" fx:if="$left">
                    Left bar
                </section>
                <section class="sidebar right" fx:area="sidebar" fx:if="$right">
                    Right bar
                </section>
            </section>
            <footer class="page_footer">
                <div class="copy">
                    © {%start_year}2009{/%}&ndash;<?php echo date('Y')?>
                    {%footer_contacts}
                        <a href="/">«{%company_name /}»</a> — {%logo_tagline /}<br />
                        Your City, Street name, 7<br />
                        E-mail: info@moscowpools.ru<br />
                        Phone: {%phone | strip_tags /}
                    {/%}
                </div>
                <div class="footer_extra" fx:area="footer_extra">
                    <ul class="social_icons" fx:template="social_menu" fx:of="menu" fx:suit="local,sidebar">
                        <li fx:item>
                            <a href="{$url editable='true'}" 
                               title="{$name editable='true'}"
                               target="_blank">
                                {set $ico_ready = 'img/ico_' . $external_host . '.png'}
                                <img src="{%image_$id | '32*32'}{$ico_ready}img/ico_.png{/$}{/%}" />
                            </a>
                        </li>
                    </ul>
                </div>
            </footer>
        </div>
    </body>
</html>
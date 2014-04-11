<!DOCTYPE html>
<html lang="en">
<head>
    <title>About Us</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?=$template_dir?>images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="<?=$template_dir?>images/favicon.ico" type="image/x-icon" />
    {css}
        css/style.css
    {/css}
    {js}
        js/jquery-1.7.min.js
        js/script.js
        js/superfish.js
        js/jquery.mobilemenu.js
        js/jquery.flexslider.js
        js/jquery.elastislide.js
        js/jquery.easing.1.3.js
        js/tabs.js
        js/jquery.ui.totop.js
    {/js}
     
    <!--[if lt IE 8]>
   <div style=' clear: both; text-align:center; position: relative;'>
     <a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode">
       <img src="http://storage.ie6countdown.com/assets/100/images/banners/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." />
    </a>
  </div>
<![endif]-->
    <!--[if lt IE 9]>
       <script type="text/javascript" src="<?=$template_dir?>js/html5.js"></script>
       <link rel="stylesheet" type="text/css" media="screen" href="<?=$template_dir?>css/ie.css">
    <![endif]-->
</head>
<body>
<!--==============================Block1=================================-->
<div class="block1">
    <div class="container_12" fx:area="top_blocks">
    <!--==============================header=================================-->
        <header>
            <h1>
                <a class="logo" 
                   accesskey=""
                   style="background-image:url('{%logo}<?=$template_dir?>images/logo.png{/%}');" 
                   href="/">
                    {%logo_text}SoftBox{/%}
                </a>
            </h1>
            <div class="nav_area" fx:area="nav_area">
                <nav fx:template="main_menu" fx:of="menu" fx:name="Main menu">
                    {$items | .menu_body /}
                    <ul fx:template="menu_body" class="sf-menu" fx:with-each="$">
                        <li fx:item {if $is_active}class="current"{/if}>
                            <a href="{$url}">{$name}</a>
                            {$submenu | .menu_body /}
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="clear"></div>
        </header>
        <!--SLIDER-->
          <div
              fx:template="top_slider"
              fx:of="page.list"
              fx:name="Top slider"
              class="flex_box">
                <div class="grid_12">
                    <div class="flexslider">
                        <ul class="slides">
                            <li fx:item>
                                <div class="banner">
                                    <div class="title">{$name}</div>
                                    <div class="text">
                                        {$description}
                                        <p>Nunc vulputate ultrices consectetur. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ut tortor urna, ut tincidunt dolor. Donec semper lacinia ultricies. Suspendisse elit lectus, fringilla in sollicitudin nec
                                          amet quam. Donec aliquam accumsan condimentum. Nullam ut lacus
                                          adipiscing ipsum molestie euismod.</p>
                                        {/$}
                                    </div>
                                    <a href="{$url}" class="button">{%read_more}Read More{/%}</a>
                                </div>
                                <img src="{%image_$id | 'width:320,height:320'}{$image}<?=$template_dir?>images/slider1.png{/$}{/%}" alt="" />
                            </li>
                            </ul>
                     </div>
                </div>
          </div>
        <!--SLIDER_END-->
    </div>
</div>
<div class="block2">
    <div class="container_12" fx:area="content">
    <!--==============================content================================-->
        <section id="content" class="cont_pad">
                <div class="wrapper" fx:template="hello_text" fx:of="text.list" fx:name="Hello text">
                     <article class="grid_12 last-col">
                      <div class="hello_box bitter" fx:item>
                              {$text}
                        </div>
                     </article>
                </div>
                       
                <div class="wrapper offers_box" fx:template="offers_box" fx:of="page.list">
                     <article class="grid_4" fx:item>
                      <div class="offer">
                              <img src="{%image_$id}<?=$template_dir?>images/offer1.png{/%}" alt="">
                              <div class="title">{$name}</div>
                                {$description}
                                <p>Lorem ipsum dolor sit amet, consect etur adip scing elit. Vestibulum ut tortor urnati dunt dolor. Nunc vulputate ultrices con sect etur donec semper lacinia ultricies.</p>
                                {/$}
                                <a href="{$url}" class="button">{%read_more}Read More{/%}</a> 
                          </div>
                     </article>
                </div>
                <div class="wrapper" fx:template="block_titled" fx:of="wrapper">
                  <article class="last-col">
                      <h2><span>{%header}Client testimonials{/%}</span></h2>
                      {$content}
                     </article>
                </div>
                <div class="testimonial_box" fx:template="carousel" fx:of="page.list">
                    <!--======================== carousel ===========================-->
                    <div id="carousel" class="es-carousel-wrapper">
                        <div class="es-carousel">
                            <ul>
                                <li fx:item>
                                    <div class="testimonial">
                                        {%text_$id}
                                            Lorem ipsum dolor sit amet, consect etur adipiscing elit. 
                                            Vestibulum ut tortor urnati dunt dolor. 
                                            Nunc vulputate ultrices con sect etur donec semper 
                                            lacinia ultri  dolore cie
                                            lorem ipsum commete.<br>
                                        {/%}
                                        <a href="{$url}" class="link1">{$name}</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
              </div>
                <div class="wrapper" id="tabs" fx:template="tabs_blockset" fx:of="widget_blockset.show">
                  <article class="grid_12">
                              <article class="grid_3 alpha" fx:with-each="$items">
                                    <ul>
                                          <li 
                                              class="{%tab_icon_$id 
                                                    type="select"
                                                    values="`array(
                                                        '' => 'None', 
                                                        'project'=>'Project', 
                                                        'blog' =>'Blog')`"
                                                    }"
                                              fx:item>
                                              <a href="#tabs-{$position}">{%tab_title_$id}Projects{/%}</a>
                                          </li>
                                     </ul>
                                </article>
                                <article 
                                    class="grid_9 omega last-col" 
                                    fx:area="$area" 
                                    fx:suit="force_wrapper:local">
                                     <div 
                                         id="tabs-{$infoblock_area_position}" 
                                         fx:template="tab_wrapper" 
                                         fx:of="wrapper">
                                        <div class="wrapper">
                                          {$content /}
                                          <div class="block_list" fx:template="pane_block_list" fx:of="page.list">
                                            <div class="tgrid_3 alpha" fx:item>
                                                <p>
                                                    <a href="{$url}">
                                                        <img src="{%image_$id}<?=$template_dir?>images/1page_img1.jpg{/%}" alt="">
                                                    </a>
                                                </p>
                                                <div class="title bitter">{$name}</div>
                                                <div>
                                                    {$description}
                                                        <p>Lorem ipsum dolor sit ametconety sect etur adipiscing elit.</p>    
                                                    {/$}
                                                </div>
                                            </div>
                                          </div>
                                        </div>
                                     </div>
                                </article>
                     </article>
                </div>
                
                <div class="newsletter extra_wrapper" fx:template="subscribe_form" fx:of="widget_subscribe_form.show">
                    <div class="f_left">
                        {%description}<p>Lorem ipsum dolor sit amet, 
                            consect etur adipi ing elit. Vestibulum ut tortor urnati dunt dolor. 
                            Nunc vulputate ultrices con sect etur donec.</p>{/%}
                    </div>
                    <div class="f_right">
                        <form id="newsletter">
                            <input type="text" 
                                    placeholder="{%placeholder}Enter your email address{/%}" />
                            <a onclick="document.getElementById('newsletter').submit()" class="button">
                                {%submit}Submit{/%}
                            </a>
                        </form>
                    </div>
                    <div class="clear"></div>
                </div>
        </section>
    </div>
</div>
<div class="block3">
    <!--==============================footer=================================-->
    <div class="container_12">
           <footer fx:area="footer" fx:suit="force_wrapper:local">
                <div class="wrapper">
                      <div fx:template="footer_block" fx:of="wrapper" 
                           class="grid_{%width 
                                            type="select" 
                                            label="Width" 
                                            values="`array(3=>'narrow', 6=>'wide')`"}6{/%}" >
                            <div class="title">{%header}Shortly About Us{/%}</div>
                        {$content /}
                      </div>
                      
                        <ul class="social" fx:template="social_links" fx:of="page.list">
                            <li fx:item>
                                <a href="{$url}"><figure><img src="{%image_$id | 'width:31;height:24'}<?=$template_dir?>images/soc1.png{/%}" alt=""></figure>
                                {$name}Follow us on Twitter{/$}</a>
                            </li>
                        </ul>
                      <div class="grid_3 privacy last-col">
                        <div class="title">{%copy}copyright{/%}</div>
                            <span class="reg">{%logo_text}SoftBox{/%}</span> © <?=date('Y')?> | 
                            <a href="{%privacy_link}/{/%}">{%privacy_text}Privacy Policy{/%}</a><br>
                        <!-- {%FOOTER_LINK} -->
                      </div>
                 </div>
        </footer>
    </div>
</div>
</body>
</html>
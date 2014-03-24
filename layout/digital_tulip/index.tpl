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
                                        {%description_$id}
                                        <p>Nunc vulputate ultrices consectetur. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ut tortor urna, ut tincidunt dolor. Donec semper lacinia ultricies. Suspendisse elit lectus, fringilla in sollicitudin nec
                                          amet quam. Donec aliquam accumsan condimentum. Nullam ut lacus
                                          adipiscing ipsum molestie euismod.</p>
                                        {/%}
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
                                {%description_$id}
                                <p>Lorem ipsum dolor sit amet, consect etur adip scing elit. Vestibulum ut tortor urnati dunt dolor. Nunc vulputate ultrices con sect etur donec semper lacinia ultricies.</p>
                                {/%}
                                <a href="{$url}" class="button">{%read_more}Read More{/%}</a> 
                          </div>
                     </article>
                </div>
                <div class="wrapper" fx:template="block_titled" fx:of="block">
                  <article class="grid_12 last-col">
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
                              <article class="grid_3 alpha">
                                    <ul fx:with-each="$blocks">
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
                                    fx:suit="force_block:local">
                                     <div 
                                         id="tabs-{$infoblock_area_position}" 
                                         fx:template="tab_wrapper" 
                                         fx:of="block">
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
                                                    {%description_$id}
                                                        <p>Lorem ipsum dolor sit ametconety sect etur adipiscing elit.</p>    
                                                    {/%}
                                                </div>
                                            </div>
                                          </div>
                                        </div>
                                     </div>
                                </article>
                     </article>
                </div>
                <div class="wrapper">
                    <article class="grid_12">
                        <h2 class="ind"><span>sign-up for the newsletter</span></h2>
                        <div class="newsletter extra_wrapper">
                            <div class="f_left">
                                Lorem ipsum dolor sit amet, consect etur adipi ing elit. Vestibulum ut tortor urnati dunt dolor. Nunc vulputate ultrices con sect etur donec.
                            </div>
                            <div class="f_right">
                                <form id="newsletter">
                                    <input type="text" value="Enter your email address" onBlur="if(this.value=='') this.value='Enter your email address'" onFocus="if(this.value =='Enter your email address' ) this.value=''"
><a onclick="document.getElementById('newsletter').submit()" class="button">Submit</a>
                                    
                                </form>
                            </div>
                                     <div class="clear"></div>
                        </div>
                    </article>
                </div>
        </section>
    </div>
</div>
<div class="block3">
    <!--==============================footer=================================-->
    <div class="container_12">
           <footer>
                <div class="wrapper">
                      <div class="grid_6">
                            <div class="title">Shortly About Us</div>
                    Sit amet, consec tetuer adipiscin elit. Praesent ves tibul moles tiet 
                    lacus aenean nonummy hendrerit mauris phaselu porta. Fusce suset
                            varius mi. Cum sociis natoque penatibus hasellus por taus suscipity
                            mitte dolor sit amet, consec tetuer adipiscin elit.                        
                      </div>
                      <div class="grid_3">
                          <div class="title">Our Contacts</div>
                            <ul class="social">
                                <li>
                                    <a href="#"><figure><img src="<?=$template_dir?>images/soc1.png" width="31" height="24" alt=""></figure>
                                    Follow us on Twitter</a>
                                 </li>
                                 <li>
                                    <a href="#"><figure><img src="<?=$template_dir?>images/soc2.png" width="31" height="26" alt=""></figure>
                                    Join us on Facebook</a>
                                 </li>
                                 <li>
                                    <a href="#"><figure><img src="<?=$template_dir?>images/soc3.png" width="31" height="26" alt=""></figure>
                                    Subscribe to our blog</a>
                                 </li>
                                 <li class="cont_item m_bottom_zero">
                                    <a href="index-4.html"><figure><img src="<?=$template_dir?>images/soc4.png" width="31" height="20" alt=""></figure>
                                    Contact Us</a>
                                 </li>
                            </ul>
                      </div>
                      <div class="grid_3 privacy last-col">
                        <div class="title">copyright</div>
                            <span class="reg">softbox</span> © 2012 | <a href="index-5.html">Privacy Policy</a><br>
                        <!-- {%FOOTER_LINK} -->
                      </div>
                 </div>
        </footer>
    </div>
</div>
</body>
</html>
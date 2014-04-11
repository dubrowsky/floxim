<!DOCTYPE html>
<html>
    <head>
        <meta fx:layout="page" fx:name="Inner" content="sidebar" />
        <meta fx:layout="index" fx:name="Index" content="index_areas" />
        <meta fx:layout="full" fx:name="Full width" content="" />
    {js}
        FX_JQUERY_PATH
        html5.js
        script.js
    {/js}
    {css}all.css{/css}
    </head>
    <body>
        <header class="header"
                fx:area="top" 
                fx:size="wide,low" 
                fx:suit="force_wrapper:local">
                <div 
                    fx:template="header_block_left" 
                    fx:of="wrapper"
                    fx:suit="local" 
                    class="header_block_left">
                    {$content}
                </div>
                <div 
                    fx:template="header_block_right" 
                    fx:suit="local" 
                    fx:of="wrapper"
                    class="header_block_right">
                    {$content}
                </div>
        </header>
        <header class="header">
            <div class="wrapper" fx:area="header" fx:size="wide,low">
                <div class="logo">
                    <a href="/">
                        <img src="{%logo}<?=$template_dir?>images/logo.png{/%}" alt="" />
                    </a>
                </div>
                <nav 
                    fx:template="top_menu"
                    fx:name="Main Menu"
                    fx:of="menu"
                    fx:suit="local"
                    class="top_menu">
                    <ul>
                        <li fx:item>
                            <a href="{$url}">{$name}</a>
                        </li>
                        <li fx:item="$is_active" class="active">
                            <a href="{$url}">{$name}</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </header>
        <?
        $bg_color = '#000';
        $bg_image = '';
        if (!isset($path)) {
            $path = array();
        }
        
        foreach($path as $path_level => $path_page) {
            if (count($path) > 1 && $path_level == 0) {
                continue;
            }
            $path_page_id = $path_page['id'];
            if (isset(${"page_bg_color_$path_page_id"})) {
                if (empty(${"page_bg_color_$path_page_id"})) {
                    unset(${"page_bg_color_$path_page_id"});
                } else {
                    $bg_color = ${"page_bg_color_$path_page_id"};
                }
            }
            if (isset(${"page_bg_image_$path_page_id"})) {
                if (empty(${"page_bg_image_$path_page_id"})) {
                    unset(${"page_bg_image_$path_page_id"});
                } else {
                    $bg_image= fx_filetable::get_path(${"page_bg_image_$path_page_id"});
                }
            }
        }
        if (!$bg_image && !isset(${"page_bg_image_".$this->v('page_id')}) ) {
            $bg_image = $template_dir."images/0.gif";
        }
        ?>
        <section 
            style="background:{%page_bg_color_$page_id}<?=$bg_color?>{/%} 
                url('{%page_bg_image_$page_id}<?=$bg_image?>{/%}') no-repeat 50% 0;"
                class="section_inner{if !$sidebar} section_inner_full{/if}">
            <div class="wrapper">
                <div style="clear:both;"></div>
                <!-- For inner pages -->
                <div class="content" fx:area="content">
                    <div 
                        class="places" 
                        fx:name="Pages by year"
                        fx:template="pages_by_year" 
                        fx:size="wide,high"
                        fx:of="page.list">
                        <div 
                            fx:each="{$items->group('publish_date | fx::date : "Y"') as $year => $pages}" 
                            class="col"
                            {if ($pages_index-1) % 3 == 0} style="clear:both;"{/if}>
                            <strong>{$year}</strong>
                            <ul fx:with-each="$pages">
                                <li fx:item>
                                    <a href="{$url}">{$name}</a>
                                    <div fx:if="$cover">
                                        <img src="{$cover|'width:110'}" alt="" />
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- This is for inner -->
                <div class="sidebar" fx:area="sidebar" fx:if="$sidebar" fx:size="narrow,high">
                    <ul fx:template="side_menu" fx:of="component_section.listing" fx:name="Side menu" class="jt_side_menu">
                        <li fx:item><a href="{$url}">{$name}</a></li>
                        <li fx:item="$is_active"><a href="{$url}"><b>{$name}</b></a></li>
                    </ul>
                </div>
                    
                <!-- For index -->
                <div fx:if="$index_areas" class="section-info holder">
                    <div class="l-side" fx:area="index_left">
                        <ul 
                            fx:template="index_photo_anounces" 
                            fx:of="component_photo.list" 
                            class="photo_anounces">
                            <li fx:item>
                                <a href="{$item.parent.url}">
                                    <img src="{$photo | 'width:140,height:100'}" alt="" />
                                </a>
                                <span>{$description}</span>                                
                            </li>
                        </ul>
                    </div>
                    <div class="center">
                        {area id="index_center" /}
                        <div fx:template="block_titled" fx:of="wrapper" class="block_titled">
                            <h2>{%header}Header{/%}</h2>
                            {$content}
                        </div>
                        <ul fx:template="index_link_list" fx:of="page.list" fx:name="Simple link list">
                            <li fx:item><a href="{$url}">{$name}</a></li>
                        </ul>
                    </div>
                    <div class="r-side" fx:area="index_right">
                        
                    </div>
                </div>
            </div>
        </section>
        <section class="footer" fx:if="!$hide_footer">
            <div class="wrapper" fx:area="footer" fx:if="!$hide_footer">
                <div class="contact">
                    <span>
                        {%contacts_label}Contacts:{/%}
                        <span class="phone">{%phone}+7 (495) 440 72 72{/%}</span>
                    </span>
                    <a href="mailto:{%email editable="false"}info@jt.ru{/%}">{%email}</a>
                </div>
                <div fx:template="footer_block" fx:of="wrapper">
                </div>
                <div class="copy">
                    {%copy}&copy; JT, <?=date('Y')?><br />
                        Photos by: <a href="http://leecannon.com/">Lee Cannon</a>. 
                        <a href="#">License</a>
                    {/%}
                </div>
            </div>
        </section>
    </body>
</html>
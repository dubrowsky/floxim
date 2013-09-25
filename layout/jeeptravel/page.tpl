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
        <header class="header">
            <div class="wrapper">
                <div class="logo">
                    <a href="/">
                        <img src="{%logo}<?=$template_dir?>images/logo.png{/%}" alt="" />
                    </a>
                </div>
                <div class="header_area" fx:area="header" fx:size="wide,low">
                    <nav 
                        fx:template="top_menu"
						fx:name="Main menu"
                        fx:of="component_section.listing"
                        class="top_menu">
                        <ul>
                            <li fx:template="inactive">
                                <a href="{$url}">{$name}</a>
                            </li>
                            <li fx:template="active" class="active">
                                <a href="{$url}">{$name}</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </header>
        <?
        $bg_color = '#000';
        $bg_image = '';
        
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
        if (!$bg_image && !${"page_bg_image_$page_id"}) {
            $bg_image = $template_dir."images/0.gif";
        }
        ?>
        <section 
            style="background:
                {%page_bg_color_$page_id}<?=$bg_color?>{/%} 
                url('{%page_bg_image_$page_id}<?=$bg_image?>{/%}') no-repeat 50% 0;"
                class="section_inner{if !$sidebar} section_inner_full{/if}">
            <div class="wrapper">
                <div style="clear:both;"></div>
                <!-- For inner pages -->
                <div class="content" fx:area="content">
                    <div 
                        class="gallery fx_not_sortable" 
                        fx:template="index_slider" 
						fx:name="Slider" 
	                    fx:of="component_page.listing">
                        <div 
                            fx:each="$items"
                            class="gallery_item {if $item_is_first} gallery_item_active{/if} slideid{$id}">
                            <img 
                                src="{%bg_photo_$id | 'width:1100,height:530,crop:middle'}<?=$template_dir?>images/img01.jpg{/%}" 
                                alt="" />
                            <div class="slide-text active">
                                <div class="slide-holder">
                                    <h1>{%header_$id type="html"}<?=$item['name']?>{/%}</h1>
                                    <span class="date">
                                        {%date_$id}May 12-16<br />Expidition{/%}
                                    </span>
                                    <div class="info">
                                        {%info_$id}
                                            <dl>
                                                <dt>Difficulty:</dt>
                                                <dd>easy</dd>
                                            </dl>
                                        {/%}
                                    </div>
                                    <div class="holder">
                                        <a href="{$url}" class="more">
                                            {%more_text_$id}More info{/%}
                                        </a>
                                        <a href="{%action_url_$id}" class="btn">
                                            {%action_text_$id}I'm in!{/%}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="switcher">
                            <ul>
                                <li fx:each="$items" class="{if $item_is_first}active{/if} slideid{$id}" data-slideid="{$id}">
                                    <a href="#" title="{$name}">{$item_index}</a>
                                </li>
                            </ul>
                        </div>
                        <a href="#" class="btn-prev">previous</a>
                        <a href="#" class="btn-next">next</a>
                    </div>
                    <div 
                        class="places" 
                        fx:name="Pages by year"
                        fx:template="pages_by_year" 
                        fx:of="component_page.listing">
                        <div 
                            fx:each="{$items->group('publish_date | fx::date : "Y"') as $year => $pages}" 
                            class="col"
                            {if ($pages_index-1) % 3 == 0} style="clear:both;"{/if}>
                            <strong>{$year}</strong>
                            <ul fx:template="$pages">
                                <li fx:template="item">
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
                <div class="sidebar" fx:area="sidebar" fx:if="$sidebar">
                    <ul fx:template="side_menu" fx:of="component_section.listing" fx:name="Side menu" class="jt_side_menu">
                        <li fx:template="inactive"><a href="{$url}">{$name}</a></li>
                        <li fx:template="active"><a href="{$url}"><b>{$name}</b></a></li>
                    </ul>
                </div>
                    
                <!-- For index -->
                <div fx:if="$index_areas" class="section-info holder">
                    <div class="l-side" fx:area="index_left">
                        <ul 
                            fx:template="index_photo_anounces" 
                            fx:of="component_photo.listing" 
                            class="photo_anounces">
                            <li fx:template="item">
                                <?
                                $parent = fx::data('content_page', $item['parent_id']);
                                extract($parent->get_fields_to_show());
                                ?>
                                <a href="{$url}">
                                    <img src="{$photo | 'width:140,height:100'}" alt="" />
                                </a>
                                <span>{$description}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="center">
                        {area id="index_center" /}
                        <div fx:template="block_titled" fx:of="block" class="block_titled">
                            <h2>{%header}Header{/%}</h2>
                            {$content}
                        </div>
                        <ul fx:template="index_link_list" fx:of="component_page.listing" fx:name="Simple link list">
                            <li fx:template="item"><a href="{$url}">{$name}</a></li>
                        </ul>
                        
                    </div>
                    <div class="r-side" fx:area="index_right">
                        
                    </div>
                </div>
            </div>
        </section>
        <section class="footer" fx:if="!$hide_footer">
            <div class="wrapper">
                <div class="contact">
                    <span>
                        {%contacts_label}Contacts:{/%}
                        <span class="phone">{%phone}+7 (495) 440 72 72{/%}</span>
                    </span>
                    <a href="mailto:{%email editable="false"}info@jt.ru{/%}">{%email}</a>
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
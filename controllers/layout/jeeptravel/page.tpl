<html fx:template="page" fx:name="Внутренняя">
    <head>
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
                    <a href="/"><img src="{%logo}<?=$template_dir?>images/logo.png{/%}" alt="" /></a>
                </div>
                <div class="header_area" fx:area="header" fx:size="wide,low">
                    <nav 
                        fx:template="top_menu"
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
        <section class="main-section" fx:if="$full_content">
            <div class="wrapper">
                <!-- для страниц с контентом на всю ширину -->
                <div class="full_content" fx:area="content">
                
                <div class="gallery fx_not_sortable" fx:template="index_slider" fx:of="component_page.listing">
                    <div 
                        fx:each="$items"
                        class="gallery_item {if $item_is_first} gallery_item_active{/if}">
                        <img 
                            src="{%bg_photo_$id}<?=$template_dir?>images/img01.jpg{/%}" 
                            alt="" />
                        <div class="slide-text active">
                            <div class="slide-holder">
                                <h1>{%header_$id}<?=$item['name']?>{/%}</h1>
                                <span class="date">
                                    {%date_$id}14 мая — 10 июня 2013<br />Экспедиция{/%}
                                </span>
                                <div class="info">
                                    {%info_$id}
                                        <dl>
                                            <dt>Сложность маршрута:</dt>
                                            <dd>легкое бездорожье</dd>
                                        </dl>
                                    {/%}
                                </div>
                                <div class="holder">
                                    <a href="{$url}" class="more">
                                        {%more_text_$id}Узнать подробности{/%}
                                    </a>
                                    <a href="{%action_url_$id}" class="btn">
                                        {%action_text_$id}Я поеду!{/%}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="switcher">
                        <ul>
                            <li fx:each="$items" {if $item_is_first}class="active"{/if}>
                                <a href="#" title="{$name}">{$item_index}</a>
                            </li>
                        </ul>
                    </div>
                    <a href="#" class="btn-prev">previous</a>
                    <a href="#" class="btn-next">next</a>
                </div>
                </div>
                <!-- Для главной -->
                <div fx:if="$index_areas" class="section-info holder">
                    <div class="l-side" fx:area="index_left">
                        <ul fx:template="index_photo_anounces" fx:of="component_photo.listing">
                            <li fx:template="item">
                                <?
                                $parent = fx::data('content_page', $item['parent_id']);
                                extract($parent->get_fields_to_show());
                                ?>
                                <a href="{$url}"><img src="{%image_$id}{$photo editable="false"}{/%}" alt="" /></a>
                                <span>{$description}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="center">
                        {area id="index_center" /}
                        <div fx:template="block_titled" fx:of="block" fx:omit="true">
                            <h2>{%header}Заголовок{/%}</h2>
                            {$content}
                        </div>
                        <ul fx:template="index_link_list" fx:of="component_page.listing">
                            <li fx:template="item"><a href="{$url}">{$name}</a></li>
                        </ul>
                        <div fx:template="index_calendar_links" fx:of="component_page.listing" fx:omit="true">
                            <div fx:template="item"><a href="{$url}" class="calendar">{$name}</a></div>
                        </div>
                    </div>
                    <div class="r-side" fx:area="index_right">
                        
                    </div>
                </div>
                <div class="img-list" fx:template="photo_listing" fx:of="component_photo.listing">
                    <div class="images fx_not_sortable" fx:template="$items">
                        <div fx:template="item" class="img-block {if $item_is_first}img-block-active{/if}">
                            <img src="{$photo}" alt="{$description}" />
                            <span class="left">{$description}</span>
                            <span class="right" fx:if="$copy->get_value()">© {$copy}</span>
                        </div>
                    </div>
                    <div class="img-slider" fx:template="$items">
                        <div fx:template="item" class="preview{if $item_is_first} preview-active{/if}">
                            <img src="{$photo}" style="height:100px;" />
                        </div>
                    </div>
                </div>
                <div class="places" fx:template="pages_by_year" fx:of="component_page.listing">
                    <?
                    $years = $items->group(function($item) {
                            return preg_replace("~-.+$~", '', $item['publish_date']); 
                    });
                    ?>
                    <div 
                        fx:each="$years as $year => $pages" 
                        class="col"
                        {if ($pages_index-1) % 3 == 0} style="clear:both;"{/if}>
                        <strong>{$year}</strong>
                        <ul fx:template="$pages">
                            <li fx:template="item">
                                <a href="{$url}">{$name}</a>
                                <div fx:if="$cover && $cover->get_value()">
                                <img src="{$cover}" alt="" style="height:50px;" />
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <?
        $bg_color = '#E9A502';
        $bg_image = '';
        
        foreach($path as $path_page) {
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
        ?>
        <section fx:if="!$full_content"
            style="background:{%page_bg_color_$page_id}<?=$bg_color?>{/%} url('{%page_bg_image_$page_id}<?=$bg_image?>{/%}') no-repeat 50% 0;" 
                class="section_inner{if $skip_sidebar} section_inner_full{/if}">
            <div class="wrapper">
                <div style="clear:both;"></div>
                <!-- Для внутренних -->
                <div class="content" fx:area="content">
                    
                </div>
                <!-- И это для внутренних -->
                <div class="sidebar" fx:area="sidebar" fx:if="!$skip_sidebar">
                    <ul fx:template="side_menu" fx:of="component_section.listing">
                        <li fx:template="inactive"><a href="{$url}">{$name}</a></li>
                        <li fx:template="active"><a href="{$url}"><b>{$name}</b></a></li>
                    </ul>
                </div>
            </div>
        </section>
        <section class="footer" fx:if="!$hide_footer">
            <div class="wrapper">
                <div class="contact">
                    <span>
                        {%contacts_label}Для связи:{/%}
                        <span class="phone">{%phone}+7 (495) 440 72 72{/%}</span>
                    </span>
                    <a href="mailto:{%mail editable="false"}info@jt.ru{/%}">{%mail}info@jt.ru{/%}</a>
                    <span class="copy">
                        {%copy}&copy; JT, <?=date('Y')?><br />
                        Автор фото: <a href="#">Lee Cannon</a>. <a href="#">Лицензия</a>
                        {/%}
                    </span>
                </div>
            </div>
        </section>
    </body>
</html>

<span fx:template="index" fx:name="Главная" fx:omit="true">
    {call id="page"}
        {$index_areas select="true"}
        {$full_content select="true"}
    {/call}
</span>

<span fx:template="full" fx:name="Во всю ширину" fx:omit="true">
    {call id="page"}
        {$index_areas select="false"}
        {$skip_sidebar select="true"}
    {/call}
</span>
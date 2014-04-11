<div class="tiles" fx:template="tiles" fx:of="page.list" fx:name="Tiles">
    <div class="tile" fx:item>
        <div class="pic">
            <a href="{$url}">
                <img   src="{%image_$id | '250*110'}
                                {$image}img/tile_pic.jpg{/$}
                            {/%}"
                    alt="" />
            </a>
        </div>
        <div class="title"><a href="{$url}">{$name}</a></div>
        <div class="description">
            {$description}
        </div>
        <div class="button"><a href="{$url}">{%more_info_$id}More info{/%}</a></div>
    </div>
</div>
    
<div class="material_tiles" fx:template="news_tiles" fx:of="publication.list">
    <div class="material" fx:item="material">
        <div class="title">
            <a href="{$url}">{$name}</a>
            &mdash; <span class="date">{$publish_date | 'd.m.Y'}</span>
        </div>
        <div class="description">{$anounce}</div>
        <div class="read_more"><a href="#">{%read_more}Read more{/%}</a></div>
    </div>
 </div>
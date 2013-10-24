<div 
    fx:template="main_news"
    fx:of="news.list"
    fx:omit="true" >
    <div 
        fx:each="$items"
        class="caption" >
        <h3 class="no-top-margin"><a href="{$url}">{$name}</a></h3>
        <div>
            {$anounce}
        </div>
        <div class="text-right text-muted">{$publish_date|'d.m.Y'}</div>
    </div>
</div>
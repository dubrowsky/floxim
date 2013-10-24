<div 
    fx:template="news_list"
    fx:of="news.list"
    fx:omit="true">
    <div fx:template="item" 
        class="news-item clearfix">
        <div class="col-md-3">
            <img src="{$image|'width:200px,crop:middle'}" alt="{$name}">
        </div>
        <div class="col-md-8 col-md-offset-1">
            <h3 class="no-top-margin"><a href="{$url}">{$name}</a></h3>
            <h5>{$publish_date|'d.m.Y'}</h5>
            <div>{$anounce}</div>
            {call id="component_classifier.entity_classifier"}{$items select="$tags" /}{/call}
        </div>
    </div>
</div>
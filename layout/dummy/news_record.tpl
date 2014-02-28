<div 
    fx:template="news_record"
    fx:of="news.record"
    fx:omit="true">
    <div fx:item 
        class="clearfix news-record">
        <h1 class="no-top-margin">{$name}</h1>
        <h3>{$publish_date|'d.m.Y'}</h3>
        <div>
            <img src="{$image}" alt="{$full_name}" class="pull-left">
            <div>{$text}</div>
            {call id="component_classifier.entity_classifier"}{$items select="$item['tags']" /}{/call}
        </div>
    </div>
</div>
<div class="news_record">
    <div class="news" fx:item>
        <div class="pic">
            <img src="{$image}" alt="{$name}" />
        </div>
        <div class="description">
            {$description}
        </div>
        {call id="component_classifier.entity_classifier"}{$items select="$item['tags']" /}{/call}
    </div>
</div>
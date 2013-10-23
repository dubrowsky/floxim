<div class="video_record">
    <div class="video" fx:template="item">
        <div class="video_code">
            {$embed_html}
        </div>
        <div class="description">
            {$description}
        </div>
        {call id="component_classifier.entity_classifier"}{$items select="$item['tags']" /}{/call}
    </div>
</div>
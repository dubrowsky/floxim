<div fx:template="list" class="video_list">
    <div fx:item class="video">
        <h2><a href="{$ur}">{$name}</a></h2>
        <div class="video">
            {$embed_html}
        </div>
        {call id="component_classifier.entity_classifier"}{$items select="$tags" /}{/call}
    </div>

    {call id="component_content.pagination" /}
</div>
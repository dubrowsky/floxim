<div fx:template="listing" class="photo_list">
    <div fx:template="item" class="photo">
        <a class="pic" href="{$url}">
            <img src="{$image}" alt="{$name}" />
        </a>
        
        {call id="component_classifier.entity_classifier"}{$items select="$tags" /}{/call}
    </div>

    {call id="component_content.pagination" /}
</div>
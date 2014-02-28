<div fx:template="list" class="photo_list">
    <div fx:item class="photo">
        <a class="pic" href="{$url}">
            <img src="{$image}" alt="{$name}" />
        </a>
        {call id="component_classifier.entity_classifier"}{$items select="$tags" /}{/call}
    </div>
    {call id="component_content.pagination" /}
</div>
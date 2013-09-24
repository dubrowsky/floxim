<div fx:template="listing" class="post_list">
    <div fx:template="item" class="post">
        <h2><a href="{$url}">{$name}Unnamed article{/$}</a></h2>
        <div class="date">
            <span>{$publish_date | 'd.m.Y'}</span>
        </div>
        
        <div fx:if="$image" class="pic">
            <img src="{$image | 'max-width:100,max-height:100'}" alt="{$name}" />
        </div>
        
        <div class="anounce">{$anounce}</div>
        {call id="component_tag.entity_tags"}{$items select="$tags" /}{/call}
        <div class="comments_counter">Comments: {$comments_counter}0{/$}</div>
    </div>

    {call id="component_content.pagination" /}
</div>
<div fx:template="tag_list" fx:of="listing" fx:name="Tag list" class="tag_list">
    <span 
        fx:template="item" 
        class="tag">
            <a style="white-space:nowrap;" href="{$url}">{$name}</a>
            <sup class="counter">{$counter}</sup>
    </span>
    <span fx:template="separator" fx:omit="true"> </span>
</div>
    
<div 
    fx:template="entity_tags" 
    fx:of="listing" 
    fx:name="Tags for entity" 
    class="entity_tags">
        {%tags_label}Tags:{/%} 
        <a fx:template="item" href="{$url}">
             {$name}
        </a>
        <span fx:template="separator">, </span>
</div>
<div class="tag_cloud">
    <span 
        fx:template="item" 
        fx:if="$item['counter'] > 0"
        class="tag">
            <a style="white-space:nowrap;" href="{$url}">{$name}</a>&nbsp;({$counter})
    </span>
    <span fx:template="separator" fx:omit="true"> </span>
</div>
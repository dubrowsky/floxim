<div class="tag_cloud">
    <span 
        fx:template="item" 
        fx:if="$item['counter'] > 0"
        class="tag">
            <a style="white-space:nowrap;" href="{$url}">{$name}</a>&nbsp;<sup class="counter">{$counter}</sup>
    </span>
    <span fx:template="separator" fx:omit="true"> </span>
</div>
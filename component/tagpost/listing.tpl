<div class="posttags">
    Метки:
    <span fx:template="item">
        <a fx:each="$tag" href="{$url}">{$name}</a>
        <span fx:if="$comment">({$comment})</span>
    </span>
    <span fx:template="separator" fx:omit="true">, </span>
</div>
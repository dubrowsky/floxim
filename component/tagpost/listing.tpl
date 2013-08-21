<div class="posttags" fx:if="count($items)">
    {%tags_label}Tags:{/%}
    <span fx:template="item">
        <span fx:if="!$tag">???</span>
        <a fx:each="$tag" href="{$url}">{$name}</a>
        <span fx:if="$comment">({$comment})</span>
    </span>
    <span fx:template="separator" fx:omit="true">, </span>
</div>
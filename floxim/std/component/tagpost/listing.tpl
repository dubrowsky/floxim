{*<div class="posttags" fx:if="count($items)">
    {%tags_label}Tags:{/%}
    <span fx:template="item">
        <a fx:each="$tag" href="{$url}">{$name}</a>
    </span>
    <span fx:template="separator">, </span>
</div>*}
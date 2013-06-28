<div class="posttags">
    Метки:
    <span fx:template="item">
        <a fx:each="$tag" href="{$url}">{$name}</a>
    </span>
    <span fx:template="separator" fx:omit="true">, </span>
</div>
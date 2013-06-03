<div class="post_list">
    <div fx:template="item" class="post">
        <h1><a href="{$url}">{$header}</a></h1>
        <p>{$anounce}</p>
        <div fx:template="$tags">
            {%tags_label}Метки:{/%}
            <a fx:template="item" href="{$url}">{$name}</a>
            <span fx:template="separator">, </span>
        </div>
    </div>
    <hr fx:template="separator" />
</div>
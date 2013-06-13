<div fx:template="listing" class="post_list">
    <div fx:template="item" class="post">
        <h1><a href="{$url}" title="{$header}">{$header}</a></h1>
        <div class="date">{$publish_date}</div>
        <p>{$anounce}</p>
        
        <div fx:template="$tags">
            {%tags_label}Метки:{/%}
            <a fx:template="item" href="{$url}">
                 {$name}
            </a>
            <span fx:template="separator">, </span>
        </div>
    </div>
    <hr fx:template="separator" />
    <div class="pagination" fx:template="$pagination">
        <a fx:template="inactive" href="{$url}">{$page}</a>
        <b fx:template="active">{$page}</b>
        <span fx:template="separator"> | </span>
    </div>
</div>
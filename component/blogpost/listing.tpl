<div fx:template="listing" class="post_list">
    <div fx:template="item" class="post">
        <h2><a href="{$url}">{$name}</a></h2>
        <div class="date">
        	<span>{$publish_date | 'd.m.Y'}</span>
		</div>
        
        <div fx:if="$image" class="pic">
            <img src="{$image | 'w:100,h:100'}" alt="{$name}" />
        </div>
        
        <div class="anounce">{$anounce}</div>
        
        <div fx:template="$tags" class="tags">
            {%tags_label}Tags:{/%} 
            <a fx:template="item" href="{$url}">
                 {$name}
            </a>
            <span fx:template="separator">, </span>
        </div>
    </div>

    <div fx:template="$pagination" class="pagination">
        <a fx:template="inactive" href="{$url}">{$page}</a>
        <b fx:template="active">{$page}</b>
        <span fx:template="separator"> | </span>
    </div>
</div>
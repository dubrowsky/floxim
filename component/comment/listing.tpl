<div fx:template="listing" class="comment_list">
	 {css}all.css{/css}	
    <div fx:template="item" class="comment">
        <span fx:if="$_is_admin && !$is_moderated" class="is_moderated" data-is_moderated="{$is_moderated}">Waiting for moderation</span>
        <h3>{$user_name}Anonymous{/$}</h3>
       	<div class="date">
        		<span>{$publish_date | 'd.m.Y'}</span>
		</div>
        <div class="text">{$comment_text}</div>
    </div>
</div>
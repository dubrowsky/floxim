<div
	fx:template="news_list"
    fx:of="news.list" 
    class="news-list">
	<div 
		fx:template="item" 
		class="news-list-item">
		<div class="date">{$publish_date|'d.m.Y'}</div>
		<div class="announce">
			<div>{$anounce}</div>
			<a href="{$url}">{$name}</a>
		</div>
		{if $tags}
		<a class="badge" fx:each="$tags->first()">{$name}</a>
		{/if}
		<div style="clear:both;"></div>
	</div>
	<a class="more" href="{%more}">{%More_news}More news{/%}</a>
</div>
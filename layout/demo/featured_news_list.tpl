<div
	fx:template="featured_news_list"
    fx:of="news.list"
    class="featured-list four-items">
	<div 
		fx:template="item" 
		class="featured-item {if $item_index%4 == 0}last{/if}">
		<img fx:if="$image" src="{$image|'width:425,height:300'}">
		<div class="caption">
			<div>{$anounce}</div>
			<a href="{$url}">{$name}</a>
		</div>
	</div>

	<a class="more" href="{%more}">{%More_news}More news{/%}</a>
	<div style="clear:both;"></div>
</div>
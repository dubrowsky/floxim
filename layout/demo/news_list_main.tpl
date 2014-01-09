<div
	fx:template="news_list_main"
    fx:of="news.list"
    class="news-list">
	<div
		fx:template="item" 
		class="news-list-item">
		<div class="photo">
			<img src="{$image}" alt="{$name}">
		</div>
		<div class="caption">
			<h2><a href="{$url}">{$name}</a></h2>
			<div class="anounce">
				{$anounce}
			</div>
		</div>
		<div style="clear:both;"></div>
	</div>
</div>

<div
	fx:template="news_record"
    fx:of="news.record"
    fx:omit="true">
	<div
		fx:template="item"
	    class="news-record">
		<div class="photo">
			<img src="{$image}" alt="{$name}">
		</div>
		<div class="caption">
			<h2>{$name}</h2>
			<div class="text">
				{$text}
			</div>
		</div>
	</div>
</div>
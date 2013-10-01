<div fx:template="listing" class="award_list">
	{css}listing.css{/css}
	<div fx:template="item" class="award">
	   <div class="year">{$year}2000{/$}</div>
		<div class="image">
			<img src="{$image}" alt="" />
		</div>
		<div class="description">
		    <a href="{$url}"><h2>{$name}Name{/$}</h2></a>
			{$description}Description{/$}
		</div>
	    <div class="clear"></div>
	</div>

    {call id="component_content.pagination" /}
</div>
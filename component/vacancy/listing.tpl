<div fx:template="listing" class="vacancy_list">
	{css}listing.css{/css}
	<div fx:template="item" class="vacancy">
	   <a href="{$url}"><h2>{$position}Position{/$}</h2></a>
	</div>

    {call id="component_content.pagination" /}
</div>
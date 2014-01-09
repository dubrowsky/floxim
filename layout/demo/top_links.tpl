<div 
	fx:omit="true"
	fx:template="top_links"
	fx:of="section.list">
	<a fx:each="$items" href="{$url}">{$name}</a>
</div>

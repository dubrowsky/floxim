<div 
    fx:template="featured_products_list"
    fx:of="product.list"
    class="featured-list">
	<div fx:each="$items" class="featured-item {if $item_index%3==0}last{/if}">
		<a href="{$url}">
			<img src="{$image|'width:430,height:430'}">
		</a>
		<div class="caption">
			{$price}
		</div>
	</div>
	<div style="clear:both;"></div>
</div>
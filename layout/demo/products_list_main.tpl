<div
	fx:template="products_list_main"
    fx:of="product.list"
    class="product-list">
	<div
		fx:item 
		class="product-list-item">
		<div class="photo">
			<img src="{$image}" alt="{$name}">
		</div>
		<div class="caption">
			<h2><a href="{$url}">{$name}</a></h2>
			<div class="desc">
				{$short_description}
			</div>
			<div class="price">{$price}</div>
		</div>
		<div style="clear:both;"></div>
	</div>
</div>
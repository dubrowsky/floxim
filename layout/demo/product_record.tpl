<div 
    fx:template="product_record"
    fx:of="product.record"
    fx:omit="true">
    <div
    	fx:template="item" 
    	class="product-record">
		<div class="photo">
            <img src="{$image}" alt="{$name}">
		</div>
		<div class="caption">
			<h2>{$name}</h2>
			<h3>{$reference}</h3>
			<div class="desc">{$description}</div>
			<div class="price">{$price}</div>
		</div>
	</div>
</div>
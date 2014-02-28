<div
        fx:template="product_record"
        fx:name="Product Record"
        fx:of="product.record"
        fx:omit="true">
    <div
        fx:item
        class="product-record">
        <div class="slider">
            <div class="slide active">
                <img src="{$image|'crop:middle,height:360,width:735'}<?=$template_dir?>/img/120110-G-IA163-068-Healy-escorts-Renda.jpg{/$}">
            </div>
        </div>
        <div class="short-desc">
            <div class="text">
                {$short_description}<p>Description</p>{/$}
            </div>
            <div class="price">{$price} {%product_currency}${/%}</div>
            <div style="clear: both;"></div>
        </div>
        <div style="clear:both;"></div>
        <div class="desc">
            {$description}
        </div>
    </div>
</div>
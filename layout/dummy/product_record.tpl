<div
    fx:template="product_record"
    fx:of="product.record"
    fx:omit="true" >
    <div
        fx:template="item"
        class="col-xs-12">
        <h1 class="no-top-margin">{$name}</h1>
        <div class="col-xs-6">
            <img src="{$image}" alt="{$name}">
        </div>
        <div class="col-xs-6">
            <h4 class="no-top-margin">{$reference}</h4>
            <div>
                {$description}
            </div>
            <h3>{$price}</h3>
        </div>
    </div>
</div> 
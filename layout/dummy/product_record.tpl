<div
    fx:template="product_record"
    fx:of="product.record"
    fx:omit="true" >
    <div
        fx:template="item"
        class="col-md-12">
        <h1 class="no-top-margin">{$name}</h1>
        <div class="col-md-6">
            <img src="{$image}" alt="{$name}">
        </div>
        <div class="col-md-6">
            <h4 class="no-top-margin">{$reference}</h4>
            <div>
                {$description}
            </div>
            <h3>{$price}</h3>
        </div>
    </div>
</div> 
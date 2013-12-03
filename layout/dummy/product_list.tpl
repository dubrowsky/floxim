<div
    fx:template="products_list"
    fx:of="product.list"
    fx:omit="true">
    <a
        fx:each="$items"
        href="{$url}"
        class="col-xs-3 thumbnail">
        <img src="{$image|'height:190,crop:middle'}" alt="{$name}">
        <div class="caption text-center">
            <h4>{$name}</h4>
            <h5>{$price}</h5>
        </div>
    </a>
</div>   
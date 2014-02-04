<div
    fx:template="featured_list"
    fx:name="Featured List"
    fx:of="page.list"
    fx:size="low,wide"
    data-four_items="{%four_items type='bool' title='Four items'}0{/%}"
    class="featured-list  {if $four_items}four-items{/if}">
    <a
        fx:each="$items"
        href="{$url}#{/$}"
        class="featured-list-item">
        <img fx:if="$image" src="{$image type='image'}<?=$template_dir?>/img/120110-G-IA163-068-Healy-escorts-Renda.jpg{/$}">
        <img fx:if="!$image" src="{%image_$id}<?=$template_dir?>/img/120110-G-IA163-068-Healy-escorts-Renda.jpg{/%}">
        <div class="caption">
            <h3>
                <p>{$name}</p>
                <div style="clear:both;"></div>
            </h3>
            <div fx:if="$price" class="price">{$price} {$currency}${/$}</div>
            <div style="clear:both;"></div>
        </div>
    </a>
    <div style="clear:both;"></div>
</div>
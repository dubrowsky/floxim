<div
    fx:template="breadcrumbs"
    fx:of="section.breadcrumbs"
    fx:name="Breadcumbs"
    class="breadcrumbs">
    {each $items}
        <h2 fx:if="$item_index == 2"><a fx:omit="$is_current" href="{$url}">{$name}</a></h2>
        <h3 fx:if="$item_index > 2">
            <a fx:omit="$is_current" href="{$url}">{$name}</a>
        </h3>
        <span fx:template="separator" fx:if="$item_index > 2">/</span>
    {/each}
</div>

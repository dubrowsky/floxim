<div
    fx:template="breadcrumbs"
    fx:of="section.breadcrumbs"
    fx:name="Breadcumbs"
    class="breadcrumbs">
    <? $page_id = fx::env('page')->get('id'); ?>
    {each $items}

        <h2 fx:if="$item_index == 2"><a fx:omit="$id != $page_id" href="{$url}">{$name}</a></h2>

        <h3 fx:if="$item_index > 2">
            <a fx:omit="!$item_is_last" href="{$url}">{$name}</a>
        </h3>
        <span fx:template="separator" fx:if="$item_index > 2">/</span>
    {/each}
</div>

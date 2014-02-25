<div fx:template="breadcrumbs" class="breadcrumbs">
    <a fx:item href="{$url}">{$name}</a>
    {%separator} / {/%}
    <h1 fx:item="$is_active">{$name}</h1>
</div>
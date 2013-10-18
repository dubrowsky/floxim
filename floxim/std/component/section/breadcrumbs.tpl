<div fx:template="breadcrumbs" class="breadcrumbs">
    <a fx:template="inactive" href="{$url}">{$name}</a>
    <span fx:template="separator" fx:omit="true"> / </span>
    <h1 fx:template="active">{$name}</h1>
</div>
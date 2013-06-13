<div fx:template="breadcrumbs" class="breadcrumbs">
    <span fx:template="inactive"><a href="{$url}">{$name}</a></span>
    <span fx:template="separator" fx:omit="true"> / </span>
    <h1 fx:template="active">{$name}</h1>
</div>
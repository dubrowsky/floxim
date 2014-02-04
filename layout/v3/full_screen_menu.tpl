
<div
    fx:template="full_screen_menu"
    fx:name="Full screen menu"
    fx:of="page.list"
    <?$cid = fx::env('page')->get('id'); ?>
    style="background-image: url({%bg_$cid}<?=$template_dir?>img/120110-G-IA163-068-Healy-escorts-Renda.jpg{/%});"
    class="full-back">

    <div class="caption">
        <h2>
            {%header_$cid}
            <p>Hans Island,</p>
            {/%}
            <div style="clear:both;"></div>
        </h2>
        <div class="text">
            {%caption_$cid}
            <p>This writing is about</p>
            {/%}
            <a class="go" fx:each="$items[0]" href="{$url}">Go</a>
        </div>
    </div>
    {if count($items)>1}
    <ul class="side-menu">
        <li
            fx:each="$items"
            class="side-menu-item {if $active}active{/if}">
            <a href="{$url}">{$name}</a>
        </li>
    </ul>
    {/if}
</div>
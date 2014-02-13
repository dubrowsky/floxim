{template id="full_screen_menu" name="Full screen menu" of="page.list"}
<?
$items = $this->v('items');
$this->set_var('ai', count($items) > 1 ?  $items->find_one('is_active', true) : $items->first());
$this->set_var('cid', fx::env('page_id'));
?>
<div
    style="background-image: url({%bg_$cid}<?=$template_dir?>img/120110-G-IA163-068-Healy-escorts-Renda.jpg{/%});"
    class="full-back">

    <div class="caption">
        <div class="h2">
            {%header_$cid}
            <p>{$ai.name}Place header here{/$}</p>
            {/%}
        </div>
        <div class="text">
            <p>{%caption_$cid type="html"}This writing is on{/%}</p>
            <a fx:if="$ai" class="go" href="{$ai.url}#content">Go</a>
        </div>
    </div>
    
    <ul fx:if="count($items) > 1" class="side-menu">
        <li
            fx:each="$items"
            class="side-menu-item {if $is_active}active{/if}">
            <a href="{$url}">{$name}</a>
        </li>
    </ul>
    <a name="content" class="content_anchor"></a>
</div>
{/template}
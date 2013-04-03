<div class="posttags">
    Метки:
    <span fx_render=".">
        <a href="<?=$item['tag']->get_page()->get('url')?>">{$item.tag.name}</a>
        {if test="!$item_is_last"}, {/if}
    </span>
</div>
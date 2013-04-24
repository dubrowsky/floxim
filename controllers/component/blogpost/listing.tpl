<div class="post" fx_render=".">
    <h1><a href="{$url}">{$header}</a></h1>
    <p>{$anounce}</p>
    <hr />
    <div fx:if="$tags">
        Метки: 
        <span fx_render="$tags" fx_render_as="$tag">
            <a href="{$url}">{$name}</a>{if test="!$tag_is_last"}, {/if}
        </span>
    </div>
    <div class="info">
        {*
        Автор: {$author}<br />
        Дата публикации: {$author}<br />
        Комментариев: <?=count($post['comments'])?><br />
        Настроение: <?=$post['mood']?><br />
        Музыка: <?=$post['music']?><br />
       *}
    </div>
</div>
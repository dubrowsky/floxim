<div class="post" fx_render=".">
    <h1><a href="{$url}">{$header}</a></h1>
    <p>{$anounce}</p>
    <hr />
    <p>Тэги:</p>
    <?
            dev_log($tags);
    ?>
    <span fx_render="$tags" fx_render_as="$tag">
        <a href="{$url}">{$name}</a>{if test="!$tag_is_last"}, {/if}
    </span>
    <div class="info">
        {*
        Автор: {$author}<br />
        Дата публикации: {$author}<br />
        Комментариев: <?=count($post['comments'])?><br />
        Настроение: <?=$post['mood']?><br />
        Музыка: <?=$post['music']?><br />
       *}
    </div>
    <a href="#">вконтакте{*<div class="social" id="vk">&nbsp;</div>*}</a>
    <a href="#">livejournal{*<div class="social" id="lj">&nbsp;</div>*}</a>
    <a href="#">rss{*<div class="social" id="rss">&nbsp;</div>*}</a>
</div>
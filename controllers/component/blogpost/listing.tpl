<div class="post" fx_render=".">
    <h1><a href="{$f_url}">{$f_header}</a></h1>
    <p>{$f_anounce}</p>
    <hr />
    <p>Тэги:</p>
    <?
            dev_log($f_tags);
    ?>
    <span fx_render="$f_tags" fx_render_as="$tag">
        <a href="{$f_url}">{$f_name}</a>{if test="!$tag_is_last"}, {/if}
    </span>
    <div class="info">
        {*
        Автор: {$f_author}<br />
        Дата публикации: {$f_author}<br />
        Комментариев: <?=count($post['comments'])?><br />
        Настроение: <?=$post['mood']?><br />
        Музыка: <?=$post['music']?><br />
       *}
    </div>
    <a href="#">вконтакте{*<div class="social" id="vk">&nbsp;</div>*}</a>
    <a href="#">livejournal{*<div class="social" id="lj">&nbsp;</div>*}</a>
    <a href="#">rss{*<div class="social" id="rss">&nbsp;</div>*}</a>
</div>
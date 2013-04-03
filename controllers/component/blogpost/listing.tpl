<?
dev_log($this);
?>
<div class="post" fx_render=".">
    <?
    dev_log(get_defined_vars());
    ?>
    <h1><a href="{$f_url}">{$f_header}</a></h1>
    <p>{$f_anounce}</p>
    <div class="info">
        {*
        Автор: {$f_author}<br />
        Дата публикации: {$f_author}<br />
        Комментариев: <?=count($post['comments'])?><br />
        Настроение: <?=$post['mood']?><br />
        Музыка: <?=$post['music']?><br />
       *}
    </div>
    <a href="#"><div class="social" id="vk">&nbsp;</div></a>
    <a href="#"><div class="social" id="lj">&nbsp;</div></a>
    <a href="#"><div class="social" id="rss">&nbsp;</div></a>
</div>
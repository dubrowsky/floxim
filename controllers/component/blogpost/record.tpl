<div class="post" fx_render=".">
    <p>{$text}</p>

    <div class="info">
        <?=fx_lang('Автор:');?> <?=$posts[$_GET['p']-1]['author']?><br />
        <?=fx_lang('Дата публикации:');?> <?=date('d.m.Y',$posts[$_GET['p']-1]['public_date'])?><br />
        <?=fx_lang('Комментариев:');?> <?=count($posts[$_GET['p']-1]['comments'])?><br />
        <?=fx_lang('Настроение:');?> <?=$posts[$_GET['p']-1]['mood']?><br />
        <?=fx_lang('Музыка:');?> <?=$posts[$_GET['p']-1]['music']?><br />
    </div>
    <a href="#"><?=fx_lang('вконтакте');?>{*<div class="social" id="vk">&nbsp;</div>*}</a>
    <a href="#">livejournal{*<div class="social" id="lj">&nbsp;</div>*}</a>
    <a href="#">rss{*<div class="social" id="rss">&nbsp;</div>*}</a>
</div>
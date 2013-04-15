тут по идее должен быть пост
<?php
echo "<pre>";
echo "\n=======================\n";
$arr = get_defined_vars();
print_r($arr);
echo "\n=======================\n";
echo "</pre>";

?>

<div class="post" fx_render=".">

    <h1><a href="{$f_url}">{$f_header}</a></h1>
    <p>{$f_full_text}</p>
    <div class="info">
        {*
        Автор: <?=$posts[$_GET['p']-1]['author']?><br />
        Дата публикации: <?=date('d.m.Y',$posts[$_GET['p']-1]['public_date'])?><br />
        Комментариев: <?=count($posts[$_GET['p']-1]['comments'])?><br />
        Настроение: <?=$posts[$_GET['p']-1]['mood']?><br />
        Музыка: <?=$posts[$_GET['p']-1]['music']?><br />
        *}
    </div>
    <a href="#"><div class="social" id="vk">&nbsp;</div></a>
    <a href="#"><div class="social" id="lj">&nbsp;</div></a>
    <a href="#"><div class="social" id="rss">&nbsp;</div></a>
</div>
Hello, i am blog record!

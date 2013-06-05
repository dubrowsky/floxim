<?php dev_log('items in template',$input); ?>
<div class="blog_calendar">
    <h2>Посты по месяцам:</h2>
    {each select="$input['years']" as="$item"}
        {*<a href="{$item['url']}">{$item['name']}</a>*}
        <h3><a href="{$url}">{$name}</a></h3>
    {/each}
        <ul>
            {each select="$input['months']" as="$item"}
            <li>
                <a href="{$url}">{$name}</a>&nbsp;({$post_counter})
            </li>
            {/each}
        </ul>

</div>
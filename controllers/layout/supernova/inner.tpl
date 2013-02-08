{call id="wrap"}
{$content}
    <div class="sidebar">
        {area id="sidebar"}
        
        {template id="wrap_simple" name="Простой блок" for="wrap"}
            <div class="block">
                {$content}
            </div>
        {/template}
        {template id="wrap_titled" name="Блок с заголовком" for="wrap"}
            <div class="block titled_block" style="border-color:{$color}#900{/$color};">
                 <div class="title" style="background:{$color};">
                    {$title}Заголовок{/$title}
                 </div>
                 <div class="data">{$content}</div>
            </div>
        {/template}
    </div>
    <div class="content content_with_side">
        {area id="content"}
    </div>
{/$content}
{/call}
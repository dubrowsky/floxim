{template id="listing"}
    <div class="menu">
        {render select="$items"}
            <div class="menu_item">
                <a href="{$f_url}">{$f_name}</a>
                {%separator}&bull;{/%separator}
            </div>
        {/render}
        {render select="$items" test="$item->is_selected()"}
            <div class="menu_item">
                <a href="{$f_url}">{$f_name}</a>
                {%separator}
                <div class="submenu">
                    {record}
                        <a href="{$f_url}">{$f_name}</a>
                        {%subseparator}|{/%subseparator}
                    {/record}
                </div>
            </div>
        {/render}
    </div>
{/template}
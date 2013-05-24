{template id="listing"}
    <div class="menu" fx_template="listing">
        {*render*}
            <div id="menu">
                <ul>
                    <li fx_render=".">
                        <a href="{$url}">
                            <span class="mw"><span class="mw">
                                <span style="{if test="$item['active']"}color: #ff0000{/if}">{$name}</span>
                            </span></span>
                        </a>
                    </li>
                </ul>
            </div>

            {*
            <div class="menu_item"
                 {if test="$item['active']"}
                     style="background:#ff0000;"
                 {elseif test="$item_is_odd"}
                     style="
                        background:{%odd_bg title="fon_nechetnogo_elementa"}#C00{/%odd_bg};
                        color:{%odd_color}#FF0{/%odd_color};"
                 {/if}>
                <a href="{$url}" fx_var="$name">Раздел</a>
                {if test="$item['childs']"}
                    <ul>
                        {render select="$item['childs']"}
                            <li><a href="{$url}">{$name}</a></li>
                        {/render}
                    </ul>
                {/if}
                <span fx_if="!$item_is_last" fx_var="separator">&bull;</span>
            </div>
            *}
        {*/render*}
    </div>
{/template}

{template id="breadcrumbs"}
    <div class="breadcrumbs">
        {render}
            <span fx_if="!$item_is_last"><a href="{$url}">{$name}</a>{%separator} -> {/%}</span>
            <h1 fx_if="$item_is_last">{$name}</h1>
        {/render}
    </div>
{/template}
{template id="listing"}
    <div class="menu" fx_template="listing">
        {render}
            <div class="menu_item" 
                 {if test="$item_is_odd"}
                     style="
                        background:{%odd_bg title="fon_nechetnogo_elementa"}#C00{/%odd_bg};
                        color:{%odd_color}#FF0{/%odd_color};"
                 {/if}>
                <a href="{$f_url}" fx_var="$f_name">Раздел</a>
                <span fx_if="!$item_is_last" fx_var="separator">&bull;</span>
            </div>
        {/render}
    </div>
{/template}

{template id="breadcrumbs"}
    {render}
        <span fx_if="!$item_is_last"><a href="{$f_url}">{$f_name}</a>{%separator} -> {/%}</span>
        <h1 fx_if="$item_is_last">{$f_name}</h1>
    {/render}
{/template}
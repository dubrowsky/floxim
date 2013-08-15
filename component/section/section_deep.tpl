<div
    fx:template="listing_deep" 
    fx:of="component_section.listing"
    fx:name="Deep menu"
    class="deep_menu">
        {css}deep.css{/css}
        
        {call id="recursive_menu"}{$level select="1"}{/call}
        
        <ul fx:template="recursive_menu" fx:if="$items">
            <li fx:each="$items" class="menu_item_{$level}">
                <a href="{$url}" {if $active}class="active"{/if}>{$name}</a>
                {call id="recursive_menu"}
                    {$items select="$children"}
                    {$level select="$level+1"}
                {/call}
            </li>
        </ul>
</div>
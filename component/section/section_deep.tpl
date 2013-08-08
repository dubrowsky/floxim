<ul 
    fx:template="listing_deep" 
    fx:of="component_section.listing"
    fx:name="Deep menu"
    fx:if="$items"
    class="deep_menu">
        <li fx:template="item" style="padding-left:15px;">
            <a href="{$url}">{$name}</a>
            {call id="listing_deep"}
                {$items select="$submenu"}
            {/call}
        </li>
</ul>
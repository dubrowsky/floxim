<ul
    fx:template="top_menu"
    fx:name="Main menu"
    fx:of="section.list"
    class="main-menu">
	<li 
            fx:each="$items" 
            class="menu-item {if $submenu} dropdown{/if} {if $is_active}current{/if}">
            <a href="{$url}">{$name}</a>
            <ul fx:with-each="$submenu" class="menu-sub-items">
                <li fx:item class="{if $is_active}active {/if}menu-sub-item">
                    <a href="{$url}">{$name}</a>
                </li>
            </ul>
	</li>
</ul>
<ul
    fx:template="main_menu"
    fx:name="Main menu"
    fx:of="section.list"
    class="main-menu">
    <li
        fx:each="$items"
        class="main-menu-item {if $children} dropdown{/if} {if $is_active}current{/if}">
        <a href="{$url}">{$name}</a>
        <div
            fx:if="$children"
            class="width-helper">
            <ul class="sub-menu">
                <li
                    fx:each="$children as $child"
                    class="sub-menu-item {if $is_active} active {/if}">
                    <a href="{$url}">{$name}</a>
                </li>
            </ul>
        </div>
    </li>
</ul>
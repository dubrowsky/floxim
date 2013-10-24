<ul
    fx:template="top_menu"
    fx:name="Main menu"
    fx:of="section.list"
    class="nav navbar-nav">
    <li fx:each="$items" class="{if $active} active{/if}{if $children} dropdown{/if}" >
        <a href="{$url}" {if $children}class="dropdown-toggle" {/if}>{$name}</a>
        <ul fx:if="$children" class="dropdown-menu">
            <li fx:each="$children" fx:prefix="child">
                <a href="{$child_url}">{$child_name}</a>
            </li>
        </ul>
    </li>
</ul>
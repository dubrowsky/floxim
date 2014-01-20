<ul
    fx:template="top_menu"
    fx:name="Main menu"
    fx:of="section.list"
    class="main-menu">
	<li 
		fx:each="$items" 
		class="menu-item {if $children} dropdown{/if} {if $item->is_active()}current{/if}">
		<a href="{$url}">{$name}</a>
		<ul fx:if="$children" class="menu-sub-items">
			<li fx:each="$children as $child" fx:prefix="child" class="{if $child->is_active()} active {/if} menu-sub-item">
				<a href="{$child_url}">{$child_name}</a>
			</li>
		</ul>
	</li>
</ul>
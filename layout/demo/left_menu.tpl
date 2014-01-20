<ul 
    fx:template="left_menu"
	fx:name="Left menu"
    fx:of="page.list"
    class="sub-menu">
	<li fx:each="$items" class="sub-menu-item {if $item->is_active()}active{/if}">
		<a href="{$url}">{$name}</a>
	</li>
</ul>
<ul 
    fx:template="left_menu"
	fx:name="Left menu"
    fx:of="section.list"
    class="list-group">
        <li fx:template="inactive" class="list-group-item"><a href="{$url}">{$name}</a></li>
        <li fx:template="active" class=" list-group-item active"><a class="active" href="{$url}">{$name}</a></li>
</ul>
<ul 
    fx:template="categories_menu"
	fx:name="Categories Menu"
    fx:of="product_category.list"
    class="list-group">
        <li fx:template="inactive" class="list-group-item"><a href="{$url}">{$name}</a></li>
        <li fx:template="active" class=" list-group-item active"><a class="active" href="{$url}">{$name}</a></li>
</ul>
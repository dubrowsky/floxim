<div 
	fx:template="simple_img"
	fx:of="photo.list" 
	class="img">
	<img fx:each="$items" src="{$photo}" alt="{$copy}"/>
</div>
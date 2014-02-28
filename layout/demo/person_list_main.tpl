<div
	fx:template="person_list_main"
    fx:of="person.list"
    class="person-list">
	<div
		fx:item 
		class="person-list-item">
		<div class="photo">
			<img src="{$photo}" alt="{$name}">
		</div>
		<div class="caption">
			<h2><a href="{$url}">{$full_name}</a></h2>
			<h3>{$position}</h3>
			<div class="anounce">
				{$short_description}
			</div>
		</div>
		<div style="clear:both;"></div>
	</div>
</div>
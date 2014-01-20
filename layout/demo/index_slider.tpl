<div
    fx:template="index_slider" 
    fx:name="Slider" 
    fx:of="page.list"
    fx:size="high,wide" 
    class="slider fx_not_sortable">
	<div
		fx:each="$items" 
		class="slide {if $item_is_first}slide_active{/if} slideid{$id}">
		<a href="{$url}"><img src="{%bg_photo_$id}<?=$template_dir?>images/img01.jpg{/%}" alt="{$name}"></a>
		<div class="caption">
			<h2>
				<p>{%header_$id}{/%}</p>
			</h2>
			<div class="text">
				{%text_$id}<p></p>{/%}
			</div>
		</div>
	</div>
    <div class="switcher">
        <ul>
            <li fx:each="$items" class="{if $item_is_first}active{/if} slideid{$id}" data-slideid="{$id}">
                <a href="#" title="{$name}">{$item_index}</a>
            </li>
        </ul>
    </div>
    <a href="#" class="btn-prev">previous</a>
    <a href="#" class="btn-next">next</a>
</div>
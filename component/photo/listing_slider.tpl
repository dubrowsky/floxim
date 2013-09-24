<div 
	 class="img-list" 
	fx:template="listing_slider" fx:of="component_photo.listing">
	
	{js}
	FX_JQUERY_PATH
   script.js
	{/js}
	{css}listing_slider.css{/css}
	<div class="images fx_not_sortable" fx:template="$items">
		<div 
			fx:template="item" 
			class="img-block {if $item_is_first}img-block-active{/if} picid{$id}" data-picid="{$id}">
				<img src="{$photo|'height:550'}" alt="{$description editable="false"}" />
					<span class="left">{$description}</span>
					<span class="right" fx:if="$copy">© {$copy}</span>
      </div>
    </div>
    <div class="img-slider" fx:template="$items">
    	<div 
    		fx:template="item" 
    			class="preview{if $item_is_first} preview-active{/if} picidprev{$id}" data-picid="{$id}">
    				<img src="{$photo|'height:100,width:135,crop:middle'}" />
    	</div>
	</div>
</div>

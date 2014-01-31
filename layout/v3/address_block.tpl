
<div
    fx:template="addres_block"
    fx:name="Address Block"
    fx:of="text.list"
    fx:size="high,wide"
    class="address-block">
    {each $items}
    <?
        $blue = ${'blue_'.$id};
        ?>
        <div
         data-blue="{%blue_$id type='bool' title='Blue'}0{/%}"
         class="{if !$blue}address{/if} {if $blue}info{/if}">
        {$text}Text{/$}
    </div>
    {/each}
    <div style="clear:both;"></div>
</div>
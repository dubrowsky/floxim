<div class="post_record">
    <div class="post" fx:template="item">
        <div class="date">
            {$publish_date|'d.m.Y'} {if $metatype}&bull; {$metatype}{/if}
        </div>
        <div class="anounce">{$anounce}</div>
        <div class="text">{$text}</p>
    </div>
</div>
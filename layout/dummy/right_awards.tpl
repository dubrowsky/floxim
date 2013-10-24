<div
    fx:template="right_awards"
    fx:name="Right Awards"
    fx:of="award.list"
    fx:omit="true">
    <div 
        fx:each="$items"
        class="award-sm-item" >
        <h4><a href="{$url}">{$name}</a></h4>
        <div>
            {$short_description}
        </div>
        <div class="text-right">
            {$year}
        </div>
    </div>
</div>
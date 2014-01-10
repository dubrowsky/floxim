<div 
    fx:template="award_record"
    fx:of="award.record">
    <div fx:template="item" 
        class="award-record">
        <h1 class="no-top-margin">{$name}</h1>
        <h3>{$year}</h3>
        <div>
            <img src="{$image}" alt="{$name}" class="pull-left">
            <div>{$description}</div>
        </div>
    </div>
</div>
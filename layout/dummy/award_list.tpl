<div 
    fx:template="award_list"
    fx:of="award.list"
    fx:omit="true">
    <div fx:template="item" 
        class="award-item clearfix">
        <div class="col-xs-3">
            <img src="{$image|'width:200px,crop:middle'}" alt="{$name}">
        </div>
        <div class="col-xs-8 col-xs-offset-1">
            <h3 class="no-top-margin"><a href="{$url}">{$name}</a></h3>
            <h5>{$year}</h5>
        </div>
    </div>
</div>
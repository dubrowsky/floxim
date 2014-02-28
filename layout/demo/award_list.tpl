<div 
    fx:template="award_list"
    fx:of="award.list"
    class="award-list">
    <div fx:item
        class="award-list-item">
        <div class="photo">
            <img src="{$image|'width:200px,crop:middle'}" alt="{$name}">
        </div>
        <div class="caption">
            <h3 class="no-top-margin"><a href="{$url}">{$name}</a></h3>
            <h5>{$year}</h5>
        </div>
    </div>
</div>
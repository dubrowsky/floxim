<div 
    fx:template="person_list"
    fx:of="person.list"
    fx:omit="true">
    <div fx:template="item" 
        class="person-item clearfix">
        <div class="col-md-3">
            <img src="{$photo|'width:200px,crop:middle'}" alt="{$full_name}">
        </div>
        <div class="col-md-8 col-md-offset-1">
            <h3 class="no-top-margin"><a href="{$url}">{$full_name}</a></h3>
            <h4>{$position}</h4>
            <h5>{$company}</h5>
            <div>{$short_description}</div>
        </div>
    </div>
</div>
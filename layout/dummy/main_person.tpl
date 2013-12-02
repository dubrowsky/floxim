<div
    fx:template="main_person"
    fx:of="person.list"
    fx:omit="true">
    <div
        fx:each="$items"
        fx:omit="true" >
        <div class="col-xs-4">
            <img src="{$photo|'width:145,height:150,crop:middle'}" alt="{$full_name}">
        </div>
        <div class="col-xs-8">
            <h3 class="no-top-margin"><a href="{$url}">{$full_name}</a></h3>
            <h4>{$position}</h4>
            <div>
                {$short_description}
            </div>
        </div>
    </div>
</div> 
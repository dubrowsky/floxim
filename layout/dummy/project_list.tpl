<div 
    fx:template="project_list"
    fx:of="project.list"
    fx:omit="true">
    <div fx:template="item" 
        class="project-item clearfix">
        <div class="col-xs-3">
            <img src="{$image|'width:200px,crop:middle'}" alt="{$name}">
        </div>
        <div class="col-xs-8 col-xs-offset-1">
            <h3 class="no-top-margin"><a href="{$url}">{$name}</a></h3>
            <h4>{$client}</h4>
            <h5>{$date|'m.Y'}</h5>
        </div>
    </div>
</div>
<div 
    fx:template="main_projects"
    fx:of="project.list"
    fx:omit="true" >
    <div 
        fx:each="$items"
        class="caption" >
        <div class="row">
            <div class="col-md-4">
                <img src="{$image|'width:90'}" alt="{$name}">
            </div>
            <div class="col-md-8">
                <h3 class="no-top-margin"><a href="{$url}">{$name}</a></h3>
                <div>
                    {$short_description}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 text-left">
                Client: <span class="text-info">{$client}</span>
            </div>
            <div class="col-md-6 text-right text-muted">
                {$date|'m.Y'}
            </div>
        </div>
    </div>
</div>
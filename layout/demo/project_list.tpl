<div 
    fx:template="project_list"
    fx:of="project.list"
    class="project-list">
    <div fx:item 
        class="project-list-item">
        <div class="photo">
            <img src="{$image|'width:200px,crop:middle'}" alt="{$name}">
        </div>
        <div class="caption">
            <h3 class="no-top-margin"><a href="{$url}">{$name}</a></h3>
            <h4>{$client}</h4>
            <h5>{$date|'m.Y'}</h5>
        </div>
    </div>
</div>
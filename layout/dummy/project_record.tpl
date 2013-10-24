<div 
    fx:template="project_record"
    fx:of="project.record"
    fx:omit="true">
    <div fx:template="item" 
        class="clearfix project-record">
        <h1 class="no-top-margin">{$name}</h1>
        <h2>{$client}</h2>
        <h3>{$date|'m.Y'}</h3>
        <div>
            <img src="{$image}" alt="{$name}" class="pull-left">
            <div>{$description}</div>
        </div>
    </div>
</div>
<div 
    fx:template="project_record"
    fx:of="project.record"
    fx:omit="true">
    <div fx:template="item" 
        class="project-record">
        <h1>{$name}</h1>
        <h2>{$client}</h2>
        <h3>{$date|'m.Y'}</h3>
        <div>
            <img src="{$image}" alt="{$name}" class="pull-left">
            <div>{$description}</div>
        </div>
    </div>
    <div style="clear:both;"></div>
</div>
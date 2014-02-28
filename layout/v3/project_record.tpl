<div
    fx:template="project_record"
    fx:name="Project Record"
    fx:of="project.record"
    fx:omit="true">
    <div fx:item  class="project-record">
        {$short_description}
       {$description} <p></p>{/$}
    </div>
</div>
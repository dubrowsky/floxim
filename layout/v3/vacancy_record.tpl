<div
    fx:template="vacancy_record"
    fx:name="Vacancy Record"
    fx:of="vacancy.record"
    fx:omit="true">
    <div  fx:item class="vacancy-record">
        <h3>{$position}</h3>
        <div>{$description}<p></p>{/$}</div>
        <h4>{%responsibilities_$id}Responsibilities{/%}</h4>
        <div>{$responsibilities}<p></p>{/$}</div>
        <h4>{%requirements_$id}Requirements{/%}</h4>
        <div>{$requirements}<p></p>{/$}</div>
        <h4>{%work_conditions_$id}Work Conditions{/%}</h4>
        <div>{$work_conditions}<p></p>{/$}</div>
    </div>
</div>
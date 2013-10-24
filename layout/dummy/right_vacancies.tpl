<div
    fx:template="right_vacancies"
    fx:name="Right Vacancies"
    fx:of="vacancy.list"
    fx:omit="true">
    <div 
        fx:each="$items"
        class="vacancy-sm-item" >
        <h4><a href="{$url}">{$name}</a></h4>
    </div>
</div>
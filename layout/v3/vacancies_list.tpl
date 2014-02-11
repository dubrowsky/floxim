<div
    fx:template="vacancies_list"
    fx:name="Vacancies List"
    fx:of="vacancy.list"
    fx:size="high,wide"
    class="vacancies-list">
    <div fx:each="$items" class="vacancies-list-item">
        <h3>{$position}</h3>
        <div class="desc">
            {$description}
        </div>
        <a href="{$url}" class="more">more info</a>
        <div style="clear:both;"></div>
    </div>
</div>
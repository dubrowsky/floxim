<div 
    fx:template="vacancy_list"
    fx:of="vacancy.list"
    class="vacancy-list">
    <div fx:item 
        class="vacancy-list-item">
        <h3 class="no-top-margin"><a href="{$url}">{$position}</a></h3>
        <h4>{if $salary_from}{$salary_from}{$currency}{/if}{if $salary_to}{%separator}-{/%}{$salary_to}{$currency}{/if}</h4>
    </div>
</div>
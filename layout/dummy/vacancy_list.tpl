<div 
    fx:template="vacancy_list"
    fx:of="vacancy.list"
    fx:omit="true">
    <div fx:template="item" 
        class="vacancy-item clearfix">
        <div class="col-md-12">
            <h3 class="no-top-margin"><a href="{$url}">{$position}</a></h3>
        </div>
    </div>
</div>
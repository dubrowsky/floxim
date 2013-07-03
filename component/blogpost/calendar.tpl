<div fx:template="calendar" class="blog_calendar">
    {js}
        FX_JQUERY_PATH
        calendar.js
    {/js}
    {css}
        calendar.css
    {/css}
    <h2 fx:if="$show_header">{%header}Посты по месяцам:{/%}</h2>
    <?
    $month_names = array(
        '', 'январь','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь'
    );
    ?>
    <div fx:template="item" class="year{if $active} year_active{/if}">
        <div class="year_title">{$year}</div>
        <div class="months" fx:template="$months">
            <div fx:template="item">
                <a fx:omit="!$active" href="{$url}">
                    {%month_$month}<?=$month_names[ (int) $month]?>{/%}
                </a> 
                ({$count})
            </div>
        </div>
    </div>
</div>
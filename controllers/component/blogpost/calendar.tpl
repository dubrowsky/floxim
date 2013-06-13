<div fx:template="calendar" class="blog_calendar">
    <?
    fx::page()->add_js_file(FX_JQUERY_PATH);
    fx::page()->add_js_file('/controllers/component/blogpost/_calendar.js');
    ?>
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
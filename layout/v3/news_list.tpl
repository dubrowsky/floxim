<div
    fx:template="news_list"
    fx:name="News List"
    fx:of="publication.list"
    fx:size="high,wide"
    class="news-list">
    <div
        fx:each="{$items->group('publish_date | fx::date : "F Y"') as $date => $news}"
        class="month-container">
        <div class="month">{$date}</div>
        {set $year = fx::date($date, 'Y')/}
        
        <b fx:if="$year == 2014">This yer</b>
        <b fx:elseif="$year == 2013">Last yer</b>
        {*
        <b fx:else>Other</b>*}
        <div fx:each="$news" class="news-list-item">
            <a href="{$url}">{$name}</a>
            <span class="date">{$publish_date}</span>
        </div>
    </div>
</div>
        
<div 
    fx:template="news_mixed" 
    fx:name="News list mixed" 
    data-fx_count_featured="{%count_featured type='int' label='count featured'}2{/%}"
    fx:of="publication.list">
    {if $count_featured > 0}
        {call id="featured_news_list"}
            {$items select="$items->slice(0, $count_featured)" /}
        {/call}
    {/if}
    {call id="news_list"}
        {$items select="$items->slice($count_featured)" /}
    {/call}
</div>
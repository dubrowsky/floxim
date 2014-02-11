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
        <div fx:each="$news" class="news-list-item">
            <a href="{$url}">{$name}</a>
            <span class="date">{$publish_date}</span>
        </div>
    </div>
</div>
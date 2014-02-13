<div
    fx:template="featured_news_list"
    fx:name="Featured News List"
    fx:of="publication.list"
    fx:size="low,wide"
    class="featuared-news-list">
    <div
        fx:each="$items"
        class="featuared-news-list-item">
        <img fx:if="$image" src="{$image}">
        <a href="{$url}" class="title">{$name}</a>
        <div class="date">{$publish_date | 'Y.m.d'}</div>
        <div class="text">
            {$anounce}
        </div>
    </div>
    <div style="clear:both;"></div>
    <a href="{%more_news_url}#{/%}" class="more">{%more_news}more news{/%}</a>
    <div style="clear:both;"></div>

</div>
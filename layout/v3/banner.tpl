<div
    fx:template="banner"
    fx:name="Simple banner"
    fx:of="page.list"
    fx:size="high,wide"
    fx:omit="true"
    >
    <div
        fx:template="item"
        class="banner">
        <img src="{%banner_image_$id|'crop:middle,width:1160,min-height:350'}<?=$template_dir?>img/120110-G-IA163-068-Healy-escorts-Renda.jpg{/%}">
        <div class="caption">
            <div class="h2">
                {%banner_header_$id}
                    <p>This is Staff</p>
                {/%}
            </div>
            <div class="text">
                {%banner_text_$id}
                    <p>This is <strong>text</strong></p>
                {/%}
                <a class="go" href="{$url}">Go</a>
            </div>
        </div>
    </div>
</div>
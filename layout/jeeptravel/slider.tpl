<div 
    class="gallery fx_not_sortable" 
    fx:template="index_slider" 
    fx:name="Slider" 
    fx:of="page.list">
    <div 
        fx:each="$items"
        class="gallery_item {if $item_is_first} gallery_item_active{/if} slideid{$id}">
        <img 
            src="{%bg_photo_$id | 'width:1100,height:530,crop:middle'}<?=$template_dir?>images/img01.jpg{/%}" 
            alt="" />
        <div class="slide-text active">
            <div class="slide-holder">
                <h1>{%header_$id type="html"}<?=$item['name']?>{/%}</h1>
                <span class="date">
                    {%date_$id}May 12-16<br />Expidition{/%}
                </span>
                <div class="info">
                    {%info_$id}
                        <dl>
                            <dt>Difficulty:</dt>
                            <dd>easy</dd>
                        </dl>
                    {/%}
                </div>
                <div class="holder">
                    <a href="{$url}" class="more">
                        {%more_text_$id}More info{/%}
                    </a>
                    <a href="{%action_url_$id}" class="btn">
                        {%action_text_$id}I'm in!{/%}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="switcher">
        <ul>
            <li fx:each="$items" class="{if $item_is_first}active{/if} slideid{$id}" data-slideid="{$id}">
                <a href="#" title="{$name}">{$item_index}</a>
            </li>
        </ul>
    </div>
    <a href="#" class="btn-prev">previous</a>
    <a href="#" class="btn-next">next</a>
</div>
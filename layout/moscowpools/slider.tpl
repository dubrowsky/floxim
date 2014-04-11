<div class="flexslider" fx:template="slider" fx:name="Main slider" fx:of="page.list">
    <ul class="slides">
        <li class="slide" fx:item>
            {set $cpid = fx::env('page_id').'_'.$id}
            <img 
                class="slide_pic" 
                src="{%image_$cpid | '980*380'}
                        {$image}img/slide_pic.jpg{/$}
                    {/%}" 
                alt="" />
            <div class="slide_data">
                <div class="body">
                    <h2>{$name}</h2>
                    <div class="slide_description">{$description}</div>
                </div>
                <div class="button">
                    <a href="{$url}">{%button_text_$id}Order{/%}</a>
                </div>
            </div>
        </li>
    </ul>
</div>
<div
    fx:template="bottom_photo"
    fx:of="photo.list"
    fx:omit="true">
    <div
        fx:each="$items"
        fx:omit="true" >
        <div class="row">
            <div class="col-xs-12 text-center">
                <img src="{$photo|'height:150,crop:middle'}" alt="{$copy}">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 text-right text-muted">
                {$copy}
            </div>
        </div>
    </div>
</div> 
<div class="tabs" 
     fx:template="tabs" 
     fx:of="widget_blockset.show" 
     fx:area="$area"
     fx:area-name="$infoblock.name" 
     fx:area-render="manual">
    <ul fx:with-each="$items">
        <li fx:item>
            <a href="#tab-{$id}"><span>{%tab_name_$id}{$name}Tab{/$}{/%}</span></a>
        </li>
    </ul>
    <div class="tab-data">
        <div fx:each="$items" id="tab-{$id}" class="tab">
            {$item.render()}
        </div>
    </div>
</div>
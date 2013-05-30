<div 
    fx:template="listing" 
    fx:name="Стандартное меню"
    id="menu" 
    class="std_menu">
    <ul>
        <li fx:each=".">
            <a href="{$url}">
                <span class="mw"><span class="mw">
                    <span style="{if test="$item['active']"}color: #ff0000{/if}">{$name}</span>
                </span></span>
            </a>
        </li>
    </ul>
</div>

<div fx:template="breadcrumbs" class="breadcrumbs">
    <div class="omit_test" fx:each="." fx:omit="$item_is_last">
        <span fx:if="!$item_is_last"><a href="{$url}">{$name}</a>{%separator} -> {/%}</span>
        <h1 fx:if="$item_is_last">{$name}</h1>
    </div>
</div>
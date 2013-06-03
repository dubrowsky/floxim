<div 
    fx:template="listing" 
    fx:name="Стандартное меню"
    id="menu" 
    class="std_menu">
    <ul>
        <li fx:template="unactive">
            <a href="{$url}">
                <span class="mw"><span class="mw">
                    <span>{$name}</span>
                </span></span>
            </a>
        </li>
        <li fx:template="active">
            <a href="{$url}">
                <span class="mw"><span class="mw">
                    <span style="color:#F00;">{$name}</span>
                </span></span>
            </a>
        </li>
    </ul>
</div>
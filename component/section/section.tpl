<div 
    fx:template="listing" 
    fx:name="Standard menu"
    id="menu" 
    class="std_menu">
    <ul>
        <li fx:template="inactive">
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
            <ul fx:template="$submenu" class="submenu">
                <li fx:template="item"><a href="{$url}">{$name}</a></li>
            </ul>
        </li>
    </ul>
</div>
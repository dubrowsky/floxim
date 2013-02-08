<!DOCTYPE html>
<html>
    <head>
        <title>My Super Template</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <div class="main_wrap">
            <div class="header">
                <a class="home" href="/">
                   <img src="{%logo}/floxim_templates/demo1/css/images/logo.gif{/%logo}" />
                </a>
                {area id="header"}
            </div>
            <div class="content">
                {$content}
            </div>
            <div class="footer">
                <div class="footer_left">
                    {%copy}&copy; 2010-<?=date('Y')?>{/%copy}<br />
                    <div itemtype="fx_var" itemprop="company">My Company Name</div>
                </div>
                <div class="footer_right">
                    {area id="footer" /}
                </div>
                
                {template id="supermenu" for="component.section.listing"}
                    <div class="supermenu">
                        <span class="title">{%title}Менюшечка:{/%title}&nbsp;</span>
                        {*
                        {php}
                            $items = $this->get_var('input');
                            foreach ($items as $i => $item) {
                                ?><span class="menu_item"><a href="<?=$item->get_url()?>">{$input.$i.name}</a></span><?
                                if ($i+1 != count($items) ) {
                                    ?><span class="sep">&nbsp;&bull;&nbsp;</span><?
                                }
                            }
                        {/php}
                        *}
                    </div>
                {/template}
            </div>
        </div>
    </body>
</html>
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
                {template id="wrap_simple" name="Простой блок" for="wrap"}
                    <div class="block">
                        {$content}
                    </div>
                {/template}
                {template id="wrap_titled" name="Блок с заголовком" for="wrap"}
                    <div class="block titled_block" style="border-color:{%color editable="false"}#900{/%color};">
                         <div class="title" style="background:{%color};">
                            {%title}Заголовок{/%title}
                         </div>
                         <div class="data">{$content}</div>
                    </div>
                {/template}
            </div>
            <div class="footer">
                <div class="footer_left">
                    <?
                        $mytest = array('one' => array('x' => 'Olo', 'y' => 'trolo'));
                    ?>
                    {%copy}&copy; 2010-<?=date('Y')?>{/%copy}<br />
                    <div fx_var="company">My Company Name</div>
                    <div fx_var="$mytest.one.x">hm...</div>
                </div>
                <div class="footer_right" fx_area="footer">
                    <div class="supermenu" fx_template="supermenu" fx_template_for="component_section.listing">
                        <span class="title">{%title}Менюшечка:{/%title}&nbsp;</span>
                        {render}
                            <span class="menu_item">
                                <a href="{$url}">{$name}</a>
                            </span>
                            <?if (!$item_is_last){?>
                                <span class="sep">{%separator}&nbsp;&bull;&nbsp;{/%separator}</span>
                            <?}?>
                        {/render}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
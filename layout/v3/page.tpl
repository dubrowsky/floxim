<!DOCTYPE html>
<html>
<head>
    <meta fx:layout="two_columns" fx:name="Two columns" content="two_columns" />
    <meta fx:layout="two_columns_inverted" fx:name="Two columns inverted" content="two_columns_inverted" />
    <meta fx:layout="one_column" fx:name="One column" content="one_column" />
    <meta fx:layout="full_width" fx:name="Full width" content="full_width" />
    <meta fx:layout="index" fx:name="Index page" content="index" />
    {js}
        FX_JQUERY_PATH
        js/script.js
        js/slider.js
    {/js}

    {css}
        http://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900&subset=latin,cyrillic
        css/styles.less
    {/css}
</head>
<body>
    <div class="wrapper">
        <nav class="fx_top_fixed">
            <div class="holder">
                <a href="/" class="logo">
                    <img src="{%logo | 'max-height:40'}">
                </a>
                <div
                    fx:area="top_nav"
                    fx:size="wide,low"
                    class="main-menu-area">

                </div>
                <div class="main-icons-area">
                    <ul
                        fx:area="icons_area"
                        fx:size="narrow,low"
                        class="icons">
                        <li class="icon search off">
                            <a></a>
                            <div class="width-helper">
                                <div class="form search_form">
                                    <form  method="POST" action="/floxim/" >
                                        <div class="input-group search">
                                            <label for="search">Search</label>
                                            <input  id="search" name="search" type="text">
                                        </div>
                                        <div class="input-group">
                                            <input type="submit" value="SEARCH">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </li>
                        <li
                            fx:template="authform_popup"
                            fx:of="widget_authform.show"
                            class="icon login <? echo fx::env('user') ? 'on' : 'off' ;?>">
                            <a></a>
                            <div class="width-helper">
                                <div class="form auth_form">
                                    <form  method="POST" action="/floxim/" >
                                        <input type="hidden" name="essence" value="module_auth" />
                                        <input type="hidden" name="action" value="auth" />
                                        <div class="input-group">
                                            <label for="AUTH_USER">{%email}Email{/%}</label>
                                            <input id="AUTH_USER" name="AUTH_USER" type="text">
                                        </div>
                                        <div class="input-group">
                                            <label for="AUTH_PW">{%pass}Password{/%}</label>
                                            <input id="AUTH_PW" name="AUTH_PW" type="password">
                                        </div>
                                        <div class="input-group remember">
                                            <label>{%remember}REMEMBER ME{/%}</label>
                                            <input type="checkbox">
                                        </div>
                                        <div class="input-group">
                                            <input type="submit" value="{%login}LOGIN{/%}">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <a class="phone">{%phone}8 (800) 123 12 45{/%}</a>
            </div>
        </nav>
        <section fx:if="$index || $full_width" class="full-width">
            <div
                fx:area="full_screen"
                fx:size="wide,high"
                class="holder">
                <div
                    fx:template="full_width_banner"
                    fx:name="Full width banner"
                    fx:of="page.list"
                    fx:size="high,wide"
                    style="background-image: url({$image});"
                    class="full-back">
                    <div class="caption">
                        <h2>
                            {%header}<p>Header</p>{/%}
                            <div style="clear:both;"></div>
                        </h2>
                        <div fx:if="$text" class="text">
                            {$text}
                            <a fx:if="$url" class="go" href="{$url}">Go</a>
                        </div>
                    </div>
                </div>
                <div style="clear: both;"></div>
            </div>
        </section>
        <section fx:if="$index || $two_columns" class="two-column">
            <div class="holder">
                <div
                    fx:if="!$index"
                    fx:area="breadcrumbs-area"
                    fx:size="wide,low"
                    class="breadcrumbs-area">
                </div>
                <div
                    fx:if="$index"
                    class="breadcrumbs-area">
                    <h2>
                        {%two_column_header}Catalog{/%}
                    </h2>
                </div>
                <div
                    fx:area="left_column"
                    fx:size="narrow,high"
                    class="left-column">
                </div>

                <div
                    fx:area="main_column"
                    fx:size="wide,high"
                    class="main-column">

                    <div fx:template="block_titled" fx:of="block" class="left-titled-block">
                        <h2>{%header}Header{/%}</h2>
                        <div class="content">
                            {$content}
                        </div>
                        <div style="clear: both;"></div>
                    </div>
                </div>
                <div style="clear: both;"></div>
            </div>
        </section>

        <section fx:if="$index" class="one-column grey">
            <div class="holder">
                <div
                        fx:if="!$index"
                        fx:area="breadcrumbs-area"
                        fx:size="wide,low"
                        class="breadcrumbs-area">
                </div>
                <div
                        fx:if="$index"
                        class="breadcrumbs-area">
                    <h2>
                        {%one_column_header}Today{/%}
                    </h2>
                </div>
                <div
                    fx:area="index_one_column"
                    fx:size="wide,high"
                    class="main-column">
                </div>
                <div style="clear: both;"></div>
            </div>
        </section>

        <section fx:if="$one_column" class="one-column">
            <div class="holder">
                <div
                    fx:area="breadcrumbs-area"
                    fx:size="wide,low"
                    class="breadcrumbs-area">
                </div>
                <div
                    fx:area="main_column"
                    fx:size="wide,high"
                    class="main-column">
                </div>
                <div style="clear: both;"></div>
            </div>
        </section>
        <section fx:if="$two_columns_inverted" class="two-column-invert">
            <div class="holder">
                <div
                    fx:area="breadcrumbs-area"
                    fx:size="wide,low"
                    class="breadcrumbs-area">
                </div>

                <div
                    fx:area="main_column"
                    fx:size="wide,high"
                    class="main-column">
                </div>
                <div
                    fx:area="right_column"
                    fx:size="narrow,high"
                    class="right-column">

                    <div fx:template="right_block_titled" fx:of="block" fx:omit="true"">
                        <h3>{%header}Header{/%}</h3>
                        {$content}
                    </div>
                </div>
                <div style="clear: both;"></div>
            </div>
        </section>
        <footer>
            <div class="holder">
                <div
                    fx:area="footer_menu"
                    fx:size="wide,low"
                    class="footer-menu-area">
                    <ul
                        fx:template="footer_menu"
                        fx:name="Footer menu"
                        fx:of="section.list"
                        class="footer-menu">
                        <li fx:each="$items" class="footer-menu-item"><a href="{$url}">{$name}</a></li>
                        <div style="clear:both;"></div>
                    </ul>
                </div>
                <div class="footer-contacts-area">
                    <a class="email">{%email}floxim@floxim.loc{/%}</a>
                    <a class="phone">{%phone}8 (800) 192 16 81{/%}</a>
                </div>
                <div style="clear:both;"></div>
                <div
                    fx:area="footer_social_icons"
                    fx:size="wide,low"
                    class="footer-social-area">
                    <ul
                        fx:template="social_icons"
                        fx:name="Footer social icons"
                        fx:of="social_icon.list"
                        class="social-icons-list">
                        <li fx:each="$items" class="social-icons-list-item {$soc_type}"><a href="{$url}"></a></li>
                        <div style="clear:both;"></div>
                    </ul>
                    <div style="clear:both;"></div>
                </div>
                <a class="copyright">{%copyright}© 2014 floxim inc.{/%}</a>
            </div>
        </footer>
    </div>
</body>
</html>
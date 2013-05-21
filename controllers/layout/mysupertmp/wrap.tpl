{template id="wrap" for="false"}
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="keywords" content="" />
        <meta name="description" content="" />
        <title>мой супер тестовый шаблон</title>
        <link rel="stylesheet" type="text/css" href="/controllers/layout/supernova/css/main.css" />
        <link rel="stylesheet" type="text/css" href="/controllers/layout/supernova/css/color.css" />
        </head>

        <body>
            <div id="header">
                <!--логотип, название, слоган-->
                <div id="logo">
                    <a href="/">
                        <img src="/controllers/layout/supernova/css/images/logo.gif" alt="" title="" />
                    </a>
                    <span>
                        <div fx_var="slogan">название или слоган <br />вашей компании</div>
                    </span>
                </div>
                <!--//логотип, название, слоган-->
                
                {area id="header"}
                    <!--menu, из-за свеобразной структуры меню, могут быть некоторые трудности у пользователей с сортировкой пунктов меню в системе управления сайтом-->
                    <div id="menu" fx_template="demo_menu" fx_template_for="component_section.listing" fx_template_name="Горизонтальное меню (главное)">
                        <ul>
                            <li fx_render=".">
                                <a {if test="$item['active']"} class="menu-active"{/if} href="{$url}" fx_replace="href">
                                    {$name}
                                </a>
                            </li>
                        </ul>
                    </div>
                                
                    <!--//menu-->

                    <!--поиск-->
                    <div class="search" fx_template="search_block" fx_template_for="widget_search.form" fx_template_name="Форма поиска">
                        <form action="#">
                            <input type="text" value="Что будем искать?" name="" class="field" onblur="if(this.value==''){this.value='Что будем искать?'}" onfocus="if(this.value=='Что будем искать?'){this.value=''}" />
                            <input type="submit" value="Поиск" name="" class="submit" />
                        </form>
                    </div>
                    <!--//поиск-->
                {/area}

                <div class="sep"></div>
            </div>

            <div id="middle">
                <!--content-->
                <div id="content" class="inner">
                    {$content}

                    {template id="wrap_simple" name="Простой блок" for="wrap"}
                        <div class="block">
                            {$content}
                        </div>
                    {/template}

                    {template id="wrap_titled" name="Блок с заголовком" for="wrap"}
                        <div class="block">
                            <div class="title">
                                <h1 style="color:#000">{$header}</h1>
                            </div>
                            <div class="data">
                                {$content}
                            </div>
                        </div>
                    {/template}

                    {template id="wrap_plain_text" name="Простой блок с текстом" for="wrap"}
                        <p>
                            {$text}
                        </p>
                    {/template}

                    {template id="wrap_titled_with_image" name="Блок с картинкой и подписью" for="wrap"}
                        <div class="article_image block">
                            <img src="css/images/content_img_1.jpg" alt="" width="357" height="163" fx_replace="src" />
                            <p class="header article">{$header}</p>
                            <p>{$text}</p>
                            <p><a href="#">{$link}</a></p>
                            <div class="sep"></div>
                        </div>
                    {/template}

                    {template id="wrap_with_h2" name="Блок с заголовком h2" for="wrap"}
                        <h2>{$header}</h2>
                        <p>{$text}</p>
                    {/template}

                    {template id="wrap_with_h3" name="Блок с заголовком h3" for="wrap"}
                        <h2>{$header}</h2>
                        <p>{$text}</p>
                    {/template}

                </div>

                {*
                <div class="form">
                <form action="#">
                <textarea rows="3" cols="1" onblur="if(this.value==''){this.value='Напишите нам всё, что вас интересует. Не стесняйтесь писать много, это поле рассчитано на три тома войны и мира.'}" onfocus="if(this.value=='Напишите нам всё, что вас интересует. Не стесняйтесь писать много, это поле рассчитано на три тома войны и мира.'){this.value=''}">Напишите нам всё, что вас интересует. Не стесняйтесь писать много, это поле рассчитано на три тома войны и мира.</textarea>
                <input type="text" value="Ваше имя*" name="" class="left" onblur="if(this.value==''){this.value='Ваше имя*'}" onfocus="if(this.value=='Ваше имя*'){this.value=''}" />
                <input type="text" value="Электронная почта*" name="" class="right" onblur="if(this.value==''){this.value='Электронная почта*'}" onfocus="if(this.value=='Электронная почта*'){this.value=''}" />
                <input type="text" value="Номер телефона" name="" class="left" onblur="if(this.value==''){this.value='Номер телефона'}" onfocus="if(this.value=='Номер телефона'){this.value=''}" />
                <select name="Выберите что-нибудь" class="right">
                <option value="Выберите что-нибудь">Выберите что-нибудь</option>
                <option value="Что-нибудь">Что-нибудь</option>
                </select>
                <div class="sep"></div>
                <input type="submit" value="Отправить" name="" class="submit left" />
                </form>
                </div>
                *}

                <!--//content-->
                <!--подразделы, блок справа-->
                <div id="right_content" fx_area="sidebar">
                    <p>Тролололушки лоло</p>
                    <div id="submenu" fx_template_for="component_section.listing">
                        <ul>
                            <li fx_render=".">
                                <a href="{$url}">{$name}</a>
                                {if test="$submenu"}
                                    <ul>
                                        <li fx_render=".">
                                            <a href="{$url}">{$name}</a>
                                        </li>
                                    </ul>
                                {/if}
                            </li>
                        </ul>
                    </div>
                </div>
                <!--//подразделы, блок справа-->
                <div class="sep"></div>
            </div>


            <!--footer-->
            <div fx_area="footer" id="footer">
                <div class="content">
                    <div class="left"  fx_var="copy">&copy; 2010 группа компаний &laquo;Netcat&raquo;.<br />Все права защищены.</div>
                    <div class="middle" fx_var="contacts">Адрес: г. Москва, ул. Мануфактурная, д. 14<br />Телефон и факс: (831) 220-80-18</div>
                    <div class="right"  fx_var="developa">&copy; 2010 Хороший пример <br />сайтостроения &mdash; <a href="#">WebSite.pu</a></div>
                    <div class="sep"></div>
                </div>
            </div>
            <!--//footer-->

        </body>
    </html>
{/template}
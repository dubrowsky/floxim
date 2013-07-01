{call id="wrap"}
    <!--верхний баннер, фоновое изображение меняется через background в general.css-->
    <div id="banner_top">
        <img src="images/top_banner.jpg" width="834" height="360" alt="" />
    </div>
    <!--//верхний баннер-->
    <div id="booo"></div>
    {$too_blocks}
        <div class="block columns_2">
            <div class="col first">
                <h2>{%c1_header}Новости и события{/%}</h2>
                {%c1_text}
                <p>
                    <a href="#">
                        <img src="controllers/layout/supernova/images/news-img.jpg" width="264" height="125" alt="" />
                    </a>
                </p>
                <p>А мне вообще тут всё понравилос, ток не пойму про, что тут?</p>
                {/%}
            </div>

            <div class="col">
                <h2>{%c2_header}Услуги компании{/%}</h2>
                {%c1_text}
                <ul>
                    <li><a href="#">Штамповка кадров</a></li>
                    <li><a href="#">Изготовление офисного планктона</a></li>
                    <li><a href="#">Обучение нерадивых работников</a></li>
                    <li><a href="#">Семинары для уволеных</a></li>
                    <li><a href="#">Форумы для знающих</a></li>
                </ul>
                <!--кнопка, тянется по ширине в пределах разумного, class="sep" - для того чтобы не было проблем с float:left-->
                <div class="button"><div class="left"><div class="right"><div class="content">
                                <a href="#">Все услуги</a>
                            </div></div></div></div>
                <div class="sep"></div>
                {/%}
                <!--кнопка-->
            </div>
            <div class="sep"></div>
        </div>
    {/$}


    {$index_block}
        <div id="partners">
            <!-- по идее это вообще бы сделать инфоблоком с -->
            {%partner_1}
            <div class="block first">
                {*<a href="#"><img src="controllers/layout/supernova/images/logo_1.gif" width="55" height="49" alt="" /></a>*}
                <p>&laquo;CSYSTEM&raquo; &mdash; цифровые системы</p>
            </div>
            {/%}
            {%partner_2}
            <div class="block">
                {*<a href="#"><img src="controllers/layout/supernova/images/logo_2.gif" width="174" height="49" alt="" /></a>*}
                <p>&laquo;Контур-Вест&raquo; &mdash; каждому по флажку</p>
            </div>
            {/%}
            {%partner_3}
            <div class="block">
                {*<a href="#"><img src="controllers/layout/supernova/images/logo_3.gif" width="197" height="49" alt="" /></a>*}
                <p>&laquo;КласикСтройКомплект&raquo; &mdash; строительная техника.</p>
            </div>
            {/%}
            {%partner_4}
            <div class="block">
                {*<a href="#"><img src="controllers/layout/supernova/images/logo_4.gif" width="122" height="49" alt="" /></a>*}
                <p>&laquo;MetroGroup&raquo; &mdash; товары на любой вкус.</p>
            </div>
            {/%}
            <div class="sep"></div>
        </div>
    {/$}
{/call}
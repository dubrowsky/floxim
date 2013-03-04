<?php

class widget_yandexpogoda extends fx_tpl_widget {

    public function record() {
        extract($this->get_vars());
        //fx_begin ?>
<a href="http://clck.yandex.ru/redir/dtype=stred/pid=7/cid=1228/*http://pogoda.yandex.ru/<?= $this->m['city'] ?>">
            <img src="http://info.weather.yandex.net/<?= $this->m['city'] ?>/<?= $this->m['types'][$f_width] ?>.png" style="border:none;" alt="Яндекс.Погода" />
            <img width="1" height="1" src="http://clck.yandex.ru/click/dtype=stred/pid=7/cid=1227/*http://img.yandex.ru/i/pix.gif" alt="" style="border:none"/>
        </a>
<? //fx_end
    }

    public function settings() {
        extract($this->get_vars());
		//fx_begin
$this->m['city'] = fx_transliterate(strtolower($f_city));
$this->m['types'] = array(175 => 1, 200 => 2, 120 => 3, 150 => 4);
//fx_end
    }

    public function add_form() {
        extract($this->get_vars());
        //fx_begin
$city = $infoblock['settings']['city'] ? $infoblock['settings']['city'] : 'moscow';
        $width = $infoblock['settings']['width'] ? $infoblock['settings']['width'] : '175';

        return array(
            array('name' => 'city', 'label' => 'Город', 'type' => 'select', 'value' => $city, 'values' => array('moscow' => 'Москва', 'saint-petersburg' => 'Санкт-Петербург', 'abakan' => 'Абакан', 'aktubinsk' => 'Актюбинск', 'almaty' => 'Алматы', 'aleisk' => 'Алейск', 'almetyevsk' => 'Альметьевск', 'anadyr' => 'Анадырь', 'apatity' => 'Апатиты', 'arzamas-16' => 'Арзамас-16', 'arkhangelsk' => 'Архангельск', 'astana' => 'Астана', 'astrakhan' => 'Астрахань', 'ashgabat' => 'Ашгабат', 'baku' => 'Баку', 'barnaul' => 'Барнаул', 'batumi' => 'Батуми', 'bakhchisaraj' => 'Бахчисарай', 'belaya cerkov' => 'Белая Церковь', 'belgorod' => 'Белгород', 'berdyansk' => 'Бердянск', 'biysk' => 'Бийск', 'bishkek' => 'Бишкек', 'blagoveschenka' => 'Благовещенка', 'blagoveshchensk' => 'Благовещенск', 'bratsk' => 'Братск', 'brest' => 'Брест', 'bryansk' => 'Брянск', 'bukhara' => 'Бухара', 'vilnius' => 'Вильнюс', 'vinnitsa' => 'Винница', 'vitebsk' => 'Витебск', 'vladivostok' => 'Владивосток', 'vladikavkaz' => 'Владикавказ', 'vladimir' => 'Владимир', 'volgograd' => 'Волгоград', 'volzhsk' => 'Волжск', 'volga' => 'Волжский', 'vologda' => 'Вологда', 'volchiha' => 'Волчиха', 'vorkuta' => 'Воркута', 'voronezh' => 'Воронеж', 'vyatka' => 'Вятка', 'gelendzhik' => 'Геленджик', 'glazov' => 'Глазов', 'gomel' => 'Гомель', 'gornoaltaysk' => 'Горноалтайск', 'grodno' => 'Гродно', 'grozdny' => 'Грозный', 'guriev' => 'Гурьев', 'gus-hrustalniy' => 'Гусь-Хрустальный', 'dzhamgul' => 'Джамгул', 'dimitrovgrad' => 'Димитровград', 'dnepropetrovsk' => 'Днепропетровск', 'donetsk' => 'Донецк', 'dubna' => 'Дубна', 'dushanbe' => 'Душанбе', 'evpatoria' => 'Евпатория', 'yekaterinburg' => 'Екатеринбург', 'yelabuga' => 'Елабуга', 'elec' => 'Елец', 'yerevan' => 'Ереван', 'esentuki' => 'Есентуки', 'zheleznovodsk' => 'Железноводск', 'zhitomir' => 'Житомир', 'zaporozhye' => 'Запорожье', 'zarinsk' => 'Заринск', 'zelenograd' => 'Зеленоград', 'zelenodolsk' => 'Зеленодольск', 'zlatoust' => 'Златоуст', 'ivanovo' => 'Иваново', 'ivano-frankivsk' => 'Ивано-Франковск', 'izhevsk' => 'Ижевск', 'irkutsk' => 'Иркутск', 'yoshkar-ola' => 'Йошкар-Ола', 'kazan' => 'Казань', 'kaluga' => 'Калуга', 'kamyshin' => 'Камышин', 'karaganda' => 'Караганда', 'kaunas' => 'Каунас', 'kemerovo' => 'Кемерово', 'konigsberg' => 'Кёнигсберг', 'kerch' => 'Керчь', 'kyzyl-orda' => 'Кзыл-Орда', 'kiev' => 'Киев', 'kirov' => 'Киров', 'kirovograd' => 'Кировоград', 'kirov-chepetsk' => 'Кирово-Чепецк', 'kislovodsk' => 'Кисловодск', 'kishinev' => 'Кишинев', 'klaipeda' => 'Клайпеда', 'kovrov' => 'Ковров', 'kokchenav' => 'Кокченав', 'komsomolsk-na-amure' => 'Комсомольск-на-Амуре', 'kostroma' => 'Кострома', 'krasnodar' => 'Краснодар', 'krasnoyarsk' => 'Красноярск', 'krivoy rog' => 'Кривой рог', 'krutiha' => 'Крутиха', 'kurgan' => 'Курган', 'kursk' => 'Курск', 'kostanay' => 'Кустанай', 'kutaisi' => 'Кутаиси', 'kyzyl' => 'Кызыл', 'lipetsk' => 'Липецк', 'lugansk' => 'Луганск', 'lutsk' => 'Луцк', 'lvov' => 'Львов', 'magadan' => 'Магадан', 'magnitogorsk' => 'Магнитогорск', 'maikop' => 'Майкоп', 'mariupol' => 'Мариуполь', 'makhachkala' => 'Махачкала', 'miass' => 'Миасс', 'mineralnie vodi' => 'Минеральные воды', 'minsk' => 'Минск', 'mirniy' => 'Мирный', 'another city / region' => 'Другой город/регион', 'murmansk' => 'Мурманск', 'naberezhnye chelny' => 'Набережные Челны', 'nadym' => 'Надым', 'nalchik' => 'Нальчик', 'naryan-mar' => 'Нарьян-Мар', 'nahodka' => 'Находка', 'nevinnomyssk' => 'Невинномысск', 'nizhnevartovsk' => 'Нижневартовск', 'nizhnekamsk' => 'Нижнекамск', 'nizhny novgorod' => 'Нижний Новгород', 'nikolaev' => 'Николаев', 'novgorod' => 'Новгород', 'novokuznetsk' => 'Новокузнецк', 'novomoskovsk' => 'Новомосковск', 'novorossiysk' => 'Новороссийск', 'novosibirsk' => 'Новосибирск', 'novouralsk' => 'Новоуральск', 'novocherkassk' => 'Новочеркасск', 'norilsk' => 'Норильск', 'obninsk' => 'Обнинск', 'odessa' => 'Одесса', 'omsk' => 'Омск', 'orel' => 'Орел', 'orenburg' => 'Оренбург', 'osh' => 'Ош', 'pavlovsk' => 'Павловск', 'pavlodar' => 'Павлодар', 'penza' => 'Пенза', 'perm' => 'Пермь', 'petrozavodsk' => 'Петрозаводск', 'petropavlovsk-kamchatskiy' => 'Петропавловск-Камчатский', 'pechora' => 'Печора', 'poltava' => 'Полтава', 'pospelixa' => 'Поспелиxа', 'pskov' => 'Псков', 'pushchino' => 'Пущино', 'pyatigorsk' => 'Пятигорск', 'riga' => 'Рига', 'rovno' => 'Ровно', 'rossosh' => 'Россошь', 'rostov-na-donu' => 'Ростов-на-Дону', 'rubtsovsk' => 'Рубцовск', 'ryazan' => 'Рязань', 'salekhard' => 'Салехард', 'samara' => 'Самара', 'saransk' => 'Саранск', 'sarapul' => 'Сарапул', 'saratov' => 'Саратов', 'sevastopol' => 'Севастополь', 'severodvinsk' => 'Северодвинск', 'semipalatinsk' => 'Семипалатинск', 'simbirsk' => 'Симбирск', 'simferopol' => 'Симферополь', 'slavgorod' => 'Славгород', 'smolensk' => 'Смоленск', 'smolensk' => 'Смоленское', 'solikamsk' => 'Соликамск', 'sosnoviy bor' => 'Сосновый Бор', 'sochi' => 'Сочи', 'stavropol' => 'Ставрополь', 'sterlitamak' => 'Стерлитамак', 'sumy' => 'Сумы', 'surgut' => 'Сургут', 'sukhumi' => 'Сухуми', 'syktyvkar' => 'Сыктывкар', 'taganrog' => 'Таганрог', 'tallinn' => 'Таллинн', 'tambov' => 'Тамбов', 'tashkent' => 'Ташкент', 'tbilisi' => 'Тбилиси', 'tver' => 'Тверь', 'temirtau' => 'Темиртау', 'ternopil' => 'Тернополь', 'togliatti' => 'Тольятти', 'tomsk' => 'Томск', 'tula' => 'Тула', 'tyumen' => 'Тюмень', 'uzhgorod' => 'Ужгород', 'ulan-ude' => 'Улан-Удэ', 'uralsk' => 'Уральск', 'ussuriysk' => 'Уссурийск', 'ust-kamenogorsk' => 'Усть-Каменогорск', 'ufa' => 'Уфа', 'feodosia' => 'Феодосия', 'frankivsk' => 'Франковск', 'khabarovsk' => 'Хабаровск', 'khanty-mansiysk' => 'Ханты-Мансийск', 'kharkiv' => 'Харьков', 'kherson' => 'Херсон', 'khmelnitsky' => 'Хмельницкий', 'khujand' => 'Ходжент', 'chardzhu' => 'Чарджоу', 'cheboksary' => 'Чебоксары', 'chelyabinsk' => 'Челябинск', 'cherepovets' => 'Череповец', 'cherkasy' => 'Черкассы', 'cherkessk' => 'Черкесск', 'chernihiv' => 'Чернигов', 'chernivtsi' => 'Черновцы', 'shymkent' => 'Чимкент', 'tchistopol' => 'Чистополь', 'chita' => 'Чита', 'shevchenko' => 'Шевченко', 'elista' => 'Элиста', 'yuzhno-sakhalinsk' => 'Южно-Сахалинск', 'yakutsk' => 'Якутск', 'yalta' => 'Ялта', 'yaroslavl' => 'Ярославль', 'ukhta' => 'Ухта')),
            array('name' => 'width', 'label' => 'Ширина', 'type' => 'select', 'value' => $width,'values' => array('120' => '120', '150' => '150', '175' => '175', '200' => '200'))
        );
//fx_end
    }

}
?>


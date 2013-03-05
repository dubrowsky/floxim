-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Мар 05 2013 г., 14:53
-- Версия сервера: 5.5.25
-- Версия PHP: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `floxim`
--

-- --------------------------------------------------------

--
-- Структура таблицы `fx_auth_external`
--

CREATE TABLE IF NOT EXISTS `fx_auth_external` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `external_id` int(11) NOT NULL,
  `type` enum('twitter','fb','openid') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_auth_user_relation`
--

CREATE TABLE IF NOT EXISTS `fx_auth_user_relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `related_id` int(11) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `User_ID` (`user_id`),
  KEY `Related_ID` (`related_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_classificator`
--

CREATE TABLE IF NOT EXISTS `fx_classificator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(32) NOT NULL,
  `table` char(32) NOT NULL,
  `checked` tinyint(1) NOT NULL DEFAULT '1',
  `sort_type` enum('id','name','priority') NOT NULL DEFAULT 'priority',
  `sort_direction` enum('asc','desc') NOT NULL DEFAULT 'asc',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Table_Name` (`table`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `fx_classificator`
--

INSERT INTO `fx_classificator` (`id`, `name`, `table`, `checked`, `sort_type`, `sort_direction`) VALUES
(1, 'Страна', 'Country', 0, 'priority', 'asc'),
(2, 'Города', 'cities', 1, 'priority', 'asc');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_classificator_cities`
--

CREATE TABLE IF NOT EXISTS `fx_classificator_cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `priority` int(11) DEFAULT NULL,
  `value` text,
  `checked` int(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `fx_classificator_cities`
--

INSERT INTO `fx_classificator_cities` (`id`, `name`, `priority`, `value`, `checked`) VALUES
(1, 'Москва', NULL, NULL, 1),
(2, 'Архангельск', NULL, NULL, 1),
(3, 'Архангельскфыв', NULL, NULL, 1),
(4, 'Архангельск', NULL, NULL, 1),
(5, 'Архангельск', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_classificator_country`
--

CREATE TABLE IF NOT EXISTS `fx_classificator_country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(64) NOT NULL,
  `priority` int(11) DEFAULT NULL,
  `value` text,
  `checked` int(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Дамп данных таблицы `fx_classificator_country`
--

INSERT INTO `fx_classificator_country` (`id`, `name`, `priority`, `value`, `checked`) VALUES
(1, 'Австралия', 1, NULL, 1),
(2, 'Австрия', 2, NULL, 1),
(3, 'Азербайджан', 3, NULL, 1),
(4, 'Албания', 4, NULL, 1),
(5, 'Алжир', 5, NULL, 1),
(6, 'Ангилья', 6, NULL, 1),
(7, 'Ангола', 7, NULL, 1),
(8, 'Андорра', 8, NULL, 1),
(9, 'ККаа', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_classificator_region`
--

CREATE TABLE IF NOT EXISTS `fx_classificator_region` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(64) NOT NULL,
  `priority` int(11) DEFAULT NULL,
  `value` text,
  `checked` int(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

--
-- Дамп данных таблицы `fx_classificator_region`
--

INSERT INTO `fx_classificator_region` (`id`, `name`, `priority`, `value`, `checked`) VALUES
(1, 'Москва', 1, NULL, 1),
(2, 'Санкт-Петербург', 2, NULL, 1),
(3, 'Абакан', 3, NULL, 1),
(4, 'Актюбинск', 4, NULL, 1),
(5, 'Алматы', 5, NULL, 1),
(6, 'Алейск', 6, NULL, 1),
(7, 'Альметьевск', 7, NULL, 1),
(8, 'Анадырь', 8, NULL, 1),
(9, 'Апатиты', 9, NULL, 1),
(10, 'Арзамас-16', 10, NULL, 1),
(11, 'Архангельск', 11, NULL, 1),
(12, 'Астана', 12, NULL, 1),
(13, 'Астрахань', 13, NULL, 1),
(14, 'Ашгабат', 14, NULL, 1),
(15, 'Баку', 15, NULL, 1),
(16, 'Барнаул', 16, NULL, 1),
(17, 'Батуми', 17, NULL, 1),
(18, 'Бахчисарай', 18, NULL, 1),
(19, 'Белая Церковь', 19, NULL, 1),
(20, 'Белгород', 20, NULL, 1),
(21, 'Бердянск', 21, NULL, 1),
(22, 'Бийск', 22, NULL, 1),
(23, 'Бишкек', 23, NULL, 1),
(24, 'Благовещенка', 24, NULL, 1),
(25, 'Благовещенск', 25, NULL, 1),
(26, 'Братск', 26, NULL, 1),
(27, 'Брест', 27, NULL, 1),
(28, 'Брянск', 28, NULL, 1),
(29, 'Бухара', 29, NULL, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_component`
--

CREATE TABLE IF NOT EXISTS `fx_component` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text COMMENT 'Описание компонента',
  `group` varchar(64) NOT NULL DEFAULT 'Main',
  `icon` varchar(255) NOT NULL,
  `store_id` text,
  `has_page` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Class_Group` (`group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Дамп данных таблицы `fx_component`
--

INSERT INTO `fx_component` (`id`, `keyword`, `name`, `description`, `group`, `icon`, `store_id`, `has_page`) VALUES
(1, 'user', 'Пользователи', NULL, 'Пользователи', '', 'component.user', 0),
(2, 'articles', 'Статьи', NULL, 'Публикации', '', 'component.articles', 0),
(3, 'awards', 'Награды и дипломы', NULL, 'Публикации', '', 'component.awards', 0),
(4, 'catalog', 'Каталог товаров', NULL, 'Товары и услуги', '', 'component.catalog', 0),
(5, 'comments', 'Комментарии', NULL, 'Базовые', '', 'component.comments', 0),
(6, 'companyshort', 'Компании, кратко', NULL, 'Корпоративные', '', 'component.companyshort', 0),
(7, 'miniarticles', 'Министатьи', NULL, 'Публикации', '', 'component.miniarticles', 0),
(8, 'newsblog', 'Новости/блог', 'Компонент для организации ленты новостей или блога компании', 'Публикации', '', 'component.newsblog', 0),
(9, 'person', 'Персоны', NULL, 'Пользователи', '', 'component.person', 0),
(10, 'photo', 'Фотогалерея', 'Простая фотогалерея с большими возможностями', 'Публикации', 'photo', 'component.photo', 0),
(11, 'pm', 'Личные сообщения', NULL, 'Пользователи', '', 'component.pm', 0),
(12, 'pricelist', 'Прайс-лист краткий', NULL, 'Товары и услуги', '', 'component.pricelist', 0),
(13, 'quotes', 'Цитаты', NULL, 'Публикации', '', 'component.quotes', 0),
(14, 'resume', 'Резюме', NULL, 'Пользователи', '', 'component.resume', 0),
(15, 'resumecontacts', 'Резюме: контакты (Контакты персональные)', NULL, 'Пользователи', '', 'component.resumecontacts', 0),
(16, 'resumeeducation', 'Резюме: образование', NULL, 'Базовые', '', 'component.resumeeducation', 0),
(17, 'resumeexperience', 'Резюме: опыт работы', NULL, 'Базовые', '', 'component.resumeexperience', 0),
(18, 'resumelinks', 'Резюме: список проектов (Ссылки)', NULL, 'Публикации', '', 'component.resumelinks', 0),
(19, 'text', 'Текст', NULL, 'Базовые', '', 'component.text', 0),
(20, 'vacancy', 'Вакансии', NULL, 'Корпоративные', '', 'component.vacancy', 0),
(22, 'faq', 'Вопрос-ответ', NULL, 'Базовые', '', 'component.faq', 0),
(23, 'page', 'Страницы', NULL, 'Базовые', '', NULL, 0),
(24, 'section', 'Разделы', 'Для меню', 'Базовые', '', NULL, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_articles`
--

CREATE TABLE IF NOT EXISTS `fx_content_articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `title` varchar(255) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `issue` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `author_link` varchar(255) DEFAULT NULL,
  `pic` int(11) DEFAULT NULL,
  `note` text,
  `text` text,
  `file` int(11) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_awards`
--

CREATE TABLE IF NOT EXISTS `fx_content_awards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `pic` int(11) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `year` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_catalog`
--

CREATE TABLE IF NOT EXISTS `fx_content_catalog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `name` varchar(255) DEFAULT NULL,
  `text` text,
  `price` int(11) DEFAULT NULL,
  `picture` int(11) DEFAULT NULL,
  `comm` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_comments`
--

CREATE TABLE IF NOT EXISTS `fx_content_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `text` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_companyshort`
--

CREATE TABLE IF NOT EXISTS `fx_content_companyshort` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `name` varchar(255) DEFAULT NULL,
  `logo` int(11) DEFAULT NULL,
  `brief` text,
  `description` text,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_faq`
--

CREATE TABLE IF NOT EXISTS `fx_content_faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `name` varchar(255) DEFAULT NULL,
  `question` text,
  `answer` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_miniarticles`
--

CREATE TABLE IF NOT EXISTS `fx_content_miniarticles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `title` varchar(255) DEFAULT NULL,
  `shorttext` text,
  `text` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_newsblog`
--

CREATE TABLE IF NOT EXISTS `fx_content_newsblog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `title` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `announce` text,
  `text` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Дамп данных таблицы `fx_content_newsblog`
--

INSERT INTO `fx_content_newsblog` (`id`, `parent_id`, `keyword`, `user_id`, `infoblock_id`, `priority`, `checked`, `created`, `last_updated`, `seo_h1`, `seo_title`, `seo_keywords`, `seo_description`, `title`, `link`, `announce`, `text`) VALUES
(8, 0, NULL, 1, 126, 3, 1, '2012-08-30 16:50:07', '2012-08-30 12:50:07', NULL, NULL, NULL, NULL, 'Японский старт нового облачного сервиса Panasonic', '', 'Управлять удаленно разнообразной бытовой техникой можно просто и\r\n удобно. Чтобы сделать это реальностью, Panasonic значительно расширяет \r\nассортимент интеллектуальных приборов и запускает, пока только в Японии,\r\n новый облачный сервис, облегчающий дистанционное общение с домашней \r\nтехникой.    ', 'Чтобы контролировать работу приборов, использовать их наиболее \r\nэффективно и экономично, пользователю потребуется смартфон с \r\nоперационной системой Android и приложение Panasonic Smart App. <br>\r\n<br>\r\nПервыми в линейке дистанционно управляемых приборов стали индукционная \r\nрисоварка и микроволновая печь 3-Star Bistro. Panasonic впервые \r\nиспользовал облачную технологию для общения с этой техникой в июне \r\nтекущего года. <br>\r\n<br>\r\nВ сентябре вниманию будущих пользователей компания представит \r\nхолодильники, кондиционеры, стиральные машины с сушкой, оснащенные \r\nфункциями удаленного управления. Управлять кондиционерами будет возможно\r\n не только с помощью смартфона с ОС Android, но и при помощи iPhone. <br>\r\n<br>\r\nПомимо крупной бытовой техники Panasonic предложит вниманию японских \r\nпокупателей счетчик калорий, тонометр и анализатор параметров тела - эти\r\n устройства также отличаются высоким «интеллектом» и взаимодействуют с \r\nпользователем через смартфон. <br>\r\n<br>\r\nНовые возможности, предлагаемые Panasonic, оценят по достоинству люди, \r\nпроводящие много времени вне дома и заботящиеся о том, чтобы домашние \r\nдела были сделаны вовремя и наиболее экономичным способом. <br>\r\n<br>\r\nУстройства для заботы о здоровье новой серии дают потребителю \r\nвозможность точно и правильно рассчитывать количество потребляемых \r\nкалорий, нагрузку в спортзале. <br>\r\n<br>\r\nПриложение Panasonic Smart App пользователи смогут скачать бесплатно, \r\nдля использования возможностей удаленного доступа владельцам умной \r\nтехники потребуется лишь зарегистрировать её на сайте Club Panasonic.    '),
(9, 0, NULL, 1, 126, 4, 1, '2012-08-30 16:51:07', '2012-08-30 12:51:07', NULL, NULL, NULL, NULL, 'Nikon COOLPIX S800c: свежий кадр - в Интернет!', '', 'Новая фотокамера Nikon COOLPIX S800c способна не только делать \r\nснимки отличного качества. Компактная новинка легко и просто позволяет \r\nвыходить в Сеть благодаря встроенному Wi-Fi модулю. Новый Nikon оснащен \r\nОС Android версии 2.3, что облегчает загрузку разнообразных полезных и \r\nприятных приложений для обработки только что сделанных фото и для \r\nотправки их в соцсети. Делиться тем, что вы видите перед собой - очень \r\nпросто!     ', 'Маленькая фотокамера Nicon COOLPIX S800c обладает 680 МБ памяти для \r\nприложений и 1,7 ГБ памяти для фото и видео. Расширить возможности \r\nфотоаппарата можно при помощи карт SD/SDHC объёмом до 32 ГБ. <br>\r\n<br>\r\nЗа резкость кадров в новом фотоаппарате Nicon COOLPIX S800c отвечает \r\nКМОП-матрица высокой чувствительности. Тыловое освещение даёт \r\nвозможность делать снимки даже в сложных условиях. Высокая \r\nсветочувствительность ISO - до 3200, помогает уменьшить смазывание \r\nизображений при съёмках при слабом освещении. Функция BSS автоматически \r\nвыбирает наиболее резкий кадр из 10 последовательных. <br>\r\n<br>\r\nБыстрый отклик гарантируют системы автофокусировки и обработки \r\nизображений EXPEED C2. Технология обнаружения движения обеспечивает \r\nкорректировку движения камеры относительно объекта съёмки.<br>\r\n<br>\r\nВысочайшее качество снимков, живые цвета поможет сохранить \r\nширокоугольный объектив маленькой камеры с десятикратным оптическим ZOOM\r\n (25-250 мм). Четкость и резкость снимков любых объектов: в движении, в \r\nотдалении и максимально приближенных - будет идеальной! <br>\r\n<br>\r\nУправление возможностями компактной фотокамеры Nicon COOLPIX S800c \r\nосуществляется при помощи 3,5-дюймового дисплея с великолепной \r\nчувствительностью. Экран камеры с антибликовым покрытием обладает \r\nшироким углом обзора и имеет разрешение в 819 тысяч точек. <br>\r\n<br>\r\nПри желании вы сможете поймать моменты даже самого быстрого движения: \r\nкамера Nikon способна делать снимки со скоростью 8,1 кадра в секунду. \r\nДля видеосъёмки в формате Full HD и со стереозвуком вы сможете \r\nиспользовать эту же камеру! В режиме HS производить съёмку можно в \r\nускоренном или замедленном режимах. Для панорамной съёмки под углами в \r\n180 или 360 градусов фотоаппарат имеет специальный режим. <br>\r\n<br>\r\nВысокую контрастность снимаемых объектов гарантирует система HDR с \r\nподсветкой. Камера Nikon обладает системой подавления вибраций со \r\nсмещением объектива, так что резкость съёмки всегда будет на высоте. <br>\r\n<br>\r\nХранить фото, сделанные Nicon COOLPIX S800c можно в хранилище Nikon в \r\nсети. Для доступа к myPicturetown.com выходить в Интернет не \r\nпотребуется, ведь камера Nikon подключается к нему напрямую через \r\nпредустановленное приложение. Чтобы не забыть, где был сделан тот или \r\nиной снимок, можно воспользоваться встроенным модулем GPS, который \r\nпозволит поставить на фото географические отметки. <br>\r\n<br>\r\nМаленькая камера Nikon порадует пользователя большим набором возможностей и отличными кадрами, сделанными в любой обстановке!    '),
(10, 0, NULL, 1, 126, 5, 1, '2012-08-30 16:51:50', '2012-11-20 09:43:09', NULL, NULL, NULL, NULL, 'Гибкий планшет Sony Pocket Tablet!', '', 'О том, что у планшетных устройств, именуемых также «таблетками», большое\r\n будущее, говорит как спрос потребителей, так и особый интерес \r\nпромышленных дизайнеров, предлагающих новые решения для этих устройств.    ', '<strong>К примеру, Sony Pocket Tablet, который создал Patrik Eriksson, является очень удачным гибридом смартфона и планшета. </strong>Гибкий\r\n OLED-дисплей нового устройства позволяет легко складывать планшет, \r\nуменьшая его до размера привычного смартфона и удобно разворачивать для \r\nработы «на большом экране». <br>\r\n<br>\r\nНовые технологии позволяют создать очень удачные модели, сочетающие в \r\nсебе функционал и удобство востребованных планшетных ноутбуков и \r\nкомпактных и лёгких смартфонов.    ');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_page`
--

CREATE TABLE IF NOT EXISTS `fx_content_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `site_id` int(10) unsigned NOT NULL,
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `content_id` int(10) unsigned NOT NULL,
  `content_type` varchar(20) NOT NULL,
  `layout_id` int(11) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`,`site_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=83 ;

--
-- Дамп данных таблицы `fx_content_page`
--

INSERT INTO `fx_content_page` (`id`, `parent_id`, `site_id`, `keyword`, `user_id`, `infoblock_id`, `priority`, `checked`, `created`, `last_updated`, `seo_h1`, `seo_title`, `seo_keywords`, `seo_description`, `content_id`, `content_type`, `layout_id`, `url`) VALUES
(1, 2, 1, NULL, 0, 0, 1, 1, '2012-05-24 12:42:50', '2013-02-25 07:51:04', '', '', '', '', 8, 'section', NULL, '/goods/'),
(2, 0, 1, NULL, 0, 0, 0, 0, '2012-05-24 12:42:50', '2013-02-07 08:42:31', '', 'Мой крутой сайт', '', '', 6, 'section', NULL, '/'),
(3, 2, 1, NULL, 0, 0, 0, 0, '2012-05-24 12:42:50', '2013-02-25 07:51:04', '', '', '', '', 7, 'section', NULL, '/404/'),
(4, 6, 1, NULL, 0, 0, 1, 1, '2012-08-29 15:00:48', '2013-02-07 08:42:31', 'Вакансии!', '', '', '', 14, 'section', NULL, '/about/vacancy/'),
(5, 2, 1, NULL, 0, 0, 3, 1, '2012-05-24 12:42:50', '2013-02-25 07:51:04', '', '', '', '', 11, 'section', NULL, '/service/'),
(6, 2, 1, NULL, 0, 0, 5, 1, '2012-05-24 12:42:50', '2013-02-25 07:51:04', '', '', '', '', 12, 'section', NULL, '/about/'),
(7, 2, 1, NULL, 0, 0, -1, 0, '2012-05-24 12:42:50', '2013-02-25 07:51:04', 'Кабинет пользователя', '', '', '', 1, 'section', NULL, '/profile/'),
(8, 8, 1, NULL, 0, 0, 0, 1, '2012-05-24 12:42:50', '2013-02-07 08:42:31', '', '', '', '', 2, 'section', NULL, '/profile/registration/'),
(10, 1, 1, NULL, 0, 0, 0, 1, '2012-08-29 15:01:34', '2013-02-13 16:06:49', '', '', '', '', 9, 'section', NULL, '/utugi'),
(11, 8, 1, NULL, 0, 0, 3, 1, '2012-05-24 12:42:50', '2013-02-07 08:42:31', '', '', '', '', 3, 'section', NULL, '/profile/passwd/'),
(12, 8, 1, NULL, 0, 0, 4, 1, '2012-05-24 12:42:50', '2013-02-07 08:42:31', '', '', '', '', 4, 'section', NULL, '/profile/recoverpasswd/'),
(13, 8, 1, NULL, 0, 0, 5, 1, '2012-05-24 12:42:50', '2013-02-07 08:42:31', '', '', '', '', 5, 'section', NULL, '/profile/pm/'),
(16, 2, 1, NULL, 0, 0, 6, 1, '2012-05-28 12:27:15', '2013-02-25 07:51:04', 'Адрес и контакты', '', '', '', 16, 'section', NULL, '/contacts/'),
(17, 1, 1, NULL, 0, 0, 1, 1, '2012-08-29 15:01:51', '2013-02-13 16:06:59', '', '', '', '', 10, 'section', NULL, '/holodilniki'),
(27, 6, 1, NULL, 0, 0, 2, 1, '2012-08-29 15:01:06', '2013-02-07 08:42:31', '', '', '', '', 15, 'section', NULL, '/about/managers/'),
(37, 6, 1, NULL, 0, 0, 0, 1, '2012-08-29 15:00:32', '2013-02-13 16:05:36', 'Новости скачать бесплатно', '', '', '', 13, 'section', NULL, '/firmnews.html'),
(45, 70, 1, NULL, 0, 0, 3, 1, '2012-08-30 16:50:07', '2013-02-07 08:42:31', '', '', '', '', 8, 'newsblog', NULL, '/about/news/news_8.html'),
(46, 70, 1, NULL, 0, 0, 4, 1, '2012-08-30 16:51:07', '2013-02-07 08:42:31', '', '', '', '', 9, 'newsblog', NULL, '/about/news/news_9.html'),
(47, 70, 1, NULL, 0, 0, 5, 1, '2012-08-30 16:51:50', '2013-02-07 08:42:31', '', '', '', '', 10, 'newsblog', NULL, '/about/news/news_10.html'),
(48, 72, 1, NULL, 0, 0, 0, 1, '2012-08-30 17:00:24', '2013-02-07 08:42:31', '', '', '', '', 1, 'person', NULL, '/about/managers/people_1.html'),
(49, 72, 1, NULL, 0, 0, 1, 1, '2012-08-30 17:25:00', '2013-02-07 08:42:31', '', '', '', '', 2, 'person', NULL, '/about/managers/people_2.html'),
(56, 73, 1, NULL, 0, 0, 0, 1, '2012-08-30 15:24:36', '2013-02-07 08:42:31', '', '', '', '', 1, 'pricelist', NULL, '/goods/irons/goods_1.html'),
(57, 73, 1, NULL, 0, 0, 1, 1, '2012-08-30 15:29:45', '2013-02-07 08:42:31', '', '', '', '', 2, 'pricelist', NULL, '/goods/irons/goods_2.html'),
(58, 73, 1, NULL, 0, 0, 2, 1, '2012-08-30 15:32:36', '2013-02-07 08:42:31', '', '', '', '', 3, 'pricelist', NULL, '/goods/irons/goods_3.html'),
(59, 73, 1, NULL, 0, 0, 3, 1, '2012-08-30 16:06:03', '2013-02-07 08:42:31', '', '', '', '', 4, 'pricelist', NULL, '/goods/irons/goods_4.html'),
(60, 74, 1, NULL, 0, 0, 4, 1, '2012-08-30 16:14:48', '2013-02-07 08:42:31', '', '', '', '', 5, 'pricelist', NULL, '/goods/fridges/goods_5.html'),
(61, 74, 1, NULL, 0, 0, 6, 1, '2012-08-30 16:19:51', '2013-02-07 08:42:31', '', '', '', '', 6, 'pricelist', NULL, '/goods/fridges/goods_6.html'),
(62, 74, 1, NULL, 0, 0, 5, 1, '2012-08-30 16:22:02', '2013-02-07 08:42:31', '', '', '', '', 7, 'pricelist', NULL, '/goods/fridges/goods_7.html'),
(71, 6, 1, NULL, 0, 0, 9, 1, '2012-08-22 16:36:40', '2013-02-07 08:42:31', '', '', '', '', 12, 'text', NULL, '/about/text_12.html'),
(72, 2, 1, NULL, 0, 0, 12, 1, '2012-08-31 12:53:42', '2013-02-07 08:42:31', '', '', '', '', 23, 'text', NULL, '/index/text1_23.html'),
(73, 5, 1, NULL, 0, 0, 10, 1, '2012-08-22 17:02:59', '2013-02-07 08:42:31', '', '', '', '', 14, 'text', NULL, '/service/cond_14.html'),
(74, 23, 1, NULL, 0, 0, 14, 1, '2012-08-31 15:32:09', '2013-02-07 08:42:31', '', '', '', '', 25, 'text', NULL, '/contacts/text_25.html'),
(75, 71, 1, NULL, 0, 0, 0, 1, '2012-08-30 16:54:29', '2013-02-07 08:42:31', '', '', '', '', 1, 'vacancy', NULL, '/about/vacancy/vacancy_1.html'),
(78, 2, 1, NULL, 3, 3, 15, 1, '2013-02-25 15:07:21', '2013-02-25 11:07:21', NULL, NULL, NULL, NULL, 18, 'section', 0, '/myblog'),
(82, 2, 1, NULL, 3, 22, 16, 1, '2013-02-27 18:41:13', '2013-02-27 14:41:13', NULL, NULL, NULL, NULL, 22, 'section', 0, '/privacy.php');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_person`
--

CREATE TABLE IF NOT EXISTS `fx_content_person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `name` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `dept` varchar(255) DEFAULT NULL,
  `job` varchar(255) DEFAULT NULL,
  `photo` int(11) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `icq` varchar(255) DEFAULT NULL,
  `skype` varchar(255) DEFAULT NULL,
  `site` varchar(255) DEFAULT NULL,
  `shorttext` text,
  `text` text,
  `birthday` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `fx_content_person`
--

INSERT INTO `fx_content_person` (`id`, `parent_id`, `keyword`, `user_id`, `infoblock_id`, `priority`, `checked`, `created`, `last_updated`, `seo_h1`, `seo_title`, `seo_keywords`, `seo_description`, `name`, `company`, `dept`, `job`, `photo`, `phone`, `email`, `icq`, `skype`, `site`, `shorttext`, `text`, `birthday`) VALUES
(1, 0, NULL, 1, 128, 0, 1, '2012-08-30 17:00:24', '2012-08-30 13:21:16', NULL, NULL, NULL, NULL, 'Петрусенко Афанасий Карлович', 'ЗАО "ФЛОКСИ ШОП"', '', 'Генеральный директор', 40, '225-50-21', '', '122-235', '', 'http://petrusenko.ru/', 'Афанасий Карлович вот уже 20 лет возглавляет нашу компанию!  ', 'За двадцать лет руководства компанией Афанасий Карлович сделал многое!  ', '1962-07-10 00:00:00'),
(2, 0, NULL, 1, 128, 1, 1, '2012-08-30 17:25:00', '2012-08-30 13:25:00', NULL, NULL, NULL, NULL, 'Айсбан Прохор Леонидович', 'ЗАО "ФЛОКСИ ШОП"', 'Отдел кадров', 'Руководитель отдела кадров', 41, '225-50-24', '', '', '', '', 'Прохор Леонидович Айсбан - один из лучших специалистов по кадрам в нашей стране!  ', 'Любой участник рынка знает династию Айсбанов, которые на протяжении уже 150 лет являются кадровиками высшего класса.  ', '1982-05-08 00:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_photo`
--

CREATE TABLE IF NOT EXISTS `fx_content_photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `pic` int(11) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_pm`
--

CREATE TABLE IF NOT EXISTS `fx_content_pm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `message` text,
  `status` int(11) DEFAULT NULL,
  `to_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_pricelist`
--

CREATE TABLE IF NOT EXISTS `fx_content_pricelist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `name` varchar(255) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `image` int(11) DEFAULT NULL,
  `description` text,
  `review_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `fx_content_pricelist`
--

INSERT INTO `fx_content_pricelist` (`id`, `parent_id`, `keyword`, `user_id`, `infoblock_id`, `priority`, `checked`, `created`, `last_updated`, `seo_h1`, `seo_title`, `seo_keywords`, `seo_description`, `name`, `price`, `image`, `description`, `review_id`) VALUES
(1, 10, NULL, 1, 122, 0, 1, '2012-08-30 15:24:36', '2013-02-25 07:35:48', NULL, NULL, NULL, NULL, 'Модный утюг', 2100, 33, 'Шикарный утюг, мечта любой домохозяйки! Стильный дизайн - и очень удобный, ммм...  ', NULL),
(2, 10, NULL, 1, 122, 1, 1, '2012-08-30 15:29:45', '2013-02-25 07:35:52', NULL, NULL, NULL, NULL, 'Утюг для пыток', 5300, 34, 'Этот утюг был разработан компанией Porsche по заказу сицилийской мафии, специально для пыток!   ', NULL),
(3, 10, NULL, 1, 122, 2, 1, '2012-08-30 15:32:36', '2013-02-25 07:35:54', NULL, NULL, NULL, NULL, 'Утюг для холостяков', 1800, 35, 'Утюг оснащен антипригарным покрытием, благодаря чему утюг годится для приготовления яичницы. Идеально подходит холостым мужчинам!  ', NULL),
(4, 10, NULL, 1, 122, 3, 1, '2012-08-30 16:06:03', '2013-02-25 07:35:57', NULL, NULL, NULL, NULL, 'Винтажный утюг!', 4200, 36, 'Такой утюг наверняка был у твоей бабушки!  ', NULL),
(5, 17, NULL, 1, 123, 4, 1, '2012-08-30 16:14:48', '2013-02-25 07:36:18', NULL, NULL, NULL, NULL, 'Очень черный холодильник', 43000, 37, 'Кому нужен такой черный холодильник??? Кто вообще будет его покупать?  ', NULL),
(6, 17, NULL, 1, 123, 6, 1, '2012-08-30 16:19:51', '2013-02-25 07:36:21', NULL, NULL, NULL, NULL, 'Довольно серый холодильник', 43500, 38, 'Хотя довольно серый холодильник и довольно сер, и, к тому же, стоит на 500 рублей дороже, чем черный, мы настоятельно рекомендуем купить именно его!  ', NULL),
(7, 17, NULL, 1, 123, 5, 1, '2012-08-30 16:22:02', '2013-02-25 07:36:24', NULL, NULL, NULL, NULL, 'Холодильник с соса-солой', 12200, 39, 'Красный холодильник с лучшим напитком на свете!<br>Внимание! Цена кока-колы не входит в цену холодильника!  ', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_quotes`
--

CREATE TABLE IF NOT EXISTS `fx_content_quotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `quote` text,
  `author` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_resume`
--

CREATE TABLE IF NOT EXISTS `fx_content_resume` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `name` varchar(255) DEFAULT NULL,
  `birthday` datetime DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `district` varchar(255) DEFAULT NULL,
  `job` varchar(255) DEFAULT NULL,
  `salary_from` varchar(255) DEFAULT NULL,
  `text` text,
  `experience` text,
  `education` text,
  `links` text,
  `contact` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_resumecontacts`
--

CREATE TABLE IF NOT EXISTS `fx_content_resumecontacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `site` varchar(255) DEFAULT NULL,
  `jabber` varchar(255) DEFAULT NULL,
  `icq` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `fb` varchar(255) DEFAULT NULL,
  `vk` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `postal` varchar(255) DEFAULT NULL,
  `extra` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_resumeeducation`
--

CREATE TABLE IF NOT EXISTS `fx_content_resumeeducation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `name` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `degree` varchar(255) DEFAULT NULL,
  `year_start` int(11) DEFAULT NULL,
  `year_end` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_resumeexperience`
--

CREATE TABLE IF NOT EXISTS `fx_content_resumeexperience` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `company` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `job` varchar(255) DEFAULT NULL,
  `responsibilities` text,
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `advice_contact` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_resumelinks`
--

CREATE TABLE IF NOT EXISTS `fx_content_resumelinks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `title` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_section`
--

CREATE TABLE IF NOT EXISTS `fx_content_section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

--
-- Дамп данных таблицы `fx_content_section`
--

INSERT INTO `fx_content_section` (`id`, `parent_id`, `keyword`, `user_id`, `infoblock_id`, `priority`, `checked`, `created`, `last_updated`, `seo_h1`, `seo_title`, `seo_keywords`, `seo_description`, `name`) VALUES
(1, 2, NULL, 0, 0, 0, 1, '2012-12-19 15:56:03', '2013-02-25 07:29:34', NULL, NULL, NULL, NULL, 'Кабинет'),
(2, 7, NULL, 0, 0, 1, 1, '2012-12-19 15:56:03', '2013-02-25 07:29:34', NULL, NULL, NULL, NULL, 'Регистрация'),
(3, 7, NULL, 0, 0, 2, 1, '2012-12-19 15:56:03', '2013-02-25 07:29:34', NULL, NULL, NULL, NULL, 'Смена пароля'),
(4, 7, NULL, 0, 0, 3, 1, '2012-12-19 15:56:03', '2013-02-25 07:29:34', NULL, NULL, NULL, NULL, 'Восстановление пароля'),
(5, 7, NULL, 0, 0, 4, 1, '2012-12-19 15:56:03', '2013-02-25 07:29:34', NULL, NULL, NULL, NULL, 'Личные сообщения'),
(6, 0, NULL, 0, 0, 5, 1, '2012-12-19 15:56:03', '2012-12-19 11:56:03', NULL, NULL, NULL, NULL, 'Титульная страница'),
(7, 2, NULL, 0, 0, 6, 1, '2012-12-19 15:56:03', '2013-02-25 07:29:34', NULL, NULL, NULL, NULL, 'Страница не найдена'),
(8, 2, NULL, 0, 3, 7, 1, '2012-12-19 15:56:04', '2013-02-27 10:26:34', NULL, NULL, NULL, NULL, 'Продукция'),
(9, 1, NULL, 0, 0, 8, 1, '2012-12-19 15:56:04', '2013-03-01 13:53:15', NULL, NULL, NULL, NULL, 'Утюги'),
(10, 1, NULL, 0, 0, 9, 1, '2012-12-19 15:56:04', '2013-02-25 07:29:34', NULL, NULL, NULL, NULL, 'Холодильники'),
(11, 2, NULL, 0, 3, 10, 1, '2012-12-19 15:56:04', '2013-02-25 07:29:34', NULL, NULL, NULL, NULL, 'Услуги'),
(12, 2, NULL, 0, 3, 11, 1, '2012-12-19 15:56:04', '2013-02-25 07:29:34', NULL, NULL, NULL, NULL, 'О компании'),
(13, 6, NULL, 0, 3, 12, 1, '2012-12-19 15:56:04', '2013-02-25 07:29:34', NULL, NULL, NULL, NULL, 'Новости'),
(14, 6, NULL, 0, 0, 13, 1, '2012-12-19 15:56:04', '2013-02-25 07:29:34', NULL, NULL, NULL, NULL, 'Вакансии'),
(15, 6, NULL, 0, 0, 14, 1, '2012-12-19 15:56:04', '2013-02-25 07:29:34', NULL, NULL, NULL, NULL, 'Менеджеры'),
(16, 2, NULL, 0, 3, 15, 1, '2012-12-19 15:56:04', '2013-02-25 07:29:34', NULL, NULL, NULL, NULL, 'Контакты'),
(18, 2, NULL, 3, 3, 16, 1, '2013-02-25 15:07:21', '2013-02-25 11:07:21', NULL, NULL, NULL, NULL, 'Блог'),
(22, 2, NULL, 3, 22, 17, 1, '2013-02-27 18:41:13', '2013-02-27 14:41:13', NULL, NULL, NULL, NULL, 'Политика конфиденциальности');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_text`
--

CREATE TABLE IF NOT EXISTS `fx_content_text` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `text` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;

--
-- Дамп данных таблицы `fx_content_text`
--

INSERT INTO `fx_content_text` (`id`, `parent_id`, `keyword`, `user_id`, `infoblock_id`, `priority`, `checked`, `created`, `last_updated`, `seo_h1`, `seo_title`, `seo_keywords`, `seo_description`, `text`) VALUES
(27, 78, NULL, 3, 15, 15, 1, '2013-02-25 15:39:27', '2013-03-01 11:59:17', NULL, NULL, NULL, NULL, 'В нашем блоге есть все!  '),
(28, 2, NULL, 3, 15, 16, 1, '2013-02-25 15:46:09', '2013-03-02 15:56:53', NULL, NULL, NULL, NULL, 'Приветствуем на главной странице нашего утюга!&nbsp;<div>У нас Вы - найдете каталог утюгов и холодильников.<br>И еще много чего.</div>'),
(29, 2, NULL, 3, 16, 17, 1, '2013-02-25 16:41:40', '2013-02-25 12:50:12', NULL, NULL, NULL, NULL, '<b>Внимание! АКЦИЯ!!!</b><div><br><div>Купите <i>что-нибудь</i> сегодня!</div><div><br></div><div>И получите великолепный подарок от нашей фирмы.</div>  </div>'),
(30, 5, NULL, 3, 15, 18, 1, '2013-02-25 17:20:20', '2013-02-25 13:20:20', NULL, NULL, NULL, NULL, 'Это текст про услуги  '),
(31, 2, NULL, 3, 19, 19, 1, '2013-02-25 18:17:19', '2013-03-01 11:26:04', NULL, NULL, NULL, NULL, 'Вчера на наш склад привезли новую партию уникальных японских утюгов.  <div><br></div><div>Спешите, количество ограничено!</div>'),
(12, 0, NULL, 1, 80, 9, 1, '2012-08-22 16:36:40', '2012-08-29 12:16:17', NULL, NULL, NULL, NULL, 'Интернет-магазин <strong>FloxiShop</strong> предлагает большой ассортимент электроники, бытовой \nтехники и товаров для автомобилистов по доступным ценам. Мы делаем все \nвозможное, чтобы покупки в нашем интернет магазине доставляли вам только\n положительные эмоции, и вам захотелось вернуться сюда еще не один раз.<br> <br>Почему нужно покупать в интернет-магазине <strong>FloxiShop</strong>? Почему именно мы, ведь подобных магазинов довольно много?\n<ul><li>Во-первых; мы располагаемся непосредственно <strong>в Зеленограде</strong>. Мы, как говорится, местные, и этим сказано если не все, то многое;</li><li>Во–вторых, позвонив нам, Вы можете быть уверенными на все 100%, что Вам ответит <strong>реальный человек</strong>&nbsp;реального интернет-магазина;</li><li>В-третьих, мы <strong>работаем полностью легально</strong>,\n у нас есть зеленоградский адрес, директор, персонал, реальные телефоны,\n сайт, зарегистрированный в РФ и т.д., мы платим все налоги и полностью \nсдаем отчетность;</li><li>В четвертых мы работаем только с \nофициальным «белым» товаром, а это означает, что в случае возникновения \nнеисправности или выявления брака производителя, Вам товар обменяют или \nотремонтируют бесплатно в соответствии с требованиями Закона «О защите \nправ потребителей»;</li><li>&nbsp;В-пятых, для нас не проблема отправить любой предварительно оплаченный товар в любую точку России;</li><li>&nbsp;В-шестых, у нас низкие цены на \nтехнику. Цены на товары ниже, чем в обычных магазинах электроники в \nсвязи с экономией на затратах, связанных с арендой торговых и офисных \nпомещений;&nbsp;</li><li>В-седьмых, доставку по Зеленограду \nмы в настоящее время осуществляем бесплатно (для товаров весом не более \n10 кг.). А если доставка бесплатна, то и нет надобности в пункте \nсамовывоза. Доставка в близлежащие населенные пункты обойдется гораздо \nдешевле, чем у конкурентов;</li><li>В-восьмых, мы ввели такую услугу, \nкак сборка компьютера по вашему заказу. Вам нужно только указать \nвыбранную конфигурацию компьютера нашему менеджеру, который просчитав \nзаказ, согласует с Вами цены и сроки (обычно 2 рабочих дня) и, \nсобственно все;</li><li>В-девятых, мы можем предложить такую услугу, как установка купленной бытовой техники. Подключим, проверим, настроим*;</li><li>И, наконец, заключительное: ошибки \nсовершают все, главное их своевременно исправлять и не совершать вновь. У\n каждого курьера имеется журнал претензий и замечаний клиента, где Вы \nвсегда можете оставить свою запись о том, что не понравилось, что нужно \nисправить. Руководство своевременно отреагирует на оставленную запись и \nизвестит Вас о принятых мерах.<br></li></ul>'),
(23, 0, NULL, 1, 30, 12, 1, '2012-08-31 12:53:42', '2012-10-26 10:56:28', NULL, NULL, NULL, NULL, 'Интернет-магазин <strong>FloxiShop</strong> предлагает большой ассортимент электроники, бытовой \nтехники и товаров для автомобилистов по доступным !ценам. Мы делаем все \nвозможное, чтобы покупки в нашем интернет магазине доставляли вам только\n положительные эмоции, и вам захотелось вернуться сюда еще не один раз. <br><br><a>Подробнее об интернет-магазине FloxiShop &gt;&gt;&gt;</a>'),
(14, 0, NULL, 1, 6, 10, 1, '2012-08-22 17:02:59', '2013-02-13 12:16:40', NULL, NULL, NULL, NULL, 'Наши условия - самые лучшие. Иногда в них вносят небольшие изменения, за ними можно следить в этом разделе. Но они все равно остаются лучшими на российском и зарубежном рынке. Даже не пытайтесь найти что-то лучшее, чем наши условия!  '),
(25, 0, NULL, 1, 132, 14, 1, '2012-08-31 15:32:09', '2012-08-31 11:32:53', NULL, NULL, NULL, NULL, '<p class="adr">\n  <strong>Адрес</strong>: \n  <span class="country-name">Россия</span>,\n  <span class="locality">Москва</span>, \n  <span class="street-address">ул. Большая Почтовая, д. 38 стр. 6</span> (мансарда здания)\n</p>\n<p><strong>Адрес для отправки корреспонденции</strong>: 105082, г. Москва, ул. Большая Почтовая, д. 38 стр. 6, ООО "НетКэт".</p>\n<p><strong>Отдел по работе с партнерами/отдел продаж</strong>: <a>dealer@netcat.ru</a>\n <abbr class="value" title="+ 7 (495) 632-1529">+ 7 (495) 632-1529</abbr> </p>\n  <p class="tel"><strong>По общим вопросам:</strong> \n  <a class="email">info@netcat.ru</a>\n<abbr class="value" title="+ 7 (495) 632-1529">+ 7 (495) 632-1529</abbr>\n</p>\n<p><strong>Техническая поддержка:</strong> <a class="email">support@netcat.ru</a> \n  <abbr class="value" title="+ 7 (495) 632-1529">+ 7 (495) 632-1529</abbr>\n</p>\n<p><strong>Время работы:</strong> <span class="workhours">пн-пт 10:00-19:00</span></p>');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_user`
--

CREATE TABLE IF NOT EXISTS `fx_content_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL DEFAULT '0',
  `password` varchar(45) NOT NULL,
  `type` enum('useful','twitter','facebook','openid') NOT NULL DEFAULT 'useful',
  `checked` tinyint(4) NOT NULL DEFAULT '0',
  `priority` int(11) DEFAULT '0' COMMENT 'Приоритет',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `email` char(255) NOT NULL,
  `keyword` varchar(255) DEFAULT NULL,
  `login` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `registration_code` varchar(45) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `forum_signature` varchar(255) DEFAULT NULL,
  `forum_messages` int(11) NOT NULL DEFAULT '0',
  `pa_balance` double NOT NULL DEFAULT '0',
  `auth_hash` varchar(50) NOT NULL DEFAULT '',
  `first_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `User_ID` (`id`),
  KEY `Checked` (`checked`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `fx_content_user`
--

INSERT INTO `fx_content_user` (`id`, `site_id`, `password`, `type`, `checked`, `priority`, `created`, `last_updated`, `email`, `keyword`, `login`, `name`, `registration_code`, `avatar`, `forum_signature`, `forum_messages`, `pa_balance`, `auth_hash`, `first_name`) VALUES
(3, 0, '698d51a19d8a121ce581499d7b701668', 'useful', 1, 0, '0000-00-00 00:00:00', '2012-12-17 15:28:25', 'admin@floxim', NULL, 'admin', 'Adminio', NULL, NULL, NULL, 0, 0, '', NULL),
(2, 1, '698d51a19d8a121ce581499d7b701668', 'useful', 1, 0, '2012-08-07 19:04:54', '2012-08-08 11:03:10', 'dubrowsky@yandex.ru', NULL, 'dubrowsky', 'Dubrowsky', '', '24', 'per aspera ad astra', 0, 0, '', 'Дубровский');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_vacancy`
--

CREATE TABLE IF NOT EXISTS `fx_content_vacancy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `infoblock_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_h1` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `job` varchar(255) DEFAULT NULL,
  `requirement` text,
  `respons` text,
  `term` text,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contacts` varchar(255) DEFAULT NULL,
  `pay_from` varchar(255) DEFAULT NULL,
  `pay_to` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `fx_content_vacancy`
--

INSERT INTO `fx_content_vacancy` (`id`, `parent_id`, `keyword`, `user_id`, `infoblock_id`, `priority`, `checked`, `created`, `last_updated`, `seo_h1`, `seo_title`, `seo_keywords`, `seo_description`, `job`, `requirement`, `respons`, `term`, `address`, `phone`, `email`, `contacts`, `pay_from`, `pay_to`) VALUES
(1, 0, NULL, 1, 127, 0, 1, '2012-08-30 16:54:29', '2012-12-14 09:44:34', NULL, NULL, NULL, NULL, 'Старший менеджер', 'Образование - не ниже 5 классов.<br>Исполнительность, лояльность.  ', 'Руководить службой доставки утюгов.  ', 'Гибкий график, работенка не пыльная! =)  ', 'Москва', '225-50-22', '', 'Прохор Леонидович', '120110 р.', '15000 р.');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_controller`
--

CREATE TABLE IF NOT EXISTS `fx_controller` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `type` enum('component','widget','layout','other') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_crontask`
--

CREATE TABLE IF NOT EXISTS `fx_crontask` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `every_days` int(11) NOT NULL DEFAULT '0',
  `every_hours` int(11) NOT NULL DEFAULT '0',
  `every_minutes` int(11) NOT NULL DEFAULT '0',
  `path` varchar(255) NOT NULL,
  `last_launch` int(11) NOT NULL,
  `checked` int(11) NOT NULL DEFAULT '1',
  `send_email_type` int(11) NOT NULL DEFAULT '0',
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `fx_crontask`
--

INSERT INTO `fx_crontask` (`id`, `name`, `every_days`, `every_hours`, `every_minutes`, `path`, `last_launch`, `checked`, `send_email_type`, `email`) VALUES
(1, 'Пересчет пузомерок', 2, 0, 0, '/puzo.php', 0, 0, 0, '');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_ctpl`
--

CREATE TABLE IF NOT EXISTS `fx_ctpl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `component_id` int(11) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `rec_num` int(11) DEFAULT '20' COMMENT 'Количество записей по умолчанию',
  `sort` text NOT NULL COMMENT 'Сортировка по умолчанию, все доступные типы сортировок',
  `action` text NOT NULL COMMENT 'Действие по умолчанию и все доступные действия',
  `with_list` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Шаблон со списком объектов (иначе можно добавить только один объект)',
  `with_full` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Шаблон с полным выводом',
  `type` enum('useful','select') DEFAULT 'useful',
  `widget` tinyint(1) NOT NULL DEFAULT '1',
  `notwidget` tinyint(1) NOT NULL DEFAULT '1',
  `embed` enum('miniblock','narrow','wide','narrow-wide') NOT NULL DEFAULT 'narrow-wide',
  `access` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- Дамп данных таблицы `fx_ctpl`
--

INSERT INTO `fx_ctpl` (`id`, `parent_id`, `component_id`, `keyword`, `name`, `rec_num`, `sort`, `action`, `with_list`, `with_full`, `type`, `widget`, `notwidget`, `embed`, `access`) VALUES
(1, 0, 1, 'main', 'Пользователи', 20, 'a:1:{s:4:"type";s:6:"manual";}', 'a:2:{s:7:"default";s:5:"index";s:7:"enabled";a:3:{i:0;s:5:"index";i:1;s:3:"add";i:2;s:6:"search";}}', 1, 1, 'useful', 0, 1, 'narrow-wide', 'a:5:{s:4:"read";s:3:"all";s:3:"add";s:3:"all";s:4:"edit";s:4:"auth";s:7:"checked";s:4:"auth";s:6:"delete";s:4:"auth";}'),
(2, 0, 2, 'main', 'Статьи', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(3, 0, 2, 'reprint', 'Перепечатка', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(4, 0, 2, 'press', 'Пресс-релиз', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(5, 0, 2, 'shortview', 'Краткий вывод', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(6, 0, 3, 'main', 'Награды и дипломы', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(7, 0, 4, 'main', 'Каталог товаров', 20, 'a:1:{s:4:"type";s:6:"manual";}', 'a:2:{s:7:"default";s:5:"index";s:7:"enabled";a:3:{i:0;s:5:"index";i:1;s:3:"add";i:2;s:6:"search";}}', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(8, 0, 4, 'pricelist', 'Прайс-лист', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(9, 0, 4, 'random', 'Случайный товар', 1, 'a:1:{s:4:"type";s:6:"random";}', 'a:2:{s:7:"default";s:5:"index";s:7:"enabled";a:3:{i:0;s:5:"index";i:1;s:3:"add";i:2;s:6:"search";}}', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(10, 0, 5, 'main', 'Комментарии', 20, 'a:1:{s:4:"type";s:6:"manual";}', 'a:2:{s:7:"default";s:5:"index";s:7:"enabled";a:3:{i:0;s:5:"index";i:1;s:3:"add";i:2;s:6:"search";}}', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(11, 0, 5, 'last', 'Последний комментарий', 1, 'a:1:{s:4:"type";s:4:"last";}', 'a:2:{s:7:"default";s:5:"index";s:7:"enabled";a:3:{i:0;s:5:"index";i:1;s:3:"add";i:2;s:6:"search";}}', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(12, 0, 6, 'companylogo', 'Логотипы', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(13, 0, 6, 'main', 'Компании, кратко', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(14, 0, 7, 'main', 'Министатьи', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(15, 0, 8, 'main', 'Новости', 20, 'a:1:{s:4:"type";s:6:"manual";}', 'a:2:{s:7:"default";s:5:"index";s:7:"enabled";a:3:{i:0;s:5:"index";i:1;s:3:"add";i:2;s:6:"search";}}', 1, 1, 'useful', 1, 1, 'narrow-wide', 'a:5:{s:4:"read";s:3:"all";s:3:"add";s:4:"auth";s:4:"edit";s:4:"auth";s:7:"checked";s:4:"auth";s:6:"delete";s:4:"auth";}'),
(16, 0, 8, 'title', 'Заголовки', 20, 'a:1:{s:4:"type";s:4:"last";}', 'a:2:{s:7:"default";s:5:"index";s:7:"enabled";a:2:{i:0;s:5:"index";i:1;s:3:"add";}}', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(17, 0, 8, 'blog', 'Блог', 20, 'a:1:{s:4:"type";s:6:"manual";}', 'a:2:{s:7:"default";s:5:"index";s:7:"enabled";a:3:{i:0;s:5:"index";i:1;s:3:"add";i:2;s:6:"search";}}', 1, 1, 'useful', 1, 1, 'narrow-wide', 'a:5:{s:4:"read";s:3:"all";s:3:"add";s:4:"auth";s:4:"edit";s:4:"auth";s:7:"checked";s:4:"auth";s:6:"delete";s:4:"auth";}'),
(18, 0, 9, 'main', 'Персоны', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(19, 0, 9, 'shortperson', 'Краткий вывод', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(20, 0, 10, 'slider', 'Слайдер', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(21, 0, 10, 'random', 'Случайная картинка', 1, 'a:2:{s:4:"type";s:6:"random";s:12:"unchangeable";s:1:"1";}', 'a:2:{s:7:"default";s:5:"index";s:7:"enabled";a:1:{i:0;s:5:"index";}}', 1, 1, 'useful', 1, 1, 'narrow-wide', 'a:5:{s:4:"read";s:3:"all";s:3:"add";s:4:"auth";s:4:"edit";s:4:"auth";s:7:"checked";s:4:"auth";s:6:"delete";s:4:"auth";}'),
(22, 0, 10, 'main', 'Список картинок', 20, 'a:1:{s:4:"type";s:4:"last";}', 'a:2:{s:7:"default";s:5:"index";s:7:"enabled";a:1:{i:0;s:5:"index";}}', 1, 1, 'useful', 1, 1, 'wide', ''),
(23, 0, 11, 'main', 'Личные сообщения', 20, 'a:1:{s:4:"type";s:6:"manual";}', 'a:2:{s:7:"default";s:5:"index";s:7:"enabled";a:3:{i:0;s:5:"index";i:1;s:3:"add";i:2;s:6:"search";}}', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(24, 0, 12, 'main', 'Прайс-лист краткий', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(25, 0, 13, 'main', 'Цитаты', 20, 'a:1:{s:4:"type";s:6:"manual";}', 'a:2:{s:7:"default";s:5:"index";s:7:"enabled";a:3:{i:0;s:5:"index";i:1;s:3:"add";i:2;s:6:"search";}}', 1, 1, 'useful', 1, 1, 'narrow-wide', 'a:5:{s:4:"read";s:3:"all";s:3:"add";s:4:"auth";s:4:"edit";s:4:"auth";s:7:"checked";s:4:"auth";s:6:"delete";s:4:"auth";}'),
(26, 0, 14, 'main', 'Резюме', 1, 'a:2:{s:4:"type";s:6:"manual";s:12:"unchangeable";s:1:"1";}', 'a:2:{s:7:"default";s:5:"index";s:7:"enabled";a:1:{i:0;s:5:"index";}}', 1, 1, 'useful', 1, 1, 'narrow-wide', 'a:5:{s:4:"read";s:3:"all";s:3:"add";s:4:"auth";s:4:"edit";s:4:"auth";s:7:"checked";s:4:"auth";s:6:"delete";s:4:"auth";}'),
(27, 0, 14, 'resumelist', 'Список резюме', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(28, 0, 15, 'main', 'Резюме: контакты (Контакты персональные)', 1, 'a:1:{s:4:"type";s:6:"manual";}', 'a:2:{s:7:"default";s:5:"index";s:7:"enabled";a:3:{i:0;s:5:"index";i:1;s:3:"add";i:2;s:6:"search";}}', 1, 1, 'useful', 1, 1, 'narrow-wide', 'a:5:{s:4:"read";s:3:"all";s:3:"add";s:4:"auth";s:4:"edit";s:4:"auth";s:7:"checked";s:4:"auth";s:6:"delete";s:4:"auth";}'),
(29, 0, 16, 'main', 'Резюме: образование', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(30, 0, 17, 'main', 'Резюме: опыт работы', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(31, 0, 18, 'main', 'Резюме: список проектов (Ссылки)', 20, 'a:1:{s:4:"type";s:6:"manual";}', 'a:2:{s:7:"default";s:5:"index";s:7:"enabled";a:3:{i:0;s:5:"index";i:1;s:3:"add";i:2;s:6:"search";}}', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(32, 0, 19, 'main', 'Текст', 20, 'a:1:{s:4:"type";s:6:"manual";}', 'a:2:{s:7:"default";s:5:"index";s:7:"enabled";a:1:{i:0;s:5:"index";}}', 1, 1, 'useful', 1, 1, 'narrow-wide', 'a:5:{s:4:"read";s:3:"all";s:3:"add";s:4:"auth";s:4:"edit";s:4:"auth";s:7:"checked";s:4:"auth";s:6:"delete";s:4:"auth";}'),
(33, 0, 20, 'main', 'Вакансии', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(34, 0, 20, 'short', 'Краткий вывод', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(36, 0, 22, 'main', 'Вопрос-ответ', 20, 'a:1:{s:4:"type";s:6:"manual";}', 'a:2:{s:7:"default";s:5:"index";s:7:"enabled";a:2:{i:0;s:5:"index";i:1;s:3:"add";}}', 1, 1, 'useful', 1, 1, 'narrow-wide', 'a:5:{s:4:"read";s:3:"all";s:3:"add";s:3:"all";s:4:"edit";s:4:"auth";s:7:"checked";s:4:"auth";s:6:"delete";s:4:"auth";}'),
(37, 0, 12, 'random', 'Случайный товар', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(38, 0, 12, 'salehits', 'Хиты продаж', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(39, 0, 9, 'imagedesc', 'Картинка-описание', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(40, 0, 23, 'main', 'Страницы', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', ''),
(41, 0, 24, 'main', 'Разделы', 20, '', '', 1, 1, 'useful', 1, 1, 'narrow-wide', '');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_datatype`
--

CREATE TABLE IF NOT EXISTS `fx_datatype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(64) NOT NULL,
  `priority` int(11) DEFAULT NULL,
  `searchable` tinyint(1) NOT NULL DEFAULT '1',
  `not_null` tinyint(1) NOT NULL DEFAULT '1',
  `default` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Дамп данных таблицы `fx_datatype`
--

INSERT INTO `fx_datatype` (`id`, `name`, `priority`, `searchable`, `not_null`, `default`) VALUES
(1, 'string', 1, 1, 1, 1),
(2, 'int', 2, 1, 1, 1),
(3, 'text', 3, 1, 1, 0),
(4, 'select', 4, 1, 1, 1),
(5, 'bool', 5, 1, 1, 1),
(6, 'file', 6, 0, 1, 0),
(7, 'float', 7, 1, 1, 1),
(8, 'datetime', 8, 1, 1, 1),
(9, 'color', 9, 1, 1, 1),
(10, 'infoblock', 10, 0, 0, 0),
(11, 'image', 11, 0, 1, 0),
(12, 'multiselect', 12, 1, 1, 0),
(13, 'link', 13, 1, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_field`
--

CREATE TABLE IF NOT EXISTS `fx_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` varchar(255) DEFAULT NULL,
  `component_id` int(11) NOT NULL DEFAULT '0',
  `ctpl_id` int(11) NOT NULL DEFAULT '0',
  `system_table_id` int(11) NOT NULL DEFAULT '0',
  `widget_id` int(11) NOT NULL DEFAULT '0',
  `name` char(64) NOT NULL,
  `description` char(255) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `format` text NOT NULL,
  `not_null` smallint(6) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `searchable` smallint(6) NOT NULL DEFAULT '1',
  `default` char(255) DEFAULT NULL,
  `inheritance` smallint(6) NOT NULL DEFAULT '0',
  `type_of_edit` int(11) NOT NULL DEFAULT '1',
  `checked` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `component_id` (`component_id`),
  KEY `TypeOfData_ID` (`type`),
  KEY `System_Table_ID` (`system_table_id`),
  KEY `Widget_Class_ID` (`widget_id`),
  KEY `TypeOfEdit_ID` (`type_of_edit`),
  KEY `Checked` (`checked`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=166 ;

--
-- Дамп данных таблицы `fx_field`
--

INSERT INTO `fx_field` (`id`, `parent`, `component_id`, `ctpl_id`, `system_table_id`, `widget_id`, `name`, `description`, `type`, `format`, `not_null`, `priority`, `searchable`, `default`, `inheritance`, `type_of_edit`, `checked`) VALUES
(1, NULL, 1, 0, 0, 0, 'name', 'Имя на сайте', 1, '', 0, 0, 1, NULL, 0, 1, 1),
(2, NULL, 1, 0, 0, 0, 'avatar', 'Аватар', 6, '', 0, 0, 1, NULL, 0, 1, 1),
(3, NULL, 1, 0, 0, 0, 'forum_signature', 'Подпись на форуме', 3, '', 0, 0, 1, NULL, 0, 1, 1),
(4, NULL, 2, 0, 0, 0, 'title', 'Заголовок', 1, '', 1, 0, 1, NULL, 0, 1, 1),
(5, NULL, 2, 0, 0, 0, 'publisher', 'Название издания', 1, '', 0, 0, 1, NULL, 0, 1, 1),
(6, NULL, 2, 0, 0, 0, 'issue', 'Номер/выпуск', 1, '', 0, 0, 1, NULL, 0, 1, 1),
(7, NULL, 2, 0, 0, 0, 'author', 'Автор', 1, '', 0, 0, 1, NULL, 0, 1, 1),
(8, NULL, 2, 0, 0, 0, 'author_link', 'Сайт или email автора', 1, '', 0, 0, 1, NULL, 0, 1, 1),
(9, NULL, 2, 0, 0, 0, 'pic', 'Картинка / лого', 11, '', 0, 0, 1, NULL, 0, 1, 1),
(10, NULL, 2, 0, 0, 0, 'note', 'Аннотация', 3, 'a:2:{s:4:"html";s:1:"1";s:7:"wysiwyg";s:1:"1";}', 0, 0, 1, NULL, 0, 1, 1),
(11, NULL, 2, 0, 0, 0, 'text', 'Текст', 3, 'a:2:{s:4:"html";s:1:"1";s:7:"wysiwyg";s:1:"1";}', 0, 0, 1, NULL, 0, 1, 1),
(12, NULL, 2, 0, 0, 0, 'file', 'Файл для скачивания', 6, '', 0, 0, 1, NULL, 0, 1, 1),
(13, NULL, 2, 0, 0, 0, 'source', 'Ссылка на оригинал', 1, '', 0, 0, 1, NULL, 0, 1, 1),
(14, NULL, 3, 0, 0, 0, 'pic', 'Изображение', 11, '', 1, 7, 1, NULL, 0, 1, 1),
(15, NULL, 3, 0, 0, 0, 'caption', 'Подпись', 1, '', 0, 8, 1, NULL, 0, 1, 1),
(16, NULL, 3, 0, 0, 0, 'year', 'Год получения', 8, '', 0, 9, 1, NULL, 0, 1, 1),
(17, NULL, 4, 0, 0, 0, 'name', 'Название', 1, '', 0, 0, 1, NULL, 0, 1, 1),
(18, NULL, 4, 0, 0, 0, 'text', 'Описание', 3, '', 0, 0, 1, NULL, 0, 1, 1),
(19, NULL, 4, 0, 0, 0, 'price', 'Цена', 2, '', 0, 0, 1, NULL, 0, 1, 1),
(20, NULL, 4, 0, 0, 0, 'picture', 'Изображение', 6, '', 0, 0, 1, NULL, 0, 1, 1),
(21, NULL, 4, 0, 0, 0, 'comm', 'Комментарии', 10, 'a:1:{s:15:"components_type";s:3:"all";}', 0, 0, 1, NULL, 0, 1, 1),
(22, NULL, 5, 0, 0, 0, 'text', 'Текст комментария!', 3, '', 0, 0, 0, '', 0, 1, 1),
(23, NULL, 6, 0, 0, 0, 'name', 'Название компании', 1, '', 0, 0, 1, NULL, 0, 1, 1),
(24, NULL, 6, 0, 0, 0, 'logo', 'Логотип', 11, '', 0, 0, 1, NULL, 0, 1, 1),
(25, NULL, 6, 0, 0, 0, 'brief', 'Краткое описание', 3, 'a:2:{s:4:"html";s:1:"1";s:7:"wysiwyg";s:1:"1";}', 0, 0, 1, NULL, 0, 1, 1),
(26, NULL, 6, 0, 0, 0, 'description', 'Полное описание', 3, 'a:2:{s:4:"html";s:1:"1";s:7:"wysiwyg";s:1:"1";}', 0, 0, 1, NULL, 0, 1, 1),
(27, NULL, 6, 0, 0, 0, 'url', 'Ссылка на сайт', 1, '', 0, 0, 1, NULL, 0, 1, 1),
(28, NULL, 7, 0, 0, 0, 'title', 'Название статьи', 1, '', 1, 8, 1, NULL, 0, 1, 1),
(29, NULL, 7, 0, 0, 0, 'shorttext', 'Краткий текст', 3, 'a:2:{s:4:"html";s:1:"1";s:7:"wysiwyg";s:1:"1";}', 1, 9, 1, NULL, 0, 1, 1),
(30, NULL, 7, 0, 0, 0, 'text', 'Полный текст', 3, 'a:2:{s:4:"html";s:1:"1";s:7:"wysiwyg";s:1:"1";}', 0, 10, 1, NULL, 0, 1, 1),
(31, NULL, 8, 0, 0, 0, 'title', 'Заголовок', 1, '', 1, 0, 1, NULL, 0, 1, 1),
(32, NULL, 8, 0, 0, 0, 'link', 'Внешняя ссылка', 1, '', 0, 1, 1, NULL, 0, 1, 1),
(33, NULL, 8, 0, 0, 0, 'announce', 'Анонс', 3, 'a:2:{s:4:"html";s:1:"1";s:7:"wysiwyg";s:1:"1";}', 0, 2, 1, NULL, 0, 1, 1),
(34, NULL, 8, 0, 0, 0, 'text', 'Текст', 3, 'a:2:{s:4:"html";s:1:"1";s:7:"wysiwyg";s:1:"1";}', 0, 3, 1, NULL, 0, 1, 1),
(35, NULL, 0, 15, 0, 0, 'group', 'Группировка', 4, 'a:2:{s:6:"source";s:6:"manual";s:6:"values";a:4:{i:1;a:2:{s:2:"id";s:4:"none";s:5:"value";s:6:"нет";}i:2;a:2:{s:2:"id";s:4:"year";s:5:"value";s:15:"по годам";}i:3;a:2:{s:2:"id";s:5:"month";s:5:"value";s:19:"по месяцам";}i:4;a:2:{s:2:"id";s:3:"day";s:5:"value";s:13:"по дням";}}}', 0, 88, 1, 'none', 0, 1, 1),
(36, NULL, 0, 15, 0, 0, 'date_place', 'Выводить дату', 4, 'a:2:{s:6:"source";s:6:"manual";s:6:"values";a:2:{i:1;a:2:{s:2:"id";s:11:"after_title";s:5:"value";s:27:"Под заголовком";}i:2;a:2:{s:2:"id";s:10:"after_text";s:5:"value";s:21:"Под текстом";}}}', 0, 89, 1, 'after_title', 0, 1, 1),
(37, NULL, 0, 15, 0, 0, 'show_announce', 'Выводить анонс в полном выводе новости', 5, '', 0, 90, 1, NULL, 0, 1, 1),
(38, NULL, 0, 16, 0, 0, 'view', 'Разделитель', 4, 'a:2:{s:6:"source";s:6:"manual";s:6:"values";a:3:{i:1;a:2:{s:2:"id";s:5:"comma";s:5:"value";s:25:"через запятую";}i:2;a:2:{s:2:"id";s:2:"br";s:5:"value";s:38:"через перенос строки";}i:3;a:2:{s:2:"id";s:4:"list";s:5:"value";s:14:"списком";}}}', 0, 94, 1, 'br', 0, 1, 1),
(39, NULL, 0, 16, 0, 0, 'show_date', 'Показывать дату', 5, '', 0, 95, 1, NULL, 0, 1, 1),
(40, NULL, 0, 17, 0, 0, 'group', 'Группировка', 4, 'a:2:{s:6:"source";s:6:"manual";s:6:"values";a:4:{i:1;a:2:{s:2:"id";s:4:"none";s:5:"value";s:6:"нет";}i:2;a:2:{s:2:"id";s:4:"year";s:5:"value";s:15:"по годам";}i:3;a:2:{s:2:"id";s:5:"month";s:5:"value";s:19:"по месяцам";}i:4;a:2:{s:2:"id";s:3:"day";s:5:"value";s:13:"по дням";}}}', 0, 91, 1, 'none', 0, 1, 1),
(41, NULL, 0, 17, 0, 0, 'date_place', 'Выводить дату', 4, 'a:2:{s:6:"source";s:6:"manual";s:6:"values";a:2:{i:1;a:2:{s:2:"id";s:11:"after_title";s:5:"value";s:27:"Под заголовком";}i:2;a:2:{s:2:"id";s:10:"after_text";s:5:"value";s:21:"Под текстом";}}}', 0, 92, 1, 'after_title', 0, 1, 1),
(42, NULL, 0, 17, 0, 0, 'show_announce', 'Выводить анонс в полном выводе поста', 5, '', 0, 93, 1, NULL, 0, 1, 1),
(43, NULL, 9, 0, 0, 0, 'name', 'ФИО', 1, '', 1, 73, 1, NULL, 0, 1, 1),
(44, NULL, 9, 0, 0, 0, 'company', 'Компания', 1, '', 0, 74, 1, NULL, 0, 1, 1),
(45, NULL, 9, 0, 0, 0, 'dept', 'Отдел / подразделение', 1, '', 0, 75, 1, NULL, 0, 1, 1),
(46, NULL, 9, 0, 0, 0, 'job', 'Должность', 1, '', 0, 76, 1, NULL, 0, 1, 1),
(47, NULL, 9, 0, 0, 0, 'photo', 'Фотография', 11, '', 0, 77, 0, '', 0, 1, 1),
(48, NULL, 9, 0, 0, 0, 'phone', 'Телефон', 1, '', 0, 78, 1, NULL, 0, 1, 1),
(49, NULL, 9, 0, 0, 0, 'email', 'Email', 1, '', 0, 79, 1, NULL, 0, 1, 1),
(50, NULL, 9, 0, 0, 0, 'icq', 'ICQ', 1, '', 0, 80, 1, NULL, 0, 1, 1),
(51, NULL, 9, 0, 0, 0, 'skype', 'Skype', 1, '', 0, 81, 1, NULL, 0, 1, 1),
(52, NULL, 9, 0, 0, 0, 'site', 'Сайт', 1, '', 0, 82, 1, NULL, 0, 1, 1),
(53, NULL, 9, 0, 0, 0, 'shorttext', 'Краткое описание', 3, 'a:2:{s:4:"html";s:1:"1";s:7:"wysiwyg";s:1:"1";}', 0, 83, 1, NULL, 0, 1, 1),
(54, NULL, 9, 0, 0, 0, 'text', 'Полное описание', 3, 'a:2:{s:4:"html";s:1:"1";s:7:"wysiwyg";s:1:"1";}', 0, 84, 1, NULL, 0, 1, 1),
(55, NULL, 9, 0, 0, 0, 'birthday', 'Дата рождения', 8, '', 0, 85, 1, NULL, 0, 1, 1),
(56, NULL, 10, 0, 0, 0, 'pic', 'Изображение', 11, '', 1, 0, 1, NULL, 0, 1, 1),
(57, NULL, 10, 0, 0, 0, 'caption', 'Заголовок', 1, '', 1, 0, 1, NULL, 0, 1, 1),
(58, NULL, 0, 20, 0, 0, 'width', 'Ширина', 2, '', 0, 101, 1, '150', 0, 1, 1),
(59, NULL, 0, 20, 0, 0, 'height', 'Высота', 2, '', 0, 102, 1, '150', 0, 1, 1),
(60, NULL, 0, 20, 0, 0, 'pause', 'Пауза ( в секундах)', 2, '', 0, 103, 1, '3', 0, 1, 1),
(61, NULL, 0, 20, 0, 0, 'control_nav', 'Показывать переключатель изображений', 5, '', 0, 104, 1, NULL, 0, 1, 1),
(62, NULL, 0, 21, 0, 0, 'width', 'Ширина', 2, '', 0, 105, 1, '150', 0, 1, 1),
(63, NULL, 0, 21, 0, 0, 'height', 'Высота', 2, '', 0, 106, 1, '150', 0, 1, 1),
(64, NULL, 0, 22, 0, 0, 'width', 'Ширина', 2, '', 0, 86, 1, '150', 0, 1, 1),
(65, NULL, 0, 22, 0, 0, 'height', 'Высота', 2, '', 0, 87, 1, '150', 0, 1, 1),
(66, NULL, 0, 22, 0, 0, 'label', 'Выводить подпись', 4, 'a:2:{s:6:"source";s:6:"manual";s:6:"values";a:3:{i:1;a:2:{s:2:"id";s:2:"up";s:5:"value";s:12:"сверху";}i:2;a:2:{s:2:"id";s:4:"down";s:5:"value";s:10:"снизу";}i:3;a:2:{s:2:"id";s:4:"none";s:5:"value";s:21:"не выводить";}}}', 0, 96, 1, 'down', 0, 1, 1),
(67, NULL, 0, 22, 0, 0, 'open', 'При нажатии открыть картинку', 4, 'a:2:{s:6:"source";s:6:"manual";s:6:"values";a:3:{i:1;a:2:{s:2:"id";s:5:"layer";s:5:"value";s:36:"Во всплывающем слое";}i:2;a:2:{s:2:"id";s:4:"full";s:5:"value";s:40:"На отдельной странице";}i:3;a:2:{s:2:"id";s:4:"none";s:5:"value";s:23:"Не открывать";}}}', 0, 97, 1, 'layer', 0, 1, 1),
(68, NULL, 0, 22, 0, 0, 'border', 'Показывать границу', 5, '', 0, 98, 1, NULL, 0, 1, 1),
(69, NULL, 0, 22, 0, 0, 'border_thickness', 'Толшина границы', 2, '', 0, 99, 1, '1', 0, 1, 1),
(70, NULL, 0, 22, 0, 0, 'border_color', 'Цвет границы', 9, '', 0, 100, 1, NULL, 0, 1, 1),
(71, NULL, 11, 0, 0, 0, 'message', 'Сообщение', 3, '', 1, 0, 1, NULL, 0, 1, 1),
(72, NULL, 11, 0, 0, 0, 'status', 'Статус сообщения', 2, '', 1, 0, 1, NULL, 0, 1, 1),
(73, NULL, 11, 0, 0, 0, 'to_user', 'Кому', 2, '', 1, 0, 1, NULL, 0, 1, 1),
(74, NULL, 12, 0, 0, 0, 'name', 'Наименование', 1, '', 1, 28, 1, NULL, 0, 1, 1),
(159, NULL, 12, 0, 0, 0, 'description', 'Описание', 3, 'a:1:{s:4:"html";s:1:"1";}', 0, 144, 0, '', 0, 1, 1),
(76, NULL, 12, 0, 0, 0, 'price', 'Цена', 7, '', 0, 30, 1, NULL, 0, 1, 1),
(77, NULL, 13, 0, 0, 0, 'quote', 'Цитата', 3, 'a:2:{s:4:"html";s:1:"1";s:7:"wysiwyg";s:1:"1";}', 1, 66, 1, NULL, 0, 1, 1),
(78, NULL, 13, 0, 0, 0, 'author', 'Автор', 1, '', 0, 67, 1, NULL, 0, 1, 1),
(79, NULL, 13, 0, 0, 0, 'link', 'Ссылка', 1, '', 0, 68, 1, NULL, 0, 1, 1),
(80, NULL, 14, 0, 0, 0, 'name', 'ФИО', 1, '', 1, 44, 1, NULL, 0, 1, 1),
(81, NULL, 14, 0, 0, 0, 'birthday', 'Дата рождения', 8, '', 0, 45, 1, NULL, 0, 1, 1),
(82, NULL, 14, 0, 0, 0, 'city', 'Город', 1, '', 0, 47, 1, NULL, 0, 1, 1),
(83, NULL, 14, 0, 0, 0, 'district', 'Район', 1, '', 0, 48, 1, NULL, 0, 1, 1),
(84, NULL, 14, 0, 0, 0, 'job', 'Позиция / должность', 1, '', 1, 49, 1, NULL, 0, 1, 1),
(85, NULL, 14, 0, 0, 0, 'salary_from', 'Зарплата от', 1, '', 0, 50, 1, NULL, 0, 1, 1),
(86, NULL, 14, 0, 0, 0, 'text', 'Кратко о себе', 3, 'a:2:{s:4:"html";s:1:"1";s:7:"wysiwyg";s:1:"1";}', 0, 52, 1, NULL, 0, 1, 1),
(87, NULL, 14, 0, 0, 0, 'experience', 'Профессиональный опыт', 10, 'a:1:{s:15:"components_type";s:3:"all";}', 0, 116, 1, NULL, 0, 1, 1),
(88, NULL, 14, 0, 0, 0, 'education', 'Образование', 10, 'a:1:{s:15:"components_type";s:3:"all";}', 0, 122, 1, NULL, 0, 1, 1),
(89, NULL, 14, 0, 0, 0, 'links', 'Ссылки на проекты', 10, 'a:1:{s:15:"components_type";s:3:"all";}', 0, 123, 1, NULL, 0, 1, 1),
(90, NULL, 14, 0, 0, 0, 'contact', 'Контакты', 10, 'a:1:{s:15:"components_type";s:3:"all";}', 0, 124, 1, NULL, 0, 1, 1),
(91, NULL, 0, 26, 0, 0, 'date_view', 'Дата рождения', 4, 'a:2:{s:6:"source";s:6:"manual";s:6:"values";a:3:{i:1;a:2:{s:2:"id";s:4:"date";s:5:"value";s:25:"выводить дату";}i:2;a:2:{s:2:"id";s:3:"age";s:5:"value";s:31:"выводить возраст";}i:3;a:2:{s:2:"id";s:8:"date_age";s:5:"value";s:43:"выводить дату и возраст";}}}', 0, 107, 1, 'date', 0, 1, 1),
(92, NULL, 15, 0, 0, 0, 'phone', 'Телефон', 1, '', 0, 55, 1, NULL, 0, 1, 1),
(93, NULL, 15, 0, 0, 0, 'email', 'email', 1, '', 0, 56, 1, NULL, 0, 1, 1),
(94, NULL, 15, 0, 0, 0, 'site', 'Сайт / блог', 1, '', 0, 57, 1, NULL, 0, 1, 1),
(95, NULL, 15, 0, 0, 0, 'jabber', 'jabber', 1, '', 0, 58, 1, NULL, 0, 1, 1),
(96, NULL, 15, 0, 0, 0, 'icq', 'ICQ', 1, '', 0, 59, 1, NULL, 0, 1, 1),
(97, NULL, 15, 0, 0, 0, 'twitter', 'Twitter', 1, '', 0, 60, 1, NULL, 0, 1, 1),
(98, NULL, 15, 0, 0, 0, 'fb', 'Facebook', 1, '', 0, 61, 1, NULL, 0, 1, 1),
(99, NULL, 15, 0, 0, 0, 'vk', 'Вконтакте', 1, '', 0, 62, 1, NULL, 0, 1, 1),
(100, NULL, 15, 0, 0, 0, 'address', 'Адрес', 1, '', 0, 63, 1, NULL, 0, 1, 1),
(101, NULL, 15, 0, 0, 0, 'postal', 'Почтовый адрес', 1, '', 0, 64, 1, NULL, 0, 1, 1),
(102, NULL, 15, 0, 0, 0, 'extra', 'Дополнительно', 3, 'a:2:{s:4:"html";s:1:"1";s:7:"wysiwyg";s:1:"1";}', 0, 65, 1, NULL, 0, 1, 1),
(103, NULL, 16, 0, 0, 0, 'name', 'Название учреждения', 1, '', 0, 117, 1, NULL, 0, 1, 1),
(104, NULL, 16, 0, 0, 0, 'department', 'Кафедра, факультет', 1, '', 0, 118, 1, NULL, 0, 1, 1),
(105, NULL, 16, 0, 0, 0, 'degree', 'Ученая степень', 1, '', 0, 119, 1, NULL, 0, 1, 1),
(106, NULL, 16, 0, 0, 0, 'year_start', 'Год начала', 2, '', 0, 120, 1, NULL, 0, 1, 1),
(107, NULL, 16, 0, 0, 0, 'year_end', 'Год окончания', 2, '', 0, 121, 1, NULL, 0, 1, 1),
(108, NULL, 17, 0, 0, 0, 'company', 'Название компании', 1, '', 0, 108, 1, NULL, 0, 1, 1),
(109, NULL, 17, 0, 0, 0, 'url', 'URL', 1, '', 0, 109, 1, NULL, 0, 1, 1),
(110, NULL, 17, 0, 0, 0, 'job', 'Должность', 1, '', 0, 111, 1, NULL, 0, 1, 1),
(111, NULL, 17, 0, 0, 0, 'responsibilities', 'Должностные обязанности', 3, '', 0, 112, 1, NULL, 0, 1, 1),
(112, NULL, 17, 0, 0, 0, 'date_start', 'Месяц и год начала', 8, '', 0, 113, 1, NULL, 0, 1, 1),
(113, NULL, 17, 0, 0, 0, 'date_end', 'Месяц и год окончания', 8, '', 0, 114, 1, NULL, 0, 1, 1),
(114, NULL, 17, 0, 0, 0, 'advice_contact', 'Контакт (ФИО, телефон) для рекомендации', 1, '', 0, 115, 1, NULL, 0, 1, 1),
(115, NULL, 18, 0, 0, 0, 'title', 'Название проекта', 1, '', 1, 20, 1, NULL, 0, 1, 1),
(116, NULL, 18, 0, 0, 0, 'url', 'Ссылка на проект', 1, '', 0, 21, 1, NULL, 0, 1, 1),
(117, NULL, 18, 0, 0, 0, 'description', 'Описание проекта', 3, 'a:2:{s:4:"html";s:1:"1";s:7:"wysiwyg";s:1:"1";}', 0, 22, 1, NULL, 0, 1, 1),
(118, NULL, 19, 0, 0, 0, 'text', 'Текст', 3, 'a:2:{s:4:"html";s:1:"1";s:7:"wysiwyg";s:1:"1";}', 0, 0, 1, NULL, 0, 1, 1),
(119, NULL, 20, 0, 0, 0, 'job', 'Должность', 1, '', 1, 34, 1, NULL, 0, 1, 1),
(120, NULL, 20, 0, 0, 0, 'requirement', 'Требования', 3, 'a:2:{s:4:"html";s:1:"1";s:7:"wysiwyg";s:1:"1";}', 1, 37, 1, NULL, 0, 1, 1),
(121, NULL, 20, 0, 0, 0, 'respons', 'Обязанности', 3, 'a:2:{s:4:"html";s:1:"1";s:7:"wysiwyg";s:1:"1";}', 0, 38, 1, NULL, 0, 1, 1),
(122, NULL, 20, 0, 0, 0, 'term', 'Условия', 3, 'a:2:{s:4:"html";s:1:"1";s:7:"wysiwyg";s:1:"1";}', 1, 39, 1, NULL, 0, 1, 1),
(123, NULL, 20, 0, 0, 0, 'address', 'Адрес', 1, '', 0, 40, 1, NULL, 0, 1, 1),
(124, NULL, 20, 0, 0, 0, 'phone', 'Телефон', 1, '', 0, 41, 1, NULL, 0, 1, 1),
(125, NULL, 20, 0, 0, 0, 'email', 'email', 1, '', 0, 42, 1, NULL, 0, 1, 1),
(126, NULL, 20, 0, 0, 0, 'contacts', 'Контактное лицо', 1, '', 0, 43, 1, NULL, 0, 1, 1),
(127, NULL, 0, 0, 0, 1, 'view', 'Вид', 4, 'a:2:{s:6:"source";s:13:"classificator";s:5:"table";s:7:"Country";}', 0, 0, 0, '', 0, 1, 1),
(151, NULL, 0, 0, 0, 6, 'sape_id', 'ID SAPE', 1, '', 0, 140, 0, '', 0, 1, 1),
(152, NULL, 1, 0, 0, 0, 'first_name', 'Фамилия', 1, '', 0, 141, 0, '', 0, 1, 1),
(128, NULL, 0, 0, 0, 1, 'show_reg_link', 'Выводить ссылку на регистрацию', 5, '', 0, 0, 1, '1', 0, 1, 1),
(129, NULL, 0, 0, 0, 1, 'show_recovery_link', 'Выводить ссылку на восстановление пароля', 5, '', 0, 0, 1, '1', 0, 1, 1),
(130, NULL, 0, 0, 0, 1, 'loginsave', '"Запомнить меня"', 4, 'a:2:{s:6:"source";s:6:"manual";s:6:"values";a:4:{i:1;a:2:{s:2:"id";s:4:"none";s:5:"value";s:52:"не показывать, не запоминать";}i:2;a:2:{s:2:"id";s:8:"show_off";s:5:"value";s:35:"показывать чекбокс";}i:3;a:2:{s:2:"id";s:7:"show_on";s:5:"value";s:54:"показывать выбранный чекбокс";}i:4;a:2:{s:2:"id";s:6:"always";s:5:"value";s:52:"не показывать, но запоминать";}}}', 0, 0, 1, 'always', 0, 1, 1),
(131, NULL, 0, 0, 0, 1, 'show_pm', 'Выводить информер о личных сообщениях', 5, '', 0, 0, 1, '1', 0, 1, 1),
(132, NULL, 0, 0, 0, 3, 'city', 'Город', 1, '', 0, 0, 1, 'moscow', 0, 1, 1),
(133, NULL, 0, 0, 0, 3, 'width', 'Ширина', 2, '', 0, 1, 1, '175', 0, 1, 1),
(134, NULL, 0, 0, 0, 4, 'cid', 'Город', 1, '', 0, 0, 1, '533,529', 0, 1, 1),
(135, NULL, 0, 0, 0, 4, 'width', 'Ширина', 2, '', 0, 1, 1, '150', 0, 1, 1),
(136, NULL, 0, 0, 0, 5, 'width', 'Ширина', 2, '', 0, 0, 1, '640', 0, 1, 1),
(137, NULL, 0, 0, 0, 5, 'height', 'Высота', 2, '', 0, 1, 1, '390', 0, 1, 1),
(138, NULL, 0, 0, 0, 5, 'url', 'URL или КОД', 1, '', 0, 2, 1, 'tGCP2MgU-bQ', 0, 1, 1),
(165, NULL, 23, 0, 0, 0, 'url', 'Адрес', 1, '', 0, 150, 0, '', 0, 1, 1),
(164, NULL, 23, 0, 0, 0, 'layout_id', 'Индивидуальный шаблон', 13, '', 0, 149, 0, '', 0, 1, 1),
(163, NULL, 24, 0, 0, 0, 'name', 'Название раздела', 1, '', 1, 148, 0, '', 0, 1, 1),
(153, NULL, 1, 0, 0, 0, 'email', 'E-mail', 1, '', 0, 142, 1, NULL, 0, 1, 1),
(154, NULL, 22, 0, 0, 0, 'name', 'Имя', 1, '', 0, 0, 1, NULL, 0, 1, 1),
(155, NULL, 22, 0, 0, 0, 'question', 'Вопрос', 3, '', 1, 1, 1, NULL, 0, 1, 1),
(156, NULL, 22, 0, 0, 0, 'answer', 'Ответ', 3, 'a:1:{s:4:"html";s:1:"1";}', 0, 2, 1, NULL, 0, 2, 1),
(157, NULL, 0, 36, 0, 0, 'with_answers', 'Показывать вопросы только с ответами', 5, '', 0, 125, 1, NULL, 0, 1, 1),
(158, NULL, 12, 0, 0, 0, 'image', 'Картинка', 11, '', 0, 143, 0, '', 0, 1, 1),
(160, NULL, 20, 0, 0, 0, 'pay_from', 'Оклад от', 1, '', 0, 145, 0, '', 0, 1, 1),
(161, NULL, 20, 0, 0, 0, 'pay_to', 'Оклад до', 1, '', 0, 146, 0, '', 0, 1, 1),
(162, NULL, 12, 0, 0, 0, 'review_id', 'Обзор', 13, '', 0, 147, 0, '', 0, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_filetable`
--

CREATE TABLE IF NOT EXISTS `fx_filetable` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `real_name` char(128) NOT NULL,
  `path` text NOT NULL,
  `type` char(64) DEFAULT NULL,
  `size` int(10) unsigned NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- Дамп данных таблицы `fx_filetable`
--

INSERT INTO `fx_filetable` (`id`, `real_name`, `path`, `type`, `size`, `to_delete`) VALUES
(1, 'r1.png', 'content/4/r1_0.png', 'image/png', 69105, 1),
(2, 'r2.jpg', 'content/4/r2_0.jpg', 'image/jpeg', 28020, 0),
(3, 'r3.jpg', 'content/4/r3_0.jpg', 'image/jpeg', 28680, 0),
(4, 'g1.jpg', 'content/4/g1_0.jpg', 'image/jpeg', 16744, 0),
(5, 'g2.jpg', 'content/4/g2_0.jpg', 'image/jpeg', 63233, 0),
(6, 'k1.jpg', 'content/4/k1_0.jpg', 'image/jpeg', 15831, 0),
(7, 'k3.jpg', 'content/4/k3_0.jpg', 'image/jpeg', 43625, 0),
(8, 'anchor.gif', 'content/21/anchor_0.gif', 'image/gif', 65, 1),
(9, 'anchor.gif', 'content/21/anchor_1.gif', 'image/gif', 65, 1),
(10, '', 'content/', '', 0, 0),
(11, 'r1.png', 'content/r1_0.png', 'image/png', 69105, 0),
(12, 'r1.png', 'content/r1_1.png', 'image/png', 69105, 0),
(13, 'r2.jpg', 'content/r2_0.jpg', 'image/jpeg', 28020, 0),
(14, '', 'content/', '', 0, 0),
(15, '', 'content/', '', 0, 0),
(16, 'r1.png', 'content/r1_2.png', 'image/png', 69105, 0),
(17, 'r2.jpg', 'content/r2_1.jpg', 'image/jpeg', 28020, 0),
(18, 'g2.jpg', 'content/g2_0.jpg', 'image/jpeg', 63233, 0),
(19, 'r1.png', 'content/r1_3.png', 'image/png', 69105, 0),
(20, 'r3.jpg', 'content/r3_0.jpg', 'image/jpeg', 28680, 0),
(21, 'Chrysanthemum.jpg', 'content/10/Chrysanthemum_0.jpg', 'image/jpeg', 194145, 0),
(22, 'Desert.jpg', 'content/10/Desert_0.jpg', 'image/jpeg', 198540, 0),
(23, 'Jellyfish.jpg', 'content/10/Jellyfish_0.jpg', 'image/jpeg', 775702, 0),
(24, 'accept.png', 'content/accept_0.png', 'image/png', 781, 0),
(25, 'chain.gif', 'content/21/chain_0.gif', 'image/gif', 998, 0),
(26, 'ok.jpg', 'content/6/ok_0.jpg', 'image/jpeg', 16262, 0),
(27, 'm_c5d93f83.jpg', 'content/10/m_c5d93f83_0.jpg', 'image/jpeg', 4756, 0),
(28, 'm_18a08dc2.jpg', 'content/10/m_18a08dc2_0.jpg', 'image/jpeg', 3788, 0),
(29, 'ava.gif', 'content/ava_0.gif', 'image/gif', 5258, 0),
(30, 'Chrysanthemum.jpg', 'content/10/Chrysanthemum_1.jpg', 'image/jpeg', 194145, 0),
(31, 'Desert.jpg', 'content/10/Desert_1.jpg', 'image/jpeg', 198540, 0),
(32, 'Jellyfish.jpg', 'content/10/Jellyfish_1.jpg', 'image/jpeg', 775702, 0),
(33, 'i4.jpg', 'content/12/i4_0.jpg', 'image/jpeg', 3280, 0),
(34, 'i3.jpg', 'content/12/i3_0.jpg', 'image/jpeg', 3405, 0),
(35, 'i2.jpg', 'content/12/i2_0.jpg', 'image/jpeg', 2772, 0),
(36, 'i.jpg', 'content/12/i_0.jpg', 'image/jpeg', 3714, 0),
(37, 'i.jpg', 'content/12/i_1.jpg', 'image/jpeg', 2200, 0),
(38, '2.jpg', 'content/12/2_0.jpg', 'image/jpeg', 1986, 0),
(39, '3.jpg', 'content/12/3_0.jpg', 'image/jpeg', 5390, 0),
(40, '7-marian-nastase-customer-service-manager_8gei.jpg', 'content/9/7-marian-nastase-customer-service-manager_8gei_0.jpg', 'image/jpeg', 304075, 0),
(41, 'deaddcb109c93553ba368bb0394f54da.jpg', 'content/9/deaddcb109c93553ba368bb0394f54da_0.jpg', 'image/jpeg', 28160, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_group`
--

CREATE TABLE IF NOT EXISTS `fx_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `fx_group`
--

INSERT INTO `fx_group` (`id`, `name`) VALUES
(1, 'Администраторы'),
(2, 'Внешние пользователи'),
(3, 'Авторизированные через внешние сервисы');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_history`
--

CREATE TABLE IF NOT EXISTS `fx_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `name` text NOT NULL,
  `marker` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='История операций' AUTO_INCREMENT=37 ;

--
-- Дамп данных таблицы `fx_history`
--

INSERT INTO `fx_history` (`id`, `user_id`, `date`, `name`, `marker`) VALUES
(1, 1, '2012-12-19 13:17:53', 'FX_HISTORY_ADMIN_COMPONENT_DELETE', 0),
(2, 1, '2012-12-19 13:18:19', 'FX_HISTORY_ADMIN_SITE_DELETE', 0),
(3, 1, '2012-12-19 13:18:25', 'FX_HISTORY_ADMIN_SITE_DELETE', 0),
(4, 1, '2012-12-19 13:18:33', 'FX_HISTORY_ADMIN_SITE_DELETE', 0),
(5, 1, '2012-12-19 13:21:30', 'FX_HISTORY_ADMIN_SUBDIVISION_DELETE', 0),
(6, 1, '2012-12-19 13:21:47', 'FX_HISTORY_ADMIN_SUBDIVISION_DELETE', 0),
(7, 1, '2012-12-19 13:21:55', 'FX_HISTORY_ADMIN_SUBDIVISION_DELETE', 0),
(8, 1, '2012-12-19 14:02:22', 'FX_HISTORY_ADMIN_COMPONENT_DELETE', 0),
(9, 1, '2012-12-19 14:08:33', 'FX_HISTORY_ADMIN_COMPONENT_DELETE', 0),
(10, 1, '2012-12-19 14:11:32', 'FX_HISTORY_ADMIN_COMPONENT_DELETE', 0),
(11, 1, '2012-12-19 14:14:04', 'FX_HISTORY_ADMIN_COMPONENT_DELETE', 0),
(12, 1, '2012-12-19 14:47:01', 'FX_HISTORY_ADMIN_FIELD_ADD', 0),
(13, 1, '2012-12-19 14:47:22', 'FX_HISTORY_ADMIN_FIELD_ADD', 0),
(14, 1, '2012-12-19 17:02:42', 'FX_HISTORY_ADMIN_TEMPLATE_ADD', 0),
(15, 1, '2012-12-19 17:04:39', 'FX_HISTORY_ADMIN_TEMPLATE_ADD', 0),
(16, 1, '2012-12-19 17:05:02', 'FX_HISTORY_ADMIN_SITE_DESIGN', 0),
(17, 1, '2012-12-19 17:07:29', 'FX_HISTORY_ADMIN_SITE_DESIGN', 0),
(18, 1, '2012-12-19 17:08:09', 'FX_HISTORY_ADMIN_SITE_DESIGN', 0),
(19, 1, '2012-12-19 17:09:15', 'FX_HISTORY_ADMIN_SITE_DESIGN', 0),
(20, 1, '2012-12-19 17:11:09', 'FX_HISTORY_ADMIN_SITE_DESIGN', 0),
(21, 1, '2012-12-19 17:12:34', 'FX_HISTORY_ADMIN_SITE_DESIGN', 0),
(22, 1, '2012-12-19 17:12:57', 'FX_HISTORY_ADMIN_SITE_DESIGN', 0),
(23, 1, '2012-12-19 17:13:37', 'FX_HISTORY_ADMIN_SITE_DESIGN', 0),
(24, 1, '2012-12-19 17:15:08', 'FX_HISTORY_ADMIN_SITE_DESIGN', 0),
(25, 1, '2012-12-19 17:15:59', 'FX_HISTORY_ADMIN_SITE_DESIGN', 0),
(26, 1, '2012-12-19 17:25:32', 'FX_HISTORY_ADMIN_TEMPLATE_MOVE', 0),
(27, 1, '2012-12-19 17:26:17', 'FX_HISTORY_ADMIN_LAYOUT_EDIT', 0),
(28, 1, '2013-01-04 23:05:04', 'FX_HISTORY_ADMIN_FIELD_ADD', 0),
(29, 1, '2013-01-04 23:06:34', 'FX_HISTORY_ADMIN_FIELD_EDIT', 0),
(30, 1, '2013-01-04 23:06:39', 'FX_HISTORY_ADMIN_FIELD_EDIT', 0),
(31, 1, '2013-01-05 01:13:05', 'FX_HISTORY_ADMIN_FIELD_EDIT', 0),
(32, 1, '2013-02-07 12:42:04', 'FX_HISTORY_ADMIN_FIELD_ADD', 0),
(33, 1, '2013-02-25 13:40:25', 'FX_HISTORY_ADMIN_FIELD_EDIT', 0),
(34, 1, '2013-02-25 13:41:15', 'FX_HISTORY_ADMIN_COMPONENT_EDIT', 0),
(35, 1, '2013-02-25 13:45:43', 'FX_HISTORY_ADMIN_COMPONENT_EDIT', 0),
(36, 1, '2013-02-25 13:46:37', 'FX_HISTORY_ADMIN_COMPONENT_EDIT', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_history_item`
--

CREATE TABLE IF NOT EXISTS `fx_history_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `history_id` int(11) NOT NULL,
  `essence` varchar(255) NOT NULL,
  `essence_id` text NOT NULL,
  `action` varchar(255) NOT NULL,
  `prestate` longtext NOT NULL,
  `poststate` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

--
-- Дамп данных таблицы `fx_history_item`
--

INSERT INTO `fx_history_item` (`id`, `history_id`, `essence`, `essence_id`, `action`, `prestate`, `poststate`) VALUES
(1, 2, 'site', '2', 'delete', 'a:19:{s:2:"id";s:1:"2";s:9:"parent_id";s:1:"0";s:4:"name";s:27:"Сайт фотографа";s:6:"domain";s:16:"floxim-photo.loc";s:11:"template_id";s:1:"1";s:5:"color";s:1:"2";s:7:"mirrors";s:0:"";s:8:"priority";s:1:"1";s:7:"checked";s:1:"1";s:12:"title_sub_id";s:2:"25";s:11:"e404_sub_id";s:2:"26";s:7:"created";s:19:"2012-06-13 12:57:58";s:12:"last_updated";s:19:"2012-08-29 14:42:37";s:6:"robots";s:0:"";s:17:"disallow_indexing";s:1:"0";s:4:"type";s:6:"useful";s:8:"language";s:2:"ru";s:12:"offline_text";s:0:"";s:8:"store_id";s:10:"site.photo";}', 'a:0:{}'),
(2, 3, 'site', '5', 'delete', 'a:19:{s:2:"id";s:1:"5";s:9:"parent_id";s:1:"0";s:4:"name";s:10:"PlayGround";s:6:"domain";s:13:"playground.fx";s:11:"template_id";s:2:"13";s:5:"color";s:1:"0";s:7:"mirrors";s:0:"";s:8:"priority";s:1:"2";s:7:"checked";s:1:"1";s:12:"title_sub_id";s:2:"39";s:11:"e404_sub_id";s:2:"40";s:7:"created";s:19:"2012-07-03 14:04:51";s:12:"last_updated";s:19:"2012-08-29 14:42:37";s:6:"robots";N;s:17:"disallow_indexing";s:1:"0";s:4:"type";s:6:"useful";s:8:"language";s:2:"en";s:12:"offline_text";N;s:8:"store_id";N;}', 'a:0:{}'),
(3, 4, 'site', '7', 'delete', 'a:19:{s:2:"id";s:1:"7";s:9:"parent_id";s:1:"0";s:4:"name";s:27:"Сайт фотографа";s:6:"domain";s:0:"";s:11:"template_id";s:2:"10";s:5:"color";s:1:"0";s:7:"mirrors";s:0:"";s:8:"priority";s:1:"3";s:7:"checked";s:1:"1";s:12:"title_sub_id";s:2:"63";s:11:"e404_sub_id";s:2:"64";s:7:"created";s:19:"2012-08-24 14:18:56";s:12:"last_updated";s:19:"2012-08-24 14:18:56";s:6:"robots";N;s:17:"disallow_indexing";s:1:"0";s:4:"type";s:6:"useful";s:8:"language";s:2:"en";s:12:"offline_text";N;s:8:"store_id";s:10:"site.photo";}', 'a:0:{}'),
(4, 10, 'ctpl', '35', 'delete', 'a:15:{s:2:"id";s:2:"35";s:9:"parent_id";s:1:"0";s:12:"component_id";s:2:"21";s:7:"keyword";s:4:"main";s:4:"name";s:4:"edin";s:7:"rec_num";s:2:"20";s:4:"sort";s:0:"";s:6:"action";s:0:"";s:9:"with_list";s:1:"1";s:9:"with_full";s:1:"1";s:4:"type";s:6:"useful";s:6:"widget";s:1:"1";s:9:"notwidget";s:1:"1";s:5:"embed";s:11:"narrow-wide";s:6:"access";s:0:"";}', 'a:0:{}'),
(5, 10, 'field', '139', 'delete', 'a:17:{s:2:"id";s:3:"139";s:6:"parent";N;s:12:"component_id";s:2:"21";s:7:"ctpl_id";s:1:"0";s:15:"system_table_id";s:1:"0";s:9:"widget_id";s:1:"0";s:4:"name";s:11:"fieldstring";s:11:"description";s:27:"Строковое поля";s:4:"type";s:1:"1";s:6:"format";s:0:"";s:8:"not_null";s:1:"0";s:8:"priority";s:3:"128";s:10:"searchable";s:1:"1";s:7:"default";N;s:11:"inheritance";s:1:"0";s:12:"type_of_edit";s:1:"1";s:7:"checked";s:1:"1";}', 'a:0:{}'),
(6, 10, 'field', '140', 'delete', 'a:17:{s:2:"id";s:3:"140";s:6:"parent";N;s:12:"component_id";s:2:"21";s:7:"ctpl_id";s:1:"0";s:15:"system_table_id";s:1:"0";s:9:"widget_id";s:1:"0";s:4:"name";s:8:"fieldint";s:11:"description";s:21:"Целое число";s:4:"type";s:1:"2";s:6:"format";s:0:"";s:8:"not_null";s:1:"0";s:8:"priority";s:3:"129";s:10:"searchable";s:1:"1";s:7:"default";N;s:11:"inheritance";s:1:"0";s:12:"type_of_edit";s:1:"1";s:7:"checked";s:1:"1";}', 'a:0:{}'),
(7, 10, 'field', '141', 'delete', 'a:17:{s:2:"id";s:3:"141";s:6:"parent";N;s:12:"component_id";s:2:"21";s:7:"ctpl_id";s:1:"0";s:15:"system_table_id";s:1:"0";s:9:"widget_id";s:1:"0";s:4:"name";s:9:"fieldtext";s:11:"description";s:10:"Текст";s:4:"type";s:1:"3";s:6:"format";s:0:"";s:8:"not_null";s:1:"0";s:8:"priority";s:3:"130";s:10:"searchable";s:1:"1";s:7:"default";N;s:11:"inheritance";s:1:"0";s:12:"type_of_edit";s:1:"1";s:7:"checked";s:1:"1";}', 'a:0:{}'),
(8, 10, 'field', '142', 'delete', 'a:17:{s:2:"id";s:3:"142";s:6:"parent";N;s:12:"component_id";s:2:"21";s:7:"ctpl_id";s:1:"0";s:15:"system_table_id";s:1:"0";s:9:"widget_id";s:1:"0";s:4:"name";s:7:"fieldwy";s:11:"description";s:34:"Текст с редактором";s:4:"type";s:1:"3";s:6:"format";a:1:{s:4:"html";s:1:"1";}s:8:"not_null";s:1:"0";s:8:"priority";s:3:"131";s:10:"searchable";s:1:"1";s:7:"default";N;s:11:"inheritance";s:1:"0";s:12:"type_of_edit";s:1:"1";s:7:"checked";s:1:"1";}', 'a:0:{}'),
(9, 10, 'field', '143', 'delete', 'a:17:{s:2:"id";s:3:"143";s:6:"parent";N;s:12:"component_id";s:2:"21";s:7:"ctpl_id";s:1:"0";s:15:"system_table_id";s:1:"0";s:9:"widget_id";s:1:"0";s:4:"name";s:11:"fieldselect";s:11:"description";s:12:"Список";s:4:"type";s:1:"4";s:6:"format";a:2:{s:6:"source";s:13:"classificator";s:5:"table";s:7:"Country";}s:8:"not_null";s:1:"0";s:8:"priority";s:3:"132";s:10:"searchable";s:1:"1";s:7:"default";N;s:11:"inheritance";s:1:"0";s:12:"type_of_edit";s:1:"1";s:7:"checked";s:1:"1";}', 'a:0:{}'),
(10, 10, 'field', '144', 'delete', 'a:17:{s:2:"id";s:3:"144";s:6:"parent";N;s:12:"component_id";s:2:"21";s:7:"ctpl_id";s:1:"0";s:15:"system_table_id";s:1:"0";s:9:"widget_id";s:1:"0";s:4:"name";s:10:"fieldfloat";s:11:"description";s:25:"Дробное число";s:4:"type";s:1:"7";s:6:"format";s:0:"";s:8:"not_null";s:1:"0";s:8:"priority";s:3:"133";s:10:"searchable";s:1:"1";s:7:"default";N;s:11:"inheritance";s:1:"0";s:12:"type_of_edit";s:1:"1";s:7:"checked";s:1:"1";}', 'a:0:{}'),
(11, 10, 'field', '145', 'delete', 'a:17:{s:2:"id";s:3:"145";s:6:"parent";N;s:12:"component_id";s:2:"21";s:7:"ctpl_id";s:1:"0";s:15:"system_table_id";s:1:"0";s:9:"widget_id";s:1:"0";s:4:"name";s:9:"fielddate";s:11:"description";s:22:"дата и время";s:4:"type";s:1:"8";s:6:"format";s:0:"";s:8:"not_null";s:1:"0";s:8:"priority";s:3:"134";s:10:"searchable";s:1:"1";s:7:"default";N;s:11:"inheritance";s:1:"0";s:12:"type_of_edit";s:1:"1";s:7:"checked";s:1:"1";}', 'a:0:{}'),
(12, 10, 'field', '146', 'delete', 'a:17:{s:2:"id";s:3:"146";s:6:"parent";N;s:12:"component_id";s:2:"21";s:7:"ctpl_id";s:1:"0";s:15:"system_table_id";s:1:"0";s:9:"widget_id";s:1:"0";s:4:"name";s:10:"fieldcolor";s:11:"description";s:8:"Цвет";s:4:"type";s:1:"9";s:6:"format";s:0:"";s:8:"not_null";s:1:"0";s:8:"priority";s:3:"135";s:10:"searchable";s:1:"1";s:7:"default";N;s:11:"inheritance";s:1:"0";s:12:"type_of_edit";s:1:"1";s:7:"checked";s:1:"1";}', 'a:0:{}'),
(13, 10, 'field', '147', 'delete', 'a:17:{s:2:"id";s:3:"147";s:6:"parent";N;s:12:"component_id";s:2:"21";s:7:"ctpl_id";s:1:"0";s:15:"system_table_id";s:1:"0";s:9:"widget_id";s:1:"0";s:4:"name";s:8:"fieldmul";s:11:"description";s:37:"Множественный выбор";s:4:"type";s:2:"12";s:6:"format";a:3:{s:6:"source";s:13:"classificator";s:5:"table";s:7:"Country";s:4:"show";s:6:"select";}s:8:"not_null";s:1:"0";s:8:"priority";s:3:"136";s:10:"searchable";s:1:"1";s:7:"default";N;s:11:"inheritance";s:1:"0";s:12:"type_of_edit";s:1:"1";s:7:"checked";s:1:"1";}', 'a:0:{}'),
(14, 10, 'field', '148', 'delete', 'a:17:{s:2:"id";s:3:"148";s:6:"parent";N;s:12:"component_id";s:2:"21";s:7:"ctpl_id";s:1:"0";s:15:"system_table_id";s:1:"0";s:9:"widget_id";s:1:"0";s:4:"name";s:8:"fieldinf";s:11:"description";s:25:"Поле-инфоблок";s:4:"type";s:2:"10";s:6:"format";a:2:{s:15:"components_type";s:6:"select";s:13:"components_id";a:1:{i:0;s:2:"19";}}s:8:"not_null";s:1:"0";s:8:"priority";s:3:"137";s:10:"searchable";s:1:"0";s:7:"default";s:0:"";s:11:"inheritance";s:1:"0";s:12:"type_of_edit";s:1:"1";s:7:"checked";s:1:"1";}', 'a:0:{}'),
(15, 10, 'field', '149', 'delete', 'a:17:{s:2:"id";s:3:"149";s:6:"parent";N;s:12:"component_id";s:2:"21";s:7:"ctpl_id";s:1:"0";s:15:"system_table_id";s:1:"0";s:9:"widget_id";s:1:"0";s:4:"name";s:9:"fieldfile";s:11:"description";s:8:"Файд";s:4:"type";s:1:"6";s:6:"format";s:0:"";s:8:"not_null";s:1:"0";s:8:"priority";s:3:"138";s:10:"searchable";s:1:"0";s:7:"default";s:0:"";s:11:"inheritance";s:1:"0";s:12:"type_of_edit";s:1:"1";s:7:"checked";s:1:"1";}', 'a:0:{}'),
(16, 10, 'field', '150', 'delete', 'a:17:{s:2:"id";s:3:"150";s:6:"parent";N;s:12:"component_id";s:2:"21";s:7:"ctpl_id";s:1:"0";s:15:"system_table_id";s:1:"0";s:9:"widget_id";s:1:"0";s:4:"name";s:8:"fieldpic";s:11:"description";s:16:"Картинко";s:4:"type";s:2:"11";s:6:"format";s:0:"";s:8:"not_null";s:1:"0";s:8:"priority";s:3:"139";s:10:"searchable";s:1:"0";s:7:"default";s:0:"";s:11:"inheritance";s:1:"0";s:12:"type_of_edit";s:1:"1";s:7:"checked";s:1:"1";}', 'a:0:{}'),
(17, 11, 'component', '21', 'delete', 'a:7:{s:2:"id";s:2:"21";s:7:"keyword";s:4:"edin";s:4:"name";s:4:"edin";s:11:"description";N;s:5:"group";s:14:"Базовые";s:4:"icon";s:0:"";s:8:"store_id";N;}', 'a:0:{}'),
(18, 13, 'field', '163', 'add', 'a:3:{s:7:"checked";N;s:12:"component_id";N;s:8:"priority";N;}', 'a:12:{s:4:"name";s:4:"name";s:11:"description";s:16:"Название";s:6:"format";N;s:4:"type";s:1:"1";s:8:"not_null";s:1:"1";s:10:"searchable";N;s:7:"default";s:0:"";s:12:"type_of_edit";s:1:"1";s:7:"checked";i:1;s:12:"component_id";s:2:"24";s:8:"priority";s:3:"148";s:2:"id";s:3:"163";}'),
(19, 15, 'template', '35', 'add', 'a:0:{}', 'a:4:{s:4:"name";s:9:"SuperNova";s:7:"keyword";s:9:"supernova";s:4:"type";s:6:"parent";s:2:"id";s:2:"35";}'),
(20, 15, 'template', '36', 'add', 'a:0:{}', 'a:5:{s:4:"name";s:35:"Титульная страница";s:7:"keyword";s:5:"index";s:4:"type";s:5:"index";s:9:"parent_id";s:2:"35";s:2:"id";s:2:"36";}'),
(21, 15, 'template', '37', 'add', 'a:0:{}', 'a:5:{s:4:"name";s:37:"Внутренняя страница";s:7:"keyword";s:5:"inner";s:4:"type";s:5:"inner";s:9:"parent_id";s:2:"35";s:2:"id";s:2:"37";}'),
(22, 15, 'template', '35', 'update', 'a:1:{s:5:"files";s:0:"";}', 'a:1:{s:5:"files";s:0:"";}'),
(23, 15, 'template', '35', 'update', 'a:1:{s:5:"files";s:0:"";}', 'a:1:{s:5:"files";s:0:"";}'),
(24, 25, 'site', '1', 'update', 'a:1:{s:11:"template_id";s:1:"1";}', 'a:1:{s:11:"template_id";s:2:"35";}'),
(25, 26, 'template', '35', 'update', 'a:1:{s:8:"priority";N;}', 'a:1:{s:8:"priority";i:0;}'),
(26, 26, 'template', '1', 'update', 'a:1:{s:8:"priority";N;}', 'a:1:{s:8:"priority";i:1;}'),
(27, 26, 'template', '4', 'update', 'a:1:{s:8:"priority";N;}', 'a:1:{s:8:"priority";i:2;}'),
(28, 26, 'template', '7', 'update', 'a:1:{s:8:"priority";N;}', 'a:1:{s:8:"priority";i:3;}'),
(29, 26, 'template', '10', 'update', 'a:1:{s:8:"priority";N;}', 'a:1:{s:8:"priority";i:4;}'),
(30, 26, 'template', '13', 'update', 'a:1:{s:8:"priority";N;}', 'a:1:{s:8:"priority";i:5;}'),
(31, 26, 'template', '16', 'update', 'a:1:{s:8:"priority";N;}', 'a:1:{s:8:"priority";i:6;}'),
(32, 26, 'template', '20', 'update', 'a:1:{s:8:"priority";N;}', 'a:1:{s:8:"priority";i:7;}'),
(33, 26, 'template', '23', 'update', 'a:1:{s:8:"priority";N;}', 'a:1:{s:8:"priority";i:8;}'),
(34, 26, 'template', '26', 'update', 'a:1:{s:8:"priority";N;}', 'a:1:{s:8:"priority";i:9;}'),
(35, 26, 'template', '32', 'update', 'a:1:{s:8:"priority";N;}', 'a:1:{s:8:"priority";i:10;}'),
(36, 27, 'template', '35', 'update', 'a:1:{s:5:"files";s:0:"";}', 'a:1:{s:5:"files";s:0:"";}'),
(37, 28, 'field', '164', 'add', 'a:3:{s:7:"checked";N;s:12:"component_id";N;s:8:"priority";N;}', 'a:12:{s:4:"name";s:9:"layout_id";s:11:"description";s:41:"Индивидуальный шаблон";s:6:"format";N;s:4:"type";s:2:"13";s:8:"not_null";N;s:10:"searchable";N;s:7:"default";N;s:12:"type_of_edit";s:1:"1";s:7:"checked";i:1;s:12:"component_id";s:2:"23";s:8:"priority";s:3:"149";s:2:"id";s:3:"164";}'),
(38, 29, 'field', '164', 'update', 'a:8:{s:4:"name";s:9:"layout_id";s:11:"description";s:41:"Индивидуальный шаблон";s:6:"format";s:0:"";s:4:"type";s:2:"13";s:8:"not_null";s:1:"0";s:10:"searchable";s:1:"0";s:7:"default";s:0:"";s:12:"type_of_edit";s:1:"1";}', 'a:8:{s:4:"name";s:11:"template_id";s:11:"description";s:41:"Индивидуальный шаблон";s:6:"format";N;s:4:"type";s:2:"13";s:8:"not_null";N;s:10:"searchable";N;s:7:"default";N;s:12:"type_of_edit";s:1:"1";}'),
(39, 30, 'field', '164', 'update', 'a:8:{s:4:"name";s:11:"template_id";s:11:"description";s:41:"Индивидуальный шаблон";s:6:"format";s:0:"";s:4:"type";s:2:"13";s:8:"not_null";s:1:"0";s:10:"searchable";s:1:"0";s:7:"default";s:0:"";s:12:"type_of_edit";s:1:"1";}', 'a:8:{s:4:"name";s:11:"template_id";s:11:"description";s:41:"Индивидуальный шаблон";s:6:"format";N;s:4:"type";s:2:"13";s:8:"not_null";N;s:10:"searchable";N;s:7:"default";N;s:12:"type_of_edit";s:1:"1";}'),
(40, 31, 'field', '164', 'update', 'a:8:{s:4:"name";s:11:"template_id";s:11:"description";s:41:"Индивидуальный шаблон";s:6:"format";s:0:"";s:4:"type";s:2:"13";s:8:"not_null";s:1:"0";s:10:"searchable";s:1:"0";s:7:"default";s:0:"";s:12:"type_of_edit";s:1:"1";}', 'a:8:{s:4:"name";s:9:"layout_id";s:11:"description";s:41:"Индивидуальный шаблон";s:6:"format";N;s:4:"type";s:2:"13";s:8:"not_null";N;s:10:"searchable";N;s:7:"default";N;s:12:"type_of_edit";s:1:"1";}'),
(41, 32, 'field', '165', 'add', 'a:3:{s:7:"checked";N;s:12:"component_id";N;s:8:"priority";N;}', 'a:12:{s:4:"name";s:3:"url";s:11:"description";s:10:"Адрес";s:6:"format";N;s:4:"type";s:1:"1";s:8:"not_null";N;s:10:"searchable";N;s:7:"default";s:0:"";s:12:"type_of_edit";s:1:"1";s:7:"checked";i:1;s:12:"component_id";s:2:"23";s:8:"priority";s:3:"150";s:2:"id";s:3:"165";}'),
(42, 33, 'field', '163', 'update', 'a:8:{s:4:"name";s:4:"name";s:11:"description";s:16:"Название";s:6:"format";s:0:"";s:4:"type";s:1:"1";s:8:"not_null";s:1:"1";s:10:"searchable";s:1:"0";s:7:"default";s:0:"";s:12:"type_of_edit";s:1:"1";}', 'a:8:{s:4:"name";s:4:"name";s:11:"description";s:31:"Название раздела";s:6:"format";N;s:4:"type";s:1:"1";s:8:"not_null";s:1:"1";s:10:"searchable";N;s:7:"default";s:0:"";s:12:"type_of_edit";s:1:"1";}'),
(43, 35, 'component', '24', 'update', 'a:3:{s:4:"name";s:14:"Разделы";s:5:"group";s:14:"Базовые";s:8:"has_page";s:1:"0";}', 'a:3:{s:4:"name";s:14:"Разделы";s:5:"group";s:14:"Базовые";s:8:"has_page";s:1:"1";}'),
(44, 36, 'component', '24', 'update', 'a:4:{s:4:"name";s:14:"Разделы";s:5:"group";s:14:"Базовые";s:8:"has_page";s:1:"1";s:11:"description";N;}', 'a:4:{s:4:"name";s:14:"Разделы";s:5:"group";s:14:"Базовые";s:8:"has_page";s:1:"1";s:11:"description";s:15:"Для меню";}');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_infoblock`
--

CREATE TABLE IF NOT EXISTS `fx_infoblock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_infoblock_id` int(11) NOT NULL DEFAULT '0',
  `site_id` int(11) NOT NULL,
  `page_id` int(10) unsigned NOT NULL,
  `checked` tinyint(1) NOT NULL DEFAULT '1',
  `priority` int(11) DEFAULT '0' COMMENT 'Приоритет',
  `name` varchar(255) NOT NULL,
  `is_listing` tinyint(4) NOT NULL,
  `controller` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `params` text NOT NULL,
  `scope` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

--
-- Дамп данных таблицы `fx_infoblock`
--

INSERT INTO `fx_infoblock` (`id`, `parent_infoblock_id`, `site_id`, `page_id`, `checked`, `priority`, `name`, `is_listing`, `controller`, `action`, `params`, `scope`) VALUES
(3, 0, 1, 2, 1, 0, 'Главное меню', 1, 'component_section', 'listing', 'a:5:{s:5:"limit";s:1:"0";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:13:"mount_page_id";s:9:"parent_id";s:0:"";}', 'a:1:{s:5:"pages";s:3:"all";}'),
(12, 0, 1, 0, 1, 0, 'Layout', 0, 'layout', 'show', '', ''),
(15, 0, 1, 2, 1, 0, 'Основной текст', 0, 'component_text', 'listing', 'a:5:{s:5:"limit";s:2:"10";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:1:{s:5:"pages";s:3:"all";}'),
(16, 0, 1, 2, 1, 0, 'Доп. текст для главной', 0, 'component_text', 'listing', 'a:5:{s:5:"limit";s:2:"10";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:1:{s:5:"pages";s:4:"this";}'),
(17, 15, 1, 5, 1, 0, 'Текст наследованный', 0, '', '', 'a:5:{s:5:"limit";s:2:"10";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:1:{s:5:"pages";s:4:"this";}'),
(18, 0, 1, 2, 1, 0, '', 0, 'component_section', 'mirror', 'a:5:{s:5:"limit";s:2:"10";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:8:"from_all";b:0;s:9:"parent_id";s:1:"1";}', 'a:1:{s:5:"pages";s:3:"all";}'),
(19, 0, 1, 2, 1, 0, 'Текст в сайдбар', 0, 'component_text', 'listing', 'a:5:{s:5:"limit";s:2:"10";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:13:"mount_page_id";s:9:"parent_id";s:0:"";}', 'a:1:{s:5:"pages";s:3:"all";}'),
(22, 0, 1, 2, 1, 0, '', 0, 'component_section', 'listing', 'a:5:{s:5:"limit";s:2:"10";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:13:"mount_page_id";s:9:"parent_id";s:0:"";}', 'a:1:{s:5:"pages";s:3:"all";}');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_infoblock2layout`
--

CREATE TABLE IF NOT EXISTS `fx_infoblock2layout` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `infoblock_id` int(10) unsigned NOT NULL,
  `layout_id` int(10) unsigned NOT NULL,
  `wrapper_name` varchar(255) NOT NULL,
  `wrapper_variant` varchar(50) NOT NULL,
  `wrapper_visual` text NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `template_variant` varchar(50) NOT NULL,
  `template_visual` text NOT NULL,
  `area` varchar(50) NOT NULL,
  `priority` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `infoblock_id` (`infoblock_id`,`layout_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Дамп данных таблицы `fx_infoblock2layout`
--

INSERT INTO `fx_infoblock2layout` (`id`, `infoblock_id`, `layout_id`, `wrapper_name`, `wrapper_variant`, `wrapper_visual`, `template_name`, `template_variant`, `template_visual`, `area`, `priority`) VALUES
(24, 22, 1, '', '', '', 'layout_supernova', 'supermenu', 'a:1:{s:5:"title";s:16:"Полезное";}', 'footer', 0),
(2, 3, 1, '', '', 'a:2:{i:163;s:13:"Ус-луги";s:9:"separator";s:5:"• !";}', 'layout_supernova', 'demo_menu', 'a:3:{s:9:"separator";s:3:"•";s:6:"odd_bg";s:4:"#111";s:9:"odd_color";s:4:"#FF0";}', 'header', 0),
(21, 19, 1, 'layout_supernova', 'wrap_titled', 'a:2:{s:5:"title";s:11:"Важно:";s:5:"color";s:4:"#666";}', 'component_text', 'listing', '', 'sidebar', 0),
(14, 12, 1, '', '', '', 'layout_supernova', 'inner', 'a:6:{s:4:"copy";s:91:"© 2010 группа компаний «FloxiShop».<br>Все права защищены.";s:4:"logo";s:38:"/controllers/layout/supernova/logo.png";s:7:"company";s:14:"Floxim Company";s:6:"slogan";s:37:"лучшие утюги России!";s:13:"replace_src_0";s:38:"/controllers/layout/supernova/logo.png";s:8:"developa";s:103:"© 2010 Хороший пример \n<br>\nсайтостроения — \n<a href="#">\nWebSite.ru\n</a>\n";}', '', 0),
(19, 17, 1, 'layout_supernova', 'wrap_titled', 'a:1:{s:5:"title";s:15:"Inherited title";}', 'component_text', 'listing', '', '', 0),
(20, 18, 1, '', '', '', 'layout_supernova', 'supermenu', 'a:2:{s:5:"title";s:34:"Мы умеем продавать";s:10:"menu_title";s:27:"Наша продукция";}', 'sidebar', 0),
(17, 15, 1, 'layout_supernova', 'wrap_titled', 'a:1:{s:5:"title";s:71:"Добро пожаловать, &nbsp;<i>ура, товарищи</i>!";}', 'component_text', 'listing', '', 'content', 0),
(18, 16, 1, 'layout_supernova', 'wrap_titled', 'a:2:{s:5:"title";s:38:"Акция, не пропустите!";s:5:"color";s:4:"#C00";}', 'component_text', 'listing', '', 'content', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_infoblock_bak`
--

CREATE TABLE IF NOT EXISTS `fx_infoblock_bak` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `field_id` int(11) NOT NULL DEFAULT '0',
  `site_id` int(11) NOT NULL,
  `page_id` int(10) unsigned NOT NULL,
  `template_id` int(11) NOT NULL DEFAULT '0',
  `main_content` tinyint(1) NOT NULL DEFAULT '0',
  `keyword` varchar(255) NOT NULL COMMENT 'Месторазмещение инфоблока',
  `url` varchar(255) NOT NULL COMMENT 'Для формирования URL',
  `name` varchar(255) DEFAULT NULL COMMENT 'Название инфоблока',
  `checked` tinyint(1) NOT NULL DEFAULT '1',
  `priority` int(11) DEFAULT '0' COMMENT 'Приоритет',
  `subdivision_id` int(11) NOT NULL DEFAULT '0',
  `individual` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Показывть только в разделе subdivision_id',
  `type` enum('content','widget') NOT NULL,
  `subtype` varchar(255) DEFAULT NULL,
  `essence_id` int(11) DEFAULT NULL,
  `list_ctpl_id` int(11) NOT NULL DEFAULT '0',
  `full_ctpl_id` int(11) NOT NULL DEFAULT '0',
  `use_format` int(11) NOT NULL DEFAULT '0',
  `replace_value` text NOT NULL,
  `visual` text COMMENT 'Значения визуальных настроек',
  `rec_num` int(11) DEFAULT NULL COMMENT 'Количество выводимых объектов',
  `sort` text COMMENT 'Сортировка',
  `default_action` varchar(255) NOT NULL DEFAULT 'index' COMMENT 'Действие по умолчанию',
  `source` text NOT NULL COMMENT 'Источник данных для зеркальных инфоблоков',
  `content_selection` text NOT NULL COMMENT 'Выбранные объекты для показа',
  `access` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `keyword` (`keyword`,`subdivision_id`,`type`),
  KEY `essence_id` (`essence_id`),
  KEY `page_id` (`page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=187 ;

--
-- Дамп данных таблицы `fx_infoblock_bak`
--

INSERT INTO `fx_infoblock_bak` (`id`, `parent_id`, `field_id`, `site_id`, `page_id`, `template_id`, `main_content`, `keyword`, `url`, `name`, `checked`, `priority`, `subdivision_id`, `individual`, `type`, `subtype`, `essence_id`, `list_ctpl_id`, `full_ctpl_id`, `use_format`, `replace_value`, `visual`, `rec_num`, `sort`, `default_action`, `source`, `content_selection`, `access`) VALUES
(2, 0, 0, 1, 5, 0, 1, 'main_content', 'cond', 'HTML-текст', 1, 0, 5, 1, 'content', 'block', 19, 32, 0, 0, '', NULL, NULL, NULL, 'index', '', '', ''),
(80, 0, 0, 1, 6, 0, 1, 'main_content', 'text', 'Текстик', 1, 17, 6, 1, 'content', 'block', 19, 32, 0, 0, 'a:2:{i:0;s:47:"Эпическая медлительность";i:1;s:42:"Примечание на странице";}', NULL, 0, 'a:0:{}', 'index', '', '', ''),
(120, 0, 0, 1, 0, 10, 0, 'slogan_baner', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:1:{i:0;s:43:"<b>Великолепные утюги!</b>";}', NULL, NULL, NULL, 'index', '', '', ''),
(121, 0, 0, 1, 0, 10, 0, 'infoblock4', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:3:{i:0;s:43:"/floxim_templates/demo4/css/images/logo.png";i:1;s:0:"";i:2;s:0:"";}', NULL, NULL, NULL, 'index', '', '', ''),
(88, 0, 0, 1, 0, 10, 0, 'slogan', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:1:{i:0;s:30:"Вся электроника!";}', NULL, NULL, NULL, 'index', '', '', ''),
(87, 0, 0, 1, 0, 1, 0, 'company_name', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:1:{i:0;s:38:"Утюги и холодильники";}', NULL, NULL, NULL, 'index', '', '', ''),
(7, 0, 0, 1, 12, 0, 1, 'main_content', 'recoverpasswd', NULL, 1, 0, 16, 0, 'widget', NULL, 2, 0, 0, 0, '', NULL, NULL, NULL, 'index', '', '', ''),
(9, 0, 0, 1, 7, 0, 1, 'main_content', 'user', 'Пользователи', 1, 1, 8, 1, 'content', 'user', 1, 1, 0, 0, '', NULL, NULL, 'a:0:{}', 'index', '', '', ''),
(11, 0, 0, 1, 13, 0, 1, 'main_content', 'pm', 'Личные сообщения', 1, 3, 17, 1, 'content', 'block', 11, 23, 0, 0, '', NULL, NULL, 'a:0:{}', 'index', '', '', ''),
(12, 0, 0, 1, 8, 0, 1, 'main_content', 'user', 'Регистрация', 1, 4, 10, 0, 'content', 'user', 1, 1, 0, 0, '', NULL, NULL, 'a:0:{}', 'add', '', '', ''),
(118, 0, 0, 1, 0, 10, 0, 'infoblock2', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:4:{i:0;s:25:"/goods/irons/goods_3.html";i:1;s:46:"/floxim_templates/demo4/css/images/utug/i2.jpg";i:2;s:0:"";i:3;s:0:"";}', NULL, NULL, NULL, 'index', '', '', ''),
(119, 0, 0, 1, 0, 10, 0, 'infoblock3', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:4:{i:0;s:25:"/goods/irons/goods_2.html";i:1;s:46:"/floxim_templates/demo4/css/images/utug/i3.jpg";i:2;s:0:"";i:3;s:0:"";}', NULL, NULL, NULL, 'index', '', '', ''),
(124, 0, 0, 1, 1, 0, 1, 'main_content', '', NULL, 1, 22, 1, 1, 'content', 'mirror', 12, 24, 0, 0, 'a:1:{i:0;s:48:"Элементы формы на сайте (h3)";}', NULL, 100, 'a:1:{s:4:"type";s:4:"last";}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(18, 0, 0, 1, 0, 1, 0, 'right_up', '', NULL, 1, 0, 0, 0, 'widget', '', 1, 0, 0, 0, '', 'a:5:{s:4:"view";s:1:"0";s:13:"show_reg_link";s:1:"0";s:18:"show_recovery_link";s:1:"0";s:9:"loginsave";s:4:"none";s:7:"show_pm";s:1:"0";}', NULL, NULL, 'index', '', '', ''),
(19, 0, 0, 1, 0, 1, 0, 'right_up', '', NULL, 1, 0, 5555, 1, 'widget', NULL, 1, 0, 0, 0, '', NULL, NULL, NULL, 'index', '', '', ''),
(20, 0, 0, 1, 0, 1, 0, 'right_block', '', 'Новости/блог', 1, 0, 5555, 1, 'content', 'mirror', 8, 16, 0, 1, 'a:1:{i:0;s:14:"Новости";}', 'a:2:{s:4:"view";s:2:"br";s:9:"show_date";s:0:"";}', 0, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(135, 0, 0, 1, 0, 1, 0, 'slogan_text', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:1:{i:0;s:473:"Вы думаете, утюг - это не современно? Ошибаетесь!<br /><br />В нашем магазине представлены лучшие и самые современные модели утюгов, разработанные дизайнерами с мировым именем и выпускаемые международными корпорациями!<br /><br />Выбери утюг своей мечты прямо сейчас!";}', NULL, NULL, NULL, 'index', '', '', ''),
(117, 0, 0, 1, 0, 10, 0, 'infoblock1', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:4:{i:0;s:25:"/goods/irons/goods_4.html";i:1;s:45:"/floxim_templates/demo4/css/images/utug/i.jpg";i:2;s:0:"";i:3;s:0:"";}', NULL, NULL, NULL, 'index', '', '', ''),
(130, 0, 0, 1, 2, 0, 1, 'main_content', '', NULL, 1, 26, 2, 1, 'content', 'mirror', 12, 38, 0, 0, 'a:1:{i:0;s:0:"";}', NULL, 0, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:2:{s:4:"type";s:6:"manual";s:7:"content";a:6:{i:0;i:7;i:1;i:5;i:2;i:4;i:3;i:3;i:4;i:2;i:5;i:1;}}', ''),
(132, 0, 0, 1, 16, 0, 1, 'main_content', 'text', 'Текст', 1, 27, 23, 1, 'content', 'block', 19, 32, 0, 0, 'a:1:{i:0;s:48:"Элементы формы на сайте (h3)";}', NULL, NULL, 'a:0:{}', 'index', '', '', ''),
(27, 0, 0, 1, 0, 4, 0, 'right_block', '', '', 1, 1, 0, 0, 'content', 'mirror', 4, 9, 0, 0, 'a:1:{i:0;s:47:"Эпическая медлительность";}', '', 0, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(28, 0, 0, 1, 0, 4, 0, 'right_block', '', 'Новости/блог', 1, 0, 5555, 1, 'content', 'mirror', 8, 16, 0, 1, 'a:1:{i:0;s:14:"Новости";}', '', 3, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(134, 0, 0, 1, 0, 1, 0, 'slogan_banner', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:1:{i:0;s:59:"Покупайте утюги, будете здоровы!";}', NULL, NULL, NULL, 'index', '', '', ''),
(30, 0, 0, 1, 2, 0, 1, 'main_content', 'text1', 'Текст (1)', 1, 4, 2, 1, 'content', 'block', 19, 32, 0, 0, 'a:1:{i:0;s:0:"";}', NULL, 0, 'a:0:{}', 'index', '', '', ''),
(113, 0, 0, 1, 0, 10, 0, 'infoblock0', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:1:{i:0;s:43:"/floxim_templates/demo4/css/images/logo.png";}', NULL, NULL, NULL, 'index', '', '', ''),
(32, 0, 0, 2, 18, 0, 1, 'main_content', '', NULL, 1, 0, 25, 1, 'content', 'mirror', 8, 17, 0, 0, 'a:1:{i:0;s:15:"Мой блог";}', 'a:2:{s:5:"group";s:4:"none";s:10:"date_place";s:11:"after_title";}', 3, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(33, 0, 0, 2, 20, 0, 1, 'main_content', 'text', 'Текст', 1, 0, 27, 1, 'content', 'block', 19, 32, 0, 0, 'a:2:{i:0;s:47:"Эпическая медлительность";i:1;s:42:"Примечание на странице";}', NULL, NULL, 'a:1:{s:4:"type";s:1:"0";}', 'index', '', '', ''),
(34, 0, 0, 2, 18, 0, 1, 'main_content', '', NULL, 1, 1, 25, 1, 'content', 'mirror', 10, 20, 0, 0, 'a:1:{i:0;s:19:"Мои работы";}', 'a:3:{s:5:"width";s:3:"350";s:6:"height";s:3:"200";s:5:"pause";s:1:"3";}', NULL, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(35, 0, 0, 2, 21, 0, 1, 'main_content', 'photo', 'Фотогалерея', 1, 1, 28, 1, 'content', 'block', 10, 22, 0, 0, 'a:2:{i:0;s:47:"Эпическая медлительность";i:1;s:42:"Примечание на странице";}', 'a:6:{s:5:"width";s:3:"200";s:6:"height";s:3:"150";s:5:"label";s:4:"down";s:4:"open";s:5:"layer";s:16:"border_thickness";s:1:"1";s:12:"border_color";s:7:"#00ff00";}', NULL, 'a:1:{s:4:"type";s:1:"0";}', 'index', '', '', ''),
(36, 0, 0, 2, 22, 0, 1, 'main_content', 'newsblog', 'Новости/блог', 1, 2, 29, 1, 'content', 'block', 8, 17, 0, 0, 'a:2:{i:0;s:47:"Эпическая медлительность";i:1;s:42:"Примечание на странице";}', 'a:2:{s:5:"group";s:4:"none";s:10:"date_place";s:11:"after_title";}', NULL, 'a:1:{s:4:"type";s:1:"0";}', 'index', '', '', ''),
(37, 0, 0, 2, 23, 0, 1, 'main_content', 'resumelinks', 'Резюме: список проектов (Ссылки)', 1, 3, 30, 1, 'content', 'block', 18, 31, 0, 0, 'a:2:{i:0;s:47:"Эпическая медлительность";i:1;s:42:"Примечание на странице";}', NULL, NULL, 'a:1:{s:4:"type";s:1:"0";}', 'index', '', '', ''),
(38, 0, 0, 2, 24, 0, 1, 'main_content', 'resumecontacts', 'Резюме: контакты (Контакты персональные)', 1, 4, 31, 1, 'content', 'block', 15, 28, 0, 0, 'a:2:{i:0;s:47:"Эпическая медлительность";i:1;s:42:"Примечание на странице";}', NULL, NULL, 'a:1:{s:4:"type";s:1:"0";}', 'index', '', '', ''),
(126, 0, 0, 1, 37, 0, 1, 'main_content', 'news', 'Новости', 1, 23, 70, 1, 'content', 'block', 8, 15, 0, 0, 'a:1:{i:0;s:48:"Элементы формы на сайте (h3)";}', NULL, NULL, 'a:0:{}', 'index', '', '', ''),
(40, 0, 0, 1, 0, 10, 0, 'banner_right', '', 'Новости/блог', 1, 0, 5555, 1, 'content', 'mirror', 8, 16, 0, 1, 'a:1:{i:0;s:14:"Новости";}', '', 3, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(114, 0, 0, 1, 0, 10, 0, 'top_right_block', '', NULL, 1, 0, 0, 0, 'widget', '', 1, 0, 0, 0, '', 'a:5:{s:4:"view";s:1:"0";s:13:"show_reg_link";s:1:"1";s:18:"show_recovery_link";s:1:"1";s:9:"loginsave";s:1:"0";s:7:"show_pm";s:1:"0";}', NULL, NULL, 'index', '', '', ''),
(103, 0, 0, 1, 0, 1, 0, 'slogan', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:1:{i:0;s:106:"«Слоган нашей компании <br />на всех мониторах в интернете»!";}', NULL, NULL, NULL, 'index', '', '', ''),
(53, 0, 0, 1, 0, 1, 0, 'infoblock2', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:3:{i:0;s:43:"/floxim_templates/demo4/css/images/logo.png";i:1;s:0:"";i:2;s:0:"";}', NULL, NULL, NULL, 'index', '', '', ''),
(54, 0, 0, 1, 0, 1, 0, 'developer', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:1:{i:0;s:99:"© 2012 Хороший пример <br />сайтостроения — <a href="#">WebSite.ru</a>";}', NULL, NULL, NULL, 'index', '', '', ''),
(55, 0, 0, 1, 0, 7, 0, 'right_up', '', '', 1, 0, 0, 0, 'widget', '', 1, 0, 0, 0, '', '', 0, '', 'index', '', '', ''),
(51, 0, 0, 1, 0, 1, 0, 'infoblock0', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:1:{i:0;s:34:"/floxim_files/messages/12/i3_0.jpg";}', NULL, NULL, NULL, 'index', '', '', ''),
(133, 0, 0, 1, 0, 1, 0, 'logo_image', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:3:{i:0;s:43:"/floxim_templates/demo4/css/images/logo.png";i:1;s:0:"";i:2;s:0:"";}', NULL, NULL, NULL, 'index', '', '', ''),
(56, 0, 0, 1, 0, 7, 0, 'right_up', '', '', 1, 0, 5555, 1, 'widget', '', 1, 0, 0, 0, '', '', 0, '', 'index', '', '', ''),
(57, 0, 0, 1, 0, 13, 0, 'right_up', '', '', 1, 0, 0, 0, 'widget', '', 1, 0, 0, 0, '', '', 0, '', 'index', '', '', ''),
(58, 0, 0, 1, 0, 13, 0, 'left_block', '', '', 1, 1, 0, 0, 'content', 'mirror', 4, 9, 0, 0, 'a:1:{i:0;s:47:"Эпическая медлительность";}', '', 0, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(59, 0, 0, 1, 0, 13, 0, 'left_block', '', '', 1, 2, 0, 0, 'content', 'mirror', 8, 15, 0, 0, 'a:1:{i:0;s:14:"Новости";}', 'a:2:{s:5:"group";s:1:"0";s:10:"date_place";s:1:"0";}', 0, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:6:"manual";}', ''),
(60, 0, 0, 1, 0, 13, 0, 'right_up', '', '', 1, 0, 5555, 1, 'widget', '', 1, 0, 0, 0, '', '', 0, '', 'index', '', '', ''),
(61, 0, 0, 1, 0, 13, 0, 'left_block', '', 'Новости/блог', 1, 0, 5555, 1, 'content', 'mirror', 8, 16, 0, 1, 'a:1:{i:0;s:14:"Новости";}', '', 3, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(71, 0, 0, 1, 0, 16, 0, 'right_up', '', '', 1, 0, 0, 0, 'widget', '', 1, 0, 0, 0, '', 'a:5:{s:4:"view";s:1:"3";s:13:"show_reg_link";s:1:"1";s:18:"show_recovery_link";s:1:"1";s:9:"loginsave";s:1:"0";s:7:"show_pm";s:1:"1";}', 0, '', 'index', '', '', ''),
(64, 0, 0, 1, 0, 20, 0, 'right_up', '', '', 1, 0, 0, 0, 'widget', '', 3, 0, 0, 0, '', 'a:2:{s:4:"city";s:6:"moscow";s:5:"width";s:3:"175";}', 0, '', 'index', '', '', ''),
(65, 0, 0, 1, 0, 20, 0, 'left_block', '', '', 1, 1, 0, 0, 'content', 'mirror', 4, 9, 0, 0, 'a:1:{i:0;s:47:"Эпическая медлительность";}', '', 0, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(66, 0, 0, 1, 0, 20, 0, 'left_block', '', '', 1, 0, 0, 0, 'content', 'mirror', 8, 15, 0, 0, 'a:1:{i:0;s:14:"Новости";}', 'a:2:{s:5:"group";s:1:"0";s:10:"date_place";s:1:"0";}', 0, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:2:{s:4:"type";s:6:"manual";s:7:"content";a:1:{i:0;i:2;}}', ''),
(67, 0, 0, 1, 0, 20, 0, 'right_up', '', '', 1, 0, 5555, 1, 'widget', '', 1, 0, 0, 0, '', '', 0, '', 'index', '', '', ''),
(68, 0, 0, 1, 0, 20, 0, 'left_block', '', 'Новости/блог', 1, 0, 5555, 1, 'content', 'mirror', 8, 16, 0, 1, 'a:1:{i:0;s:14:"Новости";}', '', 3, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(69, 0, 0, 1, 0, 23, 0, 'right_up', '', '', 1, 0, 0, 0, 'widget', '', 1, 0, 0, 0, '', '', 0, '', 'index', '', '', ''),
(70, 0, 0, 1, 0, 23, 0, 'right_up', '', '', 1, 0, 5555, 1, 'widget', '', 1, 0, 0, 0, '', '', 0, '', 'index', '', '', ''),
(72, 0, 0, 1, 0, 16, 0, 'right_up', '', '', 1, 0, 5555, 1, 'widget', '', 1, 0, 0, 0, '', '', 0, '', 'index', '', '', ''),
(73, 0, 0, 5, 0, 13, 0, 'copyright', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:1:{i:0;s:108:"&copy; 2012 группа компаний &laquo;PlayGround&raquo;.<br />Все права защищены.";}', NULL, NULL, NULL, 'index', '', '', ''),
(74, 0, 0, 5, 0, 13, 0, 'left_block', '', NULL, 1, 3, 0, 0, 'widget', '', 3, 0, 0, 1, 'a:1:{i:0;s:18:"Заголовок";}', 'a:2:{s:4:"city";s:6:"moscow";s:5:"width";s:3:"175";}', NULL, NULL, 'index', '', '', ''),
(75, 0, 0, 5, 0, 13, 0, 'developer', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:1:{i:0;s:105:"&copy; 2010 Ништяк пример <br />сайтостроения &mdash; <a href="#">WebSite.pu</a>";}', NULL, NULL, NULL, 'index', '', '', ''),
(76, 0, 0, 5, 0, 13, 0, 'left_block', '', NULL, 1, 4, 0, 0, 'widget', '', 4, 0, 0, 1, 'a:1:{i:0;s:12:"Пропке";}', 'a:2:{s:4:"city";s:6:"moscow";s:5:"width";s:3:"150";}', NULL, NULL, 'index', '', '', ''),
(77, 0, 0, 1, 0, 32, 0, 'project_name', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:1:{i:0;s:9:"FloxiShop";}', NULL, NULL, NULL, 'index', '', '', ''),
(85, 0, 0, 1, 0, 32, 0, 'main_content', 'newsblog1', 'Новости/блог (1)', 1, 19, 5555, 1, 'content', 'block', 8, 17, 0, 0, '', 'a:3:{s:5:"group";s:5:"month";s:10:"date_place";s:11:"after_title";s:13:"show_announce";s:1:"1";}', 0, 'a:0:{}', 'index', '', '', ''),
(179, 0, 0, 1, 0, 32, 0, 'right_up', '', NULL, 1, 1, 0, 0, 'widget', '', 1, 0, 0, 0, '', 'a:5:{s:4:"view";s:1:"0";s:13:"show_reg_link";s:1:"0";s:18:"show_recovery_link";s:1:"0";s:9:"loginsave";s:4:"none";s:7:"show_pm";s:1:"0";}', NULL, NULL, 'index', '', '', ''),
(125, 0, 0, 1, 0, 10, 0, 'banner_right', '', NULL, 1, 2, 0, 0, 'content', 'mirror', 12, 37, 0, 0, 'a:1:{i:0;s:19:"Товар года";}', NULL, 1, 'a:1:{s:4:"type";s:6:"random";}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(123, 0, 0, 1, 17, 0, 1, 'main_content', 'goods', 'Товары', 1, 21, 74, 1, 'content', 'block', 12, 24, 0, 0, 'a:1:{i:0;s:48:"Элементы формы на сайте (h3)";}', NULL, NULL, 'a:0:{}', 'index', '', '', ''),
(115, 0, 0, 1, 0, 10, 0, 'slogan_text', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:1:{i:0;s:96:"Успей купить утюг, который держит в руках этот мишка!";}', NULL, NULL, NULL, 'index', '', '', ''),
(116, 0, 0, 1, 0, 10, 0, 'slogan_small', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:1:{i:0;s:50:"Осталось ОЧЕНЬ МАЛО утюгов!";}', NULL, NULL, NULL, 'index', '', '', ''),
(111, 0, 0, 7, 39, 0, 1, 'main_content', 'resumelinks', 'Резюме: список проектов (Ссылки)', 1, 3, 68, 1, 'content', 'block', 18, 31, 0, 0, 'a:2:{i:0;s:47:"Эпическая медлительность";i:1;s:42:"Примечание на странице";}', NULL, NULL, 'a:1:{s:4:"type";s:1:"0";}', 'index', '', '', ''),
(112, 0, 0, 7, 38, 0, 1, 'main_content', 'resumecontacts', 'Резюме: контакты (Контакты персональные)', 1, 4, 69, 1, 'content', 'block', 15, 28, 0, 0, 'a:2:{i:0;s:47:"Эпическая медлительность";i:1;s:42:"Примечание на странице";}', NULL, NULL, 'a:1:{s:4:"type";s:1:"0";}', 'index', '', '', ''),
(127, 0, 0, 1, 4, 0, 1, 'main_content', 'vacancy', 'Вакансии', 1, 24, 71, 1, 'content', 'block', 20, 33, 0, 0, 'a:1:{i:0;s:48:"Элементы формы на сайте (h3)";}', NULL, NULL, 'a:0:{}', 'index', '', '', ''),
(128, 0, 0, 1, 27, 0, 1, 'main_content', 'people', 'Персоны', 1, 25, 72, 1, 'content', 'block', 9, 39, 0, 0, 'a:1:{i:0;s:48:"Элементы формы на сайте (h3)";}', NULL, 0, 'a:0:{}', 'index', '', '', ''),
(129, 0, 0, 1, 0, 10, 0, 'left_block', '', NULL, 1, 5, 0, 0, 'content', 'mirror', 8, 16, 0, 1, 'a:1:{i:0;s:14:"Новости";}', 'a:2:{s:4:"view";s:4:"list";s:9:"show_date";s:5:"d.m.Y";}', 0, 'a:1:{s:4:"type";s:4:"last";}', 'index', 'a:2:{s:4:"type";s:6:"select";s:10:"infoblocks";a:1:{i:0;i:126;}}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(109, 0, 0, 7, 43, 0, 1, 'main_content', 'photo', 'Фотогалерея', 1, 1, 66, 1, 'content', 'block', 10, 22, 0, 0, 'a:2:{i:0;s:47:"Эпическая медлительность";i:1;s:42:"Примечание на странице";}', 'a:6:{s:5:"width";s:3:"200";s:6:"height";s:3:"150";s:5:"label";s:4:"down";s:4:"open";s:5:"layer";s:16:"border_thickness";s:1:"1";s:12:"border_color";s:7:"#00ff00";}', NULL, 'a:1:{s:4:"type";s:1:"0";}', 'index', '', '', ''),
(110, 0, 0, 7, 44, 0, 1, 'main_content', 'newsblog', 'Новости/блог', 1, 2, 67, 1, 'content', 'block', 8, 17, 0, 0, 'a:2:{i:0;s:47:"Эпическая медлительность";i:1;s:42:"Примечание на странице";}', 'a:2:{s:5:"group";s:4:"none";s:10:"date_place";s:11:"after_title";}', NULL, 'a:1:{s:4:"type";s:1:"0";}', 'index', '', '', ''),
(108, 0, 0, 7, 9, 0, 1, 'main_content', '', NULL, 1, 1, 63, 1, 'content', 'mirror', 10, 20, 0, 0, 'a:1:{i:0;s:19:"Мои работы";}', 'a:3:{s:5:"width";s:3:"350";s:6:"height";s:3:"200";s:5:"pause";s:1:"3";}', NULL, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(107, 0, 0, 7, 42, 0, 1, 'main_content', 'text', 'Текст', 1, 0, 65, 1, 'content', 'block', 19, 32, 0, 0, 'a:2:{i:0;s:47:"Эпическая медлительность";i:1;s:42:"Примечание на странице";}', NULL, NULL, 'a:1:{s:4:"type";s:1:"0";}', 'index', '', '', ''),
(180, 0, 0, 1, 0, 32, 0, 'right', '', NULL, 1, 2, 0, 0, 'content', 'mirror', 8, 16, 0, 0, 'a:1:{i:0;s:22:"Супер акция!";}', 'a:2:{s:4:"view";s:4:"list";s:9:"show_date";s:5:"d.m.Y";}', 3, 'a:1:{s:4:"type";s:1:"0";}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(122, 0, 0, 1, 10, 0, 1, 'main_content', 'goods', 'Товары', 1, 20, 73, 1, 'content', 'block', 12, 24, 0, 0, 'a:1:{i:0;s:48:"Элементы формы на сайте (h3)";}', NULL, NULL, 'a:0:{}', 'index', '', '', ''),
(106, 0, 0, 7, 9, 0, 1, 'main_content', '', NULL, 1, 0, 63, 1, 'content', 'mirror', 8, 17, 0, 0, 'a:1:{i:0;s:15:"Мой блог";}', 'a:2:{s:5:"group";s:4:"none";s:10:"date_place";s:11:"after_title";}', 3, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(131, 0, 0, 1, 0, 10, 0, 'left_block', '', NULL, 1, 6, 0, 0, 'content', 'mirror', 20, 34, 0, 1, 'a:1:{i:0;s:16:"Вакансии";}', NULL, 0, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:2:{s:4:"type";s:6:"manual";s:7:"content";a:1:{i:0;i:1;}}', ''),
(136, 0, 0, 1, 0, 1, 0, 'right_block', '', NULL, 1, 1, 0, 0, 'content', 'mirror', 8, 16, 0, 1, 'a:1:{i:0;s:14:"Новости";}', 'a:2:{s:4:"view";s:4:"list";s:9:"show_date";s:5:"d.m.Y";}', 0, 'a:1:{s:4:"type";s:1:"0";}', 'index', 'a:2:{s:4:"type";s:6:"select";s:10:"infoblocks";a:1:{i:0;i:126;}}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(137, 0, 0, 1, 0, 1, 0, 'right_block', '', NULL, 1, 0, 0, 0, 'content', 'mirror', 20, 34, 0, 1, 'a:1:{i:0;s:16:"Вакансии";}', NULL, 0, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:2:{s:4:"type";s:6:"manual";s:7:"content";a:1:{i:0;i:1;}}', ''),
(138, 0, 0, 1, 37, 10, 0, 'slogan_baner', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:1:{i:0;s:43:"<b>Великолепные утюги!</b>";}', '', 0, '', 'index', '', '', ''),
(139, 0, 0, 1, 37, 10, 0, 'infoblock4', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:3:{i:0;s:43:"/floxim_templates/demo4/css/images/logo.png";i:1;s:0:"";i:2;s:0:"";}', '', 0, '', 'index', '', '', ''),
(140, 0, 0, 1, 37, 10, 0, 'slogan', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:1:{i:0;s:30:"Вся электроника!";}', '', 0, '', 'index', '', '', ''),
(141, 0, 0, 1, 37, 1, 0, 'company_name', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:1:{i:0;s:38:"Утюги и холодильники";}', '', 0, '', 'index', '', '', ''),
(142, 0, 0, 1, 37, 10, 0, 'infoblock2', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:4:{i:0;s:25:"/goods/irons/goods_3.html";i:1;s:46:"/floxim_templates/demo4/css/images/utug/i2.jpg";i:2;s:0:"";i:3;s:0:"";}', '', 0, '', 'index', '', '', ''),
(143, 0, 0, 1, 37, 10, 0, 'infoblock3', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:4:{i:0;s:25:"/goods/irons/goods_2.html";i:1;s:46:"/floxim_templates/demo4/css/images/utug/i3.jpg";i:2;s:0:"";i:3;s:0:"";}', '', 0, '', 'index', '', '', ''),
(144, 0, 0, 1, 37, 1, 0, 'right_up', '', '', 1, 0, 70, 1, 'widget', '', 1, 0, 0, 0, '', '', 0, '', 'index', '', '', ''),
(145, 0, 0, 1, 37, 1, 0, 'slogan_text', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:1:{i:0;s:473:"Вы думаете, утюг - это не современно? Ошибаетесь!<br /><br />В нашем магазине представлены лучшие и самые современные модели утюгов, разработанные дизайнерами с мировым именем и выпускаемые международными корпорациями!<br /><br />Выбери утюг своей мечты прямо сейчас!";}', '', 0, '', 'index', '', '', ''),
(146, 0, 0, 1, 37, 10, 0, 'infoblock1', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:4:{i:0;s:25:"/goods/irons/goods_4.html";i:1;s:45:"/floxim_templates/demo4/css/images/utug/i.jpg";i:2;s:0:"";i:3;s:0:"";}', '', 0, '', 'index', '', '', ''),
(147, 0, 0, 1, 37, 1, 0, 'slogan_banner', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:1:{i:0;s:59:"Покупайте утюги, будете здоровы!";}', '', 0, '', 'index', '', '', ''),
(148, 0, 0, 1, 37, 10, 0, 'infoblock0', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:1:{i:0;s:43:"/floxim_templates/demo4/css/images/logo.png";}', '', 0, '', 'index', '', '', ''),
(149, 0, 0, 1, 37, 10, 0, 'top_right_block', '', '', 1, 0, 70, 1, 'widget', '', 1, 0, 0, 0, '', 'a:5:{s:4:"view";s:1:"0";s:13:"show_reg_link";s:1:"1";s:18:"show_recovery_link";s:1:"1";s:9:"loginsave";s:1:"0";s:7:"show_pm";s:1:"0";}', 0, '', 'index', '', '', ''),
(150, 0, 0, 1, 37, 1, 0, 'slogan', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:1:{i:0;s:106:"«Слоган нашей компании <br />на всех мониторах в интернете»!";}', '', 0, '', 'index', '', '', ''),
(151, 0, 0, 1, 37, 1, 0, 'infoblock2', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:3:{i:0;s:43:"/floxim_templates/demo4/css/images/logo.png";i:1;s:0:"";i:2;s:0:"";}', '', 0, '', 'index', '', '', ''),
(152, 0, 0, 1, 37, 1, 0, 'developer', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:1:{i:0;s:99:"© 2012 Хороший пример <br />сайтостроения — <a href="#">WebSite.ru</a>";}', '', 0, '', 'index', '', '', ''),
(153, 0, 0, 1, 37, 7, 0, 'right_up', '', '', 1, 0, 70, 1, 'widget', '', 1, 0, 0, 0, '', '', 0, '', 'index', '', '', ''),
(154, 0, 0, 1, 37, 1, 0, 'infoblock0', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:1:{i:0;s:34:"/floxim_files/messages/12/i3_0.jpg";}', '', 0, '', 'index', '', '', ''),
(155, 0, 0, 1, 37, 1, 0, 'logo_image', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:3:{i:0;s:43:"/floxim_templates/demo4/css/images/logo.png";i:1;s:0:"";i:2;s:0:"";}', '', 0, '', 'index', '', '', ''),
(156, 0, 0, 1, 37, 13, 0, 'right_up', '', '', 1, 0, 70, 1, 'widget', '', 1, 0, 0, 0, '', '', 0, '', 'index', '', '', ''),
(157, 0, 0, 1, 37, 16, 0, 'right_up', '', '', 1, 0, 70, 1, 'widget', '', 1, 0, 0, 0, '', 'a:5:{s:4:"view";s:1:"3";s:13:"show_reg_link";s:1:"1";s:18:"show_recovery_link";s:1:"1";s:9:"loginsave";s:1:"0";s:7:"show_pm";s:1:"1";}', 0, '', 'index', '', '', ''),
(158, 0, 0, 1, 37, 20, 0, 'right_up', '', '', 1, 0, 70, 1, 'widget', '', 3, 0, 0, 0, '', 'a:2:{s:4:"city";s:6:"moscow";s:5:"width";s:3:"175";}', 0, '', 'index', '', '', ''),
(159, 0, 0, 1, 37, 20, 0, 'left_block', '', '', 1, 0, 70, 1, 'content', 'mirror', 8, 15, 0, 0, 'a:1:{i:0;s:14:"Новости";}', 'a:2:{s:5:"group";s:1:"0";s:10:"date_place";s:1:"0";}', 0, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:2:{s:4:"type";s:6:"manual";s:7:"content";a:1:{i:0;i:2;}}', ''),
(160, 0, 0, 1, 37, 23, 0, 'right_up', '', '', 1, 0, 70, 1, 'widget', '', 1, 0, 0, 0, '', '', 0, '', 'index', '', '', ''),
(161, 0, 0, 5, 37, 13, 0, 'copyright', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:1:{i:0;s:108:"&copy; 2012 группа компаний &laquo;PlayGround&raquo;.<br />Все права защищены.";}', '', 0, '', 'index', '', '', ''),
(162, 0, 0, 5, 37, 13, 0, 'developer', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:1:{i:0;s:105:"&copy; 2010 Ништяк пример <br />сайтостроения &mdash; <a href="#">WebSite.pu</a>";}', '', 0, '', 'index', '', '', ''),
(163, 0, 0, 1, 37, 32, 0, 'project_name', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:1:{i:0;s:7:"Bed Cat";}', '', 0, '', 'index', '', '', ''),
(164, 0, 0, 1, 37, 10, 0, 'slogan_text', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:1:{i:0;s:96:"Успей купить утюг, который держит в руках этот мишка!";}', '', 0, '', 'index', '', '', ''),
(165, 0, 0, 1, 37, 10, 0, 'slogan_small', '', '', 1, 0, 70, 1, 'content', '', 0, 0, 0, 0, 'a:1:{i:0;s:50:"Осталось ОЧЕНЬ МАЛО утюгов!";}', '', 0, '', 'index', '', '', ''),
(182, 0, 0, 1, 37, 32, 0, 'right_up', '', NULL, 1, 2, 70, 1, 'widget', '', 1, 0, 0, 0, '', 'a:5:{s:4:"view";s:1:"0";s:13:"show_reg_link";s:1:"0";s:18:"show_recovery_link";s:1:"0";s:9:"loginsave";s:4:"none";s:7:"show_pm";s:1:"0";}', NULL, NULL, 'index', '', '', ''),
(167, 0, 0, 1, 37, 4, 0, 'right_block', '', '', 1, 1, 70, 1, 'content', 'mirror', 4, 9, 0, 0, 'a:1:{i:0;s:47:"Эпическая медлительность";}', '', 0, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(168, 0, 0, 1, 37, 13, 0, 'left_block', '', '', 1, 1, 70, 1, 'content', 'mirror', 4, 9, 0, 0, 'a:1:{i:0;s:47:"Эпическая медлительность";}', '', 0, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(169, 0, 0, 1, 37, 20, 0, 'left_block', '', '', 1, 1, 70, 1, 'content', 'mirror', 4, 9, 0, 0, 'a:1:{i:0;s:47:"Эпическая медлительность";}', '', 0, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(184, 0, 0, 1, 37, 32, 0, 'right', '', NULL, 1, 4, 70, 1, 'content', 'mirror', 20, 34, 0, 1, 'a:1:{i:0;s:16:"Вакансии";}', NULL, 0, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:2:{s:4:"type";s:6:"manual";s:7:"content";a:1:{i:0;i:1;}}', ''),
(171, 0, 0, 1, 37, 13, 0, 'left_block', '', '', 1, 2, 70, 1, 'content', 'mirror', 8, 15, 0, 0, 'a:1:{i:0;s:14:"Новости";}', 'a:2:{s:5:"group";s:1:"0";s:10:"date_place";s:1:"0";}', 0, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:6:"manual";}', ''),
(172, 0, 0, 1, 37, 10, 0, 'banner_right', '', '', 1, 2, 70, 1, 'content', 'mirror', 12, 37, 0, 0, 'a:1:{i:0;s:19:"Товар года";}', '', 1, 'a:1:{s:4:"type";s:6:"random";}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(181, 0, 0, 1, 0, 32, 0, 'right', '', NULL, 1, 3, 0, 0, 'content', 'mirror', 20, 34, 0, 1, 'a:1:{i:0;s:16:"Вакансии";}', NULL, 0, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:2:{s:4:"type";s:6:"manual";s:7:"content";a:1:{i:0;i:1;}}', ''),
(174, 0, 0, 5, 37, 13, 0, 'left_block', '', '', 1, 3, 70, 1, 'widget', '', 3, 0, 0, 1, 'a:1:{i:0;s:18:"Заголовок";}', 'a:2:{s:4:"city";s:6:"moscow";s:5:"width";s:3:"175";}', 0, '', 'index', '', '', ''),
(175, 0, 0, 1, 37, 1, 0, 'right_block', '', '', 1, 3, 70, 1, 'content', 'mirror', 20, 34, 0, 1, 'a:1:{i:0;s:16:"Вакансии";}', '', 0, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:2:{s:4:"type";s:6:"manual";s:7:"content";a:1:{i:0;i:1;}}', ''),
(176, 0, 0, 5, 37, 13, 0, 'left_block', '', '', 1, 4, 70, 1, 'widget', '', 4, 0, 0, 1, 'a:1:{i:0;s:12:"Пропке";}', 'a:2:{s:4:"city";s:6:"moscow";s:5:"width";s:3:"150";}', 0, '', 'index', '', '', ''),
(177, 0, 0, 1, 37, 10, 0, 'left_block', '', '', 1, 5, 70, 1, 'content', 'mirror', 8, 16, 0, 1, 'a:1:{i:0;s:14:"Новости";}', 'a:2:{s:4:"view";s:4:"list";s:9:"show_date";s:5:"d.m.Y";}', 0, 'a:1:{s:4:"type";s:4:"last";}', 'index', 'a:2:{s:4:"type";s:6:"select";s:10:"infoblocks";a:1:{i:0;i:126;}}', 'a:1:{s:4:"type";s:4:"auto";}', ''),
(178, 0, 0, 1, 37, 10, 0, 'left_block', '', '', 1, 6, 70, 1, 'content', 'mirror', 20, 34, 0, 1, 'a:1:{i:0;s:16:"Вакансии";}', '', 0, 'a:0:{}', 'index', 'a:1:{s:4:"type";s:3:"all";}', 'a:2:{s:4:"type";s:6:"manual";s:7:"content";a:1:{i:0;i:1;}}', ''),
(185, 0, 0, 1, 0, 1, 0, 'address', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:1:{i:0;s:119:"Адрес: г. Москва, ул. Манофактурная, д. 14<br />Телефон и факс: (831) 220-80-18";}', NULL, NULL, NULL, 'index', '', '', ''),
(186, 0, 0, 1, 0, 1, 0, 'copyright', '', NULL, 1, 0, 0, 0, 'content', NULL, NULL, 0, 0, 0, 'a:1:{i:0;s:90:"© 2011 группа компаний «Netcat».<br />Все права защищены.";}', NULL, NULL, NULL, 'index', '', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_layout`
--

CREATE TABLE IF NOT EXISTS `fx_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `fx_layout`
--

INSERT INTO `fx_layout` (`id`, `keyword`, `name`) VALUES
(1, 'supernova', 'Super Nova');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_mail_template`
--

CREATE TABLE IF NOT EXISTS `fx_mail_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` text,
  `html` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyword` (`keyword`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `fx_mail_template`
--

INSERT INTO `fx_mail_template` (`id`, `keyword`, `subject`, `body`, `html`) VALUES
(1, 'auth_register_confirm', 'Подтверждение регистрации на сайте %SITE_NAME', 'Здравствуйте, %USER_NAME<br><br>\nВы успешно зарегистрировались на сайте <a href=''%SITE_URL''>%SITE_NAME</a><br>\nВаш логин: %USER_LOGIN<br>\nВаш пароль: %PASSWORD<br><br>\nЧтобы активировать Ваш аккаунт откройте, пожалуйста, данную ссылку: <a href=''%CONFIRM_LINK''>%CONFIRM_LINK</a><br><br>\nВы получили это сообщение, потому что Ваш e-mail адрес был зарегистрирован на сайте %SITE_URL<br>\nЕсли Вы не регистрировались на этом сайте, пожалуйста, проигнорируйте это письмо.<br><br>\nС наилучшими пожеланиями, администрация сайта <a href=''%SITE_URL''>%SITE_NAME</a>.', 1),
(2, 'auth_passwd_recovery', 'Восстановление пароля на сайте %SITE_NAME', 'Здравствуйте, %USER_NAME<br><br>\nДля восстановления пароля для пользователя %USER_LOGIN на сайте <a href=''%SITE_URL''>%SITE_NAME</a> откройте, пожалуйста, данную ссылку: <a href=''%CONFIRM_LINK''>%CONFIRM_LINK</a><br><br>\nЕсли Вы не запрашивали восстановление пароля, пожалуйста, проигнорируйте это письмо.<br><br>\nС наилучшими пожеланиями, администрация сайта <a href=''%SITE_URL''>%SITE_NAME</a>.', 1),
(3, 'auth_register_notify', 'Новый пользователь на сайте %SITE_NAME', 'Здравствуйте, администратор.<br><br>\nНа сайте <a href=''%SITE_URL''>%SITE_NAME</a> зарегистрирован новый пользователь <a href=''%PROFILE_LINK''>%USER_LOGIN</a><br><br>\nС наилучшими пожеланиями, сайт <a href=''%SITE_URL''>%SITE_NAME</a>.', 1),
(4, 'auth_new_pm', 'Новое личное сообщение на сайте %SITE_NAME', 'Здравствуйте, %USER_NAME<br><br>\nВам пришло новое личное сообщение на сайте <a href=''%SITE_URL''>%SITE_NAME</a>. Вы можете прочитать это сообщение в своем <a href=''%PM_LINK''>личном кабинете</a>.<br><br>\nС наилучшими пожеланиями, администрация сайта <a href=''%SITE_URL''>%SITE_NAME</a>.', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_menu`
--

CREATE TABLE IF NOT EXISTS `fx_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL,
  `site_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL DEFAULT '0',
  `subdivision_id` int(11) NOT NULL DEFAULT '0',
  `type` enum('level','sub','path','manual') NOT NULL DEFAULT 'level',
  `settings` text,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `keyword` (`keyword`,`subdivision_id`,`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_module`
--

CREATE TABLE IF NOT EXISTS `fx_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `installed` tinyint(4) NOT NULL DEFAULT '0',
  `inside_admin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `checked` tinyint(4) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `Checked` (`checked`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `fx_module`
--

INSERT INTO `fx_module` (`id`, `name`, `keyword`, `description`, `installed`, `inside_admin`, `checked`) VALUES
(1, 'FX_MODULE_AUTH', 'auth', 'FX_MODULE_AUTH_DESCRIPTION', 1, 1, 1),
(3, 'FX_MODULE_FORUM', 'forum', 'FX_MODULE_FORUM_DESCRIPTION', 1, 1, 1),
(4, 'FX_MODULE_FILEMANAGER', 'filemanager', 'FX_MODULE_FILEMANAGER_DESCRIPTION', 1, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_multiselect`
--

CREATE TABLE IF NOT EXISTS `fx_multiselect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `element_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=71 ;

--
-- Дамп данных таблицы `fx_multiselect`
--

INSERT INTO `fx_multiselect` (`id`, `field_id`, `content_id`, `element_id`) VALUES
(58, 147, 3, 4),
(57, 147, 3, 3);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_patch`
--

CREATE TABLE IF NOT EXISTS `fx_patch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `description` char(255) NOT NULL,
  `from` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_permission`
--

CREATE TABLE IF NOT EXISTS `fx_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0',
  `essence_id` int(11) NOT NULL DEFAULT '0',
  `permission_set` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `begin` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `User_ID` (`user_id`),
  KEY `AdminType` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `fx_permission`
--

INSERT INTO `fx_permission` (`id`, `user_id`, `type`, `essence_id`, `permission_set`, `group_id`, `begin`, `end`) VALUES
(1, 1, 1, 0, 0, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_redirect`
--

CREATE TABLE IF NOT EXISTS `fx_redirect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `priority` int(11) NOT NULL,
  `checked` tinyint(1) NOT NULL DEFAULT '1',
  `old_url` varchar(255) NOT NULL DEFAULT '',
  `new_url` varchar(255) NOT NULL DEFAULT '',
  `header` int(3) DEFAULT '301',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `fx_redirect`
--

INSERT INTO `fx_redirect` (`id`, `priority`, `checked`, `old_url`, `new_url`, `header`) VALUES
(1, 0, 1, 'floxim.org/old_url/', 'floxim.org/new_url/', 301);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_session`
--

CREATE TABLE IF NOT EXISTS `fx_session` (
  `id` char(32) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `session_start` int(11) NOT NULL DEFAULT '0',
  `session_time` int(11) NOT NULL DEFAULT '0',
  `ip` bigint(11) NOT NULL DEFAULT '0',
  `login_save` tinyint(1) NOT NULL DEFAULT '0',
  `site_id` int(11) NOT NULL DEFAULT '0',
  `auth_type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `User_ID` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_session`
--

INSERT INTO `fx_session` (`id`, `user_id`, `session_start`, `session_time`, `ip`, `login_save`, `site_id`, `auth_type`) VALUES
('8c094d8fc0f2e29551ae2e6a2058a1e4', 3, 1362384968, 1362567135, 2130706433, 0, 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_settings`
--

CREATE TABLE IF NOT EXISTS `fx_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `module` varchar(255) NOT NULL DEFAULT 'system',
  `site_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `site_ID` (`site_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40 ;

--
-- Дамп данных таблицы `fx_settings`
--

INSERT INTO `fx_settings` (`id`, `key`, `value`, `module`, `site_id`) VALUES
(1, 'version', '0.9.9', 'system', 0),
(2, 'last_check', '1346408043', 'system', 0),
(3, 'last_response', 'a:3:{s:10:"next_patch";s:5:"1.0.0";s:19:"next_patch_fulllink";s:39:"http://floxim.org/update/update_15.html";s:15:"next_patch_link";s:61:"http://floxim.org/?essence=module_patch&action=download&id=15";}', 'system', 0),
(4, 'next_patch', '1.0.0', 'system', 0),
(5, 'user_email_field', 'email', 'system', 0),
(6, 'spam_from_name', 'Администратор', 'system', 0),
(7, 'spam_from_email', 'info@nc5.loc', 'system', 0),
(8, 'secret_key', '387768baa556f6f94ee29cb0e3e2a662', 'system', 0),
(9, 'authtype', '3', 'auth', 0),
(10, 'pm_allow', '1', 'auth', 0),
(11, 'pm_notify', '1', 'auth', 0),
(12, 'friend_allow', '1', 'auth', 0),
(13, 'banned_allow', '', 'auth', 0),
(14, 'incorrect_login_form_disable', '0', 'auth', 0),
(15, 'allow_registration', '1', 'auth', 0),
(16, 'external_user_groups', 'a:1:{i:0;s:1:"2";}', 'auth', 0),
(17, 'min_pasword_length', '0', 'auth', 0),
(18, 'deny_recoverpasswd', '0', 'auth', 0),
(19, 'online_timeleft', '300', 'auth', 0),
(20, 'bind_to_site', '0', 'auth', 0),
(21, 'user_component_id', '1', 'auth', 0),
(22, 'registration_confirm', '1', 'auth', 0),
(23, 'registration_premoderation', '0', 'auth', 0),
(24, 'registration_notify_admin', '0', 'auth', 0),
(25, 'autoauthorize', '1', 'auth', 0),
(26, 'admin_notify_email', '', 'auth', 0),
(27, 'twitter_enabled', '0', 'auth', 0),
(28, 'twitter_app_id', '', 'auth', 0),
(29, 'twitter_app_key', '', 'auth', 0),
(30, 'twitter_map', 'a:2:{i:1;a:2:{s:14:"external_field";s:11:"screen_name";s:10:"user_field";s:4:"name";}i:2;a:2:{s:14:"external_field";s:17:"profile_image_url";s:10:"user_field";s:6:"avatar";}}', 'auth', 0),
(31, 'twitter_group', 'a:1:{i:0;s:1:"3";}', 'auth', 0),
(32, 'twitter_addaction', '/* Доступные переменные: $fx_core, $user, $response */\r\nif ( $fx_core->AUTHORIZE_BY == ''login'' ) {\r\n  if ( !$user[''login''] ) {\r\n    $maybe_login = $response[''screen_name''];\r\n    if ( $fx_core->user->get(''login'', $maybe_login )) {\r\n      $maybe_login .= $response[''id''];\r\n    }\r\n    $user->set(''login'', $maybe_login)->save();\r\n  }\r\n}\r\n\r\n', 'auth', 0),
(33, 'facebook_enabled', '0', 'auth', 0),
(34, 'facebook_app_id', '', 'auth', 0),
(35, 'facebook_app_key', '', 'auth', 0),
(36, 'facebook_addaction', '/* Доступные переменные: $fx_core, $user, $response */\r\nif ( $fx_core->AUTHORIZE_BY == ''login'' ) {\r\n  if ( !$user[''login''] ) {\r\n    $maybe_login = $response[''name''];\r\n    if ( $fx_core->user->get(''login'', $maybe_login )) {\r\n      $maybe_login .= $response[''id''];\r\n    }\r\n    $user->set(''login'', $maybe_login)->save();\r\n  }\r\n}\r\n', 'auth', 0),
(37, 'facebook_map', 'a:3:{i:1;a:2:{s:14:"external_field";s:4:"name";s:10:"user_field";s:4:"name";}i:2;a:2:{s:14:"external_field";s:5:"email";s:10:"user_field";s:5:"email";}i:21;a:2:{s:14:"external_field";s:6:"avatar";s:10:"user_field";s:6:"avatar";}}', 'auth', 0),
(38, 'facebook_group', 'a:1:{i:0;s:1:"3";}', 'auth', 0),
(39, 'pm_component_id', '11', 'auth', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_site`
--

CREATE TABLE IF NOT EXISTS `fx_site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `domain` varchar(128) NOT NULL,
  `layout_id` int(11) NOT NULL DEFAULT '0',
  `color` int(11) NOT NULL DEFAULT '0' COMMENT 'Расцветка',
  `mirrors` text NOT NULL,
  `priority` int(11) DEFAULT NULL,
  `checked` smallint(6) NOT NULL DEFAULT '0',
  `title_sub_id` int(11) NOT NULL DEFAULT '0',
  `e404_sub_id` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `robots` text,
  `disallow_indexing` int(11) DEFAULT '0',
  `type` enum('useful','mobile') NOT NULL DEFAULT 'useful' COMMENT 'Тип сайта: обычный или мобильный',
  `language` varchar(255) NOT NULL DEFAULT 'en',
  `offline_text` varchar(255) DEFAULT NULL,
  `store_id` text,
  PRIMARY KEY (`id`),
  KEY `Checked` (`checked`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `fx_site`
--

INSERT INTO `fx_site` (`id`, `parent_id`, `name`, `domain`, `layout_id`, `color`, `mirrors`, `priority`, `checked`, `title_sub_id`, `e404_sub_id`, `created`, `last_updated`, `robots`, `disallow_indexing`, `type`, `language`, `offline_text`, `store_id`) VALUES
(1, 0, 'FloxiShop', 'floxim', 1, 2, '', 0, 1, 2, 3, '2012-05-24 12:42:50', '2013-01-04 21:19:55', '# Floxim Robots file\r\nUser-agent: *\r\nDisallow: /install/', 0, 'useful', 'ru', '<table width=''100%'' height=''100%'' border=''0'' cellpadding=''0'' cellspacing=''0''><tr><td align=''center''>Сайт временно (!) недоступен.</td></tr></table>', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_subdivision`
--

CREATE TABLE IF NOT EXISTS `fx_subdivision` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `external_url` varchar(255) DEFAULT NULL,
  `keyword` varchar(255) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hidden_url` varchar(255) NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` smallint(1) NOT NULL DEFAULT '0',
  `disallow_indexing` int(11) DEFAULT '-1',
  `seo_description` text,
  `seo_keywords` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_h1` varchar(255) DEFAULT NULL,
  `own_design` tinyint(1) NOT NULL DEFAULT '0',
  `force_menu` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Показывать принудительно подразделы',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Hidden_URL` (`site_id`,`parent_id`,`keyword`),
  KEY `site_ID` (`site_id`),
  KEY `Parent_Sub_ID` (`parent_id`),
  KEY `Checked_2` (`checked`),
  KEY `Subdivision_ID` (`id`,`hidden_url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=76 ;

--
-- Дамп данных таблицы `fx_subdivision`
--

INSERT INTO `fx_subdivision` (`id`, `site_id`, `parent_id`, `name`, `template_id`, `external_url`, `keyword`, `last_updated`, `created`, `hidden_url`, `priority`, `checked`, `disallow_indexing`, `seo_description`, `seo_keywords`, `seo_title`, `seo_h1`, `own_design`, `force_menu`) VALUES
(1, 1, 0, 'Товары', 0, NULL, 'goods', '2012-08-29 11:01:53', '2012-05-24 12:42:50', '/goods/', 1, 1, -1, NULL, NULL, NULL, NULL, 0, 1),
(2, 1, 0, 'Титульная страница', NULL, NULL, 'index', '2012-08-29 11:04:04', '2012-05-24 12:42:50', '/index/', 0, 0, -1, NULL, NULL, 'Мой крутой сайт', NULL, 0, 1),
(3, 1, 0, 'Страница не найдена', NULL, NULL, '404', '2012-08-29 11:02:27', '2012-05-24 12:42:50', '/404/', 0, 0, -1, NULL, NULL, NULL, NULL, 0, 1),
(71, 1, 6, 'Вакансии', 0, NULL, 'vacancy', '2012-12-11 08:39:02', '2012-08-29 15:00:48', '/about/vacancy/', 1, 1, -1, NULL, NULL, NULL, 'Вакансии!', 0, 1),
(5, 1, 0, 'Услуги', 0, NULL, 'service', '2012-08-31 11:26:10', '2012-05-24 12:42:50', '/service/', 3, 1, -1, NULL, NULL, NULL, NULL, 0, 1),
(6, 1, 0, 'О компании', 0, NULL, 'about', '2012-11-19 11:22:21', '2012-05-24 12:42:50', '/about/', 5, 1, -1, NULL, NULL, NULL, NULL, 0, 1),
(8, 1, 0, 'Кабинет', 0, NULL, 'profile', '2012-08-29 11:04:34', '2012-05-24 12:42:50', '/profile/', -1, 0, -1, NULL, NULL, NULL, 'Кабинет пользователя', 0, 1),
(10, 1, 8, 'Регистрация', NULL, NULL, 'registration', '2012-04-19 08:20:58', '2012-05-24 12:42:50', '/profile/registration/', 0, 1, -1, NULL, NULL, NULL, NULL, 0, 1),
(73, 1, 1, 'Утюги', 0, NULL, 'irons', '2012-08-30 11:17:14', '2012-08-29 15:01:34', '/goods/irons/', 0, 1, -1, NULL, NULL, NULL, NULL, 0, 1),
(15, 1, 8, 'Смена пароля', NULL, NULL, 'passwd', '2012-04-19 08:20:58', '2012-05-24 12:42:50', '/profile/passwd/', 3, 1, -1, NULL, NULL, NULL, NULL, 0, 1),
(16, 1, 8, 'Восстановление пароля', NULL, NULL, 'recoverpasswd', '2012-04-19 08:20:58', '2012-05-24 12:42:50', '/profile/recoverpasswd/', 4, 1, -1, NULL, NULL, NULL, NULL, 0, 1),
(17, 1, 8, 'Личные сообщения', NULL, NULL, 'pm', '2012-04-19 08:20:58', '2012-05-24 12:42:50', '/profile/pm/', 5, 1, -1, NULL, NULL, NULL, NULL, 0, 1),
(23, 1, 0, 'Контакты', 0, NULL, 'contacts', '2012-08-31 11:33:02', '2012-05-28 12:27:15', '/contacts/', 6, 1, -1, NULL, NULL, NULL, 'Адрес и контакты', 0, 1),
(74, 1, 1, 'Холодильники', 0, NULL, 'fridges', '2012-08-29 11:01:51', '2012-08-29 15:01:51', '/goods/fridges/', 1, 1, -1, NULL, NULL, NULL, NULL, 0, 1),
(72, 1, 6, 'Менеджеры', 0, NULL, 'managers', '2012-08-29 11:01:06', '2012-08-29 15:01:06', '/about/managers/', 2, 1, -1, NULL, NULL, NULL, NULL, 0, 1),
(70, 1, 6, 'Новости', 0, NULL, 'news', '2012-11-12 13:31:46', '2012-08-29 15:00:32', '/about/news/', 0, 1, -1, NULL, NULL, NULL, 'Новости скачать бесплатно', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_template`
--

CREATE TABLE IF NOT EXISTS `fx_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `device` enum('display','mobile') NOT NULL DEFAULT 'display',
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `files` text NOT NULL,
  `colors` text NOT NULL COMMENT 'Расцветки',
  `store_id` text,
  PRIMARY KEY (`id`),
  KEY `Keyword` (`keyword`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=38 ;

--
-- Дамп данных таблицы `fx_template`
--

INSERT INTO `fx_template` (`id`, `parent_id`, `name`, `keyword`, `type`, `device`, `default`, `files`, `colors`, `store_id`) VALUES
(1, 0, 'Основной', 'demo1', 'parent', 'display', 0, 'a:2:{i:0;a:3:{s:4:"type";s:3:"css";s:7:"checked";i:1;s:4:"file";s:12:"css/main.css";}i:1;a:3:{s:4:"type";s:3:"css";s:7:"checked";i:1;s:4:"file";s:13:"css/color.css";}}', '', NULL),
(2, 1, 'Титульная страница', 'index', 'index', 'display', 0, '', '', NULL),
(3, 1, 'Внутренняя страница', 'inner', 'inner', 'display', 0, '', '', NULL),
(4, 0, 'Макет 2', 'demo2', 'parent', 'display', 0, 'a:2:{i:0;a:3:{s:4:"type";s:3:"css";s:7:"checked";i:1;s:4:"file";s:12:"css/main.css";}i:1;a:3:{s:4:"type";s:3:"css";s:7:"checked";i:1;s:4:"file";s:13:"css/color.css";}}', '', NULL),
(5, 4, 'Титульная страница', 'index', 'index', 'display', 0, '', '', NULL),
(6, 4, 'Внутренняя страница', 'inner', 'inner', 'display', 0, '', '', NULL),
(7, 0, 'Макет 3', 'demo3', 'parent', 'display', 0, 'a:2:{i:0;a:3:{s:4:"type";s:3:"css";s:7:"checked";i:1;s:4:"file";s:12:"css/main.css";}i:1;a:3:{s:4:"type";s:3:"css";s:7:"checked";i:1;s:4:"file";s:13:"css/color.css";}}', '', NULL),
(8, 7, 'Титульная страница', 'index', 'index', 'display', 0, '', '', NULL),
(9, 7, 'Внутренняя страница', 'inner', 'inner', 'display', 0, '', '', NULL),
(10, 0, 'С расцветками', 'demo4', 'parent', 'display', 0, 'a:2:{i:0;a:3:{s:4:"type";s:3:"css";s:7:"checked";i:1;s:4:"file";s:12:"css/main.css";}i:1;a:3:{s:4:"type";s:3:"css";s:7:"checked";i:1;s:4:"file";s:13:"css/color.css";}}', 'a:3:{i:1;a:5:{s:4:"name";s:13:"yellow-purple";s:4:"file";s:21:"css/yellow-purple.css";s:5:"color";s:6:"yellow";s:9:"color_alt";s:6:"purple";s:7:"default";N;}i:2;a:5:{s:4:"name";s:9:"all-black";s:4:"file";s:17:"css/all-black.css";s:5:"color";s:5:"black";s:9:"color_alt";s:5:"black";s:7:"default";N;}i:3;a:5:{s:4:"name";s:10:"green-blue";s:4:"file";s:18:"css/green-blue.css";s:5:"color";s:5:"green";s:9:"color_alt";s:4:"blue";s:7:"default";s:1:"1";}}', NULL),
(11, 10, 'Титульная страница', 'index', 'index', 'display', 0, '', '', NULL),
(12, 10, 'Внутренняя страница', 'inner', 'inner', 'display', 1, '', '', NULL),
(13, 0, 'Макет 5', 'demo5', 'parent', 'display', 0, 'a:2:{i:2;a:3:{s:4:"type";s:3:"css";s:7:"checked";i:1;s:4:"file";s:12:"css/main.css";}i:3;a:3:{s:4:"type";s:3:"css";s:7:"checked";i:1;s:4:"file";s:13:"css/color.css";}}', '', NULL),
(14, 13, 'Титульная страница', 'index', 'index', 'display', 0, '', '', NULL),
(15, 13, 'Внутренняя страница', 'inner', 'inner', 'display', 0, '', '', NULL),
(16, 0, 'Макет 6', 'demo6', 'parent', 'display', 0, 'a:2:{i:0;a:3:{s:4:"type";s:3:"css";s:7:"checked";i:1;s:4:"file";s:12:"css/main.css";}i:1;a:3:{s:4:"type";s:3:"css";s:7:"checked";i:1;s:4:"file";s:13:"css/color.css";}}', '', NULL),
(17, 16, 'Титульная страница', 'index', 'index', 'display', 0, '', '', NULL),
(18, 16, 'Внутренняя страница', 'inner', 'inner', 'display', 0, '', '', NULL),
(19, 16, 'Внутренняя страница (текст)', 'innertext', 'inner', 'display', 0, '', '', NULL),
(20, 0, 'Макет 7', 'demo7', 'parent', 'display', 0, 'a:2:{i:0;a:3:{s:4:"type";s:3:"css";s:7:"checked";i:1;s:4:"file";s:12:"css/main.css";}i:1;a:3:{s:4:"type";s:3:"css";s:7:"checked";i:1;s:4:"file";s:13:"css/color.css";}}', '', NULL),
(21, 20, 'Титульная страница', 'index', 'index', 'display', 0, '', '', NULL),
(22, 20, 'Внутренняя страница', 'inner', 'inner', 'display', 0, '', '', NULL),
(23, 0, 'Макет 8', 'demo8', 'parent', 'display', 0, 'a:2:{i:0;a:3:{s:4:"type";s:3:"css";s:7:"checked";i:1;s:4:"file";s:12:"css/main.css";}i:1;a:3:{s:4:"type";s:3:"css";s:7:"checked";i:1;s:4:"file";s:13:"css/color.css";}}', '', NULL),
(24, 23, 'Титульная страница', 'index', 'index', 'display', 0, '', '', NULL),
(25, 23, 'Внутренняя страница', 'inner', 'inner', 'display', 0, '', '', NULL),
(26, 0, 'Универсальный макет', 'universal', 'parent', 'display', 0, 'a:1:{i:0;a:3:{s:4:"type";s:3:"css";s:7:"checked";i:1;s:4:"file";s:12:"css/main.css";}}', '', 'design.universal'),
(27, 26, 'Титульная страница', 'index', 'index', 'display', 0, '', '', NULL),
(28, 26, 'Внутренняя страница', 'inner', 'inner', 'display', 0, '', '', NULL),
(32, 0, 'BestBed', 'bestbed', 'parent', 'display', 0, '', '', NULL),
(33, 32, 'Титульная страница', 'index', 'index', 'display', 0, '', '', NULL),
(34, 32, 'Внутренняя страница', 'inner', 'inner', 'display', 0, '', '', NULL),
(35, 0, 'SuperNova', 'supernova', 'parent', 'display', 0, '', '', NULL),
(36, 35, 'Титульная страница', 'index', 'index', 'display', 0, '', '', NULL),
(37, 35, 'Внутренняя страница', 'inner', 'inner', 'display', 0, '', '', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_user_group`
--

CREATE TABLE IF NOT EXISTS `fx_user_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `User_ID` (`user_id`),
  KEY `PermissionGroup_ID` (`group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `fx_user_group`
--

INSERT INTO `fx_user_group` (`id`, `user_id`, `group_id`) VALUES
(1, 1, 1),
(6, 2, 2),
(7, 3, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_widget`
--

CREATE TABLE IF NOT EXISTS `fx_widget` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `description` text,
  `group` text,
  `checked` tinyint(1) NOT NULL DEFAULT '1',
  `icon` varchar(255) NOT NULL,
  `embed` enum('miniblock','narrow','wide','narrow-wide') NOT NULL DEFAULT 'narrow-wide',
  `store_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `fx_widget`
--

INSERT INTO `fx_widget` (`id`, `name`, `keyword`, `description`, `group`, `checked`, `icon`, `embed`, `store_id`) VALUES
(1, 'Блок авторизации', 'authform', NULL, 'Личный кабинет', 1, 'auth', 'miniblock', 'widget.auth'),
(2, 'Форма восстановления пароля', 'recoverpasswd', NULL, 'Личный кабинет', 1, 'auth', 'narrow-wide', 'widget.recoverpasswd'),
(3, 'Яндекс: Погода', 'yandexpogoda', 'Виджет показывает погоду в любом городе.', 'Яндекс', 1, '', 'miniblock', 'widget.yandexpogoda'),
(4, 'Яндекс: Пробки', 'yandexprobki', 'Виджет показывает загруженность дорог в любом городе.', 'Яндекс', 1, '', 'miniblock', 'widget.yandexprobki'),
(5, 'Google: YouTube', 'youtube', 'Вставка видеороликов с YouTube', 'Google', 1, '', 'narrow-wide', 'widget.youtube'),
(6, 'Sape', 'sape', NULL, 'Яндекс', 1, '', 'narrow-wide', ''),
(7, 'Menu Test', 'menutest', NULL, 'System', 1, '', 'narrow-wide', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

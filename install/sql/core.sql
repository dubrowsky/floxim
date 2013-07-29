-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Июл 22 2013 г., 17:36
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
  KEY `Related_ID` (`related_id`),
  KEY `User_ID` (`user_id`)
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=200 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=36 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=32 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=34 AUTO_INCREMENT=1 ;

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
  `parent_id` int(11) DEFAULT NULL,
  `item_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Class_Group` (`group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=100 AUTO_INCREMENT=49 ;

--
-- Дамп данных таблицы `fx_component`
--

INSERT INTO `fx_component` (`id`, `keyword`, `name`, `description`, `group`, `icon`, `store_id`, `parent_id`, `item_name`) VALUES
(1, 'user', 'Users', '', 'Users', '', 'component.user', 0, 'User'),
(19, 'text', 'Text', '', 'Basic', '', 'component.text', 36, 'text'),
(23, 'page', 'Pages', '', 'Basic', '', NULL, 36, 'page'),
(24, 'section', 'Navigation', '', 'Basic', '', NULL, 23, 'Section'),
(32, 'tagpost', 'Tags for entity', '', 'Blog', '', NULL, 36, 'Tag to entity link'),
(31, 'tag', 'Tags', '', 'Blog', '', NULL, 23, 'Tag'),
(30, 'blogpost', 'Blog', '', 'Blog', '', NULL, 23, 'Blog post'),
(36, 'content', 'Content', '', 'Basic', '', NULL, 0, 'Content'),
(46, 'travel_route', 'Tours', '', 'Travel', '', NULL, 23, 'Tour'),
(47, 'gallery', 'Image galleries', '', 'Gallery', '', NULL, 23, 'Gallery'),
(48, 'photo', 'Image', '', 'Gallery', '', NULL, 36, 'image');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content`
--

CREATE TABLE IF NOT EXISTS `fx_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `priority` int(11) NOT NULL DEFAULT '0',
  `checked` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `type` varchar(45) NOT NULL,
  `infoblock_id` int(11) NOT NULL,
  `site_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=47 AUTO_INCREMENT=2055 ;

--
-- Дамп данных таблицы `fx_content`
--

INSERT INTO `fx_content` (`id`, `priority`, `checked`, `created`, `last_updated`, `user_id`, `type`, `infoblock_id`, `site_id`, `parent_id`) VALUES
(2, 0, 0, '2012-05-24 12:42:50', '2013-04-29 12:01:11', 0, 'section', 0, 1, 0),
(3, 7, 0, '2012-05-24 12:42:50', '2013-04-29 12:01:11', 0, 'section', 0, 1, 2),
(16, 5, 1, '2012-05-28 12:27:15', '2013-05-17 21:14:38', 0, 'section', 3, 1, 2),
(2047, 0, 1, '2013-07-19 20:08:42', '2013-07-19 16:08:42', 99, 'text', 74, 15, 2033),
(99, 0, 1, '0000-00-00 00:00:00', '2013-04-29 12:01:11', 0, 'user', 0, 1, 0),
(112, 0, 1, '2013-04-24 14:12:36', '2013-04-29 12:01:11', 99, 'text', 16, 1, 2),
(1889, 1, 1, '2013-06-10 02:26:56', '2013-07-14 03:39:35', 99, 'section', 69, 15, 1883),
(2024, 0, 1, '2013-07-12 16:10:51', '2013-07-12 08:10:51', 99, 'photo', 81, 15, 1912),
(1887, 3, 1, '2013-06-10 02:17:40', '2013-07-14 03:19:45', 99, 'section', 69, 15, 1883),
(1884, 0, 1, '2013-06-08 17:03:02', '2013-07-07 19:31:53', 99, 'page', 0, 15, 1883),
(1883, 0, 1, '2013-06-08 17:03:02', '2013-06-08 09:03:02', 99, 'page', 0, 15, NULL),
(1890, 2, 1, '2013-06-10 02:27:12', '2013-07-14 03:39:35', 99, 'section', 69, 15, 1883),
(1891, 1, 1, '2013-06-10 11:38:10', '2013-06-24 04:19:49', 99, 'travel_route', 70, 15, 1883),
(1892, 2, 1, '2013-06-10 12:17:59', '2013-06-24 04:19:49', 99, 'travel_route', 70, 15, 1883),
(1897, 0, 1, '2013-06-11 13:11:24', '2013-06-11 05:11:24', 99, 'text', 75, 15, 1883),
(1898, 2, 1, '2013-06-11 13:15:18', '2013-06-12 18:22:37', 99, 'section', 76, 15, 1883),
(1899, 1, 1, '2013-06-11 13:16:36', '2013-06-12 18:22:37', 99, 'section', 76, 15, 1883),
(1900, 3, 1, '2013-06-11 13:17:27', '2013-06-11 05:17:56', 99, 'section', 76, 15, 1883),
(1901, 4, 1, '2013-06-11 13:17:47', '2013-06-11 05:17:53', 99, 'section', 76, 15, 1883),
(1902, 0, 1, '2013-06-13 01:24:02', '2013-06-12 17:24:02', 99, 'section', 77, 15, 1887),
(1903, 0, 1, '2013-06-13 01:24:43', '2013-06-12 17:24:43', 99, 'text', 74, 15, 1887),
(1910, 0, 1, '2013-06-13 04:55:44', '2013-06-12 20:55:44', 99, 'text', 74, 15, 1902),
(1912, 1, 1, '2013-06-13 05:29:37', '2013-06-12 21:58:48', 99, 'gallery', 79, 15, 1889),
(2023, 0, 1, '2013-07-12 15:47:42', '2013-07-12 07:47:42', 99, 'photo', 81, 15, 1917),
(1914, 6, 1, '2013-06-13 05:32:07', '2013-06-12 21:58:48', 99, 'gallery', 79, 15, 1889),
(2021, 0, 1, '2013-07-12 15:03:23', '2013-07-12 07:03:23', 99, 'photo', 81, 15, 1912),
(1916, 4, 1, '2013-06-13 05:33:58', '2013-06-12 21:58:48', 99, 'gallery', 79, 15, 1889),
(1917, 3, 1, '2013-06-13 05:34:31', '2013-06-12 21:58:48', 99, 'gallery', 79, 15, 1889),
(2022, 0, 1, '2013-07-12 15:44:26', '2013-07-12 07:44:26', 99, 'photo', 81, 15, 1914),
(1919, 2, 1, '2013-06-17 04:08:53', '2013-06-16 21:00:18', 99, 'photo', 81, 15, 1916),
(1920, 4, 1, '2013-06-17 04:24:24', '2013-06-16 22:05:47', 99, 'photo', 81, 15, 1916),
(1921, 1, 1, '2013-06-17 04:24:51', '2013-06-16 21:00:18', 99, 'photo', 81, 15, 1916),
(1922, 3, 1, '2013-06-17 04:25:40', '2013-06-16 22:05:47', 99, 'photo', 81, 15, 1916),
(1923, 5, 1, '2013-06-17 04:26:42', '2013-06-16 21:00:06', 99, 'photo', 81, 15, 1916),
(1924, 0, 1, '2013-06-17 06:24:59', '2013-06-16 22:24:59', 99, 'text', 74, 15, 1916),
(1925, 4, 1, '2013-06-17 06:30:30', '2013-07-14 03:19:38', 99, 'section', 69, 15, 1883),
(2029, 0, 1, '2013-07-13 17:11:06', '2013-07-13 09:11:06', 99, 'tagpost', 84, 15, 1976),
(1976, 1, 1, '2013-06-21 14:18:44', '2013-06-24 03:13:17', 99, 'blogpost', 82, 15, 1925),
(1933, 4, 1, '2013-06-18 16:02:50', '2013-07-01 06:18:13', 99, 'tag', 83, 15, 1925),
(1968, 3, 1, '2013-06-21 13:45:59', '2013-06-24 03:13:17', 99, 'blogpost', 82, 15, 1925),
(1996, 0, 1, '2013-07-01 16:35:39', '2013-07-01 08:35:39', 99, 'blogpost', 82, 15, 1925),
(2028, 0, 1, '2013-07-13 17:11:06', '2013-07-13 09:11:06', 99, 'tag', 83, 15, 1925),
(2034, 2, 1, '2013-07-13 17:14:40', '2013-07-19 20:49:31', 99, 'tagpost', 84, 15, 1968),
(2049, 0, 1, '2013-07-19 21:09:09', '2013-07-19 17:09:09', 99, 'tagpost', 84, 15, 1996),
(2033, 0, 1, '2013-07-13 17:14:40', '2013-07-13 09:14:40', 99, 'tag', 83, 15, 1925),
(2032, 1, 1, '2013-07-13 17:14:40', '2013-07-19 20:49:31', 99, 'tagpost', 84, 15, 1968),
(2006, 0, 1, '2013-07-08 03:40:33', '2013-07-07 19:40:33', 99, 'text', 74, 15, 1884),
(2025, 0, 1, '2013-07-12 16:13:25', '2013-07-12 08:13:26', 99, 'photo', 81, 15, 1912),
(2026, 0, 1, '2013-07-12 16:18:28', '2013-07-12 08:18:28', 99, 'photo', 81, 15, 1917),
(2027, 0, 1, '2013-07-13 15:39:32', '2013-07-13 07:39:32', 99, 'text', 74, 15, 1890),
(2039, 0, 1, '2013-07-14 11:20:37', '2013-07-14 03:20:37', 99, 'tag', 83, 15, 1925),
(2040, 0, 1, '2013-07-14 11:20:37', '2013-07-14 03:20:37', 99, 'tagpost', 84, 15, 1996);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_blogpost`
--

CREATE TABLE IF NOT EXISTS `fx_content_blogpost` (
  `id` int(11) NOT NULL,
  `anounce` text,
  `text` text,
  `publish_date` datetime DEFAULT NULL,
  `metatype` varchar(255) DEFAULT NULL,
  `image` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=2824;

--
-- Дамп данных таблицы `fx_content_blogpost`
--

INSERT INTO `fx_content_blogpost` (`id`, `anounce`, `text`, `publish_date`, `metatype`, `image`) VALUES
(1968, '<p class="">\r\n	       Everyone shoud know!\r\n</p>', '<p>\r\n	      They told us this was not logistically possible and a hugely inconsiderate thing to do to a grief stricken family.\r\n</p>\r\n<p>\r\n	      I called them a bunch of a-holes and soccer-kicked the watermelon off the picnic table and into the bushes.\r\n</p>', '2013-06-11 00:00:00', 'article', 383),
(1976, '<p>\n	 Jeep Travel is here to make you laugh!</p><p>\n	 <img src="/floxim_files/content/1251362341_1243855523_vnedorojnik_21_1.jpg" style="width: 584px;"></p>', '<p>\r\n	 Put your hands up in the air!\r\n</p>', '2012-04-19 00:00:00', 'post', NULL),
(1996, '<p>\r\n	      Dominicana Republic''s Agro-tourism with a side of beach\r\n</p>', '<p>\n	       On the weekend of Jan 19th, 2013, we left the farm early one morning for Samana via a direct <em>gua-gua</em>.  It left in rising sun at 7 am from nearby Saboneta and arrived there at 10:30 am.</p><p>\n	    It was a very easy trip with one stop on an uncomfortable and overcrowded bus which was exacerbated by the holiday weekend.  Fortunately it was cheap too, only 300 pesos ($7.50 USD).</p><p>\n	    From there we met some friends and went onto Las Galeras which we am sure is one of the more beautiful places on this island.  Very Caribbean with its palm ladden beaches, forested hillsides in the distance, and white sands with colors of the sea you can hardly imagine.</p>', '2013-07-10 00:00:00', '', 379);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_gallery`
--

CREATE TABLE IF NOT EXISTS `fx_content_gallery` (
  `id` int(11) NOT NULL,
  `publish_date` datetime DEFAULT NULL,
  `cover` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_gallery`
--

INSERT INTO `fx_content_gallery` (`id`, `publish_date`, `cover`) VALUES
(1912, '2013-05-10 00:00:00', 366),
(1914, '2012-09-12 00:00:00', 368),
(1916, '2012-10-08 00:00:00', 346),
(1917, '2011-11-08 00:00:00', 371);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_page`
--

CREATE TABLE IF NOT EXISTS `fx_content_page` (
  `id` int(11) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `url` (`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=62;

--
-- Дамп данных таблицы `fx_content_page`
--

INSERT INTO `fx_content_page` (`id`, `url`, `name`, `title`) VALUES
(2, '/', 'Main page', ''),
(3, '/404/', '404', NULL),
(16, '/contacts/', 'Contacts', NULL),
(1889, '/portfolio/', 'Portfolio', NULL),
(1890, '/contacts/', 'Contacts', NULL),
(1887, '/about', 'About', NULL),
(1891, '/summer-rally', 'Mangyshlak. The Great Step', ''),
(1883, '/', 'Jeep Travels', 'Jeep Travels: super travels!'),
(1884, '/404', 'Page not found', NULL),
(1892, '/paris-dakar', 'Argentina – Chile', NULL),
(1902, '/test', 'Take part in our events', NULL),
(1898, 'http://facebook.com/', 'Facebook', NULL),
(1899, 'http://plus.google.com/', 'Google+', NULL),
(1900, 'http://instagram.com', 'Instagram', NULL),
(1901, 'http://youtube.com', 'YouTube', NULL),
(1912, '/Kiev', 'Kiev', ''),
(1914, '/Dominikanskaya-respublika', 'Dominicana', ''),
(2039, '/dominikana-2', 'dominikana', NULL),
(1916, '/gaityanski-holyday', 'Haiti', ''),
(1917, '/Pereslavl-Zalesskiy', 'Namibia', ''),
(1925, '/Blog', 'Our blog', NULL),
(2028, '/lulz', '#lulz', NULL),
(2033, '/funeral-2', 'funeral', NULL),
(1933, '/tag-ivan-kurochkin', 'John Kurochkin', 'News about John'),
(1968, '/funeral', 'Our funeral instructions', ''),
(1976, '/hi', 'Hello world!', ''),
(1996, '/dominikana', 'Dominicana, yeah!', '');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_photo`
--

CREATE TABLE IF NOT EXISTS `fx_content_photo` (
  `id` int(11) NOT NULL,
  `photo` int(11) DEFAULT NULL,
  `description` text,
  `copy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_photo`
--

INSERT INTO `fx_content_photo` (`id`, `photo`, `description`, `copy`) VALUES
(1919, 105, 'Haitian people.', 'Dmitry Medvedev'),
(1920, 108, 'Airview', NULL),
(1921, 108, 'Haiti', ''),
(2026, 374, 'Beautiful evening', ''),
(1922, 113, 'Our new friends', 'Lost squirrel'),
(2024, 372, 'Oh no!', ''),
(2025, 373, 'Ukranian steppe', ''),
(1923, 116, 'Haitian Pussy Riot', NULL),
(2021, 361, 'Kiev', ''),
(2022, 367, '', ''),
(2023, 369, 'Swakopmund', '');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_section`
--

CREATE TABLE IF NOT EXISTS `fx_content_section` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=7;

--
-- Дамп данных таблицы `fx_content_section`
--

INSERT INTO `fx_content_section` (`id`) VALUES
(2),
(3),
(16),
(1887),
(1889),
(1890),
(1898),
(1899),
(1900),
(1901),
(1902),
(1925);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_tag`
--

CREATE TABLE IF NOT EXISTS `fx_content_tag` (
  `id` int(11) NOT NULL,
  `counter` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=9;

--
-- Дамп данных таблицы `fx_content_tag`
--

INSERT INTO `fx_content_tag` (`id`, `counter`) VALUES
(1933, 1),
(2028, 2),
(2033, 1),
(2039, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_tagpost`
--

CREATE TABLE IF NOT EXISTS `fx_content_tagpost` (
  `id` int(11) NOT NULL,
  `tag_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=13;

--
-- Дамп данных таблицы `fx_content_tagpost`
--

INSERT INTO `fx_content_tagpost` (`id`, `tag_id`, `post_id`, `comment`) VALUES
(2029, 2028, 1976, NULL),
(2049, 1933, 1996, ''),
(2032, 2028, 1968, 'are u happy?'),
(2034, 2033, 1968, 'noooooooooooooo'),
(2040, 2039, 1996, '');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_text`
--

CREATE TABLE IF NOT EXISTS `fx_content_text` (
  `id` int(11) NOT NULL,
  `text` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=1199;

--
-- Дамп данных таблицы `fx_content_text`
--

INSERT INTO `fx_content_text` (`id`, `text`) VALUES
(112, '<p>Put your content here!​</p>'),
(1897, '<p>\n	<strong>Welcome!</strong></p><p>\n	   If you like a bit of sideways action in your car and don’t mind a bit (or a lot) or mud and water then Rally Driving experiences are an absolute blast and huge amounts of fun.</p>'),
(1903, '<p>\n	The adventure began back in 1977, when Thierry Sabine got lost on his motorbike in the Libyan desert during the Abidjan-Nice Rally. Saved from the sands in extremis, he returned to France still in thrall to this landscape and promising himself he would share his fascination with as many people as possible. He proceeded to come up with a route starting in Europe, continuing to Algiers and crossing Agadez before eventually finishing at Dakar. The founder coined a motto for his inspiration: "A challenge for those who go. A dream for those who stay behind." Courtesy of his great conviction and that modicum of madness peculiar to all great ideas, the plan quickly became a reality. Since then, the Paris-Dakar, a unique event sparked by the spirit of adventure, open to all riders and carrying a message of friendship between all men, has never failed to challenge, surprise and excite. Over the course of almost thirty years, it has generated innumerable sporting and human stories.</p><p>(c) <a href="http://www.dakar.com">www.dakar.com</a></p>'),
(1910, '<p class="">\n	Everyone can join our team! Please, feel free to contact us for a details.</p><p>\n	<img src="/floxim_files/content/aborigeny-tozhe_0.jpg" style=""></p>'),
(2027, '<p>\r\n	Have questions? Need more information?\r\n</p>\r\n<p>\r\n	Send a starter inquiry to <a href="http://mailto:info@jeeptravel.loc">info@jeeptravel.loc</a>\r\n</p>'),
(1924, '<p></p><p>Port-au-Prince, Haiti. Here we are!&nbsp;</p><p></p>'),
(2006, '<p>\r\n	The page you''ve requested doesn''t exist here ;(\r\n</p>'),
(2047, '<p>\r\n	We are going to post many sad  but interesting posts under the "funeral" tag. Stay idle.\r\n</p>');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_travel_route`
--

CREATE TABLE IF NOT EXISTS `fx_content_travel_route` (
  `id` int(11) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_travel_route`
--

INSERT INTO `fx_content_travel_route` (`id`, `start_date`, `end_date`) VALUES
(1891, '2013-05-14 00:00:00', '2013-05-01 00:00:00'),
(1892, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_user`
--

CREATE TABLE IF NOT EXISTS `fx_content_user` (
  `id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL DEFAULT '0',
  `password` varchar(45) NOT NULL,
  `email` char(255) NOT NULL,
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
  UNIQUE KEY `User_ID` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=104;

--
-- Дамп данных таблицы `fx_content_user`
--

INSERT INTO `fx_content_user` (`id`, `site_id`, `password`, `email`, `login`, `name`, `registration_code`, `avatar`, `forum_signature`, `forum_messages`, `pa_balance`, `auth_hash`, `first_name`) VALUES
(99, 0, '202cb962ac59075b964b07152d234b70', 'dubr.cola@gmail.com', 'admin', 'Adminio', NULL, NULL, NULL, 0, 0, '', NULL);

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=60 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=204 AUTO_INCREMENT=15 ;

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
(11, 'image', 11, 0, 1, 0),
(13, 'link', 13, 1, 1, 0),
(14, 'multilink', 14, 0, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_dictionary`
--

CREATE TABLE IF NOT EXISTS `fx_dictionary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dict_key` varchar(45) DEFAULT NULL,
  `lang_string` text,
  `lang_en` text,
  `lang_ru` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=397 ;

--
-- Дамп данных таблицы `fx_dictionary`
--

INSERT INTO `fx_dictionary` (`id`, `dict_key`, `lang_string`, `lang_en`, `lang_ru`) VALUES
(1, 'component_section', 'Отображает путь до текущей страницы в структуре сайта', 'Show path to the current page', NULL),
(2, 'component_section', 'Хлебные крошки', 'Bread crumbs', NULL),
(3, 'component_section', 'Подраздел', 'Subsection', NULL),
(4, 'component_section', 'Показывать у всех', 'Show for all items', NULL),
(5, 'component_section', 'Показывать у активного', 'Show for the active item', NULL),
(6, 'component_section', 'Не показывать', 'Don''t show', NULL),
(7, 'component_section', 'Подразделы', 'Subsections', NULL),
(8, 'component_section', 'Меню', 'Navigation', NULL),
(9, 'system', 'Не могу произвести запись в файл', 'File is not writable', NULL),
(10, 'controller_component', 'Выводит записи по произвольному фильтру', 'Show entries by filter', NULL),
(11, 'controller_component', 'Выводит список записей из указанного раздела', 'Show entries from the specified section', NULL),
(12, 'controller_component', 'Список', 'List', NULL),
(13, 'controller_component', 'Выводит отдельную запись', 'Show single entry', NULL),
(14, 'controller_component', 'Запись', 'Entry', NULL),
(15, 'controller_component', 'Указать раздел явно', 'From specified section', NULL),
(16, 'controller_component', 'Из любого раздела', 'From all sections', NULL),
(17, 'controller_component', 'Выбрать родителя', 'Choose section', NULL),
(18, 'controller_component', 'Произвольный', 'Random', NULL),
(19, 'controller_component', 'Страница, куда прицеплен инфоблок', 'The infoblock owner section', NULL),
(20, 'controller_component', 'Текущая страница', 'Current page', NULL),
(21, 'controller_component', 'Родитель', 'Parent', NULL),
(22, 'controller_component', 'По возрастанию', 'Ascending', NULL),
(23, 'controller_component', 'По убыванию', 'Descending', NULL),
(24, 'controller_component', 'Порядок', 'Order', NULL),
(25, 'controller_component', 'Сортировка', 'Sorting', NULL),
(26, 'controller_component', 'Ручная', 'Manual', NULL),
(27, 'controller_component', 'Дата создания', 'Created', NULL),
(28, 'controller_component', 'Разбивать на страницы?', 'Show pagination?', NULL),
(29, 'controller_component', 'Сколько выводить', 'How many entries to display', NULL),
(30, 'controller_layout', 'Вход', 'Sign in', NULL),
(31, 'system', 'Добавить инфоблок', 'Add infoblock', NULL),
(32, 'system', 'Ссылка', 'Link', NULL),
(33, 'system', 'Картинка', 'Picture', NULL),
(34, 'system', 'Элементы', 'Elements', NULL),
(35, 'system', 'Классификатор', 'Classifier', NULL),
(36, 'system', 'Вручную', 'Manually', NULL),
(37, 'system', 'Источник', 'Source', NULL),
(38, 'system', 'Показывать как', 'Show like', NULL),
(39, 'system', 'Текущий файл:', 'Current file:', NULL),
(40, 'system', 'заменять перенос строки на br', 'replace newline to br', NULL),
(41, 'system', 'разрешить html-теги', 'allow HTML tags', NULL),
(42, 'system', 'Связанный тип', 'Related type', NULL),
(43, 'system', 'Привязать значение к родителю', 'Bind value to the parent', NULL),
(44, 'system', 'Ключ для свойства', 'Key name for the property', NULL),
(45, 'system', 'Куда ссылается', 'Links to', NULL),
(46, 'system', 'Укажите название сайта', 'Enter the name of the site', NULL),
(47, 'system', 'Приоритет', 'Priority', NULL),
(48, 'system', 'Дата создания', 'Created', NULL),
(49, 'system', 'Такой keyword уже используется компоненте', 'This keyword is used by the component', NULL),
(50, 'system', 'Keyword может содержать только буквы, цифры, символы "дефис" и "подчеркивание"', 'Keyword can only contain letters, numbers, symbols, "hyphen" and "underscore"', NULL),
(51, 'system', 'Укажите keyword компонента', 'Specify component keyword', NULL),
(52, 'system', 'Название компонента не может быть пустым', 'Component name can not be empty', NULL),
(53, 'system', 'Укажите описание поля', 'Specify field description', NULL),
(54, 'system', 'Такое поле уже существует', 'This field already exists', NULL),
(55, 'system', 'Данное поле зарезервировано', 'This field is reserved', NULL),
(56, 'system', 'Имя поля может содержать только латинские буквы, цифры и знак подчеркивания', 'Field name can contain only letters, numbers, and the underscore character', NULL),
(57, 'system', 'name', 'name', NULL),
(58, 'system', 'Укажите название поля', 'Specify field name', NULL),
(59, 'system', 'Такой keyword уже используется в виджете', 'This keyword is used by widget', NULL),
(60, 'system', 'Keyword может сожержать только буквы и цифры', 'Keyword can contain only letters and numbers', NULL),
(61, 'system', 'Укажите keyword виджета', 'Enter the keyword of widget', NULL),
(62, 'system', 'Укажите название виджета', 'Specify the name of the widget', NULL),
(63, 'system', 'Вы собираетесь установить:', 'You are about to install:', NULL),
(64, 'system', 'Превью', 'Preview', NULL),
(65, 'system', 'Макет', 'Layout', NULL),
(66, 'system', 'Показывать, когда сайт выключен', 'Show when the site is off', NULL),
(67, 'system', 'Титульная страница', 'Cover Page', NULL),
(68, 'system', 'Запретить индексирование', 'Prevent indexing', NULL),
(69, 'system', 'Содержимое robots.txt', 'The contents of robots.txt', NULL),
(70, 'system', 'Язык сайта', 'Site language', NULL),
(71, 'system', 'Зеркала', 'Aliases', NULL),
(72, 'system', 'Домен', 'Domain', NULL),
(73, 'system', 'Название сайта', 'Site name', NULL),
(74, 'system', 'Включен', 'Enabled', NULL),
(75, 'system', 'Системные', 'System', NULL),
(76, 'system', 'Основные', 'Main', NULL),
(77, 'system', 'любое', 'any', NULL),
(78, 'system', 'вертикальное', 'vertical', NULL),
(79, 'system', 'Меню', 'Menu', NULL),
(80, 'system', 'Направление', 'Direction', NULL),
(81, 'system', 'Обязательный', 'Required', NULL),
(82, 'system', 'Блок', 'Block', NULL),
(83, 'system', 'Блоки', 'Blocks', NULL),
(84, 'system', 'Сайты', 'Sites', NULL),
(85, 'system', 'Дизайн', 'Design', NULL),
(86, 'system', 'Настройки', 'Settings', NULL),
(87, 'system', 'Карта сайта', 'Site map', NULL),
(88, 'system', 'Сайт не найден', 'Site not found', NULL),
(89, 'system', 'Страница не найдена', 'Page not found', NULL),
(90, 'system', 'Ошибка при создании временного файла', 'Error creating a temporary file', NULL),
(91, 'system', 'Добавление нового сайта', 'Create a new site', NULL),
(92, 'system', 'Новый сайт', 'New site', NULL),
(93, 'system', 'Новый', 'New', NULL),
(94, 'system', 'Экспорт', 'Export', NULL),
(95, 'system', 'для мобильный устройств', 'for mobile devices', NULL),
(96, 'system', 'Язык:', 'Language:', NULL),
(97, 'system', 'Описание', 'Description', NULL),
(98, 'system', 'Группа', 'Group', NULL),
(99, 'system', 'Другая группа', 'Another group', NULL),
(100, 'system', 'Название сущности создаваемой компонентом', 'Name of entity created by the component', NULL),
(101, 'system', 'Название компонента', 'Component name', NULL),
(102, 'system', 'Ключевое слово:', 'Keyword:', NULL),
(103, 'system', '--нет--', '--no--', NULL),
(104, 'system', 'Компонент-родитель', 'Parent component', NULL),
(105, 'system', 'по умолчанию', 'default', NULL),
(106, 'system', 'Компоненты', 'Components', NULL),
(107, 'system', 'Виджеты', 'Widgets', NULL),
(108, 'system', 'Ключевое слово', 'Keyword', NULL),
(109, 'system', 'Файл', 'File', NULL),
(110, 'system', 'Поля', 'Fields', NULL),
(111, 'system', 'установить с FloximStore', 'Install from FloximStore', NULL),
(112, 'system', 'импортировать', 'import', NULL),
(113, 'system', 'Макет внутренней страницы', 'Layout of inside page', NULL),
(114, 'system', 'Макет титульной страницы', 'Cover Page Layout', NULL),
(115, 'system', 'Выход', 'Sign out', NULL),
(116, 'system', 'Применить текущий', 'Apply the current', NULL),
(117, 'system', 'Расцветка', 'Colors', NULL),
(118, 'system', 'Макет не найден', 'Layout not found', NULL),
(119, 'system', 'Укажите название макета', 'Enter the layout name', NULL),
(120, 'system', 'Название макета', 'Layout name', NULL),
(121, 'system', 'Экспортировать в файл', 'Export to file', NULL),
(122, 'system', 'Нет файлов', 'No files', NULL),
(123, 'system', 'Макеты', 'Layouts', NULL),
(124, 'system', 'Не удалось создать каталог', 'Unable to create directory', NULL),
(125, 'system', 'Добавление макета дизайна', 'Adding a layout design', NULL),
(126, 'system', 'Импорт макета дизайна', 'Import layout design', NULL),
(127, 'system', 'пустой', 'empty', NULL),
(128, 'system', 'Используется на сайтах', 'Used on', NULL),
(129, 'system', 'Повторено', 'Repeated', NULL),
(130, 'system', 'Отменено', 'Cancelled', NULL),
(131, 'system', 'Посылаемый заголовок', 'Header sent', NULL),
(132, 'system', 'Новый url', 'New url', NULL),
(133, 'system', 'Старый url', 'Old url', NULL),
(134, 'system', 'Изменение правила переадресации', 'Changing the transfer rule', NULL),
(135, 'system', 'Добавление правила переадресации', 'Adding forwarding rules', NULL),
(136, 'system', 'Заголовок', 'Header', NULL),
(137, 'system', 'Удалять лейауты нельзя!', 'Layouts can not be deleted', NULL),
(138, 'system', 'Отвязать/скрыть', 'Unbind/Hide', NULL),
(139, 'system', 'Удалить', 'Delete', NULL),
(140, 'system', 'Инфоблок содержит контент', 'The infoblock contains some content', NULL),
(141, 'system', ' шт. Что с ним делать?', 'items. What should we do with them?', NULL),
(142, 'system', 'Будет удалено куча всего, я понимаю последствия', 'I am REALLY shure', NULL),
(143, 'system', 'Оформление блока', 'Block wrapper template', NULL),
(144, 'system', 'Шаблон', 'Template', NULL),
(145, 'system', 'Автовыбор', 'Auto select', NULL),
(146, 'system', 'Без оформления', 'With no wrapper', NULL),
(147, 'system', 'На этой и на вложенных', 'On the page and it''s children', NULL),
(148, 'system', 'Только на вложенных страницах', 'Only on children', NULL),
(149, 'system', 'Только на этой странице', 'Only on the page', NULL),
(150, 'system', 'Страница', 'Page', NULL),
(151, 'system', 'На всех страницах', 'On all pages', NULL),
(152, 'system', 'Удалить это правило', 'Remove this rule', NULL),
(153, 'system', 'Создать новое правило', 'Create a new rule', NULL),
(154, 'system', 'Обновить', 'Update', NULL),
(155, 'system', 'Создать', 'Create', NULL),
(156, 'system', 'Выбор шаблона страницы', 'Page layout', NULL),
(157, 'system', 'Настройка инфоблока', 'Infoblock settings', NULL),
(158, 'system', 'Где показывать', 'Where to show', NULL),
(159, 'system', 'Как показывать', 'How to show', NULL),
(160, 'system', 'Название блока', 'Block name', NULL),
(161, 'system', 'Что показывать', 'What to show', NULL),
(162, 'system', 'Виджет', 'Widget', NULL),
(163, 'system', 'Продолжить', 'Next', NULL),
(164, 'system', 'Установить с Store', 'Install from Store', NULL),
(165, 'system', 'Добавление инфоблока', 'Adding infoblock', NULL),
(166, 'system', 'Тип', 'Type', NULL),
(167, 'system', 'Действие', 'Action', NULL),
(168, 'system', 'Название', 'Name', NULL),
(169, 'system', 'Компонент', 'Component', NULL),
(170, 'system', 'Отдельный объект', 'Single entry', NULL),
(171, 'system', 'Mirror', 'Mirror', NULL),
(172, 'system', 'Список', 'List', NULL),
(173, 'system', 'Сменить пароль', 'Change password', NULL),
(174, 'system', 'Импорт', 'Import', NULL),
(175, 'system', 'Скачать с FloximStore', 'Download from FloximStore', NULL),
(176, 'system', 'Cкачать файл', 'Download file', NULL),
(177, 'system', 'Закачать файл', 'Upload file', NULL),
(178, 'system', 'Права', 'Permissions', NULL),
(179, 'system', 'выделить блок', 'Select parent block', NULL),
(180, 'system', 'Сменить макет сайта', 'Site layout', NULL),
(181, 'system', 'Дизайн страницы', 'Page design', NULL),
(182, 'system', 'Разработка', 'Development', NULL),
(183, 'system', 'Администрирование', 'Administration', NULL),
(184, 'system', 'Инструменты', 'Tools', NULL),
(185, 'system', 'Пользователи', 'Users', NULL),
(186, 'system', 'Сайт', 'Site', NULL),
(187, 'system', 'Управление', 'Management', NULL),
(188, 'system', 'Значение по умолчанию', 'Default value', NULL),
(189, 'system', 'Возможен поиск по полю', 'Field can be used for searching', NULL),
(190, 'system', 'Обязательно для заполнения', 'Required', NULL),
(191, 'system', 'Поле не найдено', 'Field not found', NULL),
(192, 'system', 'Поле доступно', 'Field is available for', NULL),
(193, 'system', 'всем', 'anybody', NULL),
(194, 'system', 'только админам', 'admins only', NULL),
(195, 'system', 'никому', 'nobody', NULL),
(196, 'system', 'Тип поля', 'Field type', NULL),
(197, 'system', 'Название на латинице', 'Field keyword', NULL),
(198, 'system', 'Имя', 'Name', NULL),
(199, 'system', 'Новый виджет', 'New widget', NULL),
(200, 'system', 'Размер виджета', 'Widget size', NULL),
(201, 'system', 'Миниблок', 'Mini Block', NULL),
(202, 'system', 'Узкий', 'Narrow', NULL),
(203, 'system', 'Широкий', 'Wide', NULL),
(204, 'system', 'Узко-широкий', 'Narrowly wide', NULL),
(205, 'system', 'Используемая иконка', 'Icon to be used', NULL),
(206, 'system', 'эта иконка используется по умолчанию', 'This icon is used by default', NULL),
(207, 'system', 'эта иконка находится в файле icon.png в директории виджета', 'This icon is icon.png file in the directory widget', NULL),
(208, 'system', 'эта иконка выбрана из списка иконок', 'This icon is selected from a list of icons', NULL),
(209, 'system', 'Введите название виджета', 'Enter the widget name', NULL),
(210, 'system', 'Укажите название', 'Specify the name', NULL),
(211, 'system', 'Изменение группы пользователей', 'Edit User Group', NULL),
(212, 'system', 'Добавление группы пользователей', 'Add User Group', NULL),
(213, 'system', 'Новая группа', 'New Group', NULL),
(214, 'system', 'Присвоить право директора', 'Assign the right director', NULL),
(215, 'system', 'В первой версии есть только право Директор', 'The first version has just the right director', NULL),
(216, 'system', 'Нет никак прав', 'There are no rules', NULL),
(217, 'system', 'Право', 'Permission', NULL),
(218, 'system', 'Редактирование контента', 'Content edit', NULL),
(219, 'system', 'Аватар', 'Avatar', NULL),
(220, 'system', 'Имя на сайте', 'Nick', NULL),
(221, 'system', 'Пароль еще раз', 'Confirm password', NULL),
(222, 'system', 'Пароль', 'Password', NULL),
(223, 'system', 'Логин', 'Login', NULL),
(224, 'system', 'Группы', 'Groups', NULL),
(225, 'system', 'Пароли не совпадают', 'Passwords do not match', NULL),
(226, 'system', 'Пароль не может быть пустым', 'Password can''t be empty', NULL),
(227, 'system', 'Заполните поле с логином', 'Fill in with the login', NULL),
(228, 'system', 'Выберите хотя бы одну группу', 'Please select at least one group', NULL),
(229, 'system', 'Добавление пользователя', 'Add User', NULL),
(230, 'system', 'публикации в', 'publications in', NULL),
(231, 'system', 'Выберите объекты', 'Select objects', NULL),
(232, 'system', 'опубликовал:', 'publish:', NULL),
(233, 'system', 'надо подумать, может ли какой-нибудь модуль, кроме ЛК писать сюда что-нибудь', NULL, NULL),
(234, 'system', 'друзья, отправить сообщение', 'friends, send message', NULL),
(235, 'system', 'зарегистрирован:', 'registred:', NULL),
(236, 'system', 'Активность', 'Activity', NULL),
(237, 'system', 'Регистрационные данные', 'Registration data', NULL),
(238, 'system', 'Управление правами', 'Rights management', NULL),
(239, 'system', 'Пароль и подтверждение не совпадают.', 'Password and verification do not match.', NULL),
(240, 'system', 'Пароль слишком короткий. Минимальная длина пароля', 'Password is too short. The minimum length of the password', NULL),
(241, 'system', 'Введите пароль.', 'Enter the password', NULL),
(242, 'system', 'Такой логин уже используется', 'This login is already in use', NULL),
(243, 'system', 'Ошибка: не найден инфоблок с пользователями.', 'Error: can not find information block with users.', NULL),
(244, 'system', 'Самостоятельная регистрация запрещена.', 'Self-registration is prohibited.', NULL),
(245, 'system', 'Не могу найти <?php в файле класса', 'Can not find <? ​​Php class file', NULL),
(246, 'system', 'Синтаксическая ошибка в классе компонента', 'Syntax error in the component class', NULL),
(247, 'system', 'Синтаксическая ошибка в функции', 'Syntax error in function', NULL),
(248, 'system', 'Профиль', 'Profile', NULL),
(249, 'system', 'Пользователь не найден', 'User not found', NULL),
(250, 'system', 'Список не найден', 'List not found', NULL),
(251, 'system', 'Сайт не найден', 'Site not found', NULL),
(252, 'system', 'Виджет не найден', 'Widget not found', NULL),
(253, 'system', 'Компонент не найден', 'Component not found', NULL),
(254, 'system', 'Модули', 'Modules', NULL),
(255, 'system', 'Список сайтов', 'All sites', NULL),
(256, 'system', 'Не удалось соединиться с сервером', 'Unable to connect to server', NULL),
(257, 'system', 'Переопределите метод settings в своем классе', 'Override the settings in the class', NULL),
(258, 'system', 'Настройка модуля', 'Configuring the', NULL),
(259, 'system', 'Вход', 'Login', NULL),
(260, 'system', 'Сохранено', 'Saved', NULL),
(261, 'system', 'Не получилось открыть файл!', 'Could not open file!', NULL),
(262, 'system', 'Ошибка при закачке файла', 'Error when downloading a file', NULL),
(263, 'system', 'Укажите файл', 'Enter the file', NULL),
(264, 'system', 'Не все поля переданы!', 'Not all fields are transferred!', NULL),
(265, 'system', 'Ошибка при удалении файла', 'Error Deleting File', NULL),
(266, 'system', 'Ошибка при изменении имени', 'Error when changing the name', NULL),
(267, 'system', 'Ошибка при изменении прав доступа', 'Error when permission', NULL),
(268, 'system', 'Задайте права доступа', 'Set permissions', NULL),
(269, 'system', 'Укажите имя', 'Enter the name', NULL),
(270, 'system', 'Правка файла/директории', 'Edit the file/directory', NULL),
(271, 'system', 'Просмотр содержимого', 'View the contents', NULL),
(272, 'system', 'Выполнение', 'Execution', NULL),
(273, 'system', 'Запись', 'Writing', NULL),
(274, 'system', 'Чтение', 'Reading', NULL),
(275, 'system', 'Права для остальных', 'Permissions for the rest', NULL),
(276, 'system', 'Права для группы-владельца', 'Permissions for the group owner', NULL),
(277, 'system', 'Права для пользователя-владельца', 'Permissions for the user owner', NULL),
(278, 'system', 'Не передано имя файла!', 'Do not pass the file name!', NULL),
(279, 'system', 'Ошибка при создании файла/каталога', 'An error occurred while creating the file/directory', NULL),
(280, 'system', 'Не все поля переданы', 'Not all fields are transferred', NULL),
(281, 'system', 'Укажите имя файла/каталога', 'Enter the name of the file/directory', NULL),
(282, 'system', 'Создание нового файла/директории', 'Create a new file/directory', NULL),
(283, 'system', 'Имя файла/каталога', 'Name of file/directory', NULL),
(284, 'system', 'Что создаём', 'What we create', NULL),
(285, 'system', 'каталог', 'directory', NULL),
(286, 'system', 'Не удалась запись в файл', 'Writing to file failed', NULL),
(287, 'system', 'Не удалось прочитать файл!', 'Reading of file failed', NULL),
(288, 'system', 'Гб', 'Gb', NULL),
(289, 'system', 'Мб', 'Mb', NULL),
(290, 'system', 'Кб', 'Kb', NULL),
(291, 'system', 'байт', 'byte', NULL),
(292, 'system', 'родительский каталог', 'Parent directory', NULL),
(293, 'system', 'Размер', 'Size', NULL),
(294, 'system', 'Файл-менеджер', 'File-manager', NULL),
(295, 'system', 'Неверное действие', 'Invalid action', NULL),
(296, 'system', 'Неверный id пользователя', 'Invalid user id', NULL),
(297, 'system', 'Неверный код', 'Invalid code', NULL),
(298, 'system', 'Ваш аккаунт активирован.', 'Your account has been created.', NULL),
(299, 'system', 'Ваш адрес e-mail подтвержден. Дождитесь проверки и активации учетной записи администратором.', 'Your e-mail address is confirmed. Wait for the verification and account activation by the administrator.', NULL),
(300, 'system', 'Неверный код подтверждения регистрации.', 'Invalid confirmation code registration.', NULL),
(301, 'system', 'Не передан код подтверждения регистрации.', 'Not passed the verification code registration.', NULL),
(302, 'system', 'Действие после первой авторизации', 'Action after the first authorization', NULL),
(303, 'system', 'Группы, куда попадет пользователь после авторизации', 'Group, which gets the user after login', NULL),
(304, 'system', 'Данные facebook', 'Facebook data', NULL),
(305, 'system', 'Поля пользователя', 'User fields', NULL),
(306, 'system', 'Соответсвие полей', 'Complies fields', NULL),
(307, 'system', 'включить авторизацию через facebook', 'enable authentication with facebook', NULL),
(308, 'system', 'Данные twiiter', 'Twitter data', NULL),
(309, 'system', 'включить авторизацию через твиттер', 'enable authentication with twitter', NULL),
(310, 'system', 'Минимальная длина пароля должна быть целым числом.', 'The minimum length of the password must be an integer.', NULL),
(311, 'system', 'Время, в течение которого пользователь считается online, должно быть целым числом больше 0.', 'The time during which the user is online, can be an integer greater than 0.', NULL),
(312, 'system', 'Неверный формат адреса e-mail.', 'nvalid address format of e-mail.', NULL),
(313, 'system', 'Вы не выбрали ни одной группы для зарегистрированных пользователей.', 'You have not selected any of the member.', NULL),
(314, 'system', 'HTML-письмо', 'HTML-letter', NULL),
(315, 'system', 'Тело письма', 'Letter body', NULL),
(316, 'system', 'Заголовок письма', 'Letter header', NULL),
(317, 'system', 'Восстановить форму по умолчанию', 'Restore the default form', NULL),
(318, 'system', 'Компонент "Личные сообщения"', 'Component "Private Messages"', NULL),
(319, 'system', 'Компонент "Пользователи"', 'Component "Users"', NULL),
(320, 'system', 'Разрешить добавлять пользователей во враги', 'Allow users to add enemies', NULL),
(321, 'system', 'Друзья и враги', 'Friends and enemies', NULL),
(322, 'system', 'Разрешить добавлять пользователей в друзья', 'Allow users to add as friend', NULL),
(323, 'system', 'Оповещать пользователя по e-mail о новом сообщении', 'Notify the user by e-mail about the new message', NULL),
(324, 'system', 'Личные сообщения', 'Private messages', NULL),
(325, 'system', 'Разрешить отправлять личные сообщения', 'Allow to send private messages', NULL),
(326, 'system', 'Авторизация пользователя сразу после подтверждения', 'User authentication after the confirm', NULL),
(327, 'system', 'E-mail администратора для отсылки оповещений', 'E-mail the administrator to send alerts', NULL),
(328, 'system', 'Отправлять письмо администратору при регистрации пользователя', 'Send a letter to the manager when a user logs', NULL),
(329, 'system', 'Премодерация администратором', 'Moderated by the administrator', NULL),
(330, 'system', 'Требовать подтверждение через e-mail', 'Require confirmation by e-mail', NULL),
(331, 'system', 'Группы, куда попадёт пользователь после регистрации', 'Group to which the user will get after registration', NULL),
(332, 'system', 'Разрешить самостоятельную регистрацию', 'Enable self-registration', NULL),
(333, 'system', 'Регистрация', 'Registration', NULL),
(334, 'system', 'Привязывать пользователей к сайтам', 'Bind users to sites', NULL),
(335, 'system', 'Запретить самостоятельно восстанавливать пароль', 'Deny yourself to recover your password', NULL),
(336, 'system', 'Общие настройки', 'General Settings', NULL),
(337, 'system', 'Не показывать форму при неудачной попытке авторизации', 'Do not show the form of a failed login attempt', NULL),
(338, 'system', 'Восстановлено', 'Restored', NULL),
(339, 'system', 'Несуществующая вкладка!', 'Nonexistent tab!', NULL),
(340, 'system', 'Авторизация через внешние сервисы', 'Login through external services', NULL),
(341, 'system', 'Шаблоны писем', 'Email templates', NULL),
(342, 'system', 'Общие', 'General', NULL),
(343, 'system', 'Восстановление пароля', 'Password restore', NULL),
(344, 'system', 'Подтверждение регистрации', 'Registration confirm', NULL),
(345, 'system', 'Сейчас вы будете переброшены на страницу авторизации.', 'Now you will be taken to the login page.', NULL),
(346, 'system', 'Нажмите, если не хотите ждать.', 'Click here if you do not want to wait.', NULL),
(347, 'system', 'Авторизация через twitter запрещена', 'Login via twitter disabled', NULL),
(348, 'system', 'Авторизация через facebook запрещена', 'Login via facebook disabled', NULL),
(349, 'system', 'FX_ADMIN_FIELD_STRING', 'String', 'Строка'),
(350, 'system', 'FX_ADMIN_FIELD_INT', 'Integer', 'Целое число'),
(351, 'system', 'FX_ADMIN_FIELD_TEXT', 'Text', 'Текст'),
(352, 'system', 'FX_ADMIN_FIELD_SELECT', 'Options list', 'Список'),
(353, 'system', 'FX_ADMIN_FIELD_BOOL', 'Boolean', 'Логическая переменная'),
(354, 'system', 'FX_ADMIN_FIELD_FILE', 'File', 'Файл'),
(355, 'system', 'FX_ADMIN_FIELD_FLOAT', 'Float number', 'Дробное число'),
(356, 'system', 'FX_ADMIN_FIELD_DATETIME', 'Date and time', 'Дата и время'),
(357, 'system', 'FX_ADMIN_FIELD_COLOR', 'Color', 'Цвет'),
(358, 'system', 'FX_ADMIN_FIELD_INFOBLOCK', 'Infoblock', 'Инфоблок'),
(359, 'system', 'FX_ADMIN_FIELD_IMAGE', 'Image', 'Изображение'),
(360, 'system', 'FX_ADMIN_FIELD_MULTISELECT', 'Multiple select', 'Мультисписок'),
(361, 'system', 'FX_ADMIN_FIELD_LINK', 'Link to another object', 'Связь с другим объектом'),
(362, 'system', 'FX_ADMIN_FIELD_MULTILINK', 'Multiple link', 'Множественная связь'),
(363, 'system', 'FX_ADMIN_FIELD_TEXT', 'Text', 'Текст'),
(364, 'system', 'FX_ADMIN_FIELD_SELECT', 'Options list', 'Список'),
(365, 'system', 'FX_ADMIN_FIELD_BOOL', 'Boolean', 'Логическая переменная'),
(366, 'system', 'FX_ADMIN_FIELD_FILE', 'File', 'Файл'),
(367, 'system', 'FX_ADMIN_FIELD_FLOAT', 'Float number', 'Дробное число'),
(368, 'system', 'FX_ADMIN_FIELD_DATETIME', 'Date and time', 'Дата и время'),
(369, 'system', 'FX_ADMIN_FIELD_COLOR', 'Color', 'Цвет'),
(370, 'system', 'FX_ADMIN_FIELD_INFOBLOCK', 'Infoblock', 'Инфоблок'),
(371, 'system', 'FX_ADMIN_FIELD_IMAGE', 'Image', 'Изображение'),
(372, 'system', 'FX_ADMIN_FIELD_MULTISELECT', 'Multiple select', 'Мультисписок'),
(373, 'system', 'FX_ADMIN_FIELD_LINK', 'Link to another object', 'Связь с другим объектом'),
(374, 'system', 'FX_ADMIN_FIELD_MULTILINK', 'Multiple link', 'Множественная связь'),
(375, 'system', 'add', NULL, NULL),
(376, 'system', 'edit', NULL, NULL),
(377, 'system', 'on', NULL, NULL),
(378, 'system', 'off', NULL, NULL),
(379, 'system', 'settings', NULL, NULL),
(380, 'system', 'delete', NULL, NULL),
(381, 'system', 'Render type', NULL, NULL),
(382, 'system', 'Live search', NULL, NULL),
(383, 'system', 'Simple select', NULL, NULL),
(384, 'system', 'Любой', '-Any-', NULL),
(385, 'system', 'Только на страницах типа', 'Only on pages of type', NULL),
(386, 'system', '-- выберите вариант --', '-- choose something --', NULL),
(387, 'component_section', 'Показывать только заголовок?', NULL, NULL),
(388, 'component_section', 'Скрыть на главной?', NULL, NULL),
(389, 'system', 'Welcome to Floxim.CMS, please sign in', NULL, NULL),
(390, 'system', 'Editing ', NULL, NULL),
(391, 'system', 'Fields table', NULL, NULL),
(392, 'system', 'Adding new ', NULL, NULL),
(393, 'controller_component', 'Новый инфоблок', NULL, NULL),
(394, 'controller_component', 'Инфоблок для поля ', NULL, NULL),
(395, 'system', 'Название компонента (по-русски)', NULL, NULL),
(396, 'system', 'Название сущности создаваемой компонентом (по-русски)', NULL, NULL);

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
  KEY `Checked` (`checked`),
  KEY `component_id` (`component_id`),
  KEY `System_Table_ID` (`system_table_id`),
  KEY `TypeOfData_ID` (`type`),
  KEY `TypeOfEdit_ID` (`type_of_edit`),
  KEY `Widget_Class_ID` (`widget_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=95 AUTO_INCREMENT=212 ;

--
-- Дамп данных таблицы `fx_field`
--

INSERT INTO `fx_field` (`id`, `parent`, `component_id`, `ctpl_id`, `system_table_id`, `widget_id`, `name`, `description`, `type`, `format`, `not_null`, `priority`, `searchable`, `default`, `inheritance`, `type_of_edit`, `checked`) VALUES
(1, NULL, 1, 0, 0, 0, 'name', 'Screen name', 1, '', 0, 0, 1, '', 0, 1, 1),
(2, NULL, 1, 0, 0, 0, 'avatar', 'Userpic', 6, '', 0, 0, 0, '', 0, 1, 1),
(3, NULL, 1, 0, 0, 0, 'forum_signature', 'Forum signature', 3, '', 0, 0, 1, '', 0, 1, 1),
(118, NULL, 19, 0, 0, 0, 'text', 'Text', 3, 'a:1:{s:4:"html";s:1:"1";}', 0, 0, 1, '', 0, 1, 1),
(152, NULL, 1, 0, 0, 0, 'first_name', 'First name', 1, '', 0, 141, 0, '', 0, 1, 1),
(209, NULL, 30, 0, 0, 0, 'metatype', 'meta type', 4, 'a:2:{s:6:"source";s:6:"manual";s:6:"values";a:2:{i:1;a:2:{s:2:"id";s:4:"post";s:5:"value";s:11:"Just a post";}i:2;a:2:{s:2:"id";s:7:"article";s:5:"value";s:24:"Long interesting article";}}}', 0, 171, 0, 'post', 0, 1, 1),
(165, NULL, 23, 0, 0, 0, 'url', 'URL', 1, '', 0, 2, 0, '', 0, 1, 1),
(153, NULL, 1, 0, 0, 0, 'email', 'E-mail', 1, '', 0, 142, 1, NULL, 0, 1, 1),
(186, NULL, 32, 0, 0, 0, 'tag_id', 'Tag', 13, 'a:3:{s:6:"target";s:2:"31";s:9:"prop_name";s:3:"tag";s:11:"render_type";s:10:"livesearch";}', 0, 157, 0, '', 0, 1, 1),
(195, NULL, 30, 0, 0, 0, 'tags', 'Tags', 14, 'a:2:{s:6:"target";s:7:"198.186";s:11:"render_type";s:5:"table";}', 0, 2, 0, '', 0, 1, 1),
(196, NULL, 36, 0, 0, 0, 'parent_id', 'Parent', 13, 'a:3:{s:6:"target";s:2:"23";s:9:"prop_name";s:0:"";s:11:"render_type";s:6:"select";}', 0, 161, 0, '', 0, 3, 1),
(197, NULL, 31, 0, 0, 0, 'tagposts', 'Tag posts', 14, 'a:1:{s:6:"target";s:3:"186";}', 0, 162, 0, '', 0, 3, 1),
(198, NULL, 32, 0, 0, 0, 'post_id', 'Page', 13, 'a:4:{s:6:"target";s:2:"30";s:9:"prop_name";s:4:"post";s:9:"is_parent";s:1:"1";s:11:"render_type";s:6:"select";}', 0, 163, 0, '', 0, 3, 1),
(184, NULL, 30, 0, 0, 0, 'publish_date', 'Publish date', 8, '', 0, 3, 1, '', 0, 1, 1),
(182, NULL, 30, 0, 0, 0, 'anounce', 'Anounce', 3, 'a:1:{s:4:"html";s:1:"1";}', 1, 0, 0, '', 0, 1, 1),
(183, NULL, 30, 0, 0, 0, 'text', 'Full text', 3, 'a:1:{s:4:"html";s:1:"1";}', 1, 1, 0, '', 0, 1, 1),
(191, NULL, 23, 0, 0, 0, 'title', 'Title', 1, '', 0, 158, 0, '', 0, 1, 1),
(192, NULL, 31, 0, 0, 0, 'counter', 'Usage counter', 2, '', 0, 159, 0, '0', 0, 3, 1),
(190, NULL, 23, 0, 0, 0, 'name', 'Name', 1, '', 1, 0, 1, '', 0, 1, 1),
(199, NULL, 46, 0, 0, 0, 'start_date', 'Start date', 8, '', 0, 164, 0, '', 0, 1, 1),
(200, NULL, 46, 0, 0, 0, 'end_date', 'End date', 8, '', 0, 165, 0, '', 0, 1, 1),
(201, NULL, 47, 0, 0, 0, 'publish_date', 'Publish date', 8, '', 0, 166, 0, '', 0, 1, 1),
(202, NULL, 47, 0, 0, 0, 'cover', 'Cover image', 11, '', 0, 167, 0, '', 0, 1, 1),
(203, NULL, 48, 0, 0, 0, 'photo', 'Image', 11, '', 1, 168, 0, '', 0, 1, 1),
(204, NULL, 48, 0, 0, 0, 'description', 'Description', 3, '', 0, 169, 0, '', 0, 1, 1),
(205, NULL, 48, 0, 0, 0, 'copy', 'Copy', 1, '', 0, 170, 0, '', 0, 1, 1),
(210, NULL, 32, 0, 0, 0, 'comment', 'Why the tag is relevant', 1, '', 0, 172, 0, '', 0, 1, 1),
(211, NULL, 30, 0, 0, 0, 'image', 'Image', 11, '', 0, 173, 0, '', 0, 1, 1);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=56 AUTO_INCREMENT=384 ;

--
-- Дамп данных таблицы `fx_filetable`
--

INSERT INTO `fx_filetable` (`id`, `real_name`, `path`, `type`, `size`, `to_delete`) VALUES
(53, 'code-1.png', 'content/code-1_3.png', 'image/png', 30370, 0),
(54, 'code-2.png', 'content/code-2_0.png', 'image/png', 56576, 0),
(55, 'new-yandex-index.png', 'content/new-yandex-index_0.png', 'image/png', 214827, 0),
(56, 'bg-portfolio.jpg', 'content/bg-portfolio_1.jpg', 'image/jpeg', 235261, 0),
(57, 'arrow02.png', 'content/arrow02_0.png', 'image/png', 1049, 0),
(58, 'bg-portfolio.jpg', 'content/bg-portfolio_2.jpg', 'image/jpeg', 235261, 0),
(59, 'bg-company.jpg', 'content/bg-company_1.jpg', 'image/jpeg', 68376, 0),
(60, 'bg-portfolio.jpg', 'content/bg-portfolio_3.jpg', 'image/jpeg', 235261, 0),
(61, 'arrow04.png', 'content/arrow04_0.png', 'image/png', 1017, 0),
(62, 'img01.jpg', 'content/img01_0.jpg', 'image/jpeg', 219265, 0),
(63, 'bg-contact-holder.gif', 'content/bg-contact-holder_0.gif', 'image/gif', 1165, 0),
(105, 'portfolio_photo.jpg', 'content/portfolio_photo_0.jpg', 'image/jpeg', 238783, 0),
(108, '3568885424_243abf3d8b_b.jpg', 'content/3568885424_243abf3d8b_b_0.jpg', 'image/jpeg', 372454, 0),
(113, 'aborigeny-tozhe.jpg', 'content/aborigeny-tozhe_0.jpg', 'image/jpeg', 123530, 0),
(116, 'NOMADE-RIDDIM-VOL.031.jpg', 'content/NOMADE-RIDDIM-VOL.031_0.jpg', 'image/jpeg', 422922, 0),
(163, 'гроза.jpg', 'content/groza_0.jpg', 'image/jpeg', 222559, 0),
(164, 'гроза.jpg', 'content/groza_1.jpg', 'image/jpeg', 172197, 0),
(329, 'гроза.jpg', 'content/groza_2.jpg', 'image/jpeg', 172197, 0),
(330, 'гроза.jpg', 'content/groza_3.jpg', 'image/jpeg', 172197, 0),
(331, 'гроза.jpg', 'content/groza_4.jpg', 'image/jpeg', 172197, 0),
(332, 'гроза.jpg', 'content/groza_5.jpg', 'image/jpeg', 172197, 0),
(333, 'гроза.jpg', 'content/groza_6.jpg', 'image/jpeg', 172197, 0),
(334, 'гроза.jpg', 'content/groza_7.jpg', 'image/jpeg', 172197, 0),
(335, 'гроза.jpg', 'content/groza_8.jpg', 'image/jpeg', 172197, 0),
(336, '3568885424_243abf3d8b_b.jpg', 'content/3568885424_243abf3d8b_b_1.jpg', 'image/jpeg', 372454, 0),
(363, 'bg-portfolio.jpg', 'content/bg-portfolio_7.jpg', 'image/jpeg', 235261, 0),
(338, 'гроза.jpg', 'content/groza_9.jpg', 'image/jpeg', 172197, 0),
(361, 'aborigeny_1.jpg', 'content/aborigeny_1.jpg', 'image/jpeg', 102971, 0),
(362, 'img05.jpg', 'content/img05_0.jpg', 'image/jpeg', 5717, 0),
(341, 'aborigeny.jpg', 'content/aborigeny_6.jpg', 'image/jpeg', 102971, 0),
(342, 'logo.png', 'content/logo_0.png', 'image/png', 5735, 0),
(343, 'logo.png', 'content/logo_1.png', 'image/png', 5735, 0),
(344, 'img03.jpg', 'content/img03_0.jpg', 'image/jpeg', 3574, 0),
(345, 'img01.jpg', 'content/img01_1.jpg', 'image/jpeg', 219265, 0),
(346, 'bg-portfolio.jpg', 'content/bg-portfolio_4.jpg', 'image/jpeg', 235261, 0),
(347, 'img01.jpg', 'content/img01_2.jpg', 'image/jpeg', 219265, 0),
(348, 'bg-portfolio.jpg', 'content/bg-portfolio_5.jpg', 'image/jpeg', 235261, 0),
(349, 'arrow02.png', 'content/arrow02_1.png', 'image/png', 1049, 0),
(350, 'logo.png', 'content/logo_2.png', 'image/png', 5735, 0),
(351, 'arrow02.png', 'content/arrow02_2.png', 'image/png', 1049, 0),
(352, 'arrow02.png', 'content/arrow02_3.png', 'image/png', 1049, 0),
(353, 'arrow02.png', 'content/arrow02_4.png', 'image/png', 1049, 0),
(354, 'arrow02.png', 'content/arrow02_5.png', 'image/png', 1049, 0),
(355, 'logo.gif', 'content/logo_0.gif', 'image/gif', 1265, 0),
(356, '_logo.png', 'content/logo_3.png', 'image/png', 2627, 0),
(357, 'bg-portfolio.jpg', 'content/bg-portfolio_6.jpg', 'image/jpeg', 235261, 0),
(358, 'bg-company.jpg', 'content/bg-company_2.jpg', 'image/jpeg', 68376, 0),
(359, 'logo.png', 'content/logo_4.png', 'image/png', 2627, 0),
(360, 'logo.png', 'content/logo_5.png', 'image/png', 5735, 0),
(364, 'INFOGRAPHICA_new.jpg', 'content/INFOGRAPHICA_new_0.jpg', 'image/jpeg', 1630738, 0),
(365, 'foto.jpg', 'content/foto_0.jpg', 'image/jpeg', 47794, 0),
(366, 'foto.jpg', 'content/foto_1.jpg', 'image/jpeg', 47794, 0),
(367, 'jeep-safari-2.jpg', 'content/jeep-safari-2_0.jpg', 'image/jpeg', 110257, 0),
(368, 'jeep-safari-2.jpg', 'content/jeep-safari-2_1.jpg', 'image/jpeg', 110257, 0),
(369, 'Rickus Vermeulen Jeep.jpg', 'content/Rickus_Vermeulen_Jeep_0.jpg', 'image/jpeg', 46915, 0),
(370, 'Rickus Vermeulen Jeep.jpg', 'content/Rickus_Vermeulen_Jeep_1.jpg', 'image/jpeg', 46915, 0),
(371, 'Rickus Vermeulen Jeep.jpg', 'content/Rickus_Vermeulen_Jeep_2.jpg', 'image/jpeg', 46915, 0),
(372, '1251362341_1243855523_vnedorojnik_21.jpg', 'content/1251362341_1243855523_vnedorojnik_21_0.jpg', 'image/jpeg', 113615, 0),
(373, 'foto.jpg', 'content/foto_2.jpg', 'image/jpeg', 47794, 0),
(374, '_800_600_90_667819585-IMG_2954.jpg', 'content/800_600_90_667819585-IMG_2954_0.jpg', 'image/jpeg', 39395, 0),
(375, 'aborigeny-tozhe_0.jpg', 'content/aborigeny-tozhe_0.jpg', 'image/jpeg', 123530, 0),
(376, '1251362341_1243855523_vnedorojnik_21.jpg', 'content/1251362341_1243855523_vnedorojnik_21_1.jpg', 'image/jpeg', 113615, 0),
(377, 'logo.gif', 'content/logo_1.gif', 'image/gif', 1265, 0),
(378, 'logo.png', 'content/logo_6.png', 'image/png', 5735, 0),
(379, 'img05.jpg', 'content/img05_1.jpg', 'image/jpeg', 5717, 0),
(380, 'img09.jpg', 'content/img09_1.jpg', 'image/jpeg', 4587, 0),
(381, 'img10.jpg', 'content/img10_0.jpg', 'image/jpeg', 238783, 0),
(382, 'img09.jpg', 'content/img09_2.jpg', 'image/jpeg', 4587, 0),
(383, 'img09.jpg', 'content/img09_3.jpg', 'image/jpeg', 4587, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_group`
--

CREATE TABLE IF NOT EXISTS `fx_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=197 AUTO_INCREMENT=4 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=52 COMMENT='История операций' AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `fx_history`
--

INSERT INTO `fx_history` (`id`, `user_id`, `date`, `name`, `marker`) VALUES
(1, 1, '2013-07-15 16:16:00', 'FX_HISTORY_ADMIN_COMPONENT_EDIT', 0),
(2, 1, '2013-07-19 17:54:22', 'FX_HISTORY_ADMIN_FIELD_ADD', 0);

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=373 AUTO_INCREMENT=1 ;

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
  `name` varchar(255) NOT NULL,
  `controller` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `params` text NOT NULL,
  `scope` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=210 AUTO_INCREMENT=121 ;

--
-- Дамп данных таблицы `fx_infoblock`
--

INSERT INTO `fx_infoblock` (`id`, `parent_infoblock_id`, `site_id`, `page_id`, `checked`, `name`, `controller`, `action`, `params`, `scope`) VALUES
(3, 0, 1, 2, 1, 'Main menu', 'component_section', 'listing', 'a:1:{s:7:"submenu";s:3:"all";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(12, 0, 1, 0, 1, '', 'layout', 'show', 'a:0:{}', 'a:2:{s:5:"pages";s:3:"all";s:9:"page_type";s:0:"";}'),
(16, 0, 1, 2, 1, 'Index text', 'component_text', 'listing', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(43, 0, 1, 2, 1, 'Main Header (via breadcrumbs)', 'component_section', 'breadcrumbs', 'a:2:{s:11:"header_only";s:1:"1";s:13:"hide_on_index";b:0;}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:0:"";}'),
(117, 0, 15, 1925, 1, 'Blog / By tag', 'component_blogpost', 'listing_by_tag', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:3:"tag";}'),
(53, 12, 1, 2, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(54, 12, 1, 78, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(24, 0, 1, 2, 1, '', 'widget_authform', 'show', 'a:0:{}', 'a:1:{s:5:"pages";s:3:"all";}'),
(55, 54, 1, 97, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(69, 0, 15, 1883, 1, 'Main menu', 'component_section', 'listing', 'a:1:{s:7:"submenu";s:4:"none";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(71, 67, 15, 1883, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(70, 0, 15, 1883, 1, 'Routes', 'component_travel_route', 'listing', 'a:6:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:13:"mount_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(61, 0, 1, 2, 1, 'Text / listing', 'component_text', 'listing', 'a:6:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(67, 0, 15, 0, 1, 'Layout', 'layout', 'show', '', ''),
(72, 0, 15, 1883, 1, 'Starting', 'component_travel_route', 'listing_mirror', 'a:6:{s:5:"limit";s:1:"4";s:15:"show_pagination";b:0;s:7:"sorting";s:10:"start_date";s:11:"sorting_dir";s:4:"desc";s:8:"from_all";s:1:"1";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(74, 0, 15, 1883, 1, 'Page text', 'component_text', 'listing', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:0:"";}'),
(75, 0, 15, 1883, 1, 'Index text', 'component_text', 'listing', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(76, 0, 15, 1883, 1, 'Social networks', 'component_section', 'listing', 'a:1:{s:7:"submenu";s:4:"none";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(77, 0, 15, 1887, 1, 'Menu / About', 'component_section', 'listing', 'a:1:{s:7:"submenu";s:4:"none";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(78, 0, 15, 0, 1, ' Breadcrumbs', 'component_section', 'breadcrumbs', 'a:2:{s:11:"header_only";b:0;s:13:"hide_on_index";s:1:"1";}', 'a:2:{s:5:"pages";s:3:"all";s:9:"page_type";s:0:"";}'),
(79, 0, 15, 1889, 1, 'Our gallery', 'component_gallery', 'listing', 'a:6:{s:5:"limit";s:1:"0";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:12:"publish_date";s:11:"sorting_dir";s:4:"desc";s:11:"parent_type";s:13:"mount_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(80, 67, 15, 1889, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(81, 0, 15, 1889, 1, 'Photo list', 'component_photo', 'listing', 'a:6:{s:5:"limit";s:0:"";s:15:"show_pagination";b:0;s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:7:"gallery";}'),
(82, 0, 15, 1925, 1, 'Blog posts', 'component_blogpost', 'listing', 'a:7:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:12:"publish_date";s:11:"sorting_dir";s:4:"desc";s:11:"parent_type";s:13:"mount_page_id";s:9:"parent_id";s:0:"";s:19:"field_195_infoblock";s:2:"84";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(83, 0, 15, 1925, 1, 'Tag cloud', 'component_tag', 'listing', 'a:6:{s:5:"limit";s:0:"";s:15:"show_pagination";b:0;s:7:"sorting";s:7:"counter";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:13:"mount_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(84, 0, 15, 1925, 1, 'Post tags', 'component_tagpost', 'listing', 'a:7:{s:5:"limit";s:0:"";s:15:"show_pagination";b:0;s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";i:0;b:0;}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:8:"blogpost";}'),
(85, 0, 15, 1883, 1, 'Photo / mirror', 'component_photo', 'listing_mirror', 'a:6:{s:5:"limit";s:1:"3";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:7:"created";s:11:"sorting_dir";s:4:"desc";s:8:"from_all";s:1:"1";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(86, 0, 15, 1925, 1, 'Blog calendar', 'component_blogpost', 'calendar', 'a:0:{}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(87, 0, 15, 1925, 1, 'Separate blog post', 'component_blogpost', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:8:"blogpost";}'),
(99, 71, 15, 1883, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(100, 99, 15, 1883, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(103, 0, 15, 0, 1, 'Blog / List', 'component_blogpost', 'listing', 'a:7:{s:5:"limit";s:1:"1";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:7:"created";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";s:19:"field_195_infoblock";s:2:"84";}', 'a:2:{s:5:"pages";s:3:"all";s:9:"page_type";s:0:"";}'),
(104, 0, 15, 0, 1, 'Text / List', 'component_text', 'listing', 'a:0:{}', 'a:2:{s:5:"pages";s:3:"all";s:9:"page_type";N;}'),
(118, 0, 15, 1887, 1, 'Blog / Recent', 'component_blogpost', 'listing_mirror', 'a:6:{s:5:"limit";s:1:"3";s:15:"show_pagination";b:0;s:7:"sorting";s:12:"publish_date";s:11:"sorting_dir";s:4:"desc";s:8:"from_all";s:1:"1";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_infoblock_visual`
--

CREATE TABLE IF NOT EXISTS `fx_infoblock_visual` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `infoblock_id` int(10) unsigned NOT NULL,
  `layout_id` int(10) unsigned NOT NULL,
  `wrapper` varchar(255) NOT NULL,
  `wrapper_visual` text NOT NULL,
  `template` varchar(255) NOT NULL,
  `template_visual` text NOT NULL,
  `area` varchar(50) NOT NULL,
  `priority` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `infoblock_id` (`infoblock_id`,`layout_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=138 AUTO_INCREMENT=192 ;

--
-- Дамп данных таблицы `fx_infoblock_visual`
--

INSERT INTO `fx_infoblock_visual` (`id`, `infoblock_id`, `layout_id`, `wrapper`, `wrapper_visual`, `template`, `template_visual`, `area`, `priority`) VALUES
(55, 53, 1, '', '', 'layout_supernova.index', '', '', 1),
(2, 3, 1, '', 'a:2:{i:163;s:13:"Ус-луги";s:9:"separator";s:5:"• !";}', 'layout_supernova.demo_menu', 'a:3:{s:9:"separator";s:3:"•";s:6:"odd_bg";s:4:"#111";s:9:"odd_color";s:4:"#FF0";}', 'header', 1),
(56, 54, 1, '', '', 'layout_supernova.index', '', '', 2),
(14, 12, 1, '', '', 'layout_supernova.inner', 'a:6:{s:4:"logo";s:3:"356";s:7:"company";s:14:"Floxim Company";s:6:"slogan";s:17:"Almost clear site";s:13:"replace_src_0";s:38:"/controllers/layout/supernova/logo.png";s:8:"developa";s:103:"© 2010 Хороший пример \n<br>\nсайтостроения — \n<a href="#">\nWebSite.ru\n</a>\n";s:13:"banner_slogan";s:124:"«Simplicity of sitebuilder, functionality of CMS,\n                        flexibility of framework. <br>​And it''s free!»";}', '', 4),
(188, 117, 10, '', '', 'component_blogpost.listing', '', 'content', 9),
(18, 16, 1, 'layout_supernova.wrap_titled', 'a:2:{s:5:"title";s:21:"Don''t miss the chance";s:5:"color";s:7:"#027a02";}', 'component_text.listing', '', 'content', 9),
(26, 24, 1, 'layout_supernova.wrap_titled', 'a:1:{s:5:"title";s:16:"Have an account?";}', 'widget_authform.show', 'a:3:{s:15:"replace_value_0";s:10:"Войти";s:12:"login_button";s:8:"Sign in!";s:17:"placeholder_login";s:5:"email";}', 'sidebar', 3),
(57, 55, 1, '', '', 'layout_supernova.inner', '', '', 3),
(45, 43, 1, '', '', 'component_section.breadcrumbs', 'a:1:{s:9:"separator";s:3:" / ";}', 'content', 1),
(59, 57, 1, 'layout_supernova.wrap_titled', '', 'component_page.listing', '', 'content', 8),
(109, 16, 8, '', '', 'component_text.listing', '', 'content', 9),
(108, 53, 8, '', '', 'layout_demo8.index', 'a:3:{s:9:"c1_header";s:31:"Новости | события";s:9:"logo_text";s:27:"Think<span>Different</span>";s:6:"slogan";s:37:"лучшие утюги россии!";}', '', 0),
(107, 12, 8, '', '', 'layout_demo8.index', 'a:0:{}', '', 0),
(140, 70, 10, '', '', 'layout_jeeptravel.index_slider', 'a:14:{s:9:"info_1891";s:148:"<dt><strong>Difficulty:</strong>&nbsp;easy<br><strong>Cities:</strong>&nbsp;Gada, Balle, Binji, Wurno<br><strong>Villages:</strong> Kaita, Rimi</dt>";s:14:"more_text_1891";s:12:"Tell me more";s:16:"action_text_1891";s:14:"Gonna b there!";s:9:"date_1891";s:15:"May 12–16<br>";s:11:"header_1891";s:12:"Summer Rally";s:13:"bg_photo_1892";s:3:"357";s:11:"header_1892";s:34:"It''s going to be<br>​Legen-dary!";s:16:"action_text_1892";s:15:"Yes, I''m crazy!";s:14:"more_text_1892";s:12:"Tell me more";s:9:"date_1892";s:16:"January 5 – 19";s:9:"info_1892";s:251:"<dl>\n                                            <dt>Difficulty: </dt>extremely difficult<br>​Period: 2 weeks<br>Cities<strong>: Paris, Dakar</strong><dd></dd><dt>A chance to survive:</dt><dd>~23.5%</dd>\n                                        </dl>";s:13:"bg_photo_1891";s:3:"345";s:15:"action_url_1891";s:0:"";s:15:"action_url_1892";s:0:"";}', 'content', 8),
(174, 103, 10, '', '', 'auto.auto', '', 'index_right', 0),
(103, 24, 8, '', '', 'widget_authform.show', '', 'sidebar', 2),
(101, 43, 8, '', '', 'component_section.breadcrumbs', '', 'content', 1),
(99, 3, 8, '', '', 'layout_demo8.demo_menu', '', 'header', 1),
(98, 54, 8, '', '', 'layout_demo8.index', '', '', 0),
(111, 55, 8, '', '', 'layout_demo8.index', '', '', 0),
(116, 53, 9, '', '', 'layout_dummy.2cols', '', '', 0),
(117, 3, 9, '', '', 'component_section.listing', '', 'header', 1),
(118, 16, 9, '', '', 'component_text.listing', '', 'content', 9),
(120, 24, 9, '', '', 'widget_authform.show', '', 'left', 2),
(139, 69, 10, '', '', 'layout_jeeptravel.top_menu', '', 'header', 1),
(122, 12, 9, '', '', 'layout_dummy.2cols', '', '', 0),
(123, 43, 9, '', '', 'component_section.breadcrumbs', '', 'content', 1),
(125, 61, 8, '', '', 'auto.auto', '', 'banner', 0),
(126, 61, 1, '', '', 'component_text.listing', '', '', 0),
(134, 67, 1, '', '', 'layout_supernova.index', '', '', 0),
(137, 67, 10, '', '', 'layout_jeeptravel.page', 'a:30:{s:18:"page_bg_color_1895";s:0:"";s:18:"page_bg_color_1888";s:7:"#E9A502";s:18:"page_bg_image_1888";s:52:"/controllers/layout/jeeptravel/images/bg-company.jpg";s:18:"page_bg_image_1895";s:0:"";s:18:"page_bg_color_1887";s:4:"#000";s:18:"page_bg_image_1887";s:3:"358";s:5:"phone";s:18:"+7 (905) 561 99 72";s:4:"mail";s:19:"info@jeeptravel.loc";s:18:"page_bg_color_1889";s:4:"#000";s:18:"page_bg_image_1889";s:54:"/controllers/layout/jeeptravel/images/bg-portfolio.jpg";s:18:"page_bg_color_1890";s:7:"#E9A502";s:18:"page_bg_image_1890";s:2:"60";s:18:"page_bg_color_1925";s:7:"#060008";s:18:"page_bg_image_1925";s:3:"164";s:18:"page_bg_color_1926";s:7:"#500070";s:18:"page_bg_image_1926";s:3:"326";s:4:"logo";s:3:"378";s:18:"page_bg_image_1891";s:0:"";s:18:"page_bg_image_1968";s:0:"";s:14:"contacts_label";s:8:"Call us:";s:4:"copy";s:99:"© JeepTravel, 2013<br>&nbsp; &nbsp; &nbsp;Photo by: <a href="http://leecannon.com/">Lee Cannon</a>";s:18:"page_bg_image_1883";s:0:"";s:18:"page_bg_image_1996";s:0:"";s:18:"page_bg_image_1884";s:0:"";s:18:"page_bg_color_1883";s:7:"#ffffff";s:18:"page_bg_color_1884";s:7:"#000000";s:18:"page_bg_image_1916";s:0:"";s:18:"page_bg_image_1902";s:0:"";s:18:"page_bg_image_2033";s:0:"";s:5:"email";s:11:"info@jt.com";}', '', 0),
(141, 71, 10, '', '', 'layout_jeeptravel.index', '', '', 0),
(142, 72, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:8:"Upcoming";}', 'layout_jeeptravel.index_link_list', '', 'index_center', 2),
(144, 74, 10, '', '', 'component_text.listing', '', 'content', 7),
(145, 75, 10, '', '', 'component_text.listing', '', 'index_center', 1),
(146, 76, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:9:"Follow us";}', 'layout_jeeptravel.index_link_list', '', 'index_right', 1),
(147, 71, 1, '', '', 'layout_supernova.index', '', '', 0),
(148, 69, 1, '', '', 'layout_supernova.demo_menu', '', 'header', 1),
(149, 70, 1, '', '', 'component_page.listing', '', 'content', 2),
(150, 72, 1, '', '', 'component_page.listing', '', 'footer', 2),
(151, 75, 1, '', '', 'component_text.listing', '', 'footer', 1),
(152, 76, 1, '', '', 'layout_supernova.demo_menu', '', '', 0),
(154, 74, 1, '', '', 'component_text.listing', '', 'content', 1),
(155, 77, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:13:"Jeep Travels:";}', 'layout_jeeptravel.side_menu', '', 'sidebar', 4),
(156, 78, 10, '', '', 'component_section.breadcrumbs', '', 'content', 1),
(157, 79, 10, '', '', 'layout_jeeptravel.pages_by_year', '', 'content', 5),
(158, 80, 10, '', '', 'layout_jeeptravel.full', 'a:6:{s:18:"page_bg_color_1889";s:7:"#c7c1c7";s:18:"page_bg_image_1889";s:2:"56";s:18:"page_bg_color_1916";s:7:"#706c70";s:18:"page_bg_image_1916";s:0:"";s:14:"contacts_label";s:18:"Для связи:";s:5:"phone";s:18:"+7 (905) 561 99 75";}', '', 0),
(159, 81, 10, '', '', 'layout_jeeptravel.photo_listing', '', 'content', 6),
(160, 82, 10, '', '', 'component_blogpost.listing', 'a:6:{s:13:"bg_photo_1976";s:0:"";s:10:"tags_label";s:5:"Tags:";s:12:"posted_under";s:11:"Entry tags:";s:9:"blog_name";s:21:"Jeep Travel blog feed";s:16:"blog_description";s:27:"Our blog is so interesting!";s:16:"rss_posted_under";s:11:"Entry tags:";}', 'content', 2),
(161, 83, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:10:"Tags cloud";}', 'component_tag.listing', '', 'sidebar', 1),
(162, 84, 10, '', '', 'component_tagpost.listing', '', 'content', 4),
(163, 85, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:6:"Photos";}', 'layout_jeeptravel.index_photo_anounces', 'a:2:{s:10:"image_1916";s:0:"";s:10:"image_1913";s:0:"";}', 'index_left', 1),
(164, 86, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:7:"Archive";}', 'component_blogpost.calendar', '', 'sidebar', 2),
(165, 87, 10, '', '', 'auto.auto', '', 'content', 3),
(175, 104, 10, '', '', 'auto.auto', '', 'sidebar', 0),
(170, 99, 10, '', '', 'layout_jeeptravel.index', '', '', 0),
(171, 100, 10, '', '', 'layout_jeeptravel.index', '', '', 0),
(189, 118, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:19:"Recent blog entries";}', 'component_page.listing', '', 'sidebar', 5);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_layout`
--

CREATE TABLE IF NOT EXISTS `fx_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=64 AUTO_INCREMENT=11 ;

--
-- Дамп данных таблицы `fx_layout`
--

INSERT INTO `fx_layout` (`id`, `keyword`, `name`) VALUES
(1, 'supernova', 'Super Nova'),
(8, 'demo8', 'Demo-8'),
(6, 'test1', 'Test-1'),
(9, 'dummy', 'Dummy'),
(10, 'jeeptravel', 'JeepTravel');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=647 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=68 AUTO_INCREMENT=5 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=17 AUTO_INCREMENT=1 ;

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
  KEY `AdminType` (`type`),
  KEY `User_ID` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=56 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=126;

--
-- Дамп данных таблицы `fx_session`
--

INSERT INTO `fx_session` (`id`, `user_id`, `session_start`, `session_time`, `ip`, `login_save`, `site_id`, `auth_type`) VALUES
('3afb326c3bacfbb186e2f61601f670e6', 99, 1374445208, 1374586413, 2130706433, 0, 0, 1);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=70 AUTO_INCREMENT=40 ;

--
-- Дамп данных таблицы `fx_settings`
--

INSERT INTO `fx_settings` (`id`, `key`, `value`, `module`, `site_id`) VALUES
(1, 'version', '0.9.9', 'system', 0),
(2, 'last_check', '1346408043', 'system', 0),
(3, 'last_response', 'a:3:{s:10:"next_patch";s:5:"1.0.0";s:19:"next_patch_fulllink";s:39:"http://floxim.org/update/update_15.html";s:15:"next_patch_link";s:61:"http://floxim.org/?essence=module_patch&action=download&id=15";}', 'system', 0),
(4, 'next_patch', '1.0.0', 'system', 0),
(5, 'user_email_field', 'email', 'system', 0),
(6, 'spam_from_name', 'Admin', 'system', 0),
(7, 'spam_from_email', 'dubr.cola@gmail.com', 'system', 0),
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
  `index_page_id` int(11) NOT NULL DEFAULT '0',
  `error_page_id` int(11) NOT NULL DEFAULT '0',
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=292 AUTO_INCREMENT=23 ;

--
-- Дамп данных таблицы `fx_site`
--

INSERT INTO `fx_site` (`id`, `parent_id`, `name`, `domain`, `layout_id`, `color`, `mirrors`, `priority`, `checked`, `index_page_id`, `error_page_id`, `created`, `last_updated`, `robots`, `disallow_indexing`, `type`, `language`, `offline_text`, `store_id`) VALUES
(1, 0, 'FloxiShop', 'playground.fx', 1, 2, '', 0, 1, 2, 3, '2012-05-24 12:42:50', '2013-07-15 10:32:59', '# Floxim Robots file\r\nUser-agent: *\r\nDisallow: /install/', 0, 'useful', 'en', '<table width=''100%'' height=''100%'' border=''0'' cellpadding=''0'' cellspacing=''0''><tr><td align=''center''>Сайт временно (!) недоступен.</td></tr></table>', NULL),
(15, 0, 'JeepTravel', 'floxim', 10, 0, '', 1, 1, 0, 1884, '2013-06-08 17:03:02', '2013-07-15 10:14:22', NULL, 0, 'useful', 'en', NULL, NULL);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=113 AUTO_INCREMENT=38 ;

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
  KEY `PermissionGroup_ID` (`group_id`),
  KEY `User_ID` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=13 AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `fx_user_group`
--

INSERT INTO `fx_user_group` (`id`, `user_id`, `group_id`) VALUES
(1, 99, 1),
(6, 99, 2),
(7, 100, 1);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=111 AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `fx_widget`
--

INSERT INTO `fx_widget` (`id`, `name`, `keyword`, `description`, `group`, `checked`, `icon`, `embed`, `store_id`) VALUES
(1, 'Блок авторизации', 'authform', NULL, 'Личный кабинет', 1, 'auth', 'miniblock', 'widget.auth'),
(2, 'Форма восстановления пароля', 'recoverpasswd', NULL, 'Личный кабинет', 1, 'auth', 'narrow-wide', 'widget.recoverpasswd');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

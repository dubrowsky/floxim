-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Мар 18 2014 г., 13:53
-- Версия сервера: 5.5.25
-- Версия PHP: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `floxim_loc`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=200 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=36 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=32 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=34 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_component`
--

CREATE TABLE IF NOT EXISTS `fx_component` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `description_en` text COMMENT 'Описание компонента',
  `group` varchar(64) NOT NULL DEFAULT 'Main',
  `icon` varchar(255) NOT NULL,
  `store_id` text,
  `parent_id` int(11) DEFAULT NULL,
  `item_name_en` varchar(45) DEFAULT NULL,
  `name_ru` varchar(255) DEFAULT NULL,
  `item_name_ru` varchar(255) DEFAULT NULL,
  `description_ru` text,
  PRIMARY KEY (`id`),
  KEY `Class_Group` (`group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=100 AUTO_INCREMENT=80 ;

--
-- Дамп данных таблицы `fx_component`
--

INSERT INTO `fx_component` (`id`, `keyword`, `name_en`, `description_en`, `group`, `icon`, `store_id`, `parent_id`, `item_name_en`, `name_ru`, `item_name_ru`, `description_ru`) VALUES
(1, 'user', 'Users', '', 'Users', '', 'component.user', 36, 'User', NULL, NULL, NULL),
(19, 'text', 'Text', '', 'Basic', '', 'component.text', 36, 'text', NULL, NULL, NULL),
(23, 'page', 'Pages', '', 'Basic', '', NULL, 36, 'page', NULL, NULL, NULL),
(24, 'section', 'Navigation', '', 'Basic', '', NULL, 23, 'Section', NULL, NULL, NULL),
(36, 'content', 'Content', '', 'Basic', '', NULL, 0, 'Content', 'Контент', 'Контент', NULL),
(46, 'travel_route', 'Tours', '', 'Travel', '', NULL, 23, 'Tour', NULL, NULL, NULL),
(47, 'gallery', 'Image galleries', '', 'Gallery', '', NULL, 23, 'Gallery', NULL, NULL, NULL),
(48, 'photo', 'Image', '', 'Gallery', '', NULL, 36, 'image', NULL, NULL, NULL),
(49, 'publication', 'Publications', NULL, 'Basic', '', NULL, 23, 'Publication', NULL, NULL, NULL),
(50, 'comment', 'Comment', NULL, 'Blog', '', NULL, 36, 'comment', NULL, NULL, NULL),
(58, 'faq', 'FAQ', NULL, 'Basic', '', NULL, 23, 'FAQ', NULL, NULL, NULL),
(59, 'video', 'Video', NULL, 'Basic', '', NULL, 36, 'Video', NULL, NULL, NULL),
(60, 'award', 'Award', '', 'Basic', '', NULL, 23, 'Award', NULL, NULL, NULL),
(61, 'company', 'Company', NULL, 'Basic', '', NULL, 23, 'Company', NULL, NULL, NULL),
(62, 'project', 'Project', NULL, 'Basic', '', NULL, 23, 'Project', NULL, NULL, NULL),
(63, 'vacancy', 'Vacancy', NULL, 'Basic', '', NULL, 23, 'Vacancy', NULL, NULL, NULL),
(64, 'classifier', 'Classifier', '', 'Basic', '', NULL, 23, 'Classifier', NULL, NULL, NULL),
(65, 'classifier_linker', 'Classifier Linker', NULL, 'Basic', '', NULL, 36, 'Classifier Linker', NULL, NULL, NULL),
(68, 'news', 'News', NULL, 'Basic', '', NULL, 49, 'News', NULL, NULL, NULL),
(69, 'person', 'Person', NULL, 'Basic', '', NULL, 23, 'Person', NULL, NULL, NULL),
(70, 'contact', 'Contact', NULL, 'Basic', '', NULL, 36, 'Contact', NULL, NULL, NULL),
(71, 'complex_photo', 'Complex Photo', '', 'Basic', '', NULL, 49, 'Complex Photo', NULL, NULL, NULL),
(73, 'complex_video', 'Complex Video', '', 'Basic', '', NULL, 49, 'Complex Video', NULL, NULL, NULL),
(75, 'product', 'Product', NULL, 'Basic', '', NULL, 23, 'Product', NULL, NULL, NULL),
(76, 'product_category', 'Product Category', NULL, 'Basic', '', NULL, 64, 'Product Category', NULL, NULL, NULL),
(77, 'select_linker', 'Select Linker', NULL, 'Basic', '', NULL, 36, 'select_linker', NULL, NULL, NULL),
(78, 'tag', 'Tag', NULL, 'Basic', '', NULL, 64, 'Tag', NULL, NULL, NULL),
(79, 'social_icon', 'Social icon', NULL, 'Basic', '', NULL, 36, 'social_icon', NULL, NULL, NULL);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=47 AUTO_INCREMENT=2763 ;

--
-- Дамп данных таблицы `fx_content`
--

INSERT INTO `fx_content` (`id`, `priority`, `checked`, `created`, `last_updated`, `user_id`, `type`, `infoblock_id`, `site_id`, `parent_id`) VALUES
(2, 0, 0, '2012-05-24 12:42:50', '2013-04-29 12:01:11', 0, 'section', 0, 1, 0),
(3, 7, 0, '2012-05-24 12:42:50', '2013-04-29 12:01:11', 0, 'section', 0, 1, 2),
(16, 5, 1, '2012-05-28 12:27:15', '2013-05-17 21:14:38', 0, 'section', 3, 1, 2),
(112, 0, 1, '2013-04-24 14:12:36', '2013-04-29 12:01:11', 99, 'text', 16, 1, 2),
(1883, 0, 1, '2013-06-08 17:03:02', '2013-06-08 09:03:02', 99, 'page', 0, 15, NULL),
(1887, 3, 1, '2013-06-10 02:17:40', '2013-07-14 03:19:45', 99, 'section', 69, 15, 1883),
(1891, 1, 1, '2013-06-10 11:38:10', '2013-08-21 17:35:44', 99, 'travel_route', 70, 15, 1883),
(1892, 2, 1, '2013-06-10 12:17:59', '2013-08-21 17:35:44', 99, 'travel_route', 70, 15, 1883),
(1898, 3, 1, '2013-06-11 13:15:18', '2014-02-23 10:13:42', 99, 'section', 76, 15, 1883),
(1899, 1, 1, '2013-06-11 13:16:36', '2014-02-23 10:13:42', 99, 'section', 76, 15, 1883),
(1900, 2, 1, '2013-06-11 13:17:27', '2014-02-23 10:13:42', 99, 'section', 76, 15, 1883),
(1901, 4, 1, '2013-06-11 13:17:47', '2013-06-11 05:17:53', 99, 'section', 76, 15, 1883),
(1902, 0, 1, '2013-06-13 01:24:02', '2013-06-12 17:24:02', 99, 'section', 77, 15, 1887),
(1903, 0, 1, '2013-06-13 01:24:43', '2013-06-12 17:24:43', 99, 'text', 74, 15, 1887),
(1910, 0, 1, '2013-06-13 04:55:44', '2013-06-12 20:55:44', 99, 'text', 74, 15, 1902),
(1933, 4, 1, '2013-06-18 16:02:50', '2013-07-01 06:18:13', 99, 'tag', 83, 15, 1925),
(2028, 0, 1, '2013-07-13 17:11:06', '2013-07-13 09:11:06', 99, 'tag', 83, 15, 1925),
(2033, 0, 1, '2013-07-13 17:14:40', '2013-07-13 09:14:40', 99, 'tag', 83, 15, 1925),
(2039, 0, 1, '2013-07-14 11:20:37', '2013-07-14 03:20:37', 99, 'tag', 83, 15, 1925),
(2047, 0, 1, '2013-07-19 20:08:42', '2013-07-19 16:08:42', 99, 'text', 74, 15, 2033),
(2058, 8, 1, '2013-07-25 09:36:53', '2013-07-25 05:36:53', 99, 'photo', 121, 15, 1891),
(2059, 9, 1, '2013-08-01 20:32:28', '2013-08-01 16:32:28', 99, 'text', 104, 15, 1925),
(2062, 11, 1, '2013-08-01 20:38:01', '2013-08-01 16:38:01', 99, 'text', 75, 15, 1883),
(2067, 12, 1, '2013-08-07 01:05:56', '2013-08-06 21:05:57', 99, 'tag', 83, 15, 1925),
(2069, 13, 1, '2013-08-07 01:06:48', '2013-08-06 21:06:48', 99, 'tag', 83, 15, 1925),
(2072, 2, 1, '2013-08-07 06:56:54', '2013-09-04 15:50:05', 99, 'section', 124, 15, 1883),
(2073, 1, 1, '2013-08-07 07:07:24', '2013-09-04 15:50:05', 99, 'section', 124, 15, 1883),
(2074, 1, 1, '2013-08-07 07:08:08', '2013-08-21 15:31:40', 99, 'section', 124, 15, 2072),
(2075, 18, 1, '2013-08-07 07:30:37', '2013-08-07 03:30:37', 99, 'section', 124, 15, 2074),
(2076, 1, 1, '2013-08-07 07:31:30', '2013-09-05 14:59:14', 99, 'section', 124, 15, 2075),
(2077, 20, 1, '2013-08-07 07:34:29', '2013-08-07 03:34:29', 99, 'section', 124, 15, 2073),
(2078, 2, 1, '2013-08-07 07:35:56', '2013-08-21 15:31:40', 99, 'section', 124, 15, 2072),
(2079, 21, 1, '2013-08-07 19:57:12', '2013-08-07 15:57:12', 99, 'section', 124, 15, 2076),
(2081, 22, 1, '2013-08-07 20:05:11', '2013-08-07 16:05:11', 99, 'section', 126, 15, 2075),
(2082, 2, 1, '2013-08-21 21:15:08', '2013-09-05 14:59:14', 99, 'section', 124, 15, 2075),
(2128, 23, 1, '2013-09-25 19:03:04', '2013-09-25 15:03:04', 99, 'comment', 127, 15, 1996),
(2149, 3, 1, '2013-10-02 17:18:17', '2013-11-14 12:18:26', 99, 'section', 141, 15, 1883),
(2150, 39, 1, '2013-10-02 17:20:03', '2013-10-02 13:20:03', 99, 'news', 147, 15, 2149),
(2179, 2, 1, '2013-10-03 12:28:10', '2013-11-14 12:18:26', 99, 'section', 141, 15, 1883),
(2181, 4, 1, '2013-10-03 13:39:39', '2013-11-14 11:57:11', 99, 'section', 141, 15, 1883),
(2182, 44, 1, '2013-10-03 13:40:45', '2013-10-03 09:40:45', 99, 'complex_photo', 153, 15, 2181),
(2187, 5, 1, '2013-10-03 14:08:37', '2013-11-14 11:57:11', 99, 'section', 141, 15, 1883),
(2188, 46, 1, '2013-10-03 14:19:24', '2013-10-03 10:19:24', 99, 'complex_video', 155, 15, 2187),
(2193, 47, 1, '2013-10-03 14:20:41', '2013-10-03 10:20:41', 99, 'complex_video', 155, 15, 2187),
(2210, 51, 1, '2013-10-03 16:54:26', '2013-10-03 12:54:26', 99, 'page', 0, 16, NULL),
(2211, 52, 1, '2013-10-03 16:54:26', '2013-10-03 12:54:26', 99, 'page', 0, 16, 2210),
(2212, 53, 1, '2013-10-03 17:37:56', '2013-10-14 12:16:22', 99, 'section', 158, 16, 2210),
(2214, 55, 1, '2013-10-03 18:02:03', '2013-10-03 14:02:03', 99, 'photo', 161, 16, 2210),
(2215, 56, 1, '2013-10-03 18:02:49', '2013-10-03 14:02:49', 99, 'photo', 162, 16, 2210),
(2216, 57, 1, '2013-10-03 18:37:35', '2013-10-03 14:37:35', 99, 'photo', 163, 16, 2210),
(2217, 58, 1, '2013-10-03 18:38:25', '2013-10-03 14:38:25', 99, 'photo', 163, 16, 2210),
(2218, 59, 1, '2013-10-03 18:39:28', '2013-10-03 14:39:28', 99, 'photo', 163, 16, 2210),
(2219, 60, 1, '2013-10-03 18:40:07', '2013-10-03 14:40:07', 99, 'photo', 163, 16, 2210),
(2220, 61, 1, '2013-10-04 18:54:38', '2013-10-24 13:52:30', 99, 'text', 167, 16, 2210),
(2231, 71, 1, '2013-10-07 13:44:34', '2013-10-07 09:44:34', 99, 'photo', 172, 16, 2210),
(2233, 1, 1, '2013-10-07 14:52:19', '2013-11-27 15:35:16', 99, 'section', 158, 16, 2212),
(2234, 73, 1, '2013-10-07 15:09:53', '2013-10-07 11:09:53', 99, 'text', 177, 16, 2212),
(2235, 74, 1, '2013-10-07 15:12:36', '2013-10-07 11:12:36', 99, 'text', 178, 16, 2212),
(2236, 75, 1, '2013-10-07 15:22:19', '2013-10-07 11:22:19', 99, 'faq', 179, 16, 2212),
(2238, 77, 1, '2013-10-07 15:36:10', '2013-10-07 11:36:10', 99, 'person', 182, 16, 2233),
(2239, 78, 1, '2013-10-07 16:09:26', '2013-10-07 12:09:27', 99, 'person', 182, 16, 2233),
(2242, 2, 1, '2013-10-07 16:22:44', '2013-11-27 15:35:16', 99, 'section', 158, 16, 2212),
(2243, 82, 1, '2013-10-07 16:24:17', '2013-10-07 12:24:17', 99, 'award', 186, 16, 2242),
(2244, 83, 1, '2013-10-07 16:27:07', '2013-10-07 12:27:07', 99, 'award', 186, 16, 2242),
(2245, 3, 1, '2013-10-07 16:32:52', '2013-11-27 15:35:16', 99, 'section', 158, 16, 2212),
(2246, 85, 1, '2013-10-07 16:33:24', '2013-10-07 12:33:24', 99, 'text', 189, 16, 2245),
(2247, 86, 1, '2013-10-07 16:36:42', '2013-10-07 12:36:42', 99, 'contact', 0, 16, 2238),
(2248, 4, 1, '2013-10-07 16:39:07', '2013-11-27 15:35:16', 99, 'section', 158, 16, 2212),
(2249, 88, 1, '2013-10-07 17:04:41', '2013-10-07 13:04:41', 99, 'vacancy', 193, 16, 2248),
(2250, 89, 1, '2013-10-07 17:09:43', '2013-10-07 13:09:43', 99, 'vacancy', 193, 16, 2248),
(2251, 5, 1, '2013-10-07 17:25:17', '2013-11-27 15:35:16', 99, 'section', 158, 16, 2212),
(2253, 92, 1, '2013-10-07 17:36:20', '2013-10-07 13:36:20', 99, 'project', 197, 16, 2251),
(2254, 93, 1, '2013-10-07 17:51:02', '2013-10-14 12:16:24', 99, 'section', 158, 16, 2210),
(2256, 2, 1, '2013-10-07 18:07:44', '2013-11-11 10:28:01', 99, 'news', 203, 16, 2254),
(2257, 96, 1, '2013-10-07 18:20:02', '2013-10-14 12:16:26', 99, 'section', 158, 16, 2210),
(2258, 97, 1, '2013-10-07 18:22:48', '2013-10-07 14:22:48', 99, 'product', 207, 16, 2257),
(2259, 98, 1, '2013-10-07 18:25:42', '2013-10-07 14:25:42', 99, 'product', 209, 16, 2257),
(2260, 99, 1, '2013-10-07 18:26:37', '2013-10-07 14:26:37', 99, 'product', 209, 16, 2257),
(2317, 1, 1, '2013-10-08 15:22:44', '2013-11-25 10:37:41', 99, 'product_category', 219, 16, 2257),
(2318, 123, 1, '2013-10-08 15:22:44', '2013-10-08 11:22:45', 99, 'classifier_linker', 0, 16, 2259),
(2319, 2, 1, '2013-10-08 15:23:17', '2013-11-25 10:37:41', 99, 'product_category', 219, 16, 2257),
(2320, 124, 1, '2013-10-08 15:23:17', '2013-10-08 11:23:18', 99, 'classifier_linker', 0, 16, 2260),
(2326, 125, 1, '2013-10-08 17:15:18', '2013-10-08 13:15:18', 99, 'project', 197, 16, 2251),
(2328, 127, 1, '2013-10-08 17:23:11', '2013-10-08 13:23:11', 99, 'product', 209, 16, 2257),
(2332, 130, 1, '2013-10-14 16:56:25', '2013-10-14 12:56:25', 99, 'product_category', 0, 16, NULL),
(2338, 3, 1, '2013-10-14 17:08:22', '2013-11-25 10:37:24', 99, 'product_category', 219, 16, 2257),
(2340, 134, 1, '2013-10-15 14:15:34', '2013-10-15 10:15:34', 99, 'project', 197, 16, 2251),
(2367, 135, 1, '2013-10-21 15:21:23', '2013-10-21 11:21:23', 99, 'user', 0, 16, NULL),
(2410, 140, 1, '2013-11-11 13:36:59', '2013-11-11 09:36:59', 2367, 'product', 209, 16, 2257),
(2455, 144, 1, '2013-11-12 16:22:45', '2013-11-12 12:22:45', 2367, 'news', 203, 16, 2254),
(2463, 145, 1, '2013-11-12 17:58:38', '2013-11-12 13:58:38', 2367, 'contact', 0, 16, 2238),
(2491, 147, 1, '2013-11-19 08:17:56', '2013-11-19 04:17:56', 2367, 'select_linker', 261, 15, 1883),
(2493, 148, 1, '2013-11-20 16:51:15', '2013-11-20 12:51:15', 2367, 'video', 262, 16, 2254),
(2495, 0, 1, '2013-11-20 17:13:09', '2013-11-20 13:13:09', 2367, 'select_linker', 263, 16, 0),
(2496, 0, 1, '2013-11-20 17:13:31', '2013-11-20 13:13:31', 2367, 'select_linker', 263, 16, 2254),
(2499, 150, 1, '2013-11-27 17:42:55', '2013-11-27 13:42:55', 2367, 'news', 261, 16, 2254),
(2500, 151, 1, '2013-11-27 17:46:59', '2013-11-27 13:46:59', 2367, 'news', 262, 16, 2254),
(2501, 152, 1, '2013-12-09 17:51:13', '2013-12-09 13:51:13', 2367, 'page', 0, 17, NULL),
(2502, 153, 1, '2013-12-09 17:51:13', '2013-12-09 13:51:13', 2367, 'page', 0, 17, 2501),
(2510, 1, 1, '2013-12-11 12:32:28', '2013-12-13 13:06:39', 2367, 'section', 284, 17, 2501),
(2516, 0, 1, '2013-12-11 12:44:36', '2013-12-11 08:44:36', 2367, 'select_linker', 286, 17, 2501),
(2517, 1, 1, '2013-12-11 12:44:36', '2013-12-11 08:44:36', 2367, 'select_linker', 286, 17, 2501),
(2518, 2, 1, '2013-12-11 12:44:36', '2013-12-11 08:44:36', 2367, 'select_linker', 286, 17, 2501),
(2519, 3, 1, '2013-12-11 12:44:36', '2013-12-11 08:44:36', 2367, 'select_linker', 286, 17, 2501),
(2520, 4, 1, '2013-12-11 12:44:36', '2013-12-11 08:44:36', 2367, 'select_linker', 286, 17, 2501),
(2521, 5, 1, '2013-12-11 12:44:36', '2013-12-11 08:44:36', 2367, 'select_linker', 286, 17, 2501),
(2534, 169, 1, '2013-12-11 16:00:53', '2013-12-11 12:00:53', 2367, 'tag', 0, 17, NULL),
(2537, 171, 1, '2013-12-11 16:02:30', '2013-12-11 12:02:30', 2367, 'product_category', 0, 17, NULL),
(2551, 1, 1, '2013-12-12 18:19:16', '2013-12-12 14:19:56', 2367, 'product_category', 303, 17, 2510),
(2552, 2, 1, '2013-12-12 18:19:37', '2013-12-12 14:19:56', 2367, 'product_category', 303, 17, 2510),
(2553, 3, 1, '2013-12-12 18:19:47', '2013-12-12 14:19:56', 2367, 'product_category', 303, 17, 2510),
(2562, 3, 1, '2013-12-12 19:18:31', '2013-12-13 13:06:41', 2367, 'section', 284, 17, 2501),
(2567, 2, 1, '2013-12-13 15:48:54', '2013-12-13 13:06:41', 2367, 'section', 284, 17, 2501),
(2568, 1, 1, '2013-12-13 15:49:59', '2013-12-13 11:50:29', 2367, 'tag', 315, 17, 2567),
(2569, 2, 1, '2013-12-13 15:50:16', '2013-12-13 11:50:29', 2367, 'tag', 315, 17, 2567),
(2570, 3, 1, '2013-12-13 15:50:24', '2013-12-13 11:50:29', 2367, 'tag', 315, 17, 2567),
(2571, 4, 1, '2013-12-13 15:55:49', '2013-12-24 13:40:40', 2367, 'news', 316, 17, 2567),
(2572, 2, 1, '2013-12-13 15:59:20', '2013-12-24 13:40:40', 2367, 'news', 316, 17, 2567),
(2573, 3, 1, '2013-12-13 16:00:55', '2013-12-24 13:40:40', 2367, 'news', 316, 17, 2567),
(2574, 1, 1, '2013-12-13 16:01:20', '2013-12-24 13:40:40', 2367, 'news', 316, 17, 2567),
(2580, 186, 1, '2013-12-13 16:05:39', '2013-12-13 12:05:39', 2367, 'classifier_linker', 315, 17, 2573),
(2581, 187, 1, '2013-12-13 16:05:44', '2013-12-13 12:05:44', 2367, 'classifier_linker', 315, 17, 2574),
(2590, 194, 1, '2013-12-13 17:06:22', '2013-12-13 13:06:22', 2367, 'text', 324, 17, 2562),
(2591, 2, 1, '2013-12-13 17:06:59', '2013-12-23 11:58:57', 2367, 'section', 284, 17, 2562),
(2592, 196, 1, '2013-12-13 17:20:35', '2013-12-13 13:20:35', 2367, 'person', 325, 17, 2591),
(2593, 1, 1, '2013-12-13 18:13:10', '2013-12-23 11:58:07', 2367, 'section', 284, 17, 2562),
(2594, 1, 1, '2013-12-13 18:14:46', '2013-12-23 11:52:01', 2367, 'vacancy', 327, 17, 2593),
(2595, 2, 1, '2013-12-13 18:15:47', '2013-12-23 11:52:01', 2367, 'vacancy', 327, 17, 2593),
(2596, 4, 1, '2013-12-13 18:26:33', '2013-12-23 13:01:59', 2367, 'section', 284, 17, 2562),
(2597, 201, 1, '2013-12-13 18:27:51', '2013-12-13 14:27:51', 2367, 'project', 331, 17, 2596),
(2598, 3, 1, '2013-12-13 18:50:09', '2013-12-23 13:01:59', 2367, 'section', 284, 17, 2562),
(2599, 203, 1, '2013-12-13 18:51:00', '2013-12-13 14:51:00', 2367, 'award', 333, 17, 2598),
(2600, 204, 1, '2013-12-16 13:17:37', '2013-12-16 09:17:37', 2367, 'contact', 0, 17, 2592),
(2601, 205, 1, '2013-12-16 14:09:00', '2013-12-16 10:09:00', 2367, 'section', 335, 17, 2567),
(2602, 206, 1, '2013-12-16 14:09:11', '2013-12-16 10:09:11', 2367, 'section', 335, 17, 2567),
(2616, 207, 1, '2013-12-17 18:12:14', '2013-12-17 14:12:14', 2367, 'social_icon', 338, 17, 2602),
(2617, 208, 1, '2013-12-17 18:13:01', '2013-12-17 14:13:01', 2367, 'social_icon', 338, 17, 2602),
(2622, 209, 1, '2013-12-19 11:08:23', '2013-12-19 07:08:23', 2367, 'photo', 311, 17, 2562),
(2623, 0, 1, '2014-01-20 15:00:49', '2014-01-20 11:00:49', 2367, 'select_linker', 339, 17, 2501),
(2624, 210, 1, '2014-01-20 15:45:05', '2014-01-20 11:45:05', 2367, 'news', 316, 17, 2567),
(2625, 0, 1, '2014-01-20 15:53:41', '2014-01-20 11:53:41', 2367, 'select_linker', 342, 17, 2501),
(2626, 1, 1, '2014-01-20 16:45:17', '2014-01-20 12:45:17', 2367, 'select_linker', 342, 17, 2501),
(2627, 211, 1, '2014-01-20 17:41:07', '2014-01-20 13:41:07', 2367, 'product', 344, 17, 2510),
(2628, 212, 1, '2014-01-20 17:42:06', '2014-01-20 13:42:06', 2367, 'classifier_linker', 303, 17, 2627),
(2629, 213, 1, '2014-01-20 17:45:05', '2014-01-20 13:45:05', 2367, 'classifier_linker', 303, 17, 2627),
(2630, 0, 1, '2014-01-20 17:54:47', '2014-01-20 13:54:47', 2367, 'select_linker', 323, 17, 2510),
(2631, 214, 1, '2014-01-20 17:55:47', '2014-01-20 13:55:47', 2367, 'product', 344, 17, 2510),
(2632, 214, 1, '2014-01-20 17:55:47', '2014-01-20 13:55:47', 2367, 'classifier_linker', 303, 17, 2631),
(2633, 215, 1, '2014-01-20 17:57:00', '2014-01-20 13:57:00', 2367, 'product', 344, 17, 2510),
(2634, 215, 1, '2014-01-20 17:57:00', '2014-01-20 13:57:01', 2367, 'classifier_linker', 303, 17, 2633),
(2635, 216, 1, '2014-01-28 11:39:50', '2014-01-28 07:39:50', 2367, 'page', 0, 18, NULL),
(2636, 217, 1, '2014-01-28 11:39:50', '2014-01-28 07:39:50', 2367, 'page', 0, 18, 2635),
(2638, 1, 1, '2014-01-28 12:04:17', '2014-01-30 09:26:37', 2367, 'section', 346, 18, 2635),
(2639, 2, 1, '2014-01-28 12:04:33', '2014-01-30 09:26:37', 2367, 'section', 346, 18, 2635),
(2640, 4, 1, '2014-01-28 12:07:04', '2014-02-12 15:58:05', 2367, 'section', 346, 18, 2635),
(2641, 3, 1, '2014-01-28 12:07:17', '2014-02-12 15:58:05', 2367, 'section', 346, 18, 2635),
(2642, 223, 1, '2014-01-28 12:12:50', '2014-01-28 08:12:50', 2367, 'social_icon', 348, 18, 2635),
(2652, 1, 1, '2014-01-30 13:34:21', '2014-01-30 10:29:05', 2367, 'section', 346, 18, 2638),
(2654, 2, 1, '2014-01-30 13:34:34', '2014-03-03 08:20:17', 2367, 'section', 346, 18, 2638),
(2655, 1, 1, '2014-01-30 13:38:14', '2014-02-13 09:37:55', 2367, 'section', 346, 18, 2640),
(2656, 3, 1, '2014-01-30 13:38:26', '2014-02-13 09:37:55', 2367, 'section', 346, 18, 2640),
(2657, 2, 1, '2014-01-30 13:38:46', '2014-02-13 09:37:55', 2367, 'section', 346, 18, 2640),
(2658, 3, 1, '2014-01-30 14:00:50', '2014-03-03 10:06:02', 2367, 'section', 346, 18, 2638),
(2659, 0, 1, '2014-01-30 14:07:10', '2014-01-30 10:07:10', 2367, 'select_linker', 362, 18, 2638),
(2660, 230, 1, '2014-01-30 14:38:47', '2014-01-30 10:38:47', 2367, 'product', 364, 18, 2652),
(2661, 231, 1, '2014-01-30 14:40:14', '2014-01-30 10:40:14', 2367, 'product', 364, 18, 2652),
(2662, 232, 1, '2014-01-30 14:42:35', '2014-01-30 10:42:35', 2367, 'product', 364, 18, 2652),
(2668, 3, 1, '2014-01-30 15:08:08', '2014-03-12 01:06:29', 2367, 'select_linker', 370, 18, 2635),
(2671, 2, 1, '2014-01-30 15:14:27', '2014-03-11 12:10:22', 2367, 'person', 372, 18, 2655),
(2672, 233, 1, '2014-01-30 15:14:27', '2014-01-30 11:14:27', 2367, 'contact', 0, 18, 2671),
(2673, 3, 1, '2014-01-30 15:19:09', '2014-03-11 12:10:22', 2367, 'person', 372, 18, 2655),
(2674, 234, 1, '2014-01-30 15:19:09', '2014-01-30 11:19:10', 2367, 'contact', 0, 18, 2673),
(2675, 1, 1, '2014-01-30 15:19:48', '2014-03-11 12:10:22', 2367, 'person', 372, 18, 2655),
(2676, 235, 1, '2014-01-30 15:19:48', '2014-01-30 11:19:48', 2367, 'contact', 0, 18, 2675),
(2677, 236, 1, '2014-01-30 15:33:49', '2014-01-30 11:33:49', 2367, 'vacancy', 374, 18, 2656),
(2678, 237, 1, '2014-01-30 15:37:21', '2014-01-30 11:37:21', 2367, 'news', 379, 18, 2657),
(2679, 238, 1, '2014-01-30 15:38:00', '2014-01-30 11:38:00', 2367, 'news', 379, 18, 2657),
(2680, 239, 1, '2014-01-30 15:39:49', '2014-01-30 11:39:49', 2367, 'news', 379, 18, 2657),
(2681, 240, 1, '2014-01-30 15:40:08', '2014-01-30 11:40:08', 2367, 'news', 379, 18, 2657),
(2682, 1, 1, '2014-01-30 15:41:44', '2014-02-12 15:33:33', 2367, 'select_linker', 381, 18, 2635),
(2683, 2, 1, '2014-01-30 15:41:44', '2014-02-12 15:33:33', 2367, 'select_linker', 381, 18, 2635),
(2684, 0, 1, '2014-01-30 15:41:45', '2014-02-12 15:33:33', 2367, 'select_linker', 381, 18, 2635),
(2688, 2, 1, '2014-01-30 16:20:08', '2014-02-12 15:57:27', 2367, 'project', 385, 18, 2639),
(2689, 242, 1, '2014-01-30 16:24:00', '2014-01-30 12:24:00', 2367, 'project', 385, 18, 2688),
(2690, 1, 1, '2014-01-30 16:25:46', '2014-02-12 15:57:27', 2367, 'project', 385, 18, 2639),
(2691, 0, 1, '2014-01-30 17:04:01', '2014-01-30 13:04:01', 2367, 'select_linker', 387, 18, 2635),
(2692, 244, 1, '2014-01-31 18:14:59', '2014-01-31 14:14:59', 2367, 'photo', 389, 18, 2688),
(2693, 245, 1, '2014-01-31 18:16:05', '2014-01-31 14:16:05', 2367, 'photo', 389, 18, 2688),
(2694, 0, 1, '2014-01-31 18:27:50', '2014-01-31 14:27:50', 2367, 'select_linker', 391, 18, 2640),
(2698, 249, 1, '2014-01-31 19:03:51', '2014-01-31 15:03:51', 2367, 'text', 396, 18, 2641),
(2700, 1, 1, '2014-01-31 19:09:29', '2014-01-31 15:17:18', 2367, 'text', 397, 18, 2641),
(2701, 2, 1, '2014-01-31 19:09:46', '2014-01-31 15:17:18', 2367, 'text', 397, 18, 2641),
(2703, 250, 1, '2014-02-13 13:49:35', '2014-02-13 09:49:35', 2367, 'text', 401, 18, 2660),
(2704, 251, 1, '2014-02-13 13:51:34', '2014-02-13 09:51:34', 2367, 'text', 401, 18, 2661),
(2717, 253, 1, '2014-02-28 15:21:33', '2014-02-28 11:21:33', 2367, 'tag', 315, 17, 2567),
(2721, 255, 1, '2014-02-28 17:23:02', '2014-02-28 13:23:02', 2367, 'contact', 0, 17, 2572),
(2722, 256, 1, '2014-02-28 17:23:14', '2014-02-28 13:23:14', 2367, 'contact', 0, 17, 2572),
(2723, 257, 1, '2014-02-28 17:54:51', '2014-02-28 13:54:51', 2367, 'classifier_linker', 0, 17, 2572),
(2724, 258, 1, '2014-03-02 12:31:49', '2014-03-02 08:31:49', 2367, 'classifier_linker', 0, 17, 2573),
(2725, 258, 1, '2014-03-02 12:31:49', '2014-03-02 08:31:49', 2367, 'classifier_linker', 0, 17, 2573),
(2726, 258, 1, '2014-03-02 12:31:49', '2014-03-02 08:31:49', 2367, 'contact', 0, 17, 2573),
(2727, 259, 1, '2014-03-02 12:34:09', '2014-03-02 08:34:10', 2367, 'classifier_linker', 0, 17, 2572),
(2728, 260, 1, '2014-03-07 18:51:44', '2014-03-07 14:51:44', 2367, 'tag', 0, 18, NULL),
(2729, 260, 1, '2014-03-07 18:51:44', '2014-03-07 14:51:44', 2367, 'classifier_linker', 0, 18, 2678),
(2730, 261, 1, '2014-03-08 07:08:29', '2014-03-08 03:08:29', 2367, 'classifier_linker', 0, 18, 2679),
(2731, 261, 1, '2014-03-08 07:08:29', '2014-03-08 03:08:30', 2367, 'tag', 0, 18, NULL),
(2732, 261, 1, '2014-03-08 07:08:29', '2014-03-08 03:08:30', 2367, 'classifier_linker', 0, 18, 2679),
(2733, 262, 1, '2014-03-11 15:59:01', '2014-03-11 11:59:01', 2367, 'tag', 0, 18, NULL),
(2734, 262, 1, '2014-03-11 15:59:01', '2014-03-11 11:59:01', 2367, 'classifier_linker', 0, 18, 2681),
(2735, 263, 1, '2014-03-11 16:15:11', '2014-03-11 12:15:12', 2367, 'person', 372, 18, 2655),
(2736, 263, 1, '2014-03-11 16:15:12', '2014-03-11 12:15:12', 2367, 'contact', 0, 18, 2735),
(2737, 264, 1, '2014-03-11 17:08:49', '2014-03-11 13:08:49', 2367, 'vacancy', 374, 18, 2656),
(2739, 265, 1, '2014-03-11 21:44:50', '2014-03-11 17:44:50', 2367, 'product', 364, 18, 2652),
(2740, 266, 1, '2014-03-11 21:49:15', '2014-03-11 17:49:15', 2367, 'product', 364, 18, 2654),
(2741, 267, 1, '2014-03-11 21:50:58', '2014-03-11 17:50:58', 2367, 'product', 364, 18, 2654),
(2742, 268, 1, '2014-03-11 21:56:32', '2014-03-11 17:56:32', 2367, 'product', 364, 18, 2658),
(2743, 269, 1, '2014-03-11 21:58:20', '2014-03-11 17:58:20', 2367, 'product', 364, 18, 2658),
(2744, 4, 1, '2014-03-11 21:59:51', '2014-03-15 07:11:41', 2367, 'section', 346, 18, 2638),
(2745, 271, 1, '2014-03-11 22:01:34', '2014-03-11 18:01:34', 2367, 'product', 364, 18, 2744),
(2746, 272, 1, '2014-03-11 22:02:45', '2014-03-11 18:02:45', 2367, 'product', 364, 18, 2744),
(2747, 1, 1, '2014-03-12 05:01:05', '2014-03-12 01:01:05', 2367, 'select_linker', 370, 18, 2635),
(2748, 2, 1, '2014-03-12 05:01:05', '2014-03-12 01:01:05', 2367, 'select_linker', 370, 18, 2635),
(2749, 0, 1, '2014-03-12 05:02:10', '2014-03-12 01:02:10', 2367, 'select_linker', 369, 18, 2635),
(2750, 273, 1, '2014-03-14 14:09:54', '2014-03-14 10:09:54', 2367, 'photo', 389, 18, 2688),
(2751, 274, 1, '2014-03-14 18:24:30', '2014-03-14 14:24:30', 2367, 'project', 385, 18, 2639),
(2752, 275, 1, '2014-03-14 18:26:57', '2014-03-14 14:26:57', 2367, 'photo', 389, 18, 2751),
(2753, 276, 1, '2014-03-14 18:27:36', '2014-03-14 14:27:36', 2367, 'photo', 389, 18, 2751),
(2754, 277, 1, '2014-03-14 18:28:13', '2014-03-14 14:28:13', 2367, 'photo', 389, 18, 2751),
(2755, 278, 1, '2014-03-14 18:31:09', '2014-03-14 14:31:09', 2367, 'photo', 389, 18, 2690),
(2756, 279, 1, '2014-03-14 18:31:52', '2014-03-14 14:31:52', 2367, 'photo', 389, 18, 2690),
(2757, 280, 1, '2014-03-14 18:34:09', '2014-03-14 14:34:09', 2367, 'project', 385, 18, 2639),
(2762, 281, 1, '2014-03-15 11:34:46', '2014-03-15 07:34:46', 2367, 'project', 385, 18, 2639);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_award`
--

CREATE TABLE IF NOT EXISTS `fx_content_award` (
  `id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text,
  `year` int(11) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_award`
--

INSERT INTO `fx_content_award` (`id`, `image`, `description`, `year`, `short_description`) VALUES
(2243, '', 'Stet clita kasd gubergren, no sea takimata sanctus est.. Lorem ipsum dolor sit amet. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat.\r\n\r\nLorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.\r\n\r\nLorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua.\r\n\r\nLorem ipsum dolor sit amet. Sanctus sea sed takimata ut vero voluptua. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.\r\n\r\nLorem ipsum dolor sit amet. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. At vero eos et accusam et justo duo dolores et ea rebum.', 2012, 'Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.'),
(2244, '', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.\r\n\r\nLorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum.\r\n\r\nSanctus sea sed takimata ut vero voluptua. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.\r\n\r\nDuis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. At vero eos et accusam et justo duo dolores et ea rebum. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.\r\n\r\nLorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. At vero eos et accusam et justo duo dolores et ea rebum. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 2003, 'Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis. Lorem ipsum dolor sit amet, consectetuer adipiscing elit.'),
(2599, '', 'Sanctus sea sed takimata ut vero voluptua. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.\r\n\r\nQuis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sanctus sea sed takimata ut vero voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.\r\n\r\nExcepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. Sanctus sea sed takimata ut vero voluptua. At vero eos et accusam et justo duo dolores et ea rebum.\r\n\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. Sanctus sea sed takimata ut vero voluptua.\r\n\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 2000, 'At vero eos et');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_classifier`
--

CREATE TABLE IF NOT EXISTS `fx_content_classifier` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_classifier`
--

INSERT INTO `fx_content_classifier` (`id`) VALUES
(2317),
(2319),
(2332),
(2338),
(2534),
(2537),
(2551),
(2552),
(2553),
(2568),
(2569),
(2570),
(2717),
(2728),
(2731),
(2733);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_classifier_linker`
--

CREATE TABLE IF NOT EXISTS `fx_content_classifier_linker` (
  `id` int(11) NOT NULL,
  `classifier_id` int(11) DEFAULT NULL,
  `content_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_classifier_linker`
--

INSERT INTO `fx_content_classifier_linker` (`id`, `classifier_id`, `content_id`) VALUES
(2318, 2317, 2259),
(2320, 2319, 2260),
(2580, 2570, 2573),
(2581, 2569, 2574),
(2628, 2552, 2627),
(2629, 2537, 2627),
(2632, 2551, 2631),
(2634, 2552, 2633),
(2723, 2717, 2572),
(2724, 2569, 2573),
(2725, 2717, 2573),
(2727, 2570, 2572),
(2729, 2728, 2678),
(2730, 2728, 2679),
(2732, 2731, 2679),
(2734, 2733, 2681);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_comment`
--

CREATE TABLE IF NOT EXISTS `fx_content_comment` (
  `id` int(11) NOT NULL,
  `comment_text` text,
  `publish_date` datetime DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `is_moderated` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_comment`
--

INSERT INTO `fx_content_comment` (`id`, `comment_text`, `publish_date`, `user_name`, `is_moderated`) VALUES
(2128, 'asdasd', '2013-00-00 00:00:00', 'asdasd', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_company`
--

CREATE TABLE IF NOT EXISTS `fx_content_company` (
  `id` int(11) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_complex_photo`
--

CREATE TABLE IF NOT EXISTS `fx_content_complex_photo` (
  `id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_complex_photo`
--

INSERT INTO `fx_content_complex_photo` (`id`, `image`, `description`) VALUES
(2182, '429', '<p>asdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasddfasdfasdf</p>');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_complex_video`
--

CREATE TABLE IF NOT EXISTS `fx_content_complex_video` (
  `id` int(11) NOT NULL,
  `embed_html` text,
  `description` text,
  `tags` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_complex_video`
--

INSERT INTO `fx_content_complex_video` (`id`, `embed_html`, `description`, `tags`) VALUES
(2188, '<iframe width="560" height="315" src="//www.youtube.com/embed/aIbpt1aDFqM" frameborder="0" allowfullscreen></iframe>', 'asasdfasdfasdasdf', NULL),
(2193, '<iframe width="420" height="315" src="//www.youtube.com/embed/hyvFBqlCiCw" frameborder="0" allowfullscreen></iframe>', 'asdgsdfgdfgdfgsdfg', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_contact`
--

CREATE TABLE IF NOT EXISTS `fx_content_contact` (
  `id` int(11) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `contact_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_contact`
--

INSERT INTO `fx_content_contact` (`id`, `value`, `contact_type`) VALUES
(2247, '+7918687162', 'Phone'),
(2463, '5252525', 'ICQ'),
(2600, '23442667', 'Phone'),
(2672, '', ''),
(2674, '', ''),
(2676, '', ''),
(2721, 'jabber', 'dubr.cola'),
(2722, 'phone', '505349'),
(2726, '', ''),
(2736, '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_faq`
--

CREATE TABLE IF NOT EXISTS `fx_content_faq` (
  `id` int(11) NOT NULL,
  `question` varchar(255) DEFAULT NULL,
  `answer` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_faq`
--

INSERT INTO `fx_content_faq` (`id`, `question`, `answer`) VALUES
(2236, 'Stet clita kasd gubergren?', '<p>Lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.&nbsp;</p>');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_gallery`
--

CREATE TABLE IF NOT EXISTS `fx_content_gallery` (
  `id` int(11) NOT NULL,
  `publish_date` datetime DEFAULT NULL,
  `cover` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_news`
--

CREATE TABLE IF NOT EXISTS `fx_content_news` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_news`
--

INSERT INTO `fx_content_news` (`id`) VALUES
(2150),
(2256),
(2455),
(2499),
(2500),
(2571),
(2572),
(2573),
(2574),
(2624),
(2678),
(2679),
(2680),
(2681);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_page`
--

CREATE TABLE IF NOT EXISTS `fx_content_page` (
  `id` int(11) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `comments_counter` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=62;

--
-- Дамп данных таблицы `fx_content_page`
--

INSERT INTO `fx_content_page` (`id`, `url`, `name`, `title`, `comments_counter`) VALUES
(2, '/', 'Main page', '', NULL),
(3, '/404/', '404', NULL, NULL),
(16, '/contacts/', 'Contacts', NULL, NULL),
(1883, '/', 'Jeep Travels', 'Jeep Travels: super travels!', NULL),
(1887, '/about', 'About', NULL, NULL),
(1891, '/summer-rally', 'Mangyshlak: The Great Step', '', NULL),
(1892, '/paris-dakar', 'Argentina – Chilix', '', NULL),
(1898, 'http://facebook.com/', 'Facebook', NULL, NULL),
(1899, 'http://plus.google.com/', 'Google+', NULL, NULL),
(1900, 'http://instagram.com', 'Instagram', NULL, NULL),
(1901, 'http://youtube.com', 'YouTube', NULL, NULL),
(1902, '/test', 'Take part in our events', NULL, NULL),
(1933, '/tag-ivan-kurochkin', 'John Kurochkin', 'News about John', NULL),
(2028, '/lulz', '#lulz', NULL, NULL),
(2033, '/funeral-2', 'aero funeral', NULL, NULL),
(2039, '/dominikana-666', 'Dominikana', NULL, NULL),
(2067, '/agro', 'agriculture', NULL, NULL),
(2069, '/girlz', 'agirlz', NULL, NULL),
(2072, '/Top-1', 'Top 1', '', NULL),
(2073, '/Top-2', 'Top 2', '', NULL),
(2074, '/Sub-1', 'Sub alt branch', '', NULL),
(2075, '/Sub-sub-1', 'Sub sub 1', '', NULL),
(2076, '/Sub-very-deep-1', 'Oops?', '', NULL),
(2077, '/Sub-2', 'Sub 2', '', NULL),
(2078, '/ALter-sub-1', 'Sub first branch', '', NULL),
(2079, '/Level-5-1', '5 level wow!', '', NULL),
(2081, '/ALter-sub-2', 'Alter menu item', '', NULL),
(2082, '/Dooops', 'Dooops', '', NULL),
(2149, '/news', 'Neaws', 'News', 0),
(2150, '/Test-3', 'Test', 'Test', 0),
(2151, '/test-4', 'test', NULL, 0),
(2153, '/sdf', 'sdf', NULL, 0),
(2155, '/asdfasdf', 'asdfasdf', NULL, 0),
(2157, '/asdf', 'asdf', NULL, 0),
(2159, '/asdfasdfasdf', 'asdfasdfasdf', NULL, 0),
(2161, '/asdfasdfasdf-2', 'asdfasdfasdf', NULL, 0),
(2163, '/asdfasdf-2', 'asdfasdf', NULL, 0),
(2165, '/tyurtyu', 'tyurtyu', NULL, 0),
(2167, '/asdfasdfasdf-3', 'asdfasdfasdf\\', NULL, 0),
(2169, '/wer', 'wer', NULL, 0),
(2171, '/gfgghf', 'gfgghf', NULL, 0),
(2173, '/ghjghj', 'ghjghj', NULL, 0),
(2175, '/ppp', 'ppp', NULL, 0),
(2179, '/Persons', 'Persons', 'Persons', 0),
(2181, '/Complex-Photos', 'Complex Photosa', 'Complex Photos', 0),
(2182, '/test-5', 'test', 'Test', 0),
(2187, '/Complex-Videos', 'Complex Videos', 'Complex Videos', 0),
(2188, '/Test-7', 'Test', 'Test', 0),
(2193, '/Testsdfsdgfdf', 'Testsdfsdgfdf', 'Test', 0),
(2210, '/', 'Cover Page', NULL, 0),
(2211, '/404', 'Page not found', NULL, 0),
(2212, '/about', 'About Us', 'About Us', 0),
(2233, '/Employees', 'Employees', 'Employees', 0),
(2236, '/Faq1', 'Faq1', 'Faq1', 0),
(2238, '/Ivan', 'Ivanov I.P.', 'Ivan', 0),
(2239, '/Stet-clita-kasd-gubergren', 'Stet clita kasd gubergren', 'Stet clita kasd gubergren', 0),
(2242, '/Awards', 'Awards', 'Awards', 0),
(2243, '/Duis-autem-vel-eum', 'Duis autem vel eum', 'Duis autem vel eum', 0),
(2244, '/Ut-wisi-enim-ad', 'Sanctus sea sed takimata', 'Ut wisi enim ad', 0),
(2245, '/Contacts', 'Contacts', 'Contacts', 0),
(2248, '/Vacancies', 'Vacancies', 'Vacancies', 0),
(2249, '/At-vero-eos-et', 'At vero eos et', 'At vero eos et', 0),
(2250, '/Quis-aute-iure', 'Quis aute iure', 'Quis aute iure', 0),
(2251, '/Projects', 'Projects', 'Projects', 0),
(2253, '/Lorem-ipsum-dolor-sit-3', 'Lorem ipsum dolor sit', 'Lorem ipsum dolor sit', 0),
(2254, '/News', 'News', 'News', 0),
(2256, '/Stet-clita-kasd-gubergren-2-main', '/b/', 'Ut wisi enim ad', 0),
(2257, '/Catalog', 'Catalog', 'Catalog', 0),
(2258, '/Ut-wisi-enim-ad-2', 'Ut wisi enim ad', 'Ut wisi enim ad', 0),
(2259, '/Stet-clita-kasd-gubergren-3', 'Stet clita kasd ', 'Stet clita kasd gubergren', 0),
(2260, '/At-vero-eos-et-2', 'At vero eos et', 'At vero eos et', 0),
(2317, '/wool-gloves', 'wool gloves', NULL, 0),
(2319, '/simple-gloves', 'simple gloves', NULL, 0),
(2326, '/Ut-wisi-enim-ad-3', 'Ut wisi enim ad', 'Ut wisi enim ad', 0),
(2328, '/Nam-liber-tempor-cum', 'Nam liber tempor cum', 'Nam liber tempor cum', 0),
(2332, '/gloves', 'gloves', NULL, 0),
(2338, '/ghfhg', 'ghfhg', NULL, 0),
(2340, '/Consetetur-sadipscing-elitr-sed', 'Consetetur sadipscing elitr sed', 'Consetetur sadipscing elitr sed', 0),
(2410, '/page-2410-html', 'Stet clita kasd', 'Stet clita kasd gubergren', 0),
(2455, '/Stet-clita-kasd-gubergren-2', 'Stet clita kasd gubergren', 'Stet clita kasd gubergren', 0),
(2499, '/Consetetur-sadipscing-elitr-sed-2', 'Consetetur sadipscing elitr sed', 'Consetetur sadipscing elitr sed', 0),
(2500, '/At-vero-eos-et-3', 'At vero eos et', 'At vero eos et', 0),
(2501, '/', 'At vero eos et 汉语 漢語', NULL, 0),
(2502, '/404', 'Page not found', NULL, 0),
(2510, '/Catalog', 'Catalog', 'Catalog', 0),
(2534, '/tag', 'tag', NULL, 0),
(2537, '/asd', 'asd', NULL, 0),
(2551, '/Lorem-ipsum-dolor-sit', 'Lorem ipsum dolor ', 'Lorem ipsum dolor sit', 0),
(2552, '/Duis-autem-vel-eum', 'Duis autem vel eum', 'Duis autem vel eum', 0),
(2553, '/Consetetur-sadipscing-elitr-sed', 'Consetetur ', 'Consetetur sadipscing elitr sed', 0),
(2562, '/About', 'About', 'About', 0),
(2567, '/News', 'News', 'News', 0),
(2568, '/At-vero-eos-et', 'At vero eos et', 'At vero eos et', 0),
(2569, '/Lorem-ipsum-dolor-sit-3', 'Lorem ipsum dolor ', 'Lorem ipsum dolor sit', 0),
(2570, '/Stet-clita-kasd-gubergren-3', 'Stet clita kasd', 'Stet clita kasd gubergren', 0),
(2571, '/Duis-autem-vel-eum-3', 'Duis autem vel eum', 'Duis autem vel eum', 0),
(2572, '/At-vero-eos-et-2', 'At vero eos et', 'At vero eos et', 0),
(2573, '/Lorem-ipsum-dolor-sit-4', 'Lorem ipsum dolor sit', 'Lorem ipsum dolor sit', 0),
(2574, '/Quis-aute-iure-reprehenderit', 'Quis aute iure reprehenderit', 'Quis aute iure reprehenderit', 0),
(2591, '/Employees', 'Employees', 'Employees', 0),
(2592, '/Lorem-ipsum-dolor-sit-2', 'Lorem ipsum dolor sit', 'Lorem ipsum dolor sit', 0),
(2593, '/Vacancies', 'Vacancies', 'Vacancies', 0),
(2594, '/Ut-wisi-enim-ad', 'Ut wisi enim ad', 'Ut wisi enim ad', 0),
(2595, '/Nam-liber-tempor-cum', 'Nam liber tempor cum', 'Nam liber tempor cum', 0),
(2596, '/Projects', 'Projects', 'Projects', 0),
(2597, '/Quis-aute-iure-reprehenderit-2', 'Quis aute iure reprehenderit', 'Quis aute iure reprehenderit', 0),
(2598, '/Awards', 'Awards', 'Awards', 0),
(2599, '/Excepteur-sint-obcaecat-cupiditat', 'Excepteur sint obcaecat cupiditat', 'Excepteur sint obcaecat cupiditat', 0),
(2601, '/last-week', 'last week', 'last week', 0),
(2602, '/last-month', 'last month', 'last month', 0),
(2624, '/Nam-liber-tempor-cum-2', 'Nam liber tempor cum', 'Nam liber tempor cum', 0),
(2627, '/Consetetur-sadipscing-elitr-sed-2', 'Consetetur sadipscing elitr sed', 'Consetetur sadipscing elitr sed', 0),
(2631, '/Duis-autem-vel-eum-2', 'Duis autem vel eum', 'Duis autem vel eum', 0),
(2633, '/Lorem-ipsum-dolor-sit-5', 'Lorem ipsum dolor sit', 'Lorem ipsum dolor sit', 0),
(2635, '/', 'Cover Page', NULL, 0),
(2636, '/404', 'Page not found', NULL, 0),
(2638, '/Catalog', 'Our services', 'Catalog', 0),
(2639, '/Projects', 'Projects', 'Projects', 0),
(2640, '/About', 'About', '', 0),
(2641, '/Contacts', 'Contacts', '', 0),
(2652, '/Sport-series', 'Sport', 'Sport series', 0),
(2654, '/people-photo', 'People', '', 0),
(2655, '/Team', 'The team', 'People', 0),
(2656, '/Vacancies', 'Vacancies', 'Vacancies', 0),
(2657, '/News', 'News', 'News', 0),
(2658, '/Landscapes', 'Landscapes', '', 0),
(2660, '/Football-photo-report', 'Football photo report', '', 0),
(2661, '/Skiing', 'Skiing', '', 0),
(2662, '/Swimming', 'Swimming', '', 0),
(2671, '/Ken-Cold', 'Ken Cold', '', 0),
(2673, '/Leila-Stoparsson', 'Leila Stoparsson', '', 0),
(2675, '/Nika-Lightman', 'Nika Lightman', '', 0),
(2677, '/Maker-up', 'Maker-up', '', 0),
(2678, '/Duis-autem-vel-eum', 'Redecoration in our new studio', 'Duis autem vel eum', 0),
(2679, '/Moscow-Streetshot-Contest', 'Moscow Streetshot Contest', '', 0),
(2680, '/Stet-clita-kasd-gubergren', 'Free ride proof pics!', 'Stet clita kasd gubergren', 0),
(2681, '/Moscow-Athletics-Championship', 'Moscow Athletics Championship', 'Moscow Athletics Championship', 0),
(2688, '/Carnival-of-miners', 'Carnival of miners', '', 0),
(2689, '/At-vero-eos-et', 'At vero eos et', 'At vero eos et', 0),
(2690, '/Cockfights', 'Cockfights', '', 0),
(2717, '/figag', 'figag', NULL, 0),
(2728, '/studio', 'studio', NULL, 0),
(2731, '/contest', 'contest', NULL, 0),
(2733, '/sport', 'sport', NULL, 0),
(2735, '/Sonya-Zoomer', 'Sonya Zoomer', '', 0),
(2737, '/Delivery-person', 'Delivery person', '', 0),
(2739, '/Athletics', 'Athletics', '', 0),
(2740, '/Portrait', 'Portrait', '', 0),
(2741, '/Passport-photos', 'Passport photos', '', 0),
(2742, '/Cities', 'Cities', '', 0),
(2743, '/Nature', 'Nature', '', 0),
(2744, '/Events', 'Events', '', 0),
(2745, '/Birthday-parties', 'Birthday parties', '', 0),
(2746, '/Corporate-events', 'Corporate events', '', 0),
(2751, '/Kupala-Night', 'Kupala Night', '', 0),
(2757, '/Bull-Easter', 'Bull Easter', '', 0),
(2762, '/Olo-proj', 'Olo proj', '', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_person`
--

CREATE TABLE IF NOT EXISTS `fx_content_person` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `description` text,
  `birthday` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_person`
--

INSERT INTO `fx_content_person` (`id`, `full_name`, `department`, `photo`, `short_description`, `description`, `birthday`) VALUES
(2238, 'Pertrovich Ivanov', 'Ecommerce', '', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.sdasd', 'Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.\r\n\r\n Sanctus sea sed takimata ut vero voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. At vero eos et accusam et justo duo dolores et ea rebum.\r\n\r\nDuis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. At vero eos et accusam et justo duo dolores et ea rebum. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. At vero eos et accusam et justo duo dolores et ea rebum.\r\n\r\nStet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.\r\n\r\nDuis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. At vero eos et accusam et justo duo dolores et ea rebum.', '2013-10-27 00:00:00'),
(2239, 'Stet clita kasd gubergren', 'Stet clita kasd gubergren', '', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu f', '<p>​At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p><p>Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum.</p><p>Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. At vero eos et accusam et justo duo dolores et ea rebum.</p><p>At vero eos et accusam et justo duo dolores et ea rebum. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum.</p><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.. Stet clita kasd gubergren, no sea takimata sanctus est. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Sanctus sea sed takimata ut vero voluptua.</p>', '1976-10-08 00:00:00'),
(2592, 'Lorem ipsum dolor sit', 'Lorem ipsum dolor sit', '', 'Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id', 'Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', '2013-12-11 00:00:00'),
(2671, 'Ken Cold', '', '/floxim_files/content/person/photo/3b_3_0.jpg', 'Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Ut enim ad minim veniam, quis nostrud exercitation ullamco la', '<p>\r\n	  Ken is romantics’ ideal – serene, sensitive, and a bit shy. When led into the wild, he blends into the nature to capture it beautifully.\r\n</p>', '2014-01-23 00:00:00'),
(2673, 'Leila Stoparsson', '', '/floxim_files/content/person/photo/3a_2_0.JPG', '', '<p>\r\n	 Leila is the best at shooting interiors. Her photographs always give you the sense of the place.\r\n</p>', '2014-01-17 00:00:00'),
(2675, 'Nika Lightman', '', '/floxim_files/content/person/photo/2b_0.JPG', 'cool portraits', '<p>\r\n	  Nika Lightman has a gift to shoot portraits. We all have pictures of ourselves <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">made by Nika. Accurate yet flattering!</span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;"></span>\r\n</p>', '1986-02-13 00:00:00'),
(2735, 'Sonya Zoomer', '', '/floxim_files/content/person/photo/3g_2_0.JPG', '', '<p>\r\n	 “She is a genius, a philosopher, an abstract thinker. She has a brain of the first order. She sits motionless, like a spider in the center of its web, but that web has a thousand radiations, and she knows well every quiver of each of them. She does little himself. She only plans…”\r\n</p>\r\n<p>\r\n	 Meet Sonya, our manager.\r\n</p>', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_photo`
--

CREATE TABLE IF NOT EXISTS `fx_content_photo` (
  `id` int(11) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `description` text,
  `copy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_photo`
--

INSERT INTO `fx_content_photo` (`id`, `photo`, `description`, `copy`) VALUES
(2058, '/floxim_files/content/photo/photo/7394_0_0.jpg', '<p>\r\n	 Это же Саша Васильев!\r\n</p>', ''),
(2214, '', 'Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Sanctus sea sed takimata ut vero voluptua. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.', 'Sanctus sea sed takimata'),
(2215, '', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Quis aute iure reprehenderit inUt enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi ', 'Lorem ipsum dolor sit'),
(2216, '', 'Lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', 'Ut wisi enim ad'),
(2217, '', 'Lorem ipsum dolor sit', 'Stet clita kasd gubergren'),
(2218, '', 'Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. Sanctus sea sed takimata ut vero voluptua. At vero eos et accusam et justo duo dolores et ea rebum.', 'Ut wisi enim ad'),
(2219, '', 'Sanctus sea sed takimata ut vero voluptua. Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. At vero eos et accusam et justo duo dolores et ea rebum.', 'At vero eos et'),
(2231, '', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. At vero eos et accusam et justo duo dolores et ea rebum.', 'At vero eos et'),
(2622, '', '', ''),
(2692, '/floxim_files/content/photo/photo/6_Carnival_of_miners_13_0.jpg', '', ''),
(2693, '/floxim_files/content/photo/photo/6_Carnival_of_miners_15_0.jpg', '', ''),
(2750, '/floxim_files/content/photo/photo/6_Carnival_of_miners_8_0.jpg', '', ''),
(2752, '/floxim_files/content/photo/photo/6_kupala_8_0.JPG', '', ''),
(2753, '/floxim_files/content/photo/photo/6_kupala_1_0.JPG', '', ''),
(2754, '/floxim_files/content/photo/photo/6_kupala_17_0.JPG', '', ''),
(2755, '/floxim_files/content/photo/photo/6_cockfights_5_0.jpg', '', ''),
(2756, '/floxim_files/content/photo/photo/6_cockfights_8_0.JPG', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_product`
--

CREATE TABLE IF NOT EXISTS `fx_content_product` (
  `id` int(11) NOT NULL,
  `description` text,
  `short_description` text,
  `image` varchar(255) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_product`
--

INSERT INTO `fx_content_product` (`id`, `description`, `short_description`, `image`, `price`) VALUES
(2258, 'Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. At vero eos et accusam et justo duo dolores et ea rebum.', 'Lorem ipsum dolor sit', '', 334),
(2259, 'Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet. Consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.', 'At vero eos et', '', 34534),
(2260, 'Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.', 'Lorem ipsum dolor sit', '', 234),
(2328, 'Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Stet clita kasd gubergren, no sea takimata sanctus est. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 'At vero eos et', '', 452),
(2410, '<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p><p>Sanctus sea sed takimata ut vero voluptua. Consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Sanctus sea sed takimata ut vero voluptua. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p>', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore ma', '', 234234),
(2627, '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat.. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Ut wisi enim ad minim veniam, quis nostrud exerci tation</span><span style="font-size: 15px;">ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</span>\r\n</p>', '​Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat.. ', '', 2134),
(2631, '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Stet clita kasd gubergren, no sea takimata sanctus est. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum.</span>\r\n</p>', '​Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Stet clita kasd gubergren, no sea takimata sanctus est. Nam liber tempor cum soluta nobis eleifend option con', '', 2345),
(2633, '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Sanctus sea sed takimata ut vero voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est.</span>\r\n</p>', '​Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat', '', 124323),
(2660, '<p>\r\n	  Our photographers have been shooting Champion League matches since 2008, <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">the finals of UEFA Euro 2008 and 2012. They are now getting ready for World </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Cup 2014 in Brazil.</span>\r\n</p>\r\n<p>\r\n	  If you want the drama of football match captured by professionals, hire us. We’ve <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">got all the skills, experience, and equipment needed to shot high-quality photo </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">set for you.</span>\r\n</p>', '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">We capture the emotions of players and fans during the match like no one else.</span>\r\n</p>', '/floxim_files/content/product/image/5_football_4_0.jpg', 2000),
(2661, '<p>\r\n	Ken’s speaking:\r\n</p>\r\n<blockquote>\r\n	I love to shoot winter sports, especially skiing competitions. It’s <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">dynamic, it’s graphic because skis and ski poles give the picture a great rhythm.</span>\r\n</blockquote>\r\n<blockquote>\r\n	The crowd of skiers looks fantastic on the snow. And scenery is always beautiful.\r\n</blockquote>\r\n<blockquote>\r\n	I love winter forest – perhaps that’s my Russian roots talking.\r\n</blockquote>\r\n<p>\r\n	Ken’s been shooting ski competitions around the world for several years. If you <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">need a winter sports series he is your guy.</span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;"></span>\r\n</p>', '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">We are not scary of getting cold on the ski run as we usually run twice as much </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">as the skiers to catch the most interesting moments.</span>\r\n</p>', '/floxim_files/content/product/image/5_ski_5_0.JPG', 1500),
(2662, '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">After all the time she spent in pools and seas, our photographer Leila is basically </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">half-human half-dolphin. She knows all the details about shooting in water, and </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">even has a couple of inventions of her own for underwater shooting.</span>\r\n</p>\r\n<p>\r\n	Leila’s speaking:\r\n</p>\r\n<blockquote>\r\n	I love how water changes the light, shapes, and textures of <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">things. It can be very expressive. I’m currently getting ready for European </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Aquatic Championship. </span>\r\n</blockquote>\r\n<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">A great photo report’s waiting to be made!</span>\r\n</p>', '<p>\r\n	We love to shoot water sports so much that we attend all the events we can –  <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">from Olympics to school competition in our local pool.</span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;"></span>\r\n</p>', '/floxim_files/content/product/image/5_birthday_2_0.JPG', 1500),
(2739, '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Athletics was the first competitions our team’s shot. Since our first series in </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">2007, we’ve become faster and stronger. Ken’s become bolder.</span>\r\n</p>\r\n<p>\r\n	We are so good at shooting athletics partly because competitive running, <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">jumping, and throwing things is something that happens in our studio daily.</span>\r\n</p>\r\n<p>\r\n	We are fascinated with the sight of passion and human endeavor you see at the <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">stadium during competitions. Sometimes, it’s pure heroism from the athletes. We </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">always do our best to do them justice with our photo series.</span>\r\n</p>', '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">“Faster - Higher – Stronger” – our shooting motto.</span>\r\n</p>', '/floxim_files/content/product/image/5_athlet_2_0.JPG', 2100),
(2740, '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Nika, our portraitist, is super good at catching person’s mood and character.</span>\r\n</p>\r\n<p>\r\n	She’s inventive in studio photo shoot but is open to client’s suggestions.\r\n</p>', '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">We shoot portraits with love to subject. Welcome, beautiful client!</span>\r\n</p>', '/floxim_files/content/product/image/3a_1_0.JPG', 800),
(2741, '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">You know those passport pictures that are more suited for “Wanted” posters? </span>\r\n</p>\r\n<p>\r\n	None of that if you come to our studio.\r\n</p>\r\n<p>\r\n	We do all the necessary formats. We then can do all the necessary editing really  <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">quickly. You’ll look respectable and reliable individual – promise!</span>\r\n</p>', '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">High-quality representation of you for docs and visas.</span>\r\n</p>', '/floxim_files/content/product/image/5_portrait_passport_5_0.JPG', 1000),
(2742, '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Ken is a poet of city jungles. He loves city dynamics and lights and noises. He </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">also loves to travel and will be excited to go and shoot the city you want. High-</span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">quality pictures and unique view guaranteed.</span>\r\n</p>\r\n<p>\r\n	We are also happy to take orders from city administrations to make a booklet <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">with local sights. Tourists will rush to your city and spend their money around </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">all those attractions.</span>\r\n</p>', '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">We all are in love with street photography. That’s handy if you need an urban </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">photo series.</span>\r\n</p>', '/floxim_files/content/product/image/5_city_4_0.jpg', 900),
(2743, '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Ken’s favorite book is Emerson’s Naturalistic Photography. Inspired by the </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">book he later developed his own system of aesthetics that reflect nature in a </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">beautiful and unique way.</span>\r\n</p>\r\n<p>\r\n	Ken is armed with all necessary gear and is not afraid of using it. He is very <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">patient – a must-have for a naturalistic artist – and can spend hour in the </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">woods or on the beach waiting for the perfect sunset.</span>\r\n</p>', '<p>\n	 Our photographers teamed up with major naturalistic magazines.\n</p>\n<p>\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">We are so ready to start a new expedition.</span>\n</p>\n<p>\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;"></span>\n</p>', '/floxim_files/content/product/image/5_nature_2_0.JPG', 900),
(2745, '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">We are ready to shoot the most exotic and extreme birthday parties. We have </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">all the gear to shoot in the swimming pool or on the dance floor. Yes, you can </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">put down your phone for once and enjoy celebrating.</span>\r\n</p>\r\n<p>\r\n	By the way, our team came up with a great device. It includes wide-angle <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">lens and some really technical stuff, like a stick, to make a massive selfie </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">of you and all your party guests. Imagine the joy of tagging them all later on </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Instagram!</span>\r\n</p>', '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">We’ll do our best to abstain from drinks and capture you and your friends in </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">the gorgeous way.</span>\r\n</p>', '/floxim_files/content/product/image/5_birthday_9_0.jpg', 200),
(2746, '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">We have a long experience in shooting corporate sessions, conferences, </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">parties, and awards ceremonies. Your business rivals will be envious of how </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">great your corporate events look.</span>\r\n</p>\r\n<p>\r\n	With our digital team ready to work around the clock, all images are published <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">on a password protected website within 48 hours.</span>\r\n</p>', '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Among our clients are the biggest companies in the country. Most of them </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">keep re-booking our photographers year after year.</span>\r\n</p>', '/floxim_files/content/product/image/1_2_0.JPG', 2900);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_product_category`
--

CREATE TABLE IF NOT EXISTS `fx_content_product_category` (
  `id` int(11) NOT NULL,
  `counter` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_product_category`
--

INSERT INTO `fx_content_product_category` (`id`, `counter`) VALUES
(2317, 1),
(2319, 2),
(2332, 0),
(2338, 0),
(2537, 1),
(2551, 1),
(2552, 2),
(2553, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_project`
--

CREATE TABLE IF NOT EXISTS `fx_content_project` (
  `id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `client` varchar(255) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `description` text,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_project`
--

INSERT INTO `fx_content_project` (`id`, `image`, `client`, `short_description`, `description`, `date`) VALUES
(2253, '', 'Duis autem ', 'Stet clita kasd gubergren', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. Lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. Lorem ipsum dolor sit amet.', '2013-10-03 00:00:00'),
(2326, '', 'At vero eos et', 'At vero eos et accusam et justo duo dolores et ea rebum', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.\r\n\r\nStet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. At vero eos et accusam et justo duo dolores et ea rebum. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.\r\n\r\nAt vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum..\r\n\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Sanctus sea sed takimata ut vero voluptua. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.\r\n\r\nDuis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Sanctus sea sed takimata ut vero voluptua.', '2011-10-02 00:00:00'),
(2340, '', 'Ivano I.P.', 'Lorem ipsum dolor sitLorem ipsum dolor sitLorem ipsum dolor sitLorem ipsum dolor sitLorem ipsum dolor sitLorem ipsum dolor sitLorem ipsum dolor sitLorem ipsum dolor sit', 'Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. At vero eos et accusam et justo duo dolores et ea rebum. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis.', '2013-10-17 00:00:00'),
(2597, '', 'At vero eos et', 'Lorem ipsum dolor sit', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.', '2013-12-12 00:00:00'),
(2688, '/floxim_files/content/project/image/6_Carnival_of_miners_7_0.jpg', '', '', '<p>\r\n	The carnival of Potosi in Bolivia is the traditional feast of miners who live and <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">work in one of the highest mines in the world. The patron of miners, the Devil, </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">has combined both positive – Indian mythology – and negative – Catholic – </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">roots. The miners attributed their good fortune directly with a grace of the </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">devil, and thank him in their crazy carnival dances.</span>\r\n</p>\r\n<p>\r\n	Leila went to Bolivia after Young Pathfinder offered her to do a series about <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">miners’ life in Sought America. Leila came back with a beautiful photo report.</span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;"></span>\r\n</p>', '2014-01-09 00:00:00'),
(2689, '', '', 'Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Lorem ipsum do', '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">At vero eos et accusam et justo duo dolores et ea rebum. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat.</span>\r\n</p>\r\n<p>\r\n	At vero eos et accusam et justo duo dolores et ea rebum. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet.\r\n</p>\r\n<p>\r\n	Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat.\r\n</p>\r\n<p>\r\n	Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum..\r\n</p>\r\n<p style="margin-left: 40px;">\r\n	Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum.\r\n</p>', '0000-00-00 00:00:00'),
(2690, '/floxim_files/content/project/image/6_cockfights_3_0.JPG', '', '', '<p>\r\n	 A cockfight is a blood sport between two gamecocks, held in a ring called a <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">cockpit. Cockfighting is a blood sport due in some part to the physical trauma </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">the cocks inflict on each other. Advocates of the "age old sport" often list </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">cultural and religious relevance as reasons for perpetuation of cockfighting as </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">a sport.</span>\r\n</p>\r\n<p>\r\n	 Nika disapproves of the whole thing but she went to Cuba to document the <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">fights, the true professional she is.</span>\r\n</p>', '2014-01-16 00:00:00'),
(2751, '/floxim_files/content/project/image/6_kupala_7_0.jpg', '', '', '<p>\r\n	Kupala Night, also known as Ivan Kupala Day (Feast of St. John the Baptist) <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">is celebrated in Ukraine, Belarus and Russia currently on the night of 6/7 July </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">in the Gregorian calendar.</span>\r\n</p>\r\n<p>\r\n	The fest has pagan roots. According to an ancient pagan belief, on the eve <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">of Ivan Kupala is the only time of the year when ferns bloom. Prosperity, luck </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">and power would befall whoever finds a fern flower. On that night village folks </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">would roam through the forests in search of magical herbs and especially the </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">elusive fern flower. Traditionally, unmarried women would be the first to enter </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">the forest. They are followed by young men. In 2010, they were also followed </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">by Ken who made fantastic photo series.</span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;"></span>\r\n</p>', '0000-00-00 00:00:00'),
(2757, '/floxim_files/content/project/image/6_pascua_toro_3_0.jpg', '', '', '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Pascua Toro (Bull Easter) is the traditional holiday of the inhabitants of the </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Peruvian town of Ayacucho. Pascua Toro is celebrated during Holy Saturday. </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">This holiday is famous for colorful running of the bulls through the streets of </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">the town.</span>\r\n</p>\r\n<p>\r\n	In 2011, our Ken took a huge risk and ran along with bulls. Fortunately, no <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">bulls were harmed.</span>\r\n</p>', '0000-00-00 00:00:00'),
(2762, '/floxim_files/content/project/image/art_stub_3_0_0.jpg', '', '', '', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_publication`
--

CREATE TABLE IF NOT EXISTS `fx_content_publication` (
  `id` int(11) NOT NULL,
  `publish_date` datetime DEFAULT NULL,
  `anounce` text,
  `image` varchar(255) DEFAULT NULL,
  `text` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_publication`
--

INSERT INTO `fx_content_publication` (`id`, `publish_date`, `anounce`, `image`, `text`) VALUES
(2150, '2013-10-31 00:00:00', '<p>​At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>', '', '<p>​Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Sanctus sea sed takimata ut vero voluptua. Stet clita kasd gubergren, no sea takimata sanctus est. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.</p><p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p><p>Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</p><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum.</p><p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Sanctus sea sed takimata ut vero voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet.</p>'),
(2256, '2013-10-20 00:00:00', '<p>\n	  huy\n</p>', '', '<p>\r\n	Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Stet clita kasd gubergren, no sea takimata sanctus est. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.\r\n</p>\r\n<p>\r\n	Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.\r\n</p>\r\n<p>\r\n	Consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.\r\n</p>\r\n<p>\r\n	Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. Stet clita kasd gubergren, no sea takimata sanctus est. At vero eos et accusam et justo duo dolores et ea rebum. Sanctus sea sed takimata ut vero voluptua.\r\n</p>\r\n<p>\r\n	Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum.\r\n</p>'),
(2455, '2013-11-20 00:00:00', '<p>\r\n	 Stet clita kasd gubergren, no sea takimata sanctus est. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. At vero eos et accusam et justo duo dolores et ea rebum. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.\r\n</p>', '', '<p>\r\n	 Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. At vero eos et accusam et justo duo dolores et ea rebum.\r\n</p>\r\n<p>\r\n	 At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum.\r\n</p>\r\n<p>\r\n	 Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.\r\n</p>\r\n<p>\r\n	 Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.\r\n</p>\r\n<p>\r\n	 Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.\r\n</p>'),
(2499, '2013-11-15 00:00:00', '<p>\r\n	Consetetur sadipscing elitr sed\r\n</p>', '', '<p>\r\n	Consetetur sadipscing elitr sed\r\n</p>'),
(2500, '2013-11-08 00:00:00', '<p>\r\n	Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.\r\n</p>', '', '<p>\r\n	Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.\r\n</p>'),
(2571, '2013-10-02 00:00:00', '<p>\r\n	 <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr</span>\r\n</p>', '', '<p>\r\n	 <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</span>\r\n</p>'),
(2572, '2013-12-06 00:00:00', '<p style="margin-left: 20px;">\r\n	 <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum doloadsr sit amet, consetetur<strong> sadipscidfgng</strong> elitr, </span>\r\n</p>', '', '<p>\r\n	 <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. Consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</span>\r\n</p>'),
(2573, '2013-12-15 00:00:00', '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet daolore magna aliquam erat volutpat.</span>\r\n</p>', '', '<p>\r\n	 <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum.</span>\r\n</p>'),
(2574, '2013-12-03 00:00:00', '<p>\n	 <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Stet clita kasd gubergren, no sea takimata sanctus ests Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum.</span>\n</p>', '', '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. </span>\r\n</p>\r\n<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</span>\r\n</p>'),
(2624, '2014-01-20 15:44:44', '<p>\r\n	 <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.</span>\r\n</p>', '', '<p>\r\n	 <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</span>\r\n</p>'),
(2678, '2014-02-19 00:00:00', '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Last touches, and we are ready to move in!</span>\r\n</p>', '/floxim_files/content/news/image/2a_0.jpg', ''),
(2679, '2014-03-13 15:37:34', '<p>\r\n	  Nika’s and Ken’s street series were shortlisted for Moscow Streetshot Contest.\r\n</p>', '/floxim_files/content/news/image/5_city_6_0.jpg', '<p>\r\n	  Guys didn’t win this time but we’ll be back next year.\r\n</p>\r\n<p>\r\n	  For now, have Nika and her crazy hair having fun in Russia.\r\n</p>'),
(2680, '2014-02-05 15:19:29', '<p>\n	The whole team went for free ride. Proof pics!\n</p>', '/floxim_files/content/news/image/2v_10_0.jpg', '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</span>\r\n</p>\r\n<p>\r\n	Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. At vero eos et accusam et justo duo dolores et ea rebum..\r\n</p>\r\n<p>\r\n	At vero eos et accusam et justo duo dolores et ea rebum. At vero eos et accusam et justo duo dolores et ea rebum. At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.\r\n</p>\r\n<p>\r\n	Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis. Sanctus sea sed takimata ut vero voluptua. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.\r\n</p>\r\n<p>\r\n	Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Sanctus sea sed takimata ut vero voluptua. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. At vero eos et accusam et justo duo dolores et ea rebum.\r\n</p>'),
(2681, '2013-06-07 15:39:50', '<p>\n	 <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">We made some great photo series during Moscow Athletics </span>Championship.\n</p>\n<p>\n	 Check them out.\n</p>', '/floxim_files/content/news/image/5_athlet_5_0.jpg', '');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_section`
--

CREATE TABLE IF NOT EXISTS `fx_content_section` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=7;

--
-- Дамп данных таблицы `fx_content_section`
--

INSERT INTO `fx_content_section` (`id`) VALUES
(2),
(3),
(16),
(1887),
(1898),
(1899),
(1900),
(1901),
(1902),
(2072),
(2073),
(2074),
(2075),
(2076),
(2077),
(2078),
(2079),
(2081),
(2082),
(2149),
(2179),
(2181),
(2187),
(2212),
(2233),
(2242),
(2245),
(2248),
(2251),
(2254),
(2257),
(2510),
(2562),
(2567),
(2591),
(2593),
(2596),
(2598),
(2601),
(2602),
(2638),
(2639),
(2640),
(2641),
(2652),
(2654),
(2655),
(2656),
(2657),
(2658),
(2744);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_select_linker`
--

CREATE TABLE IF NOT EXISTS `fx_content_select_linker` (
  `id` int(11) NOT NULL,
  `linked_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_select_linker`
--

INSERT INTO `fx_content_select_linker` (`id`, `linked_id`) VALUES
(2491, 1996),
(2495, 2256),
(2496, 2256),
(2516, 2513),
(2517, 2514),
(2518, 2515),
(2519, 2511),
(2520, 2510),
(2521, 2512),
(2623, 2597),
(2625, 2582),
(2626, 2584),
(2630, 2627),
(2659, 2652),
(2668, 2660),
(2682, 2678),
(2683, 2679),
(2684, 2680),
(2691, 2688),
(2694, 2656),
(2747, 2746),
(2748, 2743),
(2749, 2740);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_social_icon`
--

CREATE TABLE IF NOT EXISTS `fx_content_social_icon` (
  `id` int(11) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `soc_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_social_icon`
--

INSERT INTO `fx_content_social_icon` (`id`, `url`, `icon`, `name`, `soc_type`) VALUES
(2616, 'http://www.linkedin.com/nhome/', '', NULL, 'linkedin'),
(2617, 'https://www.facebook.com/lists/404129646265675', '', NULL, 'facebook'),
(2642, 'https://www.facebook.com/', '', '', 'facebook');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_tag`
--

CREATE TABLE IF NOT EXISTS `fx_content_tag` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_tag`
--

INSERT INTO `fx_content_tag` (`id`) VALUES
(2534),
(2568),
(2569),
(2570),
(2717),
(2728),
(2731),
(2733);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_text`
--

CREATE TABLE IF NOT EXISTS `fx_content_text` (
  `id` int(11) NOT NULL,
  `text` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=1199;

--
-- Дамп данных таблицы `fx_content_text`
--

INSERT INTO `fx_content_text` (`id`, `text`) VALUES
(112, '<p>Put your content here!​</p>'),
(1903, '<p>\n	             The adventure began back in 1977, when Thierry Sabine got lost on his motorbike in the Libyan desert during the Abidjan-Nice Rally. Saved from the sands in extremis, he returned to France still in thrall to this landscape and promising himself he would share his fascination with as many people as possible. He proceeded to come up with a route starting in Europe, continuing to Algiers and crossing Agadez before eventually finishing at Dakar. The founder coined a motto for his inspiration: "A challenge for those who go. A dream for those who stay behind." Courtesy of his great conviction and that modicum of madness peculiar to all great ideas, the plan quickly became a reality. Since then, the Paris-Dakar, a unique event sparked by the spirit of adventure, open to all riders and carrying a message of friendship between all men, has never failed to challenge, surprise and excite. Over the course of almost thirty years, it has generated innumerable sporting and human stories.</p><p>\n	            (c) <a href="http://www.dakar.com">www.dakar.com</a></p>'),
(1910, '<p class="">\n	Everyone can join our team! Please, feel free to contact us for a details.</p>'),
(2047, '<p>\r\n	We are going to post many sad  but interesting posts under the "funeral" tag. Stay idle.\r\n</p>'),
(2059, '<p>\n	Feel free to subscribe our&nbsp;<a href="/Blog?rss" target="_blank">​RSS</a>&nbsp;chanel!</p>'),
(2062, '<p>\n	 Welcome welcome welcom! Welcome, welcome, welcom! Well-come! We-llco-me! Welllllllllcomeeee!\n</p>'),
(2220, '<p>\n	                      At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. Lorem ipsum dolor sit amet, conse</p>'),
(2234, '<p>​Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis. At vero eos et accusam et justo duo dolores et ea rebum. Consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</p><p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p><p>At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Stet clita kasd gubergren, no sea takimata sanctus est.</p><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p><p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</p>'),
(2235, '<p>​Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. At vero eos et accusam et justo duo dolores et ea rebum. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p><p>Lorem ipsum dolor sit amet. Stet clita kasd gubergren, no sea takimata sanctus est. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</p><p>Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. Sanctus sea sed takimata ut vero voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p><p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum.</p><p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis.</p>'),
(2246, '<p>​Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. At vero eos et accusam et justo duo dolores et ea rebum. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p><p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. Lorem ipsum dolor sit amet.</p><p>Sanctus sea sed takimata ut vero voluptua. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sanctus sea sed takimata ut vero voluptua. Consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p><p> Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p><p> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p>'),
(2590, '<h2>Stet clita kasd gubergren</h2><p>\n	Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum. At vero eos et accusam et justo duo dolores et ea rebum. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. At vero eos et accusam et justo duo dolores et ea rebum.</p><p>\n	Lorem ipsum dolor sit amet. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. At vero eos et accusam et justo duo dolores et ea rebum. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</p><p>\n	Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. At vero eos et accusam et justo duo dolores et ea rebum. Sanctus sea sed takimata ut vero voluptua. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p><p>\n	Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p><p>\n	Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat.</p>'),
(2698, '<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">At vero eos et accusam et justo duo dolores et ea rebum.</span>\r\n</p>\r\n<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;"> Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid </span>\r\n</p>'),
(2700, '<h4><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Ut wisi enim ad</span></h4>\r\n<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Ut wisi enim ad</span>\r\n</p>'),
(2701, '<h4><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Sanctus sea sed takimata</span></h4>\r\n<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Sanctus sea sed takimata</span>\r\n</p>'),
(2703, '<p>\r\n	And it''s FREEE!!!\r\n</p>'),
(2704, '<p>\r\n	it''s my favorite shipp\r\n</p>');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_travel_route`
--

CREATE TABLE IF NOT EXISTS `fx_content_travel_route` (
  `id` int(11) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `email` char(255) NOT NULL,
  `login` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `registration_code` varchar(45) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `forum_messages` int(11) NOT NULL DEFAULT '0',
  `pa_balance` double NOT NULL DEFAULT '0',
  `auth_hash` varchar(50) NOT NULL DEFAULT '',
  `is_admin` tinyint(4) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `User_ID` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=104;

--
-- Дамп данных таблицы `fx_content_user`
--

INSERT INTO `fx_content_user` (`id`, `email`, `login`, `name`, `registration_code`, `avatar`, `forum_messages`, `pa_balance`, `auth_hash`, `is_admin`, `password`) VALUES
(2367, 'admin@floxim.loc', '', 'Admin', NULL, NULL, 0, 0, '', 1, '20EAfcH0JSFQY');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_vacancy`
--

CREATE TABLE IF NOT EXISTS `fx_content_vacancy` (
  `id` int(11) NOT NULL,
  `salary_from` int(11) DEFAULT NULL,
  `salary_to` int(11) DEFAULT NULL,
  `requirements` text,
  `responsibilities` text,
  `work_conditions` text,
  `currency` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_vacancy`
--

INSERT INTO `fx_content_vacancy` (`id`, `salary_from`, `salary_to`, `requirements`, `responsibilities`, `work_conditions`, `currency`, `image`) VALUES
(2249, 1234, 5446, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 'Stet clita kasd gubergren, no sea takimata sanctus est. Sanctus sea sed takimata ut vero voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', 'Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.', NULL, NULL),
(2250, 7567, 7807890, ' Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.', 'Lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Stet clita kasd gubergren, no sea takimata sanctus est. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. At vero eos et accusam et justo duo dolores et ea rebum.', 'At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est.', NULL, NULL),
(2594, 200, 2000, '<p>\r\n	    Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. Lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.\r\n</p>', '<p>\r\n	    Consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis.\r\n</p>', '<p>\r\n	    At vero eos et accusam et justo duo dolores et ea rebum. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis.\r\n</p>', '$', NULL),
(2595, 2020, 6000, '<p>\r\n	    Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.\r\n</p>', '<p>\r\n	    Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. At vero eos et accusam et justo duo dolores et ea rebum. Lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua.\r\n</p>', '<p>\r\n	    Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis. At vero eos et accusam et justo duo dolores et ea rebum. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.\r\n</p>', '$', NULL),
(2677, 2000, 4000, '<ul>\r\n	<li><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Knowledge of QuarkXPress, Illustrator </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">plus the standard Adobe </span>Photoshop, Dreamweaver and Microsoft programs;</li>\r\n	<li><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;"></span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Professional experience of at least three years;</span></li>\r\n	<li><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;"></span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Creativity, energy and enthusiasm;</span></li>\r\n	<li><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;"></span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Language skills in both Russian and English.</span></li>\r\n</ul>', '<ul>\r\n	<li><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">assisting with the production of presentations;</span></li>\r\n	<li><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;"></span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">organising digital photography;</span></li>\r\n	<li><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;"></span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">drawing graphs and diagrams in Illustrator;</span></li>\r\n	<li><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">u</span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">pdating photo libraries.</span></li>\r\n</ul>', '<p>\r\n	  We are working on our first photo album and need someone with experience of <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">making up a page.</span>\r\n</p>\r\n<p>\r\n	<span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">If you are good we’ll love you to bits.</span>\r\n</p>', '$', '/floxim_files/content/vacancy/image/4SPS_0.jpg'),
(2737, 0, 0, '<p>\r\n	 Walk, ride bicycles, drive vehicles, or use public conveyances in order to <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">reach destinations to deliver our newspaper</span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">.</span>\r\n</p>', '<ul>\r\n	<li>Receive the newspapers or the materials for our clients to be delivered, <span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">and information on recipients, such as names, addresses, telephone </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">numbers, and delivery instructions, communicated via telephone, two-</span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">way radio, or in person;</span></li>\r\n	<li><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Plan and follow the most efficient routes for delivering our precious </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">newpaper;</span></li>\r\n	<li><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;"></span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">Obtain signatures and payments, or arrange for recipients to make </span><span style="font-family: Arial, Helvetica, Verdana, Tahoma, sans-serif;">payments.</span></li>\r\n</ul>', '', '$', '/floxim_files/content/vacancy/image/5_birthday_1_0.JPG');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_content_video`
--

CREATE TABLE IF NOT EXISTS `fx_content_video` (
  `id` int(11) NOT NULL,
  `embed_html` text,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `fx_content_video`
--

INSERT INTO `fx_content_video` (`id`, `embed_html`, `description`) VALUES
(2493, '<iframe width="560" height="315" src="//www.youtube.com/embed/gOMvXngEHmw?wmode=opaque" frameborder="0" allowfullscreen></iframe>', '');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=60 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=204 AUTO_INCREMENT=15 ;

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
  `form_tab` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Checked` (`checked`),
  KEY `component_id` (`component_id`),
  KEY `System_Table_ID` (`system_table_id`),
  KEY `TypeOfData_ID` (`type`),
  KEY `TypeOfEdit_ID` (`type_of_edit`),
  KEY `Widget_Class_ID` (`widget_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=95 AUTO_INCREMENT=306 ;

--
-- Дамп данных таблицы `fx_field`
--

INSERT INTO `fx_field` (`id`, `parent`, `component_id`, `ctpl_id`, `system_table_id`, `widget_id`, `name`, `description`, `type`, `format`, `not_null`, `priority`, `searchable`, `default`, `inheritance`, `type_of_edit`, `checked`, `form_tab`) VALUES
(1, NULL, 1, 0, 0, 0, 'name', 'Screen name', 1, '', 0, 0, 1, '', 0, 1, 1, 0),
(2, NULL, 1, 0, 0, 0, 'avatar', 'Userpic', 11, '', 0, 0, 0, '', 0, 1, 1, 0),
(118, NULL, 19, 0, 0, 0, 'text', 'Text', 3, 'a:1:{s:4:"html";s:1:"1";}', 0, 0, 1, '', 0, 1, 1, 0),
(153, NULL, 1, 0, 0, 0, 'email', 'E-mail', 1, '', 0, 142, 1, NULL, 0, 1, 1, 0),
(165, NULL, 23, 0, 0, 0, 'url', 'URL', 1, '', 0, 2, 0, '', 0, 1, 1, 0),
(190, NULL, 23, 0, 0, 0, 'name', 'Name', 1, '', 1, 0, 1, '', 0, 1, 1, 0),
(191, NULL, 23, 0, 0, 0, 'title', 'Title', 1, '', 0, 158, 0, '', 0, 1, 1, 0),
(196, NULL, 36, 0, 0, 0, 'parent_id', 'Parent', 13, 'a:4:{s:6:"target";s:2:"23";s:9:"prop_name";s:6:"parent";s:9:"is_parent";s:1:"0";s:11:"render_type";s:6:"select";}', 0, 161, 0, '', 0, 3, 1, 0),
(199, NULL, 46, 0, 0, 0, 'start_date', 'Start date', 8, '', 0, 164, 0, '', 0, 1, 1, 0),
(200, NULL, 46, 0, 0, 0, 'end_date', 'End date', 8, '', 0, 165, 0, '', 0, 1, 1, 0),
(201, NULL, 47, 0, 0, 0, 'publish_date', 'Publish date', 8, '', 0, 166, 0, '', 0, 1, 1, 0),
(202, NULL, 47, 0, 0, 0, 'cover', 'Cover image', 11, '', 0, 167, 0, '', 0, 1, 1, 0),
(203, NULL, 48, 0, 0, 0, 'photo', 'Image', 11, '', 1, 168, 0, '', 0, 1, 1, 0),
(204, NULL, 48, 0, 0, 0, 'description', 'Description', 3, 'a:2:{s:4:"html";s:1:"1";s:5:"nl2br";s:1:"0";}', 0, 169, 0, '', 0, 1, 1, 0),
(205, NULL, 48, 0, 0, 0, 'copy', 'Copy', 1, '', 0, 170, 0, '', 0, 1, 1, 0),
(212, NULL, 49, 0, 0, 0, 'publish_date', 'Publish date', 8, '', 0, 174, 0, 'now', 0, 1, 1, 1),
(213, NULL, 49, 0, 0, 0, 'anounce', 'Anounce', 3, 'a:2:{s:4:"html";s:1:"1";s:5:"nl2br";s:1:"0";}', 0, 175, 0, '', 0, 1, 1, 2),
(214, NULL, 49, 0, 0, 0, 'image', 'Image', 11, '', 0, 176, 0, '', 0, 1, 1, 2),
(215, NULL, 49, 0, 0, 0, 'text', 'Text', 3, 'a:2:{s:4:"html";s:1:"1";s:5:"nl2br";s:1:"0";}', 0, 177, 0, '', 0, 1, 1, 3),
(216, NULL, 1, 0, 0, 0, 'is_admin', 'Is admin?', 5, '', 0, 178, 0, '0', 0, 2, 1, 0),
(218, NULL, 50, 0, 0, 0, 'comment_text', 'Comment Text', 3, 'a:1:{s:5:"nl2br";s:1:"1";}', 1, 180, 0, '', 0, 2, 1, 0),
(219, NULL, 50, 0, 0, 0, 'publish_date', 'Publish Date', 8, '', 1, 181, 0, '', 0, 2, 1, 0),
(220, NULL, 50, 0, 0, 0, 'user_name', 'User Name', 1, '', 1, 182, 0, '', 0, 2, 1, 0),
(221, NULL, 23, 0, 0, 0, 'comments_counter', 'Comments counter', 2, '', 0, 183, 0, '0', 0, 3, 1, 0),
(222, NULL, 50, 0, 0, 0, 'is_moderated', 'Moderated flag', 5, '', 0, 184, 0, '0', 0, 2, 1, 0),
(228, NULL, 58, 0, 0, 0, 'question', 'Question', 1, '', 0, 185, 0, '', 0, 1, 1, 0),
(229, NULL, 58, 0, 0, 0, 'answer', 'Answer', 3, 'a:2:{s:4:"html";s:1:"0";s:5:"nl2br";s:1:"0";}', 0, 186, 0, '', 0, 1, 1, 0),
(230, NULL, 59, 0, 0, 0, 'embed_html', 'Embed code or link', 3, 'a:2:{s:4:"html";s:1:"0";s:5:"nl2br";s:1:"0";}', 0, 187, 0, '', 0, 1, 1, 0),
(231, NULL, 59, 0, 0, 0, 'description', 'Description', 3, 'a:2:{s:4:"html";s:1:"1";s:5:"nl2br";s:1:"0";}', 0, 188, 0, '', 0, 1, 1, 0),
(232, NULL, 60, 0, 0, 0, 'image', 'Image', 11, '', 0, 189, 0, '', 0, 1, 1, 0),
(233, NULL, 60, 0, 0, 0, 'description', 'Description', 3, 'a:2:{s:4:"html";s:1:"1";s:5:"nl2br";s:1:"0";}', 0, 190, 0, '', 0, 1, 1, 0),
(234, NULL, 60, 0, 0, 0, 'year', 'Year', 2, '', 0, 191, 0, '2000', 0, 1, 1, 0),
(235, NULL, 61, 0, 0, 0, 'logo', 'Logo', 11, '', 0, 192, 0, '', 0, 1, 1, 0),
(236, NULL, 61, 0, 0, 0, 'short_description', 'Short Description', 1, '', 0, 193, 0, '', 0, 1, 1, 0),
(237, NULL, 61, 0, 0, 0, 'description', 'Description', 3, 'a:2:{s:4:"html";s:1:"0";s:5:"nl2br";s:1:"0";}', 0, 194, 0, '', 0, 1, 1, 0),
(238, NULL, 62, 0, 0, 0, 'image', 'Image', 11, '', 0, 195, 0, '', 0, 1, 1, 0),
(239, NULL, 62, 0, 0, 0, 'client', 'Client', 1, '', 0, 196, 0, '', 0, 1, 1, 0),
(240, NULL, 62, 0, 0, 0, 'short_description', 'Short Description', 1, '', 0, 197, 0, '', 0, 1, 1, 0),
(241, NULL, 62, 0, 0, 0, 'description', 'Description', 3, 'a:2:{s:4:"html";s:1:"1";s:5:"nl2br";s:1:"0";}', 0, 198, 0, '', 0, 1, 1, 0),
(242, NULL, 62, 0, 0, 0, 'date', 'Date', 8, '', 0, 199, 0, '', 0, 1, 1, 0),
(244, NULL, 63, 0, 0, 0, 'salary_from', 'Salary from', 2, '', 0, 201, 0, '', 0, 1, 1, 0),
(245, NULL, 63, 0, 0, 0, 'salary_to', 'Salary To', 2, '', 0, 202, 0, '', 0, 1, 1, 0),
(246, NULL, 63, 0, 0, 0, 'requirements', 'Requirements', 3, 'a:2:{s:4:"html";s:1:"1";s:5:"nl2br";s:1:"0";}', 0, 203, 0, '', 0, 1, 1, 0),
(247, NULL, 63, 0, 0, 0, 'responsibilities', 'Responsibilities', 3, 'a:2:{s:4:"html";s:1:"1";s:5:"nl2br";s:1:"0";}', 0, 204, 0, '', 0, 1, 1, 0),
(248, NULL, 63, 0, 0, 0, 'work_conditions', 'Work conditions', 3, 'a:2:{s:4:"html";s:1:"1";s:5:"nl2br";s:1:"0";}', 0, 205, 0, '', 0, 1, 1, 0),
(253, NULL, 64, 0, 0, 0, 'counter', 'Counter', 2, '', 0, 210, 0, '0', 0, 3, 1, 0),
(254, NULL, 65, 0, 0, 0, 'classifier_id', 'Classifier ID', 13, 'a:4:{s:6:"target";s:2:"64";s:9:"prop_name";s:10:"classifier";s:9:"is_parent";s:1:"0";s:11:"render_type";s:10:"livesearch";}', 0, 211, 0, '', 0, 1, 1, 0),
(255, NULL, 65, 0, 0, 0, 'content_id', 'Content ID', 13, 'a:4:{s:6:"target";s:2:"36";s:9:"prop_name";s:7:"content";s:9:"is_parent";s:1:"1";s:11:"render_type";s:10:"livesearch";}', 0, 212, 0, '', 0, 1, 1, 0),
(257, NULL, 69, 0, 0, 0, 'full_name', 'Full Name', 1, '', 0, 214, 0, '', 0, 1, 1, 0),
(259, NULL, 69, 0, 0, 0, 'department', 'Department', 1, '', 0, 216, 0, '', 0, 1, 1, 0),
(260, NULL, 69, 0, 0, 0, 'photo', 'Photo', 11, '', 0, 217, 0, '', 0, 1, 1, 3),
(261, NULL, 69, 0, 0, 0, 'short_description', 'Short Description', 1, '', 0, 218, 0, '', 0, 1, 1, 0),
(262, NULL, 69, 0, 0, 0, 'description', 'Description', 3, 'a:2:{s:4:"html";s:1:"1";s:5:"nl2br";s:1:"0";}', 0, 219, 0, '', 0, 1, 1, 0),
(263, NULL, 69, 0, 0, 0, 'birthday', 'Birthday', 8, '', 0, 220, 0, '', 0, 1, 1, 3),
(264, NULL, 70, 0, 0, 0, 'value', 'Value', 1, '', 0, 222, 0, '', 0, 1, 1, 0),
(265, NULL, 70, 0, 0, 0, 'contact_type', 'Type (e.g. ICQ, Skype, Jabber)', 1, '', 0, 221, 0, '', 0, 1, 1, 0),
(269, NULL, 69, 0, 0, 0, 'contacts', 'Contacts', 14, 'a:3:{s:11:"render_type";s:5:"table";s:13:"linking_field";s:3:"196";s:16:"linking_datatype";s:2:"70";}', 0, 223, 0, '', 0, 1, 1, 3),
(270, NULL, 71, 0, 0, 0, 'image', 'Image', 11, '', 0, 224, 0, '', 0, 1, 1, 0),
(271, NULL, 71, 0, 0, 0, 'description', 'Description', 3, 'a:2:{s:4:"html";s:1:"0";s:5:"nl2br";s:1:"0";}', 0, 225, 0, '', 0, 1, 1, 0),
(273, NULL, 73, 0, 0, 0, 'embed_html', 'Embed code or link', 3, 'a:2:{s:4:"html";s:1:"0";s:5:"nl2br";s:1:"0";}', 0, 227, 0, '', 0, 1, 1, 0),
(274, NULL, 73, 0, 0, 0, 'description', 'Description', 3, 'a:2:{s:4:"html";s:1:"0";s:5:"nl2br";s:1:"0";}', 0, 228, 0, '', 0, 1, 1, 0),
(277, NULL, 75, 0, 0, 0, 'description', 'Description', 3, 'a:2:{s:4:"html";s:1:"1";s:5:"nl2br";s:1:"0";}', 0, 231, 0, '', 0, 1, 1, 3),
(278, NULL, 75, 0, 0, 0, 'short_description', 'Short Description', 3, 'a:2:{s:4:"html";s:1:"1";s:5:"nl2br";s:1:"0";}', 0, 232, 0, '', 0, 1, 1, 0),
(279, NULL, 75, 0, 0, 0, 'image', 'Image', 11, '', 0, 233, 0, '', 0, 1, 1, 0),
(280, NULL, 75, 0, 0, 0, 'price', 'Price', 2, '', 0, 234, 0, '', 0, 1, 1, 0),
(283, NULL, 76, 0, 0, 0, 'counter', 'Counter', 2, '', 0, 237, 0, '0', 0, 3, 1, 0),
(287, NULL, 60, 0, 0, 0, 'short_description', 'Short Description', 1, '', 0, 241, 0, '', 0, 1, 1, 0),
(289, NULL, 1, 0, 0, 0, 'password', 'Password', 1, '', 0, 243, 0, '', 0, 1, 1, 0),
(290, NULL, 36, 0, 0, 0, 'created', 'Creation date', 8, '', 0, 244, 0, '', 0, 3, 1, 0),
(291, NULL, 36, 0, 0, 0, 'user_id', 'User', 13, 'a:4:{s:6:"target";s:1:"1";s:9:"prop_name";s:4:"user";s:9:"is_parent";s:1:"0";s:11:"render_type";s:10:"livesearch";}', 0, 245, 0, '', 0, 3, 1, 0),
(292, NULL, 36, 0, 0, 0, 'site_id', 'Site', 13, 'a:4:{s:6:"target";s:4:"site";s:9:"prop_name";s:4:"site";s:9:"is_parent";s:1:"0";s:11:"render_type";s:10:"livesearch";}', 0, 246, 0, '', 0, 3, 1, 0),
(294, NULL, 77, 0, 0, 0, 'linked_id', 'Linking content id', 13, 'a:4:{s:6:"target";s:2:"36";s:9:"prop_name";s:7:"content";s:9:"is_parent";s:1:"0";s:11:"render_type";s:10:"livesearch";}', 0, 247, 0, '', 0, 1, 1, 0),
(295, NULL, 49, 0, 0, 0, 'tags', 'Tags', 14, 'a:5:{s:11:"render_type";s:10:"livesearch";s:13:"linking_field";s:3:"255";s:16:"linking_datatype";s:2:"65";s:8:"mm_field";s:3:"254";s:11:"mm_datatype";s:2:"78";}', 0, 248, 0, '', 0, 1, 1, 0),
(296, NULL, 79, 0, 0, 0, 'url', 'URL', 1, '', 1, 249, 0, '', 0, 1, 1, 0),
(297, NULL, 79, 0, 0, 0, 'icon', 'icon', 11, '', 0, 250, 0, '', 0, 1, 1, 0),
(298, NULL, 79, 0, 0, 0, 'name', 'name', 1, '', 0, 251, 0, '', 0, 1, 1, 0),
(299, NULL, 79, 0, 0, 0, 'soc_type', 'Type (fb, vk, etc.)', 1, '', 0, 252, 0, '', 0, 3, 1, 0),
(300, NULL, 63, 0, 0, 0, 'currency', 'Currency', 1, '', 0, 253, 0, '$', 0, 1, 1, 0),
(304, NULL, 23, 0, 0, 0, 'children', 'Children', 14, 'a:3:{s:11:"render_type";s:10:"livesearch";s:13:"linking_field";s:3:"196";s:16:"linking_datatype";s:2:"36";}', 0, 257, 0, '', 0, 3, 1, 0),
(305, NULL, 63, 0, 0, 0, 'image', 'Image', 11, '', 0, 258, 0, '', 0, 1, 1, 4);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=56 AUTO_INCREMENT=633 ;

--
-- Дамп данных таблицы `fx_filetable`
--

INSERT INTO `fx_filetable` (`id`, `real_name`, `path`, `type`, `size`, `to_delete`) VALUES
(56, 'bg-portfolio.jpg', 'content/bg-portfolio_1.jpg', 'image/jpeg', 235261, 0),
(60, 'bg-portfolio.jpg', 'content/bg-portfolio_3.jpg', 'image/jpeg', 235261, 0),
(164, 'гроза.jpg', 'content/groza_1.jpg', 'image/jpeg', 172197, 0),
(345, 'img01.jpg', 'content/img01_1.jpg', 'image/jpeg', 219265, 0),
(356, '_logo.png', 'content/logo_3.png', 'image/png', 2627, 0),
(357, 'bg-portfolio.jpg', 'content/bg-portfolio_6.jpg', 'image/jpeg', 235261, 0),
(358, 'bg-company.jpg', 'content/bg-company_2.jpg', 'image/jpeg', 68376, 0),
(378, 'logo.png', 'content/logo_6.png', 'image/png', 5735, 0),
(379, 'img05.jpg', 'content/img05_1.jpg', 'image/jpeg', 5717, 0),
(389, 'bg-portfolio.jpg', 'content/bg-portfolio_0.jpg', 'image/jpeg', 235261, 0),
(390, '7394.jpg', 'content/7394_0.jpg', 'image/jpeg', 8988, 0),
(391, 'patch_0.1.1.zip', 'patches/patch_0.1.1_0.zip', 'Image', 696, 0),
(392, 'patch_0.1.1.zip', 'patches/patch_0.1.1_1.zip', 'Image', 696, 0),
(393, 'patch_0.1.1.zip', 'patches/patch_0.1.1_2.zip', 'Image', 696, 0),
(394, 'patch_0.1.1.zip', 'patches/patch_0.1.1_3.zip', 'Image', 696, 0),
(395, 'patch_0.1.1.zip', 'patches/patch_0.1.1_4.zip', 'Image', 696, 0),
(396, 'patch_0.1.1.zip', 'patches/patch_0.1.1_5.zip', 'Image', 696, 0),
(397, 'patch_0.1.1.zip', 'patches/patch_0.1.1_0.zip', 'Image', 696, 0),
(398, 'patch_0.1.1.zip', 'patches/patch_0.1.1_1.zip', 'Image', 696, 0),
(399, 'patch_0.1.1.zip', 'patches/patch_0.1.1_2.zip', 'Image', 696, 0),
(400, 'patch_0.1.1.zip', 'patches/patch_0.1.1_0.zip', 'Image', 696, 0),
(401, 'patch_0.1.1.zip', 'patches/patch_0.1.1_1.zip', 'Image', 696, 0),
(402, 'patch_0.1.1.zip', 'patches/patch_0.1.1_1.zip', 'Image', 696, 0),
(403, 'patch_0.1.1.zip', 'patches/patch_0.1.1_1.zip', 'Image', 696, 0),
(404, 'patch_0.1.1.zip', 'patches/patch_0.1.1_0.zip', 'Image', 696, 0),
(405, 'patch_0.1.1.zip', 'patches/patch_0.1.1_0.zip', 'Image', 696, 0),
(406, 'patch_0.1.2.zip', 'patches/patch_0.1.2_0.zip', 'Image', 124, 0),
(407, 'patch_0.1.5.zip', 'patches/patch_0.1.5_0.zip', 'Image', 124, 0),
(408, 'patch_0.2.0.zip', 'patches/patch_0.2.0_0.zip', 'Image', 3697131, 0),
(409, 'patch_0.2.0.zip', 'patches/patch_0.2.0_0.zip', 'Image', 124, 0),
(410, 'patch_0.1.1.zip', 'patches/patch_0.1.1_0.zip', 'Image', 696, 0),
(411, 'patch_0.1.2.zip', 'patches/patch_0.1.2_0.zip', 'Image', 124, 0),
(412, 'patch_0.1.5.zip', 'patches/patch_0.1.5_0.zip', 'Image', 124, 0),
(413, 'patch_0.2.0.zip', 'patches/patch_0.2.0_0.zip', 'Image', 124, 0),
(414, 'patch_0.1.1.zip', 'patches/patch_0.1.1_0.zip', 'Image', 696, 0),
(415, 'me.jpg', 'content/me_0.jpg', 'image/jpeg', 11149, 0),
(416, 'me.jpg', 'content/me_1.jpg', 'image/jpeg', 11149, 0),
(417, 'me.jpg', 'content/me_2.jpg', 'image/jpeg', 11149, 0),
(418, 'img06.jpg', 'content/img06_0.jpg', 'image/jpeg', 5396, 0),
(419, 'arrow_right_red.gif', 'content/arrow_right_red_0.gif', 'image/gif', 868, 0),
(420, 'slider_stub.jpg', 'content/slider_stub_0.jpg', 'image/jpeg', 71404, 0),
(421, 'art_stub_3.jpg', 'content/art_stub_3_0.jpg', 'image/jpeg', 26120, 0),
(422, 'oblako_edit.png', 'content/oblako_edit_0.png', 'image/png', 17575, 0),
(423, 'pic_cf234a6d1cee7008e148c859472470e8.jpg', 'content/pic_cf234a6d1cee7008e148c859472470e8_0.jpg', 'image/jpeg', 41026, 0),
(429, 'pic_cf234a6d1cee7008e148c859472470e8.jpg', 'content/pic_cf234a6d1cee7008e148c859472470e8_2.jpg', 'image/jpeg', 41026, 0),
(432, 'pic_cf234a6d1cee7008e148c859472470e8.jpg', 'content/pic_cf234a6d1cee7008e148c859472470e8_5.jpg', 'image/jpeg', 41026, 0),
(433, 'pic_cf234a6d1cee7008e148c859472470e8.jpg', 'content/pic_cf234a6d1cee7008e148c859472470e8_6.jpg', 'image/jpeg', 41026, 0),
(434, 'pic_cf234a6d1cee7008e148c859472470e8.jpg', 'content/pic_cf234a6d1cee7008e148c859472470e8_7.jpg', 'image/jpeg', 41026, 0),
(435, 'pic_cf234a6d1cee7008e148c859472470e8.jpg', 'content/pic_cf234a6d1cee7008e148c859472470e8_8.jpg', 'image/jpeg', 41026, 0),
(436, 'top7_5bc1e924e748f5ae47b8ad0db7f1e096.jpg', 'content/top7_5bc1e924e748f5ae47b8ad0db7f1e096_0.jpg', 'image/jpeg', 16552, 0),
(437, 'top7_99c6e04ab50912c4c8faa3dfa0d78189.jpg', 'content/top7_99c6e04ab50912c4c8faa3dfa0d78189_0.jpg', 'image/jpeg', 24689, 0),
(438, 'top7_99c6e04ab50912c4c8faa3dfa0d78189.jpg', 'content/top7_99c6e04ab50912c4c8faa3dfa0d78189_1.jpg', 'image/jpeg', 24689, 0),
(445, 'anyday-00113348.jpg', 'content/anyday-00113348_1.jpg', 'image/jpeg', 10281, 0),
(450, 'top7_99c6e04ab50912c4c8faa3dfa0d78189.jpg', 'content/top7_99c6e04ab50912c4c8faa3dfa0d78189_4.jpg', 'image/jpeg', 24689, 0),
(452, 'top7_5bc1e924e748f5ae47b8ad0db7f1e096.jpg', 'content/top7_5bc1e924e748f5ae47b8ad0db7f1e096_4.jpg', 'image/jpeg', 16552, 0),
(453, 'pic_cf234a6d1cee7008e148c859472470e8.jpg', 'content/pic_cf234a6d1cee7008e148c859472470e8_3.jpg', 'image/jpeg', 41026, 0),
(456, 'awards-2007.png', 'content/awards-2007_4.png', 'image/png', 12875, 0),
(457, 'awards-2007.png', 'content/awards-2007_0.png', 'image/png', 12875, 0),
(459, 'awards-2007.png', 'content/awards-2007_3.png', 'image/png', 12875, 0),
(461, 'pic_cf234a6d1cee7008e148c859472470e8.jpg', 'content/pic_cf234a6d1cee7008e148c859472470e8_9.jpg', 'image/jpeg', 41026, 0),
(462, 'anyday-00113601.jpg', 'content/anyday-00113601_1.jpg', 'image/jpeg', 13461, 0),
(463, 'anyday-00113599.jpg', 'content/anyday-00113599_1.jpg', 'image/jpeg', 11259, 0),
(464, 'anyday-00113599.jpg', 'content/anyday-00113599_2.jpg', 'image/jpeg', 11259, 0),
(465, 'anyday-00113601.jpg', 'content/anyday-00113601_2.jpg', 'image/jpeg', 13461, 0),
(467, 'awards-2007.png', 'content/awards-2007_2.png', 'image/png', 12875, 0),
(469, 'eleganzza-00119360.jpg', 'content/eleganzza-00119360_0.jpg', 'image/jpeg', 9239, 0),
(470, 'logo_es.jpg', 'content/logo_es_0.jpg', 'image/jpeg', 23597, 0),
(471, 'logo_es.jpg', 'content/logo_es_1.jpg', 'image/jpeg', 23597, 0),
(472, 'logos.jpg', 'content/logos_0.jpg', 'image/jpeg', 5070, 0),
(473, 'logo_es.jpg', 'content/logo_es_2.jpg', 'image/jpeg', 23597, 0),
(475, 'awards-2007.png', 'content/awards-2007_5.png', 'image/png', 12875, 0),
(476, 'eleganzza-00116508.jpg', 'content/eleganzza-00116508_0.jpg', 'image/jpeg', 11737, 0),
(478, 'img06_0.jpg', 'content/img06_0.jpg', 'image/jpeg', 5396, 0),
(479, 'Screenshot from 2013-11-20 13:34:13.png', 'content/Screenshot_from_2013-11-20_13_34_13_2.png', 'image/png', 129489, 0),
(482, 'slide.jpg', 'content/slide_0.jpg', 'image/jpeg', 469679, 0),
(483, 'slide2.png', 'content/slide2_0.png', 'image/png', 1532079, 0),
(484, 'slide2.png', 'content/slide2_1.png', 'image/png', 1532079, 0),
(487, 'feature-item2.png', 'content/feature-item2_2.png', 'image/png', 83479, 0),
(495, 'slide2.png', 'content/slide2_2.png', 'image/png', 1532079, 0),
(496, 'slide2.png', 'content/slide2_3.png', 'image/png', 1532079, 0),
(500, 'slide.jpg', 'content/slide_1.jpg', 'image/jpeg', 469679, 0),
(501, 'slide.jpg', 'content/slide_2.jpg', 'image/jpeg', 469679, 0),
(504, 'feature-item2.png', 'content/feature-item2_4.png', 'image/png', 83479, 0),
(505, 'feature-item2.png', 'content/feature-item2_5.png', 'image/png', 83479, 0),
(506, 'feature-item.png', 'content/feature-item_3.png', 'image/png', 49569, 0),
(507, 'feature-item2.png', 'content/feature-item2_6.png', 'image/png', 83479, 0),
(508, 'slide.jpg', 'content/slide_3.jpg', 'image/jpeg', 469679, 0),
(509, 'slide.jpg', 'content/slide_4.jpg', 'image/jpeg', 469679, 0),
(513, 'slide2.png', 'content/slide2_4.png', 'image/png', 1532079, 0),
(514, 'slide2.png', 'content/slide2_5.png', 'image/png', 1532079, 0),
(515, 'attack02.jpeg', 'content/attack02_0.jpeg', 'image/jpeg', 58475, 0),
(516, 'feature-item2.png', 'content/feature-item2_7.png', 'image/png', 83479, 0),
(517, 'feature-item.png', 'content/feature-item_7.png', 'image/png', 49569, 0),
(520, 'banner.png', 'content/banner_0.png', 'image/png', 225331, 0),
(521, '1280px-Sortie_de_l_op_ra_en_l_an_2000-2_1.jpg', 'content/1280px-Sortie_de_l_op_ra_en_l_an_2000-2_1_0.jpg', 'image/jpeg', 426225, 0),
(522, 'Screenshot from 2014-01-10 16:51:51.png', 'content/Screenshot_from_2014-01-10_16_51_51_0.png', 'image/png', 260371, 0),
(523, '1280px-Sortie_de_l_op_ra_en_l_an_2000-2_1.jpg', 'content/1280px-Sortie_de_l_op_ra_en_l_an_2000-2_1_1.jpg', 'image/jpeg', 426225, 0),
(524, '1280px-Sortie_de_l_op_ra_en_l_an_2000-2_1.jpg', 'content/1280px-Sortie_de_l_op_ra_en_l_an_2000-2_1_2.jpg', 'image/jpeg', 426225, 0),
(525, '1280px-Sortie_de_l_op_ra_en_l_an_2000-2_1.jpg', 'content/1280px-Sortie_de_l_op_ra_en_l_an_2000-2_1_3.jpg', 'image/jpeg', 426225, 0),
(526, 'slide.jpg', 'content/slide_5.jpg', 'image/jpeg', 469679, 0),
(527, 'feature-item.png', 'content/feature-item_4.png', 'image/png', 49569, 0),
(528, 'slide.jpg', 'content/slide_6.jpg', 'image/jpeg', 469679, 0),
(529, 'feature-item2.png', 'content/feature-item2_8.png', 'image/png', 83479, 0),
(530, 'feature-item2.png', 'content/feature-item2_9.png', 'image/png', 83479, 0),
(532, 'HansIsland.png', 'content/HansIsland_0.png', 'image/png', 1247358, 0),
(533, 'HansIsland.png', 'content/HansIsland_1.png', 'image/png', 1247358, 0),
(534, 'HansIsland.png', 'content/HansIsland_2.png', 'image/png', 1247358, 0),
(535, 'HansIsland.png', 'content/HansIsland_3.png', 'image/png', 1247358, 0),
(536, 'employee.png', 'content/employee_0.png', 'image/png', 154565, 0),
(537, 'employee.png', 'content/employee_1.png', 'image/png', 154565, 0),
(538, 'employee.png', 'content/employee_2.png', 'image/png', 154565, 0),
(539, 'HansIsland.png', 'content/HansIsland_4.png', 'image/png', 1247358, 0),
(540, 'HansIsland.png', 'content/HansIsland_5.png', 'image/png', 1247358, 0),
(541, 'employee.png', 'content/employee_3.png', 'image/png', 154565, 0),
(543, 'HansIsland.png', 'content/HansIsland_6.png', 'image/png', 1247358, 0),
(544, 'HansIsland.png', 'content/HansIsland_7.png', 'image/png', 1247358, 0),
(545, 'employee.png', 'content/employee_4.png', 'image/png', 154565, 0),
(546, 'HansIsland.png', 'content/HansIsland_8.png', 'image/png', 1247358, 0),
(547, '3fl-about-people.jpg', 'content/3fl-about-people_0.jpg', 'image/jpeg', 461243, 0),
(548, 'HansIsland.png', 'content/HansIsland_9.png', 'image/png', 1247358, 0),
(549, '1280px-Sortie_de_l_op_ra_en_l_an_2000-2_1_0.jpg', 'content/1280px-Sortie_de_l_op_ra_en_l_an_2000-2_1_0.jpg', 'image/jpeg', 426225, 0),
(550, '1280px-Sortie_de_l_op_ra_en_l_an_2000-2_1_0.jpg', 'content/1280px-Sortie_de_l_op_ra_en_l_an_2000-2_1_0.jpg', 'image/jpeg', 426225, 0),
(551, '7394_0.jpg', 'content/7394_0.jpg', 'image/jpeg', 8988, 0),
(552, 'attack02_0.jpeg', 'content/attack02_0.jpeg', 'image/jpeg', 58475, 0),
(553, 'bg-portfolio_6.jpg', 'content/bg-portfolio_6.jpg', 'image/jpeg', 235261, 0),
(554, '1.jpg', 'content/1_0.jpg', 'image/jpeg', 42907, 0),
(555, '7394_0.jpg', 'content/7394_0.jpg', 'image/jpeg', 8988, 0),
(556, '2.jpg', 'content/2_0.jpg', 'image/jpeg', 90794, 0),
(557, '6_pascua toro_19.JPG', 'content/6_pascua_toro_19_0.JPG', 'image/jpeg', 6708148, 0),
(558, '2a.jpg', 'content/2a_0.jpg', 'image/jpeg', 2006482, 0),
(559, '2a.jpg', 'content/2a_0.jpg', 'image/jpeg', 2006482, 0),
(560, '3а.JPG', 'content/3a_0.JPG', 'image/jpeg', 6142668, 0),
(561, '3б_2.JPG', 'content/3b_2_0.JPG', 'image/jpeg', 2199222, 0),
(562, '3в_1.JPG', 'content/3v_1_0.JPG', 'image/jpeg', 4683927, 0),
(563, '3в_2.JPG', 'content/3v_2_0.JPG', 'image/jpeg', 3721626, 0),
(564, '3г_2.JPG', 'content/3g_2_0.JPG', 'image/jpeg', 5148823, 0),
(565, '4SPS.jpg', 'content/4SPS_0.jpg', 'image/jpeg', 5744225, 0),
(566, '5_birthday_1.JPG', 'content/5_birthday_1_0.JPG', 'image/jpeg', 1922298, 0),
(567, '5_football.JPG', 'content/5_football_0.JPG', 'image/jpeg', 272228, 0),
(568, 'logo_6.png', 'content/logo_6_0.png', 'image/png', 5735, 0),
(569, '7394_0.jpg', 'content/7394_0_0.jpg', 'image/jpeg', 8988, 0),
(570, '7394_0.jpg', 'content/7394_0_0.jpg', 'image/jpeg', 8988, 0),
(571, '7394_0.jpg', 'content/7394_0_1.jpg', 'image/jpeg', 8988, 0),
(572, '7394_0.jpg', 'content/7394_0_2.jpg', 'image/jpeg', 8988, 0),
(573, '7394_0.jpg', 'content/7394_0_3.jpg', 'image/jpeg', 8988, 0),
(574, '7394_0.jpg', 'content/7394_0_3.jpg', 'image/jpeg', 8988, 0),
(575, '7394_0.jpg', 'content/7394_0_3.jpg', 'image/jpeg', 8988, 0),
(576, '7394_0.jpg', 'content/7394_0_0.jpg', 'image/jpeg', 8988, 0),
(577, '2в_10.jpg', 'content/2v_10_0.jpg', 'image/jpeg', 2295356, 0),
(578, '2a.jpg', 'content/2a_0.jpg', 'image/jpeg', 2006482, 0),
(579, '5_city_6.jpg', 'content/5_city_6_0.jpg', 'image/jpeg', 4660236, 0),
(580, '5_athlet_5.jpg', 'content/5_athlet_5_0.jpg', 'image/jpeg', 5422772, 0),
(581, '6_pascua toro_19.JPG', 'content/6_pascua_toro_19_0.JPG', 'image/jpeg', 6708148, 0),
(582, '5_football_4.jpg', 'content/5_football_4_0.jpg', 'image/jpeg', 6378315, 0),
(583, '5_ski_5.JPG', 'content/5_ski_5_0.JPG', 'image/jpeg', 5248331, 0),
(584, '5_birthday_2.JPG', 'content/5_birthday_2_0.JPG', 'image/jpeg', 3021140, 0),
(585, '5_athlet_2.JPG', 'content/5_athlet_2_0.JPG', 'image/jpeg', 1040664, 0),
(586, '3а_1.JPG', 'content/3a_1_0.JPG', 'image/jpeg', 5391981, 0),
(587, '5_portrait_passport_5.JPG', 'content/5_portrait_passport_5_0.JPG', 'image/jpeg', 234851, 0),
(588, '5_city_4.jpg', 'content/5_city_4_0.jpg', 'image/jpeg', 4403301, 0),
(589, '5_nature_2.JPG', 'content/5_nature_2_0.JPG', 'image/jpeg', 7600878, 0),
(590, '5_birthday_9.jpg', 'content/5_birthday_9_0.jpg', 'image/jpeg', 5015120, 0),
(591, '1_2.JPG', 'content/1_2_0.JPG', 'image/jpeg', 6135325, 0),
(592, '5_ski_4.JPG', 'content/5_ski_4_0.JPG', 'image/jpeg', 3332886, 0),
(593, '5_3.jpg', 'content/5_3_0.jpg', 'image/jpeg', 253561, 0),
(594, '5_nature_5.jpg', 'content/5_nature_5_0.jpg', 'image/jpeg', 4334271, 0),
(595, '6_pascua toro_10.jpg', 'content/6_pascua_toro_10_0.jpg', 'image/jpeg', 5664993, 0),
(596, '5_swim.jpg', 'content/5_swim_0.jpg', 'image/jpeg', 6319631, 0),
(597, '5_football_4.jpg', 'content/5_football_4_0.jpg', 'image/jpeg', 6378315, 0),
(598, '3б_6.jpg', 'content/3b_6_0.jpg', 'image/jpeg', 4813880, 0),
(599, '5_open air_2.jpg', 'content/5_open_air_2_0.jpg', 'image/jpeg', 2253422, 0),
(600, '4SPS.jpg', 'content/4SPS_0.jpg', 'image/jpeg', 5744225, 0),
(601, '5_birthday_1.JPG', 'content/5_birthday_1_0.JPG', 'image/jpeg', 1922298, 0),
(602, 'logo_3.png', 'content/logo_3_0.png', 'image/png', 2627, 0),
(603, '2б.JPG', 'content/2b_0.JPG', 'image/jpeg', 171627, 0),
(604, '3б_3.jpg', 'content/3b_3_0.jpg', 'image/jpeg', 4636243, 0),
(605, '3в_1.JPG', 'content/3v_1_0.JPG', 'image/jpeg', 4683927, 0),
(606, '3в_2.JPG', 'content/3v_2_0.JPG', 'image/jpeg', 3721626, 0),
(607, '3г_2.JPG', 'content/3g_2_0.JPG', 'image/jpeg', 5148823, 0),
(608, '3а_2.JPG', 'content/3a_2_0.JPG', 'image/jpeg', 2324369, 0),
(609, '2a_2.JPG', 'content/2a_2_0.JPG', 'image/jpeg', 6338383, 0),
(610, '6_cockfights_3.JPG', 'content/6_cockfights_3_0.JPG', 'image/jpeg', 4680374, 0),
(611, '6_Carnival of miners_7.jpg', 'content/6_Carnival_of_miners_7_0.jpg', 'image/jpeg', 3667914, 0),
(612, '6_Carnival of miners_13.jpg', 'content/6_Carnival_of_miners_13_0.jpg', 'image/jpeg', 3511902, 0),
(613, '6_Carnival of miners_15.jpg', 'content/6_Carnival_of_miners_15_0.jpg', 'image/jpeg', 3806355, 0),
(614, '6_Carnival of miners_8.jpg', 'content/6_Carnival_of_miners_8_0.jpg', 'image/jpeg', 6290595, 0),
(615, '6_kupala_7.jpg', 'content/6_kupala_7_0.jpg', 'image/jpeg', 4901663, 0),
(616, '6_kupala_8.JPG', 'content/6_kupala_8_0.JPG', 'image/jpeg', 5206401, 0),
(617, '6_kupala_1.JPG', 'content/6_kupala_1_0.JPG', 'image/jpeg', 3130293, 0),
(618, '6_kupala_17.JPG', 'content/6_kupala_17_0.JPG', 'image/jpeg', 2884729, 0),
(619, '6_cockfights_5.jpg', 'content/6_cockfights_5_0.jpg', 'image/jpeg', 8001128, 0),
(620, '6_cockfights_8.JPG', 'content/6_cockfights_8_0.JPG', 'image/jpeg', 7672268, 0),
(621, '6_pascua toro_3.jpg', 'content/6_pascua_toro_3_0.jpg', 'image/jpeg', 3516735, 0),
(622, 'attack02_0.jpeg', 'content/attack02_0_0.jpeg', 'image/jpeg', 58475, 0),
(623, 'attack02_0.jpeg', 'content/attack02_0_1.jpeg', 'image/jpeg', 58475, 0),
(624, 'anyday-00113599_2.jpg', 'content/anyday-00113599_2_0.jpg', 'image/jpeg', 11259, 0),
(625, 'anyday-00113601_1.jpg', 'content/anyday-00113601_1_0.jpg', 'image/jpeg', 13461, 0),
(626, 'arrest_0.png', 'content/arrest_0_0.png', 'image/png', 203815, 0),
(627, 'anyday-00113599_2.jpg', 'content/anyday-00113599_2_0.jpg', 'image/jpeg', 11259, 0),
(628, 'attack02_0.jpeg', 'content/attack02_0_0.jpeg', 'image/jpeg', 58475, 0),
(629, 'art_stub_3_0.jpg', 'content/art_stub_3_0_0.jpg', 'image/jpeg', 26120, 0),
(630, 'art_stub_3_0.jpg', 'content/art_stub_3_0_0.jpg', 'image/jpeg', 26120, 0),
(631, 'anyday-00113601_1.jpg', 'content/anyday-00113601_1_0.jpg', 'image/jpeg', 13461, 0),
(632, 'attack02_0.jpeg', 'content/attack02_0_0.jpeg', 'image/jpeg', 58475, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_group`
--

CREATE TABLE IF NOT EXISTS `fx_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=197 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `fx_group`
--

INSERT INTO `fx_group` (`id`, `name`) VALUES
(1, 'Administrators'),
(2, 'External users'),
(3, 'Authorized by external services');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=52 COMMENT='История операций' AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=373 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=210 AUTO_INCREMENT=406 ;

--
-- Дамп данных таблицы `fx_infoblock`
--

INSERT INTO `fx_infoblock` (`id`, `parent_infoblock_id`, `site_id`, `page_id`, `checked`, `name`, `controller`, `action`, `params`, `scope`) VALUES
(54, 12, 1, 78, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(55, 54, 1, 97, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(61, 0, 1, 2, 1, 'Text / listing', 'component_text', 'list_infoblock', 'a:6:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(67, 0, 15, 0, 1, 'Layout', 'layout', 'show', '', ''),
(69, 0, 15, 1883, 1, 'Main menu', 'component_section', 'list_infoblock', 'a:1:{s:7:"submenu";s:3:"all";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(70, 0, 15, 1883, 1, 'Routes', 'component_travel_route', 'list_infoblock', 'a:6:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:13:"mount_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(71, 67, 15, 1883, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(72, 0, 15, 1883, 1, 'Starting', 'component_travel_route', 'list_filtered', 'a:6:{s:5:"limit";s:1:"4";s:15:"show_pagination";b:0;s:7:"sorting";s:10:"start_date";s:11:"sorting_dir";s:4:"desc";s:8:"from_all";s:1:"1";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(74, 0, 15, 1883, 1, 'Page text', 'component_text', 'list_infoblock', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:0:"";}'),
(75, 0, 15, 1883, 1, 'Index text', 'component_text', 'list_infoblock', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(76, 0, 15, 1883, 1, 'Social networks', 'component_section', 'list_infoblock', 'a:1:{s:7:"submenu";s:4:"none";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(77, 0, 15, 1887, 1, 'Menu / About', 'component_section', 'list_infoblock', 'a:1:{s:7:"submenu";s:4:"none";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(78, 0, 15, 1883, 1, ' Breadcrumbs', 'component_section', 'breadcrumbs', 'a:2:{s:11:"header_only";s:1:"0";s:13:"hide_on_index";s:1:"1";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(83, 0, 15, 1925, 1, 'Tag cloud', 'component_tag', 'list_infoblock', 'a:6:{s:5:"limit";s:0:"";s:15:"show_pagination";b:0;s:7:"sorting";s:4:"name";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:13:"mount_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(85, 0, 15, 1883, 1, 'Photo / mirror', 'component_photo', 'list_filtered', 'a:6:{s:5:"limit";s:1:"3";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:7:"created";s:11:"sorting_dir";s:4:"desc";s:8:"from_all";s:1:"1";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(99, 71, 15, 1883, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(100, 99, 15, 1883, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(104, 0, 15, 1883, 1, 'Text in sidebar', 'component_text', 'list_infoblock', 'a:1:{s:11:"parent_type";s:15:"current_page_id";}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:0:"";}'),
(121, 0, 15, 1891, 1, 'Image / List', 'component_photo', 'list_infoblock', 'a:4:{s:5:"limit";s:2:"10";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(126, 0, 15, 2075, 1, 'Navigation / Alter sub', 'component_section', 'list_infoblock', 'a:1:{s:7:"submenu";s:4:"none";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(127, 0, 15, 1925, 1, 'Comment / List', 'component_comment', 'list_infoblock', 'a:6:{s:5:"limit";s:0:"";s:15:"show_pagination";b:0;s:7:"sorting";s:12:"publish_date";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:8:"blogpost";}'),
(128, 0, 15, 1925, 1, 'Comment / add', 'component_comment', 'add', 'a:1:{s:19:"target_infoblock_id";s:3:"127";}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:8:"blogpost";}'),
(144, 0, 15, 0, 1, '', 'component_publication', 'calendar', 'a:0:{}', 'a:2:{s:5:"pages";s:3:"all";s:9:"page_type";s:11:"publication";}'),
(147, 0, 15, 2149, 1, '', 'component_news', 'list_infoblock', 'a:6:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(148, 0, 15, 2149, 1, '', 'component_news', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:4:"news";}'),
(151, 0, 15, 2149, 1, '', 'component_news', 'listing_by_tag', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:8:"news_tag";}'),
(152, 0, 15, 2179, 1, '', 'component_person', 'list_infoblock', 'a:6:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(153, 0, 15, 2181, 1, '', 'component_complex_photo', 'list_infoblock', 'a:6:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(154, 0, 15, 2181, 1, '', 'component_complex_photo', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:13:"complex_photo";}'),
(155, 0, 15, 2187, 1, '', 'component_complex_video', 'list_infoblock', 'a:6:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(156, 0, 15, 2179, 1, '', 'component_person', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:6:"person";}'),
(157, 0, 16, 0, 1, '', 'layout', 'show', 'a:0:{}', 'a:2:{s:5:"pages";s:3:"all";s:9:"page_type";s:0:"";}'),
(158, 0, 16, 2210, 1, 'Main menu', 'component_section', 'list_infoblock', 'a:4:{s:5:"limit";s:1:"0";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:7:"submenu";s:3:"all";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(167, 0, 16, 2210, 1, '', 'component_text', 'list_infoblock', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(172, 0, 16, 2210, 1, '', 'component_photo', 'list_infoblock', 'a:6:{s:5:"limit";s:1:"1";s:15:"show_pagination";s:1:"0";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(173, 0, 16, 0, 1, '', 'component_person', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:3:"all";s:9:"page_type";s:6:"person";}'),
(178, 0, 16, 2212, 1, '', 'component_text', 'list_infoblock', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(179, 0, 16, 2212, 1, '', 'component_faq', 'list_infoblock', 'a:6:{s:5:"limit";s:1:"1";s:15:"show_pagination";s:1:"0";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:13:"mount_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(180, 0, 16, 2233, 1, 'Person / List', 'component_person', 'list_infoblock', 'a:7:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";s:19:"field_269_infoblock";s:3:"new";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(181, 157, 16, 2233, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(182, 0, 16, 2233, 1, 'Person / List', 'component_person', 'list_infoblock', 'a:7:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";s:19:"field_269_infoblock";s:3:"new";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(183, 0, 16, 2233, 1, 'Person / Single entry', 'component_person', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:6:"person";}'),
(185, 157, 16, 2242, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(186, 0, 16, 2242, 1, 'Award / List', 'component_award', 'list_infoblock', 'a:6:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:4:"year";s:11:"sorting_dir";s:4:"desc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(188, 0, 16, 2242, 1, 'Award / Single entry', 'component_award', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:5:"award";}'),
(189, 0, 16, 2245, 1, 'Text / List', 'component_text', 'list_infoblock', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(191, 157, 16, 2210, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(192, 157, 16, 2248, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(193, 0, 16, 2248, 1, 'Vacancy / List', 'component_vacancy', 'list_infoblock', 'a:6:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(194, 0, 16, 2248, 1, 'Vacancy / Single entry', 'component_vacancy', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:7:"vacancy";}'),
(196, 0, 16, 2251, 1, 'Project / List', 'component_project', 'list_infoblock', 'a:6:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:4:"date";s:11:"sorting_dir";s:4:"desc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(197, 0, 16, 2251, 1, 'Project / List in about', 'component_project', 'list_infoblock', 'a:5:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(199, 157, 16, 2251, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(200, 0, 16, 2251, 1, 'Project / Single entry', 'component_project', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:7:"project";}'),
(202, 157, 16, 2254, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(203, 0, 16, 2254, 1, 'News / List', 'component_news', 'list_infoblock', 'a:5:{s:5:"limit";b:0;s:7:"sorting";b:0;s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";b:0;s:19:"field_256_infoblock";s:3:"214";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(204, 0, 16, 2254, 1, 'News / Single entry', 'component_news', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:4:"news";}'),
(205, 157, 16, 2254, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:4:"news";}'),
(206, 0, 16, 2254, 1, 'News / Single entry', 'component_news', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:4:"news";}'),
(207, 0, 16, 2257, 1, 'Product / List', 'component_product', 'list_infoblock', 'a:6:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(208, 157, 16, 2257, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(209, 0, 16, 2257, 1, 'Product / List', 'component_product', 'list_infoblock', 'a:5:{s:5:"limit";b:0;s:7:"sorting";b:0;s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";b:0;s:19:"field_284_infoblock";s:3:"219";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(212, 0, 16, 2254, 1, 'News / Calendar', 'component_news', 'calendar', 'a:1:{s:19:"source_infoblock_id";s:3:"203";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(216, 0, 16, 2254, 1, 'News / by tag', 'component_news', 'listing_by_tag', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:8:"news_tag";}'),
(217, 157, 16, 2212, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(218, 157, 16, 2245, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(219, 0, 16, 2257, 1, 'Product / Categories', 'component_product_category', 'list_infoblock', 'a:4:{s:5:"limit";s:3:"100";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:4:"desc";s:11:"parent_type";s:13:"mount_page_id";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(220, 0, 16, 2257, 1, 'Product / Single entry', 'component_product', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:7:"product";}'),
(221, 0, 16, 2257, 1, 'Product / by category', 'component_product', 'listing_by_category', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:16:"product_category";}'),
(228, 157, 16, 2248, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:0:"";}'),
(229, 0, 16, 0, 1, 'Auth / Widget', 'widget_authform', 'show', 'a:0:{}', 'a:2:{s:5:"pages";s:3:"all";s:9:"page_type";s:0:"";}'),
(230, 0, 16, 2210, 1, 'Searchline', 'widget_search', 'show', 'a:0:{}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";N;}'),
(232, 0, 16, 2210, 1, 'Project / On Main', 'component_project', 'list_filtered', 'a:5:{s:5:"limit";s:1:"2";s:10:"pagination";s:1:"0";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:4:"desc";s:10:"conditions";b:0;}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(234, 0, 16, 2210, 1, 'Person / On Main', 'component_person', 'list_filtered', 'a:5:{s:5:"limit";s:1:"1";s:10:"pagination";s:1:"0";s:7:"sorting";s:7:"created";s:11:"sorting_dir";s:3:"asc";s:10:"conditions";b:0;}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(235, 0, 16, 2212, 1, 'Award / On Right', 'component_award', 'list_filtered', 'a:5:{s:5:"limit";s:1:"2";s:10:"pagination";s:1:"0";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:10:"conditions";b:0;}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(236, 0, 16, 2212, 1, 'Vacancy / On Right', 'component_vacancy', 'list_filtered', 'a:5:{s:5:"limit";s:1:"4";s:10:"pagination";s:1:"0";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:10:"conditions";b:0;}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(237, 0, 16, 2212, 1, 'Navigation / ', 'component_section', 'list_submenu', 'a:6:{s:5:"limit";s:1:"0";s:10:"pagination";s:1:"0";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:7:"submenu";s:4:"none";s:19:"source_infoblock_id";s:3:"158";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(241, 0, 16, 2210, 1, 'Product / On Main', 'component_product', 'list_filtered_featured', 'a:4:{s:5:"limit";s:1:"4";s:10:"pagination";s:1:"0";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(258, 0, 16, 0, 1, 'Product / ', 'component_product', 'list_selected', 'a:5:{s:5:"limit";s:2:"10";s:10:"pagination";s:1:"0";s:7:"sorting";s:7:"created";s:11:"sorting_dir";s:3:"asc";s:8:"selected";a:1:{s:3:"f_1";a:1:{i:0;s:4:"2327";}}}', 'a:2:{s:5:"pages";s:3:"all";s:9:"page_type";s:0:"";}'),
(260, 0, 16, 2210, 1, 'News / ', 'component_news', 'list_filtered', 'a:5:{s:5:"limit";s:1:"1";s:10:"pagination";s:1:"0";s:7:"sorting";s:7:"created";s:11:"sorting_dir";s:3:"asc";s:10:"conditions";b:0;}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(262, 0, 17, 0, 1, '', 'layout', 'show', 'a:0:{}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(278, 0, 17, 2501, 1, 'Authorization form / Widget', 'widget_authform', 'show', 'a:0:{}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(284, 0, 17, 2501, 1, 'Navigation / Main', 'component_section', 'list_infoblock', 'a:1:{s:7:"submenu";s:3:"all";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(294, 0, 17, 2501, 1, 'Navigation / ', 'component_section', 'list_filtered', 'a:2:{s:10:"conditions";a:1:{s:5:"new_1";a:3:{s:4:"name";s:12:"infoblock_id";s:8:"operator";s:1:"=";s:5:"value";a:1:{i:0;s:3:"284";}}}s:7:"submenu";s:3:"all";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(300, 262, 17, 2501, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(303, 0, 17, 2510, 1, 'Product / ', 'component_product_category', 'list_infoblock', 'a:4:{s:5:"limit";s:0:"";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:13:"mount_page_id";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(307, 262, 17, 2562, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(309, 0, 17, 2562, 1, 'News / ', 'component_news', 'list_filtered', 'a:5:{s:5:"limit";s:0:"";s:10:"pagination";s:1:"0";s:7:"sorting";s:7:"created";s:11:"sorting_dir";s:3:"asc";s:10:"conditions";b:0;}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(311, 0, 17, 2562, 1, 'Image / ', 'component_photo', 'list_infoblock', 'a:4:{s:5:"limit";s:0:"";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(313, 0, 17, 2510, 1, 'Product / ', 'component_product', 'listing_by_category', 'a:4:{s:5:"limit";s:0:"";s:10:"pagination";s:1:"0";s:7:"sorting";s:7:"created";s:11:"sorting_dir";s:3:"asc";}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:16:"product_category";}'),
(315, 0, 17, 2567, 1, 'Tag / ', 'component_tag', 'list_infoblock', 'a:5:{s:5:"limit";s:0:"";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:13:"mount_page_id";s:16:"create_record_ib";s:1:"0";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(316, 0, 17, 2567, 1, 'News / ', 'component_news', 'list_infoblock', 'a:5:{s:5:"limit";s:0:"";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:19:"field_295_infoblock";s:3:"315";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(317, 0, 17, 2501, 1, 'News / ', 'component_news', 'list_filtered', 'a:5:{s:5:"limit";s:1:"4";s:10:"pagination";s:1:"0";s:7:"sorting";s:7:"created";s:11:"sorting_dir";s:3:"asc";s:10:"conditions";a:1:{s:5:"new_1";a:2:{s:4:"name";s:12:"publish_date";s:8:"operator";s:7:"in_past";}}}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(319, 0, 17, 2567, 1, 'News / ', 'component_news', 'listing_by_tag', 'a:4:{s:5:"limit";s:0:"";s:10:"pagination";s:1:"0";s:7:"sorting";s:7:"created";s:11:"sorting_dir";s:3:"asc";}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:3:"tag";}'),
(320, 0, 17, 2567, 1, 'News / Single entry', 'component_news', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:4:"news";}'),
(323, 0, 17, 2510, 1, 'Product / ', 'component_product', 'list_selected', 'a:2:{s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(324, 0, 17, 2562, 1, 'Text / ', 'component_text', 'list_infoblock', 'a:1:{s:11:"parent_type";s:15:"current_page_id";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(325, 0, 17, 2591, 1, 'Person / ', 'component_person', 'list_infoblock', 'a:4:{s:5:"limit";s:0:"";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(326, 0, 17, 2592, 1, 'Person / Single entry', 'component_person', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(327, 0, 17, 2593, 1, 'Vacancy / ', 'component_vacancy', 'list_infoblock', 'a:5:{s:5:"limit";s:0:"";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:16:"create_record_ib";s:1:"0";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(328, 0, 17, 2593, 1, 'Vacancy / Single entry', 'component_vacancy', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:7:"vacancy";}'),
(330, 0, 17, 2562, 1, 'Navigation / ', 'component_section', 'list_filtered', 'a:2:{s:10:"conditions";a:1:{s:5:"new_1";a:3:{s:4:"name";s:9:"parent_id";s:8:"operator";s:1:"=";s:5:"value";a:1:{i:0;s:4:"2562";}}}s:7:"submenu";s:4:"none";}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:0:"";}'),
(331, 0, 17, 2596, 1, 'Project / ', 'component_project', 'list_infoblock', 'a:5:{s:5:"limit";s:0:"";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:16:"create_record_ib";s:1:"0";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(332, 0, 17, 2596, 1, 'Project / Single entry', 'component_project', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:7:"project";}'),
(333, 0, 17, 2598, 1, 'Award / ', 'component_award', 'list_infoblock', 'a:5:{s:5:"limit";s:0:"";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:16:"create_record_ib";s:1:"0";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(334, 0, 17, 2598, 1, 'Award / Single entry', 'component_award', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:5:"award";}'),
(335, 0, 17, 2567, 1, 'Navigation / ', 'component_section', 'list_infoblock', 'a:1:{s:7:"submenu";s:4:"none";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(336, 0, 17, 2601, 1, 'News / last week', 'component_news', 'list_filtered', 'a:5:{s:5:"limit";s:0:"";s:10:"pagination";s:1:"0";s:7:"sorting";s:12:"publish_date";s:11:"sorting_dir";s:3:"asc";s:10:"conditions";a:1:{s:5:"new_1";a:4:{s:4:"name";s:12:"publish_date";s:8:"operator";s:4:"last";s:5:"value";s:1:"1";s:8:"interval";s:4:"WEEK";}}}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(337, 0, 17, 2602, 1, 'News / ', 'component_news', 'list_filtered', 'a:5:{s:5:"limit";s:0:"";s:10:"pagination";s:1:"0";s:7:"sorting";s:12:"publish_date";s:11:"sorting_dir";s:3:"asc";s:10:"conditions";a:1:{s:5:"new_1";a:4:{s:4:"name";s:12:"publish_date";s:8:"operator";s:4:"last";s:5:"value";s:1:"1";s:8:"interval";s:5:"MONTH";}}}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(338, 0, 17, 2501, 1, 'Social Icons', 'component_social_icon', 'list_infoblock', 'a:4:{s:5:"limit";s:0:"";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(339, 0, 17, 2501, 1, 'Project / ', 'component_project', 'list_selected', 'a:2:{s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(341, 0, 17, 2501, 1, 'Product / ', 'component_product_category', 'list_filtered', 'a:5:{s:5:"limit";s:0:"";s:10:"pagination";s:1:"0";s:7:"sorting";s:4:"name";s:11:"sorting_dir";s:3:"asc";s:10:"conditions";a:1:{s:5:"new_1";a:2:{s:4:"name";s:9:"parent_id";s:8:"operator";s:1:"=";}}}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(342, 0, 17, 2501, 1, 'Product / ', 'component_product', 'list_selected', 'a:2:{s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(343, 0, 17, 2501, 1, 'Product / ', 'component_product', 'list_filtered', 'a:5:{s:5:"limit";s:1:"4";s:10:"pagination";s:1:"0";s:7:"sorting";s:7:"created";s:11:"sorting_dir";s:3:"asc";s:10:"conditions";a:1:{s:5:"new_1";a:2:{s:4:"name";s:9:"parent_id";s:8:"operator";s:1:"=";}}}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(344, 0, 17, 2510, 1, 'Product / ', 'component_product', 'list_infoblock', 'a:5:{s:5:"limit";s:0:"";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:19:"field_284_infoblock";s:3:"303";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(345, 0, 18, 2657, 1, '', 'layout', 'show', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:4:"news";}'),
(346, 0, 18, 2635, 1, 'Navigation / ', 'component_section', 'list_infoblock', 'a:1:{s:7:"submenu";s:3:"all";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(347, 0, 18, 2635, 1, 'Navigation / ', 'component_section', 'list_filtered', 'a:2:{s:10:"conditions";a:1:{s:5:"new_1";a:2:{s:4:"name";s:9:"parent_id";s:8:"operator";s:1:"=";}}s:7:"submenu";s:4:"none";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(348, 0, 18, 2635, 1, ' / ', 'component_social_icon', 'list_infoblock', 'a:4:{s:5:"limit";s:0:"";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:13:"mount_page_id";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(349, 0, 18, 2635, 1, 'Authorization form / Widget', 'widget_authform', 'show', 'a:0:{}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(359, 345, 18, 2638, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(360, 0, 18, 2635, 1, 'Navigation / breadcrumbs', 'component_section', 'breadcrumbs', 'a:2:{s:11:"header_only";s:1:"0";s:13:"hide_on_index";s:1:"0";}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:0:"";}'),
(361, 0, 18, 2638, 1, 'Navigation / ', 'component_section', 'list_filtered', 'a:2:{s:10:"conditions";a:1:{s:5:"new_1";a:3:{s:4:"name";s:9:"parent_id";s:8:"operator";s:1:"=";s:5:"value";a:1:{i:0;s:4:"2638";}}}s:7:"submenu";s:4:"none";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(362, 0, 18, 2638, 1, 'Navigation / ', 'component_section', 'list_selected', 'a:1:{s:7:"submenu";s:4:"none";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(363, 345, 18, 2638, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:7:"section";}'),
(364, 0, 18, 2638, 1, 'Product / ', 'component_product', 'list_infoblock', 'a:4:{s:5:"limit";s:0:"";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:7:"section";}'),
(366, 345, 18, 2652, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:7:"product";}'),
(367, 0, 18, 2638, 1, 'Product / Single entry', 'component_product', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:7:"product";}'),
(369, 0, 18, 2635, 1, 'Product / ', 'component_product', 'list_selected', 'a:2:{s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(370, 0, 18, 2635, 1, 'Product / ', 'component_product', 'list_selected', 'a:2:{s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(371, 345, 18, 2655, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(372, 0, 18, 2655, 1, 'Person / ', 'component_person', 'list_infoblock', 'a:5:{s:5:"limit";s:0:"";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:16:"create_record_ib";s:1:"0";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(373, 345, 18, 2656, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(374, 0, 18, 2656, 1, 'Vacancy / ', 'component_vacancy', 'list_infoblock', 'a:5:{s:5:"limit";s:0:"";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:16:"create_record_ib";s:1:"0";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(376, 0, 18, 2656, 1, 'Vacancy / Single entry', 'component_vacancy', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:7:"vacancy";}'),
(378, 345, 18, 2657, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:0:"";}'),
(379, 0, 18, 2657, 1, 'News', 'component_news', 'list_infoblock', 'a:4:{s:5:"limit";s:0:"";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(381, 0, 18, 2635, 1, 'Featured news', 'component_news', 'list_selected', 'a:2:{s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(382, 345, 18, 2639, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(385, 0, 18, 2639, 1, 'Project / ', 'component_project', 'list_infoblock', 'a:4:{s:5:"limit";s:0:"";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:13:"mount_page_id";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(386, 0, 18, 2639, 1, 'Project / Single entry', 'component_project', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:7:"project";}'),
(387, 0, 18, 2635, 1, 'Project / ', 'component_project', 'list_selected', 'a:2:{s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(388, 0, 18, 2638, 1, 'Navigation / ', 'component_section', 'list_filtered', 'a:2:{s:10:"conditions";a:1:{s:5:"new_1";a:3:{s:4:"name";s:9:"parent_id";s:8:"operator";s:1:"=";s:5:"value";a:1:{i:0;s:4:"2638";}}}s:7:"submenu";s:4:"none";}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:0:"";}'),
(389, 0, 18, 2639, 1, 'Project gallery', 'component_photo', 'list_infoblock', 'a:4:{s:5:"limit";s:0:"";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:7:"project";}'),
(390, 345, 18, 2640, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(391, 0, 18, 2640, 1, 'Navigation / ', 'component_section', 'list_selected', 'a:1:{s:7:"submenu";s:4:"none";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(392, 0, 18, 2640, 1, 'Last news', 'component_news', 'list_filtered', 'a:5:{s:5:"limit";s:1:"4";s:10:"pagination";s:1:"0";s:7:"sorting";s:7:"created";s:11:"sorting_dir";s:3:"asc";s:10:"conditions";a:1:{s:5:"new_1";a:2:{s:4:"name";s:9:"parent_id";s:8:"operator";s:1:"=";}}}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(393, 345, 18, 2641, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(396, 0, 18, 2641, 1, 'Text / ', 'component_text', 'list_infoblock', 'a:1:{s:11:"parent_type";s:15:"current_page_id";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(397, 0, 18, 2641, 1, 'Text / ', 'component_text', 'list_infoblock', 'a:2:{s:5:"limit";s:0:"";s:11:"parent_type";s:15:"current_page_id";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(398, 0, 18, 2657, 1, 'News / Single entry', 'component_news', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:4:"news";}'),
(399, 345, 18, 2635, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(400, 0, 18, 2635, 1, 'Navigation / ', 'component_section', 'list_filtered', 'a:2:{s:10:"conditions";a:1:{s:5:"new_1";a:3:{s:4:"name";s:9:"parent_id";s:8:"operator";s:1:"=";s:5:"value";a:1:{i:0;s:4:"2638";}}}s:7:"submenu";s:4:"none";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(402, 366, 18, 2638, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:7:"product";}'),
(403, 378, 18, 2640, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:0:"";}'),
(404, 0, 18, 2640, 1, 'Navigation / ', 'component_section', 'list_filtered', 'a:2:{s:10:"conditions";a:1:{s:5:"new_1";a:3:{s:4:"name";s:9:"parent_id";s:8:"operator";s:1:"=";s:5:"value";a:1:{i:0;s:4:"2640";}}}s:7:"submenu";s:4:"none";}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:0:"";}'),
(405, 403, 18, 2640, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:0:"";}');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=138 AUTO_INCREMENT=478 ;

--
-- Дамп данных таблицы `fx_infoblock_visual`
--

INSERT INTO `fx_infoblock_visual` (`id`, `infoblock_id`, `layout_id`, `wrapper`, `wrapper_visual`, `template`, `template_visual`, `area`, `priority`) VALUES
(56, 54, 1, '', '', 'layout_supernova.index', '', '', 2),
(57, 55, 1, '', '', 'layout_supernova.inner', '', '', 3),
(59, 57, 1, 'layout_supernova.wrap_titled', '', 'component_page.list', '', 'content', 8),
(98, 54, 8, '', '', 'layout_demo8.index', '', '', 0),
(111, 55, 8, '', '', 'layout_demo8.index', '', '', 0),
(125, 61, 8, '', '', 'auto.auto', '', 'banner', 0),
(126, 61, 1, '', '', 'component_text.list', '', '', 0),
(134, 67, 1, '', '', 'layout_supernova.index', '', '', 0),
(137, 67, 10, '', '', 'layout_jeeptravel.page', 'a:31:{s:18:"page_bg_color_1895";s:0:"";s:18:"page_bg_color_1888";s:7:"#E9A502";s:18:"page_bg_image_1888";s:52:"/controllers/layout/jeeptravel/images/bg-company.jpg";s:18:"page_bg_image_1895";s:0:"";s:18:"page_bg_color_1887";s:4:"#000";s:18:"page_bg_image_1887";s:38:"/floxim_files/content/bg-company_2.jpg";s:5:"phone";s:14:"+7  561 99 75";s:4:"mail";s:19:"info@jeeptravel.loc";s:18:"page_bg_color_1889";s:7:"#c7c1c7";s:18:"page_bg_image_1889";s:40:"/floxim_files/content/bg-portfolio_0.jpg";s:18:"page_bg_color_1890";s:7:"#E9A502";s:18:"page_bg_image_1890";s:40:"/floxim_files/content/bg-portfolio_3.jpg";s:18:"page_bg_color_1925";s:7:"#7a7a7a";s:18:"page_bg_image_1925";s:0:"";s:18:"page_bg_color_1926";s:7:"#500070";s:18:"page_bg_image_1926";s:3:"326";s:4:"logo";s:34:"/floxim_files/content/logo_6_0.png";s:18:"page_bg_image_1891";s:0:"";s:18:"page_bg_image_1968";s:0:"";s:14:"contacts_label";s:8:"Call us:";s:4:"copy";s:93:"© JeepTravel, 2013<br>&nbsp; &nbsp; Photo by: <a href="http://leecannon.com/">Lee Cannon</a>";s:18:"page_bg_image_1883";s:0:"";s:18:"page_bg_image_1996";s:0:"";s:18:"page_bg_image_1884";s:0:"";s:18:"page_bg_color_1883";s:7:"#fafafa";s:18:"page_bg_color_1884";s:7:"#000000";s:18:"page_bg_image_1916";s:0:"";s:18:"page_bg_image_1902";s:0:"";s:18:"page_bg_image_2033";s:0:"";s:5:"email";s:11:"info@jt.com";s:18:"page_bg_color_1996";s:7:"#000000";}', '', 1),
(139, 69, 10, '', '', 'layout_jeeptravel.top_menu', '', 'header', 1),
(140, 70, 10, '', '', 'layout_jeeptravel.index_slider', 'a:15:{s:9:"info_1891";s:510:"<dt><strong>Difficulty:</strong> easy<br>\n <strong>Cities:</strong> <span data-redactor="verified" style="color: rgb(217, 150, 148);">Gada</span>, <span data-redactor="verified" style="color: rgb(255, 255, 0);">B<strong>a</strong>lle</span>, <a href="https://google.com"><strong><span data-redactor="verified" style="color: rgb(242, 195, 20);">Binji</span></strong></a>, Wurno<br>\n <strong>Villages:</strong> Kaita, Rimi<span data-redactor="verified" style="color: rgb(84, 141, 212);"><br>​</span><br>\n </dt>";s:14:"more_text_1891";s:12:"Tell me more";s:16:"action_text_1891";s:14:"Gonna b there!";s:9:"date_1891";s:9:"May 12-15";s:11:"header_1891";s:16:"Summer Rally<br>";s:13:"bg_photo_1892";s:40:"/floxim_files/content/bg-portfolio_6.jpg";s:11:"header_1892";s:34:"It''s going to be<br>​Legen-dary!";s:16:"action_text_1892";s:15:"Yes, I''m crazy!";s:14:"more_text_1892";s:12:"Tell me more";s:9:"date_1892";s:16:"January 5 – 19";s:9:"info_1892";s:251:"<dl>\n                                            <dt>Difficulty: </dt>extremely difficult<br>​Period: 2 weeks<br>Cities<strong>: Paris, Dakar</strong><dd></dd><dt>A chance to survive:</dt><dd>~23.5%</dd>\n                                        </dl>";s:13:"bg_photo_1891";s:33:"/floxim_files/content/img01_1.jpg";s:15:"action_url_1891";s:18:"http://google.com/";s:15:"action_url_1892";s:0:"";s:13:"bg_photo_2085";s:39:"/floxim_files/content/oblako_edit_0.png";}', 'content', 3),
(141, 71, 10, '', '', 'layout_jeeptravel.index', '', '', 0),
(142, 72, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:8:"Upcoming";}', 'layout_jeeptravel.index_link_list', '', 'index_center', 1),
(144, 74, 10, '', '', 'component_text.list', '', 'content', 2),
(145, 75, 10, '', '', 'component_text.list', '', 'index_center', 2),
(146, 76, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:9:"Follow us";}', 'layout_jeeptravel.index_link_list', '', 'index_right', 1),
(147, 71, 1, '', '', 'layout_supernova.index', '', '', 0),
(148, 69, 1, '', '', 'layout_supernova.demo_menu', '', 'header', 1),
(149, 70, 1, '', '', 'component_page.list', '', 'content', 2),
(150, 72, 1, '', '', 'component_page.list', '', 'footer', 2),
(151, 75, 1, '', '', 'component_text.list', '', 'footer', 1),
(152, 76, 1, '', '', 'layout_supernova.demo_menu', '', '', 0),
(154, 74, 1, '', '', 'component_text.list', '', 'content', 1),
(155, 77, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:13:"Jeep Travels:";}', 'layout_jeeptravel.side_menu', '', 'sidebar', 4),
(156, 78, 10, '', '', 'component_page.breadcrumbs', 'a:1:{s:9:"separator";s:5:" / ";}', 'content', 1),
(161, 83, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:4:"Tags";}', 'component_tag.tag_list', '', 'sidebar', 2),
(163, 85, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:6:"Photos";}', 'layout_jeeptravel.index_photo_anounces', 'a:2:{s:10:"image_1916";s:0:"";s:10:"image_1913";s:0:"";}', 'index_left', 1),
(170, 99, 10, '', '', 'layout_jeeptravel.index', '', '', 0),
(171, 100, 10, '', '', 'layout_jeeptravel.index', '', '', 0),
(175, 104, 10, '', '', 'component_text.list', '', 'sidebar', 1),
(192, 121, 10, '', '', 'component_photo.listing_slider', '', 'content', 4),
(197, 126, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:8:"Alt menu";}', 'component_section.listing_deep', '', 'content', 5),
(198, 127, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:8:"Comments";}', 'component_comment.list', '', 'content', 6),
(199, 128, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:11:"Add Comment";}', 'component_comment.add', '', 'content', 7),
(215, 144, 10, '', '', 'component_publication.calendar', '', 'sidebar', 6),
(218, 147, 10, '', '', 'component_news.list', 'a:1:{s:10:"tags_label";s:5:"Tags:";}', 'content', 8),
(219, 148, 10, '', '', 'component_news.record', '', 'content', 9),
(222, 151, 10, '', '', 'component_news.list', '', 'content', 10),
(223, 152, 10, '', '', 'component_person.list', '', 'content', 11),
(224, 153, 10, '', '', 'component_complex_photo.list', 'a:1:{s:10:"tags_label";s:5:"Tags:";}', 'content', 12),
(225, 154, 10, '', '', 'component_complex_photo.record', '', 'content', 13),
(226, 155, 10, '', '', 'component_complex_video.list', 'a:1:{s:10:"tags_label";s:5:"Tags:";}', 'content', 14),
(227, 156, 10, '', '', 'component_person.record', '', 'content', 15),
(228, 157, 9, '', '', 'layout_dummy.two_columns', 'a:2:{s:4:"logo";s:35:"/floxim_files/content/logo_es_2.jpg";s:9:"add_phone";s:15:"8.800.213.23.45";}', '', 1),
(229, 158, 9, '', '', 'layout_dummy.top_menu', 'a:2:{s:4:"logo";s:35:"/floxim_files/content/logo_es_0.jpg";s:5:"brand";s:16:"Ut enim ad minim";}', 'menu', 1),
(238, 167, 9, 'layout_dummy.block_titled', 'a:1:{s:6:"header";s:21:"Lorem ipsum dolor sit";}', 'component_text.list', '', 'left_block', 1),
(243, 172, 9, 'layout_dummy.block_titled_bottom_left', 'a:1:{s:6:"header";s:18:"Photo Of The Month";}', 'layout_dummy.bottom_photo', '', 'bottom_right_block', 0),
(244, 173, 9, '', '', 'layout_dummy.person_record', '', 'two_columns_left_block', 0),
(249, 178, 9, 'layout_dummy.block_page', 'a:1:{s:6:"header";s:8:"About Us";}', 'component_text.list', '', 'three_columns_middle_block', 0),
(250, 179, 9, 'layout_dummy.block_primary_block', 'a:1:{s:6:"header";s:3:"FAQ";}', 'layout_dummy.left_faq', '', 'columns_left_block', 5),
(251, 180, 9, 'layout_dummy.block_page', '', 'layout_dummy.person_list', '', 'three_columns_middle_block', 1),
(252, 181, 9, '', '', 'layout_dummy.two_columns', '', '', 0),
(253, 182, 9, 'layout_dummy.block_page', 'a:1:{s:6:"header";s:9:"Employees";}', 'layout_dummy.person_list', '', 'two_columns_right_block', 1),
(254, 183, 9, '', '', 'layout_dummy.person_record', '', 'two_columns_right_block', 2),
(256, 185, 9, '', '', 'layout_dummy.two_columns', '', '', 0),
(257, 186, 9, 'layout_dummy.block_page', 'a:1:{s:6:"header";s:6:"Awards";}', 'layout_dummy.award_list', '', 'two_columns_right_block', 3),
(259, 188, 9, '', '', 'layout_dummy.award_record', '', 'two_columns_right_block', 4),
(260, 189, 9, 'layout_dummy.block_page', 'a:1:{s:6:"header";s:8:"Contacts";}', 'component_text.list', '', 'three_columns_middle_block', 2),
(262, 191, 9, '', '', 'layout_dummy.index', '', '', 0),
(263, 192, 9, '', '', 'layout_dummy.two_columns', '', '', 0),
(264, 193, 9, 'layout_dummy.block_page', 'a:1:{s:6:"header";s:9:"Vacancies";}', 'layout_dummy.vacancy_list', '', 'two_columns_right_block', 5),
(265, 194, 9, '', '', 'layout_dummy.vacancy_record', '', 'three_columns_middle_block', 3),
(267, 196, 9, 'layout_dummy.block_page', '', 'layout_dummy.project_list', '', 'three_columns_middle_block', 4),
(268, 197, 9, 'layout_dummy.block_page', 'a:1:{s:6:"header";s:8:"Projects";}', 'layout_dummy.project_list', '', 'two_columns_right_block', 6),
(270, 199, 9, '', '', 'layout_dummy.two_columns', '', '', 0),
(271, 200, 9, '', '', 'layout_dummy.project_record', '', 'two_columns_right_block', 7),
(273, 202, 9, '', '', 'layout_dummy.two_columns', '', '', 0),
(274, 203, 9, 'layout_dummy.block_page', 'a:1:{s:6:"header";s:4:"News";}', 'layout_dummy.news_list', 'a:1:{s:10:"tags_label";s:15:"Tags:         \n";}', 'two_columns_right_block', 13),
(275, 204, 9, '', '', 'layout_dummy.news_record', '', 'three_columns_middle_block', 6),
(276, 205, 9, '', '', 'layout_dummy.two_columns', '', '', 0),
(277, 206, 9, '', '', 'layout_dummy.news_record', 'a:1:{s:10:"tags_label";s:5:"Tags:";}', 'two_columns_right_block', 8),
(278, 207, 9, 'layout_dummy.block_page', '', 'layout_dummy.featured_products', '', 'three_columns_middle_block', 7),
(279, 208, 9, '', '', 'layout_dummy.two_columns', '', '', 0),
(280, 209, 9, 'layout_dummy.block_page', 'a:1:{s:6:"header";s:8:"Products";}', 'layout_dummy.products_list', '', 'two_columns_right_block', 9),
(283, 212, 9, 'layout_dummy.block_primary_block', 'a:1:{s:6:"header";s:8:"Calendar";}', 'component_news.calendar', 'a:1:{s:6:"expand";s:5:"false";}', 'columns_left_block', 1),
(287, 216, 9, '', '', 'layout_dummy.news_list', 'a:1:{s:10:"tags_label";s:4:"Tags";}', 'two_columns_right_block', 10),
(288, 217, 9, '', '', 'layout_dummy.three_columns', '', '', 0),
(289, 218, 9, '', '', 'layout_dummy.three_columns', '', '', 0),
(290, 219, 9, '', '', 'layout_dummy.categories_menu', '', 'columns_left_block', 2),
(291, 220, 9, '', '', 'layout_dummy.product_record', '', 'two_columns_right_block', 11),
(292, 221, 9, '', '', 'layout_dummy.products_list', '', 'two_columns_right_block', 12),
(299, 228, 9, '', '', 'layout_dummy.three_columns', '', '', 0),
(300, 229, 9, '', '', 'layout_dummy.authform_popup', '', 'log-in', 0),
(301, 230, 9, '', '', 'layout_dummy.searchline', 'a:1:{s:2:"go";s:3:"Go!";}', 'menu', 2),
(303, 232, 9, 'layout_dummy.block_titled', 'a:1:{s:6:"header";s:8:"Projects";}', 'layout_dummy.main_projects', '', 'right_block', 1),
(305, 234, 9, 'layout_dummy.block_titled_bottom_left', 'a:1:{s:6:"header";s:20:"Employee Of The Year";}', 'layout_dummy.main_person', '', 'bottom_left_block', 1),
(306, 235, 9, 'layout_dummy.block_info_block', 'a:1:{s:6:"header";s:6:"Awards";}', 'layout_dummy.right_awards', '', 'three_columns_right_block', 0),
(307, 236, 9, 'layout_dummy.block_success_block', 'a:1:{s:6:"header";s:9:"Vacancies";}', 'layout_dummy.right_vacancies', '', 'three_columns_right_block', 1),
(308, 237, 9, '', '', 'layout_dummy.left_menu', '', 'columns_left_block', 4),
(312, 241, 9, 'layout_dummy.block_titled_bottom', 'a:1:{s:6:"header";s:17:"Featured Products";}', 'layout_dummy.featured_products', '', 'bottom_wide_block', 0),
(329, 258, 9, '', '', 'layout_dummy.featured_products', '', 'two_columns_right_block', 15),
(331, 260, 9, 'layout_dummy.block_titled', 'a:1:{s:6:"header";s:4:"News";}', 'layout_dummy.main_news', '', 'center_block', 1),
(334, 262, 11, '', '', 'layout_demo.two_columns', 'a:2:{s:6:"header";s:7:"Catalog";s:23:"two_column_index_header";s:7:"Catalog";}', '', 3),
(350, 278, 11, '', '', 'layout_demo.authform_popup', '', 'icons_area', 0),
(356, 284, 11, '', '', 'layout_demo.top_menu', '', 'top_nav', 1),
(366, 294, 11, '', '', 'layout_demo.footer_menu', '', 'footer', 1),
(372, 300, 11, '', '', 'layout_demo.index', '', '', 1),
(375, 303, 11, '', '', 'layout_demo.left_menu', '', 'left_column', 1),
(379, 307, 11, '', '', 'layout_demo.three_columns', '', '', 2),
(381, 309, 11, '', '', 'layout_demo.news_list', 'a:1:{s:4:"more";s:5:"/News";}', 'left_column', 2),
(383, 311, 11, '', '', 'layout_demo.simple_img', '', 'right_column', 1),
(385, 313, 11, '', '', 'layout_demo.products_list_main', '', 'main_column', 1),
(387, 315, 11, '', '', 'layout_demo.left_menu', '', 'left_column', 3),
(388, 316, 11, '', '', 'component_news.list', 'a:2:{s:4:"more";s:0:"";s:10:"tags_label";s:12:"Posted under";}', 'main_column', 21),
(389, 317, 11, '', 'a:1:{s:6:"header";s:4:"News";}', 'layout_demo.featured_news_list', 'a:1:{s:4:"more";s:5:"/News";}', 'main_column', 20),
(391, 319, 11, '', '', 'layout_demo.news_list_main', '', 'main_column', 3),
(392, 320, 11, '', '', 'layout_demo.news_record', '', 'main_column', 4),
(395, 323, 11, '', '', 'layout_demo.index_slider', 'a:9:{s:13:"bg_photo_2584";s:34:"/floxim_files/content/slide2_4.png";s:11:"header_2584";s:9:"Mega prod";s:9:"text_2584";s:8:"Buyyyyyy";s:13:"bg_photo_2582";s:34:"/floxim_files/content/slide2_5.png";s:11:"header_2582";s:15:"Stet clita kasd";s:9:"text_2582";s:18:"At vero eos et<br>";s:13:"bg_photo_2627";s:33:"/floxim_files/content/slide_6.jpg";s:11:"header_2627";s:6:"asdasd";s:9:"text_2627";s:5:"dasda";}', 'main_column', 5),
(396, 324, 11, '', '', 'component_text.list', '', 'main_column', 8),
(397, 325, 11, '', '', 'layout_demo.person_list_main', '', 'main_column', 9),
(398, 326, 11, '', '', 'layout_demo.person_record', '', 'main_column', 10),
(399, 327, 11, '', '', 'layout_demo.vacancy_list', 'a:1:{s:9:"separator";s:5:" - ";}', 'main_column', 18),
(400, 328, 11, '', '', 'layout_demo.vacancy_record', 'a:3:{s:4:"from";s:6:"From ";s:2:"to";s:4:"To ";s:9:"phone_tpl";s:9:"Phone:  ";}', 'main_column', 11),
(402, 330, 11, '', '', 'layout_demo.left_menu', '', 'left_column', 5),
(403, 331, 11, '', '', 'layout_demo.project_list', '', 'main_column', 12),
(404, 332, 11, '', '', 'layout_demo.project_record', '', 'main_column', 13),
(405, 333, 11, '', '', 'layout_demo.award_list', 'a:1:{s:12:"is_wide_2599";s:1:"0";}', 'main_column', 14),
(406, 334, 11, '', '', 'layout_demo.award_record', '', 'main_column', 15),
(407, 335, 11, '', '', 'layout_demo.top_links', '', 'header_links', 1),
(408, 336, 11, '', '', 'layout_demo.news_list_main', '', 'main_column', 16),
(409, 337, 11, '', '', 'layout_demo.news_list_main', '', 'main_column', 17),
(410, 338, 11, '', '', 'layout_demo.social_icons', '', 'right_column', 2),
(411, 339, 11, '', '', 'layout_demo.full_screen_banner', 'a:3:{s:12:"banner_image";s:69:"/floxim_files/content/1280px-Sortie_de_l_op_ra_en_l_an_2000-2_1_1.jpg";s:13:"banner_header";s:15:"Lorem idssfpfdf";s:11:"banner_text";s:51:"Excepteur sint obcaecat<br>\ncupiditat non proident,";}', 'full_screen', 1),
(413, 341, 11, '', '', 'layout_demo.left_menu', '', 'index_left_column', 1),
(414, 342, 11, '', '', 'layout_demo.index_slider', 'a:6:{s:13:"bg_photo_2582";s:69:"/floxim_files/content/1280px-Sortie_de_l_op_ra_en_l_an_2000-2_1_3.jpg";s:11:"header_2582";s:21:"Lorem ipsum dolor sit";s:9:"text_2582";s:24:"Nam liber tempor cum<br>";s:13:"bg_photo_2584";s:33:"/floxim_files/content/slide_5.jpg";s:11:"header_2584";s:12:"sdfgsdfgsdfg";s:9:"text_2584";s:17:"sadfgsdfgsdfgsdfg";}', 'index_main_column', 1),
(415, 343, 11, '', '', 'layout_demo.featured_products_list', '', 'index_main_column', 2),
(416, 344, 11, '', '', 'layout_demo.featured_products_list_with_categories', '', 'main_column', 22),
(417, 345, 12, '', '', 'layout_v3.two_columns_inverted', 'a:5:{s:5:"phone";s:17:"8 (800) 123 12 42";s:4:"logo";s:0:"";s:17:"two_column_header";s:29:"\n                Yes, we can!";s:17:"one_column_header";s:25:"\n                Our news";s:9:"logo_name";s:10:"Photo Team";}', '', 1),
(418, 346, 12, '', '', 'layout_v3.main_menu', '', 'top_nav', 2),
(419, 347, 12, '', '', 'layout_v3.footer_menu', '', 'footer_menu', 1),
(420, 348, 12, '', '', 'layout_v3.social_icons', '', 'footer_social_icons', 1),
(421, 349, 12, '', '', 'layout_v3.authform_popup', 'a:1:{s:5:"login";s:7:"Log in!";}', 'icons_area', 1),
(431, 359, 12, '', '', 'layout_v3.one_column', '', '', 2),
(432, 360, 12, '', '', 'layout_v3.breadcrumbs', '', 'breadcrumbs-area', 1),
(433, 361, 12, '', '', 'layout_v3.featured_list', 'a:7:{s:5:"image";s:38:"/floxim_files/content/HansIsland_0.png";s:10:"image_2652";s:35:"/floxim_files/content/5_ski_4_0.JPG";s:10:"four_items";s:1:"1";s:8:"image_id";s:32:"/floxim_files/content/7394_0.jpg";s:10:"image_2654";s:31:"/floxim_files/content/5_3_0.jpg";s:10:"image_2658";s:38:"/floxim_files/content/5_nature_5_0.jpg";s:10:"image_2744";s:44:"/floxim_files/content/6_pascua_toro_10_0.jpg";}', 'main_column', 2),
(434, 362, 12, '', '', 'layout_v3.banner', 'a:3:{s:18:"banner_header_2652";s:27:"<p>\n	Special condtions\n</p>";s:16:"banner_text_2652";s:26:"<p>\n	For sport series\n</p>";s:17:"banner_image_2652";s:34:"/floxim_files/content/5_swim_0.jpg";}', 'main_column', 1),
(435, 363, 12, '', '', 'layout_v3.two_columns', '', '', 3),
(436, 364, 12, '', '', 'layout_v3.featured_list', 'a:1:{s:10:"four_items";s:1:"0";}', 'main_column', 3),
(438, 366, 12, '', '', 'layout_v3.two_columns', '', '', 4),
(439, 367, 12, '', '', 'layout_v3.product_record', '', 'main_column', 4),
(441, 369, 12, '', '', 'layout_v3.banner', 'a:5:{s:18:"banner_header_2660";s:28:"<p>\n	  Greate ship\n</p>\n<br>";s:16:"banner_text_2660";s:43:"<p>\n	It''s really <strong>cool</strong>\n</p>";s:7:"go_2660";s:2:"Go";s:17:"banner_image_2740";s:32:"/floxim_files/content/3b_6_0.jpg";s:16:"banner_text_2740";s:26:"<p>\n	Better than ever\n</p>";}', 'main_column', 5),
(442, 370, 12, '', 'a:1:{s:6:"header";s:10:"Best ships";}', 'layout_v3.featured_list', 'a:1:{s:10:"four_items";s:1:"0";}', 'main_column', 6),
(443, 371, 12, '', '', 'layout_v3.one_column', '', '', 5),
(444, 372, 12, '', '', 'layout_v3.person_list', 'a:4:{s:13:"facebook_2671";s:0:"";s:7:"vk_2671";s:0:"";s:7:"li_2671";s:0:"";s:12:"twitter_2671";s:0:"";}', 'main_column', 7),
(445, 373, 12, '', '', 'layout_v3.one_column', '', '', 6),
(446, 374, 12, '', '', 'layout_v3.vacancies_list', '', 'main_column', 8),
(448, 376, 12, '', '', 'layout_v3.vacancy_record', 'a:4:{s:21:"responsibilities_2677";s:23:"What you will be doing";s:17:"requirements_2677";s:16:"We need from you";s:21:"responsibilities_2737";s:16:"Your duties are:";s:17:"requirements_2737";s:22:"You should be able to:";}', 'main_column', 9),
(450, 378, 12, '', '', 'layout_v3.two_columns', '', '', 8),
(451, 379, 12, '', '', 'layout_v3.news_mixed', 'a:3:{s:9:"show_more";s:1:"0";s:12:"show_anounce";s:1:"0";s:14:"count_featured";s:1:"2";}', 'main_column', 12),
(453, 381, 12, '', '', 'layout_v3.featured_news_list', 'a:3:{s:13:"more_news_url";s:5:"/news";s:9:"show_more";s:1:"1";s:12:"show_anounce";s:1:"0";}', 'index_one_column', 1),
(454, 382, 12, '', '', 'layout_v3.full_width', '', '', 9),
(457, 385, 12, '', '', 'layout_v3.full_screen_menu', 'a:13:{s:7:"bg_2688";s:38:"/floxim_files/content/HansIsland_8.png";s:3:"bg_";s:0:"";s:7:"bg_2690";s:69:"/floxim_files/content/1280px-Sortie_de_l_op_ra_en_l_an_2000-2_1_0.jpg";s:11:"header_2688";s:0:"";s:12:"caption_2688";s:71:"<p>\n	 The carnival of Potosi\n</p>\n<p>\n	<strong>in Bolivia</strong>\n</p>";s:11:"header_2690";s:0:"";s:11:"header_2639";s:42:"<p>\n	 Our projects\n</p>\n<p>\n	are cool\n</p>";s:12:"caption_2639";s:11:"Ain''t they?";s:12:"caption_2690";s:27:"<p>\n	The age old sport\n</p>";s:7:"bg_2639";s:32:"/floxim_files/content/2a_2_0.JPG";s:12:"caption_2751";s:20:"<p>\n	Pagan fest\n</p>";s:12:"caption_2757";s:28:"<p>\n	a.k.a. Pascua Toro\n</p>";s:7:"bg_2761";s:0:"";}', 'full_screen', 1),
(458, 386, 12, 'layout_v3.block_titled', 'a:1:{s:6:"header";s:13:"About Project";}', 'layout_v3.project_record', '', 'full_screen', 2),
(459, 387, 12, '', '', 'layout_v3.full_screen_menu', 'a:4:{s:7:"header_";s:15:"Hans Island<br>";s:11:"header_2635";s:45:"<p>\n	 Team of professional photographers\n</p>";s:12:"caption_2635";s:77:"<p>\n	We come in all sizes and shapes ready to\n</p>\nshoot any series you like.";s:7:"bg_2635";s:44:"/floxim_files/content/6_pascua_toro_19_0.JPG";}', 'full_screen', 3),
(460, 388, 12, '', '', 'layout_v3.side_menu', 'a:1:{s:10:"unstylized";s:1:"0";}', 'left_column', 8),
(461, 389, 12, 'layout_v3.block_titled', 'a:1:{s:6:"header";s:6:"Images";}', 'layout_v3.slider', 'a:1:{s:10:"thumbnails";s:1:"1";}', 'full_screen', 4),
(462, 390, 12, '', '', 'layout_v3.one_column', '', '', 10),
(463, 391, 12, '', '', 'layout_v3.banner', 'a:3:{s:18:"banner_header_2656";s:19:"<p>\n	Need job?\n</p>";s:16:"banner_text_2656";s:31:"<p>\n	Look at our vacancies\n</p>";s:17:"banner_image_2656";s:40:"/floxim_files/content/5_open_air_2_0.jpg";}', 'main_column', 13),
(464, 392, 12, 'layout_v3.right_block_titled', 'a:1:{s:6:"header";s:12:"Latest news:";}', 'layout_v3.featured_news_list', 'a:3:{s:13:"more_news_url";s:5:"/News";s:9:"show_more";s:1:"1";s:12:"show_anounce";s:1:"0";}', 'main_column', 14),
(465, 393, 12, '', '', 'layout_v3.two_columns_inverted', '', '', 11),
(468, 396, 12, 'layout_v3.right_block_titled', 'a:1:{s:6:"header";s:11:"Lorem ipsum";}', 'layout_v3.contact_block', '', 'right_column', 3),
(469, 397, 12, '', '', 'layout_v3.addres_block', 'a:2:{s:9:"blue_2701";s:1:"1";s:9:"blue_2700";s:1:"0";}', 'main_column', 15),
(470, 398, 12, '', '', 'layout_v3.news_record', '', 'main_column', 16),
(471, 399, 12, '', '', 'layout_v3.index', '', '', 12),
(472, 400, 12, '', '', 'layout_v3.side_menu', 'a:1:{s:10:"unstylized";s:1:"0";}', 'left_column', 9),
(474, 402, 12, '', '', 'layout_v3.two_columns', '', '', 13),
(475, 403, 12, '', '', 'layout_v3.two_columns', '', '', 14),
(476, 404, 12, '', '', 'layout_v3.side_menu', '', 'left_column', 10),
(477, 405, 12, '', '', 'layout_v3.two_columns', '', '', 15);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_lang`
--

CREATE TABLE IF NOT EXISTS `fx_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `en_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `native_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `lang_code` varchar(5) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang_code` (`lang_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Дамп данных таблицы `fx_lang`
--

INSERT INTO `fx_lang` (`id`, `en_name`, `native_name`, `lang_code`) VALUES
(1, 'English', 'English', 'en'),
(9, 'Russian', 'Русский', 'ru');

-- --------------------------------------------------------

--
-- Структура таблицы `fx_lang_string`
--

CREATE TABLE IF NOT EXISTS `fx_lang_string` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dict` varchar(45) DEFAULT NULL,
  `string` text,
  `lang_en` text,
  `lang_ru` text,
  `lang_rus` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=760 ;

--
-- Дамп данных таблицы `fx_lang_string`
--

INSERT INTO `fx_lang_string` (`id`, `dict`, `string`, `lang_en`, `lang_ru`, `lang_rus`) VALUES
(1, 'component_section', 'Show path to the current page', 'Show path to the current page', 'Отображает путь до текущей страницы в структуре сайта', NULL),
(2, 'component_section', 'Bread crumbs', 'Bread crumbs', 'Хлебные крошки', NULL),
(3, 'component_section', 'Subsection', 'Subsection', 'Подраздел', NULL),
(4, 'component_section', 'Show for all items', 'Show for all items', 'Показывать у всех', NULL),
(5, 'component_section', 'Show for the active item', 'Show for the active item', 'Показывать у активного', NULL),
(6, 'component_section', 'Don''t show', 'Don''t show', 'Не показывать', NULL),
(7, 'component_section', 'Subsections', 'Subsections', 'Подразделы', NULL),
(8, 'component_section', 'Navigation', 'Navigation', 'Меню', NULL),
(9, 'system', 'File is not writable', 'File is not writable', 'Не могу произвести запись в файл', NULL),
(10, 'controller_component', 'Show entries by filter', 'Show entries by filter', 'Выводит записи по произвольному фильтру', NULL),
(11, 'controller_component', 'Show entries from the specified section', 'Show entries from the specified section', 'Выводит список записей из указанного раздела', NULL),
(12, 'controller_component', 'List', 'List', 'Список', NULL),
(13, 'controller_component', 'Show single entry', 'Show single entry', 'Выводит отдельную запись', NULL),
(14, 'controller_component', 'Entry', 'Entry', 'Запись', NULL),
(15, 'controller_component', 'From specified section', 'From specified section', 'Указать раздел явно', NULL),
(16, 'controller_component', 'From all sections', 'From all sections', 'Из любого раздела', NULL),
(17, 'controller_component', 'Choose section', 'Choose section', 'Выбрать родителя', NULL),
(18, 'controller_component', 'Random', 'Random', 'Произвольный', NULL),
(19, 'controller_component', 'The infoblock owner section', 'The infoblock owner section', 'Страница, куда прицеплен инфоблок', NULL),
(20, 'controller_component', 'Current page', 'Current page', 'Текущая страница', NULL),
(21, 'controller_component', 'Parent', 'Parent', 'Родитель', NULL),
(22, 'controller_component', 'Ascending', 'Ascending', 'По возрастанию', NULL),
(23, 'controller_component', 'Descending', 'Descending', 'По убыванию', NULL),
(24, 'controller_component', 'Order', 'Order', 'Порядок', NULL),
(25, 'controller_component', 'Sorting', 'Sorting', 'Сортировка', NULL),
(26, 'controller_component', 'Manual', 'Manual', 'Ручная', NULL),
(27, 'controller_component', 'Created', 'Created', 'Дата создания', NULL),
(28, 'controller_component', 'Show pagination?', 'Show pagination?', 'Разбивать на страницы?', NULL),
(29, 'controller_component', 'How many entries to display', 'How many entries to display', 'Сколько выводить', NULL),
(30, 'controller_layout', 'Sign in', 'Sign in', 'Вход', NULL),
(31, 'system', 'Add infoblock', 'Add infoblock', 'Добавить инфоблок', NULL),
(32, 'system', 'Link', 'Link', 'Ссылка', NULL),
(33, 'system', 'Picture', 'Picture', 'Картинка', NULL),
(34, 'system', 'Elements', 'Elements', 'Элементы', NULL),
(35, 'system', 'Classifier', 'Classifier', 'Классификатор', NULL),
(36, 'system', 'Manually', 'Manually', 'Вручную', NULL),
(37, 'system', 'Source', 'Source', 'Источник', NULL),
(38, 'system', 'Show like', 'Show like', 'Показывать как', NULL),
(39, 'system', 'Current file:', 'Current file:', 'Текущий файл:', NULL),
(40, 'system', 'replace newline to br', 'replace newline to br', 'заменять перенос строки на br', NULL),
(41, 'system', 'allow HTML tags', 'allow HTML tags', 'разрешить html-теги', NULL),
(42, 'system', 'Related type', 'Related type', 'Связанный тип', NULL),
(43, 'system', 'Bind value to the parent', 'Bind value to the parent', 'Привязать значение к родителю', NULL),
(44, 'system', 'Key name for the property', 'Key name for the property', 'Ключ для свойства', NULL),
(45, 'system', 'Links to', 'Links to', 'Куда ссылается', NULL),
(46, 'system', 'Enter the name of the site', 'Enter the name of the site', 'Укажите название сайта', NULL),
(47, 'system', 'Priority', 'Priority', 'Приоритет', NULL),
(48, 'system', 'Created', 'Created', 'Дата создания', NULL),
(49, 'system', 'This keyword is used by the component', 'This keyword is used by the component', 'Такой keyword уже используется компоненте', NULL),
(50, 'system', 'Keyword can only contain letters, numbers, symbols, "hyphen" and "underscore"', 'Keyword can only contain letters, numbers, symbols, "hyphen" and "underscore"', 'Keyword может содержать только буквы, цифры, символы "дефис" и "подчеркивание"', NULL),
(51, 'system', 'Specify component keyword', 'Specify component keyword', 'Укажите keyword компонента', NULL),
(52, 'system', 'Component name can not be empty', 'Component name can not be empty', 'Название компонента не может быть пустым', NULL),
(53, 'system', 'Specify field description', 'Specify field description', 'Укажите описание поля', NULL),
(54, 'system', 'This field already exists', 'This field already exists', 'Такое поле уже существует', NULL),
(55, 'system', 'This field is reserved', 'This field is reserved', 'Данное поле зарезервировано', NULL),
(56, 'system', 'Field name can contain only letters, numbers, and the underscore character', 'Field name can contain only letters, numbers, and the underscore character', 'Имя поля может содержать только латинские буквы, цифры и знак подчеркивания', NULL),
(57, 'system', 'name', 'name', 'name', NULL),
(58, 'system', 'Specify field name', 'Specify field name', 'Укажите название поля', NULL),
(59, 'system', 'This keyword is used by widget', 'This keyword is used by widget', 'Такой keyword уже используется в виджете', NULL),
(60, 'system', 'Keyword can contain only letters and numbers', 'Keyword can contain only letters and numbers', 'Keyword может сожержать только буквы и цифры', NULL),
(61, 'system', 'Enter the keyword of widget', 'Enter the keyword of widget', 'Укажите keyword виджета', NULL),
(62, 'system', 'Specify the name of the widget', 'Specify the name of the widget', 'Укажите название виджета', NULL),
(63, 'system', 'You are about to install:', 'You are about to install:', 'Вы собираетесь установить:', NULL),
(64, 'system', 'Preview', 'Preview', 'Превью', NULL),
(65, 'system', 'Layout', 'Layout', 'Макет', NULL),
(66, 'system', 'Show when the site is off', 'Show when the site is off', 'Показывать, когда сайт выключен', NULL),
(67, 'system', 'Cover Page', 'Cover Page', 'Титульная страница', NULL),
(68, 'system', 'Prevent indexing', 'Prevent indexing', 'Запретить индексирование', NULL),
(69, 'system', 'The contents of robots.txt', 'The contents of robots.txt', 'Содержимое robots.txt', NULL),
(70, 'system', 'Site language', 'Site language', 'Язык сайта', NULL),
(71, 'system', 'Aliases', 'Aliases', 'Зеркала', NULL),
(72, 'system', 'Domain', 'Domain', 'Домен', NULL),
(73, 'system', 'Site name', 'Site name', 'Название сайта', NULL),
(74, 'system', 'Enabled', 'Enabled', 'Включен', NULL),
(75, 'system', 'System', 'System', 'Системные', NULL),
(76, 'system', 'Main', 'Main', 'Основные', NULL),
(77, 'system', 'any', 'any', 'любое', NULL),
(78, 'system', 'vertical', 'vertical', 'вертикальное', NULL),
(79, 'system', 'Menu', 'Menu', 'Меню', NULL),
(80, 'system', 'Direction', 'Direction', 'Направление', NULL),
(81, 'system', 'Required', 'Required', 'Обязательный', NULL),
(82, 'system', 'Block', 'Block', 'Блок', NULL),
(83, 'system', 'Blocks', 'Blocks', 'Блоки', NULL),
(84, 'system', 'Sites', 'Sites', 'Сайты', NULL),
(85, 'system', 'Design', 'Design', 'Дизайн', NULL),
(86, 'system', 'Settings', 'Settings', 'Настройки', NULL),
(87, 'system', 'Site map', 'Site map', 'Карта сайта', NULL),
(88, 'system', 'Site not found', 'Site not found', 'Сайт не найден', NULL),
(89, 'system', 'Page not found', 'Page not found', 'Страница не найдена', NULL),
(90, 'system', 'Error creating a temporary file', 'Error creating a temporary file', 'Ошибка при создании временного файла', NULL),
(91, 'system', 'Create a new site', 'Create a new site', 'Добавление нового сайта', NULL),
(92, 'system', 'Add new site', 'Add new site', 'Новый сайт', NULL),
(93, 'system', 'New', 'New', 'Новый', NULL),
(94, 'system', 'Export', 'Export', 'Экспорт', NULL),
(95, 'system', 'for mobile devices', 'for mobile devices', 'для мобильный устройств', NULL),
(96, 'system', 'Language:', 'Language:', 'Язык:', NULL),
(97, 'system', 'Description', 'Description', 'Описание', NULL),
(98, 'system', 'Group', 'Group', 'Группа', NULL),
(99, 'system', 'Another group', 'Another group', 'Другая группа', NULL),
(100, 'system', 'Name of entity created by the component', 'Name of entity created by the component', 'Название сущности создаваемой компонентом', NULL),
(101, 'system', 'Component name', 'Component name', 'Название компонента', NULL),
(102, 'system', 'Keyword:', 'Keyword:', 'Ключевое слово:', NULL),
(103, 'system', '--no--', '--no--', '--нет--', NULL),
(104, 'system', 'Parent component', 'Parent component', 'Компонент-родитель', NULL),
(105, 'system', 'default', 'default', 'по умолчанию', NULL),
(106, 'system', 'Components', 'Components', 'Компоненты', NULL),
(107, 'system', 'Widgets', 'Widgets', 'Виджеты', NULL),
(108, 'system', 'Keyword', 'Keyword', 'Ключевое слово', NULL),
(109, 'system', 'File', 'File', 'Файл', NULL),
(110, 'system', 'Fields', 'Fields', 'Поля', NULL),
(111, 'system', 'Install from FloximStore', 'Install from FloximStore', 'установить с FloximStore', NULL),
(112, 'system', 'import', 'import', 'импортировать', NULL),
(113, 'system', 'Layout of inside page', 'Layout of inside page', 'Макет внутренней страницы', NULL),
(114, 'system', 'Cover Page Layout', 'Cover Page Layout', 'Макет титульной страницы', NULL),
(115, 'system', 'Sign out', 'Sign out', 'Выход', NULL),
(116, 'system', 'Apply the current', 'Apply the current', 'Применить текущий', NULL),
(117, 'system', 'Colors', 'Colors', 'Расцветка', NULL),
(118, 'system', 'Layout not found', 'Layout not found', 'Макет не найден', NULL),
(119, 'system', 'Enter the layout name', 'Enter the layout name', 'Укажите название макета', NULL),
(120, 'system', 'Layout name', 'Layout name', 'Название макета', NULL),
(121, 'system', 'Export to file', 'Export to file', 'Экспортировать в файл', NULL),
(122, 'system', 'No files', 'No files', 'Нет файлов', NULL),
(123, 'system', 'Layouts', 'Layouts', 'Макеты', NULL),
(124, 'system', 'Unable to create directory', 'Unable to create directory', 'Не удалось создать каталог', NULL),
(125, 'system', 'Adding a layout design', 'Adding a layout design', 'Добавление макета дизайна', NULL),
(126, 'system', 'Import layout design', 'Import layout design', 'Импорт макета дизайна', NULL),
(127, 'system', 'empty', 'empty', 'пустой', NULL),
(128, 'system', 'Used on', 'Used on', 'Используется на сайтах', NULL),
(129, 'system', 'Repeated', 'Repeated', 'Повторено', NULL),
(130, 'system', 'Cancelled', 'Cancelled', 'Отменено', NULL),
(131, 'system', 'Header sent', 'Header sent', 'Посылаемый заголовок', NULL),
(132, 'system', 'New url', 'New url', 'Новый url', NULL),
(133, 'system', 'Old url', 'Old url', 'Старый url', NULL),
(134, 'system', 'Changing the transfer rule', 'Changing the transfer rule', 'Изменение правила переадресации', NULL),
(135, 'system', 'Adding forwarding rules', 'Adding forwarding rules', 'Добавление правила переадресации', NULL),
(136, 'system', 'Header', 'Header', 'Заголовок', NULL),
(137, 'system', 'Layouts can not be deleted', 'Layouts can not be deleted', 'Удалять лейауты нельзя!', NULL),
(138, 'system', 'Unbind/Hide', 'Unbind/Hide', 'Отвязать/скрыть', NULL),
(139, 'system', 'Delete', 'Delete', 'Удалить', NULL),
(140, 'system', 'The infoblock contains some content', 'The infoblock contains some content', 'Инфоблок содержит контент', NULL),
(141, 'system', 'items. What should we do with them?', 'items. What should we do with them?', ' шт. Что с ним делать?', NULL),
(142, 'system', 'I am REALLY shure', 'I am REALLY shure', 'Будет удалено куча всего, я понимаю последствия', NULL),
(143, 'system', 'Block wrapper template', 'Block wrapper template', 'Оформление блока', NULL),
(144, 'system', 'Template', 'Template', 'Шаблон', NULL),
(145, 'system', 'Auto select', 'Auto select', 'Автовыбор', NULL),
(146, 'system', 'With no wrapper', 'With no wrapper', 'Без оформления', NULL),
(147, 'system', 'On the page and it''s children', 'On the page and it''s children', 'На этой и на вложенных', NULL),
(148, 'system', 'Only on children', 'Only on children', 'Только на вложенных страницах', NULL),
(149, 'system', 'Only on the page', 'Only on the page', 'Только на этой странице', NULL),
(150, 'system', 'Page', 'Page', 'Страница', NULL),
(151, 'system', 'On all pages', 'On all pages', 'На всех страницах', NULL),
(152, 'system', 'Remove this rule', 'Remove this rule', 'Удалить это правило', NULL),
(153, 'system', 'Create a new rule', 'Create a new rule', 'Создать новое правило', NULL),
(154, 'system', 'Update', 'Update', 'Обновить', NULL),
(155, 'system', 'Create', 'Create', 'Создать', NULL),
(156, 'system', 'Page layout', 'Page layout', 'Выбор шаблона страницы', NULL),
(157, 'system', 'Infoblock settings', 'Infoblock settings', 'Настройка инфоблока', NULL),
(158, 'system', 'Where to show', 'Where to show', 'Где показывать', NULL),
(159, 'system', 'How to show', 'How to show', 'Как показывать', NULL),
(160, 'system', 'Block name', 'Block name', 'Название блока', NULL),
(161, 'system', 'What to show', 'What to show', 'Что показывать', NULL),
(162, 'system', 'Widget', 'Widget', 'Виджет', NULL),
(163, 'system', 'Next', 'Next', 'Продолжить', NULL),
(164, 'system', 'Install from Store', 'Install from Store', 'Установить с Store', NULL),
(165, 'system', 'Adding infoblock', 'Adding infoblock', 'Добавление инфоблока', NULL),
(166, 'system', 'Type', 'Type', 'Тип', NULL),
(167, 'system', 'Action', 'Action', 'Действие', NULL),
(168, 'system', 'Name', 'Name', 'Название', NULL),
(169, 'system', 'Component', 'Component', 'Компонент', NULL),
(170, 'system', 'Single entry', 'Single entry', 'Отдельный объект', NULL),
(171, 'system', 'Mirror', 'Mirror', 'Mirror', NULL),
(172, 'system', 'List', 'List', 'Список', NULL),
(173, 'system', 'Change password', 'Change password', 'Сменить пароль', NULL),
(174, 'system', 'Import', 'Import', 'Импорт', NULL),
(175, 'system', 'Download from FloximStore', 'Download from FloximStore', 'Скачать с FloximStore', NULL),
(176, 'system', 'Download file', 'Download file', 'Cкачать файл', NULL),
(177, 'system', 'Upload file', 'Upload file', 'Закачать файл', NULL),
(178, 'system', 'Permissions', 'Permissions', 'Права', NULL),
(179, 'system', 'Select parent block', 'Select parent block', 'выделить блок', NULL),
(180, 'system', 'Site layout', 'Site layout', 'Сменить макет сайта', NULL),
(181, 'system', 'Page design', 'Page design', 'Дизайн страницы', NULL),
(182, 'system', 'Development', 'Development', 'Разработка', NULL),
(183, 'system', 'Administration', 'Administration', 'Администрирование', NULL),
(184, 'system', 'Tools', 'Tools', 'Инструменты', NULL),
(185, 'system', 'Users', 'Users', 'Пользователи', NULL),
(186, 'system', 'Site', 'Site', 'Сайт', NULL),
(187, 'system', 'Management', 'Management', 'Управление', NULL),
(188, 'system', 'Default value', 'Default value', 'Значение по умолчанию', NULL),
(189, 'system', 'Field can be used for searching', 'Field can be used for searching', 'Возможен поиск по полю', NULL),
(190, 'system', 'Required', 'Required', 'Обязательно для заполнения', NULL),
(191, 'system', 'Field not found', 'Field not found', 'Поле не найдено', NULL),
(192, 'system', 'Field is available for', 'Field is available for', 'Поле доступно', NULL),
(193, 'system', 'anybody', 'anybody', 'всем', NULL),
(194, 'system', 'admins only', 'admins only', 'только админам', NULL),
(195, 'system', 'nobody', 'nobody', 'никому', NULL),
(196, 'system', 'Field type', 'Field type', 'Тип поля', NULL),
(197, 'system', 'Field keyword', 'Field keyword', 'Название на латинице', NULL),
(198, 'system', 'Name', 'Name', 'Имя', NULL),
(199, 'system', 'New widget', 'New widget', 'Новый виджет', NULL),
(200, 'system', 'Widget size', 'Widget size', 'Размер виджета', NULL),
(201, 'system', 'Mini Block', 'Mini Block', 'Миниблок', NULL),
(202, 'system', 'Narrow', 'Narrow', 'Узкий', NULL),
(203, 'system', 'Wide', 'Wide', 'Широкий', NULL),
(204, 'system', 'Narrowly wide', 'Narrowly wide', 'Узко-широкий', NULL),
(205, 'system', 'Icon to be used', 'Icon to be used', 'Используемая иконка', NULL),
(206, 'system', 'This icon is used by default', 'This icon is used by default', 'эта иконка используется по умолчанию', NULL),
(207, 'system', 'This icon is icon.png file in the directory widget', 'This icon is icon.png file in the directory widget', 'эта иконка находится в файле icon.png в директории виджета', NULL),
(208, 'system', 'This icon is selected from a list of icons', 'This icon is selected from a list of icons', 'эта иконка выбрана из списка иконок', NULL),
(209, 'system', 'Enter the widget name', 'Enter the widget name', 'Введите название виджета', NULL),
(210, 'system', 'Specify the name', 'Specify the name', 'Укажите название', NULL),
(211, 'system', 'Edit User Group', 'Edit User Group', 'Изменение группы пользователей', NULL),
(212, 'system', 'Add User Group', 'Add User Group', 'Добавление группы пользователей', NULL),
(213, 'system', 'New Group', 'New Group', 'Новая группа', NULL),
(214, 'system', 'Assign the right director', 'Assign the right director', 'Присвоить право директора', NULL),
(215, 'system', 'The first version has just the right director', 'The first version has just the right director', 'В первой версии есть только право Директор', NULL),
(216, 'system', 'There are no rules', 'There are no rules', 'Нет никак прав', NULL),
(217, 'system', 'Permission', 'Permission', 'Право', NULL),
(218, 'system', 'Content edit', 'Content edit', 'Редактирование контента', NULL),
(219, 'system', 'Avatar', 'Avatar', 'Аватар', NULL),
(220, 'system', 'Nick', 'Nick', 'Имя на сайте', NULL),
(221, 'system', 'Confirm password', 'Confirm password', 'Пароль еще раз', NULL),
(222, 'system', 'Password', 'Password', 'Пароль', NULL),
(223, 'system', 'Login', 'Login', 'Логин', NULL),
(224, 'system', 'Groups', 'Groups', 'Группы', NULL),
(225, 'system', 'Passwords do not match', 'Passwords do not match', 'Пароли не совпадают', NULL),
(226, 'system', 'Password can''t be empty', 'Password can''t be empty', 'Пароль не может быть пустым', NULL),
(227, 'system', 'Fill in with the login', 'Fill in with the login', 'Заполните поле с логином', NULL),
(228, 'system', 'Please select at least one group', 'Please select at least one group', 'Выберите хотя бы одну группу', NULL),
(229, 'system', 'Add User', 'Add User', 'Добавление пользователя', NULL),
(230, 'system', 'publications in', 'publications in', 'публикации в', NULL),
(231, 'system', 'Select objects', 'Select objects', 'Выберите объекты', NULL),
(232, 'system', 'publish:', 'publish:', 'опубликовал:', NULL),
(234, 'system', 'friends, send message', 'friends, send message', 'друзья, отправить сообщение', NULL),
(235, 'system', 'registred:', 'registred:', 'зарегистрирован:', NULL),
(236, 'system', 'Activity', 'Activity', 'Активность', NULL),
(237, 'system', 'Registration data', 'Registration data', 'Регистрационные данные', NULL),
(238, 'system', 'Rights management', 'Rights management', 'Управление правами', NULL),
(239, 'system', 'Password and verification do not match.', 'Password and verification do not match.', 'Пароль и подтверждение не совпадают.', NULL),
(240, 'system', 'Password is too short. The minimum length of the password', 'Password is too short. The minimum length of the password', 'Пароль слишком короткий. Минимальная длина пароля', NULL),
(241, 'system', 'Enter the password', 'Enter the password', 'Введите пароль.', NULL),
(242, 'system', 'This login is already in use', 'This login is already in use', 'Такой логин уже используется', NULL),
(243, 'system', 'Error: can not find information block with users.', 'Error: can not find information block with users.', 'Ошибка: не найден инфоблок с пользователями.', NULL),
(244, 'system', 'Self-registration is prohibited.', 'Self-registration is prohibited.', 'Самостоятельная регистрация запрещена.', NULL),
(245, 'system', 'Can not find <? ​​Php class file', 'Can not find <? ​​Php class file', 'Не могу найти <?php в файле класса', NULL),
(246, 'system', 'Syntax error in the component class', 'Syntax error in the component class', 'Синтаксическая ошибка в классе компонента', NULL),
(247, 'system', 'Syntax error in function', 'Syntax error in function', 'Синтаксическая ошибка в функции', NULL),
(248, 'system', 'Profile', 'Profile', 'Профиль', NULL),
(249, 'system', 'User not found', 'User not found', 'Пользователь не найден', NULL),
(250, 'system', 'List not found', 'List not found', 'Список не найден', NULL),
(251, 'system', 'Site not found', 'Site not found', 'Сайт не найден', NULL),
(252, 'system', 'Widget not found', 'Widget not found', 'Виджет не найден', NULL),
(253, 'system', 'Component not found', 'Component not found', 'Компонент не найден', NULL),
(254, 'system', 'Modules', 'Modules', 'Модули', NULL),
(255, 'system', 'All sites', 'All sites', 'Список сайтов', NULL),
(256, 'system', 'Unable to connect to server', 'Unable to connect to server', 'Не удалось соединиться с сервером', NULL),
(257, 'system', 'Override the settings in the class', 'Override the settings in the class', 'Переопределите метод settings в своем классе', NULL),
(258, 'system', 'Configuring the', 'Configuring the', 'Настройка модуля', NULL),
(259, 'system', 'Login', 'Login', 'Вход', NULL),
(260, 'system', 'Saved', 'Saved', 'Сохранено', NULL),
(261, 'system', 'Could not open file!', 'Could not open file!', 'Не получилось открыть файл!', NULL),
(262, 'system', 'Error when downloading a file', 'Error when downloading a file', 'Ошибка при закачке файла', NULL),
(263, 'system', 'Enter the file', 'Enter the file', 'Укажите файл', NULL),
(264, 'system', 'Not all fields are transferred!', 'Not all fields are transferred!', 'Не все поля переданы!', NULL),
(265, 'system', 'Error Deleting File', 'Error Deleting File', 'Ошибка при удалении файла', NULL),
(266, 'system', 'Error when changing the name', 'Error when changing the name', 'Ошибка при изменении имени', NULL),
(267, 'system', 'Error when permission', 'Error when permission', 'Ошибка при изменении прав доступа', NULL),
(268, 'system', 'Set permissions', 'Set permissions', 'Задайте права доступа', NULL),
(269, 'system', 'Enter the name', 'Enter the name', 'Укажите имя', NULL),
(270, 'system', 'Edit the file/directory', 'Edit the file/directory', 'Правка файла/директории', NULL),
(271, 'system', 'View the contents', 'View the contents', 'Просмотр содержимого', NULL),
(272, 'system', 'Execution', 'Execution', 'Выполнение', NULL),
(273, 'system', 'Writing', 'Writing', 'Запись', NULL),
(274, 'system', 'Reading', 'Reading', 'Чтение', NULL),
(275, 'system', 'Permissions for the rest', 'Permissions for the rest', 'Права для остальных', NULL),
(276, 'system', 'Permissions for the group owner', 'Permissions for the group owner', 'Права для группы-владельца', NULL),
(277, 'system', 'Permissions for the user owner', 'Permissions for the user owner', 'Права для пользователя-владельца', NULL),
(278, 'system', 'Do not pass the file name!', 'Do not pass the file name!', 'Не передано имя файла!', NULL),
(279, 'system', 'An error occurred while creating the file/directory', 'An error occurred while creating the file/directory', 'Ошибка при создании файла/каталога', NULL),
(280, 'system', 'Not all fields are transferred', 'Not all fields are transferred', 'Не все поля переданы', NULL),
(281, 'system', 'Enter the name of the file/directory', 'Enter the name of the file/directory', 'Укажите имя файла/каталога', NULL),
(282, 'system', 'Create a new file/directory', 'Create a new file/directory', 'Создание нового файла/директории', NULL),
(283, 'system', 'Name of file/directory', 'Name of file/directory', 'Имя файла/каталога', NULL),
(284, 'system', 'What we create', 'What we create', 'Что создаём', NULL),
(285, 'system', 'directory', 'directory', 'каталог', NULL),
(286, 'system', 'Writing to file failed', 'Writing to file failed', 'Не удалась запись в файл', NULL),
(287, 'system', 'Reading of file failed', 'Reading of file failed', 'Не удалось прочитать файл!', NULL),
(288, 'system', 'Gb', 'Gb', 'Гб', NULL),
(289, 'system', 'Mb', 'Mb', 'Мб', NULL),
(290, 'system', 'Kb', 'Kb', 'Кб', NULL),
(291, 'system', 'byte', 'byte', 'байт', NULL),
(292, 'system', 'Parent directory', 'Parent directory', 'родительский каталог', NULL),
(293, 'system', 'Size', 'Size', 'Размер', NULL),
(294, 'system', 'File-manager', 'File-manager', 'Файл-менеджер', NULL),
(295, 'system', 'Invalid action', 'Invalid action', 'Неверное действие', NULL),
(296, 'system', 'Invalid user id', 'Invalid user id', 'Неверный id пользователя', NULL),
(297, 'system', 'Invalid code', 'Invalid code', 'Неверный код', NULL),
(298, 'system', 'Your account has been created.', 'Your account has been created.', 'Ваш аккаунт активирован.', NULL),
(299, 'system', 'Your e-mail address is confirmed. Wait for the verification and account activation by the administrator.', 'Your e-mail address is confirmed. Wait for the verification and account activation by the administrator.', 'Ваш адрес e-mail подтвержден. Дождитесь проверки и активации учетной записи администратором.', NULL),
(300, 'system', 'Invalid confirmation code registration.', 'Invalid confirmation code registration.', 'Неверный код подтверждения регистрации.', NULL),
(301, 'system', 'Not passed the verification code registration.', 'Not passed the verification code registration.', 'Не передан код подтверждения регистрации.', NULL),
(302, 'system', 'Action after the first authorization', 'Action after the first authorization', 'Действие после первой авторизации', NULL),
(303, 'system', 'Group, which gets the user after login', 'Group, which gets the user after login', 'Группы, куда попадет пользователь после авторизации', NULL),
(304, 'system', 'Facebook data', 'Facebook data', 'Данные facebook', NULL),
(305, 'system', 'User fields', 'User fields', 'Поля пользователя', NULL),
(306, 'system', 'Complies fields', 'Complies fields', 'Соответсвие полей', NULL),
(307, 'system', 'enable authentication with facebook', 'enable authentication with facebook', 'включить авторизацию через facebook', NULL),
(308, 'system', 'Twitter data', 'Twitter data', 'Данные twiiter', NULL),
(309, 'system', 'enable authentication with twitter', 'enable authentication with twitter', 'включить авторизацию через твиттер', NULL),
(310, 'system', 'The minimum length of the password must be an integer.', 'The minimum length of the password must be an integer.', 'Минимальная длина пароля должна быть целым числом.', NULL),
(311, 'system', 'The time during which the user is online, can be an integer greater than 0.', 'The time during which the user is online, can be an integer greater than 0.', 'Время, в течение которого пользователь считается online, должно быть целым числом больше 0.', NULL),
(312, 'system', 'nvalid address format of e-mail.', 'nvalid address format of e-mail.', 'Неверный формат адреса e-mail.', NULL),
(313, 'system', 'You have not selected any of the member.', 'You have not selected any of the member.', 'Вы не выбрали ни одной группы для зарегистрированных пользователей.', NULL),
(314, 'system', 'HTML-letter', 'HTML-letter', 'HTML-письмо', NULL),
(315, 'system', 'Letter body', 'Letter body', 'Тело письма', NULL),
(316, 'system', 'Letter header', 'Letter header', 'Заголовок письма', NULL),
(317, 'system', 'Restore the default form', 'Restore the default form', 'Восстановить форму по умолчанию', NULL),
(318, 'system', 'Component "Private Messages"', 'Component "Private Messages"', 'Компонент "Личные сообщения"', NULL),
(319, 'system', 'Component "Users"', 'Component "Users"', 'Компонент "Пользователи"', NULL),
(320, 'system', 'Allow users to add enemies', 'Allow users to add enemies', 'Разрешить добавлять пользователей во враги', NULL),
(321, 'system', 'Friends and enemies', 'Friends and enemies', 'Друзья и враги', NULL),
(322, 'system', 'Allow users to add as friend', 'Allow users to add as friend', 'Разрешить добавлять пользователей в друзья', NULL),
(323, 'system', 'Notify the user by e-mail about the new message', 'Notify the user by e-mail about the new message', 'Оповещать пользователя по e-mail о новом сообщении', NULL),
(324, 'system', 'Private messages', 'Private messages', 'Личные сообщения', NULL),
(325, 'system', 'Allow to send private messages', 'Allow to send private messages', 'Разрешить отправлять личные сообщения', NULL),
(326, 'system', 'User authentication after the confirm', 'User authentication after the confirm', 'Авторизация пользователя сразу после подтверждения', NULL),
(327, 'system', 'E-mail the administrator to send alerts', 'E-mail the administrator to send alerts', 'E-mail администратора для отсылки оповещений', NULL),
(328, 'system', 'Send a letter to the manager when a user logs', 'Send a letter to the manager when a user logs', 'Отправлять письмо администратору при регистрации пользователя', NULL),
(329, 'system', 'Moderated by the administrator', 'Moderated by the administrator', 'Премодерация администратором', NULL),
(330, 'system', 'Require confirmation by e-mail', 'Require confirmation by e-mail', 'Требовать подтверждение через e-mail', NULL),
(331, 'system', 'Group to which the user will get after registration', 'Group to which the user will get after registration', 'Группы, куда попадёт пользователь после регистрации', NULL),
(332, 'system', 'Enable self-registration', 'Enable self-registration', 'Разрешить самостоятельную регистрацию', NULL),
(333, 'system', 'Registration', 'Registration', 'Регистрация', NULL),
(334, 'system', 'Bind users to sites', 'Bind users to sites', 'Привязывать пользователей к сайтам', NULL),
(335, 'system', 'Deny yourself to recover your password', 'Deny yourself to recover your password', 'Запретить самостоятельно восстанавливать пароль', NULL),
(336, 'system', 'General Settings', 'General Settings', 'Общие настройки', NULL),
(337, 'system', 'Do not show the form of a failed login attempt', 'Do not show the form of a failed login attempt', 'Не показывать форму при неудачной попытке авторизации', NULL),
(338, 'system', 'Restored', 'Restored', 'Восстановлено', NULL),
(339, 'system', 'Nonexistent tab!', 'Nonexistent tab!', 'Несуществующая вкладка!', NULL),
(340, 'system', 'Login through external services', 'Login through external services', 'Авторизация через внешние сервисы', NULL),
(341, 'system', 'Email templates', 'Email templates', 'Шаблоны писем', NULL),
(342, 'system', 'General', 'General', 'Общие', NULL),
(343, 'system', 'Password restore', 'Password restore', 'Восстановление пароля', NULL),
(344, 'system', 'Registration confirm', 'Registration confirm', 'Подтверждение регистрации', NULL),
(345, 'system', 'Now you will be taken to the login page.', 'Now you will be taken to the login page.', 'Сейчас вы будете переброшены на страницу авторизации.', NULL),
(346, 'system', 'Click here if you do not want to wait.', 'Click here if you do not want to wait.', 'Нажмите, если не хотите ждать.', NULL),
(347, 'system', 'Login via twitter disabled', 'Login via twitter disabled', 'Авторизация через twitter запрещена', NULL),
(348, 'system', 'Login via facebook disabled', 'Login via facebook disabled', 'Авторизация через facebook запрещена', NULL),
(349, 'system', 'FX_ADMIN_FIELD_STRING', 'String', 'Строка', NULL),
(350, 'system', 'FX_ADMIN_FIELD_INT', 'Integer', 'Целое число', NULL),
(352, 'system', 'FX_ADMIN_FIELD_SELECT', 'Options list', 'Список', NULL),
(353, 'system', 'FX_ADMIN_FIELD_BOOL', 'Boolean', 'Логическая переменная', NULL),
(354, 'system', 'FX_ADMIN_FIELD_FILE', 'File', 'Файл', NULL),
(355, 'system', 'FX_ADMIN_FIELD_FLOAT', 'Float number', 'Дробное число', NULL),
(356, 'system', 'FX_ADMIN_FIELD_DATETIME', 'Date and time', 'Дата и время', NULL),
(357, 'system', 'FX_ADMIN_FIELD_COLOR', 'Color', 'Цвет', NULL),
(359, 'system', 'FX_ADMIN_FIELD_IMAGE', 'Image', 'Изображение', NULL),
(360, 'system', 'FX_ADMIN_FIELD_MULTISELECT', 'Multiple select', 'Мультисписок', NULL),
(361, 'system', 'FX_ADMIN_FIELD_LINK', 'Link to another object', 'Связь с другим объектом', NULL),
(362, 'system', 'FX_ADMIN_FIELD_MULTILINK', 'Multiple link', 'Множественная связь', NULL),
(363, 'system', 'FX_ADMIN_FIELD_TEXT', 'Text', 'Текст', NULL),
(375, 'system', 'add', 'add', 'add', NULL),
(376, 'system', 'edit', 'edit', 'edit', NULL),
(377, 'system', 'on', 'on', 'on', NULL),
(378, 'system', 'off', 'off', 'off', NULL),
(379, 'system', 'settings', 'settings', 'settings', NULL),
(380, 'system', 'delete', 'delete', 'delete', NULL),
(381, 'system', 'Render type', 'Render type', 'Render type', NULL),
(382, 'system', 'Live search', 'Live search', 'Live search', NULL),
(383, 'system', 'Simple select', 'Simple select', 'Simple select', NULL),
(384, 'system', '-Any-', '-Any-', 'Любой', NULL),
(385, 'system', 'Only on pages of type', 'Only on pages of type', 'Только на страницах типа', NULL),
(386, 'system', '-- choose something --', '-- choose something --', '-- выберите вариант --', NULL),
(387, 'component_section', 'Show only header?', 'Show only header?', 'Показывать только заголовок?', NULL),
(388, 'component_section', 'Hide on the index page', 'Hide on the index page', 'Скрыть на главной?', NULL),
(389, 'system', 'Welcome to Floxim.CMS, please sign in', 'Welcome to Floxim.CMS, please sign in', 'Welcome to Floxim.CMS, please sign in', NULL),
(390, 'system', 'Editing ', 'Editing ', 'Editing ', NULL),
(391, 'system', 'Fields table', 'Fields table', 'Fields table', NULL),
(392, 'system', 'Adding new ', 'Adding new ', 'Adding new ', NULL),
(393, 'controller_component', 'New infoblock', 'New infoblock', 'Новый инфоблок', NULL),
(394, 'controller_component', 'Infoblock for the field', 'Infoblock for the field', 'Инфоблок для поля ', NULL),
(396, 'system', 'Name of an entity created by the component', 'Name of an entity created by the component', 'Название сущности создаваемой компонентом (по-русски)', NULL),
(397, 'system', 'Component actions', 'Component actions', 'Component actions', NULL),
(398, 'system', 'Templates', 'Templates', 'Templates', NULL),
(399, 'system', 'Source', 'Source', 'Source', NULL),
(400, 'system', 'Action', 'Action', 'Action', NULL),
(401, 'system', 'File', 'File', 'File', NULL),
(402, 'system', 'Save', 'Save', 'Сохранить', NULL),
(403, 'system', 'Used', 'Used', 'Used', NULL),
(404, 'component_section', 'Nesting level', 'Nesting level', 'Уровень вложенности', NULL),
(405, 'component_section', '2 levels', '2 levels', '2 уровня', NULL),
(406, 'component_section', '3 levels', '3 levels', '3 уровня', NULL),
(407, 'component_section', 'Current level +1', 'Current level +1', 'Текущий +1', NULL),
(408, 'component_section', 'No limit', 'No limit', 'Без ограничения', NULL),
(409, 'system', 'Cancel', 'Cancel', 'Отменить', NULL),
(410, 'system', 'Redo', 'Redo', 'Вернуть', NULL),
(411, 'system', 'More', 'More', 'Еще', NULL),
(412, 'system', 'Patches', NULL, NULL, NULL),
(413, 'system', 'Update check failed', NULL, NULL, NULL),
(414, 'system', 'Installing patch %s...', NULL, NULL, NULL),
(415, 'content', 'Current Floxim version:', NULL, NULL, NULL),
(416, 'system', 'Current Floxim version:', NULL, NULL, NULL),
(433, 'system', 'Название компонента (по-русски)', 'Название компонента (по-русски)', NULL, NULL),
(434, 'system', 'Welcome to Floxim.CMS, please sign in', 'Welcome to Floxim.CMS, please sign in', NULL, NULL),
(435, 'system', 'Login', 'Login', NULL, NULL),
(436, 'system', 'Password', 'Password', NULL, NULL),
(437, 'system', 'Login', 'Login', NULL, NULL),
(438, 'system', 'Add', 'Add', NULL, NULL),
(439, 'system', 'Add new component', 'Add new component', NULL, NULL),
(440, 'system', 'Add new Components', 'Add new Components', NULL, NULL),
(441, 'system', 'Add new widget', 'Add new widget', NULL, NULL),
(442, 'system', 'Add new field', 'Add new field', NULL, NULL),
(443, 'system', 'Keyword (название папки с макетом)', 'Keyword (название папки с макетом)', NULL, NULL),
(444, 'system', 'Layout keyword', 'Layout keyword', NULL, NULL),
(445, 'system', 'Add new layout', 'Add new layout', NULL, NULL),
(446, 'system', 'Finish', 'Finish', NULL, NULL),
(447, 'system', 'Keyword can only contain letters, numbers, symbols, \\"hyphen\\" and \\"underscore\\"', 'Keyword can only contain letters, numbers, symbols, \\"hyphen\\" and \\"underscore\\"', NULL, NULL),
(448, 'system', 'Welcome to Floxim.CMS, please sign in', 'Welcome to Floxim.CMS, please sign in', NULL, NULL),
(449, 'system', 'Login', 'Login', NULL, NULL),
(450, 'system', 'Password', 'Password', NULL, NULL),
(451, 'system', 'Login', 'Login', NULL, NULL),
(452, 'controller_component', 'Limit', 'Limit', NULL, NULL),
(453, 'controller_component', 'Conditoins', 'Conditoins', NULL, NULL),
(454, 'controller_component', 'Conditions', 'Conditions', NULL, NULL),
(455, 'controller_component', 'Infoblock page', 'Infoblock page', NULL, NULL),
(456, 'system', 'I am REALLY sure', 'I am REALLY sure', NULL, NULL),
(457, 'component_section', 'Source infoblock', 'Source infoblock', NULL, NULL),
(458, 'component_section', 'Source infoblock', 'Source infoblock', NULL, NULL),
(459, 'system', 'Email', 'Email', NULL, NULL),
(460, 'system', 'Edit User', 'Edit User', NULL, NULL),
(461, 'system', 'Edit', 'Edit', NULL, NULL),
(462, 'system', 'Admin', 'Admin', NULL, NULL),
(463, 'system', 'Fill in email', 'Fill in email', NULL, NULL),
(464, 'system', 'Add new user', 'Add new user', NULL, NULL),
(465, 'system', 'Fill in correct email', 'Fill in correct email', NULL, NULL),
(466, 'system', 'Fill in name', 'Fill in name', NULL, NULL),
(467, 'system', 'Ununique email', 'Ununique email', NULL, NULL),
(468, 'system', 'Edit user', 'Edit user', NULL, NULL),
(469, 'system', 'Add user', 'Add user', NULL, NULL),
(470, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(471, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(472, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(473, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(474, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(475, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(476, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(477, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(478, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(479, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(480, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(481, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(482, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(483, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(484, 'system', 'Block wrapper', 'Block wrapper', NULL, NULL),
(485, 'system', 'Template2', 'Template2', NULL, NULL),
(486, 'system', 'NOW() by default', 'NOW() by default', NULL, NULL),
(487, 'system', 'Languages', 'Languages', NULL, NULL),
(488, 'system', 'Add new language', 'Add new language', NULL, NULL),
(492, 'system', 'Language name', 'Language name', NULL, NULL),
(493, 'system', 'Enter english language name', 'Enter english language name', NULL, NULL),
(494, 'system', 'Native language name', 'Native language name', NULL, NULL),
(495, 'system', 'Enter native language name', 'Enter native language name', NULL, NULL),
(496, 'system', 'Language code', 'Language code', NULL, NULL),
(497, 'system', 'Enter language code', 'Enter language code', NULL, NULL),
(498, 'system', 'Create a new language', 'Create a new language', NULL, NULL),
(499, 'system', 'Naitive name', 'Naitive name', NULL, NULL),
(500, 'component_section', 'Add subsection to', 'Add subsection to', NULL, NULL),
(501, 'system', 'Language', 'Language', NULL, NULL),
(502, 'system', 'Language', 'Language', NULL, NULL),
(503, 'system', 'Inherited from', 'Inherited from', NULL, NULL),
(504, 'system', 'Editable', 'Editable', NULL, NULL),
(505, 'system', 'No', 'No', NULL, NULL),
(506, 'system', 'Yes', 'Yes', NULL, NULL),
(507, 'system', 'Inherited', 'Inherited', NULL, NULL),
(508, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(509, 'controller_component', 'Show pagination?', 'Show pagination?', NULL, NULL),
(510, 'controller_component', 'Sorting', 'Sorting', NULL, NULL),
(511, 'controller_component', 'Order', 'Order', NULL, NULL),
(512, 'controller_component', 'Ascending', 'Ascending', NULL, NULL),
(513, 'controller_component', 'Descending', 'Descending', NULL, NULL),
(514, 'controller_component', 'Parent', 'Parent', NULL, NULL),
(515, 'controller_component', 'Current page', 'Current page', NULL, NULL),
(516, 'controller_component', 'Infoblock page', 'Infoblock page', NULL, NULL),
(517, 'controller_component', 'Conditions', 'Conditions', NULL, NULL),
(518, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(519, 'component_section', 'Subsections', 'Subsections', NULL, NULL),
(520, 'component_section', 'Don''t show', 'Don''t show', NULL, NULL),
(521, 'component_section', 'Show for the active item', 'Show for the active item', NULL, NULL),
(522, 'component_section', 'Show for all items', 'Show for all items', NULL, NULL),
(523, 'component_section', 'Source infoblock', 'Source infoblock', NULL, NULL),
(524, 'component_section', 'Show only header?', 'Show only header?', NULL, NULL),
(525, 'component_section', 'Hide on the index page', 'Hide on the index page', NULL, NULL),
(526, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(527, 'controller_component', 'Show pagination?', 'Show pagination?', NULL, NULL),
(528, 'controller_component', 'Sorting', 'Sorting', NULL, NULL),
(529, 'controller_component', 'Order', 'Order', NULL, NULL),
(530, 'controller_component', 'Ascending', 'Ascending', NULL, NULL),
(531, 'controller_component', 'Descending', 'Descending', NULL, NULL),
(532, 'controller_component', 'Parent', 'Parent', NULL, NULL),
(533, 'controller_component', 'Current page', 'Current page', NULL, NULL),
(534, 'controller_component', 'Infoblock page', 'Infoblock page', NULL, NULL),
(535, 'controller_component', 'Conditions', 'Conditions', NULL, NULL),
(536, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(537, 'component_section', 'Subsections', 'Subsections', NULL, NULL),
(538, 'component_section', 'Don''t show', 'Don''t show', NULL, NULL),
(539, 'component_section', 'Show for the active item', 'Show for the active item', NULL, NULL),
(540, 'component_section', 'Show for all items', 'Show for all items', NULL, NULL),
(541, 'component_section', 'Source infoblock', 'Source infoblock', NULL, NULL),
(542, 'component_section', 'Show only header?', 'Show only header?', NULL, NULL),
(543, 'component_section', 'Hide on the index page', 'Hide on the index page', NULL, NULL),
(544, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(545, 'controller_component', 'Show pagination?', 'Show pagination?', NULL, NULL),
(546, 'controller_component', 'Sorting', 'Sorting', NULL, NULL),
(547, 'controller_component', 'Order', 'Order', NULL, NULL),
(548, 'controller_component', 'Ascending', 'Ascending', NULL, NULL),
(549, 'controller_component', 'Descending', 'Descending', NULL, NULL),
(550, 'controller_component', 'Parent', 'Parent', NULL, NULL),
(551, 'controller_component', 'Current page', 'Current page', NULL, NULL),
(552, 'controller_component', 'Infoblock page', 'Infoblock page', NULL, NULL),
(553, 'controller_component', 'Conditions', 'Conditions', NULL, NULL),
(554, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(555, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(556, 'controller_component', 'Show pagination?', 'Show pagination?', NULL, NULL),
(557, 'controller_component', 'Sorting', 'Sorting', NULL, NULL),
(558, 'controller_component', 'Order', 'Order', NULL, NULL),
(559, 'controller_component', 'Ascending', 'Ascending', NULL, NULL),
(560, 'controller_component', 'Descending', 'Descending', NULL, NULL),
(561, 'controller_component', 'Parent', 'Parent', NULL, NULL),
(562, 'controller_component', 'Current page', 'Current page', NULL, NULL),
(563, 'controller_component', 'Infoblock page', 'Infoblock page', NULL, NULL),
(564, 'controller_component', 'Conditions', 'Conditions', NULL, NULL);
INSERT INTO `fx_lang_string` (`id`, `dict`, `string`, `lang_en`, `lang_ru`, `lang_rus`) VALUES
(565, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(566, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(567, 'controller_component', 'Show pagination?', 'Show pagination?', NULL, NULL),
(568, 'controller_component', 'Sorting', 'Sorting', NULL, NULL),
(569, 'controller_component', 'Order', 'Order', NULL, NULL),
(570, 'controller_component', 'Ascending', 'Ascending', NULL, NULL),
(571, 'controller_component', 'Descending', 'Descending', NULL, NULL),
(572, 'controller_component', 'Parent', 'Parent', NULL, NULL),
(573, 'controller_component', 'Current page', 'Current page', NULL, NULL),
(574, 'controller_component', 'Infoblock page', 'Infoblock page', NULL, NULL),
(575, 'controller_component', 'Conditions', 'Conditions', NULL, NULL),
(576, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(577, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(578, 'controller_component', 'Show pagination?', 'Show pagination?', NULL, NULL),
(579, 'controller_component', 'Sorting', 'Sorting', NULL, NULL),
(580, 'controller_component', 'Order', 'Order', NULL, NULL),
(581, 'controller_component', 'Ascending', 'Ascending', NULL, NULL),
(582, 'controller_component', 'Descending', 'Descending', NULL, NULL),
(583, 'controller_component', 'Parent', 'Parent', NULL, NULL),
(584, 'controller_component', 'Current page', 'Current page', NULL, NULL),
(585, 'controller_component', 'Infoblock page', 'Infoblock page', NULL, NULL),
(586, 'controller_component', 'Conditions', 'Conditions', NULL, NULL),
(587, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(588, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(589, 'controller_component', 'Show pagination?', 'Show pagination?', NULL, NULL),
(590, 'controller_component', 'Sorting', 'Sorting', NULL, NULL),
(591, 'controller_component', 'Order', 'Order', NULL, NULL),
(592, 'controller_component', 'Ascending', 'Ascending', NULL, NULL),
(593, 'controller_component', 'Descending', 'Descending', NULL, NULL),
(594, 'controller_component', 'Parent', 'Parent', NULL, NULL),
(595, 'controller_component', 'Current page', 'Current page', NULL, NULL),
(596, 'controller_component', 'Infoblock page', 'Infoblock page', NULL, NULL),
(597, 'controller_component', 'Conditions', 'Conditions', NULL, NULL),
(598, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(599, 'component_section', 'Subsections', 'Subsections', NULL, NULL),
(600, 'component_section', 'Don''t show', 'Don''t show', NULL, NULL),
(601, 'component_section', 'Show for the active item', 'Show for the active item', NULL, NULL),
(602, 'component_section', 'Show for all items', 'Show for all items', NULL, NULL),
(603, 'component_section', 'Source infoblock', 'Source infoblock', NULL, NULL),
(604, 'component_section', 'Show only header?', 'Show only header?', NULL, NULL),
(605, 'component_section', 'Hide on the index page', 'Hide on the index page', NULL, NULL),
(606, 'system', 'Page design', 'Page design', NULL, NULL),
(607, 'system', 'add', 'add', NULL, NULL),
(608, 'system', 'edit', 'edit', NULL, NULL),
(609, 'system', 'on', 'on', NULL, NULL),
(610, 'system', 'off', 'off', NULL, NULL),
(611, 'system', 'settings', 'settings', NULL, NULL),
(612, 'system', 'delete', 'delete', NULL, NULL),
(613, 'system', 'Select parent block', 'Select parent block', NULL, NULL),
(614, 'system', 'Permissions', 'Permissions', NULL, NULL),
(615, 'system', 'Upload file', 'Upload file', NULL, NULL),
(616, 'system', 'Download file', 'Download file', NULL, NULL),
(617, 'system', 'Site map', 'Site map', NULL, NULL),
(618, 'system', 'Export', 'Export', NULL, NULL),
(619, 'system', 'Download from FloximStore', 'Download from FloximStore', NULL, NULL),
(620, 'system', 'Import', 'Import', NULL, NULL),
(621, 'system', 'Change password', 'Change password', NULL, NULL),
(622, 'system', 'Cancel', 'Cancel', NULL, NULL),
(623, 'system', 'Redo', 'Redo', NULL, NULL),
(624, 'system', 'More', 'More', NULL, NULL),
(625, 'system', 'Management', 'Management', NULL, NULL),
(626, 'system', 'Development', 'Development', NULL, NULL),
(627, 'system', 'Sign out', 'Sign out', NULL, NULL),
(628, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(629, 'controller_component', 'Show pagination?', 'Show pagination?', NULL, NULL),
(630, 'controller_component', 'Sorting', 'Sorting', NULL, NULL),
(631, 'controller_component', 'Order', 'Order', NULL, NULL),
(632, 'controller_component', 'Ascending', 'Ascending', NULL, NULL),
(633, 'controller_component', 'Descending', 'Descending', NULL, NULL),
(634, 'controller_component', 'Parent', 'Parent', NULL, NULL),
(635, 'controller_component', 'Current page', 'Current page', NULL, NULL),
(636, 'controller_component', 'Infoblock page', 'Infoblock page', NULL, NULL),
(637, 'controller_component', 'Conditions', 'Conditions', NULL, NULL),
(638, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(639, 'component_section', 'Subsections', 'Subsections', NULL, NULL),
(640, 'component_section', 'Don''t show', 'Don''t show', NULL, NULL),
(641, 'component_section', 'Show for the active item', 'Show for the active item', NULL, NULL),
(642, 'component_section', 'Show for all items', 'Show for all items', NULL, NULL),
(643, 'component_section', 'Source infoblock', 'Source infoblock', NULL, NULL),
(644, 'component_section', 'Show only header?', 'Show only header?', NULL, NULL),
(645, 'component_section', 'Hide on the index page', 'Hide on the index page', NULL, NULL),
(646, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(647, 'controller_component', 'Show pagination?', 'Show pagination?', NULL, NULL),
(648, 'controller_component', 'Sorting', 'Sorting', NULL, NULL),
(649, 'controller_component', 'Order', 'Order', NULL, NULL),
(650, 'controller_component', 'Ascending', 'Ascending', NULL, NULL),
(651, 'controller_component', 'Descending', 'Descending', NULL, NULL),
(652, 'controller_component', 'Parent', 'Parent', NULL, NULL),
(653, 'controller_component', 'Current page', 'Current page', NULL, NULL),
(654, 'controller_component', 'Infoblock page', 'Infoblock page', NULL, NULL),
(655, 'controller_component', 'Conditions', 'Conditions', NULL, NULL),
(656, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(657, 'component_section', 'Subsections', 'Subsections', NULL, NULL),
(658, 'component_section', 'Don''t show', 'Don''t show', NULL, NULL),
(659, 'component_section', 'Show for the active item', 'Show for the active item', NULL, NULL),
(660, 'component_section', 'Show for all items', 'Show for all items', NULL, NULL),
(661, 'component_section', 'Source infoblock', 'Source infoblock', NULL, NULL),
(662, 'component_section', 'Show only header?', 'Show only header?', NULL, NULL),
(663, 'component_section', 'Hide on the index page', 'Hide on the index page', NULL, NULL),
(664, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(665, 'controller_component', 'Show pagination?', 'Show pagination?', NULL, NULL),
(666, 'controller_component', 'Sorting', 'Sorting', NULL, NULL),
(667, 'controller_component', 'Order', 'Order', NULL, NULL),
(668, 'controller_component', 'Ascending', 'Ascending', NULL, NULL),
(669, 'controller_component', 'Descending', 'Descending', NULL, NULL),
(670, 'controller_component', 'Parent', 'Parent', NULL, NULL),
(671, 'controller_component', 'Current page', 'Current page', NULL, NULL),
(672, 'controller_component', 'Infoblock page', 'Infoblock page', NULL, NULL),
(673, 'controller_component', 'Conditions', 'Conditions', NULL, NULL),
(674, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(675, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(676, 'controller_component', 'Show pagination?', 'Show pagination?', NULL, NULL),
(677, 'controller_component', 'Sorting', 'Sorting', NULL, NULL),
(678, 'controller_component', 'Order', 'Order', NULL, NULL),
(679, 'controller_component', 'Ascending', 'Ascending', NULL, NULL),
(680, 'controller_component', 'Descending', 'Descending', NULL, NULL),
(681, 'controller_component', 'Parent', 'Parent', NULL, NULL),
(682, 'controller_component', 'Current page', 'Current page', NULL, NULL),
(683, 'controller_component', 'Infoblock page', 'Infoblock page', NULL, NULL),
(684, 'controller_component', 'Conditions', 'Conditions', NULL, NULL),
(685, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(686, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(687, 'controller_component', 'Show pagination?', 'Show pagination?', NULL, NULL),
(688, 'controller_component', 'Sorting', 'Sorting', NULL, NULL),
(689, 'controller_component', 'Order', 'Order', NULL, NULL),
(690, 'controller_component', 'Ascending', 'Ascending', NULL, NULL),
(691, 'controller_component', 'Descending', 'Descending', NULL, NULL),
(692, 'controller_component', 'Parent', 'Parent', NULL, NULL),
(693, 'controller_component', 'Current page', 'Current page', NULL, NULL),
(694, 'controller_component', 'Infoblock page', 'Infoblock page', NULL, NULL),
(695, 'controller_component', 'Conditions', 'Conditions', NULL, NULL),
(696, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(697, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(698, 'controller_component', 'Show pagination?', 'Show pagination?', NULL, NULL),
(699, 'controller_component', 'Sorting', 'Sorting', NULL, NULL),
(700, 'controller_component', 'Order', 'Order', NULL, NULL),
(701, 'controller_component', 'Ascending', 'Ascending', NULL, NULL),
(702, 'controller_component', 'Descending', 'Descending', NULL, NULL),
(703, 'controller_component', 'Parent', 'Parent', NULL, NULL),
(704, 'controller_component', 'Current page', 'Current page', NULL, NULL),
(705, 'controller_component', 'Infoblock page', 'Infoblock page', NULL, NULL),
(706, 'controller_component', 'Conditions', 'Conditions', NULL, NULL),
(707, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(708, 'controller_component', 'Count entries', 'Count entries', NULL, NULL),
(709, 'controller_component', 'Show pagination?', 'Show pagination?', NULL, NULL),
(710, 'controller_component', 'Sorting', 'Sorting', NULL, NULL),
(711, 'controller_component', 'Order', 'Order', NULL, NULL),
(712, 'controller_component', 'Ascending', 'Ascending', NULL, NULL),
(713, 'controller_component', 'Descending', 'Descending', NULL, NULL),
(714, 'controller_component', 'Parent', 'Parent', NULL, NULL),
(715, 'controller_component', 'Current page', 'Current page', NULL, NULL),
(716, 'controller_component', 'Infoblock page', 'Infoblock page', NULL, NULL),
(717, 'controller_component', 'Conditions', 'Conditions', NULL, NULL),
(718, 'controller_component', 'Selected', 'Selected', NULL, NULL),
(719, 'component_section', 'Subsections', 'Subsections', NULL, NULL),
(720, 'component_section', 'Don''t show', 'Don''t show', NULL, NULL),
(721, 'component_section', 'Show for the active item', 'Show for the active item', NULL, NULL),
(722, 'component_section', 'Show for all items', 'Show for all items', NULL, NULL),
(723, 'component_section', 'Source infoblock', 'Source infoblock', NULL, NULL),
(724, 'component_section', 'Show only header?', 'Show only header?', NULL, NULL),
(725, 'component_section', 'Hide on the index page', 'Hide on the index page', NULL, NULL),
(726, 'system', 'Page design', 'Page design', NULL, NULL),
(727, 'system', 'add', 'add', NULL, NULL),
(728, 'system', 'edit', 'edit', NULL, NULL),
(729, 'system', 'on', 'on', NULL, NULL),
(730, 'system', 'off', 'off', NULL, NULL),
(731, 'system', 'settings', 'settings', NULL, NULL),
(732, 'system', 'delete', 'delete', NULL, NULL),
(733, 'system', 'Select parent block', 'Select parent block', NULL, NULL),
(734, 'system', 'Permissions', 'Permissions', NULL, NULL),
(735, 'system', 'Upload file', 'Upload file', NULL, NULL),
(736, 'system', 'Download file', 'Download file', NULL, NULL),
(737, 'system', 'Site map', 'Site map', NULL, NULL),
(738, 'system', 'Export', 'Export', NULL, NULL),
(739, 'system', 'Download from FloximStore', 'Download from FloximStore', NULL, NULL),
(740, 'system', 'Import', 'Import', NULL, NULL),
(741, 'system', 'Change password', 'Change password', NULL, NULL),
(742, 'system', 'Cancel', 'Cancel', NULL, NULL),
(743, 'system', 'Redo', 'Redo', NULL, NULL),
(744, 'system', 'More', 'More', NULL, NULL),
(745, 'system', 'Management', 'Management', NULL, NULL),
(746, 'system', 'Development', 'Development', NULL, NULL),
(747, 'system', 'Sign out', 'Sign out', NULL, NULL),
(748, 'system', 'Editing ', 'Editing ', NULL, NULL),
(749, 'system', 'Editing ', 'Editing ', NULL, NULL),
(750, 'system', 'Editing ', 'Editing ', NULL, NULL),
(751, 'system', 'Editing ', 'Editing ', NULL, NULL),
(752, 'system', 'Editing ', 'Editing ', NULL, NULL),
(753, 'system', 'Logs', 'Logs', NULL, NULL),
(754, 'system', 'Request', 'Request', NULL, NULL),
(755, 'system', 'Date', 'Date', NULL, NULL),
(756, 'system', 'Time', 'Time', NULL, NULL),
(757, 'system', 'Entries', 'Entries', NULL, NULL),
(758, 'system', 'Delte', 'Delte', NULL, NULL),
(759, 'system', 'Delete all', 'Delete all', NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `fx_layout`
--

CREATE TABLE IF NOT EXISTS `fx_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=64 AUTO_INCREMENT=13 ;

--
-- Дамп данных таблицы `fx_layout`
--

INSERT INTO `fx_layout` (`id`, `keyword`, `name`) VALUES
(1, 'supernova', 'Super Nova'),
(9, 'dummy', 'Dummy'),
(10, 'jeeptravel', 'JeepTravel'),
(11, 'demo', 'demo'),
(12, 'v3', 'v3');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=647 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=68 AUTO_INCREMENT=5 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=17 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fx_patch`
--

CREATE TABLE IF NOT EXISTS `fx_patch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to` varchar(255) CHARACTER SET utf8 NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `description` char(255) CHARACTER SET utf8 NOT NULL,
  `from` varchar(255) CHARACTER SET utf8 NOT NULL,
  `status` varchar(20) CHARACTER SET utf8 NOT NULL,
  `url` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=24 ;

--
-- Дамп данных таблицы `fx_patch`
--

INSERT INTO `fx_patch` (`id`, `to`, `created`, `description`, `from`, `status`, `url`) VALUES
(20, '0.1.1', '2013-08-19 15:24:17', 'Adding some trolo file!', '0.1.0', 'installed', 'http://floxim.org/getfloxim/patches/0.1.0-0.1.1/patch_0.1.1.zip'),
(21, '0.1.2', '2013-08-19 15:25:46', '', '0.1.1', 'ready', 'http://floxim.org/getfloxim/patches/0.1.1-0.1.2/patch_0.1.2.zip'),
(22, '0.1.5', '2013-08-19 15:48:58', '', '0.1.2', 'pending', 'http://floxim.org/getfloxim/patches/0.1.2-0.1.5/patch_0.1.5.zip'),
(23, '0.2.0', '2013-08-19 15:48:58', '', '0.1.5', 'pending', 'http://floxim.org/getfloxim/patches/0.1.5-0.2.0/patch_0.2.0.zip');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=56 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=126;

--
-- Дамп данных таблицы `fx_session`
--

INSERT INTO `fx_session` (`id`, `user_id`, `session_start`, `session_time`, `ip`, `login_save`, `site_id`, `auth_type`) VALUES
('746a01c73990999546b74d7520f32a41', 2367, 1395065349, 1395222068, 2130706433, 0, 0, 1),
('d4020b281f4a0b68b7704ecf86beecef', 2367, 1395056493, 1395148511, 2130706433, 0, 0, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=70 AUTO_INCREMENT=40 ;

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
(7, 'spam_from_email', 'zuynew@yandex.ru', 'system', 0),
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=292 AUTO_INCREMENT=19 ;

--
-- Дамп данных таблицы `fx_site`
--

INSERT INTO `fx_site` (`id`, `parent_id`, `name`, `domain`, `layout_id`, `color`, `mirrors`, `priority`, `checked`, `index_page_id`, `error_page_id`, `created`, `last_updated`, `robots`, `disallow_indexing`, `type`, `language`, `offline_text`, `store_id`) VALUES
(1, 0, 'PlayGround', 'alt.floxim.loc', 10, 2, '', 0, 1, 2, 3, '2012-05-24 12:42:50', '2014-01-21 14:58:41', '# Floxim Robots file\r\nUser-agent: *\r\nDisallow: /install/', 0, 'useful', 'en', '<table width=''100%'' height=''100%'' border=''0'' cellpadding=''0'' cellspacing=''0''><tr><td align=''center''>Сайт временно (!) недоступен.</td></tr></table>', NULL),
(15, 0, 'JeepTravel', 'floxim.loc', 10, 0, '', 1, 1, 1883, 1884, '2013-06-08 17:03:02', '2013-09-09 13:02:57', NULL, 0, 'useful', 'en', NULL, NULL),
(16, 0, 'Default', 'floxim.def', 9, 0, '', 2, 1, 2210, 2211, '2013-10-03 16:54:26', '2013-10-03 12:55:11', NULL, 0, 'useful', 'en', NULL, NULL),
(17, 0, 'Demo', 'dem.floxim.loc', 11, 0, '', 3, 1, 2501, 2502, '2013-12-09 17:51:13', '2013-12-10 09:55:30', NULL, 0, 'useful', 'en', NULL, NULL),
(18, 0, 'Floxim', 'v3.floxim.loc', 12, 0, '', 4, 1, 2635, 2636, '2014-01-28 11:39:50', '2014-01-28 07:43:47', NULL, 0, 'useful', 'en', NULL, NULL);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=113 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=13 AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `fx_user_group`
--

INSERT INTO `fx_user_group` (`id`, `user_id`, `group_id`) VALUES
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=111 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `fx_widget`
--

INSERT INTO `fx_widget` (`id`, `name`, `keyword`, `description`, `group`, `checked`, `icon`, `embed`, `store_id`) VALUES
(1, 'Authorization form', 'authform', '', 'Profile', 1, 'auth', '', 'widget.auth'),
(2, 'Password recover form', 'recoverpasswd', '', 'Profile', 1, 'auth', '', 'widget.recoverpasswd'),
(3, 'Search Line', 'search', NULL, NULL, 1, '', 'narrow-wide', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

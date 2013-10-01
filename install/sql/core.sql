-- phpMyAdmin SQL Dump
-- version 3.5.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 01, 2013 at 05:23 PM
-- Server version: 5.5.32-MariaDB
-- PHP Version: 5.5.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `floxim`
--

-- --------------------------------------------------------

--
-- Table structure for table `fx_auth_external`
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
-- Table structure for table `fx_classificator`
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
-- Table structure for table `fx_classificator_cities`
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
-- Table structure for table `fx_classificator_country`
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
-- Table structure for table `fx_classificator_region`
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
-- Table structure for table `fx_component`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=100 AUTO_INCREMENT=62 ;

--
-- Dumping data for table `fx_component`
--

INSERT INTO `fx_component` (`id`, `keyword`, `name`, `description`, `group`, `icon`, `store_id`, `parent_id`, `item_name`) VALUES
(1, 'user', 'Users', '', 'Users', '', 'component.user', 0, 'User'),
(19, 'text', 'Text', '', 'Basic', '', 'component.text', 36, 'text'),
(23, 'page', 'Pages', '', 'Basic', '', NULL, 36, 'page'),
(24, 'section', 'Navigation', '', 'Basic', '', NULL, 23, 'Section'),
(30, 'blogpost', 'Blog', '', 'Blog', '', NULL, 49, 'Blog post'),
(31, 'tag', 'Tags', '', 'Blog', '', NULL, 23, 'Tag'),
(32, 'tagpost', 'Tags for entity', '', 'Blog', '', NULL, 36, 'Tag to entity link'),
(36, 'content', 'Content', '', 'Basic', '', NULL, 0, 'Content'),
(46, 'travel_route', 'Tours', '', 'Travel', '', NULL, 23, 'Tour'),
(47, 'gallery', 'Image galleries', '', 'Gallery', '', NULL, 23, 'Gallery'),
(48, 'photo', 'Image', '', 'Gallery', '', NULL, 36, 'image'),
(49, 'publication', 'Publications', NULL, 'Basic', '', NULL, 23, 'Publication'),
(50, 'comment', 'Comment', NULL, 'Blog', '', NULL, 36, 'comment'),
(58, 'faq', 'FAQ', NULL, 'Basic', '', NULL, 23, 'FAQ'),
(59, 'video', 'Video', NULL, 'Basic', '', NULL, 36, 'Video'),
(60, 'award', 'Award', '', 'Basic', '', NULL, 23, 'Award'),
(61, 'company', 'Company', NULL, 'Basic', '', NULL, 23, 'Company');

-- --------------------------------------------------------

--
-- Table structure for table `fx_content`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=47 AUTO_INCREMENT=2141 ;

--
-- Dumping data for table `fx_content`
--

INSERT INTO `fx_content` (`id`, `priority`, `checked`, `created`, `last_updated`, `user_id`, `type`, `infoblock_id`, `site_id`, `parent_id`) VALUES
(2, 0, 0, '2012-05-24 12:42:50', '2013-04-29 12:01:11', 0, 'section', 0, 1, 0),
(3, 7, 0, '2012-05-24 12:42:50', '2013-04-29 12:01:11', 0, 'section', 0, 1, 2),
(16, 5, 1, '2012-05-28 12:27:15', '2013-05-17 21:14:38', 0, 'section', 3, 1, 2),
(99, 0, 1, '0000-00-00 00:00:00', '2013-07-26 18:12:18', 0, 'user', 0, 0, 0),
(112, 0, 1, '2013-04-24 14:12:36', '2013-04-29 12:01:11', 99, 'text', 16, 1, 2),
(1883, 0, 1, '2013-06-08 17:03:02', '2013-06-08 09:03:02', 99, 'page', 0, 15, NULL),
(1887, 3, 1, '2013-06-10 02:17:40', '2013-07-14 03:19:45', 99, 'section', 69, 15, 1883),
(1889, 1, 1, '2013-06-10 02:26:56', '2013-07-14 03:39:35', 99, 'section', 69, 15, 1883),
(1890, 2, 1, '2013-06-10 02:27:12', '2013-07-14 03:39:35', 99, 'section', 69, 15, 1883),
(1891, 1, 1, '2013-06-10 11:38:10', '2013-08-21 17:35:44', 99, 'travel_route', 70, 15, 1883),
(1892, 2, 1, '2013-06-10 12:17:59', '2013-08-21 17:35:44', 99, 'travel_route', 70, 15, 1883),
(1898, 1, 1, '2013-06-11 13:15:18', '2013-09-10 13:03:24', 99, 'section', 76, 15, 1883),
(1899, 3, 1, '2013-06-11 13:16:36', '2013-09-10 13:03:24', 99, 'section', 76, 15, 1883),
(1900, 2, 1, '2013-06-11 13:17:27', '2013-09-10 13:03:24', 99, 'section', 76, 15, 1883),
(1901, 4, 1, '2013-06-11 13:17:47', '2013-06-11 05:17:53', 99, 'section', 76, 15, 1883),
(1902, 0, 1, '2013-06-13 01:24:02', '2013-06-12 17:24:02', 99, 'section', 77, 15, 1887),
(1903, 0, 1, '2013-06-13 01:24:43', '2013-06-12 17:24:43', 99, 'text', 74, 15, 1887),
(1910, 0, 1, '2013-06-13 04:55:44', '2013-06-12 20:55:44', 99, 'text', 74, 15, 1902),
(1912, 1, 1, '2013-06-13 05:29:37', '2013-06-12 21:58:48', 99, 'gallery', 79, 15, 1889),
(1914, 6, 1, '2013-06-13 05:32:07', '2013-06-12 21:58:48', 99, 'gallery', 79, 15, 1889),
(1916, 4, 1, '2013-06-13 05:33:58', '2013-06-12 21:58:48', 99, 'gallery', 79, 15, 1889),
(1917, 3, 1, '2013-06-13 05:34:31', '2013-06-12 21:58:48', 99, 'gallery', 79, 15, 1889),
(1919, 2, 1, '2013-06-17 04:08:53', '2013-06-16 21:00:18', 99, 'photo', 81, 15, 1916),
(1923, 5, 1, '2013-06-17 04:26:42', '2013-06-16 21:00:06', 99, 'photo', 81, 15, 1916),
(1924, 0, 1, '2013-06-17 06:24:59', '2013-06-16 22:24:59', 99, 'text', 74, 15, 1916),
(1925, 4, 1, '2013-06-17 06:30:30', '2013-07-14 03:19:38', 99, 'section', 69, 15, 1883),
(1933, 4, 1, '2013-06-18 16:02:50', '2013-07-01 06:18:13', 99, 'tag', 83, 15, 1925),
(1968, 3, 1, '2013-06-21 13:45:59', '2013-06-24 03:13:17', 99, 'blogpost', 82, 15, 1925),
(1976, 1, 1, '2013-06-21 14:18:44', '2013-06-24 03:13:17', 99, 'blogpost', 82, 15, 1925),
(1996, 0, 1, '2013-07-01 16:35:39', '2013-07-01 08:35:39', 99, 'blogpost', 82, 15, 1925),
(2021, 0, 1, '2013-07-12 15:03:23', '2013-07-12 07:03:23', 99, 'photo', 81, 15, 1912),
(2022, 0, 1, '2013-07-12 15:44:26', '2013-07-12 07:44:26', 99, 'photo', 81, 15, 1914),
(2023, 0, 1, '2013-07-12 15:47:42', '2013-07-12 07:47:42', 99, 'photo', 81, 15, 1917),
(2025, 0, 1, '2013-07-12 16:13:25', '2013-07-12 08:13:26', 99, 'photo', 81, 15, 1912),
(2026, 0, 1, '2013-07-12 16:18:28', '2013-07-12 08:18:28', 99, 'photo', 81, 15, 1917),
(2027, 0, 1, '2013-07-13 15:39:32', '2013-07-13 07:39:32', 99, 'text', 74, 15, 1890),
(2028, 0, 1, '2013-07-13 17:11:06', '2013-07-13 09:11:06', 99, 'tag', 83, 15, 1925),
(2029, 0, 1, '2013-07-13 17:11:06', '2013-07-13 09:11:06', 99, 'tagpost', 84, 15, 1976),
(2032, 1, 1, '2013-07-13 17:14:40', '2013-07-19 20:49:31', 99, 'tagpost', 84, 15, 1968),
(2033, 0, 1, '2013-07-13 17:14:40', '2013-07-13 09:14:40', 99, 'tag', 83, 15, 1925),
(2034, 2, 1, '2013-07-13 17:14:40', '2013-07-19 20:49:31', 99, 'tagpost', 84, 15, 1968),
(2039, 0, 1, '2013-07-14 11:20:37', '2013-07-14 03:20:37', 99, 'tag', 83, 15, 1925),
(2040, 2, 1, '2013-07-14 11:20:37', '2013-09-05 09:57:13', 99, 'tagpost', 84, 15, 1996),
(2047, 0, 1, '2013-07-19 20:08:42', '2013-07-19 16:08:42', 99, 'text', 74, 15, 2033),
(2049, 4, 1, '2013-07-19 21:09:09', '2013-09-06 14:51:16', 99, 'tagpost', 84, 15, 1996),
(2058, 8, 1, '2013-07-25 09:36:53', '2013-07-25 05:36:53', 99, 'photo', 121, 15, 1891),
(2059, 9, 1, '2013-08-01 20:32:28', '2013-08-01 16:32:28', 99, 'text', 104, 15, 1925),
(2062, 11, 1, '2013-08-01 20:38:01', '2013-08-01 16:38:01', 99, 'text', 75, 15, 1883),
(2067, 12, 1, '2013-08-07 01:05:56', '2013-08-06 21:05:57', 99, 'tag', 83, 15, 1925),
(2068, 1, 1, '2013-08-07 01:05:56', '2013-09-05 09:57:13', 99, 'tagpost', 84, 15, 1996),
(2069, 13, 1, '2013-08-07 01:06:48', '2013-08-06 21:06:48', 99, 'tag', 83, 15, 1925),
(2070, 13, 1, '2013-08-07 01:06:48', '2013-08-06 21:06:48', 99, 'tagpost', 84, 15, 1968),
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
(2083, 3, 1, '2013-08-26 20:36:55', '2013-09-02 14:25:20', 99, 'tagpost', 84, 15, 1996),
(2084, 5, 1, '2013-09-06 18:03:04', '2013-09-06 14:51:16', 99, 'tagpost', 84, 15, 1996),
(2128, 23, 1, '2013-09-25 19:03:04', '2013-09-25 15:03:04', 99, 'comment', 127, 15, 1996),
(2132, 24, 1, '2013-09-30 17:31:24', '2013-09-30 13:31:24', 99, 'section', 69, 15, 1883),
(2133, 25, 1, '2013-09-30 17:32:26', '2013-09-30 13:32:26', 99, 'faq', 131, 15, 2132),
(2134, 26, 1, '2013-09-30 17:39:51', '2013-09-30 13:39:51', 99, 'section', 69, 15, 1883),
(2135, 27, 1, '2013-09-30 17:44:31', '2013-09-30 13:44:31', 99, 'video', 133, 15, 2134),
(2139, 28, 1, '2013-10-01 15:39:47', '2013-10-01 11:39:47', 99, 'section', 69, 15, 1883),
(2140, 29, 1, '2013-10-01 15:41:51', '2013-10-01 11:41:51', 99, 'award', 134, 15, 2139);

-- --------------------------------------------------------

--
-- Table structure for table `fx_content_award`
--

CREATE TABLE IF NOT EXISTS `fx_content_award` (
  `id` int(11) NOT NULL,
  `image` int(11) DEFAULT NULL,
  `description` text,
  `year` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fx_content_award`
--

INSERT INTO `fx_content_award` (`id`, `image`, `description`, `year`) VALUES
(2140, 426, 'asdfasdfasdfasdf', 2010);

-- --------------------------------------------------------

--
-- Table structure for table `fx_content_blogpost`
--

CREATE TABLE IF NOT EXISTS `fx_content_blogpost` (
  `id` int(11) NOT NULL,
  `metatype` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=2824;

--
-- Dumping data for table `fx_content_blogpost`
--

INSERT INTO `fx_content_blogpost` (`id`, `metatype`) VALUES
(1968, 'article'),
(1976, 'post'),
(1996, 'post');

-- --------------------------------------------------------

--
-- Table structure for table `fx_content_comment`
--

CREATE TABLE IF NOT EXISTS `fx_content_comment` (
  `id` int(11) NOT NULL,
  `comment_text` text,
  `publish_date` datetime DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `is_moderated` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fx_content_comment`
--

INSERT INTO `fx_content_comment` (`id`, `comment_text`, `publish_date`, `user_name`, `is_moderated`) VALUES
(2128, 'asdasd', '2013-00-00 00:00:00', 'asdasd', 0);

-- --------------------------------------------------------

--
-- Table structure for table `fx_content_company`
--

CREATE TABLE IF NOT EXISTS `fx_content_company` (
  `id` int(11) NOT NULL,
  `logo` int(11) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fx_content_faq`
--

CREATE TABLE IF NOT EXISTS `fx_content_faq` (
  `id` int(11) NOT NULL,
  `question` varchar(255) DEFAULT NULL,
  `answer` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fx_content_faq`
--

INSERT INTO `fx_content_faq` (`id`, `question`, `answer`) VALUES
(2133, 'WTF?', '<p>Just, in case. ...</p>');

-- --------------------------------------------------------

--
-- Table structure for table `fx_content_gallery`
--

CREATE TABLE IF NOT EXISTS `fx_content_gallery` (
  `id` int(11) NOT NULL,
  `publish_date` datetime DEFAULT NULL,
  `cover` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fx_content_gallery`
--

INSERT INTO `fx_content_gallery` (`id`, `publish_date`, `cover`) VALUES
(1912, '2013-05-10 00:00:00', 366),
(1914, '2012-09-12 00:00:00', 368),
(1916, '2012-10-08 00:00:00', 346),
(1917, '2011-11-08 00:00:00', 371);

-- --------------------------------------------------------

--
-- Table structure for table `fx_content_page`
--

CREATE TABLE IF NOT EXISTS `fx_content_page` (
  `id` int(11) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `comments_counter` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=62;

--
-- Dumping data for table `fx_content_page`
--

INSERT INTO `fx_content_page` (`id`, `url`, `name`, `title`, `comments_counter`) VALUES
(2, '/', 'Main page', '', NULL),
(3, '/404/', '404', NULL, NULL),
(16, '/contacts/', 'Contacts', NULL, NULL),
(1883, '/', 'Jeep Travels', 'Jeep Travels: super travels!', NULL),
(1887, '/about', 'About', NULL, NULL),
(1889, '/portfolio/', 'Portfolio', NULL, NULL),
(1890, '/contacts', 'Contacts', NULL, NULL),
(1891, '/summer-rally', 'Mangyshlak: The Great Step', '', NULL),
(1892, '/paris-dakar', 'Argentina – Chilix', '', NULL),
(1898, 'http://facebook.com/', 'Facebook', NULL, NULL),
(1899, 'http://plus.google.com/', 'Google+', NULL, NULL),
(1900, 'http://instagram.com', 'Instagram', NULL, NULL),
(1901, 'http://youtube.com', 'YouTube', NULL, NULL),
(1902, '/test', 'Take part in our events', NULL, NULL),
(1912, '/Kiev', 'Kiev', '', NULL),
(1914, '/Dominikanskaya-respublika', 'Dominicana', '', NULL),
(1916, '/gaityanski-holyday', 'Haiti', '', NULL),
(1917, '/Pereslavl-Zalesskiy', 'Namibia', '', NULL),
(1925, '/Blog', 'Our blog', NULL, NULL),
(1933, '/tag-ivan-kurochkin', 'John Kurochkin', 'News about John', NULL),
(1968, '/funeral', 'Our funeral instructions', '', NULL),
(1976, '/hi', 'Hello world!', '', NULL),
(1996, '/dominikana', 'Dominicana is mega cool', '', 1),
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
(2132, '/faq', 'FAQ', 'FAQ', 0),
(2133, '/wtf', 'Wtf?', 'Wtf?', 0),
(2134, '/videos', 'Videos', 'Videos', 0),
(2139, '/awards', 'Awards', 'Awards', 0),
(2140, '/test-2', 'Test', 'Test', 0);

-- --------------------------------------------------------

--
-- Table structure for table `fx_content_photo`
--

CREATE TABLE IF NOT EXISTS `fx_content_photo` (
  `id` int(11) NOT NULL,
  `photo` int(11) DEFAULT NULL,
  `description` text,
  `copy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fx_content_photo`
--

INSERT INTO `fx_content_photo` (`id`, `photo`, `description`, `copy`) VALUES
(1919, 105, 'Haitian people.', 'Dmitry Medvedev'),
(1923, 116, 'Haitian Pussy Riot', NULL),
(2021, 361, 'Kiev', ''),
(2022, 367, '', ''),
(2023, 369, 'Swakopmund', ''),
(2025, 373, 'Ukranian steppe', ''),
(2026, 374, 'Beautiful evening', ''),
(2058, 390, 'Это же Саша Васильев!', '');

-- --------------------------------------------------------

--
-- Table structure for table `fx_content_publication`
--

CREATE TABLE IF NOT EXISTS `fx_content_publication` (
  `id` int(11) NOT NULL,
  `publish_date` datetime DEFAULT NULL,
  `anounce` text,
  `image` int(11) DEFAULT NULL,
  `text` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fx_content_publication`
--

INSERT INTO `fx_content_publication` (`id`, `publish_date`, `anounce`, `image`, `text`) VALUES
(1968, '2013-05-17 12:20:17', '<p class="">\n	         Everyone shoud know! what?</p><p class="">\n	This is «Alyona»!</p>', 383, '<p>\r\n	        They told us this was not logistically possible and a hugely inconsiderate thing to do to a grief stricken family.\r\n</p>\r\n<p>\r\n	        I called them a bunch of a-holes and soccer-kicked the watermelon off the picnic table and into the bushes.\r\n</p>'),
(1976, '2012-04-19 00:00:00', '<p>​Jeep Travel is here to make you laugh.</p>', NULL, '<p>\r\n	 Put your hands up in the air!\r\n</p>'),
(1996, '2013-10-26 00:00:00', '<p>\r\n	                                Dominicana Republic''s Afro-tourism with a side of beach!\r\n</p>', 379, '<p>\r\n	                 On the weekend of Jan 19th, 2013, we left the farm early one morning for Samana via a direct <em>gua-gua</em>.  It left in rising sun at 7 am from nearby Saboneta and arrived there at 10:30 am.\r\n</p>\r\n<p>\r\n	 <img src="/floxim_files/content/me_2.jpg">\r\n</p>\r\n<p>\r\n	              It was a very easy trip with one stop on an uncomfortable and overcrowded bus which was exacerbated by the holiday weekend.  Fortunately it was cheap too, only 300 pesos ($7.50 USD).\r\n</p>\r\n<p>\r\n	              From there we met some friends and went onto Las Galeras which we am sure is one of the more beautiful places on this island.  Very Caribbean with its palm ladden beaches, forested hillsides in the distance, and white sands with colors of the sea you can hardly imagine.\r\n</p>');

-- --------------------------------------------------------

--
-- Table structure for table `fx_content_section`
--

CREATE TABLE IF NOT EXISTS `fx_content_section` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=7;

--
-- Dumping data for table `fx_content_section`
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
(1925),
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
(2132),
(2134),
(2139);

-- --------------------------------------------------------

--
-- Table structure for table `fx_content_tag`
--

CREATE TABLE IF NOT EXISTS `fx_content_tag` (
  `id` int(11) NOT NULL,
  `counter` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=9;

--
-- Dumping data for table `fx_content_tag`
--

INSERT INTO `fx_content_tag` (`id`, `counter`) VALUES
(1933, 1),
(2028, 3),
(2033, 2),
(2039, 1),
(2067, 1),
(2069, 1);

-- --------------------------------------------------------

--
-- Table structure for table `fx_content_tagpost`
--

CREATE TABLE IF NOT EXISTS `fx_content_tagpost` (
  `id` int(11) NOT NULL,
  `tag_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=13;

--
-- Dumping data for table `fx_content_tagpost`
--

INSERT INTO `fx_content_tagpost` (`id`, `tag_id`, `post_id`, `comment`) VALUES
(2029, 2028, 1976, NULL),
(2032, 2028, 1968, 'are u happy?'),
(2034, 2033, 1968, 'noooooooooooooo'),
(2040, 2039, 1996, ''),
(2049, 1933, 1996, ''),
(2068, 2067, 1996, ''),
(2070, 2069, 1968, ''),
(2083, 2033, 1996, ''),
(2084, 2028, 1996, '');

-- --------------------------------------------------------

--
-- Table structure for table `fx_content_text`
--

CREATE TABLE IF NOT EXISTS `fx_content_text` (
  `id` int(11) NOT NULL,
  `text` text,
  `olofild` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=1199;

--
-- Dumping data for table `fx_content_text`
--

INSERT INTO `fx_content_text` (`id`, `text`, `olofild`) VALUES
(112, '<p>Put your content here!​</p>', NULL),
(1903, '<p>\n	The adventure began back in 1977, when Thierry Sabine got lost on his motorbike in the Libyan desert during the Abidjan-Nice Rally. Saved from the sands in extremis, he returned to France still in thrall to this landscape and promising himself he would share his fascination with as many people as possible. He proceeded to come up with a route starting in Europe, continuing to Algiers and crossing Agadez before eventually finishing at Dakar. The founder coined a motto for his inspiration: "A challenge for those who go. A dream for those who stay behind." Courtesy of his great conviction and that modicum of madness peculiar to all great ideas, the plan quickly became a reality. Since then, the Paris-Dakar, a unique event sparked by the spirit of adventure, open to all riders and carrying a message of friendship between all men, has never failed to challenge, surprise and excite. Over the course of almost thirty years, it has generated innumerable sporting and human stories.</p><p>(c) <a href="http://www.dakar.com">www.dakar.com</a></p>', NULL),
(1910, '<p class="">\n	Everyone can join our team! Please, feel free to contact us for a details.</p>', NULL),
(1924, '<p></p><p>Port-au-Prince, Haiti. Here we are!&nbsp;</p><p></p>', NULL),
(2027, '<p>\r\n	Have questions? Need more information?\r\n</p>\r\n<p>\r\n	Send a starter inquiry to <a href="http://mailto:info@jeeptravel.loc">info@jeeptravel.loc</a>\r\n</p>', NULL),
(2047, '<p>\r\n	We are going to post many sad  but interesting posts under the "funeral" tag. Stay idle.\r\n</p>', NULL),
(2059, '<p>\n	Feel free to subscribe our&nbsp;<a href="/Blog?rss" target="_blank">​RSS</a>&nbsp;chanel!</p>', NULL),
(2062, '<p>\r\n	Welcome welcome welcom!\r\n</p>', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fx_content_travel_route`
--

CREATE TABLE IF NOT EXISTS `fx_content_travel_route` (
  `id` int(11) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fx_content_travel_route`
--

INSERT INTO `fx_content_travel_route` (`id`, `start_date`, `end_date`) VALUES
(1891, '2013-05-14 00:00:00', '2013-05-01 00:00:00'),
(1892, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `fx_content_user`
--

CREATE TABLE IF NOT EXISTS `fx_content_user` (
  `id` int(11) NOT NULL,
  `password` varchar(45) NOT NULL,
  `email` char(255) NOT NULL,
  `login` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `registration_code` varchar(45) DEFAULT NULL,
  `avatar` int(11) DEFAULT NULL,
  `forum_messages` int(11) NOT NULL DEFAULT '0',
  `pa_balance` double NOT NULL DEFAULT '0',
  `auth_hash` varchar(50) NOT NULL DEFAULT '',
  `is_admin` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `User_ID` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=104;

--
-- Dumping data for table `fx_content_user`
--

INSERT INTO `fx_content_user` (`id`, `password`, `email`, `login`, `name`, `registration_code`, `avatar`, `forum_messages`, `pa_balance`, `auth_hash`, `is_admin`) VALUES
(99, 'dd6825a7baab5df7043ec16b948bb06e', 'zuynew@yandex.ru', 'admin', 'Adminio', NULL, NULL, 0, 0, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `fx_content_video`
--

CREATE TABLE IF NOT EXISTS `fx_content_video` (
  `id` int(11) NOT NULL,
  `embed_html` text,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fx_content_video`
--

INSERT INTO `fx_content_video` (`id`, `embed_html`, `description`) VALUES
(2135, '<iframe width="420" height="315" src="//www.youtube.com/embed/4OGMGNqz15Q" frameborder="0" allowfullscreen></iframe>', '');

-- --------------------------------------------------------

--
-- Table structure for table `fx_controller`
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
-- Table structure for table `fx_crontask`
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
-- Table structure for table `fx_datatype`
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
-- Dumping data for table `fx_datatype`
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
-- Table structure for table `fx_field`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=95 AUTO_INCREMENT=238 ;

--
-- Dumping data for table `fx_field`
--

INSERT INTO `fx_field` (`id`, `parent`, `component_id`, `ctpl_id`, `system_table_id`, `widget_id`, `name`, `description`, `type`, `format`, `not_null`, `priority`, `searchable`, `default`, `inheritance`, `type_of_edit`, `checked`) VALUES
(1, NULL, 1, 0, 0, 0, 'name', 'Screen name', 1, '', 0, 0, 1, '', 0, 1, 1),
(2, NULL, 1, 0, 0, 0, 'avatar', 'Userpic', 11, '', 0, 0, 0, '', 0, 1, 1),
(118, NULL, 19, 0, 0, 0, 'text', 'Text', 3, 'a:1:{s:4:"html";s:1:"1";}', 0, 0, 1, '', 0, 1, 1),
(153, NULL, 1, 0, 0, 0, 'email', 'E-mail', 1, '', 0, 142, 1, NULL, 0, 1, 1),
(165, NULL, 23, 0, 0, 0, 'url', 'URL', 1, '', 0, 2, 0, '', 0, 1, 1),
(186, NULL, 32, 0, 0, 0, 'tag_id', 'Tag', 13, 'a:3:{s:6:"target";s:2:"31";s:9:"prop_name";s:3:"tag";s:11:"render_type";s:10:"livesearch";}', 0, 157, 0, '', 0, 1, 1),
(190, NULL, 23, 0, 0, 0, 'name', 'Name', 1, '', 1, 0, 1, '', 0, 1, 1),
(191, NULL, 23, 0, 0, 0, 'title', 'Title', 1, '', 0, 158, 0, '', 0, 1, 1),
(192, NULL, 31, 0, 0, 0, 'counter', 'Usage counter', 2, '', 0, 159, 0, '0', 0, 3, 1),
(195, NULL, 30, 0, 0, 0, 'tags', 'Tags', 14, 'a:2:{s:6:"target";s:7:"198.186";s:11:"render_type";s:5:"table";}', 0, 2, 0, '', 0, 1, 1),
(196, NULL, 36, 0, 0, 0, 'parent_id', 'Parent', 13, 'a:3:{s:6:"target";s:2:"23";s:9:"prop_name";s:0:"";s:11:"render_type";s:6:"select";}', 0, 161, 0, '', 0, 3, 1),
(197, NULL, 31, 0, 0, 0, 'tagposts', 'Tag posts', 14, 'a:1:{s:6:"target";s:3:"186";}', 0, 162, 0, '', 0, 3, 1),
(198, NULL, 32, 0, 0, 0, 'post_id', 'Page', 13, 'a:4:{s:6:"target";s:2:"30";s:9:"prop_name";s:4:"post";s:9:"is_parent";s:1:"1";s:11:"render_type";s:6:"select";}', 0, 163, 0, '', 0, 3, 1),
(199, NULL, 46, 0, 0, 0, 'start_date', 'Start date', 8, '', 0, 164, 0, '', 0, 1, 1),
(200, NULL, 46, 0, 0, 0, 'end_date', 'End date', 8, '', 0, 165, 0, '', 0, 1, 1),
(201, NULL, 47, 0, 0, 0, 'publish_date', 'Publish date', 8, '', 0, 166, 0, '', 0, 1, 1),
(202, NULL, 47, 0, 0, 0, 'cover', 'Cover image', 11, '', 0, 167, 0, '', 0, 1, 1),
(203, NULL, 48, 0, 0, 0, 'photo', 'Image', 11, '', 1, 168, 0, '', 0, 1, 1),
(204, NULL, 48, 0, 0, 0, 'description', 'Description', 3, '', 0, 169, 0, '', 0, 1, 1),
(205, NULL, 48, 0, 0, 0, 'copy', 'Copy', 1, '', 0, 170, 0, '', 0, 1, 1),
(209, NULL, 30, 0, 0, 0, 'metatype', 'meta type', 4, 'a:2:{s:6:"source";s:6:"manual";s:6:"values";a:2:{i:1;a:2:{s:2:"id";s:4:"post";s:5:"value";s:11:"Just a post";}i:2;a:2:{s:2:"id";s:7:"article";s:5:"value";s:24:"Long interesting article";}}}', 0, 171, 0, 'post', 0, 1, 1),
(210, NULL, 32, 0, 0, 0, 'comment', 'Why the tag is relevant', 1, '', 0, 172, 0, '', 0, 1, 1),
(212, NULL, 49, 0, 0, 0, 'publish_date', 'Publish date', 8, '', 0, 174, 0, '', 0, 1, 1),
(213, NULL, 49, 0, 0, 0, 'anounce', 'Anounce', 3, 'a:1:{s:4:"html";s:1:"1";}', 0, 175, 0, '', 0, 1, 1),
(214, NULL, 49, 0, 0, 0, 'image', 'Image', 11, '', 0, 176, 0, '', 0, 1, 1),
(215, NULL, 49, 0, 0, 0, 'text', 'Text', 3, 'a:1:{s:4:"html";s:1:"1";}', 0, 177, 0, '', 0, 1, 1),
(216, NULL, 1, 0, 0, 0, 'is_admin', 'Is admin?', 5, '', 0, 178, 0, '0', 0, 2, 1),
(217, NULL, 19, 0, 0, 0, 'olofild', 'Olo field', 1, '', 0, 179, 0, 'olo', 0, 1, 1),
(218, NULL, 50, 0, 0, 0, 'comment_text', 'Comment Text', 3, 'a:1:{s:5:"nl2br";s:1:"1";}', 1, 180, 0, '', 0, 2, 1),
(219, NULL, 50, 0, 0, 0, 'publish_date', 'Publish Date', 8, '', 1, 181, 0, '', 0, 2, 1),
(220, NULL, 50, 0, 0, 0, 'user_name', 'User Name', 1, '', 1, 182, 0, '', 0, 2, 1),
(221, NULL, 23, 0, 0, 0, 'comments_counter', 'Comments counter', 2, '', 0, 183, 0, '0', 0, 2, 1),
(222, NULL, 50, 0, 0, 0, 'is_moderated', 'Moderated flag', 5, '', 0, 184, 0, '0', 0, 2, 1),
(228, NULL, 58, 0, 0, 0, 'question', 'Question', 1, '', 0, 185, 0, '', 0, 1, 1),
(229, NULL, 58, 0, 0, 0, 'answer', 'Answer', 3, 'a:2:{s:4:"html";s:1:"0";s:5:"nl2br";s:1:"0";}', 0, 186, 0, '', 0, 1, 1),
(230, NULL, 59, 0, 0, 0, 'embed_html', 'Embed code or link', 3, 'a:2:{s:4:"html";s:1:"0";s:5:"nl2br";s:1:"0";}', 0, 187, 0, '', 0, 1, 1),
(231, NULL, 59, 0, 0, 0, 'description', 'Description', 3, 'a:2:{s:4:"html";s:1:"0";s:5:"nl2br";s:1:"0";}', 0, 188, 0, '', 0, 1, 1),
(232, NULL, 60, 0, 0, 0, 'image', 'Image', 11, '', 0, 189, 0, '', 0, 1, 1),
(233, NULL, 60, 0, 0, 0, 'description', 'Description', 3, 'a:2:{s:4:"html";s:1:"0";s:5:"nl2br";s:1:"0";}', 0, 190, 0, '', 0, 1, 1),
(234, NULL, 60, 0, 0, 0, 'year', 'Year', 2, '', 0, 191, 0, '2000', 0, 1, 1),
(235, NULL, 61, 0, 0, 0, 'logo', 'Logo', 11, '', 0, 192, 0, '', 0, 1, 1),
(236, NULL, 61, 0, 0, 0, 'short_description', 'Short Description', 1, '', 0, 193, 0, '', 0, 1, 1),
(237, NULL, 61, 0, 0, 0, 'description', 'Description', 3, 'a:2:{s:4:"html";s:1:"0";s:5:"nl2br";s:1:"0";}', 0, 194, 0, '', 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `fx_filetable`
--

CREATE TABLE IF NOT EXISTS `fx_filetable` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `real_name` char(128) NOT NULL,
  `path` text NOT NULL,
  `type` char(64) DEFAULT NULL,
  `size` int(10) unsigned NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=56 AUTO_INCREMENT=427 ;

--
-- Dumping data for table `fx_filetable`
--

INSERT INTO `fx_filetable` (`id`, `real_name`, `path`, `type`, `size`, `to_delete`) VALUES
(56, 'bg-portfolio.jpg', 'content/bg-portfolio_1.jpg', 'image/jpeg', 235261, 0),
(60, 'bg-portfolio.jpg', 'content/bg-portfolio_3.jpg', 'image/jpeg', 235261, 0),
(105, 'portfolio_photo.jpg', 'content/portfolio_photo_0.jpg', 'image/jpeg', 238783, 0),
(116, 'NOMADE-RIDDIM-VOL.031.jpg', 'content/NOMADE-RIDDIM-VOL.031_0.jpg', 'image/jpeg', 422922, 0),
(164, 'гроза.jpg', 'content/groza_1.jpg', 'image/jpeg', 172197, 0),
(345, 'img01.jpg', 'content/img01_1.jpg', 'image/jpeg', 219265, 0),
(346, 'bg-portfolio.jpg', 'content/bg-portfolio_4.jpg', 'image/jpeg', 235261, 0),
(356, '_logo.png', 'content/logo_3.png', 'image/png', 2627, 0),
(357, 'bg-portfolio.jpg', 'content/bg-portfolio_6.jpg', 'image/jpeg', 235261, 0),
(358, 'bg-company.jpg', 'content/bg-company_2.jpg', 'image/jpeg', 68376, 0),
(361, 'aborigeny_1.jpg', 'content/aborigeny_1.jpg', 'image/jpeg', 102971, 0),
(366, 'foto.jpg', 'content/foto_1.jpg', 'image/jpeg', 47794, 0),
(367, 'jeep-safari-2.jpg', 'content/jeep-safari-2_0.jpg', 'image/jpeg', 110257, 0),
(368, 'jeep-safari-2.jpg', 'content/jeep-safari-2_1.jpg', 'image/jpeg', 110257, 0),
(369, 'Rickus Vermeulen Jeep.jpg', 'content/Rickus_Vermeulen_Jeep_0.jpg', 'image/jpeg', 46915, 0),
(371, 'Rickus Vermeulen Jeep.jpg', 'content/Rickus_Vermeulen_Jeep_2.jpg', 'image/jpeg', 46915, 0),
(373, 'foto.jpg', 'content/foto_2.jpg', 'image/jpeg', 47794, 0),
(374, '_800_600_90_667819585-IMG_2954.jpg', 'content/800_600_90_667819585-IMG_2954_0.jpg', 'image/jpeg', 39395, 0),
(378, 'logo.png', 'content/logo_6.png', 'image/png', 5735, 0),
(379, 'img05.jpg', 'content/img05_1.jpg', 'image/jpeg', 5717, 0),
(383, 'img09.jpg', 'content/img09_3.jpg', 'image/jpeg', 4587, 0),
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
(426, 'awards-2007.png', 'content/awards-2007_0.png', 'image/png', 12875, 0);

-- --------------------------------------------------------

--
-- Table structure for table `fx_group`
--

CREATE TABLE IF NOT EXISTS `fx_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=197 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `fx_group`
--

INSERT INTO `fx_group` (`id`, `name`) VALUES
(1, 'Administrators'),
(2, 'External users'),
(3, 'Authorized by external services');

-- --------------------------------------------------------

--
-- Table structure for table `fx_history`
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
-- Table structure for table `fx_history_item`
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
-- Table structure for table `fx_infoblock`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=210 AUTO_INCREMENT=136 ;

--
-- Dumping data for table `fx_infoblock`
--

INSERT INTO `fx_infoblock` (`id`, `parent_infoblock_id`, `site_id`, `page_id`, `checked`, `name`, `controller`, `action`, `params`, `scope`) VALUES
(3, 0, 1, 2, 1, 'Main menu', 'component_section', 'listing', 'a:1:{s:7:"submenu";s:3:"all";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(12, 0, 1, 0, 1, '', 'layout', 'show', 'a:0:{}', 'a:2:{s:5:"pages";s:3:"all";s:9:"page_type";s:0:"";}'),
(16, 0, 1, 2, 1, 'Index text', 'component_text', 'listing', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(24, 0, 1, 2, 1, '', 'widget_authform', 'show', 'a:0:{}', 'a:1:{s:5:"pages";s:3:"all";}'),
(43, 0, 1, 2, 1, 'Main Header (via breadcrumbs)', 'component_section', 'breadcrumbs', 'a:2:{s:11:"header_only";s:1:"1";s:13:"hide_on_index";b:0;}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:0:"";}'),
(53, 12, 1, 2, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(54, 12, 1, 78, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(55, 54, 1, 97, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(61, 0, 1, 2, 1, 'Text / listing', 'component_text', 'listing', 'a:6:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(67, 0, 15, 0, 1, 'Layout', 'layout', 'show', '', ''),
(69, 0, 15, 1883, 1, 'Main menu', 'component_section', 'listing', 'a:1:{s:7:"submenu";s:3:"all";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(70, 0, 15, 1883, 1, 'Routes', 'component_travel_route', 'listing', 'a:6:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:13:"mount_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(71, 67, 15, 1883, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(72, 0, 15, 1883, 1, 'Starting', 'component_travel_route', 'listing_mirror', 'a:6:{s:5:"limit";s:1:"4";s:15:"show_pagination";b:0;s:7:"sorting";s:10:"start_date";s:11:"sorting_dir";s:4:"desc";s:8:"from_all";s:1:"1";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(74, 0, 15, 1883, 1, 'Page text', 'component_text', 'listing', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:0:"";}'),
(75, 0, 15, 1883, 1, 'Index text', 'component_text', 'listing', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(76, 0, 15, 1883, 1, 'Social networks', 'component_section', 'listing', 'a:1:{s:7:"submenu";s:4:"none";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(77, 0, 15, 1887, 1, 'Menu / About', 'component_section', 'listing', 'a:1:{s:7:"submenu";s:4:"none";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(78, 0, 15, 0, 1, ' Breadcrumbs', 'component_section', 'breadcrumbs', 'a:2:{s:11:"header_only";b:0;s:13:"hide_on_index";s:1:"1";}', 'a:2:{s:5:"pages";s:3:"all";s:9:"page_type";s:0:"";}'),
(79, 0, 15, 1889, 1, 'Our gallery', 'component_gallery', 'listing', 'a:6:{s:5:"limit";s:1:"0";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:12:"publish_date";s:11:"sorting_dir";s:4:"desc";s:11:"parent_type";s:13:"mount_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(80, 67, 15, 1889, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(81, 0, 15, 1889, 1, 'Photo list', 'component_photo', 'listing', 'a:6:{s:5:"limit";s:0:"";s:15:"show_pagination";b:0;s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:7:"gallery";}'),
(82, 0, 15, 1925, 1, 'Blog posts', 'component_blogpost', 'listing', 'a:7:{s:5:"limit";s:1:"2";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:12:"publish_date";s:11:"sorting_dir";s:4:"desc";s:11:"parent_type";s:13:"mount_page_id";s:9:"parent_id";s:0:"";s:19:"field_195_infoblock";s:2:"84";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(83, 0, 15, 1925, 1, 'Tag cloud', 'component_tag', 'listing', 'a:6:{s:5:"limit";s:0:"";s:15:"show_pagination";b:0;s:7:"sorting";s:4:"name";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:13:"mount_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(84, 0, 15, 1925, 1, 'Post tags', 'component_tagpost', 'listing', 'a:7:{s:5:"limit";s:0:"";s:15:"show_pagination";b:0;s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";i:0;b:0;}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:8:"blogpost";}'),
(85, 0, 15, 1883, 1, 'Photo / mirror', 'component_photo', 'listing_mirror', 'a:6:{s:5:"limit";s:1:"3";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:7:"created";s:11:"sorting_dir";s:4:"desc";s:8:"from_all";s:1:"1";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(86, 0, 15, 1925, 1, 'Blog calendar', 'component_blogpost', 'calendar', 'a:0:{}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(87, 0, 15, 1925, 1, 'Separate blog post', 'component_blogpost', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:8:"blogpost";}'),
(99, 71, 15, 1883, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(100, 99, 15, 1883, 1, '', '', '', 'a:0:{}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(104, 0, 15, 1883, 1, 'Text in sidebar', 'component_text', 'listing', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:0:"";}'),
(117, 0, 15, 1925, 1, 'Blog / By tag', 'component_blogpost', 'listing_by_tag', 'a:0:{}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:3:"tag";}'),
(118, 0, 15, 1887, 1, 'Blog / Recent', 'component_blogpost', 'listing_mirror', 'a:6:{s:5:"limit";s:1:"3";s:15:"show_pagination";b:0;s:7:"sorting";s:12:"publish_date";s:11:"sorting_dir";s:4:"desc";s:8:"from_all";s:1:"1";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(121, 0, 15, 1891, 1, 'Image / List', 'component_photo', 'listing', 'a:6:{s:5:"limit";s:2:"10";s:15:"show_pagination";b:0;s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:4:"this";s:9:"page_type";s:0:"";}'),
(124, 0, 15, 1883, 1, 'Navigation / Deep', 'component_section', 'listing', 'a:1:{s:7:"submenu";s:3:"all";}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:0:"";}'),
(126, 0, 15, 2075, 1, 'Navigation / Alter sub', 'component_section', 'listing', 'a:1:{s:7:"submenu";s:4:"none";}', 'a:2:{s:5:"pages";s:11:"descendants";s:9:"page_type";s:0:"";}'),
(127, 0, 15, 1925, 1, 'Comment / List', 'component_comment', 'listing', 'a:6:{s:5:"limit";s:0:"";s:15:"show_pagination";b:0;s:7:"sorting";s:12:"publish_date";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:8:"blogpost";}'),
(128, 0, 15, 1925, 1, 'Comment / add', 'component_comment', 'add', 'a:1:{s:19:"target_infoblock_id";s:3:"127";}', 'a:2:{s:5:"pages";s:8:"children";s:9:"page_type";s:8:"blogpost";}'),
(131, 0, 15, 0, 1, '', 'component_faq', 'listing', 'a:6:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:3:"all";s:9:"page_type";N;}'),
(132, 0, 15, 0, 1, '', 'component_faq', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:3:"all";s:9:"page_type";N;}'),
(133, 0, 15, 0, 1, '', 'component_video', 'listing', 'a:6:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:6:"manual";s:11:"sorting_dir";s:3:"asc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:3:"all";s:9:"page_type";N;}'),
(134, 0, 15, 0, 1, '', 'component_award', 'listing', 'a:6:{s:5:"limit";s:2:"10";s:15:"show_pagination";s:1:"1";s:7:"sorting";s:4:"year";s:11:"sorting_dir";s:4:"desc";s:11:"parent_type";s:15:"current_page_id";s:9:"parent_id";s:0:"";}', 'a:2:{s:5:"pages";s:3:"all";s:9:"page_type";N;}'),
(135, 0, 15, 0, 1, '', 'component_award', 'record', 'a:0:{}', 'a:2:{s:5:"pages";s:3:"all";s:9:"page_type";N;}');

-- --------------------------------------------------------

--
-- Table structure for table `fx_infoblock_visual`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=138 AUTO_INCREMENT=207 ;

--
-- Dumping data for table `fx_infoblock_visual`
--

INSERT INTO `fx_infoblock_visual` (`id`, `infoblock_id`, `layout_id`, `wrapper`, `wrapper_visual`, `template`, `template_visual`, `area`, `priority`) VALUES
(2, 3, 1, '', 'a:2:{i:163;s:13:"Ус-луги";s:9:"separator";s:5:"• !";}', 'layout_supernova.demo_menu', 'a:3:{s:9:"separator";s:3:"•";s:6:"odd_bg";s:4:"#111";s:9:"odd_color";s:4:"#FF0";}', 'header', 1),
(14, 12, 1, '', '', 'layout_supernova.inner', 'a:6:{s:4:"logo";s:3:"356";s:7:"company";s:14:"Floxim Company";s:6:"slogan";s:17:"Almost clear site";s:13:"replace_src_0";s:38:"/controllers/layout/supernova/logo.png";s:8:"developa";s:103:"© 2010 Хороший пример \n<br>\nсайтостроения — \n<a href="#">\nWebSite.ru\n</a>\n";s:13:"banner_slogan";s:124:"«Simplicity of sitebuilder, functionality of CMS,\n                        flexibility of framework. <br>​And it''s free!»";}', '', 4),
(18, 16, 1, 'layout_supernova.wrap_titled', 'a:2:{s:5:"title";s:21:"Don''t miss the chance";s:5:"color";s:7:"#027a02";}', 'component_text.listing', '', 'content', 9),
(26, 24, 1, 'layout_supernova.wrap_titled', 'a:1:{s:5:"title";s:16:"Have an account?";}', 'widget_authform.show', 'a:3:{s:15:"replace_value_0";s:10:"Войти";s:12:"login_button";s:8:"Sign in!";s:17:"placeholder_login";s:5:"email";}', 'sidebar', 3),
(45, 43, 1, '', '', 'component_section.breadcrumbs', 'a:1:{s:9:"separator";s:3:" / ";}', 'content', 1),
(55, 53, 1, '', '', 'layout_supernova.index', '', '', 1),
(56, 54, 1, '', '', 'layout_supernova.index', '', '', 2),
(57, 55, 1, '', '', 'layout_supernova.inner', '', '', 3),
(59, 57, 1, 'layout_supernova.wrap_titled', '', 'component_page.listing', '', 'content', 8),
(98, 54, 8, '', '', 'layout_demo8.index', '', '', 0),
(99, 3, 8, '', '', 'layout_demo8.demo_menu', '', 'header', 1),
(101, 43, 8, '', '', 'component_section.breadcrumbs', '', 'content', 1),
(103, 24, 8, '', '', 'widget_authform.show', '', 'sidebar', 2),
(107, 12, 8, '', '', 'layout_demo8.index', 'a:0:{}', '', 0),
(108, 53, 8, '', '', 'layout_demo8.index', 'a:3:{s:9:"c1_header";s:31:"Новости | события";s:9:"logo_text";s:27:"Think<span>Different</span>";s:6:"slogan";s:37:"лучшие утюги россии!";}', '', 0),
(109, 16, 8, '', '', 'component_text.listing', '', 'content', 9),
(111, 55, 8, '', '', 'layout_demo8.index', '', '', 0),
(116, 53, 9, '', '', 'layout_dummy.2cols', '', '', 0),
(117, 3, 9, '', '', 'component_section.listing', '', 'header', 1),
(118, 16, 9, '', '', 'component_text.listing', '', 'content', 9),
(120, 24, 9, '', '', 'widget_authform.show', '', 'left', 2),
(122, 12, 9, '', '', 'layout_dummy.2cols', '', '', 0),
(123, 43, 9, '', '', 'component_section.breadcrumbs', '', 'content', 1),
(125, 61, 8, '', '', 'auto.auto', '', 'banner', 0),
(126, 61, 1, '', '', 'component_text.listing', '', '', 0),
(134, 67, 1, '', '', 'layout_supernova.index', '', '', 0),
(137, 67, 10, '', '', 'layout_jeeptravel.page', 'a:31:{s:18:"page_bg_color_1895";s:0:"";s:18:"page_bg_color_1888";s:7:"#E9A502";s:18:"page_bg_image_1888";s:52:"/controllers/layout/jeeptravel/images/bg-company.jpg";s:18:"page_bg_image_1895";s:0:"";s:18:"page_bg_color_1887";s:4:"#000";s:18:"page_bg_image_1887";s:3:"358";s:5:"phone";s:18:"+7 (905) 561 99 75";s:4:"mail";s:19:"info@jeeptravel.loc";s:18:"page_bg_color_1889";s:7:"#c7c1c7";s:18:"page_bg_image_1889";s:3:"389";s:18:"page_bg_color_1890";s:7:"#E9A502";s:18:"page_bg_image_1890";s:2:"60";s:18:"page_bg_color_1925";s:7:"#7a7a7a";s:18:"page_bg_image_1925";s:0:"";s:18:"page_bg_color_1926";s:7:"#500070";s:18:"page_bg_image_1926";s:3:"326";s:4:"logo";s:3:"378";s:18:"page_bg_image_1891";s:0:"";s:18:"page_bg_image_1968";s:0:"";s:14:"contacts_label";s:8:"Call us:";s:4:"copy";s:93:"© JeepTravel, 2013<br>&nbsp; &nbsp; Photo by: <a href="http://leecannon.com/">Lee Cannon</a>";s:18:"page_bg_image_1883";s:0:"";s:18:"page_bg_image_1996";s:0:"";s:18:"page_bg_image_1884";s:0:"";s:18:"page_bg_color_1883";s:7:"#fafafa";s:18:"page_bg_color_1884";s:7:"#000000";s:18:"page_bg_image_1916";s:0:"";s:18:"page_bg_image_1902";s:0:"";s:18:"page_bg_image_2033";s:0:"";s:5:"email";s:11:"info@jt.com";s:18:"page_bg_color_1996";s:7:"#000000";}', '', 0),
(139, 69, 10, '', '', 'layout_jeeptravel.top_menu', '', 'header', 1),
(140, 70, 10, '', '', 'layout_jeeptravel.index_slider', 'a:15:{s:9:"info_1891";s:510:"<dt><strong>Difficulty:</strong> easy<br>\n <strong>Cities:</strong> <span data-redactor="verified" style="color: rgb(217, 150, 148);">Gada</span>, <span data-redactor="verified" style="color: rgb(255, 255, 0);">B<strong>a</strong>lle</span>, <a href="https://google.com"><strong><span data-redactor="verified" style="color: rgb(242, 195, 20);">Binji</span></strong></a>, Wurno<br>\n <strong>Villages:</strong> Kaita, Rimi<span data-redactor="verified" style="color: rgb(84, 141, 212);"><br>​</span><br>\n </dt>";s:14:"more_text_1891";s:12:"Tell me more";s:16:"action_text_1891";s:14:"Gonna b there!";s:9:"date_1891";s:9:"May 12-15";s:11:"header_1891";s:16:"Summer Rally<br>";s:13:"bg_photo_1892";s:3:"357";s:11:"header_1892";s:34:"It''s going to be<br>​Legen-dary!";s:16:"action_text_1892";s:15:"Yes, I''m crazy!";s:14:"more_text_1892";s:12:"Tell me more";s:9:"date_1892";s:16:"January 5 – 19";s:9:"info_1892";s:251:"<dl>\n                                            <dt>Difficulty: </dt>extremely difficult<br>​Period: 2 weeks<br>Cities<strong>: Paris, Dakar</strong><dd></dd><dt>A chance to survive:</dt><dd>~23.5%</dd>\n                                        </dl>";s:13:"bg_photo_1891";s:3:"345";s:15:"action_url_1891";s:18:"http://google.com/";s:15:"action_url_1892";s:0:"";s:13:"bg_photo_2085";s:3:"422";}', 'content', 8),
(141, 71, 10, '', '', 'layout_jeeptravel.index', '', '', 0),
(142, 72, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:8:"Upcoming";}', 'layout_jeeptravel.index_link_list', '', 'index_center', 1),
(144, 74, 10, '', '', 'component_text.listing', '', 'content', 7),
(145, 75, 10, '', '', 'component_text.listing', '', 'index_center', 2),
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
(158, 80, 10, '', '', 'layout_jeeptravel.full', '', '', 0),
(159, 81, 10, '', '', 'component_photo.listing_slider', '', 'content', 6),
(160, 82, 10, '', '', 'component_blogpost.listing', 'a:8:{s:13:"bg_photo_1976";s:0:"";s:10:"tags_label";s:10:"Post tags:";s:12:"posted_under";s:11:"Entry tags:";s:9:"blog_name";s:21:"Jeep Travel blog feed";s:16:"blog_description";s:27:"Our blog is so interesting!";s:16:"rss_posted_under";s:11:"Entry tags:";s:20:"pagination_separator";s:4:"· ";s:9:"next_page";s:2:"»";}', 'content', 2),
(161, 83, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:4:"Tags";}', 'component_tag.tag_list', '', 'sidebar', 2),
(162, 84, 10, '', '', 'component_tagpost.listing', '', 'content', 4),
(163, 85, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:6:"Photos";}', 'layout_jeeptravel.index_photo_anounces', 'a:2:{s:10:"image_1916";s:0:"";s:10:"image_1913";s:0:"";}', 'index_left', 1),
(164, 86, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:7:"Archive";}', 'component_blogpost.calendar', 'a:1:{s:6:"expand";s:4:"true";}', 'sidebar', 3),
(165, 87, 10, '', '', 'auto.auto', 'a:1:{s:10:"tags_label";s:16:"Entry tagged by:";}', 'content', 3),
(170, 99, 10, '', '', 'layout_jeeptravel.index', '', '', 0),
(171, 100, 10, '', '', 'layout_jeeptravel.index', '', '', 0),
(175, 104, 10, '', '', 'auto.auto', '', 'sidebar', 1),
(188, 117, 10, '', '', 'component_blogpost.listing', '', 'content', 9),
(189, 118, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:19:"Recent blog entries";}', 'component_page.listing', '', 'sidebar', 5),
(192, 121, 10, '', '', 'component_photo', '', 'content', 10),
(195, 124, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:9:"Deep menu";}', 'component_section.listing_deep', '', 'sidebar', 6),
(197, 126, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:8:"Alt menu";}', 'component_section.listing_deep', '', 'content', 11),
(198, 127, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:8:"Comments";}', 'component_comment.listing', '', 'content', 12),
(199, 128, 10, 'layout_jeeptravel.block_titled', 'a:1:{s:6:"header";s:11:"Add Comment";}', 'component_comment.add', '', 'content', 13),
(202, 131, 10, '', '', 'component_faq.listing', '', 'content', 14),
(203, 132, 10, '', '', 'component_faq.record', '', 'content', 15),
(204, 133, 10, '', '', 'component_video.listing', '', 'content', 16),
(205, 134, 10, '', '', 'component_award.listing', '', 'content', 17),
(206, 135, 10, '', '', 'component_award.record', '', 'content', 18);

-- --------------------------------------------------------

--
-- Table structure for table `fx_lang_string`
--

CREATE TABLE IF NOT EXISTS `fx_lang_string` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dict` varchar(45) DEFAULT NULL,
  `string` text,
  `lang_en` text,
  `lang_ru` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=452 ;

--
-- Dumping data for table `fx_lang_string`
--

INSERT INTO `fx_lang_string` (`id`, `dict`, `string`, `lang_en`, `lang_ru`) VALUES
(1, 'component_section', 'Show path to the current page', 'Show path to the current page', 'Отображает путь до текущей страницы в структуре сайта'),
(2, 'component_section', 'Bread crumbs', 'Bread crumbs', 'Хлебные крошки'),
(3, 'component_section', 'Subsection', 'Subsection', 'Подраздел'),
(4, 'component_section', 'Show for all items', 'Show for all items', 'Показывать у всех'),
(5, 'component_section', 'Show for the active item', 'Show for the active item', 'Показывать у активного'),
(6, 'component_section', 'Don''t show', 'Don''t show', 'Не показывать'),
(7, 'component_section', 'Subsections', 'Subsections', 'Подразделы'),
(8, 'component_section', 'Navigation', 'Navigation', 'Меню'),
(9, 'system', 'File is not writable', 'File is not writable', 'Не могу произвести запись в файл'),
(10, 'controller_component', 'Show entries by filter', 'Show entries by filter', 'Выводит записи по произвольному фильтру'),
(11, 'controller_component', 'Show entries from the specified section', 'Show entries from the specified section', 'Выводит список записей из указанного раздела'),
(12, 'controller_component', 'List', 'List', 'Список'),
(13, 'controller_component', 'Show single entry', 'Show single entry', 'Выводит отдельную запись'),
(14, 'controller_component', 'Entry', 'Entry', 'Запись'),
(15, 'controller_component', 'From specified section', 'From specified section', 'Указать раздел явно'),
(16, 'controller_component', 'From all sections', 'From all sections', 'Из любого раздела'),
(17, 'controller_component', 'Choose section', 'Choose section', 'Выбрать родителя'),
(18, 'controller_component', 'Random', 'Random', 'Произвольный'),
(19, 'controller_component', 'The infoblock owner section', 'The infoblock owner section', 'Страница, куда прицеплен инфоблок'),
(20, 'controller_component', 'Current page', 'Current page', 'Текущая страница'),
(21, 'controller_component', 'Parent', 'Parent', 'Родитель'),
(22, 'controller_component', 'Ascending', 'Ascending', 'По возрастанию'),
(23, 'controller_component', 'Descending', 'Descending', 'По убыванию'),
(24, 'controller_component', 'Order', 'Order', 'Порядок'),
(25, 'controller_component', 'Sorting', 'Sorting', 'Сортировка'),
(26, 'controller_component', 'Manual', 'Manual', 'Ручная'),
(27, 'controller_component', 'Created', 'Created', 'Дата создания'),
(28, 'controller_component', 'Show pagination?', 'Show pagination?', 'Разбивать на страницы?'),
(29, 'controller_component', 'How many entries to display', 'How many entries to display', 'Сколько выводить'),
(30, 'controller_layout', 'Sign in', 'Sign in', 'Вход'),
(31, 'system', 'Add infoblock', 'Add infoblock', 'Добавить инфоблок'),
(32, 'system', 'Link', 'Link', 'Ссылка'),
(33, 'system', 'Picture', 'Picture', 'Картинка'),
(34, 'system', 'Elements', 'Elements', 'Элементы'),
(35, 'system', 'Classifier', 'Classifier', 'Классификатор'),
(36, 'system', 'Manually', 'Manually', 'Вручную'),
(37, 'system', 'Source', 'Source', 'Источник'),
(38, 'system', 'Show like', 'Show like', 'Показывать как'),
(39, 'system', 'Current file:', 'Current file:', 'Текущий файл:'),
(40, 'system', 'replace newline to br', 'replace newline to br', 'заменять перенос строки на br'),
(41, 'system', 'allow HTML tags', 'allow HTML tags', 'разрешить html-теги'),
(42, 'system', 'Related type', 'Related type', 'Связанный тип'),
(43, 'system', 'Bind value to the parent', 'Bind value to the parent', 'Привязать значение к родителю'),
(44, 'system', 'Key name for the property', 'Key name for the property', 'Ключ для свойства'),
(45, 'system', 'Links to', 'Links to', 'Куда ссылается'),
(46, 'system', 'Enter the name of the site', 'Enter the name of the site', 'Укажите название сайта'),
(47, 'system', 'Priority', 'Priority', 'Приоритет'),
(48, 'system', 'Created', 'Created', 'Дата создания'),
(49, 'system', 'This keyword is used by the component', 'This keyword is used by the component', 'Такой keyword уже используется компоненте'),
(50, 'system', 'Keyword can only contain letters, numbers, symbols, "hyphen" and "underscore"', 'Keyword can only contain letters, numbers, symbols, "hyphen" and "underscore"', 'Keyword может содержать только буквы, цифры, символы "дефис" и "подчеркивание"'),
(51, 'system', 'Specify component keyword', 'Specify component keyword', 'Укажите keyword компонента'),
(52, 'system', 'Component name can not be empty', 'Component name can not be empty', 'Название компонента не может быть пустым'),
(53, 'system', 'Specify field description', 'Specify field description', 'Укажите описание поля'),
(54, 'system', 'This field already exists', 'This field already exists', 'Такое поле уже существует'),
(55, 'system', 'This field is reserved', 'This field is reserved', 'Данное поле зарезервировано'),
(56, 'system', 'Field name can contain only letters, numbers, and the underscore character', 'Field name can contain only letters, numbers, and the underscore character', 'Имя поля может содержать только латинские буквы, цифры и знак подчеркивания'),
(57, 'system', 'name', 'name', 'name'),
(58, 'system', 'Specify field name', 'Specify field name', 'Укажите название поля'),
(59, 'system', 'This keyword is used by widget', 'This keyword is used by widget', 'Такой keyword уже используется в виджете'),
(60, 'system', 'Keyword can contain only letters and numbers', 'Keyword can contain only letters and numbers', 'Keyword может сожержать только буквы и цифры'),
(61, 'system', 'Enter the keyword of widget', 'Enter the keyword of widget', 'Укажите keyword виджета'),
(62, 'system', 'Specify the name of the widget', 'Specify the name of the widget', 'Укажите название виджета'),
(63, 'system', 'You are about to install:', 'You are about to install:', 'Вы собираетесь установить:'),
(64, 'system', 'Preview', 'Preview', 'Превью'),
(65, 'system', 'Layout', 'Layout', 'Макет'),
(66, 'system', 'Show when the site is off', 'Show when the site is off', 'Показывать, когда сайт выключен'),
(67, 'system', 'Cover Page', 'Cover Page', 'Титульная страница'),
(68, 'system', 'Prevent indexing', 'Prevent indexing', 'Запретить индексирование'),
(69, 'system', 'The contents of robots.txt', 'The contents of robots.txt', 'Содержимое robots.txt'),
(70, 'system', 'Site language', 'Site language', 'Язык сайта'),
(71, 'system', 'Aliases', 'Aliases', 'Зеркала'),
(72, 'system', 'Domain', 'Domain', 'Домен'),
(73, 'system', 'Site name', 'Site name', 'Название сайта'),
(74, 'system', 'Enabled', 'Enabled', 'Включен'),
(75, 'system', 'System', 'System', 'Системные'),
(76, 'system', 'Main', 'Main', 'Основные'),
(77, 'system', 'any', 'any', 'любое'),
(78, 'system', 'vertical', 'vertical', 'вертикальное'),
(79, 'system', 'Menu', 'Menu', 'Меню'),
(80, 'system', 'Direction', 'Direction', 'Направление'),
(81, 'system', 'Required', 'Required', 'Обязательный'),
(82, 'system', 'Block', 'Block', 'Блок'),
(83, 'system', 'Blocks', 'Blocks', 'Блоки'),
(84, 'system', 'Sites', 'Sites', 'Сайты'),
(85, 'system', 'Design', 'Design', 'Дизайн'),
(86, 'system', 'Settings', 'Settings', 'Настройки'),
(87, 'system', 'Site map', 'Site map', 'Карта сайта'),
(88, 'system', 'Site not found', 'Site not found', 'Сайт не найден'),
(89, 'system', 'Page not found', 'Page not found', 'Страница не найдена'),
(90, 'system', 'Error creating a temporary file', 'Error creating a temporary file', 'Ошибка при создании временного файла'),
(91, 'system', 'Create a new site', 'Create a new site', 'Добавление нового сайта'),
(92, 'system', 'Add new site', 'Add new site', 'Новый сайт'),
(93, 'system', 'New', 'New', 'Новый'),
(94, 'system', 'Export', 'Export', 'Экспорт'),
(95, 'system', 'for mobile devices', 'for mobile devices', 'для мобильный устройств'),
(96, 'system', 'Language:', 'Language:', 'Язык:'),
(97, 'system', 'Description', 'Description', 'Описание'),
(98, 'system', 'Group', 'Group', 'Группа'),
(99, 'system', 'Another group', 'Another group', 'Другая группа'),
(100, 'system', 'Name of entity created by the component', 'Name of entity created by the component', 'Название сущности создаваемой компонентом'),
(101, 'system', 'Component name', 'Component name', 'Название компонента'),
(102, 'system', 'Keyword:', 'Keyword:', 'Ключевое слово:'),
(103, 'system', '--no--', '--no--', '--нет--'),
(104, 'system', 'Parent component', 'Parent component', 'Компонент-родитель'),
(105, 'system', 'default', 'default', 'по умолчанию'),
(106, 'system', 'Components', 'Components', 'Компоненты'),
(107, 'system', 'Widgets', 'Widgets', 'Виджеты'),
(108, 'system', 'Keyword', 'Keyword', 'Ключевое слово'),
(109, 'system', 'File', 'File', 'Файл'),
(110, 'system', 'Fields', 'Fields', 'Поля'),
(111, 'system', 'Install from FloximStore', 'Install from FloximStore', 'установить с FloximStore'),
(112, 'system', 'import', 'import', 'импортировать'),
(113, 'system', 'Layout of inside page', 'Layout of inside page', 'Макет внутренней страницы'),
(114, 'system', 'Cover Page Layout', 'Cover Page Layout', 'Макет титульной страницы'),
(115, 'system', 'Sign out', 'Sign out', 'Выход'),
(116, 'system', 'Apply the current', 'Apply the current', 'Применить текущий'),
(117, 'system', 'Colors', 'Colors', 'Расцветка'),
(118, 'system', 'Layout not found', 'Layout not found', 'Макет не найден'),
(119, 'system', 'Enter the layout name', 'Enter the layout name', 'Укажите название макета'),
(120, 'system', 'Layout name', 'Layout name', 'Название макета'),
(121, 'system', 'Export to file', 'Export to file', 'Экспортировать в файл'),
(122, 'system', 'No files', 'No files', 'Нет файлов'),
(123, 'system', 'Layouts', 'Layouts', 'Макеты'),
(124, 'system', 'Unable to create directory', 'Unable to create directory', 'Не удалось создать каталог'),
(125, 'system', 'Adding a layout design', 'Adding a layout design', 'Добавление макета дизайна'),
(126, 'system', 'Import layout design', 'Import layout design', 'Импорт макета дизайна'),
(127, 'system', 'empty', 'empty', 'пустой'),
(128, 'system', 'Used on', 'Used on', 'Используется на сайтах'),
(129, 'system', 'Repeated', 'Repeated', 'Повторено'),
(130, 'system', 'Cancelled', 'Cancelled', 'Отменено'),
(131, 'system', 'Header sent', 'Header sent', 'Посылаемый заголовок'),
(132, 'system', 'New url', 'New url', 'Новый url'),
(133, 'system', 'Old url', 'Old url', 'Старый url'),
(134, 'system', 'Changing the transfer rule', 'Changing the transfer rule', 'Изменение правила переадресации'),
(135, 'system', 'Adding forwarding rules', 'Adding forwarding rules', 'Добавление правила переадресации'),
(136, 'system', 'Header', 'Header', 'Заголовок'),
(137, 'system', 'Layouts can not be deleted', 'Layouts can not be deleted', 'Удалять лейауты нельзя!'),
(138, 'system', 'Unbind/Hide', 'Unbind/Hide', 'Отвязать/скрыть'),
(139, 'system', 'Delete', 'Delete', 'Удалить'),
(140, 'system', 'The infoblock contains some content', 'The infoblock contains some content', 'Инфоблок содержит контент'),
(141, 'system', 'items. What should we do with them?', 'items. What should we do with them?', ' шт. Что с ним делать?'),
(142, 'system', 'I am REALLY shure', 'I am REALLY shure', 'Будет удалено куча всего, я понимаю последствия'),
(143, 'system', 'Block wrapper template', 'Block wrapper template', 'Оформление блока'),
(144, 'system', 'Template', 'Template', 'Шаблон'),
(145, 'system', 'Auto select', 'Auto select', 'Автовыбор'),
(146, 'system', 'With no wrapper', 'With no wrapper', 'Без оформления'),
(147, 'system', 'On the page and it''s children', 'On the page and it''s children', 'На этой и на вложенных'),
(148, 'system', 'Only on children', 'Only on children', 'Только на вложенных страницах'),
(149, 'system', 'Only on the page', 'Only on the page', 'Только на этой странице'),
(150, 'system', 'Page', 'Page', 'Страница'),
(151, 'system', 'On all pages', 'On all pages', 'На всех страницах'),
(152, 'system', 'Remove this rule', 'Remove this rule', 'Удалить это правило'),
(153, 'system', 'Create a new rule', 'Create a new rule', 'Создать новое правило'),
(154, 'system', 'Update', 'Update', 'Обновить'),
(155, 'system', 'Create', 'Create', 'Создать'),
(156, 'system', 'Page layout', 'Page layout', 'Выбор шаблона страницы'),
(157, 'system', 'Infoblock settings', 'Infoblock settings', 'Настройка инфоблока'),
(158, 'system', 'Where to show', 'Where to show', 'Где показывать'),
(159, 'system', 'How to show', 'How to show', 'Как показывать'),
(160, 'system', 'Block name', 'Block name', 'Название блока'),
(161, 'system', 'What to show', 'What to show', 'Что показывать'),
(162, 'system', 'Widget', 'Widget', 'Виджет'),
(163, 'system', 'Next', 'Next', 'Продолжить'),
(164, 'system', 'Install from Store', 'Install from Store', 'Установить с Store'),
(165, 'system', 'Adding infoblock', 'Adding infoblock', 'Добавление инфоблока'),
(166, 'system', 'Type', 'Type', 'Тип'),
(167, 'system', 'Action', 'Action', 'Действие'),
(168, 'system', 'Name', 'Name', 'Название'),
(169, 'system', 'Component', 'Component', 'Компонент'),
(170, 'system', 'Single entry', 'Single entry', 'Отдельный объект'),
(171, 'system', 'Mirror', 'Mirror', 'Mirror'),
(172, 'system', 'List', 'List', 'Список'),
(173, 'system', 'Change password', 'Change password', 'Сменить пароль'),
(174, 'system', 'Import', 'Import', 'Импорт'),
(175, 'system', 'Download from FloximStore', 'Download from FloximStore', 'Скачать с FloximStore'),
(176, 'system', 'Download file', 'Download file', 'Cкачать файл'),
(177, 'system', 'Upload file', 'Upload file', 'Закачать файл'),
(178, 'system', 'Permissions', 'Permissions', 'Права'),
(179, 'system', 'Select parent block', 'Select parent block', 'выделить блок'),
(180, 'system', 'Site layout', 'Site layout', 'Сменить макет сайта'),
(181, 'system', 'Page design', 'Page design', 'Дизайн страницы'),
(182, 'system', 'Development', 'Development', 'Разработка'),
(183, 'system', 'Administration', 'Administration', 'Администрирование'),
(184, 'system', 'Tools', 'Tools', 'Инструменты'),
(185, 'system', 'Users', 'Users', 'Пользователи'),
(186, 'system', 'Site', 'Site', 'Сайт'),
(187, 'system', 'Management', 'Management', 'Управление'),
(188, 'system', 'Default value', 'Default value', 'Значение по умолчанию'),
(189, 'system', 'Field can be used for searching', 'Field can be used for searching', 'Возможен поиск по полю'),
(190, 'system', 'Required', 'Required', 'Обязательно для заполнения'),
(191, 'system', 'Field not found', 'Field not found', 'Поле не найдено'),
(192, 'system', 'Field is available for', 'Field is available for', 'Поле доступно'),
(193, 'system', 'anybody', 'anybody', 'всем'),
(194, 'system', 'admins only', 'admins only', 'только админам'),
(195, 'system', 'nobody', 'nobody', 'никому'),
(196, 'system', 'Field type', 'Field type', 'Тип поля'),
(197, 'system', 'Field keyword', 'Field keyword', 'Название на латинице'),
(198, 'system', 'Name', 'Name', 'Имя'),
(199, 'system', 'New widget', 'New widget', 'Новый виджет'),
(200, 'system', 'Widget size', 'Widget size', 'Размер виджета'),
(201, 'system', 'Mini Block', 'Mini Block', 'Миниблок'),
(202, 'system', 'Narrow', 'Narrow', 'Узкий'),
(203, 'system', 'Wide', 'Wide', 'Широкий'),
(204, 'system', 'Narrowly wide', 'Narrowly wide', 'Узко-широкий'),
(205, 'system', 'Icon to be used', 'Icon to be used', 'Используемая иконка'),
(206, 'system', 'This icon is used by default', 'This icon is used by default', 'эта иконка используется по умолчанию'),
(207, 'system', 'This icon is icon.png file in the directory widget', 'This icon is icon.png file in the directory widget', 'эта иконка находится в файле icon.png в директории виджета'),
(208, 'system', 'This icon is selected from a list of icons', 'This icon is selected from a list of icons', 'эта иконка выбрана из списка иконок'),
(209, 'system', 'Enter the widget name', 'Enter the widget name', 'Введите название виджета'),
(210, 'system', 'Specify the name', 'Specify the name', 'Укажите название'),
(211, 'system', 'Edit User Group', 'Edit User Group', 'Изменение группы пользователей'),
(212, 'system', 'Add User Group', 'Add User Group', 'Добавление группы пользователей'),
(213, 'system', 'New Group', 'New Group', 'Новая группа'),
(214, 'system', 'Assign the right director', 'Assign the right director', 'Присвоить право директора'),
(215, 'system', 'The first version has just the right director', 'The first version has just the right director', 'В первой версии есть только право Директор'),
(216, 'system', 'There are no rules', 'There are no rules', 'Нет никак прав'),
(217, 'system', 'Permission', 'Permission', 'Право'),
(218, 'system', 'Content edit', 'Content edit', 'Редактирование контента'),
(219, 'system', 'Avatar', 'Avatar', 'Аватар'),
(220, 'system', 'Nick', 'Nick', 'Имя на сайте'),
(221, 'system', 'Confirm password', 'Confirm password', 'Пароль еще раз'),
(222, 'system', 'Password', 'Password', 'Пароль'),
(223, 'system', 'Login', 'Login', 'Логин'),
(224, 'system', 'Groups', 'Groups', 'Группы'),
(225, 'system', 'Passwords do not match', 'Passwords do not match', 'Пароли не совпадают'),
(226, 'system', 'Password can''t be empty', 'Password can''t be empty', 'Пароль не может быть пустым'),
(227, 'system', 'Fill in with the login', 'Fill in with the login', 'Заполните поле с логином'),
(228, 'system', 'Please select at least one group', 'Please select at least one group', 'Выберите хотя бы одну группу'),
(229, 'system', 'Add User', 'Add User', 'Добавление пользователя'),
(230, 'system', 'publications in', 'publications in', 'публикации в'),
(231, 'system', 'Select objects', 'Select objects', 'Выберите объекты'),
(232, 'system', 'publish:', 'publish:', 'опубликовал:'),
(234, 'system', 'friends, send message', 'friends, send message', 'друзья, отправить сообщение'),
(235, 'system', 'registred:', 'registred:', 'зарегистрирован:'),
(236, 'system', 'Activity', 'Activity', 'Активность'),
(237, 'system', 'Registration data', 'Registration data', 'Регистрационные данные'),
(238, 'system', 'Rights management', 'Rights management', 'Управление правами'),
(239, 'system', 'Password and verification do not match.', 'Password and verification do not match.', 'Пароль и подтверждение не совпадают.'),
(240, 'system', 'Password is too short. The minimum length of the password', 'Password is too short. The minimum length of the password', 'Пароль слишком короткий. Минимальная длина пароля'),
(241, 'system', 'Enter the password', 'Enter the password', 'Введите пароль.'),
(242, 'system', 'This login is already in use', 'This login is already in use', 'Такой логин уже используется'),
(243, 'system', 'Error: can not find information block with users.', 'Error: can not find information block with users.', 'Ошибка: не найден инфоблок с пользователями.'),
(244, 'system', 'Self-registration is prohibited.', 'Self-registration is prohibited.', 'Самостоятельная регистрация запрещена.'),
(245, 'system', 'Can not find <? ​​Php class file', 'Can not find <? ​​Php class file', 'Не могу найти <?php в файле класса'),
(246, 'system', 'Syntax error in the component class', 'Syntax error in the component class', 'Синтаксическая ошибка в классе компонента'),
(247, 'system', 'Syntax error in function', 'Syntax error in function', 'Синтаксическая ошибка в функции'),
(248, 'system', 'Profile', 'Profile', 'Профиль'),
(249, 'system', 'User not found', 'User not found', 'Пользователь не найден'),
(250, 'system', 'List not found', 'List not found', 'Список не найден'),
(251, 'system', 'Site not found', 'Site not found', 'Сайт не найден'),
(252, 'system', 'Widget not found', 'Widget not found', 'Виджет не найден'),
(253, 'system', 'Component not found', 'Component not found', 'Компонент не найден'),
(254, 'system', 'Modules', 'Modules', 'Модули'),
(255, 'system', 'All sites', 'All sites', 'Список сайтов'),
(256, 'system', 'Unable to connect to server', 'Unable to connect to server', 'Не удалось соединиться с сервером'),
(257, 'system', 'Override the settings in the class', 'Override the settings in the class', 'Переопределите метод settings в своем классе'),
(258, 'system', 'Configuring the', 'Configuring the', 'Настройка модуля'),
(259, 'system', 'Login', 'Login', 'Вход'),
(260, 'system', 'Saved', 'Saved', 'Сохранено'),
(261, 'system', 'Could not open file!', 'Could not open file!', 'Не получилось открыть файл!'),
(262, 'system', 'Error when downloading a file', 'Error when downloading a file', 'Ошибка при закачке файла'),
(263, 'system', 'Enter the file', 'Enter the file', 'Укажите файл'),
(264, 'system', 'Not all fields are transferred!', 'Not all fields are transferred!', 'Не все поля переданы!'),
(265, 'system', 'Error Deleting File', 'Error Deleting File', 'Ошибка при удалении файла'),
(266, 'system', 'Error when changing the name', 'Error when changing the name', 'Ошибка при изменении имени'),
(267, 'system', 'Error when permission', 'Error when permission', 'Ошибка при изменении прав доступа'),
(268, 'system', 'Set permissions', 'Set permissions', 'Задайте права доступа'),
(269, 'system', 'Enter the name', 'Enter the name', 'Укажите имя'),
(270, 'system', 'Edit the file/directory', 'Edit the file/directory', 'Правка файла/директории'),
(271, 'system', 'View the contents', 'View the contents', 'Просмотр содержимого'),
(272, 'system', 'Execution', 'Execution', 'Выполнение'),
(273, 'system', 'Writing', 'Writing', 'Запись'),
(274, 'system', 'Reading', 'Reading', 'Чтение'),
(275, 'system', 'Permissions for the rest', 'Permissions for the rest', 'Права для остальных'),
(276, 'system', 'Permissions for the group owner', 'Permissions for the group owner', 'Права для группы-владельца'),
(277, 'system', 'Permissions for the user owner', 'Permissions for the user owner', 'Права для пользователя-владельца'),
(278, 'system', 'Do not pass the file name!', 'Do not pass the file name!', 'Не передано имя файла!'),
(279, 'system', 'An error occurred while creating the file/directory', 'An error occurred while creating the file/directory', 'Ошибка при создании файла/каталога'),
(280, 'system', 'Not all fields are transferred', 'Not all fields are transferred', 'Не все поля переданы'),
(281, 'system', 'Enter the name of the file/directory', 'Enter the name of the file/directory', 'Укажите имя файла/каталога'),
(282, 'system', 'Create a new file/directory', 'Create a new file/directory', 'Создание нового файла/директории'),
(283, 'system', 'Name of file/directory', 'Name of file/directory', 'Имя файла/каталога'),
(284, 'system', 'What we create', 'What we create', 'Что создаём'),
(285, 'system', 'directory', 'directory', 'каталог'),
(286, 'system', 'Writing to file failed', 'Writing to file failed', 'Не удалась запись в файл'),
(287, 'system', 'Reading of file failed', 'Reading of file failed', 'Не удалось прочитать файл!'),
(288, 'system', 'Gb', 'Gb', 'Гб'),
(289, 'system', 'Mb', 'Mb', 'Мб'),
(290, 'system', 'Kb', 'Kb', 'Кб'),
(291, 'system', 'byte', 'byte', 'байт'),
(292, 'system', 'Parent directory', 'Parent directory', 'родительский каталог'),
(293, 'system', 'Size', 'Size', 'Размер'),
(294, 'system', 'File-manager', 'File-manager', 'Файл-менеджер'),
(295, 'system', 'Invalid action', 'Invalid action', 'Неверное действие'),
(296, 'system', 'Invalid user id', 'Invalid user id', 'Неверный id пользователя'),
(297, 'system', 'Invalid code', 'Invalid code', 'Неверный код'),
(298, 'system', 'Your account has been created.', 'Your account has been created.', 'Ваш аккаунт активирован.'),
(299, 'system', 'Your e-mail address is confirmed. Wait for the verification and account activation by the administrator.', 'Your e-mail address is confirmed. Wait for the verification and account activation by the administrator.', 'Ваш адрес e-mail подтвержден. Дождитесь проверки и активации учетной записи администратором.'),
(300, 'system', 'Invalid confirmation code registration.', 'Invalid confirmation code registration.', 'Неверный код подтверждения регистрации.'),
(301, 'system', 'Not passed the verification code registration.', 'Not passed the verification code registration.', 'Не передан код подтверждения регистрации.'),
(302, 'system', 'Action after the first authorization', 'Action after the first authorization', 'Действие после первой авторизации'),
(303, 'system', 'Group, which gets the user after login', 'Group, which gets the user after login', 'Группы, куда попадет пользователь после авторизации'),
(304, 'system', 'Facebook data', 'Facebook data', 'Данные facebook'),
(305, 'system', 'User fields', 'User fields', 'Поля пользователя'),
(306, 'system', 'Complies fields', 'Complies fields', 'Соответсвие полей'),
(307, 'system', 'enable authentication with facebook', 'enable authentication with facebook', 'включить авторизацию через facebook'),
(308, 'system', 'Twitter data', 'Twitter data', 'Данные twiiter'),
(309, 'system', 'enable authentication with twitter', 'enable authentication with twitter', 'включить авторизацию через твиттер'),
(310, 'system', 'The minimum length of the password must be an integer.', 'The minimum length of the password must be an integer.', 'Минимальная длина пароля должна быть целым числом.'),
(311, 'system', 'The time during which the user is online, can be an integer greater than 0.', 'The time during which the user is online, can be an integer greater than 0.', 'Время, в течение которого пользователь считается online, должно быть целым числом больше 0.'),
(312, 'system', 'nvalid address format of e-mail.', 'nvalid address format of e-mail.', 'Неверный формат адреса e-mail.'),
(313, 'system', 'You have not selected any of the member.', 'You have not selected any of the member.', 'Вы не выбрали ни одной группы для зарегистрированных пользователей.'),
(314, 'system', 'HTML-letter', 'HTML-letter', 'HTML-письмо'),
(315, 'system', 'Letter body', 'Letter body', 'Тело письма'),
(316, 'system', 'Letter header', 'Letter header', 'Заголовок письма'),
(317, 'system', 'Restore the default form', 'Restore the default form', 'Восстановить форму по умолчанию'),
(318, 'system', 'Component "Private Messages"', 'Component "Private Messages"', 'Компонент "Личные сообщения"'),
(319, 'system', 'Component "Users"', 'Component "Users"', 'Компонент "Пользователи"'),
(320, 'system', 'Allow users to add enemies', 'Allow users to add enemies', 'Разрешить добавлять пользователей во враги'),
(321, 'system', 'Friends and enemies', 'Friends and enemies', 'Друзья и враги'),
(322, 'system', 'Allow users to add as friend', 'Allow users to add as friend', 'Разрешить добавлять пользователей в друзья'),
(323, 'system', 'Notify the user by e-mail about the new message', 'Notify the user by e-mail about the new message', 'Оповещать пользователя по e-mail о новом сообщении'),
(324, 'system', 'Private messages', 'Private messages', 'Личные сообщения'),
(325, 'system', 'Allow to send private messages', 'Allow to send private messages', 'Разрешить отправлять личные сообщения'),
(326, 'system', 'User authentication after the confirm', 'User authentication after the confirm', 'Авторизация пользователя сразу после подтверждения'),
(327, 'system', 'E-mail the administrator to send alerts', 'E-mail the administrator to send alerts', 'E-mail администратора для отсылки оповещений'),
(328, 'system', 'Send a letter to the manager when a user logs', 'Send a letter to the manager when a user logs', 'Отправлять письмо администратору при регистрации пользователя'),
(329, 'system', 'Moderated by the administrator', 'Moderated by the administrator', 'Премодерация администратором'),
(330, 'system', 'Require confirmation by e-mail', 'Require confirmation by e-mail', 'Требовать подтверждение через e-mail'),
(331, 'system', 'Group to which the user will get after registration', 'Group to which the user will get after registration', 'Группы, куда попадёт пользователь после регистрации'),
(332, 'system', 'Enable self-registration', 'Enable self-registration', 'Разрешить самостоятельную регистрацию'),
(333, 'system', 'Registration', 'Registration', 'Регистрация'),
(334, 'system', 'Bind users to sites', 'Bind users to sites', 'Привязывать пользователей к сайтам'),
(335, 'system', 'Deny yourself to recover your password', 'Deny yourself to recover your password', 'Запретить самостоятельно восстанавливать пароль'),
(336, 'system', 'General Settings', 'General Settings', 'Общие настройки'),
(337, 'system', 'Do not show the form of a failed login attempt', 'Do not show the form of a failed login attempt', 'Не показывать форму при неудачной попытке авторизации'),
(338, 'system', 'Restored', 'Restored', 'Восстановлено'),
(339, 'system', 'Nonexistent tab!', 'Nonexistent tab!', 'Несуществующая вкладка!'),
(340, 'system', 'Login through external services', 'Login through external services', 'Авторизация через внешние сервисы'),
(341, 'system', 'Email templates', 'Email templates', 'Шаблоны писем'),
(342, 'system', 'General', 'General', 'Общие'),
(343, 'system', 'Password restore', 'Password restore', 'Восстановление пароля'),
(344, 'system', 'Registration confirm', 'Registration confirm', 'Подтверждение регистрации'),
(345, 'system', 'Now you will be taken to the login page.', 'Now you will be taken to the login page.', 'Сейчас вы будете переброшены на страницу авторизации.'),
(346, 'system', 'Click here if you do not want to wait.', 'Click here if you do not want to wait.', 'Нажмите, если не хотите ждать.'),
(347, 'system', 'Login via twitter disabled', 'Login via twitter disabled', 'Авторизация через twitter запрещена'),
(348, 'system', 'Login via facebook disabled', 'Login via facebook disabled', 'Авторизация через facebook запрещена'),
(349, 'system', 'FX_ADMIN_FIELD_STRING', 'String', 'Строка'),
(350, 'system', 'FX_ADMIN_FIELD_INT', 'Integer', 'Целое число'),
(352, 'system', 'FX_ADMIN_FIELD_SELECT', 'Options list', 'Список'),
(353, 'system', 'FX_ADMIN_FIELD_BOOL', 'Boolean', 'Логическая переменная'),
(354, 'system', 'FX_ADMIN_FIELD_FILE', 'File', 'Файл'),
(355, 'system', 'FX_ADMIN_FIELD_FLOAT', 'Float number', 'Дробное число'),
(356, 'system', 'FX_ADMIN_FIELD_DATETIME', 'Date and time', 'Дата и время'),
(357, 'system', 'FX_ADMIN_FIELD_COLOR', 'Color', 'Цвет'),
(359, 'system', 'FX_ADMIN_FIELD_IMAGE', 'Image', 'Изображение'),
(360, 'system', 'FX_ADMIN_FIELD_MULTISELECT', 'Multiple select', 'Мультисписок'),
(361, 'system', 'FX_ADMIN_FIELD_LINK', 'Link to another object', 'Связь с другим объектом'),
(362, 'system', 'FX_ADMIN_FIELD_MULTILINK', 'Multiple link', 'Множественная связь'),
(363, 'system', 'FX_ADMIN_FIELD_TEXT', 'Text', 'Текст'),
(375, 'system', 'add', 'add', 'add'),
(376, 'system', 'edit', 'edit', 'edit'),
(377, 'system', 'on', 'on', 'on'),
(378, 'system', 'off', 'off', 'off'),
(379, 'system', 'settings', 'settings', 'settings'),
(380, 'system', 'delete', 'delete', 'delete'),
(381, 'system', 'Render type', 'Render type', 'Render type'),
(382, 'system', 'Live search', 'Live search', 'Live search'),
(383, 'system', 'Simple select', 'Simple select', 'Simple select'),
(384, 'system', '-Any-', '-Any-', 'Любой'),
(385, 'system', 'Only on pages of type', 'Only on pages of type', 'Только на страницах типа'),
(386, 'system', '-- choose something --', '-- choose something --', '-- выберите вариант --'),
(387, 'component_section', 'Show only header?', 'Show only header?', 'Показывать только заголовок?'),
(388, 'component_section', 'Hide on the index page', 'Hide on the index page', 'Скрыть на главной?'),
(389, 'system', 'Welcome to Floxim.CMS, please sign in', 'Welcome to Floxim.CMS, please sign in', 'Welcome to Floxim.CMS, please sign in'),
(390, 'system', 'Editing ', 'Editing ', 'Editing '),
(391, 'system', 'Fields table', 'Fields table', 'Fields table'),
(392, 'system', 'Adding new ', 'Adding new ', 'Adding new '),
(393, 'controller_component', 'New infoblock', 'New infoblock', 'Новый инфоблок'),
(394, 'controller_component', 'Infoblock for the field', 'Infoblock for the field', 'Инфоблок для поля '),
(396, 'system', 'Name of an entity created by the component', 'Name of an entity created by the component', 'Название сущности создаваемой компонентом (по-русски)'),
(397, 'system', 'Component actions', 'Component actions', 'Component actions'),
(398, 'system', 'Templates', 'Templates', 'Templates'),
(399, 'system', 'Source', 'Source', 'Source'),
(400, 'system', 'Action', 'Action', 'Action'),
(401, 'system', 'File', 'File', 'File'),
(402, 'system', 'Save', 'Save', 'Сохранить'),
(403, 'system', 'Used', 'Used', 'Used'),
(404, 'component_section', 'Nesting level', 'Nesting level', 'Уровень вложенности'),
(405, 'component_section', '2 levels', '2 levels', '2 уровня'),
(406, 'component_section', '3 levels', '3 levels', '3 уровня'),
(407, 'component_section', 'Current level +1', 'Current level +1', 'Текущий +1'),
(408, 'component_section', 'No limit', 'No limit', 'Без ограничения'),
(409, 'system', 'Cancel', 'Cancel', 'Отменить'),
(410, 'system', 'Redo', 'Redo', 'Вернуть'),
(411, 'system', 'More', 'More', 'Еще'),
(412, 'system', 'Patches', NULL, NULL),
(413, 'system', 'Update check failed', NULL, NULL),
(414, 'system', 'Installing patch %s...', NULL, NULL),
(415, 'content', 'Current Floxim version:', NULL, NULL),
(416, 'system', 'Current Floxim version:', NULL, NULL),
(433, 'system', 'Название компонента (по-русски)', 'Название компонента (по-русски)', NULL),
(434, 'system', 'Welcome to Floxim.CMS, please sign in', 'Welcome to Floxim.CMS, please sign in', NULL),
(435, 'system', 'Login', 'Login', NULL),
(436, 'system', 'Password', 'Password', NULL),
(437, 'system', 'Login', 'Login', NULL),
(438, 'system', 'Add', 'Add', NULL),
(439, 'system', 'Add new component', 'Add new component', NULL),
(440, 'system', 'Add new Components', 'Add new Components', NULL),
(441, 'system', 'Add new widget', 'Add new widget', NULL),
(442, 'system', 'Add new field', 'Add new field', NULL),
(443, 'system', 'Keyword (название папки с макетом)', 'Keyword (название папки с макетом)', NULL),
(444, 'system', 'Layout keyword', 'Layout keyword', NULL),
(445, 'system', 'Add new layout', 'Add new layout', NULL),
(446, 'system', 'Finish', 'Finish', NULL),
(447, 'system', 'Keyword can only contain letters, numbers, symbols, \\"hyphen\\" and \\"underscore\\"', 'Keyword can only contain letters, numbers, symbols, \\"hyphen\\" and \\"underscore\\"', NULL),
(448, 'system', 'Welcome to Floxim.CMS, please sign in', 'Welcome to Floxim.CMS, please sign in', NULL),
(449, 'system', 'Login', 'Login', NULL),
(450, 'system', 'Password', 'Password', NULL),
(451, 'system', 'Login', 'Login', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fx_layout`
--

CREATE TABLE IF NOT EXISTS `fx_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=64 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `fx_layout`
--

INSERT INTO `fx_layout` (`id`, `keyword`, `name`) VALUES
(1, 'supernova', 'Super Nova'),
(9, 'dummy', 'Dummy'),
(10, 'jeeptravel', 'JeepTravel');

-- --------------------------------------------------------

--
-- Table structure for table `fx_mail_template`
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
-- Table structure for table `fx_module`
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
-- Dumping data for table `fx_module`
--

INSERT INTO `fx_module` (`id`, `name`, `keyword`, `description`, `installed`, `inside_admin`, `checked`) VALUES
(1, 'FX_MODULE_AUTH', 'auth', 'FX_MODULE_AUTH_DESCRIPTION', 1, 1, 1),
(3, 'FX_MODULE_FORUM', 'forum', 'FX_MODULE_FORUM_DESCRIPTION', 1, 1, 1),
(4, 'FX_MODULE_FILEMANAGER', 'filemanager', 'FX_MODULE_FILEMANAGER_DESCRIPTION', 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `fx_multiselect`
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
-- Table structure for table `fx_patch`
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
-- Dumping data for table `fx_patch`
--

INSERT INTO `fx_patch` (`id`, `to`, `created`, `description`, `from`, `status`, `url`) VALUES
(20, '0.1.1', '2013-08-19 15:24:17', 'Adding some trolo file!', '0.1.0', 'installed', 'http://floxim.org/getfloxim/patches/0.1.0-0.1.1/patch_0.1.1.zip'),
(21, '0.1.2', '2013-08-19 15:25:46', '', '0.1.1', 'ready', 'http://floxim.org/getfloxim/patches/0.1.1-0.1.2/patch_0.1.2.zip'),
(22, '0.1.5', '2013-08-19 15:48:58', '', '0.1.2', 'pending', 'http://floxim.org/getfloxim/patches/0.1.2-0.1.5/patch_0.1.5.zip'),
(23, '0.2.0', '2013-08-19 15:48:58', '', '0.1.5', 'pending', 'http://floxim.org/getfloxim/patches/0.1.5-0.2.0/patch_0.2.0.zip');

-- --------------------------------------------------------

--
-- Table structure for table `fx_redirect`
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
-- Table structure for table `fx_session`
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
-- Dumping data for table `fx_session`
--

INSERT INTO `fx_session` (`id`, `user_id`, `session_start`, `session_time`, `ip`, `login_save`, `site_id`, `auth_type`) VALUES
('6f92936a1f341ed872ad4479fb24ded1', 99, 1380538693, 1380720103, 2130706433, 0, 0, 1),
('a90c2b79527784cc9f0b94167c0a7cd4', 99, 1380529939, 1380616695, 2130706433, 0, 0, 1),
('df96c6ff6e91afd136da0119a67775bc', 99, 1380530380, 1380623159, 2130706433, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `fx_settings`
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
-- Dumping data for table `fx_settings`
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
-- Table structure for table `fx_site`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=292 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `fx_site`
--

INSERT INTO `fx_site` (`id`, `parent_id`, `name`, `domain`, `layout_id`, `color`, `mirrors`, `priority`, `checked`, `index_page_id`, `error_page_id`, `created`, `last_updated`, `robots`, `disallow_indexing`, `type`, `language`, `offline_text`, `store_id`) VALUES
(1, 0, 'PlayGround', 'alt.floxim.loc', 1, 2, '', 0, 1, 2, 3, '2012-05-24 12:42:50', '2013-09-23 16:25:57', '# Floxim Robots file\r\nUser-agent: *\r\nDisallow: /install/', 0, 'useful', 'en', '<table width=''100%'' height=''100%'' border=''0'' cellpadding=''0'' cellspacing=''0''><tr><td align=''center''>Сайт временно (!) недоступен.</td></tr></table>', NULL),
(15, 0, 'JeepTravel', 'floxim.loc', 10, 0, '', 1, 1, 1883, 1884, '2013-06-08 17:03:02', '2013-09-09 13:02:57', NULL, 0, 'useful', 'en', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fx_template`
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
-- Table structure for table `fx_user_group`
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
-- Dumping data for table `fx_user_group`
--

INSERT INTO `fx_user_group` (`id`, `user_id`, `group_id`) VALUES
(1, 99, 1),
(6, 99, 2),
(7, 100, 1);

-- --------------------------------------------------------

--
-- Table structure for table `fx_widget`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=111 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `fx_widget`
--

INSERT INTO `fx_widget` (`id`, `name`, `keyword`, `description`, `group`, `checked`, `icon`, `embed`, `store_id`) VALUES
(1, 'Authorization form', 'authform', '', 'Profile', 1, 'auth', '', 'widget.auth'),
(2, 'Password recover form', 'recoverpasswd', '', 'Profile', 1, 'auth', '', 'widget.recoverpasswd');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

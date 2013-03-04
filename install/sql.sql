/*!40100 SET CHARACTER SET utf8*/;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET NAMES 'utf8';

DROP TABLE IF EXISTS `%%FX_PREFIX%%classificator`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%classificator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(32) NOT NULL,
  `table` char(32) NOT NULL,
  `checked` tinyint(1) NOT NULL DEFAULT '1',
  `sort_type` enum('id','name','priority') NOT NULL DEFAULT 'priority',
  `sort_direction` enum('asc','desc') NOT NULL DEFAULT 'asc',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Table_Name` (`table`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `%%FX_PREFIX%%classificator` (`id`, `name`, `table`, `checked`, `sort_type`, `sort_direction`) VALUES
(1, 'Страна', 'Country', 0, 'priority', 'asc');

DROP TABLE IF EXISTS  `%%FX_PREFIX%%classificator_country`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%classificator_country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(64) NOT NULL,
  `priority` int(11) DEFAULT NULL,
  `value` text,
  `checked` int(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `%%FX_PREFIX%%classificator_country` (`id`, `name`, `priority`, `value`, `checked`) VALUES
(1, 'Австралия', 1, NULL, 1),
(2, 'Австрия', 2, NULL, 1),
(3, 'Азербайджан', 3, NULL, 1),
(4, 'Албания', 4, NULL, 1),
(5, 'Алжир', 5, NULL, 1),
(6, 'Ангилья', 6, NULL, 1),
(7, 'Ангола', 7, NULL, 1),
(8, 'Андорра', 8, NULL, 1);

DROP TABLE IF EXISTS  `%%FX_PREFIX%%classificator_region`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%classificator_region` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(64) NOT NULL,
  `priority` int(11) DEFAULT NULL,
  `value` text,
  `checked` int(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `%%FX_PREFIX%%classificator_region` (`id`, `name`, `priority`, `value`, `checked`) VALUES
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


DROP TABLE IF EXISTS  `%%FX_PREFIX%%component`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%component` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text COMMENT 'Описание компонента',
  `group` varchar(64) NOT NULL DEFAULT 'Main',
  `icon` varchar(255) NOT NULL,
  `store_id` text,
  PRIMARY KEY (`id`),
  KEY `Class_Group` (`group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%crontask` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `%%FX_PREFIX%%ctpl`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%ctpl` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `%%FX_PREFIX%%datatype`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%datatype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(64) NOT NULL,
  `priority` int(11) DEFAULT NULL,
  `searchable` tinyint(1) NOT NULL DEFAULT '1',
  `not_null` tinyint(1) NOT NULL DEFAULT '1',
  `default` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

INSERT INTO `%%FX_PREFIX%%datatype` (`id`, `name`, `priority`, `searchable`, `not_null`, `default`) VALUES
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
(12, 'multiselect', 12, 1, 1, 0);

DROP TABLE IF EXISTS  `%%FX_PREFIX%%field`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%field` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `%%FX_PREFIX%%filetable`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%filetable` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `real_name` char(128) NOT NULL,
  `path` text NOT NULL,
  `type` char(64) DEFAULT NULL,
  `size` int(10) unsigned NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `%%FX_PREFIX%%group`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

INSERT INTO `%%FX_PREFIX%%group` (`id`, `name`) VALUES
(1, 'Администраторы'),
(2, 'Внешние пользователи'),
(3, 'Авторизированные через внешние сервисы');

DROP TABLE IF EXISTS  `%%FX_PREFIX%%history`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `name` text NOT NULL,
  `marker` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='История операций';

DROP TABLE IF EXISTS  `%%FX_PREFIX%%history_item`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%history_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `history_id` int(11) NOT NULL,
  `essence` varchar(255) NOT NULL,
  `essence_id` text NOT NULL,
  `action` varchar(255) NOT NULL,
  `prestate` longtext NOT NULL,
  `poststate` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `%%FX_PREFIX%%infoblock`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%infoblock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `field_id` int(11) NOT NULL DEFAULT '0',
  `site_id` int(11) NOT NULL,
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
  `message_selection` text NOT NULL COMMENT 'Выбранные объекты для показа',
  `access` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `keyword` (`keyword`,`subdivision_id`,`type`),
  KEY `essence_id` (`essence_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `%%FX_PREFIX%%mail_template`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%mail_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` text,
  `html` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyword` (`keyword`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

DROP TABLE IF EXISTS `%%FX_PREFIX%%menu`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%menu` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `%%FX_PREFIX%%module`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `checked` tinyint(4) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `Checked` (`checked`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%%FX_PREFIX%%multiselect`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%multiselect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `element_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS  `%%FX_PREFIX%%patch`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%patch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `description` char(255) NOT NULL,
  `from` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);

DROP TABLE IF EXISTS  `%%FX_PREFIX%%permission`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%permission` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `%%FX_PREFIX%%permission` (`id`, `user_id`, `type`, `essence_id`, `permission_set`, `group_id`, `begin`, `end`) VALUES
(1, 1, 1, 0, 0, 0, NULL, NULL);

DROP TABLE IF EXISTS  `%%FX_PREFIX%%redirect`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%redirect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `priority` int(11) NOT NULL,
  `checked` tinyint(1) NOT NULL DEFAULT '1',
  `old_url` varchar(255) NOT NULL DEFAULT '',
  `new_url` varchar(255) NOT NULL DEFAULT '',
  `header` int(3) DEFAULT '301',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `%%FX_PREFIX%%redirect` (`id`, `priority`, `checked`, `old_url`, `new_url`, `header`) VALUES
(1, 0, 1, 'floxim.org/old_url/', 'floxim.org/new_url/', 301);

DROP TABLE IF EXISTS  `%%FX_PREFIX%%session`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%session` (
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

DROP TABLE IF EXISTS `%%FX_PREFIX%%settings`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `module` varchar(255) NOT NULL DEFAULT 'system',
  `site_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `site_ID` (`site_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `%%FX_PREFIX%%settings` (`key`, `value`, `module`, `site_id`) VALUES
('version', '0.9.9', 'system', 0),
('last_check', '', 'system', 0),
('last_response', '', 'system', 0),
('next_patch', '', 'system', 0),
('user_email_field', 'email', 'system', 0),
('spam_from_name', 'Администратор', 'system', 0),
('spam_from_email', 'info@nc5.loc', 'system', 0),
('secret_key', MD5(NOW()), 'system', 0);

DROP TABLE IF EXISTS  `%%FX_PREFIX%%site`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `domain` varchar(128) NOT NULL,
  `template_id` int(11) NOT NULL DEFAULT '0',
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%%FX_PREFIX%%subdivision`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%subdivision` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS  `%%FX_PREFIX%%template`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%template` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `%%FX_PREFIX%%user`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%user` (
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `User_ID` (`id`),
  KEY `Checked` (`checked`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `%%FX_PREFIX%%user` (`id`, `site_id`, `password`, `type`, `checked`, `priority`, `created`, `last_updated`, `email`, `keyword`, `login`, `name`, `registration_code`, `avatar`, `forum_signature`, `forum_messages`, `pa_balance`, `auth_hash`) VALUES
(1, 0, 'c4ca4238a0b923820dcc509a6f75849b', 'useful', 1, 0, '2012-04-20 16:10:46', '2012-04-20 16:10:46', 'info@floxim.loc', '', 'admin', 'Администратор', NULL, '15', 'Есть многое на свете друг Горацио, что и не снилось нашим мудрецам', 3, 100, '');

DROP TABLE IF EXISTS  `%%FX_PREFIX%%user_group` ;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%user_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `User_ID` (`user_id`),
  KEY `PermissionGroup_ID` (`group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `%%FX_PREFIX%%user_group` (`id`, `user_id`, `group_id`) VALUES (1, 1, 1);

DROP TABLE IF EXISTS  `%%FX_PREFIX%%widget`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%widget` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

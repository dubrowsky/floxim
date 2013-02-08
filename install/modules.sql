/*!40100 SET CHARACTER SET utf8*/;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET NAMES 'utf8';

DROP TABLE IF EXISTS  `%%FX_PREFIX%%auth_external`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%auth_external` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `external_id` int(11) NOT NULL,
  `type` enum('twitter','fb','openid') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `%%FX_PREFIX%%auth_user_relation`;
CREATE TABLE IF NOT EXISTS `%%FX_PREFIX%%auth_user_relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `related_id` int(11) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `User_ID` (`user_id`),
  KEY `Related_ID` (`related_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `%%FX_PREFIX%%component` (`id`, `keyword`, `name`, `description`, `group`, `icon`, `store_id`) VALUES
(1, 'user', 'Пользователи', NULL, 'Пользователи', '', 'component.user');

INSERT INTO `%%FX_PREFIX%%ctpl` (`id`, `parent_id`, `component_id`, `keyword`, `name`, `rec_num`, `sort`, `action`, `with_list`, `with_full`, `type`,`widget`, `notwidget`, `embed`, `access`) VALUES
(1, 0, 1, 'main', 'Пользователи', 20, 'a:1:{s:4:"type";s:6:"manual";}', 'a:2:{s:7:"default";s:5:"index";s:7:"enabled";a:3:{i:0;s:5:"index";i:1;s:3:"add";i:2;s:6:"search";}}', 1, 1, 'useful', 0, 1, 'narrow-wide', '');

INSERT INTO `%%FX_PREFIX%%field` (`parent`, `component_id`, `ctpl_id`, `system_table_id`, `widget_id`, `name`, `description`, `type`, `format`, `not_null`, `priority`, `searchable`, `default`, `inheritance`, `type_of_edit`, `checked`) VALUES
(NULL, 1, 0, 0, 0, 'name', 'Имя на сайте', 1, '', 0, 0, 1, NULL, 0, 1, 1),
(NULL, 1, 0, 0, 0, 'avatar', 'Аватар', 6, '', 0, 0, 1, NULL, 0, 1, 1),
(NULL, 1, 0, 0, 0, 'forum_signature', 'Подпись на форуме', 3, '', 0, 0, 1, NULL, 0, 1, 1);

INSERT INTO `%%FX_PREFIX%%mail_template` (`id`, `keyword`, `subject`, `body`, `html`) VALUES
(1, 'auth_register_confirm', 'Подтверждение регистрации на сайте %SITE_NAME', 'Здравствуйте, %USER_NAME<br><br>\nВы успешно зарегистрировались на сайте <a href=''%SITE_URL''>%SITE_NAME</a><br>\nВаш логин: %USER_LOGIN<br>\nВаш пароль: %PASSWORD<br><br>\nЧтобы активировать Ваш аккаунт откройте, пожалуйста, данную ссылку: <a href=''%CONFIRM_LINK''>%CONFIRM_LINK</a><br><br>\nВы получили это сообщение, потому что Ваш e-mail адрес был зарегистрирован на сайте %SITE_URL<br>\nЕсли Вы не регистрировались на этом сайте, пожалуйста, проигнорируйте это письмо.<br><br>\nС наилучшими пожеланиями, администрация сайта <a href=''%SITE_URL''>%SITE_NAME</a>.', 1),
(2, 'auth_passwd_recovery', 'Восстановление пароля на сайте %SITE_NAME', 'Здравствуйте, %USER_NAME<br><br>\nДля восстановления пароля для пользователя %USER_LOGIN на сайте <a href=''%SITE_URL''>%SITE_NAME</a> откройте, пожалуйста, данную ссылку: <a href=''%CONFIRM_LINK''>%CONFIRM_LINK</a><br><br>\nЕсли Вы не запрашивали восстановление пароля, пожалуйста, проигнорируйте это письмо.<br><br>\nС наилучшими пожеланиями, администрация сайта <a href=''%SITE_URL''>%SITE_NAME</a>.', 1);
INSERT INTO `%%FX_PREFIX%%module` (`id`, `name`, `keyword`, `description`,`checked`) VALUES
(1, 'FX_MODULE_AUTH', 'auth', 'FX_MODULE_AUTH_DESCRIPTION', 1),
(3, 'FX_MODULE_FORUM', 'forum', 'FX_MODULE_FORUM_DESCRIPTION',1),
(4, 'FX_MODULE_FILEMANAGER', 'filemanager', 'FX_MODULE_FILEMANAGER_DESCRIPTION', 1);

INSERT INTO `%%FX_PREFIX%%settings` (`key`, `value`, `module`, `site_id`) VALUES
('authtype', '3', 'auth', 0),
('pm_allow', '1', 'auth', 0),
('pm_notify', '1', 'auth', 0),
('friend_allow', '1', 'auth', 0),
('banned_allow', '', 'auth', 0),
('incorrect_login_form_disable', '0', 'auth', 0),
('allow_registration', '1', 'auth', 0),
('external_user_groups', 'a:1:{i:0;s:1:"2";}', 'auth', 0),
('min_pasword_length', '0', 'auth', 0),
('deny_recoverpasswd', '0', 'auth', 0),
('online_timeleft', '300', 'auth', 0),
('bind_to_site', '0', 'auth', 0),
('user_component_id', '1', 'auth', 0),
('registration_confirm', '1', 'auth', 0),
('registration_premoderation', '0', 'auth', 0),
('registration_notify_admin', '0', 'auth', 0),
('autoauthorize', '1', 'auth', 0),
('admin_notify_email', '', 'auth', 0),
('twitter_enabled', '0', 'auth', 0),
('twitter_app_id', '', 'auth', 0),
('twitter_app_key', '', 'auth', 0),
('twitter_map', 'a:2:{i:1;a:2:{s:14:"external_field";s:11:"screen_name";s:10:"user_field";s:4:"name";}i:2;a:2:{s:14:"external_field";s:17:"profile_image_url";s:10:"user_field";s:6:"avatar";}}', 'auth', 0),
('twitter_group', 'a:1:{i:0;s:1:"3";}', 'auth', 0),
('twitter_addaction', '/* Доступные переменные: $fx_core, $user, $response */\r\nif ( fx::config()->AUTHORIZE_BY == ''login'' ) {\r\n  if ( !$user[''login''] ) {\r\n    $maybe_login = $response[''screen_name''];\r\n    if ( $fx_core->user->get(''login'', $maybe_login )) {\r\n      $maybe_login .= $response[''id''];\r\n    }\r\n    $user->set(''login'', $maybe_login)->save();\r\n  }\r\n}\r\n\r\n', 'auth', 0),
('facebook_enabled', '0', 'auth', 0),
('facebook_app_id', '', 'auth', 0),
('facebook_app_key', '', 'auth', 0),
('facebook_addaction', '/* Доступные переменные: $fx_core, $user, $response */\r\nif ( fx::config()->AUTHORIZE_BY == ''login'' ) {\r\n  if ( !$user[''login''] ) {\r\n    $maybe_login = $response[''name''];\r\n    if ( $fx_core->user->get(''login'', $maybe_login )) {\r\n      $maybe_login .= $response[''id''];\r\n    }\r\n    $user->set(''login'', $maybe_login)->save();\r\n  }\r\n}\r\n', 'auth', 0),
('facebook_map', 'a:3:{i:1;a:2:{s:14:"external_field";s:4:"name";s:10:"user_field";s:4:"name";}i:2;a:2:{s:14:"external_field";s:5:"email";s:10:"user_field";s:5:"email";}i:21;a:2:{s:14:"external_field";s:6:"avatar";s:10:"user_field";s:6:"avatar";}}', 'auth', 0),
('facebook_group', 'a:1:{i:0;s:1:"3";}', 'auth', 0);
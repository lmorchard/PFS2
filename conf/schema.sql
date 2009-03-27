DROP TABLE IF EXISTS `plugins`;
CREATE TABLE `plugins` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `latest_version` varchar(255) NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon_url` varchar(255) NOT NULL,
  `license_url` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
DROP TABLE IF EXISTS `mimes`;
CREATE TABLE `mimes` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `suffixes` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `plugins_mimes`;
CREATE TABLE `plugins_mimes` (
  `mime_id` int(11) unsigned NOT NULL,
  `plugin_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`mime_id`,`plugin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `plugin_releases`;
CREATE TABLE `plugin_releases` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `plugin_id` int(11) unsigned NOT NULL,
  `guid` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `xpi_location` varchar(255) NOT NULL,
  `installer_location` varchar(255) NOT NULL,
  `installer_hash` varchar(255) NOT NULL,
  `installer_shows_ui` tinyint(1) NOT NULL,
  `manual_installation_url` varchar(255) NOT NULL,
  `license_url` varchar(255) NOT NULL,
  `needs_restart` tinyint(1) NOT NULL,
  `min` varchar(255) default NULL,
  `max` varchar(255) default NULL,
  `xpcomabi` varchar(255) default NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `oses`;
CREATE TABLE `oses` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `plugin_releases_oses`;
CREATE TABLE `plugin_releases_oses` (
  `os_id` int(11) unsigned NOT NULL,
  `plugin_release_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`os_id`,`plugin_release_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `platforms`;
CREATE TABLE `platforms` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `app_id` varchar(255) NOT NULL,
  `app_release` varchar(255) NOT NULL,
  `app_version` varchar(255) NOT NULL,
  `locale` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `plugin_releases_platforms`;
CREATE TABLE `plugin_releases_platforms` (
  `platform_id` int(11) unsigned NOT NULL,
  `plugin_release_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`platform_id`,`plugin_release_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `oses` (`name`) VALUES
    ('*'),
    ('win'),
    ('windows vista'),
    ('windows nt 6.0'),
    ('mac'),
    ('mac os x'),
    ('ppc mac os x'),
    ('intel mac os x'),
    ('linux'),
    ('linux x86'),
    ('linux x86_64'),
    ('sunos'),
    ('sunos sun4u');

SET @fx_guid = '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}';

INSERT INTO `platforms` (`app_id`, `app_release`, `app_version`, `locale`) VALUES
    ('*', '*', '*', '*'),
    (@fx_guid, '*', '*', '*'),
    (@fx_guid, '*', '*', 'ja-JP'),
    (@fx_guid, '3.0', '*', '*');

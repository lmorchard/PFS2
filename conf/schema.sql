/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DROP TABLE IF EXISTS `mimes`;
CREATE TABLE `mimes` (
    `id` int(11) unsigned NOT NULL auto_increment,
    `name` varchar(255) NOT NULL,
    `description` text NOT NULL,
    `suffixes` varchar(255) NOT NULL,
    UNIQUE INDEX `unique_mime` (`name`),
    PRIMARY KEY  (`id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `oses`;
CREATE TABLE `oses` (
    `id` int(11) unsigned NOT NULL auto_increment,
    `name` varchar(255) NOT NULL,
    UNIQUE INDEX `unique_os` (`name`),
    PRIMARY KEY  (`id`)
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

DROP TABLE IF EXISTS `platforms`;
CREATE TABLE `platforms` (
    `id` int(11) unsigned NOT NULL auto_increment,
    `app_id` varchar(255) NOT NULL,
    `app_release` varchar(255) NOT NULL,
    `app_version` varchar(255) NOT NULL,
    `locale` varchar(255) NOT NULL,
    UNIQUE INDEX `unique_platform` (`app_id`, `app_release`, `app_version`, `locale`),
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET @fx_guid = '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}';

INSERT INTO `platforms` (`app_id`, `app_release`, `app_version`, `locale`) VALUES
('*', '*', '*', '*'),
(@fx_guid, '*', '*', '*'),
(@fx_guid, '*', '*', 'ja-JP'),
(@fx_guid, '3.0', '*', '*');

DROP TABLE IF EXISTS `plugins`;
CREATE TABLE `plugins` (
    `id` int(11) unsigned NOT NULL auto_increment,
    `pfs_id` char(64) NOT NULL UNIQUE,
    `name` varchar(255) NOT NULL,
    `description` text NOT NULL,
    `latest_release_id` int(11) unsigned,
    `vendor` varchar(255) NOT NULL,
    `url` varchar(255) NOT NULL,
    `icon_url` varchar(255) NOT NULL,
    `license_url` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`),
    FOREIGN KEY (`latest_release_id`) REFERENCES plugin_releases(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `plugins_mimes`;
CREATE TABLE `plugins_mimes` (
    `id` int(11) unsigned NOT NULL auto_increment,
    `plugin_id` int(11) unsigned NOT NULL,
    `mime_id` int(11) unsigned NOT NULL,
    UNIQUE INDEX (`mime_id`,`plugin_id`),
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `plugin_aliases`;
CREATE TABLE `plugin_aliases` (
    `id` int(11) unsigned NOT NULL auto_increment,
    `plugin_id` int(11) unsigned NOT NULL,
    `alias` varchar(255) NOT NULL,
    `is_regex` tinyint(1) NOT NULL default 0,
    UNIQUE INDEX `unique_release` (`plugin_id`, `alias`, `is_regex`),
    PRIMARY KEY (`id`),
    FOREIGN KEY (`plugin_id`) REFERENCES plugins(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `plugin_releases`;
CREATE TABLE `plugin_releases` (
    `id` int(11) unsigned NOT NULL auto_increment,
    `plugin_id` int(11) unsigned NOT NULL,
    `os_id` int(11) unsigned NOT NULL,
    `platform_id` int(11) unsigned NOT NULL,
    `status_code` int(11) default NULL,
    `vulnerability_description` TEXT NULL,
    `vulnerability_url` varchar(255) default NULL,
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
    `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `unique_release` (`plugin_id`, `os_id`, `platform_id`, `version`),
    FOREIGN KEY (`plugin_id`) REFERENCES plugins(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

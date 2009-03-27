-- MySQL dump 10.11
--
-- Host: localhost    Database: pfs2_test
-- ------------------------------------------------------
-- Server version	5.0.77-log

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

--
-- Table structure for table `mimes`
--

DROP TABLE IF EXISTS `mimes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mimes` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `suffixes` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mimes`
--

LOCK TABLES `mimes` WRITE;
/*!40000 ALTER TABLE `mimes` DISABLE KEYS */;
INSERT INTO `mimes` VALUES (1,'application/x-shockwave-flash','Shockwave Flash','swf'),(2,'application/futuresplash','FutureSplash Player','spl'),(3,'application/x-director','Adobe Shockwave Player','dcr,dxr'),(4,'audio/x-pn-realaudio-plugin/','',''),(5,'audio/x-pn-realaudio','',''),(6,'application/sdp','SDP stream descriptor','sdp'),(7,'application/x-sdp','SDP stream descriptor','sdp'),(8,'application/x-rtsp','RTSP stream descriptor','rtsp,rts'),(9,'video/quicktime','QuickTime Movie','mov,qt,mqv'),(10,'video/flc','AutoDesk Animator','flc,fli,cel'),(11,'audio/x-wav','WAVE audio','wav,bwf'),(12,'audio/wav','WAVE audio','wav,bwf'),(13,'audio/aiff','AIFF audio','aiff,aif,aifc,cdda'),(14,'audio/x-aiff','AIFF audio','aiff,aif,aifc,cdda'),(15,'audio/basic','uLaw/AU audio','au,snd,ulw'),(16,'audio/mid','MIDI','mid,midi,smf,kar'),(17,'audio/x-midi','MIDI','mid,midi,smf,kar'),(18,'audio/midi','MIDI','mid,midi,smf,kar'),(19,'audio/vnd.qcelp','QUALCOMM PureVoice audio','qcp'),(20,'audio/x-gsm','GSM audio','gsm'),(21,'audio/AMR','AMR audio','AMR'),(22,'audio/aac','AAC audio','aac,adts'),(23,'audio/x-aac','AAC audio','aac,adts'),(24,'audio/x-caf','CAF audio','caf'),(25,'audio/ac3','AC3 audio','ac3'),(26,'audio/x-ac3','AC3 audio','ac3'),(27,'video/x-mpeg','MPEG media','mpeg,mpg,m1s,m1v,m1a,m75,m15,mp2,mpm,mpv,mpa'),(28,'video/mpeg','MPEG media','mpeg,mpg,m1s,m1v,m1a,m75,m15,mp2,mpm,mpv,mpa'),(29,'audio/mpeg','MPEG audio','mpeg,mpg,m1s,m1a,mp2,mpm,mpa,m2a'),(30,'audio/x-mpeg','MPEG audio','mpeg,mpg,m1s,m1a,mp2,mpm,mpa,m2a'),(31,'video/3gpp','3GPP media','3gp,3gpp'),(32,'audio/3gpp','3GPP media','3gp,3gpp'),(33,'video/3gpp2','3GPP2 media','3g2,3gp2'),(34,'audio/3gpp2','3GPP2 media','3g2,3gp2'),(35,'video/sd-video','SD video','sdv'),(36,'application/x-mpeg','AMC media','amc'),(37,'video/mp4','MPEG-4 media','mp4'),(38,'audio/mp4','MPEG-4 media','mp4'),(39,'audio/x-m4a','AAC audio','m4a'),(40,'audio/x-m4p','AAC audio','m4p'),(41,'audio/x-m4b','AAC audio book','m4b'),(42,'video/x-m4v','Video','m4v'),(43,'audio/mp3','MP3 audio','mp3,swa'),(44,'audio/x-mp3','MP3 audio','mp3,swa'),(45,'audio/mpeg3','MP3 audio','mp3,swa'),(46,'audio/x-mpeg3','MP3 audio','mp3,swa'),(47,'image/x-bmp','BMP image','bmp,dib'),(48,'image/x-macpaint','MacPaint image','pntg,pnt,mac'),(49,'image/pict','PICT image','pict,pic,pct'),(50,'image/x-pict','PICT image','pict,pic,pct'),(51,'image/png','PNG image','png'),(52,'image/x-png','PNG image','png'),(53,'image/x-quicktime','QuickTime image','qtif,qti'),(54,'image/x-sgi','SGI image','sgi,rgb'),(55,'image/x-targa','TGA image','targa,tga'),(56,'image/tiff','TIFF image','tif,tiff'),(57,'image/x-tiff','TIFF image','tif,tiff'),(58,'image/jp2','JPEG2000 image','jp2'),(59,'image/jpeg2000','JPEG2000 image','jp2'),(60,'image/jpeg2000-image','JPEG2000 image','jp2'),(61,'image/x-jpeg2000-image','JPEG2000 image','jp2'),(62,'application/x-java-vm','',''),(63,'application/x-java-applet;jpi-version=1.5','',''),(64,'application/x-java-bean;jpi-version=1.5','',''),(65,'application/x-java-applet;version=1.3','',''),(66,'application/x-java-bean;version=1.3','',''),(67,'application/x-java-applet;version=1.2.2','',''),(68,'application/x-java-bean;version=1.2.2','',''),(69,'application/x-java-applet;version=1.2.1','',''),(70,'application/x-java-bean;version=1.2.1','',''),(71,'application/x-java-applet;version=1.4.2','',''),(72,'application/x-java-bean;version=1.4.2','',''),(73,'application/x-java-applet;version=1.5','',''),(74,'application/x-java-bean;version=1.5','',''),(75,'application/x-java-applet;version=1.3.1','',''),(76,'application/x-java-bean;version=1.3.1','',''),(77,'application/x-java-applet;version=1.4','',''),(78,'application/x-java-bean;version=1.4','',''),(79,'application/x-java-applet;version=1.4.1','',''),(80,'application/x-java-bean;version=1.4.1','',''),(81,'application/x-java-applet;version=1.2','',''),(82,'application/x-java-bean;version=1.2','',''),(83,'application/x-java-applet;version=1.1.3','',''),(84,'application/x-java-bean;version=1.1.3','',''),(85,'application/x-java-applet;version=1.1.2','',''),(86,'application/x-java-bean;version=1.1.2','',''),(87,'application/x-java-applet;version=1.1.1','',''),(88,'application/x-java-bean;version=1.1.1','',''),(89,'application/x-java-applet;version=1.1','',''),(90,'application/x-java-bean;version=1.1','',''),(91,'application/x-java-applet','',''),(92,'application/x-java-bean','',''),(93,'application/pdf','',''),(94,'application/vnd.fdf','',''),(95,'application/vnd.adobe.xfdf','',''),(96,'application/vnd.adobe.xdp+xml','',''),(97,'application/vnd.adobe.xfd+xml','',''),(98,'application/x-mtx','',''),(99,'application/asx','',''),(100,'application/x-mplayer2','',''),(101,'audio/x-ms-wax','',''),(102,'audio/x-ms-wma','',''),(103,'video/x-ms-asf','',''),(104,'video/x-ms-asf-plugin','',''),(105,'video/x-ms-wm','',''),(106,'video/x-ms-wmp','',''),(107,'video/x-ms-wmv','',''),(108,'video/x-ms-wmx','',''),(109,'video/x-ms-wvx','',''),(110,'application/x-xstandard','',''),(111,'application/x-dnl','',''),(112,'application/x-videoegg-loader','',''),(113,'video/divx','','');
/*!40000 ALTER TABLE `mimes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oses`
--

DROP TABLE IF EXISTS `oses`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `oses` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `oses`
--

LOCK TABLES `oses` WRITE;
/*!40000 ALTER TABLE `oses` DISABLE KEYS */;
INSERT INTO `oses` VALUES (1,'*'),(2,'win'),(3,'windows vista'),(4,'windows nt 6.0'),(5,'mac'),(6,'mac os x'),(7,'ppc mac os x'),(8,'intel mac os x'),(9,'linux'),(10,'linux x86'),(11,'linux x86_64'),(12,'sunos'),(13,'sunos sun4u');
/*!40000 ALTER TABLE `oses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `platforms`
--

DROP TABLE IF EXISTS `platforms`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `platforms` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `app_id` varchar(255) NOT NULL,
  `app_release` varchar(255) NOT NULL,
  `app_version` varchar(255) NOT NULL,
  `locale` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `platforms`
--

LOCK TABLES `platforms` WRITE;
/*!40000 ALTER TABLE `platforms` DISABLE KEYS */;
INSERT INTO `platforms` VALUES (1,'*','*','*','*'),(2,'{ec8030f7-c20a-464f-9b0e-13a3a9e97384}','*','*','*'),(3,'{ec8030f7-c20a-464f-9b0e-13a3a9e97384}','*','*','ja-JP'),(4,'{ec8030f7-c20a-464f-9b0e-13a3a9e97384}','3.0','*','*');
/*!40000 ALTER TABLE `platforms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_releases`
--

DROP TABLE IF EXISTS `plugin_releases`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `plugin_releases`
--

LOCK TABLES `plugin_releases` WRITE;
/*!40000 ALTER TABLE `plugin_releases` DISABLE KEYS */;
INSERT INTO `plugin_releases` VALUES (1,1,'{4cfaef8a-a6c9-41a0-8e6f-967eb8f49143}','','10.0.22.87','','','',0,'http://www.adobe.com/go/getflashplayer','http://www.adobe.com/go/eula_flashplayer',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(2,1,'{4cfaef8a-a6c9-41a0-8e6f-967eb8f49143}','','10.0.22.87','http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-win.xpi','','',0,'http://www.adobe.com/go/getflashplayer','http://www.adobe.com/go/eula_flashplayer',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(3,1,'{7a646d7b-0202-4491-9151-cf66fa0722b2}','','10.0.22.87','http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-linux.xpi','','',0,'http://www.adobe.com/go/getflashplayer','http://www.adobe.com/go/eula_flashplayer',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(4,1,'{89977581-9028-4be0-b151-7c4f9bcd3211}','','10.0.22.87','http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-mac.xpi','','',0,'http://www.adobe.com/go/getflashplayer','http://www.adobe.com/go/eula_flashplayer',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(5,1,'{0ae66efd-e183-431a-ab51-3af2c278a3dd}','','10.0.22.87','http://download.macromedia.com/pub/flashplayer/xpi/current/flashplayer-solaris-sparc.xpi','','',0,'http://www.adobe.com/go/getflashplayer','http://www.adobe.com/go/eula_flashplayer',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(6,1,'{0ae66efd-e183-431a-ab51-3af2c278a3dd}','','10.0.22.87','http://download.macromedia.com/pub/flashplayer/xpi/current/flashplayer-solaris-x86.xpi','','',0,'http://www.adobe.com/go/getflashplayer','http://www.adobe.com/go/eula_flashplayer',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(7,2,'{4cfaef8a-a6c9-41a0-8e6f-967eb8f49143}','','10.0.22.87','','','',0,'http://www.adobe.com/go/getflashplayer','http://www.adobe.com/go/eula_flashplayer_jp',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(8,2,'{4cfaef8a-a6c9-41a0-8e6f-967eb8f49143}','','10.0.22.87','http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-win.xpi','','',0,'http://www.adobe.com/go/getflashplayer','http://www.adobe.com/go/eula_flashplayer_jp',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(9,2,'{7a646d7b-0202-4491-9151-cf66fa0722b2}','','10.0.22.87','http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-linux.xpi','','',0,'http://www.adobe.com/go/getflashplayer','http://www.adobe.com/go/eula_flashplayer_jp',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(10,2,'{89977581-9028-4be0-b151-7c4f9bcd3211}','','10.0.22.87','http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-mac.xpi','','',0,'http://www.adobe.com/go/getflashplayer','http://www.adobe.com/go/eula_flashplayer_jp',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(11,2,'{0ae66efd-e183-431a-ab51-3af2c278a3dd}','','10.0.22.87','http://download.macromedia.com/pub/flashplayer/xpi/current/flashplayer-solaris-sparc.xpi','','',0,'http://www.adobe.com/go/getflashplayer','http://www.adobe.com/go/eula_flashplayer_jp',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(12,2,'{0ae66efd-e183-431a-ab51-3af2c278a3dd}','','10.0.22.87','http://download.macromedia.com/pub/flashplayer/xpi/current/flashplayer-solaris-x86.xpi','','',0,'http://www.adobe.com/go/getflashplayer','http://www.adobe.com/go/eula_flashplayer_jp',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(13,3,'{45f2a22c-4029-4209-8b3d-1421b989633f}','','10.1','https://www.macromedia.com/go/xpi_shockwaveplayer_win','','',0,'http://www.adobe.com/go/getshockwave/','http://www.adobe.com/go/eula_shockwaveplayer',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(14,3,'{49141640-b629-4d57-a539-b521c4a99eff}','','10.1','https://www.macromedia.com/go/xpi_shockwaveplayer_macosx','','',0,'http://www.adobe.com/go/getshockwave/','http://www.adobe.com/go/eula_shockwaveplayer',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(15,4,'{45f2a22c-4029-4209-8b3d-1421b989633f}','','10.1','https://www.macromedia.com/go/xpi_shockwaveplayerj_win','','',0,'http://www.adobe.com/go/getshockwave/','http://www.adobe.com/go/eula_shockwaveplayer_jp',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(16,4,'{49141640-b629-4d57-a539-b521c4a99eff}','','10.1','https://www.macromedia.com/go/xpi_shockwaveplayerj_macosx','','',0,'http://www.adobe.com/go/getshockwave/','http://www.adobe.com/go/eula_shockwaveplayer_jp',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(17,5,'{d586351c-cb55-41a7-8e7b-4aaac5172d39}','','10.5','http://forms.real.com/real/player/download.html?type=firefox','','',0,'http://www.real.com/','',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(18,5,'{269eb771-59de-4702-9209-ca97ce522f6d}','','10.5','','','',0,'http://www.real.com/','',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(19,6,'{a42bb825-7eee-420f-8ee7-834062b6fefd}','','','','','',0,'http://www.apple.com/quicktime/download/','',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(20,6,'{a42bb825-7eee-420f-8ee7-834062b6fefd}','','','','','',0,'http://www.apple.com/quicktime/download/','',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(21,7,'{fbe640ef-4375-4f45-8d79-767d60bf75b8}','','','','http://java.com/firefoxjre_exe','sha1:89a78d34a36d7e25cc32b1a507a2cd6fb87dd40a',0,'http://java.com/firefoxjre','',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(22,7,'{92a550f2-dfd2-4d2f-a35d-a98cfda73595}','','','http://java.com/jre-install.xpi','http://java.com/firefoxjre_exe','sha1:89a78d34a36d7e25cc32b1a507a2cd6fb87dd40a',0,'http://java.com/firefoxjre','',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(23,7,'{fbe640ef-4375-4f45-8d79-767d60bf75b8}','','','','','',0,'http://java.com/firefoxjre','',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(24,8,'{d87cd824-67cb-4547-8587-616c70318095}','','','','','',0,'http://www.adobe.com/products/acrobat/readstep.html','',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(25,8,'{d87cd824-67cb-4547-8587-616c70318095}','','','','','',0,'http://www.adobe.com/products/acrobat/readstep.html','',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(26,8,'{d87cd824-67cb-4547-8587-616c70318095}','','','','','',0,'http://www.adobe.com/products/acrobat/readstep.html','',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(27,9,'{03f998b2-0e00-11d3-a498-00104b6eb52e}','','','','','',0,'http://www.viewpoint.com/pub/products/vmp.html','',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(28,9,'{03f998b2-0e00-11d3-a498-00104b6eb52e}','','','','','',0,'http://www.viewpoint.com/pub/products/vmp.html','',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(29,10,'{cff1240a-fd24-4b9f-8183-ccd96e5300d0}','','','','','',0,'http://port25.technet.com/pages/windows-media-player-firefox-plugin-download.aspx','',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(30,10,'{cff1240a-fd24-4b9f-8183-ccd96e5300d0}','','','','','',0,'http://port25.technet.com/pages/windows-media-player-firefox-plugin-download.aspx','',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(31,11,'{3563d917-2f44-4e05-8769-47e655e92361}','','','http://xstandard.com/download/xstandard.xpi','','',0,'http://xstandard.com/download/','http://xstandard.com/license/',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(32,11,'{3563d917-2f44-4e05-8769-47e655e92361}','','','http://xstandard.com/download/xstandard.xpi','','',0,'http://xstandard.com/download/','http://xstandard.com/license/',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(33,12,'{ce9317a3-e2f8-49b9-9b3b-a7fb5ec55161}','','5.5','http://digitalwebbooks.com/reader/xpinst.xpi','','',0,'http://digitalwebbooks.com/reader/','',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(34,13,'{b8b881f0-2e07-11db-a98b-0800200c9a66}','','','http://update.videoegg.com/Install/Windows/Initial/VideoEggPublisher.xpi','','',0,'http://www.videoegg.com/','',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(35,14,'{a8b771f0-2e07-11db-a98b-0800200c9a66}','','','http://download.divx.com/player/DivXWebPlayer.xpi','','',0,'http://go.divx.com/plugin/download/','http://go.divx.com/plugin/license/',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07'),(36,14,'{a8b771f0-2e07-11db-a98b-0800200c9a66}','','','http://download.divx.com/player/DivXWebPlayerMac.xpi','','',0,'http://go.divx.com/plugin/download/','http://go.divx.com/plugin/license/',0,'','','','2009-03-27 21:00:07','2009-03-27 21:00:07');
/*!40000 ALTER TABLE `plugin_releases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_releases_oses`
--

DROP TABLE IF EXISTS `plugin_releases_oses`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `plugin_releases_oses` (
  `os_id` int(11) unsigned NOT NULL,
  `plugin_release_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`os_id`,`plugin_release_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `plugin_releases_oses`
--

LOCK TABLES `plugin_releases_oses` WRITE;
/*!40000 ALTER TABLE `plugin_releases_oses` DISABLE KEYS */;
INSERT INTO `plugin_releases_oses` VALUES (1,18),(1,23),(2,2),(2,8),(2,13),(2,15),(2,17),(2,19),(2,22),(2,24),(2,27),(2,29),(2,31),(2,33),(2,34),(2,35),(4,1),(4,7),(4,21),(5,4),(5,10),(5,14),(5,16),(5,20),(5,25),(5,28),(5,30),(5,32),(5,36),(9,3),(9,9),(9,26),(12,6),(12,12),(13,5),(13,11);
/*!40000 ALTER TABLE `plugin_releases_oses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_releases_platforms`
--

DROP TABLE IF EXISTS `plugin_releases_platforms`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `plugin_releases_platforms` (
  `platform_id` int(11) unsigned NOT NULL,
  `plugin_release_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`platform_id`,`plugin_release_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `plugin_releases_platforms`
--

LOCK TABLES `plugin_releases_platforms` WRITE;
/*!40000 ALTER TABLE `plugin_releases_platforms` DISABLE KEYS */;
INSERT INTO `plugin_releases_platforms` VALUES (2,1),(2,2),(2,3),(2,4),(2,5),(2,6),(2,13),(2,14),(2,17),(2,18),(2,19),(2,20),(2,21),(2,22),(2,23),(2,24),(2,25),(2,26),(2,27),(2,28),(2,29),(2,30),(2,31),(2,32),(2,33),(2,34),(2,35),(2,36),(3,7),(3,8),(3,9),(3,10),(3,11),(3,12),(3,15),(3,16);
/*!40000 ALTER TABLE `plugin_releases_platforms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugins`
--

DROP TABLE IF EXISTS `plugins`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `plugins`
--

LOCK TABLES `plugins` WRITE;
/*!40000 ALTER TABLE `plugins` DISABLE KEYS */;
INSERT INTO `plugins` VALUES (1,'Adobe Flash Player','','','Adobe','http://www.adobe.com/go/getflashplayer','','http://www.adobe.com/go/eula_flashplayer'),(2,'Adobe Flash Player','','','Adobe','http://www.adobe.com/go/getflashplayer','','http://www.adobe.com/go/eula_flashplayer_jp'),(3,'Adobe Shockwave Player','','','Adobe','http://www.adobe.com/go/getshockwave/','','http://www.adobe.com/go/eula_shockwaveplayer'),(4,'Adobe Shockwave Player','','','Adobe','http://www.adobe.com/go/getshockwave/','???','http://www.adobe.com/go/eula_shockwaveplayer_jp'),(5,'Real Player','','','Real Networks','http://www.real.com','',''),(6,'Apple Quicktime','','','Apple','http://www.apple.com/quicktime/download/','',''),(7,'Java Runtime Environment','','','Sun Microsystems','http://java.com/firefoxjre','',''),(8,'Adobe Acrobat Plug-In','','','Adobe','http://www.adobe.com/products/acrobat/readstep.html','',''),(9,'Viewpoint Media Player','','','Viewpoint','http://www.viewpoint.com/pub/products/vmp.html','',''),(10,'Windows Media Player','','','Microsoft','http://port25.technet.com/pages/windows-media-player-firefox-plugin-download.aspx','',''),(11,'XStandard XHTML WYSIWYG Editor','','','','','http://xstandard.com/images/xicon32x32.gif','http://xstandard.com/license/'),(12,'DNL Reader','','','','','http://digitalwebbooks.com/reader/dwb16.gif',''),(13,'VideoEgg Publisher','','','','','http://videoegg.com/favicon.ico',''),(14,'DivX Web Player','','','','','http://images.divx.com/divx/player/webplayer.png','http://go.divx.com/plugin/license/');
/*!40000 ALTER TABLE `plugins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugins_mimes`
--

DROP TABLE IF EXISTS `plugins_mimes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `plugins_mimes` (
  `mime_id` int(11) unsigned NOT NULL,
  `plugin_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`mime_id`,`plugin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `plugins_mimes`
--

LOCK TABLES `plugins_mimes` WRITE;
/*!40000 ALTER TABLE `plugins_mimes` DISABLE KEYS */;
INSERT INTO `plugins_mimes` VALUES (1,1),(1,2),(2,1),(2,2),(3,3),(3,4),(4,5),(5,5),(6,6),(7,6),(8,6),(9,6),(10,6),(11,6),(12,6),(13,6),(14,6),(15,6),(16,6),(17,6),(18,6),(19,6),(20,6),(21,6),(22,6),(23,6),(24,6),(25,6),(26,6),(27,6),(28,6),(29,6),(30,6),(31,6),(32,6),(33,6),(34,6),(35,6),(36,6),(37,6),(38,6),(39,6),(40,6),(41,6),(42,6),(43,6),(44,6),(45,6),(46,6),(47,6),(48,6),(49,6),(50,6),(51,6),(52,6),(53,6),(54,6),(55,6),(56,6),(57,6),(58,6),(59,6),(60,6),(61,6),(62,7),(63,7),(64,7),(65,7),(66,7),(67,7),(68,7),(69,7),(70,7),(71,7),(72,7),(73,7),(74,7),(75,7),(76,7),(77,7),(78,7),(79,7),(80,7),(81,7),(82,7),(83,7),(84,7),(85,7),(86,7),(87,7),(88,7),(89,7),(90,7),(91,7),(92,7),(93,8),(94,8),(95,8),(96,8),(97,8),(98,9),(99,10),(100,10),(101,10),(102,10),(103,10),(104,10),(105,10),(106,10),(107,10),(108,10),(109,10),(110,11),(111,12),(112,13),(113,14);
/*!40000 ALTER TABLE `plugins_mimes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-03-27 21:18:52

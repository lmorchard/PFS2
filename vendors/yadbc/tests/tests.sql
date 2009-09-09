-- MySQL dump 10.10
--
-- Host: localhost
-- ------------------------------------------------------
-- Server version	5.0.22

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
-- Table structure for table `locales`
--

DROP TABLE IF EXISTS `locales`;
CREATE TABLE `locales` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `locales`
--


/*!40000 ALTER TABLE `locales` DISABLE KEYS */;
LOCK TABLES `locales` WRITE;
INSERT INTO `locales` VALUES (1,'en-US'),(2,'sv-SE'),(3,'bg'),(4,'el'),(5,'ro'),(6,'ca'),(7,'es-ES'),(8,'ga-IE'),(9,'da'),(10,'pa-IN'),(11,'de'),(12,'fi'),(13,'lt'),(14,'fr'),(15,'sl'),(16,'zh-CN'),(17,'ja'),(18,'pl'),(19,'he'),(20,'ru'),(21,'nb-NO'),(22,'ko'),(23,'cs'),(24,'es-AR'),(25,'mk'),(26,'eu'),(27,'sk'),(28,'nl'),(29,'hu'),(30,'it'),(31,'zh-TW'),(32,'en-GB'),(33,'ar'),(34,'gu-IN'),(35,'pt-BR'),(36,'tr'),(37,'mn'),(38,'ja-JP-mac'),(39,'zu'),(40,'pt-PT'),(41,'fy-NL'),(42,'af'),(43,'xh'),(44,'nso'),(45,'nn-NO'),(46,'ka'),(47,'be'),(48,'ku');
UNLOCK TABLES;
/*!40000 ALTER TABLE `locales` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


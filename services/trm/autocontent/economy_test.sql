-- MySQL dump 10.13  Distrib 5.1.47, for redhat-linux-gnu (i386)
--
-- Host: localhost    Database: autocontent
-- ------------------------------------------------------
-- Server version	5.1.47-log

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
-- Table structure for table `economy_test`
--

DROP TABLE IF EXISTS `economy_test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `economy_test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `short` varchar(30) NOT NULL,
  `name` varchar(30) NOT NULL DEFAULT '',
  `unit` varchar(30) NOT NULL DEFAULT '',
  `value` varchar(30) NOT NULL DEFAULT '',
  `date` varchar(30) NOT NULL DEFAULT '',
  `change_percent` float NOT NULL DEFAULT '0',
  `country` varchar(10) DEFAULT NULL,
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `short` (`short`,`unit`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `economy_test`
--

LOCK TABLES `economy_test` WRITE;
/*!40000 ALTER TABLE `economy_test` DISABLE KEYS */;
INSERT INTO `economy_test` VALUES (1,'trm','TRM','COP','2690.1500','2015-07-08',18.5752,'CO','2015-07-08 13:59:44'),(2,'ibex35','IBEX 35','','10430.3000','08/07/2015',0.81,'es','2015-07-08 13:59:51'),(3,'ftse100','FTSE 100','','6432.2100','07/07/2015',-1.58,'gb','2015-07-08 13:59:51'),(4,'bovespa','IBOVESPA (BVSP)','','52343.7100','2015-07-08',-1.3316,'br','2015-07-08 13:59:52'),(5,'uvr','UVR','','222.5588','2015-07-08',0,'co','2015-07-08 13:59:57'),(6,'cafe','Caf?','USD','1.4200','2015-07-08',-71.831,'co','2015-07-08 13:59:57'),(7,'interbancariaea','Interbancaria E.A.','%','4.5200','2015-07-08',0,'co','2015-07-08 13:59:57'),(8,'libra','Libra Esterlina','USD','1.5350','2015-07-08',-4.9902,'uk','2015-07-08 13:59:58'),(9,'libra','Libra Esterlina','COP','4129.3803','2015-07-08',14.5117,'uk','2015-07-08 13:59:58'),(10,'cad','Dolar Canadiense','USD','0.7849','2015-07-08',-17.1869,'ca','2015-07-08 13:59:58'),(11,'cad','Dolar Canadiense','COP','2111.5777','2015-07-08',4.585,'ca','2015-07-08 13:59:58'),(12,'aud','Dolar Australiano','USD','0.7420','2015-07-08',-6.6307,'au','2015-07-08 13:59:58'),(13,'aud','Dolar Australiano','COP','1996.0913','2015-07-08',13.1761,'au','2015-07-08 13:59:58'),(14,'euro','Euro','USD','1.1058','2015-07-08',-26.0897,'fr','2015-07-08 13:59:58'),(15,'euro','Euro','COP','2974.7679','2015-07-08',-2.6683,'fr','2015-07-08 13:59:58'),(16,'dowjones','Dow Jones','','17536.0200','2015-07-08',52.051,'us','2015-07-08 13:59:58'),(17,'nasdaq','NASDAQ','','4910.7100','2015-07-08',64.2815,'us','2015-07-08 13:59:58'),(18,'syp','S&P 500','','2049.0300','2015-07-08',55.6522,'us','2015-07-08 13:59:58');
/*!40000 ALTER TABLE `economy_test` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-07-08 14:46:07

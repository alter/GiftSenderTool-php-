--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `accountName` varchar(255) DEFAULT NULL,
    `code` varchar(32) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `accountName` (`accountName`,`code`),
    CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`code`) REFERENCES `presents` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;

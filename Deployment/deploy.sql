DROP TABLE IF EXISTS `deployVersionControl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deployVersionControl` (
  `serverType` varchar(100) NOT NULL,
  `version` int NOT NULL,
  `deprecated` boolean NOT NULL default 0,
  PRIMARY KEY (`serverType`, `version`)
) 

-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: sgpc
-- ------------------------------------------------------
-- Server version	9.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `calles`
--

DROP TABLE IF EXISTS `calles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calles` (
  `id_calle` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_calle`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calles`
--

LOCK TABLES `calles` WRITE;
/*!40000 ALTER TABLE `calles` DISABLE KEYS */;
INSERT INTO `calles` VALUES (1,'gallardo'),(2,'bustillo'),(3,'caramillo'),(6,'general galadriel'),(7,'mitre'),(8,'moreno'),(9,'morales'),(10,'diag. capraro'),(11,'12 de octubre');
/*!40000 ALTER TABLE `calles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calles_recorridos`
--

DROP TABLE IF EXISTS `calles_recorridos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calles_recorridos` (
  `id_calle_recorrido` int NOT NULL AUTO_INCREMENT,
  `id_recorrido` int NOT NULL,
  `id_calle` int NOT NULL,
  PRIMARY KEY (`id_calle_recorrido`),
  KEY `calles_recorridos_FK` (`id_recorrido`),
  KEY `calles_recorridos_FK_1` (`id_calle`),
  CONSTRAINT `calles_recorridos_FK` FOREIGN KEY (`id_recorrido`) REFERENCES `recorridos` (`id_recorrido`) ON DELETE CASCADE,
  CONSTRAINT `calles_recorridos_FK_1` FOREIGN KEY (`id_calle`) REFERENCES `calles` (`id_calle`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calles_recorridos`
--

LOCK TABLES `calles_recorridos` WRITE;
/*!40000 ALTER TABLE `calles_recorridos` DISABLE KEYS */;
INSERT INTO `calles_recorridos` VALUES (11,2,3),(13,2,1),(15,5,1),(17,5,6),(18,6,1),(20,6,2),(21,7,1),(22,7,2),(23,7,6),(24,8,1),(25,8,2),(26,8,3),(29,8,6),(30,8,7),(31,8,8),(32,8,9),(33,8,10),(34,8,11);
/*!40000 ALTER TABLE `calles_recorridos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `choferes`
--

DROP TABLE IF EXISTS `choferes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `choferes` (
  `id_chofer` int NOT NULL AUTO_INCREMENT,
  `dni` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `id_nacionalidad` int NOT NULL,
  PRIMARY KEY (`id_chofer`),
  KEY `choferes_FK` (`id_nacionalidad`),
  CONSTRAINT `choferes_FK` FOREIGN KEY (`id_nacionalidad`) REFERENCES `nacionalidades` (`id_nacionalidad`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `choferes`
--

LOCK TABLES `choferes` WRITE;
/*!40000 ALTER TABLE `choferes` DISABLE KEYS */;
INSERT INTO `choferes` VALUES (1,'1111332','Manito','xiaomingopoo',5),(4,'2135134133','elyapo','weon',4),(5,'6624711','cralo','sarlo',45),(6,'12345678','Maximiliano','Cardillo',1);
/*!40000 ALTER TABLE `choferes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresas`
--

DROP TABLE IF EXISTS `empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresas` (
  `id_empresa` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_empresa`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresas`
--

LOCK TABLES `empresas` WRITE;
/*!40000 ALTER TABLE `empresas` DISABLE KEYS */;
INSERT INTO `empresas` VALUES (1,'via bariloche'),(2,'Chevalier');
/*!40000 ALTER TABLE `empresas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hoteles`
--

DROP TABLE IF EXISTS `hoteles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hoteles` (
  `id_hotel` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `direccion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`id_hotel`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hoteles`
--

LOCK TABLES `hoteles` WRITE;
/*!40000 ALTER TABLE `hoteles` DISABLE KEYS */;
INSERT INTO `hoteles` VALUES (1,'aguas del norte','calle pirincho 123');
/*!40000 ALTER TABLE `hoteles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lugares`
--

DROP TABLE IF EXISTS `lugares`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lugares` (
  `id_lugar` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_lugar`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lugares`
--

LOCK TABLES `lugares` WRITE;
/*!40000 ALTER TABLE `lugares` DISABLE KEYS */;
INSERT INTO `lugares` VALUES (1,'El Bolson'),(2,'Bariloche'),(3,'Mendoza'),(4,'Cascada de la virgen'),(5,'Los repollos');
/*!40000 ALTER TABLE `lugares` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nacionalidades`
--

DROP TABLE IF EXISTS `nacionalidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nacionalidades` (
  `id_nacionalidad` int NOT NULL AUTO_INCREMENT,
  `nacionalidad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_nacionalidad`)
) ENGINE=InnoDB AUTO_INCREMENT=184 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nacionalidades`
--

LOCK TABLES `nacionalidades` WRITE;
/*!40000 ALTER TABLE `nacionalidades` DISABLE KEYS */;
INSERT INTO `nacionalidades` VALUES (1,'Argentina'),(2,'Brasil'),(4,'Chile'),(5,'Afganistán'),(6,'Albania'),(7,'Alemania'),(8,'Andorra'),(9,'Angola'),(10,'Antigua y Barbuda'),(11,'Arabia Saudita'),(12,'Argelia'),(13,'Armenia'),(14,'Australia'),(15,'Austria'),(16,'Bahamas'),(17,'Baréin'),(18,'Bangladés'),(19,'Barbados'),(20,'Bélgica'),(21,'Belice'),(22,'Benín'),(23,'Bielorrusia'),(24,'Birmania'),(25,'Bolivia'),(26,'Bosnia y Herzegovina'),(27,'Botsuana'),(28,'Bulgaria'),(29,'Burkina Faso'),(30,'Burundi'),(31,'Cabo Verde'),(32,'Camboya'),(33,'Camerún'),(34,'Canadá'),(35,'Catar'),(36,'Chad'),(37,'China'),(38,'Chipre'),(39,'Colombia'),(40,'Comoras'),(41,'Congo'),(42,'Costa Rica'),(43,'Croacia'),(44,'Cuba'),(45,'Dinamarca'),(46,'Dominica'),(47,'Ecuador'),(48,'Egipto'),(49,'El Salvador'),(50,'Emiratos Árabes Unidos'),(51,'Eslovaquia'),(52,'Eslovenia'),(53,'España'),(54,'Estados Unidos'),(55,'Estonia'),(56,'Etiopía'),(57,'Filipinas'),(58,'Finlandia'),(59,'Francia'),(60,'Gabón'),(61,'Gambia'),(62,'Georgia'),(63,'Ghana'),(64,'Granada'),(65,'Grecia'),(66,'Guatemala'),(67,'Guinea'),(68,'Guinea-Bisáu'),(69,'Guinea Ecuatorial'),(70,'Guyana'),(71,'Haití'),(72,'Honduras'),(73,'Hungría'),(74,'India'),(75,'Indonesia'),(76,'Irak'),(77,'Irán'),(78,'Irlanda'),(79,'Islandia'),(80,'Israel'),(81,'Italia'),(82,'Jamaica'),(83,'Japón'),(84,'Jordania'),(85,'Kazajistán'),(86,'Kenia'),(87,'Kirguistán'),(88,'Kiribati'),(89,'Kuwait'),(90,'Laos'),(91,'Letonia'),(92,'Líbano'),(93,'Liberia'),(94,'Libia'),(95,'Liechtenstein'),(96,'Lituania'),(97,'Luxemburgo'),(98,'Macedonia del Norte'),(99,'Madagascar'),(100,'Malasia'),(101,'Malaui'),(102,'Maldivas'),(103,'Malí'),(104,'Malta'),(105,'Marruecos'),(106,'Mauricio'),(107,'Mauritania'),(108,'México'),(109,'Micronesia'),(110,'Moldavia'),(111,'Mónaco'),(112,'Mongolia'),(113,'Montenegro'),(114,'Mozambique'),(115,'Namibia'),(116,'Nauru'),(117,'Nepal'),(118,'Nicaragua'),(119,'Níger'),(120,'Nigeria'),(121,'Noruega'),(122,'Nueva Zelanda'),(123,'Omán'),(124,'Pakistán'),(125,'Palaos'),(126,'Palestina'),(127,'Panamá'),(128,'Papúa Nueva Guinea'),(129,'Paraguay'),(130,'Perú'),(131,'Polonia'),(132,'Portugal'),(133,'Reino Unido'),(134,'República Centroafricana'),(135,'República Checa'),(136,'República del Congo'),(137,'República Democrática del Congo'),(138,'República Dominicana'),(139,'Ruanda'),(140,'Rumanía'),(141,'Rusia'),(142,'San Cristóbal y Nieves'),(143,'San Marino'),(144,'San Vicente y las Granadinas'),(145,'Santa Lucía'),(146,'Santo Tomé y Príncipe'),(147,'Senegal'),(148,'Serbia'),(149,'Seychelles'),(150,'Sierra Leona'),(151,'Singapur'),(152,'Siria'),(153,'Somalia'),(154,'Sri Lanka'),(155,'Sudáfrica'),(156,'Sudán'),(157,'Sudán del Sur'),(158,'Suecia'),(159,'Suiza'),(160,'Surinam'),(161,'Tailandia'),(162,'Tanzania'),(163,'Tayikistán'),(164,'Timor Oriental'),(165,'Togo'),(166,'Tonga'),(167,'Trinidad y Tobago'),(168,'Túnez'),(169,'Turkmenistán'),(170,'Turquía'),(171,'Tuvalu'),(172,'Ucrania'),(173,'Uganda'),(174,'Uruguay'),(175,'Uzbekistán'),(176,'Vanuatu'),(177,'Vaticano'),(178,'Venezuela'),(179,'Vietnam'),(180,'Yemen'),(181,'Yibuti'),(182,'Zambia'),(183,'Zimbabue');
/*!40000 ALTER TABLE `nacionalidades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permisos`
--

DROP TABLE IF EXISTS `permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permisos` (
  `id_permiso` int NOT NULL AUTO_INCREMENT,
  `id_chofer` int NOT NULL,
  `id_usuario` int NOT NULL,
  `id_servicio` int NOT NULL,
  `tipo` enum('charter','linea','otros') CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `fecha_reserva` timestamp NOT NULL,
  `fecha_emision` timestamp NOT NULL,
  `arribo_salida` enum('arribo','salida') COLLATE utf8mb4_spanish2_ci NOT NULL,
  `observacion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `pasajeros` int NOT NULL,
  `id_lugar` int NOT NULL,
  PRIMARY KEY (`id_permiso`),
  KEY `permisos_FK` (`id_chofer`),
  KEY `permisos_FK_1` (`id_usuario`),
  KEY `permisos_FK_2` (`id_servicio`),
  KEY `permisos_lugares_FK` (`id_lugar`),
  CONSTRAINT `permisos_FK` FOREIGN KEY (`id_chofer`) REFERENCES `choferes` (`id_chofer`),
  CONSTRAINT `permisos_FK_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `permisos_FK_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`),
  CONSTRAINT `permisos_lugares_FK` FOREIGN KEY (`id_lugar`) REFERENCES `lugares` (`id_lugar`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos`
--

LOCK TABLES `permisos` WRITE;
/*!40000 ALTER TABLE `permisos` DISABLE KEYS */;
INSERT INTO `permisos` VALUES (4,1,1,1,'charter','2025-08-08 03:00:00','2025-08-08 17:31:44','arribo','',1,30,4),(5,4,1,1,'charter','2025-08-08 03:00:00','2025-08-08 17:54:33','arribo','',1,30,2),(6,1,1,1,'charter','2025-08-08 03:00:00','2025-08-08 18:02:09','arribo','',1,30,3),(7,6,1,2,'linea','2025-08-13 03:00:00','2025-08-13 15:49:41','salida','Los pasajeros tienen equipos de snowboard.',1,100,1),(8,6,1,2,'charter','2025-08-13 03:00:00','2025-08-13 16:08:36','salida','Permiso de prueba',1,98,2),(9,6,1,2,'charter','2025-08-13 03:00:00','2025-08-13 16:52:08','arribo','Esta observación es larga porque estamos probando el peor caso de tamaño de permiso.',1,101,4);
/*!40000 ALTER TABLE `permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `puntos_detencion`
--

DROP TABLE IF EXISTS `puntos_detencion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `puntos_detencion` (
  `id_punto_detencion` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `id_calle` int NOT NULL,
  PRIMARY KEY (`id_punto_detencion`),
  KEY `puntos_detencion_FK` (`id_calle`),
  CONSTRAINT `puntos_detencion_FK` FOREIGN KEY (`id_calle`) REFERENCES `calles` (`id_calle`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `puntos_detencion`
--

LOCK TABLES `puntos_detencion` WRITE;
/*!40000 ALTER TABLE `puntos_detencion` DISABLE KEYS */;
INSERT INTO `puntos_detencion` VALUES (1,'parada1',1),(2,'parada2',1),(3,'parada3',1),(4,'kiosco la colo',2),(6,'ladronima',2),(8,'aaaa',3),(9,'bbbb',6);
/*!40000 ALTER TABLE `puntos_detencion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recorridos`
--

DROP TABLE IF EXISTS `recorridos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recorridos` (
  `id_recorrido` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_recorrido`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recorridos`
--

LOCK TABLES `recorridos` WRITE;
/*!40000 ALTER TABLE `recorridos` DISABLE KEYS */;
INSERT INTO `recorridos` VALUES (2,'recorridooo'),(4,'rutannanan'),(5,'recorridogod'),(6,'otroreco'),(7,'Vistas'),(8,'Recorrido largo');
/*!40000 ALTER TABLE `recorridos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recorridos_permisos`
--

DROP TABLE IF EXISTS `recorridos_permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recorridos_permisos` (
  `id_recorrido_permiso` int NOT NULL AUTO_INCREMENT,
  `id_permiso` int NOT NULL,
  `id_recorrido` int NOT NULL,
  PRIMARY KEY (`id_recorrido_permiso`),
  KEY `recorridos_permisos_FK` (`id_permiso`),
  KEY `recorridos_permisos_FK_1` (`id_recorrido`),
  CONSTRAINT `recorridos_permisos_FK` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permiso`),
  CONSTRAINT `recorridos_permisos_FK_1` FOREIGN KEY (`id_recorrido`) REFERENCES `recorridos` (`id_recorrido`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recorridos_permisos`
--

LOCK TABLES `recorridos_permisos` WRITE;
/*!40000 ALTER TABLE `recorridos_permisos` DISABLE KEYS */;
INSERT INTO `recorridos_permisos` VALUES (2,4,5),(3,5,5),(4,6,5),(5,7,7),(6,8,7),(7,9,8);
/*!40000 ALTER TABLE `recorridos_permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `remember_tokens`
--

DROP TABLE IF EXISTS `remember_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `remember_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `fecha_expiracion` timestamp NOT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `remember_tokens_FK` (`id_usuario`),
  CONSTRAINT `remember_tokens_FK` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `remember_tokens`
--

LOCK TABLES `remember_tokens` WRITE;
/*!40000 ALTER TABLE `remember_tokens` DISABLE KEYS */;
INSERT INTO `remember_tokens` VALUES (1,1,'fba2b9e619b121810e59d4460a9d5990301584267b12a48e45ac7b26eb00ccf6','2025-08-29 14:05:06','2025-07-30 11:05:06'),(2,1,'26fa8282444f329ff0b81517e3b1e4fcc99b1cc24d23e9def41f3c02305ca700','2025-09-07 14:19:49','2025-08-08 11:19:49'),(3,1,'00668e08873f76d5e0594276f6c16def91c35e43165cf9e3abf8d59d62893996','2025-09-07 17:53:37','2025-08-08 14:53:37'),(4,1,'be3adbec0bccba4911bb9089526bc72d53abca49c9fa5ad4099307fa2c26100a','2025-09-12 14:19:26','2025-08-13 11:19:26');
/*!40000 ALTER TABLE `remember_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservas_puntos`
--

DROP TABLE IF EXISTS `reservas_puntos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservas_puntos` (
  `id_reserva_punto` int NOT NULL AUTO_INCREMENT,
  `fecha_horario` timestamp NOT NULL,
  `id_hotel` int DEFAULT NULL,
  `id_permiso` int NOT NULL,
  `id_punto_detencion` int NOT NULL,
  PRIMARY KEY (`id_reserva_punto`),
  KEY `reservas_puntos_FK` (`id_punto_detencion`),
  KEY `reservas_puntos_FK_1` (`id_hotel`),
  KEY `reservas_puntos_FK_2` (`id_permiso`),
  CONSTRAINT `reservas_puntos_FK` FOREIGN KEY (`id_punto_detencion`) REFERENCES `puntos_detencion` (`id_punto_detencion`),
  CONSTRAINT `reservas_puntos_FK_1` FOREIGN KEY (`id_hotel`) REFERENCES `hoteles` (`id_hotel`),
  CONSTRAINT `reservas_puntos_FK_2` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permiso`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservas_puntos`
--

LOCK TABLES `reservas_puntos` WRITE;
/*!40000 ALTER TABLE `reservas_puntos` DISABLE KEYS */;
INSERT INTO `reservas_puntos` VALUES (1,'2025-08-08 16:15:00',1,5,1),(2,'2025-08-08 18:30:00',NULL,5,9),(3,'2025-08-08 16:15:00',1,6,1),(4,'2025-08-08 18:30:00',NULL,6,9),(5,'2025-08-13 20:30:00',1,7,1),(6,'2025-08-13 12:15:00',NULL,7,4),(7,'2025-08-13 20:30:00',1,8,1),(8,'2025-08-13 12:30:00',NULL,8,2),(9,'2025-08-13 18:30:00',1,9,4),(10,'2025-08-13 11:15:00',NULL,9,8);
/*!40000 ALTER TABLE `reservas_puntos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servicios`
--

DROP TABLE IF EXISTS `servicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `servicios` (
  `id_servicio` int NOT NULL AUTO_INCREMENT,
  `id_empresa` int NOT NULL,
  `interno` int NOT NULL,
  `dominio` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_servicio`),
  KEY `servicios_FK` (`id_empresa`),
  CONSTRAINT `servicios_FK` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicios`
--

LOCK TABLES `servicios` WRITE;
/*!40000 ALTER TABLE `servicios` DISABLE KEYS */;
INSERT INTO `servicios` VALUES (1,1,5587,'lla321'),(2,2,7998,'ABC123');
/*!40000 ALTER TABLE `servicios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_usuarios`
--

DROP TABLE IF EXISTS `tipos_usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_usuarios` (
  `id_tipo_usuario` int NOT NULL AUTO_INCREMENT,
  `tipo_usuario` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_tipo_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_usuarios`
--

LOCK TABLES `tipos_usuarios` WRITE;
/*!40000 ALTER TABLE `tipos_usuarios` DISABLE KEYS */;
INSERT INTO `tipos_usuarios` VALUES (1,'admin');
/*!40000 ALTER TABLE `tipos_usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `cargo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `sector` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `contrasenia` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `id_tipo_usuario` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_usuario`),
  KEY `usuarios_FK` (`id_tipo_usuario`),
  CONSTRAINT `usuarios_FK` FOREIGN KEY (`id_tipo_usuario`) REFERENCES `tipos_usuarios` (`id_tipo_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'jonas','Jonas','Porro','admin','presidencia','$2y$12$f7EdIv1LIE9wsVw7uM.BaeGFERko1CX3CdtyYWg0cDue6l1ecBSme',1,1);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'sgpc'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-13 11:49:27

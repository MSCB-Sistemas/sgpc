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
  PRIMARY KEY (`id_calle`),
  UNIQUE KEY `calles_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calles`
--

LOCK TABLES `calles` WRITE;
/*!40000 ALTER TABLE `calles` DISABLE KEYS */;
INSERT INTO `calles` VALUES (5,'Av. 12 de Octubre'),(27,'Av. Bustillo'),(29,'Beschtedt'),(53,'Elflein'),(28,'Moreno'),(54,'Onelli'),(55,'Quaglia'),(1,'Rolando'),(26,'Vice Almirante O\'Connor');
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
  CONSTRAINT `calles_recorridos_FK` FOREIGN KEY (`id_recorrido`) REFERENCES `recorridos` (`id_recorrido`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `calles_recorridos_FK_1` FOREIGN KEY (`id_calle`) REFERENCES `calles` (`id_calle`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calles_recorridos`
--

LOCK TABLES `calles_recorridos` WRITE;
/*!40000 ALTER TABLE `calles_recorridos` DISABLE KEYS */;
INSERT INTO `calles_recorridos` VALUES (4,2,1),(5,2,5),(6,2,28),(7,2,27),(8,3,1),(9,3,27),(31,1,5),(32,1,27),(33,1,28),(45,11,5),(46,11,27),(47,11,28),(48,10,1),(49,9,26),(50,9,54),(51,9,28),(52,12,1),(53,12,27);
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
  UNIQUE KEY `choferes_unique` (`dni`,`id_nacionalidad`),
  KEY `choferes_FK` (`id_nacionalidad`),
  CONSTRAINT `choferes_FK` FOREIGN KEY (`id_nacionalidad`) REFERENCES `nacionalidades` (`id_nacionalidad`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `choferes`
--

LOCK TABLES `choferes` WRITE;
/*!40000 ALTER TABLE `choferes` DISABLE KEYS */;
INSERT INTO `choferes` VALUES (1,'233223323','Juan','Pérez',1),(4,'111888979','María','Herrera',1),(5,'43904897','Carlos','Martínez',1),(7,'232132132','Ana','Morales',1),(9,'23322332','Luis','Castillo',1),(12,'233223321','Diego','Díaz',1);
/*!40000 ALTER TABLE `choferes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `derechos`
--

DROP TABLE IF EXISTS `derechos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `derechos` (
  `id_derecho` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id_derecho`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `derechos`
--

LOCK TABLES `derechos` WRITE;
/*!40000 ALTER TABLE `derechos` DISABLE KEYS */;
INSERT INTO `derechos` VALUES (1,'editar abm'),(2,'editar usuarios'),(3,'god'),(4,'eliminar usuarios'),(5,'ver estadisticas'),(6,'cargar permiso'),(7,'eliminar permiso'),(8,'borrar abm'),(9,'cargar abm'),(10,'ver abm'),(11,'crear usuarios');
/*!40000 ALTER TABLE `derechos` ENABLE KEYS */;
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
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_empresa`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresas`
--

LOCK TABLES `empresas` WRITE;
/*!40000 ALTER TABLE `empresas` DISABLE KEYS */;
INSERT INTO `empresas` VALUES (1,'Via Bariloche',1),(8,'Crucero del Norte',1),(9,'Chevallier',1),(10,'Flecha Bus',1),(62,'Andesmar',1);
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
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_hotel`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hoteles`
--

LOCK TABLES `hoteles` WRITE;
/*!40000 ALTER TABLE `hoteles` DISABLE KEYS */;
INSERT INTO `hoteles` VALUES (1,'Alma del Lago Suites & Spa','Bustillo 1151',1),(2,'Design Suites Bariloche','Bustillo km 2.5',1),(4,'Llao Llao','Bustillo km 25',1),(44,'Villa Huinid','Bustillo 2600',1),(45,'Nido del Cóndor Hotel & Spa','Bustillo km 6.9',1);
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
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_lugar`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lugares`
--

LOCK TABLES `lugares` WRITE;
/*!40000 ALTER TABLE `lugares` DISABLE KEYS */;
INSERT INTO `lugares` VALUES (1,'Junin de los Andes',1),(2,'El Bolsón',1),(41,'El Hoyo',1),(50,'Lago Puelo',1),(51,'Esquel',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=191 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nacionalidades`
--

LOCK TABLES `nacionalidades` WRITE;
/*!40000 ALTER TABLE `nacionalidades` DISABLE KEYS */;
INSERT INTO `nacionalidades` VALUES (1,'Argentina'),(10,'Brasil'),(11,'Chile'),(12,'Afganistán'),(13,'Albania'),(14,'Alemania'),(15,'Andorra'),(16,'Angola'),(17,'Antigua y Barbuda'),(18,'Arabia Saudita'),(19,'Argelia'),(20,'Armenia'),(21,'Australia'),(22,'Austria'),(23,'Bahamas'),(24,'Baréin'),(25,'Bangladés'),(26,'Barbados'),(27,'Bélgica'),(28,'Belice'),(29,'Benín'),(30,'Bielorrusia'),(31,'Birmania'),(32,'Bolivia'),(33,'Bosnia y Herzegovina'),(34,'Botsuana'),(35,'Bulgaria'),(36,'Burkina Faso'),(37,'Burundi'),(38,'Cabo Verde'),(39,'Camboya'),(40,'Camerún'),(41,'Canadá'),(42,'Catar'),(43,'Chad'),(44,'China'),(45,'Chipre'),(46,'Colombia'),(47,'Comoras'),(48,'Congo'),(49,'Costa Rica'),(50,'Croacia'),(51,'Cuba'),(52,'Dinamarca'),(53,'Dominica'),(54,'Ecuador'),(55,'Egipto'),(56,'El Salvador'),(57,'Emiratos Árabes Unidos'),(58,'Eslovaquia'),(59,'Eslovenia'),(60,'España'),(61,'Estados Unidos'),(62,'Estonia'),(63,'Etiopía'),(64,'Filipinas'),(65,'Finlandia'),(66,'Francia'),(67,'Gabón'),(68,'Gambia'),(69,'Georgia'),(70,'Ghana'),(71,'Granada'),(72,'Grecia'),(73,'Guatemala'),(74,'Guinea'),(75,'Guinea-Bisáu'),(76,'Guinea Ecuatorial'),(77,'Guyana'),(78,'Haití'),(79,'Honduras'),(80,'Hungría'),(81,'India'),(82,'Indonesia'),(83,'Irak'),(84,'Irán'),(85,'Irlanda'),(86,'Islandia'),(87,'Israel'),(88,'Italia'),(89,'Jamaica'),(90,'Japón'),(91,'Jordania'),(92,'Kazajistán'),(93,'Kenia'),(94,'Kirguistán'),(95,'Kiribati'),(96,'Kuwait'),(97,'Laos'),(98,'Letonia'),(99,'Líbano'),(100,'Liberia'),(101,'Libia'),(102,'Liechtenstein'),(103,'Lituania'),(104,'Luxemburgo'),(105,'Macedonia del Norte'),(106,'Madagascar'),(107,'Malasia'),(108,'Malaui'),(109,'Maldivas'),(110,'Malí'),(111,'Malta'),(112,'Marruecos'),(113,'Mauricio'),(114,'Mauritania'),(115,'México'),(116,'Micronesia'),(117,'Moldavia'),(118,'Mónaco'),(119,'Mongolia'),(120,'Montenegro'),(121,'Mozambique'),(122,'Namibia'),(123,'Nauru'),(124,'Nepal'),(125,'Nicaragua'),(126,'Níger'),(127,'Nigeria'),(128,'Noruega'),(129,'Nueva Zelanda'),(130,'Omán'),(131,'Pakistán'),(132,'Palaos'),(133,'Palestina'),(134,'Panamá'),(135,'Papúa Nueva Guinea'),(136,'Paraguay'),(137,'Perú'),(138,'Polonia'),(139,'Portugal'),(140,'Reino Unido'),(141,'República Centroafricana'),(142,'República Checa'),(143,'República del Congo'),(144,'República Democrática del Congo'),(145,'República Dominicana'),(146,'Ruanda'),(147,'Rumanía'),(148,'Rusia'),(149,'San Cristóbal y Nieves'),(150,'San Marino'),(151,'San Vicente y las Granadinas'),(152,'Santa Lucía'),(153,'Santo Tomé y Príncipe'),(154,'Senegal'),(155,'Serbia'),(156,'Seychelles'),(157,'Sierra Leona'),(158,'Singapur'),(159,'Siria'),(160,'Somalia'),(161,'Sri Lanka'),(162,'Sudáfrica'),(163,'Sudán'),(164,'Sudán del Sur'),(165,'Suecia'),(166,'Suiza'),(167,'Surinam'),(168,'Tailandia'),(169,'Tanzania'),(170,'Tayikistán'),(171,'Timor Oriental'),(172,'Togo'),(173,'Tonga'),(174,'Trinidad y Tobago'),(175,'Túnez'),(176,'Turkmenistán'),(177,'Turquía'),(178,'Tuvalu'),(179,'Ucrania'),(180,'Uganda'),(181,'Uruguay'),(182,'Uzbekistán'),(183,'Vanuatu'),(184,'Vaticano'),(185,'Venezuela'),(186,'Vietnam'),(187,'Yemen'),(188,'Yibuti'),(189,'Zambia'),(190,'Zimbabue');
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
  `arribo_salida` enum('arribo','salida') CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos`
--

LOCK TABLES `permisos` WRITE;
/*!40000 ALTER TABLE `permisos` DISABLE KEYS */;
INSERT INTO `permisos` VALUES (2,7,1,3,'charter','2025-08-12 03:00:00','2025-08-11 14:23:00','salida','ojo con este',1,15,2),(3,1,1,2,'charter','2025-08-13 03:00:00','2025-08-11 14:42:05','arribo','',1,52,1),(4,1,1,2,'charter','2025-08-13 03:00:00','2025-08-11 14:42:40','arribo','',1,52,1),(5,5,1,2,'charter','2025-08-11 03:00:00','2025-08-11 16:05:41','arribo','',1,6556,2),(6,5,1,2,'charter','2025-08-11 03:00:00','2025-08-11 16:05:53','arribo','',1,6556,2),(7,1,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:06:34','arribo','',0,54654,1),(8,4,1,2,'charter','2025-08-11 03:00:00','2025-08-11 16:08:03','arribo','',1,454564,1),(9,4,1,3,'charter','2025-08-11 03:00:00','2025-08-11 16:08:58','arribo','12',1,123,2),(10,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','',1,78,1),(11,1,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:06:34','arribo','',1,54654,1),(12,4,1,2,'charter','2025-08-11 03:00:00','2025-08-11 16:08:03','arribo','',1,454564,1),(13,4,1,3,'charter','2025-08-11 03:00:00','2025-08-11 16:08:58','arribo','12',1,123,2),(14,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','',1,78,1),(15,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','',1,78,1),(16,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','',1,78,1),(17,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','',1,78,1),(18,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','',1,78,1),(19,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','',1,78,1),(20,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','',1,78,1),(21,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','',1,78,1),(22,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','',1,78,1),(23,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','Requiere permiso especial',1,78,1),(24,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','',1,78,1),(25,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','',1,78,1),(26,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','Abandonar dársena antes de las 15hs',1,78,1),(27,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','',1,78,1),(28,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','',1,78,1),(29,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','',1,78,1),(30,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','',1,78,1),(31,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','',1,78,1),(32,4,1,1,'charter','2025-08-11 03:00:00','2025-08-11 16:19:56','arribo','',1,78,1),(33,1,1,1,'charter','2025-08-15 03:00:00','2025-08-14 15:03:35','arribo','Equipos de deportes de nieve',0,33,1);
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
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_punto_detencion`),
  KEY `puntos_detencion_FK` (`id_calle`),
  CONSTRAINT `puntos_detencion_FK` FOREIGN KEY (`id_calle`) REFERENCES `calles` (`id_calle`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `puntos_detencion`
--

LOCK TABLES `puntos_detencion` WRITE;
/*!40000 ALTER TABLE `puntos_detencion` DISABLE KEYS */;
INSERT INTO `puntos_detencion` VALUES (1,'Garita 123',1,1),(2,'Garita 54',1,1),(3,'Correo',5,1),(9,'Maxikiosco Buggy',29,1),(10,'Shopping',54,1),(11,'Viajes Condor',54,1);
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
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_recorrido`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recorridos`
--

LOCK TABLES `recorridos` WRITE;
/*!40000 ALTER TABLE `recorridos` DISABLE KEYS */;
INSERT INTO `recorridos` VALUES (1,'ruta 1',0),(2,'recorrido1',0),(3,'ramiro',0),(9,'Bosques',1),(10,'Comercial',1),(11,'Vistas del lago',1),(12,'Villa La Angostura',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recorridos_permisos`
--

LOCK TABLES `recorridos_permisos` WRITE;
/*!40000 ALTER TABLE `recorridos_permisos` DISABLE KEYS */;
INSERT INTO `recorridos_permisos` VALUES (1,2,2),(2,3,3),(3,4,3),(4,5,2),(5,6,2),(6,7,2),(7,8,2),(8,9,2),(9,10,2),(10,33,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `remember_tokens`
--

LOCK TABLES `remember_tokens` WRITE;
/*!40000 ALTER TABLE `remember_tokens` DISABLE KEYS */;
INSERT INTO `remember_tokens` VALUES (31,1,'2db7c2ae57fb0fbfda5d47446493a119550e423b9d71b31136720698c8df402a','2025-09-04 15:53:59','2025-08-05 12:53:59'),(32,1,'d2fd78a8b738679002df190baaaa6c75ecf742c2b851b5c327d123f8632aeafd','2025-09-04 15:54:18','2025-08-05 12:54:18'),(33,1,'prueba','2025-09-04 15:53:59','2025-08-05 12:53:59'),(35,1,'7b3bb1f43edc34b730ef85cfd9dd3f737233297b5878d08622faea42f47aa766','2025-09-05 14:26:59','2025-08-06 11:26:59'),(39,1,'388c1a5480d328ea9171b4513301700d0c86b9bbd788ffe9d6c8845df08dfcc5','2025-09-20 15:32:42','2025-08-21 12:32:42');
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservas_puntos`
--

LOCK TABLES `reservas_puntos` WRITE;
/*!40000 ALTER TABLE `reservas_puntos` DISABLE KEYS */;
INSERT INTO `reservas_puntos` VALUES (1,'2025-08-12 18:30:00',2,2,1),(2,'2025-08-13 16:15:00',1,3,1),(3,'2025-12-13 16:15:00',1,4,1),(4,'2025-08-11 06:30:00',NULL,10,1),(5,'2024-08-13 16:15:00',2,2,1),(6,'2025-12-13 16:15:00',1,4,2),(7,'2025-12-13 16:15:00',1,4,1),(8,'2025-12-13 16:15:00',1,4,2),(9,'2025-12-13 16:15:00',1,4,1),(10,'2025-12-13 16:15:00',1,4,1),(11,'2025-12-13 16:15:00',1,4,2),(12,'2025-12-13 16:15:00',1,4,1),(13,'2025-12-13 16:15:00',1,4,1),(14,'2025-12-13 16:15:00',1,4,1),(15,'2025-12-13 16:15:00',1,4,1),(16,'2025-08-15 14:15:00',1,33,3);
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
  UNIQUE KEY `servicios_unique` (`id_empresa`,`interno`,`dominio`),
  KEY `servicios_FK` (`id_empresa`),
  CONSTRAINT `servicios_FK` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicios`
--

LOCK TABLES `servicios` WRITE;
/*!40000 ALTER TABLE `servicios` DISABLE KEYS */;
INSERT INTO `servicios` VALUES (1,1,34333223,'543AXX'),(11,1,34333223,'AZ132CU'),(2,9,93903039,'993ABV'),(3,10,6325,'abb789');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_usuarios`
--

LOCK TABLES `tipos_usuarios` WRITE;
/*!40000 ALTER TABLE `tipos_usuarios` DISABLE KEYS */;
INSERT INTO `tipos_usuarios` VALUES (1,'admin'),(2,'director'),(3,'operario'),(4,'invitado');
/*!40000 ALTER TABLE `tipos_usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_usuarios_derechos`
--

DROP TABLE IF EXISTS `tipos_usuarios_derechos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_usuarios_derechos` (
  `id_tipo_usuario_derecho` int NOT NULL AUTO_INCREMENT,
  `id_tipo_usuario` int NOT NULL,
  `id_derecho` int NOT NULL,
  PRIMARY KEY (`id_tipo_usuario_derecho`),
  KEY `tipos_usuarios_derechos_tipos_usuarios_FK` (`id_tipo_usuario`),
  KEY `tipos_usuarios_derechos_derechos_FK` (`id_derecho`),
  CONSTRAINT `tipos_usuarios_derechos_derechos_FK` FOREIGN KEY (`id_derecho`) REFERENCES `derechos` (`id_derecho`),
  CONSTRAINT `tipos_usuarios_derechos_tipos_usuarios_FK` FOREIGN KEY (`id_tipo_usuario`) REFERENCES `tipos_usuarios` (`id_tipo_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_usuarios_derechos`
--

LOCK TABLES `tipos_usuarios_derechos` WRITE;
/*!40000 ALTER TABLE `tipos_usuarios_derechos` DISABLE KEYS */;
INSERT INTO `tipos_usuarios_derechos` VALUES (1,1,3),(2,2,1),(3,2,2),(4,2,4),(5,2,5),(6,2,6),(7,2,7),(8,2,8),(9,2,9),(10,3,5),(11,3,10),(12,3,6),(13,3,9),(14,4,5),(16,1,1),(17,1,2),(18,1,4),(19,1,5),(20,1,6),(21,1,3),(22,1,8),(23,1,9),(24,1,10),(25,2,10),(26,1,7),(27,1,11),(28,2,11);
/*!40000 ALTER TABLE `tipos_usuarios_derechos` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'admin','admin','admin','admin','administracion','$2y$12$nyBLCPBCXiYrb1.0iFqyW.XNlVnvaGs4/duTYLP4pSceVjF.KkuqK',1,1),(6,'operario','operario','a','a','a','$2y$12$LOuYBbjJ.p6eV6jrVXpgUudAx3fw8LYV.t1lS/NXzUDczCYQV9k0u',3,1),(7,'invitado','Invitado','A','B','B','$2y$12$xjLfdttcELU1r/W1Cl8Hs.FW31.QJT2hbS6IYQRbexT34rpPEmqRa',4,1),(8,'director','director','A','A','A','$2y$12$nyBLCPBCXiYrb1.0iFqyW.XNlVnvaGs4/duTYLP4pSceVjF.KkuqK',2,1);
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

-- Dump completed on 2025-08-28 10:28:25

-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: curso_social_network
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `curso_social_network`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `curso_social_network` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `curso_social_network`;

--
-- Table structure for table `following`
--

DROP TABLE IF EXISTS `following`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `following` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user` int(255) DEFAULT NULL,
  `followed` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_following_users` (`user`),
  KEY `fk_followed` (`followed`),
  CONSTRAINT `fk_followed` FOREIGN KEY (`followed`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_following_users` FOREIGN KEY (`user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `following`
--

LOCK TABLES `following` WRITE;
/*!40000 ALTER TABLE `following` DISABLE KEYS */;
INSERT INTO `following` VALUES (6,10,11),(12,8,10),(13,8,9),(14,8,11),(15,8,3),(16,11,1),(17,11,10),(18,11,2),(19,9,10),(20,9,11),(21,9,8),(22,9,2),(23,9,3),(24,9,4),(25,10,9),(26,1,11),(27,1,10),(28,1,8),(29,10,8),(30,8,1),(32,10,1),(37,10,4);
/*!40000 ALTER TABLE `following` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `likes`
--

DROP TABLE IF EXISTS `likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `likes` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user` int(255) DEFAULT NULL,
  `publication` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_likes_users` (`user`),
  KEY `fk_likes_publication` (`publication`),
  CONSTRAINT `fk_likes_publication` FOREIGN KEY (`publication`) REFERENCES `publications` (`id`),
  CONSTRAINT `fk_likes_users` FOREIGN KEY (`user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `likes`
--

LOCK TABLES `likes` WRITE;
/*!40000 ALTER TABLE `likes` DISABLE KEYS */;
INSERT INTO `likes` VALUES (5,10,22),(6,10,21),(7,10,19);
/*!40000 ALTER TABLE `likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `type_id` int(255) DEFAULT NULL,
  `readed` varchar(3) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `extra` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_notifications_users` (`user_id`),
  CONSTRAINT `fk_notifications_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `private_messages`
--

DROP TABLE IF EXISTS `private_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `private_messages` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `message` longtext DEFAULT NULL,
  `emitter` int(255) DEFAULT NULL,
  `receiver` int(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `readed` varchar(3) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_emmiter_privates` (`emitter`),
  KEY `fk_receiver_privates` (`receiver`),
  CONSTRAINT `fk_emmiter_privates` FOREIGN KEY (`emitter`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_receiver_privates` FOREIGN KEY (`receiver`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `private_messages`
--

LOCK TABLES `private_messages` WRITE;
/*!40000 ALTER TABLE `private_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `private_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publications`
--

DROP TABLE IF EXISTS `publications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publications` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) DEFAULT NULL,
  `text` mediumtext DEFAULT NULL,
  `document` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_publications_users` (`user_id`),
  CONSTRAINT `fk_publications_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publications`
--

LOCK TABLES `publications` WRITE;
/*!40000 ALTER TABLE `publications` DISABLE KEYS */;
INSERT INTO `publications` VALUES (2,10,'prueba 2 sin pic ni dox',NULL,NULL,'','2023-11-21 00:03:16'),(3,10,'prueba 3 sin dox pero con pic (que bonita la ina)',NULL,'101700521461.jpeg','','2023-11-21 00:04:21'),(5,10,'prueba 5 con pic y doc','101700521763.xlsx','101700521763.jpeg','','2023-11-21 00:09:23'),(6,8,'Soy la segunda cuenta admin pero yo si soy el chambeador no como',NULL,'81700522321.jpeg','','2023-11-21 00:18:41'),(7,8,'Tengo hambre',NULL,NULL,'','2023-11-21 00:19:27'),(8,11,'Soy Jose Angel Matos, soy docente en la academia generica',NULL,'111700522437.jpeg','','2023-11-21 00:20:37'),(9,11,'Buenos dias a todos, menos a ti cabron @superadmin',NULL,NULL,'','2023-11-21 00:21:05'),(10,9,'Soy un random sin foto, buenos dias',NULL,NULL,'','2023-11-21 00:21:54'),(11,1,'Buenas, espero que estes disfrutando mi curso!!',NULL,NULL,'','2023-11-21 19:18:09'),(12,1,'Cualquier duda, no dudes en escribirme!!',NULL,NULL,'','2023-11-21 19:18:26'),(16,8,'Adjunto informe de predefensa','81700599536.docx',NULL,'','2023-11-21 21:45:36'),(18,11,'Terminado el ppt de la defensa','111700599960.pptx',NULL,'','2023-11-21 21:52:40'),(19,1,'Terminado el curso? Aca dejo un material de apoyo','11700600047.zip',NULL,'','2023-11-21 21:54:07'),(20,9,'APURENSE','91700600137.pdf',NULL,'','2023-11-21 21:55:37'),(21,10,'hola desde la maquina de Luis',NULL,'101700601326.jpeg','','2023-11-21 22:15:26'),(22,10,'Probando documento','101700601376.pdf','101700601376.png','','2023-11-21 22:16:16');
/*!40000 ALTER TABLE `publications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `role` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nick` varchar(50) DEFAULT NULL,
  `bio` varchar(255) DEFAULT NULL,
  `active` varchar(2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_uniques_fields` (`email`,`nick`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'ROLE ADMIN','admin@admin.com','Victor','Robles','$2y$04$0c2vKvN7Q5QTE2NrkAuF1eU7ESl4jEyNun7CVHU.ZzZwF0UgTgKDu','admin','Soy el que imparte el curso que estas viendo!!','1',NULL),(2,'ROLE_USER','williams@suarez.com','Williams','Suarez','$2y$04$ZMrFwz4OB1v6HgOs9X8tZePdnYu/Brb2u32gFnI6UjirgtsH7BksW','will_suarez','NULL','NU',NULL),(3,'ROLE_USER','juan@lopez.com','Juan','Lopez','$2y$04$eAx.PW1NWz.tmGkeunaMpupVa4xhb1DOl5UOSvfXogNMcPEM0EPwK','juan_lopez','NULL','NU',NULL),(4,'ROLE_USER','manuel@lopez.com','Manuel','Lopez','$2y$04$Cn4l/dHUcyqANfn5O/agyeqNQ2MaUYX.XRl.5idmgIRxvU0Yak2e6','manu_lopez','NULL','NU',NULL),(8,'ROLE_USER','admin2@admin.com','Will Admin','Segundo','$2y$04$iQVW5TxWGnpHZYpIJY6mb.W.i09cValpv0jcK0qOuuexK00L2I0GS','admin2','bio','','81700065969.jpeg'),(9,'ROLE_USER','admin3@admin.com','Administrador','Tercero','$2y$04$S.UBqdK5wVwfou0i65Eu3O4peRmVkJXwtoqrB8f.GauRv2Azngw2K','admin3','','',NULL),(10,'ROLE_USER','superadmin@admin.com','Williams','Suarez','$2y$04$0c2vKvN7Q5QTE2NrkAuF1eU7ESl4jEyNun7CVHU.ZzZwF0UgTgKDu','superadmin','cuenta de superadmin de prueba','','101700068328.jpeg'),(11,'ROLE_USER','jose_angel@matos.com','Jose Angel','Matos','$2y$04$umEVxckWsCeOTomo0pRGLe99qFJ4uQyaXOONYHE22pw36VijWjjVa','jose_matos','biografia','','111700065427.jpeg');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'curso_social_network'
--

--
-- Dumping routines for database 'curso_social_network'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-11-22  8:12:05

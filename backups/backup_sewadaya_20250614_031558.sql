-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: sewadaya
-- ------------------------------------------------------
-- Server version	8.0.30

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
-- Table structure for table `clothing_items`
--

DROP TABLE IF EXISTS `clothing_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clothing_items` (
  `item_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `size` enum('anak-anak','dewasa') NOT NULL,
  `rental_price_per_day` decimal(10,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clothing_items`
--

LOCK TABLES `clothing_items` WRITE;
/*!40000 ALTER TABLE `clothing_items` DISABLE KEYS */;
INSERT INTO `clothing_items` VALUES (5,'baju kurung','pakaian adat lampung','dewasa',50000.00,4,'uploads/img_684c6e6a717a3.jpg','2025-06-13 18:31:06'),(9,'king baba','baju adat dayak untuk laki laki','dewasa',300000.00,3,'uploads/img_684cc3878878b.jpg','2025-06-14 00:34:15'),(11,'baju adat bali','buat cowo','anak-anak',10000.00,5,'uploads/img_684cd7bda2cca.jpg','2025-06-14 02:00:29');
/*!40000 ALTER TABLE `clothing_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `rental_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  PRIMARY KEY (`payment_id`),
  KEY `rental_id` (`rental_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`rental_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rental_status_log`
--

DROP TABLE IF EXISTS `rental_status_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rental_status_log` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `rental_id` int DEFAULT NULL,
  `old_status` enum('pending','rented','returned','cancelled') DEFAULT NULL,
  `new_status` enum('pending','rented','returned','cancelled') DEFAULT NULL,
  `change_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rental_status_log`
--

LOCK TABLES `rental_status_log` WRITE;
/*!40000 ALTER TABLE `rental_status_log` DISABLE KEYS */;
INSERT INTO `rental_status_log` VALUES (1,1,'pending','cancelled','2025-06-13 23:49:23'),(3,3,'pending','cancelled','2025-06-13 23:50:35'),(5,5,'pending','cancelled','2025-06-13 23:51:36'),(7,7,'pending','cancelled','2025-06-13 23:58:15'),(9,9,'pending','rented','2025-06-14 00:01:14'),(11,9,'rented','returned','2025-06-14 00:01:29'),(13,11,'pending','rented','2025-06-14 00:29:01'),(15,17,'pending','rented','2025-06-14 01:38:40'),(17,13,'pending','rented','2025-06-14 01:38:44'),(19,15,'pending','rented','2025-06-14 01:38:46'),(21,19,'pending','rented','2025-06-14 02:02:12'),(23,11,'rented','returned','2025-06-14 02:02:18'),(25,13,'rented','returned','2025-06-14 02:02:26'),(27,15,'rented','returned','2025-06-14 03:14:01'),(29,23,'pending','rented','2025-06-14 03:15:34');
/*!40000 ALTER TABLE `rental_status_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rentals`
--

DROP TABLE IF EXISTS `rentals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rentals` (
  `rental_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `item_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `rental_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `actual_return_date` date DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','rented','returned','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`rental_id`),
  KEY `user_id` (`user_id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `rentals_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `clothing_items` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rentals`
--

LOCK TABLES `rentals` WRITE;
/*!40000 ALTER TABLE `rentals` DISABLE KEYS */;
INSERT INTO `rentals` VALUES (1,1,5,1,'2025-06-13','2025-06-14',NULL,50000.00,'cancelled','2025-06-13 23:48:42'),(3,1,5,1,'2025-06-15','2025-06-16',NULL,50000.00,'cancelled','2025-06-13 23:50:13'),(5,1,5,1,'2025-06-16','2025-06-18',NULL,100000.00,'cancelled','2025-06-13 23:51:10'),(7,1,5,1,'2025-06-18','2025-06-20',NULL,100000.00,'cancelled','2025-06-13 23:57:34'),(9,1,5,1,'2025-06-23','2025-06-25','2025-06-14',100000.00,'returned','2025-06-14 00:00:20'),(11,1,5,1,'2025-06-15','2025-06-17','2025-06-14',100000.00,'returned','2025-06-14 00:27:19'),(13,7,9,1,'2025-06-14','2025-06-15','2025-06-14',300000.00,'returned','2025-06-14 00:35:12'),(15,7,9,1,'2025-06-14','2025-06-15','2025-06-14',300000.00,'returned','2025-06-14 00:36:22'),(17,7,5,1,'2025-06-14','2025-06-18',NULL,200000.00,'rented','2025-06-14 01:20:21'),(19,7,5,5,'2025-06-14','2025-06-15',NULL,50000.00,'rented','2025-06-14 01:44:44'),(21,1,11,5,'2025-06-14','2025-06-16',NULL,20000.00,'pending','2025-06-14 02:03:18'),(23,9,9,2,'2025-06-14','2025-06-15',NULL,300000.00,'rented','2025-06-14 03:14:58');
/*!40000 ALTER TABLE `rentals` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `BeforeInsertRental` BEFORE INSERT ON `rentals` FOR EACH ROW BEGIN
    DECLARE v_rental_price_per_day DECIMAL(10, 2);
    DECLARE v_days INT;

    -- Dapatkan harga sewa per hari dari item yang terkait
    SELECT rental_price_per_day INTO v_rental_price_per_day
    FROM clothing_items
    WHERE item_id = NEW.item_id;

    -- Hitung jumlah hari penyewaan. Jika kurang dari 1 hari, anggap 1 hari.
    SET v_days = DATEDIFF(NEW.return_date, NEW.rental_date);
    IF v_days < 1 THEN
        SET v_days = 1;
    END IF;

    -- Set total_price untuk baris yang akan dimasukkan
    SET NEW.total_price = v_days * v_rental_price_per_day;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `AfterUpdateRentalStatus` AFTER UPDATE ON `rentals` FOR EACH ROW BEGIN
    -- Jika status lama berbeda dengan status baru, masukkan catatan log
    IF OLD.status <> NEW.status THEN
        INSERT INTO rental_status_log (rental_id, old_status, new_status)
        VALUES (OLD.rental_id, OLD.status, NEW.status);
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'maw','$2y$10$dNwWeXawiX1sckqXoQL0g.Pw4HD3MQsXpznhWX.l43hzfL/LSGp0m','maw@gmail.com','customer','2025-06-13 16:55:51'),(3,'admin','$2y$10$no11HKYJgbyuzw8SCh5ogeWxdRkyqAgpuPVA6BLCN30J1KXUNDsgy','admin@example.com','admin','2025-06-13 17:39:44'),(7,'citra','$2y$10$hECV4fZzv4lxeccYJ63vse91qiECdYsYJViSy0X3myW0veE7a.YvK','citrafardiani@gmail.com','customer','2025-06-13 22:50:25'),(9,'adin','$2y$10$smJkqILucTLrO8eMoq94qOlLIOlE46Z/xhkGSpc/aeG4FFZgoregq','adin@gmail.com','customer','2025-06-14 02:01:45');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-14 10:15:59

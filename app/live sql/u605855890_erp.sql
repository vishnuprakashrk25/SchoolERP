-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: erp_school
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `academic_years`
--

DROP TABLE IF EXISTS `academic_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_years` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_years`
--

LOCK TABLES `academic_years` WRITE;
/*!40000 ALTER TABLE `academic_years` DISABLE KEYS */;
INSERT INTO `academic_years` VALUES (1,'2025-2026','2025-06-01','2026-03-31',0,'2026-03-11 07:06:33','2026-03-11 07:06:33',NULL),(2,'2025-2026','2025-06-01','2026-03-31',0,'2026-03-11 07:06:33','2026-03-11 07:06:33',NULL),(3,'2025-2026','2025-06-01','2026-03-31',1,'2026-03-11 08:01:28','2026-03-11 08:01:28',NULL),(4,'2025-2026','2025-06-01','2026-03-31',1,'2026-03-15 02:05:46','2026-03-15 02:05:46',NULL),(5,'2025-2026','2025-06-01','2026-03-31',1,'2026-03-15 02:07:39','2026-03-15 02:07:39',NULL),(6,'2025-2026','2025-06-01','2026-03-31',1,'2026-03-15 02:08:06','2026-03-15 02:08:06',NULL),(7,'2026-2027','2026-04-01','2027-03-31',0,'2026-03-21 06:11:09','2026-03-21 06:11:09',NULL);
/*!40000 ALTER TABLE `academic_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admission_queries`
--

DROP TABLE IF EXISTS `admission_queries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admission_queries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admission_queries`
--

LOCK TABLES `admission_queries` WRITE;
/*!40000 ALTER TABLE `admission_queries` DISABLE KEYS */;
/*!40000 ALTER TABLE `admission_queries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assignment_submissions`
--

DROP TABLE IF EXISTS `assignment_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assignment_submissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assignment_id` bigint(20) unsigned NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assignment_submissions_assignment_id_foreign` (`assignment_id`),
  KEY `assignment_submissions_student_id_foreign` (`student_id`),
  CONSTRAINT `assignment_submissions_assignment_id_foreign` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assignment_submissions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assignment_submissions`
--

LOCK TABLES `assignment_submissions` WRITE;
/*!40000 ALTER TABLE `assignment_submissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `assignment_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint(20) unsigned NOT NULL,
  `section_id` bigint(20) unsigned NOT NULL,
  `subject_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `assignment_date` date NOT NULL,
  `submission_date` date NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assignments_class_id_foreign` (`class_id`),
  KEY `assignments_section_id_foreign` (`section_id`),
  KEY `assignments_subject_id_foreign` (`subject_id`),
  KEY `assignments_created_by_foreign` (`created_by`),
  CONSTRAINT `assignments_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assignments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `assignments_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assignments_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assignments`
--

LOCK TABLES `assignments` WRITE;
/*!40000 ALTER TABLE `assignments` DISABLE KEYS */;
/*!40000 ALTER TABLE `assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `behaviour_settings`
--

DROP TABLE IF EXISTS `behaviour_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `behaviour_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `student_comment` tinyint(1) NOT NULL DEFAULT 0,
  `parent_comment` tinyint(1) NOT NULL DEFAULT 1,
  `student_view` tinyint(1) NOT NULL DEFAULT 1,
  `parent_view` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `behaviour_settings_branch_id_index` (`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `behaviour_settings`
--

LOCK TABLES `behaviour_settings` WRITE;
/*!40000 ALTER TABLE `behaviour_settings` DISABLE KEYS */;
INSERT INTO `behaviour_settings` VALUES (1,NULL,0,1,1,0,'2026-10-26 07:42:35','2026-10-26 07:42:35',NULL);
/*!40000 ALTER TABLE `behaviour_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,'Main Branch','MB001','2026-02-14 06:37:15','2026-02-14 06:37:15',NULL,NULL,NULL,1,NULL),(2,'Main Branch','BR_MAIN','2026-03-30 10:46:25','2026-03-30 10:46:25','1234567890','info@mainbranch.com','123 Main St',1,NULL),(3,'Main Campus','MAIN','2026-04-10 14:42:42','2026-04-10 14:42:42','1234567890','admin@school.com','123 School Lane',1,NULL),(4,'City Branch','CITY','2026-04-10 14:42:42','2026-04-10 14:42:42','0987654321','city@school.com','456 City Road',1,NULL);
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bulk_print_logs`
--

DROP TABLE IF EXISTS `bulk_print_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bulk_print_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `generated_by` bigint(20) unsigned NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bulk_print_logs_generated_by_foreign` (`generated_by`),
  CONSTRAINT `bulk_print_logs_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bulk_print_logs`
--

LOCK TABLES `bulk_print_logs` WRITE;
/*!40000 ALTER TABLE `bulk_print_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `bulk_print_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `certificate_templates`
--

DROP TABLE IF EXISTS `certificate_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `certificate_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `template` longtext NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `certificate_templates`
--

LOCK TABLES `certificate_templates` WRITE;
/*!40000 ALTER TABLE `certificate_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `certificate_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_rooms`
--

DROP TABLE IF EXISTS `class_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `class_rooms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `room_no` varchar(255) NOT NULL,
  `capacity` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_rooms`
--

LOCK TABLES `class_rooms` WRITE;
/*!40000 ALTER TABLE `class_rooms` DISABLE KEYS */;
/*!40000 ALTER TABLE `class_rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_routines`
--

DROP TABLE IF EXISTS `class_routines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `class_routines` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint(20) unsigned NOT NULL,
  `section_id` bigint(20) unsigned DEFAULT NULL,
  `subject_id` bigint(20) unsigned NOT NULL,
  `teacher_id` bigint(20) unsigned NOT NULL,
  `room_id` bigint(20) unsigned NOT NULL,
  `day` varchar(255) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `class_routines_class_id_foreign` (`class_id`),
  KEY `class_routines_section_id_foreign` (`section_id`),
  KEY `class_routines_subject_id_foreign` (`subject_id`),
  KEY `class_routines_room_id_foreign` (`room_id`),
  KEY `class_routines_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `class_routines_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `class_routines_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `class_rooms` (`id`),
  CONSTRAINT `class_routines_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`),
  CONSTRAINT `class_routines_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  CONSTRAINT `class_routines_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_routines`
--

LOCK TABLES `class_routines` WRITE;
/*!40000 ALTER TABLE `class_routines` DISABLE KEYS */;
/*!40000 ALTER TABLE `class_routines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_subjects`
--

DROP TABLE IF EXISTS `class_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `class_subjects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint(20) unsigned NOT NULL,
  `subject_id` bigint(20) unsigned NOT NULL,
  `teacher_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `class_subjects_class_id_foreign` (`class_id`),
  KEY `class_subjects_subject_id_foreign` (`subject_id`),
  KEY `class_subjects_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `class_subjects_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `class_subjects_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  CONSTRAINT `class_subjects_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_subjects`
--

LOCK TABLES `class_subjects` WRITE;
/*!40000 ALTER TABLE `class_subjects` DISABLE KEYS */;
INSERT INTO `class_subjects` VALUES (3,6,1,1,'2026-03-30 11:22:17','2026-03-30 11:22:17',NULL),(4,3,2,1,'2026-04-10 14:48:24','2026-04-10 14:48:24',NULL),(5,4,2,1,'2026-04-10 14:48:24','2026-04-10 14:48:24',NULL),(6,5,2,1,'2026-04-10 14:48:24','2026-04-10 14:48:24',NULL),(7,6,2,1,'2026-04-10 14:48:24','2026-04-10 14:48:24',NULL),(8,7,2,1,'2026-04-10 14:48:24','2026-04-10 14:48:24',NULL),(9,8,2,1,'2026-04-10 14:48:24','2026-04-10 14:48:24',NULL),(10,9,2,1,'2026-04-10 14:48:24','2026-04-10 14:48:24',NULL),(11,10,2,1,'2026-04-10 14:48:24','2026-04-10 14:48:24',NULL),(12,11,2,1,'2026-04-10 14:48:24','2026-04-10 14:48:24',NULL),(17,3,2,10,'2026-10-27 11:26:37','2026-10-27 11:26:37',NULL),(18,4,2,10,'2026-10-27 11:26:37','2026-10-27 11:26:37',NULL),(19,5,2,10,'2026-10-27 11:26:38','2026-10-27 11:26:38',NULL),(20,6,2,10,'2026-10-27 11:26:38','2026-10-27 11:26:38',NULL),(21,7,2,10,'2026-10-27 11:26:38','2026-10-27 11:26:38',NULL),(22,8,2,10,'2026-10-27 11:26:38','2026-10-27 11:26:38',NULL),(23,9,2,10,'2026-10-27 11:26:38','2026-10-27 11:26:38',NULL),(24,10,2,10,'2026-10-27 11:26:38','2026-10-27 11:26:38',NULL),(25,11,2,10,'2026-10-27 11:26:38','2026-10-27 11:26:38',NULL);
/*!40000 ALTER TABLE `class_subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_teachers`
--

DROP TABLE IF EXISTS `class_teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `class_teachers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint(20) unsigned NOT NULL,
  `section_id` bigint(20) unsigned DEFAULT NULL,
  `teacher_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `class_teachers_class_id_foreign` (`class_id`),
  KEY `class_teachers_section_id_foreign` (`section_id`),
  KEY `class_teachers_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `class_teachers_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `class_teachers_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`),
  CONSTRAINT `class_teachers_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_teachers`
--

LOCK TABLES `class_teachers` WRITE;
/*!40000 ALTER TABLE `class_teachers` DISABLE KEYS */;
INSERT INTO `class_teachers` VALUES (1,7,5,1,'2026-04-10 14:48:24','2026-04-10 14:48:24',NULL),(6,7,5,10,'2026-10-27 11:26:37','2026-10-27 11:26:37',NULL),(7,3,NULL,1,'2026-11-01 02:58:42','2026-11-01 02:58:42',NULL);
/*!40000 ALTER TABLE `class_teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `academic_year_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `classes_branch_id_foreign` (`branch_id`),
  KEY `classes_academic_year_id_foreign` (`academic_year_id`),
  KEY `classes_category_id_foreign` (`category_id`),
  CONSTRAINT `classes_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
  CONSTRAINT `classes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `classes_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `student_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
INSERT INTO `classes` VALUES (3,'Class 10 Updated',1,1,10,'2026-03-13 02:26:44','2026-10-21 06:23:45',NULL),(4,'Grade 10',1,1,10,'2026-03-21 03:33:35','2026-10-21 06:23:45',NULL),(5,'Class X',1,7,10,'2026-03-21 06:11:09','2026-10-21 06:23:45',NULL),(6,'Class 10',2,7,10,'2026-03-30 10:46:25','2026-10-21 06:23:45',NULL),(7,'Class 1',3,7,NULL,'2026-04-10 14:42:44','2026-04-10 14:42:44',NULL),(8,'Class 2',3,7,NULL,'2026-04-10 14:42:45','2026-04-10 14:42:45',NULL),(9,'Class 3',3,7,6,'2026-04-10 14:42:45','2026-10-21 06:23:45',NULL),(10,'Class 4',3,7,7,'2026-04-10 14:42:45','2026-10-21 06:23:45',NULL),(11,'Class 5',3,7,8,'2026-04-10 14:42:45','2026-10-21 06:23:45',NULL),(61,'Class Standalone',1,1,NULL,'2026-11-01 02:58:40','2026-11-01 02:58:40',NULL),(62,'Class Standalone',1,1,NULL,'2026-11-01 03:27:36','2026-11-01 03:27:36',NULL),(64,'Class Standalone',1,1,NULL,'2026-11-01 03:28:40','2026-11-01 03:28:40',NULL);
/*!40000 ALTER TABLE `classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `complaints`
--

DROP TABLE IF EXISTS `complaints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `complaints` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `complaint_by` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `description` text NOT NULL,
  `action_taken` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complaints`
--

LOCK TABLES `complaints` WRITE;
/*!40000 ALTER TABLE `complaints` DISABLE KEYS */;
/*!40000 ALTER TABLE `complaints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dormitories`
--

DROP TABLE IF EXISTS `dormitories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dormitories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dormitories_branch_id_foreign` (`branch_id`),
  CONSTRAINT `dormitories_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dormitories`
--

LOCK TABLES `dormitories` WRITE;
/*!40000 ALTER TABLE `dormitories` DISABLE KEYS */;
/*!40000 ALTER TABLE `dormitories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dormitory_rooms`
--

DROP TABLE IF EXISTS `dormitory_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dormitory_rooms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dormitory_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `number_of_bed` varchar(255) DEFAULT NULL,
  `cost_per_bed` varchar(255) DEFAULT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dormitory_rooms_dormitory_id_foreign` (`dormitory_id`),
  KEY `dormitory_rooms_branch_id_foreign` (`branch_id`),
  CONSTRAINT `dormitory_rooms_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dormitory_rooms_dormitory_id_foreign` FOREIGN KEY (`dormitory_id`) REFERENCES `dormitories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dormitory_rooms`
--

LOCK TABLES `dormitory_rooms` WRITE;
/*!40000 ALTER TABLE `dormitory_rooms` DISABLE KEYS */;
/*!40000 ALTER TABLE `dormitory_rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `download_content_types`
--

DROP TABLE IF EXISTS `download_content_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `download_content_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `download_content_types`
--

LOCK TABLES `download_content_types` WRITE;
/*!40000 ALTER TABLE `download_content_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `download_content_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `download_contents`
--

DROP TABLE IF EXISTS `download_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `download_contents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `uploaded_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `download_contents_type_id_foreign` (`type_id`),
  KEY `download_contents_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `download_contents_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `download_content_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `download_contents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `download_contents`
--

LOCK TABLES `download_contents` WRITE;
/*!40000 ALTER TABLE `download_contents` DISABLE KEYS */;
/*!40000 ALTER TABLE `download_contents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `google_event_id` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `events_user_id_foreign` (`user_id`),
  CONSTRAINT `events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_results`
--

DROP TABLE IF EXISTS `exam_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam_results` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned DEFAULT NULL,
  `exam_id` bigint(20) unsigned DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `marks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exam_results_student_id_foreign` (`student_id`),
  CONSTRAINT `exam_results_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_results`
--

LOCK TABLES `exam_results` WRITE;
/*!40000 ALTER TABLE `exam_results` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_schedules`
--

DROP TABLE IF EXISTS `exam_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam_schedules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `exam_id` bigint(20) unsigned DEFAULT NULL,
  `class_id` bigint(20) unsigned DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_schedules`
--

LOCK TABLES `exam_schedules` WRITE;
/*!40000 ALTER TABLE `exam_schedules` DISABLE KEYS */;
INSERT INTO `exam_schedules` VALUES (1,1,7,2,'2026-09-15','2026-04-11 03:20:08','2026-04-11 03:20:08',NULL);
/*!40000 ALTER TABLE `exam_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exams`
--

DROP TABLE IF EXISTS `exams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exams` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `academic_year_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exams_academic_year_id_foreign` (`academic_year_id`),
  CONSTRAINT `exams_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exams`
--

LOCK TABLES `exams` WRITE;
/*!40000 ALTER TABLE `exams` DISABLE KEYS */;
INSERT INTO `exams` VALUES (1,'First Term Exam',7,'2026-04-11 03:16:18','2026-04-11 03:16:18',NULL);
/*!40000 ALTER TABLE `exams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_groups`
--

DROP TABLE IF EXISTS `fee_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fee_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `school_id` bigint(20) unsigned DEFAULT NULL,
  `academic_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_groups`
--

LOCK TABLES `fee_groups` WRITE;
/*!40000 ALTER TABLE `fee_groups` DISABLE KEYS */;
INSERT INTO `fee_groups` VALUES (1,'Monthly Tuition',NULL,NULL,NULL,NULL,NULL,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL);
/*!40000 ALTER TABLE `fee_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_structures`
--

DROP TABLE IF EXISTS `fee_structures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fee_structures` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint(20) unsigned DEFAULT NULL,
  `fee_type_id` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_structures`
--

LOCK TABLES `fee_structures` WRITE;
/*!40000 ALTER TABLE `fee_structures` DISABLE KEYS */;
INSERT INTO `fee_structures` VALUES (1,7,1,5000.00,'2026-04-10 14:49:07','2026-04-10 14:49:07',NULL);
/*!40000 ALTER TABLE `fee_structures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_types`
--

DROP TABLE IF EXISTS `fee_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fee_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_types`
--

LOCK TABLES `fee_types` WRITE;
/*!40000 ALTER TABLE `fee_types` DISABLE KEYS */;
INSERT INTO `fee_types` VALUES (1,'Tuition Fee',0.00,'2026-04-10 14:49:07','2026-04-10 14:49:07',NULL);
/*!40000 ALTER TABLE `fee_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `module` varchar(255) DEFAULT NULL,
  `uploaded_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `files_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `files_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `files`
--

LOCK TABLES `files` WRITE;
/*!40000 ALTER TABLE `files` DISABLE KEYS */;
/*!40000 ALTER TABLE `files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `general_settings`
--

DROP TABLE IF EXISTS `general_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `general_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `school_name` varchar(255) DEFAULT NULL,
  `site_title` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `fees_income_head` varchar(255) DEFAULT NULL,
  `school_code` varchar(255) DEFAULT NULL,
  `suic_code` varchar(255) DEFAULT NULL,
  `zone` varchar(255) DEFAULT NULL,
  `category_of_institution` varchar(255) DEFAULT NULL,
  `academic_year_id` bigint(20) unsigned DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `date_format` varchar(255) DEFAULT NULL,
  `week_start_day` varchar(255) DEFAULT NULL,
  `time_zone` varchar(255) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `currency_symbol` varchar(255) DEFAULT NULL,
  `max_upload_size` varchar(255) DEFAULT NULL,
  `multiple_roll_number` varchar(255) DEFAULT NULL,
  `promotion_without_exam` varchar(255) DEFAULT NULL,
  `subject_attendance_layout` varchar(255) DEFAULT NULL,
  `new_fees_module` varchar(255) DEFAULT NULL,
  `result_type` varchar(255) DEFAULT NULL,
  `student_admission` varchar(255) DEFAULT NULL,
  `due_fees_login_restriction` varchar(255) DEFAULT NULL,
  `in_news_auto_approval_comment` varchar(255) DEFAULT NULL,
  `in_news_can_comment` varchar(255) DEFAULT NULL,
  `blog_search` varchar(255) DEFAULT NULL,
  `recent_blog` varchar(255) DEFAULT NULL,
  `carry_forward_fees_due_days` int(11) DEFAULT NULL,
  `queue_connection` varchar(255) DEFAULT NULL,
  `copyright_text` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `general_settings`
--

LOCK TABLES `general_settings` WRITE;
/*!40000 ALTER TABLE `general_settings` DISABLE KEYS */;
INSERT INTO `general_settings` VALUES (1,NULL,NULL,'My School','School ERP',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-11-01 03:27:39','2026-11-01 03:27:39',NULL);
/*!40000 ALTER TABLE `general_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `generated_certificates`
--

DROP TABLE IF EXISTS `generated_certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `generated_certificates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `certificate_template_id` bigint(20) unsigned NOT NULL,
  `issue_date` date NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `generated_certificates_student_id_foreign` (`student_id`),
  KEY `generated_certificates_certificate_template_id_foreign` (`certificate_template_id`),
  CONSTRAINT `generated_certificates_certificate_template_id_foreign` FOREIGN KEY (`certificate_template_id`) REFERENCES `certificate_templates` (`id`),
  CONSTRAINT `generated_certificates_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `generated_certificates`
--

LOCK TABLES `generated_certificates` WRITE;
/*!40000 ALTER TABLE `generated_certificates` DISABLE KEYS */;
/*!40000 ALTER TABLE `generated_certificates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `generated_id_cards`
--

DROP TABLE IF EXISTS `generated_id_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `generated_id_cards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `template_id` bigint(20) unsigned NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `generated_id_cards_student_id_foreign` (`student_id`),
  KEY `generated_id_cards_template_id_foreign` (`template_id`),
  CONSTRAINT `generated_id_cards_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `generated_id_cards_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `id_card_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `generated_id_cards`
--

LOCK TABLES `generated_id_cards` WRITE;
/*!40000 ALTER TABLE `generated_id_cards` DISABLE KEYS */;
/*!40000 ALTER TABLE `generated_id_cards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `homework_submissions`
--

DROP TABLE IF EXISTS `homework_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `homework_submissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `homework_id` bigint(20) unsigned NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `submission_date` datetime DEFAULT NULL,
  `submission_file` varchar(255) DEFAULT NULL,
  `student_notes` text DEFAULT NULL,
  `marks` decimal(8,2) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `evaluated_by` bigint(20) unsigned DEFAULT NULL,
  `evaluation_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `homework_submissions_homework_id_student_id_unique` (`homework_id`,`student_id`),
  KEY `homework_submissions_homework_id_index` (`homework_id`),
  KEY `homework_submissions_student_id_index` (`student_id`),
  KEY `homework_submissions_evaluated_by_index` (`evaluated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `homework_submissions`
--

LOCK TABLES `homework_submissions` WRITE;
/*!40000 ALTER TABLE `homework_submissions` DISABLE KEYS */;
INSERT INTO `homework_submissions` VALUES (4,2,5,'2026-10-31 09:17:29',NULL,NULL,45.00,'completed',NULL,'2026-10-31','Great effort','2026-10-31 03:44:18','2026-10-31 03:47:29',NULL),(5,2,39,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-10-31 03:44:18','2026-10-31 03:44:18',NULL),(6,2,40,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-10-31 03:44:18','2026-10-31 03:44:18',NULL),(7,2,41,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-10-31 03:44:18','2026-10-31 03:44:18',NULL),(8,3,34,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-10-31 03:53:11','2026-10-31 03:53:11',NULL),(9,3,35,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-10-31 03:53:11','2026-10-31 03:53:11',NULL),(10,3,36,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-10-31 03:53:11','2026-10-31 03:53:11',NULL),(11,4,34,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-10-31 03:53:11','2026-10-31 03:53:11','2026-10-31 03:53:11'),(12,4,35,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-10-31 03:53:11','2026-10-31 03:53:11','2026-10-31 03:53:11'),(13,4,36,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-10-31 03:53:11','2026-10-31 03:53:11','2026-10-31 03:53:11'),(14,4,5,'2026-10-31 09:23:11','uploads/homework_submissions/1793438591_6ae5b37f839c4.pdf','Here is my completed assignment',92.00,'completed',1,'2026-10-31','Excellent work on problem sets','2026-10-31 03:53:11','2026-10-31 03:53:11','2026-10-31 03:53:11'),(15,5,34,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 01:55:08','2026-11-01 01:55:08',NULL),(16,5,35,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 01:55:08','2026-11-01 01:55:08',NULL),(17,5,36,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 01:55:08','2026-11-01 01:55:08',NULL),(18,6,34,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 01:55:08','2026-11-01 01:55:09','2026-11-01 01:55:09'),(19,6,35,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 01:55:08','2026-11-01 01:55:09','2026-11-01 01:55:09'),(20,6,36,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 01:55:08','2026-11-01 01:55:09','2026-11-01 01:55:09'),(21,6,5,'2026-11-01 07:25:08','uploads/homework_submissions/1793517908_6ae6e954e5f57.pdf','Here is my completed assignment',92.00,'completed',1,'2026-11-01','Excellent work on problem sets','2026-11-01 01:55:08','2026-11-01 01:55:09','2026-11-01 01:55:09'),(22,7,24,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 02:58:41','2026-11-01 02:58:41',NULL),(23,7,25,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 02:58:41','2026-11-01 02:58:41',NULL),(24,7,34,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 02:58:41','2026-11-01 02:58:41',NULL),(25,7,35,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 02:58:41','2026-11-01 02:58:41',NULL),(26,7,36,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 02:58:41','2026-11-01 02:58:41',NULL),(27,8,5,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:27:37','2026-11-01 03:27:37',NULL),(28,8,24,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:27:37','2026-11-01 03:27:37',NULL),(29,8,25,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:27:37','2026-11-01 03:27:37',NULL),(30,8,34,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:27:37','2026-11-01 03:27:37',NULL),(31,8,35,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:27:37','2026-11-01 03:27:37',NULL),(32,8,36,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:27:37','2026-11-01 03:27:37',NULL),(33,8,74,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:27:37','2026-11-01 03:27:37',NULL),(34,8,75,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:27:37','2026-11-01 03:27:37',NULL),(35,9,34,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:27:47','2026-11-01 03:27:47',NULL),(36,9,35,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:27:47','2026-11-01 03:27:47',NULL),(37,9,36,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:27:47','2026-11-01 03:27:47',NULL),(38,10,34,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:27:48','2026-11-01 03:27:49','2026-11-01 03:27:49'),(39,10,35,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:27:48','2026-11-01 03:27:49','2026-11-01 03:27:49'),(40,10,36,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:27:48','2026-11-01 03:27:49','2026-11-01 03:27:49'),(41,10,5,'2026-11-01 08:57:48','uploads/homework_submissions/1793523468_6ae6ff0ccfe76.pdf','Here is my completed assignment',92.00,'completed',1,'2026-11-01','Excellent work on problem sets','2026-11-01 03:27:48','2026-11-01 03:27:49','2026-11-01 03:27:49'),(42,11,5,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:28:40','2026-11-01 03:28:40',NULL),(43,11,24,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:28:40','2026-11-01 03:28:40',NULL),(44,11,25,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:28:40','2026-11-01 03:28:40',NULL),(45,11,34,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:28:40','2026-11-01 03:28:40',NULL),(46,11,35,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:28:40','2026-11-01 03:28:40',NULL),(47,11,36,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:28:40','2026-11-01 03:28:40',NULL),(48,11,74,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:28:40','2026-11-01 03:28:40',NULL),(49,11,75,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:28:40','2026-11-01 03:28:40',NULL),(50,11,77,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:28:40','2026-11-01 03:28:40',NULL),(51,11,82,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:28:40','2026-11-01 03:28:40',NULL),(52,12,34,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:29:08','2026-11-01 03:29:08',NULL),(53,12,35,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:29:08','2026-11-01 03:29:08',NULL),(54,12,36,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:29:08','2026-11-01 03:29:08',NULL),(55,13,34,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:29:09','2026-11-01 03:29:09','2026-11-01 03:29:09'),(56,13,35,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:29:09','2026-11-01 03:29:09','2026-11-01 03:29:09'),(57,13,36,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,'2026-11-01 03:29:09','2026-11-01 03:29:09','2026-11-01 03:29:09'),(58,13,5,'2026-11-01 08:59:09','uploads/homework_submissions/1793523549_6ae6ff5d1fdc9.pdf','Here is my completed assignment',92.00,'completed',1,'2026-11-01','Excellent work on problem sets','2026-11-01 03:29:09','2026-11-01 03:29:09','2026-11-01 03:29:09');
/*!40000 ALTER TABLE `homework_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `homeworks`
--

DROP TABLE IF EXISTS `homeworks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `homeworks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `academic_year_id` bigint(20) unsigned DEFAULT NULL,
  `class_id` bigint(20) unsigned NOT NULL,
  `section_id` bigint(20) unsigned DEFAULT NULL,
  `subject_id` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `evaluated_by` bigint(20) unsigned DEFAULT NULL,
  `homework_date` date NOT NULL,
  `submission_date` date NOT NULL,
  `evaluation_date` date DEFAULT NULL,
  `marks` decimal(8,2) NOT NULL DEFAULT 100.00,
  `description` text DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `homeworks_branch_id_index` (`branch_id`),
  KEY `homeworks_academic_year_id_index` (`academic_year_id`),
  KEY `homeworks_class_id_index` (`class_id`),
  KEY `homeworks_section_id_index` (`section_id`),
  KEY `homeworks_subject_id_index` (`subject_id`),
  KEY `homeworks_created_by_index` (`created_by`),
  KEY `homeworks_evaluated_by_index` (`evaluated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `homeworks`
--

LOCK TABLES `homeworks` WRITE;
/*!40000 ALTER TABLE `homeworks` DISABLE KEYS */;
INSERT INTO `homeworks` VALUES (2,1,7,5,3,1,NULL,NULL,'2026-11-01','2026-11-05','2026-10-31',50.00,'Complete chapter 1 exercise',NULL,'active','2026-10-31 03:44:18','2026-10-31 03:47:29',NULL),(3,1,1,3,1,1,1,NULL,'2026-10-31','2026-11-07',NULL,50.00,'Solve all chapter 3 questions','uploads/homework/1793438591_6ae5b37f220ef.pdf','active','2026-10-31 03:53:11','2026-10-31 03:53:11',NULL),(4,1,1,3,1,1,1,1,'2026-10-31','2026-11-05','2026-10-31',100.00,'Updated assignment description',NULL,'active','2026-10-31 03:53:11','2026-10-31 03:53:11','2026-10-31 03:53:11'),(5,1,1,3,1,1,1,NULL,'2026-11-01','2026-11-08',NULL,50.00,'Solve all chapter 3 questions','uploads/homework/1793517908_6ae6e95496e2e.pdf','active','2026-11-01 01:55:08','2026-11-01 01:55:08',NULL),(6,1,1,3,1,1,1,1,'2026-11-01','2026-11-06','2026-11-01',100.00,'Updated assignment description',NULL,'active','2026-11-01 01:55:08','2026-11-01 01:55:09','2026-11-01 01:55:09'),(7,1,1,3,NULL,1,1,NULL,'2026-11-01','2026-11-04',NULL,50.00,'Homework for entire class with no section',NULL,'active','2026-11-01 02:58:41','2026-11-01 02:58:41',NULL),(8,1,1,3,NULL,1,1,NULL,'2026-11-01','2026-11-04',NULL,50.00,'Homework for entire class with no section',NULL,'active','2026-11-01 03:27:37','2026-11-01 03:27:37',NULL),(9,1,1,3,1,1,1,NULL,'2026-11-01','2026-11-08',NULL,50.00,'Solve all chapter 3 questions','uploads/homework/1793523465_6ae6ff097ac8d.pdf','active','2026-11-01 03:27:45','2026-11-01 03:27:45',NULL),(10,1,1,3,1,1,1,1,'2026-11-01','2026-11-06','2026-11-01',100.00,'Updated assignment description',NULL,'active','2026-11-01 03:27:48','2026-11-01 03:27:49','2026-11-01 03:27:49'),(11,1,1,3,NULL,1,1,NULL,'2026-11-01','2026-11-04',NULL,50.00,'Homework for entire class with no section',NULL,'active','2026-11-01 03:28:40','2026-11-01 03:28:40',NULL),(12,1,1,3,1,1,1,NULL,'2026-11-01','2026-11-08',NULL,50.00,'Solve all chapter 3 questions','uploads/homework/1793523548_6ae6ff5ccd911.pdf','active','2026-11-01 03:29:08','2026-11-01 03:29:08',NULL),(13,1,1,3,1,1,1,1,'2026-11-01','2026-11-06','2026-11-01',100.00,'Updated assignment description',NULL,'active','2026-11-01 03:29:09','2026-11-01 03:29:09','2026-11-01 03:29:09');
/*!40000 ALTER TABLE `homeworks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `id_card_templates`
--

DROP TABLE IF EXISTS `id_card_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `id_card_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `template` longtext NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `id_card_templates`
--

LOCK TABLES `id_card_templates` WRITE;
/*!40000 ALTER TABLE `id_card_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `id_card_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incidents`
--

DROP TABLE IF EXISTS `incidents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `incidents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `point` int(11) NOT NULL DEFAULT 0,
  `is_negative` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `incidents_branch_id_index` (`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incidents`
--

LOCK TABLES `incidents` WRITE;
/*!40000 ALTER TABLE `incidents` DISABLE KEYS */;
INSERT INTO `incidents` VALUES (2,NULL,'Academic Excellence',10,0,'Outstanding academic performance','2026-10-30 23:58:09','2026-10-30 23:58:09',NULL),(3,NULL,'Late to Class',3,1,'Tardy','2026-10-31 00:15:09','2026-10-31 00:15:09',NULL);
/*!40000 ALTER TABLE `incidents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_threads`
--

DROP TABLE IF EXISTS `message_threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_threads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `message_threads_created_by_foreign` (`created_by`),
  CONSTRAINT `message_threads_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_threads`
--

LOCK TABLES `message_threads` WRITE;
/*!40000 ALTER TABLE `message_threads` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_threads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint(20) unsigned NOT NULL,
  `receiver_id` bigint(20) unsigned NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_sender_id_foreign` (`sender_id`),
  KEY `messages_receiver_id_foreign` (`receiver_id`),
  CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`),
  CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2026_02_13_174532_create_admission_queries_table',2),(6,'2026_02_13_174939_create_permission_tables',2),(7,'2026_03_08_082644_create_academic_years_table',3),(8,'2026_03_08_082646_create_user_profiles_table',3),(9,'2026_03_08_082648_create_admission_queries_table',4),(10,'2026_03_08_082649_create_postal_receives_table',4),(11,'2026_03_08_082650_create_phone_call_logs_table',4),(12,'2026_03_08_082650_create_postal_dispatches_table',4),(13,'2026_03_08_082651_create_certificate_templates_table',4),(14,'2026_03_08_082651_create_complaints_table',4),(15,'2026_03_08_082658_create_student_categories_table',5),(16,'2026_03_08_082659_create_students_table',6),(17,'2026_03_08_082710_create_generated_certificates_table',7),(18,'2026_03_08_082652_create_id_card_templates_table',7),(19,'2026_03_08_082653_create_classes_table',7),(20,'2026_03_08_082711_create_generated_id_cards_table',7),(21,'2026_03_08_082654_create_sections_table',7),(22,'2026_03_08_082655_create_subjects_table',8),(23,'2026_03_08_082712_create_optional_subjects_table',9),(24,'2026_03_08_082656_create_teachers_table',10),(25,'2026_03_08_082713_create_class_subjects_table',11),(26,'2026_03_08_082657_create_class_rooms_table',11),(27,'2026_03_08_082714_create_class_teachers_table',11),(28,'2026_03_08_082658_create_class_routines_table',11),(29,'2026_03_08_082700_create_student_academics_table',11),(30,'2026_03_08_082700_create_student_parents_table',11),(31,'2026_03_08_082701_create_student_documents_table',11),(32,'2026_03_08_082701_create_student_multi_class_table',11),(33,'2026_03_08_082702_create_assignments_table',12),(34,'2026_03_08_082715_create_assignment_submissions_table',13),(35,'2026_03_08_082703_create_download_content_types_table',13),(36,'2026_03_08_082703_create_study_materials_table',13),(37,'2026_03_08_082704_create_download_contents_table',13),(38,'2026_03_08_082705_create_shared_contents_table',13),(39,'2026_03_08_082705_create_videos_table',13),(40,'2026_03_08_082706_create_messages_table',13),(41,'2026_03_08_082706_create_notifications_table',13),(42,'2026_03_08_082707_create_bulk_print_logs_table',13),(43,'2026_03_08_082707_create_message_threads_table',13),(44,'2026_03_08_082708_create_files_table',13),(45,'2026_03_08_090336_update_branches_table',13),(46,'2026_03_11_072240_add_softdelete_to_all_tables',14),(47,'2026_03_11_113845_update_students_table_for_relations',15),(48,'2026_03_11_105142_add_class_id_to_students_table',16),(49,'2026_03_21_085835_add_type_to_subjects_table',16),(50,'2026_03_21_112801_add_fields_to_students_table',17),(51,'2026_03_31_041210_create_exams_table',18),(52,'2026_03_31_041211_create_exam_results_table',18),(53,'2026_03_31_041212_create_exam_schedules_table',18),(54,'2026_03_31_041213_create_fee_groups_table',18),(55,'2026_03_31_041214_create_fee_structures_table',18),(56,'2026_03_31_041215_create_fee_types_table',18),(57,'2026_03_31_041216_create_payments_table',18),(58,'2026_03_31_041217_create_school_classes_table',18),(59,'2026_03_31_041217_create_staff_departments_table',18),(60,'2026_03_31_041218_create_staff_table',18),(61,'2026_03_31_041219_create_staff_attendances_table',18),(62,'2026_03_31_041221_create_student_attendances_table',18),(63,'2026_03_31_041222_create_student_fees_table',18),(64,'2026_03_31_041223_create_student_multi_classes_table',18),(65,'2026_03_31_041224_create_timetables_table',18),(66,'2026_04_08_075603_add_two_factor_columns_to_users_table',19),(67,'2026_04_12_000000_add_missing_fields_to_student_tables',20),(68,'2026_04_12_000001_create_supporting_student_tables',21),(69,'2026_04_12_000002_add_final_design_fields_to_students',22),(70,'2026_04_12_000003_add_final_ui_fields_to_student_info',23),(71,'2026_04_17_000000_make_users_email_nullable',24),(72,'2026_04_17_065319_make_gender_nullable_in_students_table',25),(73,'2026_04_21_150004_add_missing_fields_to_staff_table',26),(74,'2026_04_21_165455_unify_staff_and_teachers_tables',27),(75,'2026_04_22_162000_drop_unwanted_fields_from_staff_table',28),(76,'2026_04_22_162500_drop_department_id_from_staff_table',29),(77,'2026_05_03_065029_create_expenses_table',30),(78,'2026_05_03_065030_create_todos_table',30),(79,'2026_05_03_065031_create_events_table',30),(80,'2026_05_03_072818_add_extra_fields_to_events_table',31),(81,'2026_05_03_112354_update_fee_groups_table',32),(82,'2014_10_11_000000_create_branches_table',33),(83,'2026_05_11_000001_add_status_to_student_fees_table',34),(84,'2026_07_18_000000_create_general_settings_table',34),(85,'2026_07_24_000000_add_google_calendar_columns',35),(86,'2026_07_28_000000_add_apaar_id_aadharshila_no_pen_no_to_students_table',36),(87,'2026_07_28_000001_add_suic_zone_category_to_general_settings_table',37),(88,'2026_07_31_000000_add_student_code_to_students_table',38),(89,'2026_07_31_000001_rename_staff_no_and_add_category_to_staff_table',39),(90,'2026_08_04_000000_add_full_name_to_users_students_staff_tables',40),(91,'2026_08_08_000000_add_academic_year_id_to_students_table',41),(92,'2026_08_09_000000_add_fields_to_student_attendances_table',41),(93,'2026_08_12_000000_add_note_to_staff_attendances_table',41),(94,'2026_08_29_000000_add_state_to_general_settings_table',42),(95,'2026_10_16_000000_create_incidents_and_student_incidents_tables',43),(96,'2026_10_16_204600_create_sidebar_menus_table',44),(97,'2026_10_20_113000_create_proper_sidebar_menu_tables',45),(98,'2026_10_21_120000_add_class_mapping_to_student_categories_table',46),(99,'2026_10_21_172000_add_category_id_to_classes_table',47),(100,'2026_10_23_000000_ensure_sanctum_and_web_roles_exist',48),(101,'2026_10_25_000000_create_homeworks_and_submissions_tables',49),(102,'2026_10_26_000000_create_behaviour_settings_table',50),(103,'2026_10_27_000000_make_branch_id_nullable_in_classes_table',51),(104,'2026_10_29_000000_add_driver_role_and_permissions',52),(105,'2026_10_31_000000_sync_student_roll_no_from_academics',53),(106,'2026_10_31_000001_add_soft_deletes_to_all_tables',54),(107,'2026_11_01_000000_make_class_and_section_nullable',55);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1,NULL),(1,'App\\Models\\User',14,NULL),(2,'App\\Models\\User',15,NULL),(2,'App\\Models\\User',16,NULL),(2,'App\\Models\\User',17,NULL),(2,'App\\Models\\User',18,NULL),(2,'App\\Models\\User',19,NULL),(2,'App\\Models\\User',97,NULL),(2,'App\\Models\\User',100,NULL),(2,'App\\Models\\User',103,NULL),(2,'App\\Models\\User',166,NULL),(2,'App\\Models\\User',188,NULL),(4,'App\\Models\\User',1,NULL),(4,'App\\Models\\User',4,NULL),(4,'App\\Models\\User',6,NULL),(4,'App\\Models\\User',9,NULL),(4,'App\\Models\\User',10,NULL),(4,'App\\Models\\User',11,NULL),(4,'App\\Models\\User',12,NULL),(4,'App\\Models\\User',13,NULL),(4,'App\\Models\\User',20,NULL),(4,'App\\Models\\User',21,NULL),(4,'App\\Models\\User',24,NULL),(4,'App\\Models\\User',25,NULL),(4,'App\\Models\\User',26,NULL),(4,'App\\Models\\User',27,NULL),(4,'App\\Models\\User',28,NULL),(4,'App\\Models\\User',29,NULL),(4,'App\\Models\\User',31,NULL),(4,'App\\Models\\User',32,NULL),(4,'App\\Models\\User',33,NULL),(4,'App\\Models\\User',34,NULL),(4,'App\\Models\\User',35,NULL),(4,'App\\Models\\User',36,NULL),(4,'App\\Models\\User',37,NULL),(4,'App\\Models\\User',38,NULL),(4,'App\\Models\\User',39,NULL),(4,'App\\Models\\User',40,NULL),(4,'App\\Models\\User',41,NULL),(4,'App\\Models\\User',98,NULL),(4,'App\\Models\\User',101,NULL),(4,'App\\Models\\User',104,NULL),(4,'App\\Models\\User',116,NULL),(4,'App\\Models\\User',117,NULL),(4,'App\\Models\\User',118,NULL),(4,'App\\Models\\User',202,NULL),(4,'App\\Models\\User',204,NULL),(4,'App\\Models\\User',206,NULL),(4,'App\\Models\\User',211,NULL),(4,'App\\Models\\User',212,NULL),(4,'App\\Models\\User',221,NULL),(4,'App\\Models\\User',225,NULL),(5,'App\\Models\\User',99,NULL),(5,'App\\Models\\User',102,NULL),(5,'App\\Models\\User',105,NULL),(5,'App\\Models\\User',203,NULL),(5,'App\\Models\\User',205,NULL),(5,'App\\Models\\User',207,NULL),(7,'App\\Models\\User',201,NULL);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_foreign` (`user_id`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,14,'School Reopening','The school will reopen on April 1st.',0,'2026-04-11 03:20:08','2026-04-11 03:20:08',NULL),(2,14,'School Reopening','The school will reopen on April 1st.',0,'2026-10-27 11:26:38','2026-10-27 11:26:38',NULL),(3,14,'School Reopening','The school will reopen on April 1st.',0,'2026-10-27 11:27:20','2026-10-27 11:27:20',NULL),(4,14,'School Reopening','The school will reopen on April 1st.',0,'2026-10-27 23:03:19','2026-10-27 23:03:19',NULL);
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `optional_subjects`
--

DROP TABLE IF EXISTS `optional_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `optional_subjects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subject_id` bigint(20) unsigned NOT NULL,
  `class_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `optional_subjects_subject_id_foreign` (`subject_id`),
  KEY `optional_subjects_class_id_foreign` (`class_id`),
  CONSTRAINT `optional_subjects_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `optional_subjects_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `optional_subjects`
--

LOCK TABLES `optional_subjects` WRITE;
/*!40000 ALTER TABLE `optional_subjects` DISABLE KEYS */;
/*!40000 ALTER TABLE `optional_subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
INSERT INTO `password_reset_tokens` VALUES ('abc@example.com','$2y$12$V5y4XhA4/8Tz1TnyYdRsguKgyj/PuBFRAUUgqkdelO6DYL4CMP2Xm','2026-10-23 06:14:18'),('bulkstaff_1787743392_1@example.com','$2y$12$NOTt5opId3oy.ZXlFzOz1uMa6dkib3XCEeSv8F65ciHdnVKJMw3Ai','2026-08-26 05:53:18'),('father@example.com','$2y$12$SKnngmpb5mJWIxSV2AkV.e386xNi6RI5chXDiGdcpLogc2/4TtV/u','2026-10-23 06:14:24'),('john_1785902216@example.com','$2y$12$XhIW2GCNP9XtTO.J.LCbV.q/E3G93SbiqJqK/u4FwRU38Up4l7K.O','2026-08-04 22:26:57'),('junaid_1785911598@example.com','$2y$12$GDJ5jpfGVL4AwW8lf9HDqeVRxufZJqmUJOS9uiEkJ0W3Ox8uvQPKO','2026-08-05 01:03:24'),('junaid_1785911604@example.com','$2y$12$xj5eZbZ7g2WN9g3aOni59OQAcS.xn6NDYaVHYTNGlictkEYM8GvBC','2026-08-05 01:03:25'),('parent.9436@example.com','$2y$12$jCbjwjjTVM5fe2yutbC8RewYsIpyPWNQlAjqxtvg1wUht6Eyhty9y','2026-10-23 05:37:32'),('rkvishnuprakash@gmail.com','$2y$12$/vgdjitS4pRjJBzLvEmdNOvQRhCh9zbj2bPX43xVzeeYQ3zPf8Kc.','2026-07-18 23:05:10'),('student.2214@example.com','$2y$12$Q8qPkRBopGiENPYKy7hJLOhIZHoVI5Fr3XKPhiElC1Uza9q6U0st6','2026-10-23 05:37:12'),('test.parent.9854@example.com','$2y$12$4K6CHqb3aRdHBihSJYXu/O0esMasundHcXJ3D7t5/9fYOByvASLai','2026-10-23 05:38:58'),('test.student.4024@example.com','$2y$12$fFpyoFJ4hWnmOBvRq2YC9.PO3L7onNcTrK7gOPo5PsRYNFYvLjAma','2026-10-23 05:38:52'),('teststaff_1787677780@example.com','$2y$12$QweTrSPMezZ1pdJETkbRbe6S9CrZ8n/NEzGMkRD.dW9t2luPCPmnu','2026-08-25 11:39:41'),('vishnuprakashrk636@gmail.com','$2y$12$WzHLCJViAhUFQ/uuDPHwXuPgK/3Z432y/aaCo3I9UsF6oGycF/Eey','2026-10-11 00:29:52');
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_fee_id` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'manage_admission','web','2026-02-14 00:57:50','2026-02-14 00:57:50',NULL),(2,'view_reports','web','2026-02-14 00:57:50','2026-02-14 00:57:50',NULL),(3,'manage_academics','web','2026-04-10 14:42:39','2026-04-10 14:42:39',NULL),(4,'manage_fees','web','2026-04-10 14:42:40','2026-04-10 14:42:40',NULL),(5,'view_students','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(6,'create_students','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(7,'edit_students','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(8,'delete_students','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(9,'manage_admissions','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(10,'manage_student_documents','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(11,'manage_student_categories','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(12,'manage_classes','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(13,'manage_sections','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(14,'manage_subjects','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(15,'manage_class_teachers','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(16,'manage_class_rooms','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(17,'manage_timetables','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(18,'view_attendance','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(19,'mark_student_attendance','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(20,'mark_staff_attendance','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(21,'manage_exams','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(22,'manage_exam_schedules','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(23,'enter_exam_marks','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(24,'view_exam_results','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(25,'view_fees','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(26,'collect_fees','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(27,'manage_fee_structures','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(28,'manage_fee_groups','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(29,'view_expenses','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(30,'view_staff','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(31,'create_staff','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(32,'edit_staff','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(33,'delete_staff','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(34,'manage_departments','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(35,'manage_admission_queries','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(36,'manage_complaints','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(37,'manage_postal_records','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(38,'manage_phone_call_logs','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(39,'view_events','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(40,'create_events','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(41,'edit_events','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(42,'delete_events','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(43,'manage_general_settings','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(44,'manage_roles_permissions','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(45,'view_assigned_classes','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(46,'submit_student_attendance','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(47,'submit_exam_marks','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(48,'upload_study_materials','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(49,'create_assignments','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(50,'view_child_attendance','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(51,'view_child_fees','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(52,'view_child_results','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(53,'view_child_timetable','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(54,'view_child_homework','web','2026-10-14 13:44:45','2026-10-14 13:44:45',NULL),(55,'manage_admission','sanctum','2026-10-27 11:26:35','2026-10-27 11:26:35',NULL),(56,'view_reports','sanctum','2026-10-27 11:26:35','2026-10-27 11:26:35',NULL),(57,'manage_academics','sanctum','2026-10-27 11:26:35','2026-10-27 11:26:35',NULL),(58,'manage_fees','sanctum','2026-10-27 11:26:35','2026-10-27 11:26:35',NULL),(59,'view_transport','web','2026-10-28 22:58:55','2026-10-28 22:58:55',NULL),(60,'view_assigned_routes','web','2026-10-28 22:58:55','2026-10-28 22:58:55',NULL),(61,'view_vehicles','web','2026-10-28 22:58:55','2026-10-28 22:58:55',NULL),(62,'view_student_transport_list','web','2026-10-28 22:58:55','2026-10-28 22:58:55',NULL),(63,'manage_vehicles','web','2026-10-28 22:58:55','2026-10-28 22:58:55',NULL),(64,'manage_routes','web','2026-10-28 22:58:55','2026-10-28 22:58:55',NULL),(65,'view_transport','sanctum','2026-10-28 22:58:56','2026-10-28 22:58:56',NULL),(66,'view_assigned_routes','sanctum','2026-10-28 22:58:56','2026-10-28 22:58:56',NULL),(67,'view_vehicles','sanctum','2026-10-28 22:58:56','2026-10-28 22:58:56',NULL),(68,'view_student_transport_list','sanctum','2026-10-28 22:58:56','2026-10-28 22:58:56',NULL),(69,'manage_vehicles','sanctum','2026-10-28 22:58:56','2026-10-28 22:58:56',NULL),(70,'manage_routes','sanctum','2026-10-28 22:58:56','2026-10-28 22:58:56',NULL);
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',1,'erp_token','aecad659ab565bce8304539d1513555687b5f20e1a5928db1c0e1db3fd02ffa6','[\"*\"]',NULL,NULL,'2026-02-14 01:10:16','2026-02-14 01:10:16'),(2,'App\\Models\\User',1,'erp_token','5257eaad9712243f065454129a25d4c5f0b74cf852251de4368352ab9e731508','[\"*\"]',NULL,NULL,'2026-02-14 01:13:22','2026-02-14 01:13:22'),(3,'App\\Models\\User',1,'erp_token','436d2592484afe728d02bddc1c816813fb11df84e77ac5e0af70a41244a62d48','[\"*\"]',NULL,NULL,'2026-02-14 01:13:28','2026-02-14 01:13:28'),(4,'App\\Models\\User',1,'erp_token','a90c84e7ddac6bbfff5cb2fc209eeb297fd7642504576ff19461342f192ba9c0','[\"*\"]',NULL,NULL,'2026-02-14 01:14:01','2026-02-14 01:14:01'),(5,'App\\Models\\User',1,'erp_token','2efd202fa15cb1b8e9d5a2115d52bb995cb604a26823f20c0560470b0c67d188','[\"*\"]','2026-03-15 02:08:06',NULL,'2026-03-11 05:13:19','2026-03-15 02:08:06'),(6,'App\\Models\\User',1,'erp_token','b6b5e859a235065e60b7d5b2ab375bdf9119c27368ed33141204de2b2fc48db6','[\"*\"]',NULL,NULL,'2026-03-15 02:10:04','2026-03-15 02:10:04'),(7,'App\\Models\\User',1,'erp_token','5d4d6e2aa9ff8168ef80a8687bcb8576b16267e5065f59273a841ba2641177cf','[\"*\"]',NULL,NULL,'2026-03-15 04:53:51','2026-03-15 04:53:51'),(8,'App\\Models\\User',1,'erp_token','c588c0706a888666398dd5ea4930affb7dfcf4d588db7b6ed8c68ce01547eb8b','[\"*\"]','2026-03-15 06:08:43',NULL,'2026-03-15 05:11:11','2026-03-15 06:08:43'),(9,'App\\Models\\User',1,'erp_token','3418f0f21c8fba8904a65847bc2276ecab5d32505da501e93bab92b578c2f91a','[\"*\"]','2026-03-21 06:13:53',NULL,'2026-03-21 01:23:38','2026-03-21 06:13:53'),(10,'App\\Models\\User',13,'erp_token','a1741e1f9f01bf984335e2fb096ab7e945515d85c11f528c5ea42109ef3d0647','[\"*\"]',NULL,NULL,'2026-03-21 06:22:53','2026-03-21 06:22:53'),(11,'App\\Models\\User',13,'erp_token','db20662e51a5fb3b26bc57e6b1728ec2a18855fce5778b21ee673b70f51aeabb','[\"*\"]',NULL,NULL,'2026-03-21 06:27:45','2026-03-21 06:27:45'),(12,'App\\Models\\User',1,'test','6063703fa30cd89a73868c383cec2068ddbd44ab015079b387e38e80e059cea1','[\"*\"]','2026-03-25 23:49:41',NULL,'2026-03-25 23:49:40','2026-03-25 23:49:41'),(13,'App\\Models\\User',13,'erp_token','00a8ed54896f87a3dd19533abe2d62ee6b9d6d79d616588122c092c4269c90ee','[\"*\"]',NULL,NULL,'2026-03-26 01:57:25','2026-03-26 01:57:25'),(14,'App\\Models\\User',14,'erp_token','e6484b5db475d427e30bacbba53f74cfe28f9eeda7bff461261aa63c02461d47','[\"*\"]',NULL,NULL,'2026-04-11 12:47:13','2026-04-11 12:47:13'),(15,'App\\Models\\User',1,'erp_token','beb438455e87ff4bf48bbade090768fb18de0c255c968ddf3d9c8e3708d4f762','[\"*\"]',NULL,NULL,'2026-04-11 12:47:49','2026-04-11 12:47:49'),(16,'App\\Models\\User',1,'erp_token','470d8ff721865035e83d9fdd9c5c96c0e5abfb7c9fcbe0df574280efbf63c55c','[\"*\"]',NULL,NULL,'2026-04-11 21:07:08','2026-04-11 21:07:08'),(17,'App\\Models\\User',1,'erp_token','3a27c08c2453647cac0220df415b7cca8c1c05ebbab7524d9aaadd947db743cb','[\"*\"]',NULL,NULL,'2026-04-12 03:29:36','2026-04-12 03:29:36'),(18,'App\\Models\\User',1,'erp_token','b606566ffa25e9b3fde2310725ba0d2209e4ec72fa720bee32a6986260d23228','[\"*\"]','2026-04-12 04:15:57',NULL,'2026-04-12 04:01:08','2026-04-12 04:15:57'),(19,'App\\Models\\User',1,'erp_token','6161a15bb76094a17aae5b49108472b3d68d15cf50d084cadf3caf6557f97d20','[\"*\"]','2026-04-17 01:31:23',NULL,'2026-04-17 00:02:54','2026-04-17 01:31:23'),(20,'App\\Models\\User',1,'erp_token','5daff68e62f292e449c8b4e5abbc1a6d693ccd067510cd1c5f436d893ddb02e0','[\"*\"]','2026-04-20 03:36:21',NULL,'2026-04-20 01:38:26','2026-04-20 03:36:21'),(21,'App\\Models\\User',14,'erp_token','e8d12d252672ba325952fcc6afb0de97f409cdcdfe81a865186817382df2609c','[\"*\"]',NULL,NULL,'2026-05-08 17:14:21','2026-05-08 17:14:21'),(22,'App\\Models\\User',14,'erp_token','599fc0e841cd9847b657e26dfd95ab339d5c29d45eb50ab0f621ca934b8b8cb1','[\"*\"]','2026-05-24 13:37:08',NULL,'2026-05-08 17:14:30','2026-05-24 13:37:08'),(23,'App\\Models\\User',14,'erp_token','ee1907b6f1e60f1c93b6375382a70acc80bcd0815dacada89fa7da7404b04c9d','[\"*\"]',NULL,NULL,'2026-05-10 07:00:56','2026-05-10 07:00:56'),(24,'App\\Models\\User',14,'erp_token','6a58fd8b24c8d97255b8b43693c2a81dc83a82b0266c43fc17051874e5462bff','[\"*\"]','2026-05-10 11:11:45',NULL,'2026-05-10 07:01:21','2026-05-10 11:11:45'),(25,'App\\Models\\User',1,'erp_token','ff971a2f3d9f6581296b5146b893a11e3f3456bd886693c95ff0c62733ef52f1','[\"*\"]','2026-07-14 10:18:42',NULL,'2026-05-10 07:01:23','2026-07-14 10:18:42'),(26,'App\\Models\\User',14,'erp_token','d0b5a88fdb97b9385bbc74776e619f71c60fee18c781556ec384d033c6cbd742','[\"*\"]',NULL,NULL,'2026-05-10 09:27:35','2026-05-10 09:27:35'),(27,'App\\Models\\User',14,'erp_token','33242330cab434cfab3f408a99195c48b15452e4e70bed16fc1de28a86123ebf','[\"*\"]','2026-05-10 10:08:37',NULL,'2026-05-10 09:28:58','2026-05-10 10:08:37'),(28,'App\\Models\\User',14,'erp_token','6eb2704199e4fce8aeb9e6516ee5cfc4d6040eabdf67d6a13499887e78849695','[\"*\"]','2026-06-23 06:10:06',NULL,'2026-05-10 10:24:36','2026-06-23 06:10:06'),(29,'App\\Models\\User',14,'erp_token','d8b91f1c38b68f31d7efeae6b34c84964e0d6fecd878aae03a150de658793b99','[\"*\"]','2026-05-10 10:58:51',NULL,'2026-05-10 10:26:52','2026-05-10 10:58:51'),(30,'App\\Models\\User',14,'erp_token','bfab5cee025a1a23a98509e0c2b7e1d621dbbf319a0023f95e404740633dec06','[\"*\"]',NULL,NULL,'2026-05-10 12:59:20','2026-05-10 12:59:20'),(31,'App\\Models\\User',14,'erp_token','10014a4026f9b83c31c51ea6e08f2df9140fb8ca8a4a6184636960cedba04f92','[\"*\"]','2026-06-10 18:47:02',NULL,'2026-05-16 06:40:03','2026-06-10 18:47:02'),(32,'App\\Models\\User',14,'erp_token','0f061de4b9ba89489231a46b06617f9c32773b9f08b93647ae4146e3b91a05ee','[\"*\"]',NULL,NULL,'2026-05-19 14:34:18','2026-05-19 14:34:18'),(33,'App\\Models\\User',14,'erp_token','7b4e59e55dfeef4e04a602a503e8934dbc770f09f3d87089099982e8e9d008f8','[\"*\"]','2026-07-09 12:10:24',NULL,'2026-05-19 14:37:06','2026-07-09 12:10:24'),(34,'App\\Models\\User',14,'erp_token','f9483c9306fc84829e6434d7b965dd9d396ff2529a8364380349608cc11e2037','[\"*\"]','2026-06-01 09:54:41',NULL,'2026-06-01 09:53:14','2026-06-01 09:54:41'),(35,'App\\Models\\User',14,'erp_token','09805ff7292714a732c7f93cf03a8e8238526b2db8652a32ee3ce9b40122c03d','[\"*\"]','2026-06-23 06:13:45',NULL,'2026-06-23 06:10:36','2026-06-23 06:13:45'),(36,'App\\Models\\User',14,'erp_token','4c3e6d72bf8a561bd623e5f3d27f83ae66042706a5da515ba82011ddb55fb0f8','[\"*\"]','2026-06-23 06:15:19',NULL,'2026-06-23 06:14:03','2026-06-23 06:15:19'),(37,'App\\Models\\User',14,'erp_token','d81ec87df48e53c99b8d2f8da30dbb130e7a1115f548b0193c2518bb5942946d','[\"*\"]',NULL,NULL,'2026-06-23 06:15:32','2026-06-23 06:15:32'),(38,'App\\Models\\User',14,'erp_token','028737ac1fd8ccd6bd9d62a2dc23175352250099cebc2a1234a40d5b055c2eb6','[\"*\"]','2026-06-23 06:16:49',NULL,'2026-06-23 06:15:50','2026-06-23 06:16:49'),(39,'App\\Models\\User',14,'erp_token','23e14f194a883b2467d2140e906e3cdf8fde79c1340b21c829ad5e3230369529','[\"*\"]','2026-06-23 06:19:11',NULL,'2026-06-23 06:16:58','2026-06-23 06:19:11'),(40,'App\\Models\\User',14,'erp_token','f488b463f21a462c97503a131ede434115dea384881c82564d61e0ac9d78f834','[\"*\"]','2026-06-23 06:21:27',NULL,'2026-06-23 06:21:15','2026-06-23 06:21:27'),(41,'App\\Models\\User',14,'erp_token','5e932cc1741b040c95bd517d454460351108f564c3e1a5a3f980bf09ac0d26ca','[\"*\"]',NULL,NULL,'2026-06-30 11:15:11','2026-06-30 11:15:11'),(42,'App\\Models\\User',14,'erp_token','4caefee6712e9c1ab27a5afb70acc386ccc761756ddd1846cb8cd99df25b699e','[\"*\"]','2026-07-14 05:49:35',NULL,'2026-07-09 12:07:03','2026-07-14 05:49:35'),(43,'App\\Models\\User',1,'erp_token','05518865e3022e3aceb63ad3b03434f06dd4833d8d9fcf8353673cab3b1f18bc','[\"*\"]',NULL,NULL,'2026-07-09 12:07:22','2026-07-09 12:07:22'),(44,'App\\Models\\User',1,'erp_token','91b04ab4e68cf459e0d3606abff3631346550263a00f3c72799c27228eb7f1aa','[\"*\"]',NULL,NULL,'2026-07-09 12:07:32','2026-07-09 12:07:32'),(45,'App\\Models\\User',1,'erp_token','787d7a3dd8ec0f83a5e558eb43c5a2f9add37ef807fb58f601a2c3fdef52fc9b','[\"*\"]','2026-07-09 13:04:35',NULL,'2026-07-09 12:13:29','2026-07-09 13:04:35'),(46,'App\\Models\\User',14,'erp_token','a1972fd754e0bee480c54160515dec78ebe07aea67851c1a0c58e64c77983e71','[\"*\"]','2026-07-14 05:50:24',NULL,'2026-07-09 12:19:11','2026-07-14 05:50:24'),(47,'App\\Models\\User',14,'erp_token','cb5b5b95cfbbd02b662ac6516b5ce9ac94579dc5b1e56a434fda7a7b29595768','[\"*\"]','2026-07-14 06:24:14',NULL,'2026-07-14 06:14:48','2026-07-14 06:24:14'),(48,'App\\Models\\User',14,'erp_token','6cf84fc4d6879183a27ed3c4846af03de8893ecc361da1054bdf17e7f94979f2','[\"*\"]','2026-07-14 06:30:09',NULL,'2026-07-14 06:28:16','2026-07-14 06:30:09'),(49,'App\\Models\\User',14,'erp_token','43580c3b83086dc91a732bf7bf0286e2b826f3a3f2d9cb18d7a25842042547e2','[\"*\"]','2026-07-14 06:33:13',NULL,'2026-07-14 06:30:32','2026-07-14 06:33:13'),(50,'App\\Models\\User',14,'erp_token','cc2474c20e2f21614fa8519c08a78c1af919414316defff5417ff2600769abcc','[\"*\"]','2026-07-14 06:33:59',NULL,'2026-07-14 06:33:52','2026-07-14 06:33:59'),(51,'App\\Models\\User',14,'erp_token','c92f25c78c1bc29abd1cddb49e0ab2a7aa754abb0575932eb04a7d946e461cb1','[\"*\"]',NULL,NULL,'2026-07-14 06:34:33','2026-07-14 06:34:33'),(52,'App\\Models\\User',14,'erp_token','39ed55f4007588c8d21f0a8eedff35b781b30618e15804a510b04773286114c6','[\"*\"]',NULL,NULL,'2026-07-14 06:35:46','2026-07-14 06:35:46'),(53,'App\\Models\\User',14,'erp_token','338916febd3dd912651de057948ff2a56758dea624c9b13d372eb5fcc331124f','[\"*\"]',NULL,NULL,'2026-07-14 06:36:02','2026-07-14 06:36:02'),(54,'App\\Models\\User',14,'erp_token','9a73bf75ac18acc20dea925536207645cd6acc2b164dd48b865a8af0be00a204','[\"*\"]','2026-07-14 06:39:51',NULL,'2026-07-14 06:39:36','2026-07-14 06:39:51'),(55,'App\\Models\\User',14,'erp_token','2ba5724d6127d3f30207f7128e051b9d94d7fef0c4200a8db7d44ed1ccb6c69f','[\"*\"]','2026-07-14 06:44:12',NULL,'2026-07-14 06:40:21','2026-07-14 06:44:12'),(56,'App\\Models\\User',14,'erp_token','eb0967e8b81197e0e8b88f7d28c6c41646e0dffbd60086eb9bba01bf248adbb6','[\"*\"]','2026-07-14 06:44:33',NULL,'2026-07-14 06:44:32','2026-07-14 06:44:33'),(57,'App\\Models\\User',14,'erp_token','4353c354350d1bbbe59d6739fd193ce881d691b77aa0d3e9476868d780173939','[\"*\"]','2026-07-14 06:47:17',NULL,'2026-07-14 06:46:40','2026-07-14 06:47:17'),(58,'App\\Models\\User',14,'erp_token','e318bf026276ed8269edebad96b47d695c8d969906c35521a140abefbd1e47bb','[\"*\"]','2026-07-14 06:48:46',NULL,'2026-07-14 06:47:33','2026-07-14 06:48:46'),(59,'App\\Models\\User',14,'erp_token','57ec915373a3e717e21dec5fe26fd709cdf6903f830a58fffed5af5b6122b91b','[\"*\"]','2026-07-14 06:51:13',NULL,'2026-07-14 06:49:08','2026-07-14 06:51:13'),(60,'App\\Models\\User',14,'erp_token','396ad5df32240a83b3e6578e882ad9cb82608b4c34bc99706f4fed8015b010e2','[\"*\"]',NULL,NULL,'2026-07-14 06:51:31','2026-07-14 06:51:31'),(61,'App\\Models\\User',14,'erp_token','b225695adbb265609cfc369dc1470d0544fa372ff27b3312183626e4bcc89b89','[\"*\"]',NULL,NULL,'2026-07-14 06:52:19','2026-07-14 06:52:19'),(62,'App\\Models\\User',14,'erp_token','37a1773bea1fd63de54301e6dd2e2d21ed9d34b08cc1897ce37bd2aef43cd606','[\"*\"]',NULL,NULL,'2026-07-14 06:52:32','2026-07-14 06:52:32'),(63,'App\\Models\\User',14,'erp_token','f03592f07763b5beda7d4bb7128afe21eb41aaec3b23afa17866e47e7737d386','[\"*\"]',NULL,NULL,'2026-07-14 06:52:45','2026-07-14 06:52:45'),(64,'App\\Models\\User',14,'erp_token','c2b91080dc92b092dfb911b06e614bf614d94f8ac82b3c26d7131c8a329ec4b5','[\"*\"]','2026-07-14 07:10:52',NULL,'2026-07-14 06:55:20','2026-07-14 07:10:52'),(65,'App\\Models\\User',14,'erp_token','b42aaec09bee62b6f9e97e36344388c82c087bb0f05b3cbb7254955341fcc61f','[\"*\"]','2026-07-14 07:12:08',NULL,'2026-07-14 07:11:53','2026-07-14 07:12:08'),(66,'App\\Models\\User',14,'erp_token','911431133fa78df8019e9ade300476e0a6b7bcd428a82af325beb26b040c15ad','[\"*\"]','2026-07-14 07:47:47',NULL,'2026-07-14 07:24:28','2026-07-14 07:47:47'),(67,'App\\Models\\User',14,'erp_token','2124511a39028164d9423ed63647139d9a0af9144bf09d21f1fed107b46857d9','[\"*\"]','2026-07-14 07:55:18',NULL,'2026-07-14 07:48:15','2026-07-14 07:55:18'),(68,'App\\Models\\User',14,'erp_token','00812015b02c33e6afaacf6b6d658ad47df52ceecaa82ef7a5282e069deb0f20','[\"*\"]','2026-07-14 07:55:58',NULL,'2026-07-14 07:55:35','2026-07-14 07:55:58'),(69,'App\\Models\\User',14,'erp_token','526d70933357cae00b1eadb79bdeebaa3008d102cec199caf3a7138eecaaf4e8','[\"*\"]','2026-07-15 16:23:11',NULL,'2026-07-14 07:56:12','2026-07-15 16:23:11'),(70,'App\\Models\\User',14,'erp_token','63b08c84cd1c55126dd795b16b0e2b511c2bf85bb35deb713545a92a17635bb6','[\"*\"]','2026-07-14 09:01:53',NULL,'2026-07-14 09:00:23','2026-07-14 09:01:53'),(71,'App\\Models\\User',14,'erp_token','f5dca42c202a339dd231b83e7174fac9ffdbcb65621b5f182bea65913b15afbe','[\"*\"]','2026-07-14 09:43:29',NULL,'2026-07-14 09:36:38','2026-07-14 09:43:29'),(72,'App\\Models\\User',14,'erp_token','3e3167afccd08a7795f89a4680461c26303922ffae24769c155a608a331aef2d','[\"*\"]',NULL,NULL,'2026-07-14 09:48:41','2026-07-14 09:48:41'),(73,'App\\Models\\User',14,'erp_token','f291bd8d98a9066e8da59cb742cf5ecc494b0bcaa8c6bcaae977ad6cb5d45f79','[\"*\"]','2026-07-15 16:25:24',NULL,'2026-07-15 16:25:14','2026-07-15 16:25:24'),(74,'App\\Models\\User',14,'erp_token','4b22f78d4814f3e8fb992bfd5aecc02c2da6e6428d168f962c300a3a14d13914','[\"*\"]','2026-07-15 16:27:01',NULL,'2026-07-15 16:26:53','2026-07-15 16:27:01'),(75,'App\\Models\\User',14,'erp_token','89dc7243c06f113875e8186e968bf08a18a164cfc7ddb31f09829996e754e927','[\"*\"]','2026-07-15 16:28:50',NULL,'2026-07-15 16:28:48','2026-07-15 16:28:50'),(76,'App\\Models\\User',14,'erp_token','ecae1cd52b0f2d0361ff7fa7696b2c5fe6b68b90aa06dec64ed669ce46061bef','[\"*\"]','2026-07-16 09:48:53',NULL,'2026-07-15 16:29:12','2026-07-16 09:48:53'),(77,'App\\Models\\User',14,'erp_token','259ef992fddc21b3ee3ac25fdfbba79a60988f3d4ac32bf4f195703d2446d711','[\"*\"]','2026-07-15 16:50:03',NULL,'2026-07-15 16:49:48','2026-07-15 16:50:03'),(78,'App\\Models\\User',14,'erp_token','5f09447c3c90589427a374f171fc24ab779bd71809dd6de7e51ffd9e25f423ff','[\"*\"]','2026-07-15 19:06:04',NULL,'2026-07-15 16:50:11','2026-07-15 19:06:04'),(79,'App\\Models\\User',14,'erp_token','0cd9441cc354c62e3901e63346767cccc09f57ed48fbc215b8795a9aaaf01948','[\"*\"]','2026-07-15 19:03:41',NULL,'2026-07-15 18:10:42','2026-07-15 19:03:41'),(80,'App\\Models\\User',14,'erp_token','cc082cc53eba1b2162e04f90dad050a1a501aba0abce75522f033b8135a1f189','[\"*\"]','2026-07-16 13:06:06',NULL,'2026-07-16 09:49:17','2026-07-16 13:06:06'),(81,'App\\Models\\User',14,'erp_token','db41434036a553e05e661a10c8a983443b0784e309750d29a7a82e74473ff332','[\"*\"]','2026-07-18 21:48:56',NULL,'2026-07-18 21:48:24','2026-07-18 21:48:56'),(82,'App\\Models\\User',14,'erp_token','d2cc1477fd518223d60e5e32453c33da1dafc560af701377ffbb50d72090cb9c','[\"*\"]','2026-07-18 22:16:26',NULL,'2026-07-18 22:15:23','2026-07-18 22:16:26'),(83,'App\\Models\\User',1,'test','e22056ca4952a045e0eddb4a5ab9216c0eadb9aeb8481c94429ea0c1ef1aac17','[\"*\"]','2026-08-25 11:38:29',NULL,'2026-08-25 11:38:29','2026-08-25 11:38:29'),(84,'App\\Models\\User',1,'test','225173627aea11de8d6d68f16cda7382080cc876ca553db79f7626d42b02d585','[\"*\"]','2026-08-25 11:39:40',NULL,'2026-08-25 11:39:40','2026-08-25 11:39:40'),(85,'App\\Models\\User',1,'test','40547835c99e15de9da4cf5e8c9d9170a12864a91a68b470193b4a86507796ff','[\"*\"]','2026-08-26 05:29:18',NULL,'2026-08-26 05:29:16','2026-08-26 05:29:18'),(86,'App\\Models\\User',1,'test','7d71d368c10212ccd17caa96736a74fdd96abacfb1e1930c87b3017bacb5260c','[\"*\"]','2026-08-26 05:52:47',NULL,'2026-08-26 05:52:46','2026-08-26 05:52:47'),(87,'App\\Models\\User',1,'test','833d6507a81c3adae1ed2b91a59516b59af43c26570789be7028a55ada615de3','[\"*\"]','2026-08-26 05:53:12',NULL,'2026-08-26 05:53:12','2026-08-26 05:53:12'),(88,'App\\Models\\User',1,'test','dc4d7da15b497d4fe51e46b78491d742bf3d3ac7dbd15d66f287ad515f50855a','[\"*\"]','2026-08-26 06:18:49',NULL,'2026-08-26 06:18:49','2026-08-26 06:18:49'),(89,'App\\Models\\User',200,'erp_token','9b3e6794ac412b5305de4cf345df4e51d8ef838a97bd7b1353340ec71a70dde9','[\"*\"]',NULL,NULL,'2026-10-12 22:07:06','2026-10-12 22:07:06'),(90,'App\\Models\\User',200,'erp_token','2994faa47daba1b1d6863aa6ffce13ac5089f6daca9aa48b58732fca8f689ba1','[\"*\"]',NULL,NULL,'2026-10-12 22:07:08','2026-10-12 22:07:08'),(91,'App\\Models\\User',14,'erp_token','4a96ca5b76b6be058d21244e0f32bb3c2dcc74cc6f9bb6e7a5d3f8d4c7e9b4f0','[\"*\"]','2026-10-23 06:14:17',NULL,'2026-10-23 06:12:30','2026-10-23 06:14:17');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phone_call_logs`
--

DROP TABLE IF EXISTS `phone_call_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phone_call_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `call_type` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `follow_up_date` date DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phone_call_logs`
--

LOCK TABLES `phone_call_logs` WRITE;
/*!40000 ALTER TABLE `phone_call_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `phone_call_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `postal_dispatches`
--

DROP TABLE IF EXISTS `postal_dispatches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `postal_dispatches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `to_title` varchar(255) NOT NULL,
  `reference_no` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date` date NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `postal_dispatches`
--

LOCK TABLES `postal_dispatches` WRITE;
/*!40000 ALTER TABLE `postal_dispatches` DISABLE KEYS */;
/*!40000 ALTER TABLE `postal_dispatches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `postal_receives`
--

DROP TABLE IF EXISTS `postal_receives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `postal_receives` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_title` varchar(255) NOT NULL,
  `reference_no` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date` date NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `postal_receives`
--

LOCK TABLES `postal_receives` WRITE;
/*!40000 ALTER TABLE `postal_receives` DISABLE KEYS */;
/*!40000 ALTER TABLE `postal_receives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1,NULL),(1,7,NULL),(2,1,NULL),(2,7,NULL),(3,1,NULL),(3,7,NULL),(4,1,NULL),(4,7,NULL),(5,1,NULL),(5,7,NULL),(6,1,NULL),(6,7,NULL),(7,1,NULL),(7,7,NULL),(8,1,NULL),(8,7,NULL),(9,1,NULL),(9,7,NULL),(10,1,NULL),(10,7,NULL),(11,1,NULL),(11,7,NULL),(12,1,NULL),(12,7,NULL),(13,1,NULL),(13,7,NULL),(14,1,NULL),(14,7,NULL),(15,1,NULL),(15,7,NULL),(16,1,NULL),(16,7,NULL),(17,1,NULL),(17,7,NULL),(18,1,NULL),(18,7,NULL),(19,1,NULL),(19,7,NULL),(20,1,NULL),(20,7,NULL),(21,1,NULL),(21,7,NULL),(22,1,NULL),(22,7,NULL),(23,1,NULL),(23,7,NULL),(24,1,NULL),(24,7,NULL),(25,1,NULL),(25,7,NULL),(26,1,NULL),(26,7,NULL),(27,1,NULL),(27,7,NULL),(28,1,NULL),(28,7,NULL),(29,1,NULL),(29,7,NULL),(30,1,NULL),(30,7,NULL),(31,1,NULL),(31,7,NULL),(32,1,NULL),(32,7,NULL),(33,1,NULL),(33,7,NULL),(34,1,NULL),(34,7,NULL),(35,1,NULL),(35,7,NULL),(36,1,NULL),(36,7,NULL),(37,1,NULL),(37,7,NULL),(38,1,NULL),(38,7,NULL),(39,1,NULL),(39,2,NULL),(39,5,NULL),(39,7,NULL),(40,1,NULL),(40,7,NULL),(41,1,NULL),(41,7,NULL),(42,1,NULL),(42,7,NULL),(43,1,NULL),(43,7,NULL),(44,1,NULL),(44,7,NULL),(45,1,NULL),(45,2,NULL),(45,7,NULL),(46,1,NULL),(46,2,NULL),(46,7,NULL),(47,1,NULL),(47,2,NULL),(47,7,NULL),(48,1,NULL),(48,2,NULL),(48,7,NULL),(49,1,NULL),(49,7,NULL),(50,1,NULL),(50,5,NULL),(50,7,NULL),(51,1,NULL),(51,5,NULL),(51,7,NULL),(52,1,NULL),(52,5,NULL),(52,7,NULL),(53,1,NULL),(53,5,NULL),(53,7,NULL),(54,1,NULL),(54,7,NULL),(55,13,NULL),(56,13,NULL),(57,13,NULL),(58,13,NULL),(59,1,NULL),(59,7,NULL),(59,15,NULL),(60,1,NULL),(60,7,NULL),(60,15,NULL),(61,1,NULL),(61,7,NULL),(61,15,NULL),(62,1,NULL),(62,7,NULL),(62,15,NULL),(63,1,NULL),(63,7,NULL),(64,1,NULL),(64,7,NULL),(65,9,NULL),(65,13,NULL),(65,16,NULL),(66,9,NULL),(66,13,NULL),(66,16,NULL),(67,9,NULL),(67,13,NULL),(67,16,NULL),(68,9,NULL),(68,13,NULL),(68,16,NULL),(69,9,NULL),(69,13,NULL),(70,9,NULL),(70,13,NULL);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','web','2026-02-14 00:57:50','2026-02-14 00:57:50',NULL),(2,'teacher','web','2026-02-14 00:57:50','2026-02-14 00:57:50',NULL),(3,'staff','web','2026-02-14 00:57:50','2026-02-14 00:57:50',NULL),(4,'student','web',NULL,NULL,NULL),(5,'parent','web','2026-04-10 14:42:38','2026-04-10 14:42:38',NULL),(7,'super-admin','web','2026-10-19 02:39:32','2026-10-19 02:39:32',NULL),(8,'super-admin','api','2026-10-19 02:39:32','2026-10-19 02:39:32',NULL),(9,'super-admin','sanctum','2026-10-19 02:39:32','2026-10-19 02:39:32',NULL),(10,'student','sanctum','2026-10-23 04:07:23','2026-10-23 04:07:23',NULL),(11,'parent','sanctum','2026-10-23 04:07:23','2026-10-23 04:07:23',NULL),(12,'teacher','sanctum','2026-10-23 04:07:23','2026-10-23 04:07:23',NULL),(13,'admin','sanctum','2026-10-23 04:07:23','2026-10-23 04:07:23',NULL),(14,'staff','sanctum','2026-10-23 04:07:23','2026-10-23 04:07:23',NULL),(15,'driver','web','2026-10-28 22:58:55','2026-10-28 22:58:55',NULL),(16,'driver','sanctum','2026-10-28 22:58:55','2026-10-28 22:58:55',NULL);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routes`
--

DROP TABLE IF EXISTS `routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `routes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `fare` varchar(255) DEFAULT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `routes_branch_id_foreign` (`branch_id`),
  CONSTRAINT `routes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routes`
--

LOCK TABLES `routes` WRITE;
/*!40000 ALTER TABLE `routes` DISABLE KEYS */;
/*!40000 ALTER TABLE `routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_classes`
--

DROP TABLE IF EXISTS `school_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `school_classes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_classes`
--

LOCK TABLES `school_classes` WRITE;
/*!40000 ALTER TABLE `school_classes` DISABLE KEYS */;
/*!40000 ALTER TABLE `school_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sections_class_id_foreign` (`class_id`),
  CONSTRAINT `sections_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES (1,4,'Science','2026-03-21 03:33:35','2026-03-21 03:33:35',NULL),(2,4,'Humanities','2026-03-21 03:33:35','2026-03-21 03:33:35',NULL),(3,5,'Section A','2026-03-21 06:11:09','2026-03-21 06:11:09',NULL),(4,6,'A','2026-03-30 10:46:25','2026-03-30 10:46:25',NULL),(5,7,'A','2026-04-10 14:42:45','2026-04-10 14:42:45',NULL),(6,7,'B','2026-04-10 14:42:45','2026-04-10 14:42:45',NULL),(7,8,'A','2026-04-10 14:42:45','2026-04-10 14:42:45',NULL),(8,8,'B','2026-04-10 14:42:45','2026-04-10 14:42:45',NULL),(9,9,'A','2026-04-10 14:42:45','2026-04-10 14:42:45',NULL),(10,9,'B','2026-04-10 14:42:45','2026-04-10 14:42:45',NULL),(11,10,'A','2026-04-10 14:42:45','2026-04-10 14:42:45',NULL),(12,10,'B','2026-04-10 14:42:45','2026-04-10 14:42:45',NULL),(13,11,'A','2026-04-10 14:42:45','2026-04-10 14:42:45',NULL),(14,11,'B','2026-04-10 14:42:45','2026-04-10 14:42:45',NULL),(53,NULL,'Section Global X','2026-11-01 02:58:34','2026-11-01 02:58:34',NULL),(54,NULL,'Section Global X','2026-11-01 03:27:36','2026-11-01 03:27:36',NULL),(56,NULL,'Section Global X','2026-11-01 03:28:40','2026-11-01 03:28:40',NULL);
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shared_contents`
--

DROP TABLE IF EXISTS `shared_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shared_contents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` bigint(20) unsigned NOT NULL,
  `shared_with_role` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shared_contents_content_id_foreign` (`content_id`),
  CONSTRAINT `shared_contents_content_id_foreign` FOREIGN KEY (`content_id`) REFERENCES `download_contents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shared_contents`
--

LOCK TABLES `shared_contents` WRITE;
/*!40000 ALTER TABLE `shared_contents` DISABLE KEYS */;
/*!40000 ALTER TABLE `shared_contents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sidebar_menus`
--

DROP TABLE IF EXISTS `sidebar_menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sidebar_menus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `section` varchar(100) NOT NULL,
  `title` varchar(150) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sidebar_menus_section_index` (`section`),
  KEY `sidebar_menus_order_index` (`order`),
  KEY `sidebar_menus_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sidebar_menus`
--

LOCK TABLES `sidebar_menus` WRITE;
/*!40000 ALTER TABLE `sidebar_menus` DISABLE KEYS */;
INSERT INTO `sidebar_menus` VALUES (1,'Administration','Admin Section','/sidebar_icons/admin_section.svg','/admin-section',1,1,'2026-10-27 23:03:13','2026-10-27 23:03:13',NULL),(2,'Administration','Academics','/sidebar_icons/academics.svg','/academics',2,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(3,'Administration','Study Material','/sidebar_icons/study_material.svg','/study-material',3,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(4,'Administration','Lesson Plan','/sidebar_icons/lesson_plan.svg','/lesson-plan',4,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(5,'Administration','Bulk Print','/sidebar_icons/bulk_print.svg','/bulk-print',5,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(6,'Administration','Download Center','/sidebar_icons/download_center.svg','/download-center',6,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(7,'Student','Student Info','/sidebar_icons/student_info.svg','/student-info',7,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(8,'Student','Remarks Point','/sidebar_icons/behaviour_records.svg','/behaviour-records',8,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(9,'Student','Fees','/sidebar_icons/fees.svg','/fees',9,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(10,'Student','Home Work','/sidebar_icons/home_work.svg','/home-work',10,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(11,'Student','Library','/sidebar_icons/library.svg','/library',11,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(12,'Student','Transport','/sidebar_icons/transport.svg','/transport',12,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(13,'Student','YES Garden','/sidebar_icons/dormitory.svg','/dormitory',13,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(14,'HR','Human Resource','/sidebar_icons/admin_section.svg','/human-resource',14,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(15,'Exam','Examination','/sidebar_icons/examination.svg','/examination',15,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(16,'Exam','Exam Plan','/sidebar_icons/exam_plan.svg','/exam-plan',16,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(17,'Exam','Online Exam','/sidebar_icons/online_exam.svg','/online_exam',17,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(18,'Settings Section','General Settings','/sidebar_icons/examination.svg','/settings-section',18,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL);
/*!40000 ALTER TABLE `sidebar_menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sidebar_sub_menus`
--

DROP TABLE IF EXISTS `sidebar_sub_menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sidebar_sub_menus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sidebar_menu_id` bigint(20) unsigned NOT NULL,
  `title` varchar(150) NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sidebar_sub_menus_sidebar_menu_id_index` (`sidebar_menu_id`),
  KEY `sidebar_sub_menus_order_index` (`order`),
  KEY `sidebar_sub_menus_is_active_index` (`is_active`),
  CONSTRAINT `sidebar_sub_menus_sidebar_menu_id_foreign` FOREIGN KEY (`sidebar_menu_id`) REFERENCES `sidebar_menus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sidebar_sub_menus`
--

LOCK TABLES `sidebar_sub_menus` WRITE;
/*!40000 ALTER TABLE `sidebar_sub_menus` DISABLE KEYS */;
INSERT INTO `sidebar_sub_menus` VALUES (1,1,'Admission Query',NULL,1,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(2,1,'Complaint',NULL,2,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(3,1,'Postal Receive',NULL,3,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(4,1,'Postal Dispatch',NULL,4,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(5,1,'Phone Call Log',NULL,5,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(6,1,'Admin Setup',NULL,6,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(7,1,'ID Card',NULL,7,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(8,1,'Certificate',NULL,8,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(9,1,'Generate Certificate',NULL,9,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(10,1,'Generate ID Card',NULL,10,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(11,2,'Optional Subject',NULL,1,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(12,2,'Section',NULL,2,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(13,2,'Class',NULL,3,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(14,2,'Subjects',NULL,4,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(15,2,'Assign Class Teacher',NULL,5,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(16,2,'Assign Subject',NULL,6,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(17,2,'Class Room ',NULL,7,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(18,2,'Class Routine',NULL,8,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(19,3,'Upload Content',NULL,1,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(20,3,'Assignment',NULL,2,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(21,3,'Syllabus',NULL,3,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(22,3,'Other Downloads',NULL,4,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(23,6,'Content Type',NULL,1,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(24,6,'Content List',NULL,2,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(25,6,'Shared Content List',NULL,3,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(26,6,'Video List',NULL,4,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(27,7,'Student Category',NULL,1,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(28,7,'Add Student',NULL,2,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(29,7,'Student List',NULL,3,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(30,7,'Multi Class Student',NULL,4,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(31,7,'Delete Student Record',NULL,5,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(32,7,'Unassigned Student',NULL,6,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(33,7,'Student Attendance',NULL,7,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(34,7,'Student Group',NULL,8,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(35,7,'Student Promote',NULL,9,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(36,7,'Disabled Students',NULL,10,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(37,7,'Subject Wise Attendance',NULL,11,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(38,7,'Student Export',NULL,12,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(39,7,'SMS Sending Time',NULL,13,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(40,7,'Student Settings',NULL,14,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(41,8,'Incidents',NULL,1,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(42,8,'Assign Incident',NULL,2,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(43,8,'Student Incident Report',NULL,3,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(44,8,'Remarks Report',NULL,4,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(45,8,'Incident Wise report',NULL,5,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(46,8,'Settings',NULL,6,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(47,9,'Fees Group',NULL,1,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(48,9,'Fees Type',NULL,2,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(49,9,'Fees Invoice',NULL,3,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(50,9,'Bank Payment',NULL,4,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(51,9,'Fees Carry Forward',NULL,5,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(52,14,'Add Staff',NULL,1,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(53,14,'Staff List',NULL,2,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL),(54,18,'General Settings',NULL,1,1,'2026-10-27 23:03:14','2026-10-27 23:03:14',NULL);
/*!40000 ALTER TABLE `sidebar_sub_menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staff` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `staff_code` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `date_of_joining` date DEFAULT NULL,
  `marital_status` varchar(255) DEFAULT NULL,
  `emergency_mobile` varchar(255) DEFAULT NULL,
  `driving_license` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `is_expert_staff` tinyint(1) NOT NULL DEFAULT 0,
  `current_address` text DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `qualifications` text DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `twitter_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `joining_letter` varchar(255) DEFAULT NULL,
  `other_document` varchar(255) DEFAULT NULL,
  `bank_account_name` varchar(255) DEFAULT NULL,
  `bank_account_no` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_branch_name` varchar(255) DEFAULT NULL,
  `epf_no` varchar(255) DEFAULT NULL,
  `basic_salary` decimal(15,2) DEFAULT NULL,
  `contract_type` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff`
--

LOCK TABLES `staff` WRITE;
/*!40000 ALTER TABLE `staff` DISABLE KEYS */;
INSERT INTO `staff` VALUES (1,NULL,'staff1@school.com','+1 (814) 656-5259','Assistant',NULL,'2026-04-10 14:42:49','2026-04-10 14:42:49',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,NULL,'staff2@school.com','+15125853947','Assistant',NULL,'2026-04-10 14:42:50','2026-04-10 14:42:50',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,NULL,'staff3@school.com','712.209.9717','Assistant',NULL,'2026-04-10 14:42:50','2026-04-10 14:42:50',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,97,'rkvishnuprakash@gmail.com',NULL,'Instructor',NULL,'2026-07-18 23:02:21','2026-07-18 23:02:21',NULL,'TCH-0097','Teacher','Test','Teacher',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,100,'rkvishnuprakash@gmail.com',NULL,'Instructor',NULL,'2026-07-18 23:04:09','2026-07-18 23:04:09',NULL,'TCH-0100','Teacher','Test','Teacher',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,103,'rkvishnuprakash@gmail.com',NULL,'Instructor',NULL,'2026-07-18 23:05:09','2026-07-18 23:05:09',NULL,'TCH-0103','Teacher','Test','Teacher',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,166,'teststaff_1787677780@example.com',NULL,NULL,NULL,'2026-08-25 11:39:40','2026-08-25 11:39:42','2026-08-25 11:39:42',NULL,'Teacher','UpdatedStaff','Staff','UpdatedStaff Staff',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(26,188,'bulkstaff_1787743392_1@example.com',NULL,NULL,'Teaching','2026-08-26 05:53:17','2026-08-26 05:53:17',NULL,'STF_BULK_7536','Teacher','Bulk1','Staff','Bulk1 Staff',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(27,15,NULL,NULL,'Senior Teacher',NULL,'2026-10-27 11:26:36','2026-10-27 11:26:36',NULL,NULL,'Teacher',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2001-09-27',NULL,NULL,NULL,NULL,0,NULL,NULL,'MA/MSc',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(28,16,NULL,NULL,'Senior Teacher',NULL,'2026-10-27 11:26:36','2026-10-27 11:26:36',NULL,NULL,'Teacher',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-06',NULL,NULL,NULL,NULL,0,NULL,NULL,'MA/MSc',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(29,17,NULL,NULL,'Senior Teacher',NULL,'2026-10-27 11:26:37','2026-10-27 11:26:37',NULL,NULL,'Teacher',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-03-13',NULL,NULL,NULL,NULL,0,NULL,NULL,'MA/MSc',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(30,18,NULL,NULL,'Senior Teacher',NULL,'2026-10-27 11:26:37','2026-10-27 11:26:37',NULL,NULL,'Teacher',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2005-12-01',NULL,NULL,NULL,NULL,0,NULL,NULL,'MA/MSc',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(31,19,NULL,NULL,'Senior Teacher',NULL,'2026-10-27 11:26:37','2026-10-27 11:26:37',NULL,NULL,'Teacher',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1981-09-17',NULL,NULL,NULL,NULL,0,NULL,NULL,'MA/MSc',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_attendances`
--

DROP TABLE IF EXISTS `staff_attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staff_attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` bigint(20) unsigned DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_attendances`
--

LOCK TABLES `staff_attendances` WRITE;
/*!40000 ALTER TABLE `staff_attendances` DISABLE KEYS */;
/*!40000 ALTER TABLE `staff_attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_departments`
--

DROP TABLE IF EXISTS `staff_departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staff_departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_departments`
--

LOCK TABLES `staff_departments` WRITE;
/*!40000 ALTER TABLE `staff_departments` DISABLE KEYS */;
INSERT INTO `staff_departments` VALUES (1,'Academic','2026-04-10 14:42:44','2026-04-10 14:42:44',NULL),(2,'Administration','2026-04-10 14:42:44','2026-04-10 14:42:44',NULL),(3,'Accounts','2026-04-10 14:42:44','2026-04-10 14:42:44',NULL),(4,'Library','2026-04-10 14:42:44','2026-04-10 14:42:44',NULL),(5,'Transport','2026-04-10 14:42:44','2026-04-10 14:42:44',NULL);
/*!40000 ALTER TABLE `staff_departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_academics`
--

DROP TABLE IF EXISTS `student_academics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_academics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `class_id` bigint(20) unsigned NOT NULL,
  `section_id` bigint(20) unsigned DEFAULT NULL,
  `roll_no` varchar(255) DEFAULT NULL,
  `academic_year_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_academics_student_id_foreign` (`student_id`),
  KEY `student_academics_class_id_foreign` (`class_id`),
  KEY `student_academics_section_id_foreign` (`section_id`),
  KEY `student_academics_academic_year_id_foreign` (`academic_year_id`),
  CONSTRAINT `student_academics_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
  CONSTRAINT `student_academics_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `student_academics_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`),
  CONSTRAINT `student_academics_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_academics`
--

LOCK TABLES `student_academics` WRITE;
/*!40000 ALTER TABLE `student_academics` DISABLE KEYS */;
INSERT INTO `student_academics` VALUES (1,5,5,3,NULL,7,'2026-03-21 06:11:09','2026-03-21 06:11:09',NULL),(2,6,6,4,'101',7,'2026-03-30 10:53:40','2026-03-30 10:53:40',NULL),(3,7,7,5,'1',7,'2026-04-10 14:48:24','2026-04-10 14:48:24',NULL),(4,8,7,5,'2',7,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL),(5,9,7,5,'3',7,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL),(6,10,7,5,'4',7,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL),(7,11,7,5,'5',7,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL),(8,12,7,5,'6',7,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL),(9,13,7,5,'7',7,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL),(10,14,7,5,'8',7,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL),(11,15,7,5,'9',7,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL),(12,16,7,5,'10',7,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL),(13,19,4,2,NULL,1,'2026-04-12 04:08:00','2026-04-12 04:08:00',NULL),(14,20,4,2,NULL,1,'2026-04-12 04:10:02','2026-04-12 04:10:02',NULL),(15,21,4,2,NULL,1,'2026-04-12 04:15:58','2026-04-12 04:15:58',NULL),(16,23,4,2,NULL,1,'2026-04-17 01:31:24','2026-04-17 01:31:24',NULL),(17,24,3,6,NULL,6,'2026-05-10 07:15:42','2026-05-10 07:15:42',NULL),(18,25,3,5,NULL,7,'2026-05-10 10:33:21','2026-05-10 10:33:21',NULL),(19,26,7,5,NULL,7,'2026-05-10 10:48:11','2026-05-10 10:48:11',NULL),(20,27,7,5,NULL,7,'2026-05-10 10:50:47','2026-05-10 10:50:47',NULL),(21,28,7,5,NULL,7,'2026-05-10 10:53:04','2026-05-10 10:53:04',NULL),(22,29,7,5,NULL,7,'2026-05-10 10:53:53','2026-05-10 10:53:53',NULL),(23,30,11,5,NULL,6,'2026-06-04 14:53:58','2026-06-04 14:53:58',NULL),(26,34,3,1,NULL,1,'2026-07-18 23:02:25','2026-07-18 23:02:25',NULL),(27,35,3,1,NULL,1,'2026-07-18 23:04:25','2026-07-18 23:04:25',NULL),(28,36,3,1,NULL,1,'2026-07-18 23:05:11','2026-07-18 23:05:11',NULL),(30,39,5,3,NULL,7,'2026-08-04 22:27:08','2026-08-04 22:27:08',NULL),(31,41,5,3,NULL,7,'2026-08-05 01:03:56','2026-08-05 01:03:56',NULL),(32,40,5,3,NULL,7,'2026-08-05 01:03:56','2026-08-05 01:03:56',NULL),(46,67,3,1,NULL,1,'2026-10-23 05:37:34','2026-10-23 05:37:34',NULL),(47,68,3,1,NULL,1,'2026-10-23 05:39:00','2026-10-23 05:39:00',NULL),(48,69,9,1,NULL,1,'2026-10-23 06:14:26','2026-10-23 06:14:26',NULL),(49,5,3,3,'999',1,'2026-11-01 02:58:42','2026-11-01 03:24:46',NULL),(50,5,3,NULL,'999',1,'2026-11-01 03:27:37','2026-11-01 03:27:37',NULL),(51,5,3,NULL,'999',1,'2026-11-01 03:28:40','2026-11-01 03:28:40',NULL);
/*!40000 ALTER TABLE `student_academics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_attendances`
--

DROP TABLE IF EXISTS `student_attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned DEFAULT NULL,
  `class_id` bigint(20) unsigned DEFAULT NULL,
  `section_id` bigint(20) unsigned DEFAULT NULL,
  `academic_year_id` bigint(20) unsigned DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_attendances_student_id_foreign` (`student_id`),
  KEY `student_attendances_class_id_foreign` (`class_id`),
  KEY `student_attendances_section_id_foreign` (`section_id`),
  KEY `student_attendances_academic_year_id_foreign` (`academic_year_id`),
  CONSTRAINT `student_attendances_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL,
  CONSTRAINT `student_attendances_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `student_attendances_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL,
  CONSTRAINT `student_attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_attendances`
--

LOCK TABLES `student_attendances` WRITE;
/*!40000 ALTER TABLE `student_attendances` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_categories`
--

DROP TABLE IF EXISTS `student_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `class_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`class_ids`)),
  `class_pattern` varchar(255) DEFAULT NULL,
  `stream` varchar(255) DEFAULT NULL,
  `min_class_level` int(11) DEFAULT NULL,
  `max_class_level` int(11) DEFAULT NULL,
  `is_auto_assign` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_categories`
--

LOCK TABLES `student_categories` WRITE;
/*!40000 ALTER TABLE `student_categories` DISABLE KEYS */;
INSERT INTO `student_categories` VALUES (1,'API Test Category','Created via test script',NULL,NULL,NULL,NULL,NULL,1,'2026-03-25 23:49:42','2026-03-25 23:49:42','2026-03-25 23:49:42'),(2,'General','General Category',NULL,NULL,NULL,NULL,NULL,1,'2026-03-30 10:46:25','2026-03-30 10:46:25',NULL),(3,'OBC',NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-04-10 14:42:44','2026-04-10 14:42:44',NULL),(4,'SC',NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-04-10 14:42:44','2026-04-10 14:42:44',NULL),(5,'ST',NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-04-10 14:42:44','2026-04-10 14:42:44',NULL),(6,'Rainbow - 3','Only 3rd Class',NULL,NULL,NULL,NULL,NULL,1,'2026-10-21 06:23:45','2026-10-21 06:23:45',NULL),(7,'Rainbow - 4','Only 4th Class',NULL,NULL,NULL,NULL,NULL,1,'2026-10-21 06:23:45','2026-10-21 06:23:45',NULL),(8,'Rainbow - 5','Only 5th Class',NULL,NULL,NULL,NULL,NULL,1,'2026-10-21 06:23:45','2026-10-21 06:23:45',NULL),(9,'Planet','6th to 8th Class',NULL,NULL,NULL,NULL,NULL,1,'2026-10-21 06:23:45','2026-10-21 06:23:45',NULL),(10,'Galaxy HS','9th to 10th Class',NULL,NULL,NULL,NULL,NULL,1,'2026-10-21 06:23:45','2026-10-21 06:23:45',NULL),(11,'Galaxy HSS (Arts)','11th and 12th Class (Arts Only)',NULL,NULL,NULL,NULL,NULL,1,'2026-10-21 06:23:45','2026-10-21 06:23:45',NULL);
/*!40000 ALTER TABLE `student_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_documents`
--

DROP TABLE IF EXISTS `student_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_documents_student_id_foreign` (`student_id`),
  CONSTRAINT `student_documents_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_documents`
--

LOCK TABLES `student_documents` WRITE;
/*!40000 ALTER TABLE `student_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_emergency_contacts`
--

DROP TABLE IF EXISTS `student_emergency_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_emergency_contacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `relation` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_emergency_contacts_student_id_foreign` (`student_id`),
  CONSTRAINT `student_emergency_contacts_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_emergency_contacts`
--

LOCK TABLES `student_emergency_contacts` WRITE;
/*!40000 ALTER TABLE `student_emergency_contacts` DISABLE KEYS */;
INSERT INTO `student_emergency_contacts` VALUES (1,69,'Bob','Uncle','1234','2026-10-23 06:14:26','2026-10-23 06:14:26',NULL);
/*!40000 ALTER TABLE `student_emergency_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_fees`
--

DROP TABLE IF EXISTS `student_fees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_fees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned DEFAULT NULL,
  `fee_structure_id` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `due_date` date DEFAULT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_fees_student_id_foreign` (`student_id`),
  CONSTRAINT `student_fees_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_fees`
--

LOCK TABLES `student_fees` WRITE;
/*!40000 ALTER TABLE `student_fees` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_fees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_groups`
--

DROP TABLE IF EXISTS `student_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_groups_branch_id_foreign` (`branch_id`),
  CONSTRAINT `student_groups_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_groups`
--

LOCK TABLES `student_groups` WRITE;
/*!40000 ALTER TABLE `student_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_incidents`
--

DROP TABLE IF EXISTS `student_incidents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_incidents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `incident_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `point` int(11) NOT NULL DEFAULT 0,
  `is_negative` tinyint(1) NOT NULL DEFAULT 0,
  `date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `assigned_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_incidents_branch_id_index` (`branch_id`),
  KEY `student_incidents_student_id_index` (`student_id`),
  KEY `student_incidents_incident_id_index` (`incident_id`),
  KEY `student_incidents_assigned_by_index` (`assigned_by`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_incidents`
--

LOCK TABLES `student_incidents` WRITE;
/*!40000 ALTER TABLE `student_incidents` DISABLE KEYS */;
INSERT INTO `student_incidents` VALUES (1,NULL,6,2,'Academic Excellence',10,0,'2026-10-31','Awarded for term test',NULL,'2026-10-30 23:58:09','2026-10-30 23:58:09',NULL),(2,NULL,6,3,'Late to Class',3,1,'2026-10-31',NULL,NULL,'2026-10-31 00:15:09','2026-10-31 00:15:09',NULL);
/*!40000 ALTER TABLE `student_incidents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_multi_class`
--

DROP TABLE IF EXISTS `student_multi_class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_multi_class` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `class_id` bigint(20) unsigned NOT NULL,
  `section_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_multi_class_student_id_foreign` (`student_id`),
  KEY `student_multi_class_class_id_foreign` (`class_id`),
  KEY `student_multi_class_section_id_foreign` (`section_id`),
  CONSTRAINT `student_multi_class_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `student_multi_class_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`),
  CONSTRAINT `student_multi_class_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_multi_class`
--

LOCK TABLES `student_multi_class` WRITE;
/*!40000 ALTER TABLE `student_multi_class` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_multi_class` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_multi_classes`
--

DROP TABLE IF EXISTS `student_multi_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_multi_classes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned DEFAULT NULL,
  `class_id` bigint(20) unsigned DEFAULT NULL,
  `section_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_multi_classes_student_id_foreign` (`student_id`),
  CONSTRAINT `student_multi_classes_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_multi_classes`
--

LOCK TABLES `student_multi_classes` WRITE;
/*!40000 ALTER TABLE `student_multi_classes` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_multi_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_parents`
--

DROP TABLE IF EXISTS `student_parents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_parents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `father_phone` varchar(255) DEFAULT NULL,
  `father_email` varchar(255) DEFAULT NULL,
  `father_occupation` varchar(255) DEFAULT NULL,
  `father_photo` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `mother_phone` varchar(255) DEFAULT NULL,
  `mother_email` varchar(255) DEFAULT NULL,
  `mother_occupation` varchar(255) DEFAULT NULL,
  `mother_photo` varchar(255) DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `guardian_phone` varchar(255) DEFAULT NULL,
  `guardian_occupation` varchar(255) DEFAULT NULL,
  `guardian_email` varchar(255) DEFAULT NULL,
  `guardian_relation` varchar(255) DEFAULT NULL,
  `guardian_address` text DEFAULT NULL,
  `guardian_photo` varchar(255) DEFAULT NULL,
  `guardian_is` varchar(255) DEFAULT NULL COMMENT 'Father, Mother, Other',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_parents_student_id_foreign` (`student_id`),
  CONSTRAINT `student_parents_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_parents`
--

LOCK TABLES `student_parents` WRITE;
/*!40000 ALTER TABLE `student_parents` DISABLE KEYS */;
INSERT INTO `student_parents` VALUES (1,5,'John Doe','1234567890',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-03-25 23:49:42','2026-03-25 23:49:42','2026-03-25 23:49:42'),(2,6,'Michael Doe','1122334455',NULL,NULL,NULL,'Sarah Doe','5544332211',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-03-30 10:46:25','2026-03-30 10:46:25',NULL),(3,7,'Darrion Bogan I','850-659-1894',NULL,NULL,NULL,'Madonna Powlowski','(346) 724-0651',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-10 14:48:24','2026-04-10 14:48:24',NULL),(4,8,'Murphy Ziemann','1-320-219-4249',NULL,NULL,NULL,'Juliet Goldner','(870) 651-1533',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL),(5,9,'Webster Harber','478.398.5302',NULL,NULL,NULL,'Theodora Quitzon','229.707.1144',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL),(6,10,'Sherman Wehner','727-466-2302',NULL,NULL,NULL,'Laurie Leuschke','(920) 430-9536',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL),(7,11,'Dalton Waters','361-767-7026',NULL,NULL,NULL,'Dr. Aleen Conroy MD','220-323-8533',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL),(8,12,'Dr. Wellington Zboncak PhD','858-480-0766',NULL,NULL,NULL,'Susan Runolfsdottir II','+16783766149',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL),(9,13,'Dr. Juvenal Altenwerth','(838) 219-1625',NULL,NULL,NULL,'Elinor Pouros','484-970-2263',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL),(10,14,'Mr. Donny Lemke DDS','+1-734-333-1113',NULL,NULL,NULL,'Yessenia Littel','630-838-9852',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL),(11,15,'Mr. Marquis Feest DDS','626.932.4927',NULL,NULL,NULL,'Prof. Minnie Blanda','703-326-6329',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL),(12,16,'Norris Flatley','630.375.5839',NULL,NULL,NULL,'Darlene Schuster','260.841.3346',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL),(13,19,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-12 04:08:00','2026-04-12 04:08:00',NULL),(14,20,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-12 04:10:02','2026-04-12 04:10:02',NULL),(15,21,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-12 04:15:58','2026-04-12 04:15:58',NULL),(16,23,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-17 01:31:24','2026-04-17 01:31:24',NULL),(17,24,'fshgdh','86464656',NULL,NULL,NULL,'gdggdh','65464646',NULL,NULL,NULL,NULL,NULL,NULL,'a@gmail.com',NULL,'gfgdfsfgdsh',NULL,NULL,'2026-05-10 07:15:42','2026-05-10 07:15:42',NULL),(18,25,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-10 10:33:21','2026-05-10 10:33:21',NULL),(19,26,'Ramesh Nair','9999999991','father@example.com',NULL,NULL,'Lakshmi Nair','9999999992','mother@example.com',NULL,NULL,'Ramesh Nair','9999999991',NULL,NULL,'Father',NULL,NULL,NULL,'2026-05-10 10:48:11','2026-05-10 10:48:11',NULL),(20,27,'Raj Kumar','9999999999',NULL,NULL,NULL,'Anita Kumar','8888888888',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-10 10:50:47','2026-05-10 10:50:47',NULL),(21,28,'Raj Kumar','9999999999',NULL,NULL,NULL,'Anita Kumar','8888888888',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-10 10:53:04','2026-05-10 10:53:04',NULL),(22,29,'Raj Kumar','9999999999',NULL,NULL,NULL,'Anita Kumar','8888888888',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-10 10:53:53','2026-05-10 10:53:53',NULL),(23,30,'Abdul Azeez','9874653210',NULL,NULL,NULL,'Amina VP','8745632109',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-06-04 14:53:58','2026-06-04 14:53:58',NULL),(27,34,'Father Prakash','1234567890','rkvishnuprakash@gmail.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Father','2026-07-18 23:02:25','2026-07-18 23:02:25',NULL),(28,35,'Father Prakash','1234567890','rkvishnuprakash@gmail.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Father','2026-07-18 23:04:25','2026-07-18 23:04:25',NULL),(29,36,'Father Prakash','1234567890','rkvishnuprakash@gmail.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Father','2026-07-18 23:05:11','2026-07-18 23:05:11',NULL),(32,39,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-04 22:27:08','2026-08-04 22:27:08',NULL),(33,40,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-05 01:03:56','2026-08-05 01:03:56',NULL),(34,41,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-05 01:03:56','2026-08-05 01:03:56',NULL),(50,67,'AutoTest Parent','9876543211','parent.9436@example.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'father','2026-10-23 05:37:31','2026-10-23 05:37:31',NULL),(52,69,'Richard Doe','0987654321','father@example.com','Engineer',NULL,'Jane Doe','1122334455','mother@example.com','Teacher',NULL,'Uncle Bob','6677889900',NULL,NULL,'Uncle',NULL,NULL,'Father','2026-10-23 06:14:23','2026-10-23 06:14:23',NULL),(57,74,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-11-01 03:24:44','2026-11-01 03:24:44',NULL),(58,5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-11-01 03:24:46','2026-11-01 03:24:46',NULL),(59,75,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-11-01 03:25:24','2026-11-01 03:25:24',NULL),(61,77,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-11-01 03:27:50','2026-11-01 03:27:50',NULL),(66,82,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-11-01 03:28:12','2026-11-01 03:28:12',NULL);
/*!40000 ALTER TABLE `student_parents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `admission_no` varchar(255) NOT NULL,
  `student_code` varchar(255) DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  `roll_no` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `religion` varchar(255) DEFAULT NULL,
  `caste` varchar(255) DEFAULT NULL,
  `blood_group` varchar(255) DEFAULT NULL,
  `height` varchar(255) DEFAULT NULL,
  `weight` varchar(255) DEFAULT NULL,
  `as_on_date` date DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `emergency_phone` varchar(255) DEFAULT NULL,
  `alternate_phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `student_group_id` bigint(20) unsigned DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `class_id` bigint(20) unsigned DEFAULT NULL,
  `section_id` bigint(20) unsigned DEFAULT NULL,
  `academic_year_id` bigint(20) unsigned DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `nationality` varchar(255) DEFAULT NULL,
  `current_address` text DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `bank_account_no` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `ifsc_code` varchar(255) DEFAULT NULL,
  `national_id_no` varchar(255) DEFAULT NULL,
  `local_id_no` varchar(255) DEFAULT NULL,
  `birth_certificate_no` varchar(255) DEFAULT NULL,
  `apaar_id` varchar(255) DEFAULT NULL,
  `aadharshila_no` varchar(255) DEFAULT NULL,
  `pen_no` varchar(255) DEFAULT NULL,
  `previous_school_name` varchar(255) DEFAULT NULL,
  `previous_qualification` varchar(255) DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `previous_school_details` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `is_disabled` tinyint(1) NOT NULL DEFAULT 0,
  `disable_reason` varchar(255) DEFAULT NULL,
  `disable_date` date DEFAULT NULL,
  `route_id` bigint(20) unsigned DEFAULT NULL,
  `vehicle_id` bigint(20) unsigned DEFAULT NULL,
  `dormitory_id` bigint(20) unsigned DEFAULT NULL,
  `room_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_admission_no_unique` (`admission_no`),
  UNIQUE KEY `students_student_code_unique` (`student_code`),
  KEY `students_category_id_foreign` (`category_id`),
  KEY `students_user_id_foreign` (`user_id`),
  KEY `students_class_id_foreign` (`class_id`),
  KEY `students_section_id_foreign` (`section_id`),
  KEY `students_student_group_id_foreign` (`student_group_id`),
  KEY `students_route_id_foreign` (`route_id`),
  KEY `students_vehicle_id_foreign` (`vehicle_id`),
  KEY `students_dormitory_id_foreign` (`dormitory_id`),
  KEY `students_room_id_foreign` (`room_id`),
  KEY `students_academic_year_id_foreign` (`academic_year_id`),
  CONSTRAINT `students_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL,
  CONSTRAINT `students_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `student_categories` (`id`),
  CONSTRAINT `students_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_dormitory_id_foreign` FOREIGN KEY (`dormitory_id`) REFERENCES `dormitories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `students_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `dormitory_rooms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `students_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `students_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_student_group_id_foreign` FOREIGN KEY (`student_group_id`) REFERENCES `student_groups` (`id`) ON DELETE SET NULL,
  CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (5,NULL,'STU-1774093269',NULL,NULL,NULL,'John','Doe','John Doe',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'male','1234567890',NULL,NULL,'john_1774093269@example.com',10,NULL,NULL,1,'2026-03-21 06:11:09','2026-11-01 03:24:46',NULL,5,3,1,NULL,NULL,'American','123 Street Ave','456 Block Rd',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(6,NULL,'ADM2026001',NULL,NULL,'101','John','Doe','John Doe',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'male','0987654321',NULL,NULL,'john.doe@example.com',10,NULL,NULL,1,'2026-03-30 10:46:25','2026-03-30 10:46:25',NULL,6,4,NULL,NULL,'2010-01-01','American','456 Test Ave','456 Test Ave',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(7,NULL,'STU0001',NULL,NULL,'1','Declan','Mraz','Declan Mraz',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'female','(283) 836-6510',NULL,NULL,'ludwig.kunde@example.com',2,NULL,NULL,1,'2026-04-10 14:48:24','2026-04-10 14:48:24',NULL,7,5,NULL,NULL,'2000-06-19',NULL,'999 Rowe Square Apt. 220\nFeestbury, KS 95615-5294','228 Justen Estate Apt. 706\nPagacburgh, ID 44848-2249',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(8,NULL,'STU0002',NULL,NULL,'2','Elvera','Friesen','Elvera Friesen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'male','(703) 536-8121',NULL,NULL,'brendan24@example.org',2,NULL,NULL,1,'2026-04-10 14:48:24','2026-04-10 14:48:24',NULL,7,5,NULL,NULL,'1976-11-02',NULL,'388 Koss Ports Suite 096\nWest Macy, NH 66058-6987','39151 Lind Cape\nHilmaburgh, CA 17117-4689',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(9,NULL,'STU0003',NULL,NULL,'3','Adolfo','Cassin','Adolfo Cassin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'female','+1-574-835-5108',NULL,NULL,'mcdermott.andreane@example.com',2,NULL,NULL,1,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL,7,5,NULL,NULL,'2001-08-22',NULL,'382 Herman Mountains Apt. 543\nWardhaven, DC 46326','3220 Mosciski Stravenue Suite 136\nSouth Ronny, ME 72528-3908',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(10,NULL,'STU0004',NULL,NULL,'4','Tod','Dickinson','Tod Dickinson',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'female','425-839-0708',NULL,NULL,'kovacek.emmanuelle@example.com',2,NULL,NULL,1,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL,7,5,NULL,NULL,'1996-11-03',NULL,'1138 Jefferey Way Apt. 637\nSchmelerberg, KS 57354-1661','639 Erik Heights\nFeestland, NJ 35247',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(11,NULL,'STU0005',NULL,NULL,'5','Hosea','Johnston','Hosea Johnston',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'female','+1 (651) 895-8612',NULL,NULL,'schumm.lilly@example.org',2,NULL,NULL,1,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL,7,5,NULL,NULL,'1971-03-10',NULL,'8772 Zoila Lane\nLake Gladyce, NC 93076-5023','8404 Casper Junction Suite 173\nEast Tayatown, NJ 52577-6587',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(12,NULL,'STU0006',NULL,NULL,'6','Waylon','Hartmann','Waylon Hartmann',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'male','(805) 656-7543',NULL,NULL,'ymuller@example.com',2,NULL,NULL,1,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL,7,5,NULL,NULL,'1985-06-23',NULL,'3498 Crona Mountain\nEast Spencer, CT 17065-3939','41502 Kuhlman Trail\nGarrisonport, ND 12613',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(13,NULL,'STU0007',NULL,NULL,'7','Jevon','Funk','Jevon Funk',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'male','+1 (480) 589-8582',NULL,NULL,'mariano.deckow@example.net',2,NULL,NULL,1,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL,7,5,NULL,NULL,'1972-12-27',NULL,'6019 Ozella Flat\nPort Ashtyn, KS 02747','4503 Becker Park Suite 974\nCassinfurt, NM 99215-3483',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(14,NULL,'STU0008',NULL,NULL,'8','Chad','Jenkins','Chad Jenkins',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'female','+1-854-662-8153',NULL,NULL,'nora.oconnell@example.net',2,NULL,NULL,1,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL,7,5,NULL,NULL,'2000-09-21',NULL,'6120 Alene Tunnel Apt. 009\nLake Miguel, OR 04740','1712 Ullrich Spur\nNorth Glorialand, TN 06415',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(15,NULL,'STU0009',NULL,NULL,'9','Emmitt','Thiel','Emmitt Thiel',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'male','+1-254-979-9760',NULL,NULL,'brooklyn.hills@example.com',2,NULL,NULL,1,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL,7,5,NULL,NULL,'1991-12-07',NULL,'876 Bernhard Meadow Suite 670\nLake Aniyah, CA 79193-3868','526 Hilpert Trace Suite 566\nNew Lue, TX 00374-8123',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(16,NULL,'STU0010',NULL,NULL,'10','Adaline','Johns','Adaline Johns',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'female','1-432-677-3296',NULL,NULL,'una.kertzmann@example.net',2,NULL,NULL,1,'2026-04-10 14:48:25','2026-04-10 14:48:25',NULL,7,5,NULL,NULL,'1972-09-25',NULL,'281 Langosh Summit Suite 144\nWest Aracelyland, MS 68460','77117 Gislason Overpass Apt. 801\nProhaskafurt, WI 48890',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(19,27,'1233',NULL,NULL,NULL,'test',NULL,'test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'male',NULL,NULL,NULL,'test07@gmail.com',10,NULL,NULL,1,'2026-04-12 04:08:00','2026-04-12 04:08:00',NULL,4,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(20,28,'12333',NULL,NULL,NULL,'test',NULL,'test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'male',NULL,NULL,NULL,'test007@gmail.com',10,NULL,NULL,1,'2026-04-12 04:10:02','2026-04-12 04:10:02',NULL,4,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(21,29,'12338',NULL,NULL,NULL,'test',NULL,'test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'male',NULL,NULL,NULL,'test005@gmail.com',10,NULL,NULL,1,'2026-04-12 04:15:58','2026-04-12 04:15:58',NULL,4,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(23,34,'45321',NULL,NULL,NULL,'test',NULL,'test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,10,NULL,NULL,1,'2026-04-17 01:31:24','2026-04-17 01:31:24',NULL,4,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(24,35,'4646466',NULL,NULL,'6464656','Junaid','Ahammed','Junaid Ahammed',NULL,'Muslim','Muslim mappila','A+',NULL,NULL,NULL,'Male',NULL,NULL,NULL,NULL,10,NULL,NULL,1,'2026-05-10 07:15:42','2026-05-10 07:15:42',NULL,3,6,NULL,NULL,NULL,NULL,'Avunhippuram(House)','gdfngndkdhnhnkjd',NULL,NULL,NULL,'64656646464646',NULL,'5456464646546',NULL,NULL,NULL,'amup school','gfsfgdshshsgh',NULL,'hdhdhhshdddddd','hehyyeyehehr',0,NULL,NULL,NULL,NULL,NULL,NULL),(25,36,'QW123',NULL,NULL,'12354','Ramees','AP','Ramees AP',NULL,'Muslim','MAPPILA','A+',NULL,NULL,NULL,'Male',NULL,NULL,NULL,NULL,10,NULL,NULL,1,'2026-05-10 10:33:21','2026-05-10 10:33:21',NULL,3,5,NULL,NULL,NULL,NULL,'TEST ADDRESS','TEST ADDRESS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(26,37,'STU1002',NULL,'2026-05-10','22','Meera','Nair','Meera Nair',NULL,'Hindu','General','O+',NULL,NULL,NULL,'female','9876543211',NULL,NULL,'meera@example.com',NULL,NULL,NULL,1,'2026-05-10 10:48:11','2026-05-10 10:48:11',NULL,7,5,NULL,NULL,NULL,'Indian','Thrissur, Kerala','Thrissur, Kerala',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(27,38,'STU1001',NULL,NULL,'15','Arjun','Kumar','Arjun Kumar',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'male','9876543210',NULL,NULL,'arjun@example.com',NULL,NULL,NULL,1,'2026-05-10 10:50:47','2026-05-10 10:50:47',NULL,7,5,NULL,NULL,NULL,'Indian','Kochi, Kerala','Kochi, Kerala',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(28,39,'STU10045',NULL,NULL,'15','Arjun','Kumar','Arjun Kumar',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'male','9876543210',NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-05-10 10:53:04','2026-05-10 10:53:04',NULL,7,5,NULL,NULL,NULL,'Indian','Kochi, Kerala','Kochi, Kerala',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(29,40,'STU100445',NULL,NULL,'15','Arjun',NULL,'Arjun',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'male','9876543210',NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-05-10 10:53:53','2026-05-10 10:53:53',NULL,7,5,NULL,NULL,NULL,'Indian','Kochi, Kerala','Kochi, Kerala',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(30,41,'246515',NULL,NULL,'1','anfas','kaloor','anfas kaloor',NULL,'Muslim','MAPPILA',NULL,NULL,NULL,NULL,'Male',NULL,NULL,NULL,NULL,8,NULL,NULL,1,'2026-06-04 14:53:58','2026-06-04 14:53:58',NULL,11,5,NULL,NULL,NULL,NULL,'kaloor house','sacasc',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(34,98,'ADM4587',NULL,NULL,NULL,'Test','Student','Test Student',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'student_6a5c5358be9b1@example.com',10,NULL,NULL,1,'2026-07-18 23:02:25','2026-07-18 23:02:25',NULL,3,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(35,101,'ADM1228',NULL,NULL,NULL,'Test','Student','Test Student',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'student_6a5c53d18b6b1@example.com',10,NULL,NULL,1,'2026-07-18 23:04:25','2026-07-18 23:04:25',NULL,3,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(36,104,'ADM3583',NULL,NULL,NULL,'Test','Student','Test Student',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'student_6a5c53ff7b84b@example.com',10,NULL,NULL,1,'2026-07-18 23:05:11','2026-07-18 23:05:11',NULL,3,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(39,116,'STU-1785902216',NULL,NULL,NULL,'John','Doe','John Doe',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'male','1234567890',NULL,NULL,'john_1785902216@example.com',10,NULL,NULL,1,'2026-08-04 22:27:08','2026-08-04 22:27:08',NULL,5,3,NULL,NULL,NULL,'American','123 Street Ave','456 Block Rd',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(40,117,'STU-DATE-1785911598',NULL,'2026-08-11',NULL,'Junaid','KT','Junaid KT','2010-12-15',NULL,NULL,NULL,NULL,NULL,NULL,'male',NULL,NULL,NULL,'junaid_1785911598@example.com',10,NULL,NULL,1,'2026-08-05 01:03:56','2026-08-05 01:03:56',NULL,5,3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(41,118,'STU-DATE-1785911604',NULL,'2026-08-11',NULL,'Junaid','KT','Junaid KT','2010-12-15',NULL,NULL,NULL,NULL,NULL,NULL,'male',NULL,NULL,NULL,'junaid_1785911604@example.com',10,NULL,NULL,1,'2026-08-05 01:03:56','2026-08-05 01:03:56',NULL,5,3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(69,206,'ADM1263',NULL,'2023-01-01','101','John','Doe','John Doe','2010-01-01','Christianity','General','O+','150cm','45kg',NULL,'male','1234567890','9988776655','5566778899','abc@example.com',1,1,NULL,1,'2026-10-23 06:14:23','2026-10-23 06:14:23',NULL,9,1,1,NULL,NULL,NULL,'123 Main St','456 Other St','123456789','Example Bank','EXAMP001','NIC123456','LOC123456','BC123456','APAAR123456','AADHAR123456','PEN123456','Old School','Grade 8','None',NULL,NULL,0,NULL,'2023-01-01',1,1,1,1),(74,211,'TEST_ADM_67590',NULL,NULL,NULL,'Student','Without Caste','Student Without Caste',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,10,NULL,NULL,1,'2026-11-01 03:24:44','2026-11-01 03:24:44',NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(75,212,'TEST_ADM_36441',NULL,NULL,NULL,'Student','Without Caste','Student Without Caste',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,10,NULL,NULL,1,'2026-11-01 03:25:24','2026-11-01 03:25:24',NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(77,221,'TEST_ADM_16514',NULL,NULL,NULL,'Student','Without Caste','Student Without Caste',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,10,NULL,NULL,1,'2026-11-01 03:27:50','2026-11-01 03:27:50',NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(82,225,'TEST_ADM_42235',NULL,NULL,NULL,'Student','Without Caste','Student Without Caste',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,10,NULL,NULL,1,'2026-11-01 03:28:12','2026-11-01 03:28:12',NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `study_materials`
--

DROP TABLE IF EXISTS `study_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `study_materials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `class_id` bigint(20) unsigned DEFAULT NULL,
  `section_id` bigint(20) unsigned DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `uploaded_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `study_materials_class_id_foreign` (`class_id`),
  KEY `study_materials_section_id_foreign` (`section_id`),
  KEY `study_materials_subject_id_foreign` (`subject_id`),
  KEY `study_materials_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `study_materials_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `study_materials_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`),
  CONSTRAINT `study_materials_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  CONSTRAINT `study_materials_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `study_materials`
--

LOCK TABLES `study_materials` WRITE;
/*!40000 ALTER TABLE `study_materials` DISABLE KEYS */;
/*!40000 ALTER TABLE `study_materials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subjects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `type` enum('Theory','Practical') NOT NULL DEFAULT 'Theory',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,'Mathematics','MATH10','Theory','2026-03-30 10:46:25','2026-03-30 10:46:25',NULL),(2,'Mathematics','MATH','Theory','2026-04-10 14:42:45','2026-04-10 14:42:45',NULL),(3,'Science','SCI','Theory','2026-04-10 14:42:45','2026-04-10 14:42:45',NULL),(4,'English','ENG','Theory','2026-04-10 14:42:45','2026-04-10 14:42:45',NULL),(5,'History','HIST','Theory','2026-04-10 14:42:45','2026-04-10 14:42:45',NULL),(6,'Physics Lab','PHY_LAB','Practical','2026-04-10 14:42:45','2026-04-10 14:42:45',NULL);
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timetables`
--

DROP TABLE IF EXISTS `timetables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timetables` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint(20) unsigned DEFAULT NULL,
  `section_id` bigint(20) unsigned DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `teacher_id` bigint(20) unsigned DEFAULT NULL,
  `day` varchar(255) DEFAULT NULL,
  `start_time` varchar(255) DEFAULT NULL,
  `end_time` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timetables`
--

LOCK TABLES `timetables` WRITE;
/*!40000 ALTER TABLE `timetables` DISABLE KEYS */;
/*!40000 ALTER TABLE `timetables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `todos`
--

DROP TABLE IF EXISTS `todos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `todos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `todos_user_id_foreign` (`user_id`),
  CONSTRAINT `todos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `todos`
--

LOCK TABLES `todos` WRITE;
/*!40000 ALTER TABLE `todos` DISABLE KEYS */;
/*!40000 ALTER TABLE `todos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_profiles`
--

DROP TABLE IF EXISTS `user_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_profiles_user_id_foreign` (`user_id`),
  CONSTRAINT `user_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_profiles`
--

LOCK TABLES `user_profiles` WRITE;
/*!40000 ALTER TABLE `user_profiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `google_access_token` text DEFAULT NULL,
  `google_refresh_token` text DEFAULT NULL,
  `google_token_expires_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_users_branch` (`branch_id`),
  CONSTRAINT `fk_users_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=229 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'Admin','Admin','admin@erp.com','$2y$12$.c94mO70Tqxg3J/3qnw1bexTGRgIH5j5t3O/lvJhIdQ8XPXskcxX.',NULL,NULL,NULL,1,NULL,'2026-02-14 06:37:50','2026-02-14 06:37:50',NULL,NULL,NULL,NULL),(2,NULL,'vishnu','vishnu','vishnu@gmail.com','$2y$12$DqDgUfHOKhFiZ.vwg3AhKuNrI66akF/YImPoHRtOwo168x8ll8Gba',NULL,NULL,NULL,1,NULL,'2026-03-11 06:12:57','2026-03-11 06:12:57',NULL,NULL,NULL,NULL),(6,NULL,'vishnu','vishnu','vishnu1@gmail.com','$2y$12$rwnUufKTP5HMtwJJyHsq6Opq5xvvwiCqhF7sqxkMe10qXNjiRWivq',NULL,NULL,NULL,1,NULL,'2026-03-11 06:18:44','2026-03-11 06:18:44',NULL,NULL,NULL,NULL),(9,NULL,'vishnu','vishnu','vishnuyy@gmail.com','$2y$12$Z8.t4CVy8sQ7vEUnPwGrKe8j43U/aht/xQ6Weu.89PzJk7mY2i7RW',NULL,NULL,NULL,1,NULL,'2026-03-11 06:52:28','2026-03-11 06:52:28',NULL,NULL,NULL,NULL),(10,NULL,'John','John','john_1774093145@example.com','$2y$12$7T2gmVWZVdpRJRKA2zxWle3FA9N/Yy1PpAt2zKu/YJ4FEPSLBcNKu',NULL,NULL,NULL,1,NULL,'2026-03-21 06:09:06','2026-03-21 06:09:06',NULL,NULL,NULL,NULL),(11,NULL,'John','John','john_1774093209@example.com','$2y$12$b3BjUkTu5D7jQOF7oNeziOfLALevx.5JBbXnd4l9q12l2f5.GgTBC',NULL,NULL,NULL,1,NULL,'2026-03-21 06:10:10','2026-03-21 06:10:10',NULL,NULL,NULL,NULL),(12,NULL,'John','John','john_1774093229@example.com','$2y$12$oRdSYFECBtPagr1I93VQSePvnoODFEk/2ReCr8g7RAzww1og4/Ur.',NULL,NULL,NULL,1,NULL,'2026-03-21 06:10:29','2026-03-21 06:10:29',NULL,NULL,NULL,NULL),(13,NULL,'John','John','john_1774093269@example.com','$2y$12$LU2flJO244jR0t5aAbLukeaBFfHOXC8Q7K4LPCdZeD9xlizfrl9a6',NULL,NULL,NULL,1,NULL,'2026-03-21 06:11:09','2026-03-21 06:11:09',NULL,NULL,NULL,NULL),(14,NULL,'Admin User','Admin User','admin@admin.com','$2y$12$pr7zsMBvXEwe40420iexBunWSQxIOkNADhE1.ojnNMHE40oGluIo.',NULL,NULL,NULL,1,NULL,'2026-03-30 10:45:53','2026-03-30 10:45:53',NULL,NULL,NULL,NULL),(15,3,'Jaylan Buckridge IV','Jaylan Buckridge IV','teacher1@school.com','$2y$12$Iv.2maDI3EPYgyYUbpoEtuqMey9fEptc7ZvdxK3hOX2.sLdaC/GBG',NULL,NULL,NULL,1,NULL,'2026-04-10 14:42:47','2026-04-10 14:42:47',NULL,NULL,NULL,NULL),(16,3,'Tess Mayer','Tess Mayer','teacher2@school.com','$2y$12$2vhaUmSs4f/Q5LR92zeqk.76VsoR.0UWfSFWKJcyfGABPWEH.3yQe',NULL,NULL,NULL,1,NULL,'2026-04-10 14:42:47','2026-04-10 14:42:47',NULL,NULL,NULL,NULL),(17,3,'Mr. Grover DuBuque','Mr. Grover DuBuque','teacher3@school.com','$2y$12$.lf3N9jV9.0M.KwsaoUIwuhQ3LuqoM.I/6bIdNxfPX5kQw4aBg6.i',NULL,NULL,NULL,1,NULL,'2026-04-10 14:42:48','2026-04-10 14:42:48',NULL,NULL,NULL,NULL),(18,3,'Gerard Hill','Gerard Hill','teacher4@school.com','$2y$12$NabUpEQsl6EDfC1w1rPlPe4lzluRncdSRNF3MHQXxEE17ZB8O.ptu',NULL,NULL,NULL,1,NULL,'2026-04-10 14:42:48','2026-04-10 14:42:48',NULL,NULL,NULL,NULL),(19,3,'Dr. Heath Borer Jr.','Dr. Heath Borer Jr.','teacher5@school.com','$2y$12$WWfVpblEUSUpkN2kGm1J8eJ08kAh/wgj/cXNduOophc5Qz/XVwP5y',NULL,NULL,NULL,1,NULL,'2026-04-10 14:42:49','2026-04-10 14:42:49',NULL,NULL,NULL,NULL),(20,NULL,'test','test','test@gmail.com','$2y$12$SIrA4.AJw5WR7mAKF6hyj.KtLGcdlKFAX4PkJomjukXDnfYch2w6m',NULL,NULL,NULL,1,NULL,'2026-04-12 03:46:00','2026-04-12 03:46:00',NULL,NULL,NULL,NULL),(21,NULL,'test','test','69db63501915d@gmail.com','$2y$12$L5gsUITgRUhw9L2GkmC/2e3onIkjNlpaMDHeEGMguMlqA7unXo4NK',NULL,NULL,NULL,1,NULL,'2026-04-12 03:48:08','2026-04-12 03:48:08',NULL,NULL,NULL,NULL),(22,NULL,'test','test','69db63d839792@gmail.com','$2y$12$f17dJvvNozfXDBNDKxWk6OP30okvlLEQjKlLoB1D90LiylKqyOpyi',NULL,NULL,NULL,1,NULL,'2026-04-12 03:50:24','2026-04-12 03:50:24',NULL,NULL,NULL,NULL),(23,NULL,'test','test','69db643339987@gmail.com','$2y$12$.VAVJvsYzENrFYD62.nis.i0PapIOeAgM0sAHxM/XEv6bP8r5E1Zy',NULL,NULL,NULL,1,NULL,'2026-04-12 03:51:55','2026-04-12 03:51:55',NULL,NULL,NULL,NULL),(24,NULL,'test','test','test2@gmail.com','$2y$12$nBfeyPUyhtYPBk2YQ1csc.3a32x2Y3AGC5GLxXOn9P4gIwWm7NINu',NULL,NULL,NULL,1,NULL,'2026-04-12 04:02:26','2026-04-12 04:02:26',NULL,NULL,NULL,NULL),(25,NULL,'test','test','test6@gmail.com','$2y$12$rh1jeOYhN.PwUsXlA9vSoukP6UF3TXrFhHPj6jXxexwbRRSEvxG1G',NULL,NULL,NULL,1,NULL,'2026-04-12 04:03:09','2026-04-12 04:03:09',NULL,NULL,NULL,NULL),(26,NULL,'test','test','test67@gmail.com','$2y$12$YCF3skLkTO8F/D1g8n2dmOmq2B9OoVwq1DAs1xn2yBJcEIVkFS9cy',NULL,NULL,NULL,1,NULL,'2026-04-12 04:04:47','2026-04-12 04:04:47',NULL,NULL,NULL,NULL),(27,NULL,'test','test','test07@gmail.com','$2y$12$SrLeG86cNAJYN09s5w1Ua.En44PbxQwrKdsvgTcCcoZmhfYXVwTk.',NULL,NULL,NULL,1,NULL,'2026-04-12 04:08:00','2026-04-12 04:08:00',NULL,NULL,NULL,NULL),(28,NULL,'test','test','test007@gmail.com','$2y$12$EPdLIySTc50EtJWwFz/M7ucaoulW9BNRsftqNlAknqGZr1dDcgDwW',NULL,NULL,NULL,1,NULL,'2026-04-12 04:10:01','2026-04-12 04:10:01',NULL,NULL,NULL,NULL),(29,NULL,'test','test','test005@gmail.com','$2y$12$d93XW9xyF2exPVU8wO2JKOVlIdjlyNPKhIez98VS4tLOu88x8Fv4S',NULL,NULL,NULL,1,NULL,'2026-04-12 04:15:58','2026-04-12 04:15:58',NULL,NULL,NULL,NULL),(31,NULL,'test','test',NULL,'$2y$12$ba1xuwVw4vrGleXRdSHeG.FNQ6lEKuF4aXHCE2GKlSoXn/aXQNOlG',NULL,NULL,NULL,1,NULL,'2026-04-17 00:03:56','2026-04-17 00:03:56',NULL,NULL,NULL,NULL),(32,NULL,'test','test',NULL,'$2y$12$NTso.JU7ugFCPtcnWBe7D.Bs2Il26qmPwV19qBLXlUgnUwT2OXPp2',NULL,NULL,NULL,1,NULL,'2026-04-17 01:17:18','2026-04-17 01:17:18',NULL,NULL,NULL,NULL),(33,NULL,'test','test',NULL,'$2y$12$2dh34Vvfv153UaytdBJMXeGYjXNgW5BHAd9ezO6Oz3AsrWMdX64H2',NULL,NULL,NULL,1,NULL,'2026-04-17 01:17:30','2026-04-17 01:17:30',NULL,NULL,NULL,NULL),(34,NULL,'test','test',NULL,'$2y$12$M75h7/amFuUvXtJnk/VV3eFmzSKnJvykEUBtB6Wh3Yie9R89yC07G',NULL,NULL,NULL,1,NULL,'2026-04-17 01:31:23','2026-04-17 01:31:23',NULL,NULL,NULL,NULL),(35,NULL,'Junaid','Junaid',NULL,'$2y$12$ucZi6Idq50tOdLGEgtw5pOrQFq0uaV.ekNi4umiCxtrzyuIC4mR2y',NULL,NULL,NULL,1,NULL,'2026-05-10 07:15:42','2026-05-10 07:15:42',NULL,NULL,NULL,NULL),(36,NULL,'Ramees','Ramees',NULL,'$2y$12$sb87okrEm/FGxRhcHE6FSOBJd3Suj.r5Hh33EtVcczn.zTIkAIv5a',NULL,NULL,NULL,1,NULL,'2026-05-10 10:33:21','2026-05-10 10:33:21',NULL,NULL,NULL,NULL),(37,NULL,'Meera','Meera','meera@example.com','$2y$12$Jp7fhRo1ohoCHA3fPyz/7.UE/leeh3LzsI9lKGEX0wLIWsEPeRAZW',NULL,NULL,NULL,1,NULL,'2026-05-10 10:48:11','2026-05-10 10:48:11',NULL,NULL,NULL,NULL),(38,NULL,'Arjun','Arjun','arjun@example.com','$2y$12$AZ2yanTmOALxG6uoqXhvxOk50ZxqCDUYu3mqAdkZI0e6R9qsPczoa',NULL,NULL,NULL,1,NULL,'2026-05-10 10:50:47','2026-05-10 10:50:47',NULL,NULL,NULL,NULL),(39,NULL,'Arjun','Arjun',NULL,'$2y$12$1w448DDi42caXjK65fncN.deFz1ZTVi35WWSv/wCnFzvzRqqeNBuC',NULL,NULL,NULL,1,NULL,'2026-05-10 10:53:04','2026-05-10 10:53:04',NULL,NULL,NULL,NULL),(40,NULL,'Arjun','Arjun',NULL,'$2y$12$5CD2xr5Y0MDmny1QdK10kukx/kqrWmISxdgoRTj68DMT0R9nu6Z.G',NULL,NULL,NULL,1,NULL,'2026-05-10 10:53:53','2026-05-10 10:53:53',NULL,NULL,NULL,NULL),(41,NULL,'anfas','anfas',NULL,'$2y$12$bdMVxOU.tE2CzGF1/02jbuA78Fn7gtLe1/txyLKs0IsOa0/BYdtWe',NULL,NULL,NULL,1,NULL,'2026-06-04 14:53:58','2026-06-04 14:53:58',NULL,NULL,NULL,NULL),(42,1,'Settings Admin','Settings Admin','settings_admin_6a5b82753dbad@example.com','$2y$04$KQq7ZYl6z83wSu1ZZHxfY.DksHk6berpTSNQ.VbMC9cogCTocxhgu',NULL,NULL,NULL,1,NULL,'2026-07-18 08:11:09','2026-07-18 08:11:09',NULL,NULL,NULL,NULL),(47,1,'Settings Admin','Settings Admin','settings_admin_6a5b82a5b8378@example.com','$2y$04$rrNboS5xLDZX8gLxDOucg.7zGFOpRyW2IORJaMe2mo0FfGxZ5fH56',NULL,NULL,NULL,1,NULL,'2026-07-18 08:11:57','2026-07-18 08:11:57',NULL,NULL,NULL,NULL),(52,1,'Settings Admin','Settings Admin','settings_admin_6a5c3e08101b6@example.com','$2y$04$FIdXTFW2hAKWdvOkPwslpe4wWVtIYGgAgUA1xUi48TJlsJio3gToO',NULL,NULL,NULL,1,NULL,'2026-07-18 21:31:28','2026-07-18 21:31:28',NULL,NULL,NULL,NULL),(57,1,'Settings Admin','Settings Admin','settings_admin_6a5c454454502@example.com','$2y$04$aNUE9.B0K4CzqvLi1QCAEei/D1NhgqXKmEg7ZdWs9VgyrYrbG9/ju',NULL,NULL,NULL,1,NULL,'2026-07-18 22:02:20','2026-07-18 22:02:20',NULL,NULL,NULL,NULL),(98,2,'Test Student','Test Student','student_6a5c5358be9b1@example.com','$2y$12$j4DZQiDuBOBsgmVO0qTrpuOVNGu28McKmgYijKQl42YPPMey7rvgm',NULL,NULL,NULL,1,NULL,'2026-07-18 23:02:25','2026-07-18 23:02:25',NULL,NULL,NULL,NULL),(101,2,'Test Student','Test Student','student_6a5c53d18b6b1@example.com','$2y$12$9dmLhOeGVT4KGethWZXWD.nDYOzzowYz2Fv3zl0bzs4Tgn18jImt.',NULL,NULL,NULL,1,NULL,'2026-07-18 23:04:25','2026-07-18 23:04:25',NULL,NULL,NULL,NULL),(104,2,'Test Student','Test Student','student_6a5c53ff7b84b@example.com','$2y$12$F5Mlvj.XLfFXHYZgwuaSVOVNI2.BLvK48oo2xdA23nhtb9es9.DTS',NULL,NULL,NULL,1,NULL,'2026-07-18 23:05:11','2026-07-18 23:05:11',NULL,NULL,NULL,NULL),(105,NULL,'Father Prakash','Father Prakash','rkvishnuprakash@gmail.com','$2y$12$Wtk4Jrbkxm5xEAn.hvAXr.XbJyBg7kwiYCjVDEnhqJ1eRbnBdVG1G',NULL,NULL,NULL,1,NULL,'2026-07-18 23:05:12','2026-07-18 23:05:12',NULL,NULL,NULL,NULL),(116,NULL,'John Doe','John Doe','john_1785902216@example.com','$2y$12$pcKKz2WetgtxOJ4l6DSfD.7Dgn/TlEprA7aZsI6dc/.vURi1ToyQa',NULL,NULL,NULL,1,NULL,'2026-08-04 22:26:57','2026-08-04 22:26:57',NULL,NULL,NULL,NULL),(117,NULL,'Junaid KT','Junaid KT','junaid_1785911598@example.com','$2y$12$xJJj8aZmOYbMd2u6ANNeueoLvQYVXCchrg31haHicYxPAJU4bJBZ.',NULL,NULL,NULL,1,NULL,'2026-08-05 01:03:22','2026-08-05 01:03:22',NULL,NULL,NULL,NULL),(118,NULL,'Junaid KT','Junaid KT','junaid_1785911604@example.com','$2y$12$FRDDeG0OsYA/2VNooECNIuVdMcKvgB2q3mFYbC55Su21F4h.13FL.',NULL,NULL,NULL,1,NULL,'2026-08-05 01:03:25','2026-08-05 01:03:25',NULL,NULL,NULL,NULL),(145,1,'Settings Admin',NULL,'settings_admin_6a8dcb556041a@example.com','$2y$04$lSXz2K1H3twEj3DsJbp7t.v7OXwwy8LXQ2XrR.JUMblbGMsn4KnP2',NULL,NULL,NULL,1,NULL,'2026-08-25 11:35:25','2026-08-25 11:35:25',NULL,NULL,NULL,NULL),(166,NULL,'UpdatedStaff Staff','UpdatedStaff Staff','teststaff_1787677780@example.com','$2y$12$FATrdW22kYpyudzlO0wp4eTx4fUvzrygBdM4L3AP2PuzIqWuJJyrm',NULL,NULL,NULL,1,NULL,'2026-08-25 11:39:40','2026-08-25 11:39:42',NULL,NULL,NULL,NULL),(167,1,'Settings Admin',NULL,'settings_admin_6a8dcc5db492b@example.com','$2y$04$kuqxW8hHnbfPzFYmOJi1XO6qCEdNZCGO3ueUFFRczQW.HIS7MQ3Oi',NULL,NULL,NULL,1,NULL,'2026-08-25 11:39:49','2026-08-25 11:39:49',NULL,NULL,NULL,NULL),(188,NULL,'Bulk1 Staff','Bulk1 Staff','bulkstaff_1787743392_1@example.com','$2y$12$bD5praXcdNboKiIKlkY.YOpr67.bghRNQY1d4rg5fxE3KQIVWOpUm',NULL,NULL,NULL,1,NULL,'2026-08-26 05:53:17','2026-08-26 05:53:17',NULL,NULL,NULL,NULL),(189,1,'Settings Admin',NULL,'settings_admin_6a92553201c3e@example.com','$2y$04$z.v1Z0OaO0Ybu7OeEv.BO.YnIhX6VtTHLeXQbBv7PnvH729y.K5xi',NULL,NULL,NULL,1,NULL,'2026-08-28 22:12:42','2026-08-28 22:12:42',NULL,NULL,NULL,NULL),(194,1,'Settings Admin',NULL,'settings_admin_6a9256530ce29@example.com','$2y$04$yljvyN1NYTDn6RCe2ZzWfehkoUBA.RHFSEorisS3QnB97iMnIy1ue',NULL,NULL,NULL,1,NULL,'2026-08-28 22:17:31','2026-08-28 22:17:31',NULL,NULL,NULL,NULL),(199,NULL,'Vishnu Prakash Test','Vishnu Prakash Test','vishnuprakashrk636@gmail.com','$2y$12$lcIN6lQUzY/hDvDlvdlxieAcAFZulLG6GMydty2YGQFCT5SKcslUi',NULL,NULL,NULL,1,NULL,'2026-10-11 00:29:51','2026-10-11 00:29:51',NULL,NULL,NULL,NULL),(201,NULL,'SaaS Super Admin','SaaS Super Admin','superadmin@erp.com','$2y$12$G8bBQSJGVdg0nGC5VLpfwOJrs5dStYS9oM2pBCBggV3..Mk6UMU4S',NULL,NULL,NULL,1,NULL,'2026-10-19 02:39:32','2026-10-19 03:15:35',NULL,NULL,NULL,NULL),(206,NULL,'John Doe','John Doe','abc@example.com','$2y$12$5XEQQ96v35kvO0NlZcXcouHK5B8wL0ougtRf35QOGhQ9qYURn/Vui',NULL,NULL,NULL,1,NULL,'2026-10-23 06:14:17','2026-10-23 06:14:17',NULL,NULL,NULL,NULL),(207,NULL,'Richard Doe',NULL,'father@example.com','$2y$12$mTnHGB4CmEyO4H2XNenumejsYP0bWGj4CU4sK73Y239QOmT26baay',NULL,NULL,NULL,1,NULL,'2026-10-23 06:14:24','2026-10-23 06:14:24',NULL,NULL,NULL,NULL),(211,NULL,'Student Without Caste','Student Without Caste',NULL,'$2y$04$/vvuUEE15vqUi74T5FL4V.xzG7.Y.mDaAs0L1rEHnfKMBcKOjBj1u',NULL,NULL,NULL,1,NULL,'2026-11-01 03:24:43','2026-11-01 03:24:43',NULL,NULL,NULL,NULL),(212,NULL,'Student Without Caste','Student Without Caste',NULL,'$2y$04$cxvZTcB.orxoPDaN6wV/o.l9msCm7aV2w9s4AL60RprPg4XawRhVG',NULL,NULL,NULL,1,NULL,'2026-11-01 03:25:24','2026-11-01 03:25:24',NULL,NULL,NULL,NULL),(213,1,'Settings Admin',NULL,'settings_admin_6ae6ff036b84c@example.com','$2y$04$/6CJKohji3IMLhtxUIL.CeBl3BH2gPThbyJ6tHOFxidJivImddZMS',NULL,NULL,NULL,1,NULL,'2026-11-01 03:27:39','2026-11-01 03:27:39',NULL,NULL,NULL,NULL),(221,NULL,'Student Without Caste','Student Without Caste',NULL,'$2y$04$bPF0byfxDeNORR2ZjgQzReqhGQIe.nHYus1pN2hMkwj8yvkgDPVuq',NULL,NULL,NULL,1,NULL,'2026-11-01 03:27:50','2026-11-01 03:27:50',NULL,NULL,NULL,NULL),(225,NULL,'Student Without Caste','Student Without Caste',NULL,'$2y$04$70ddW0nBwLhOrPHu6GgiwONYLAxNR0qZFZxt4523Q4cx73ITLAura',NULL,NULL,NULL,1,NULL,'2026-11-01 03:28:12','2026-11-01 03:28:12',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_no` varchar(255) NOT NULL,
  `vehicle_model` varchar(255) DEFAULT NULL,
  `driver_name` varchar(255) DEFAULT NULL,
  `driver_license` varchar(255) DEFAULT NULL,
  `driver_contact` varchar(255) DEFAULT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicles_branch_id_foreign` (`branch_id`),
  CONSTRAINT `vehicles_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `videos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `youtube_link` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `uploaded_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `videos_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `videos_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `videos`
--

LOCK TABLES `videos` WRITE;
/*!40000 ALTER TABLE `videos` DISABLE KEYS */;
/*!40000 ALTER TABLE `videos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-01 23:38:53

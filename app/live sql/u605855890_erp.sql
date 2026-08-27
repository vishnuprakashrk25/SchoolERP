-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 16, 2026 at 04:04 PM
-- Server version: 11.8.8-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u605855890_erp`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`id`, `name`, `start_date`, `end_date`, `is_current`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '2025-2026', '2025-06-01', '2026-03-31', 0, '2026-03-11 07:06:33', '2026-03-11 07:06:33', NULL),
(2, '2025-2026', '2025-06-01', '2026-03-31', 0, '2026-03-11 07:06:33', '2026-03-11 07:06:33', NULL),
(3, '2025-2026', '2025-06-01', '2026-03-31', 1, '2026-03-11 08:01:28', '2026-03-11 08:01:28', NULL),
(4, '2025-2026', '2025-06-01', '2026-03-31', 1, '2026-03-15 02:05:46', '2026-03-15 02:05:46', NULL),
(5, '2025-2026', '2025-06-01', '2026-03-31', 1, '2026-03-15 02:07:39', '2026-03-15 02:07:39', NULL),
(6, '2025-2026', '2025-06-01', '2026-03-31', 1, '2026-03-15 02:08:06', '2026-03-15 02:08:06', NULL),
(7, '2026-2027', '2026-04-01', '2027-03-31', 0, '2026-03-21 06:11:09', '2026-03-21 06:11:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admission_queries`
--

CREATE TABLE `admission_queries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `assignment_date` date NOT NULL,
  `submission_date` date NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submissions`
--

CREATE TABLE `assignment_submissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assignment_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `code`, `created_at`, `updated_at`, `phone`, `email`, `address`, `status`, `deleted_at`) VALUES
(1, 'Main Branch', 'MB001', '2026-02-14 06:37:15', '2026-02-14 06:37:15', NULL, NULL, NULL, 1, NULL),
(2, 'Main Branch', 'BR_MAIN', '2026-03-30 10:46:25', '2026-03-30 10:46:25', '1234567890', 'info@mainbranch.com', '123 Main St', 1, NULL),
(3, 'Main Campus', 'MAIN', '2026-04-10 14:42:42', '2026-04-10 14:42:42', '1234567890', 'admin@school.com', '123 School Lane', 1, NULL),
(4, 'City Branch', 'CITY', '2026-04-10 14:42:42', '2026-04-10 14:42:42', '0987654321', 'city@school.com', '456 City Road', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bulk_print_logs`
--

CREATE TABLE `bulk_print_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `generated_by` bigint(20) UNSIGNED NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificate_templates`
--

CREATE TABLE `certificate_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `template` longtext NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `academic_year_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`, `branch_id`, `academic_year_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 'Class 10 Updated', 1, 1, '2026-03-13 02:26:44', '2026-03-13 02:33:41', NULL),
(4, 'Grade 10', 1, 1, '2026-03-21 03:33:35', '2026-03-21 03:33:35', NULL),
(5, 'Class X', 1, 7, '2026-03-21 06:11:09', '2026-03-21 06:11:09', NULL),
(6, 'Class 10', 2, 7, '2026-03-30 10:46:25', '2026-03-30 10:46:25', NULL),
(7, 'Class 1', 3, 7, '2026-04-10 14:42:44', '2026-04-10 14:42:44', NULL),
(8, 'Class 2', 3, 7, '2026-04-10 14:42:45', '2026-04-10 14:42:45', NULL),
(9, 'Class 3', 3, 7, '2026-04-10 14:42:45', '2026-04-10 14:42:45', NULL),
(10, 'Class 4', 3, 7, '2026-04-10 14:42:45', '2026-04-10 14:42:45', NULL),
(11, 'Class 5', 3, 7, '2026-04-10 14:42:45', '2026-04-10 14:42:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `class_rooms`
--

CREATE TABLE `class_rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `room_no` varchar(255) NOT NULL,
  `capacity` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_routines`
--

CREATE TABLE `class_routines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `day` varchar(255) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_subjects`
--

CREATE TABLE `class_subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_subjects`
--

INSERT INTO `class_subjects` (`id`, `class_id`, `subject_id`, `teacher_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 6, 1, 1, '2026-03-30 11:22:17', '2026-03-30 11:22:17', NULL),
(4, 3, 2, 1, '2026-04-10 14:48:24', '2026-04-10 14:48:24', NULL),
(5, 4, 2, 1, '2026-04-10 14:48:24', '2026-04-10 14:48:24', NULL),
(6, 5, 2, 1, '2026-04-10 14:48:24', '2026-04-10 14:48:24', NULL),
(7, 6, 2, 1, '2026-04-10 14:48:24', '2026-04-10 14:48:24', NULL),
(8, 7, 2, 1, '2026-04-10 14:48:24', '2026-04-10 14:48:24', NULL),
(9, 8, 2, 1, '2026-04-10 14:48:24', '2026-04-10 14:48:24', NULL),
(10, 9, 2, 1, '2026-04-10 14:48:24', '2026-04-10 14:48:24', NULL),
(11, 10, 2, 1, '2026-04-10 14:48:24', '2026-04-10 14:48:24', NULL),
(12, 11, 2, 1, '2026-04-10 14:48:24', '2026-04-10 14:48:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `class_teachers`
--

CREATE TABLE `class_teachers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_teachers`
--

INSERT INTO `class_teachers` (`id`, `class_id`, `section_id`, `teacher_id`, `created_at`, `updated_at`) VALUES
(1, 7, 5, 1, '2026-04-10 14:48:24', '2026-04-10 14:48:24');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `complaint_by` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `description` text NOT NULL,
  `action_taken` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dormitories`
--

CREATE TABLE `dormitories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dormitory_rooms`
--

CREATE TABLE `dormitory_rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dormitory_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `number_of_bed` varchar(255) DEFAULT NULL,
  `cost_per_bed` varchar(255) DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `download_contents`
--

CREATE TABLE `download_contents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `uploaded_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `download_content_types`
--

CREATE TABLE `download_content_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `academic_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `name`, `academic_year_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'First Term Exam', 7, '2026-04-11 03:16:18', '2026-04-11 03:16:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `exam_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `marks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_schedules`
--

CREATE TABLE `exam_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` bigint(20) UNSIGNED DEFAULT NULL,
  `class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_schedules`
--

INSERT INTO `exam_schedules` (`id`, `exam_id`, `class_id`, `subject_id`, `exam_date`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 7, 2, '2026-09-15', '2026-04-11 03:20:08', '2026-04-11 03:20:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_groups`
--

CREATE TABLE `fee_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `school_id` bigint(20) UNSIGNED DEFAULT NULL,
  `academic_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_groups`
--

INSERT INTO `fee_groups` (`id`, `name`, `description`, `school_id`, `academic_id`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Monthly Tuition', NULL, NULL, NULL, NULL, NULL, '2026-04-10 14:48:25', '2026-04-10 14:48:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fee_structures`
--

CREATE TABLE `fee_structures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fee_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_structures`
--

INSERT INTO `fee_structures` (`id`, `class_id`, `fee_type_id`, `amount`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 7, 1, 5000.00, '2026-04-10 14:49:07', '2026-04-10 14:49:07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fee_types`
--

CREATE TABLE `fee_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_types`
--

INSERT INTO `fee_types` (`id`, `name`, `amount`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Tuition Fee', 0.00, '2026-04-10 14:49:07', '2026-04-10 14:49:07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `module` varchar(255) DEFAULT NULL,
  `uploaded_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `generated_certificates`
--

CREATE TABLE `generated_certificates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `certificate_template_id` bigint(20) UNSIGNED NOT NULL,
  `issue_date` date NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `generated_id_cards`
--

CREATE TABLE `generated_id_cards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `template_id` bigint(20) UNSIGNED NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `id_card_templates`
--

CREATE TABLE `id_card_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `template` longtext NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message_threads`
--

CREATE TABLE `message_threads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2026_02_13_174532_create_admission_queries_table', 2),
(6, '2026_02_13_174939_create_permission_tables', 2),
(7, '2026_03_08_082644_create_academic_years_table', 3),
(8, '2026_03_08_082646_create_user_profiles_table', 3),
(9, '2026_03_08_082648_create_admission_queries_table', 4),
(10, '2026_03_08_082649_create_postal_receives_table', 4),
(11, '2026_03_08_082650_create_phone_call_logs_table', 4),
(12, '2026_03_08_082650_create_postal_dispatches_table', 4),
(13, '2026_03_08_082651_create_certificate_templates_table', 4),
(14, '2026_03_08_082651_create_complaints_table', 4),
(15, '2026_03_08_082658_create_student_categories_table', 5),
(16, '2026_03_08_082659_create_students_table', 6),
(17, '2026_03_08_082652_create_generated_certificates_table', 7),
(18, '2026_03_08_082652_create_id_card_templates_table', 7),
(19, '2026_03_08_082653_create_classes_table', 7),
(20, '2026_03_08_082653_create_generated_id_cards_table', 7),
(21, '2026_03_08_082654_create_sections_table', 7),
(22, '2026_03_08_082655_create_subjects_table', 8),
(23, '2026_03_08_082655_create_optional_subjects_table', 9),
(24, '2026_03_08_082656_create_teachers_table', 10),
(25, '2026_03_08_082656_create_class_subjects_table', 11),
(26, '2026_03_08_082657_create_class_rooms_table', 11),
(27, '2026_03_08_082657_create_class_teachers_table', 11),
(28, '2026_03_08_082658_create_class_routines_table', 11),
(29, '2026_03_08_082700_create_student_academics_table', 11),
(30, '2026_03_08_082700_create_student_parents_table', 11),
(31, '2026_03_08_082701_create_student_documents_table', 11),
(32, '2026_03_08_082701_create_student_multi_class_table', 11),
(33, '2026_03_08_082702_create_assignments_table', 12),
(34, '2026_03_08_082702_create_assignment_submissions_table', 13),
(35, '2026_03_08_082703_create_download_content_types_table', 13),
(36, '2026_03_08_082703_create_study_materials_table', 13),
(37, '2026_03_08_082704_create_download_contents_table', 13),
(38, '2026_03_08_082705_create_shared_contents_table', 13),
(39, '2026_03_08_082705_create_videos_table', 13),
(40, '2026_03_08_082706_create_messages_table', 13),
(41, '2026_03_08_082706_create_notifications_table', 13),
(42, '2026_03_08_082707_create_bulk_print_logs_table', 13),
(43, '2026_03_08_082707_create_message_threads_table', 13),
(44, '2026_03_08_082708_create_files_table', 13),
(45, '2026_03_08_090336_update_branches_table', 13),
(46, '2026_03_11_072240_add_softdelete_to_all_tables', 14),
(47, '2026_03_11_113845_update_students_table_for_relations', 15),
(48, '2026_03_11_105142_add_class_id_to_students_table', 16),
(49, '2026_03_21_085835_add_type_to_subjects_table', 16),
(50, '2026_03_21_112801_add_fields_to_students_table', 17),
(51, '2026_03_31_041210_create_exams_table', 18),
(52, '2026_03_31_041211_create_exam_results_table', 18),
(53, '2026_03_31_041212_create_exam_schedules_table', 18),
(54, '2026_03_31_041213_create_fee_groups_table', 18),
(55, '2026_03_31_041214_create_fee_structures_table', 18),
(56, '2026_03_31_041215_create_fee_types_table', 18),
(57, '2026_03_31_041216_create_payments_table', 18),
(58, '2026_03_31_041217_create_school_classes_table', 18),
(59, '2026_03_31_041217_create_staff_departments_table', 18),
(60, '2026_03_31_041218_create_staff_table', 18),
(61, '2026_03_31_041219_create_staff_attendances_table', 18),
(62, '2026_03_31_041221_create_student_attendances_table', 18),
(63, '2026_03_31_041222_create_student_fees_table', 18),
(64, '2026_03_31_041223_create_student_multi_classes_table', 18),
(65, '2026_03_31_041224_create_timetables_table', 18),
(66, '2026_04_08_075603_add_two_factor_columns_to_users_table', 19),
(67, '2026_04_12_000000_add_missing_fields_to_student_tables', 20),
(68, '2026_04_12_000001_create_supporting_student_tables', 21),
(69, '2026_04_12_000002_add_final_design_fields_to_students', 22),
(70, '2026_04_12_000003_add_final_ui_fields_to_student_info', 23),
(71, '2026_04_17_000000_make_users_email_nullable', 24),
(72, '2026_04_17_065319_make_gender_nullable_in_students_table', 25),
(73, '2026_04_21_150004_add_missing_fields_to_staff_table', 26),
(74, '2026_04_21_165455_unify_staff_and_teachers_tables', 27),
(75, '2026_04_22_162000_drop_unwanted_fields_from_staff_table', 28),
(76, '2026_04_22_162500_drop_department_id_from_staff_table', 29),
(77, '2026_05_03_065029_create_expenses_table', 30),
(78, '2026_05_03_065030_create_todos_table', 30),
(79, '2026_05_03_065031_create_events_table', 30),
(80, '2026_05_03_072818_add_extra_fields_to_events_table', 31),
(81, '2026_05_03_112354_update_fee_groups_table', 32);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(4, 'App\\Models\\User', 4),
(4, 'App\\Models\\User', 6),
(4, 'App\\Models\\User', 9),
(4, 'App\\Models\\User', 10),
(4, 'App\\Models\\User', 11),
(4, 'App\\Models\\User', 12),
(4, 'App\\Models\\User', 13),
(1, 'App\\Models\\User', 14),
(2, 'App\\Models\\User', 15),
(2, 'App\\Models\\User', 16),
(2, 'App\\Models\\User', 17),
(2, 'App\\Models\\User', 18),
(2, 'App\\Models\\User', 19),
(4, 'App\\Models\\User', 20),
(4, 'App\\Models\\User', 21),
(4, 'App\\Models\\User', 24),
(4, 'App\\Models\\User', 25),
(4, 'App\\Models\\User', 26),
(4, 'App\\Models\\User', 27),
(4, 'App\\Models\\User', 28),
(4, 'App\\Models\\User', 29),
(4, 'App\\Models\\User', 31),
(4, 'App\\Models\\User', 32),
(4, 'App\\Models\\User', 33),
(4, 'App\\Models\\User', 34),
(4, 'App\\Models\\User', 35),
(4, 'App\\Models\\User', 36),
(4, 'App\\Models\\User', 37),
(4, 'App\\Models\\User', 38),
(4, 'App\\Models\\User', 39),
(4, 'App\\Models\\User', 40),
(4, 'App\\Models\\User', 41);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `is_read`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 14, 'School Reopening', 'The school will reopen on April 1st.', 0, '2026-04-11 03:20:08', '2026-04-11 03:20:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `optional_subjects`
--

CREATE TABLE `optional_subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_fee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'manage_admission', 'web', '2026-02-14 00:57:50', '2026-02-14 00:57:50'),
(2, 'view_reports', 'web', '2026-02-14 00:57:50', '2026-02-14 00:57:50'),
(3, 'manage_academics', 'web', '2026-04-10 14:42:39', '2026-04-10 14:42:39'),
(4, 'manage_fees', 'web', '2026-04-10 14:42:40', '2026-04-10 14:42:40');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'erp_token', 'aecad659ab565bce8304539d1513555687b5f20e1a5928db1c0e1db3fd02ffa6', '[\"*\"]', NULL, NULL, '2026-02-14 01:10:16', '2026-02-14 01:10:16'),
(2, 'App\\Models\\User', 1, 'erp_token', '5257eaad9712243f065454129a25d4c5f0b74cf852251de4368352ab9e731508', '[\"*\"]', NULL, NULL, '2026-02-14 01:13:22', '2026-02-14 01:13:22'),
(3, 'App\\Models\\User', 1, 'erp_token', '436d2592484afe728d02bddc1c816813fb11df84e77ac5e0af70a41244a62d48', '[\"*\"]', NULL, NULL, '2026-02-14 01:13:28', '2026-02-14 01:13:28'),
(4, 'App\\Models\\User', 1, 'erp_token', 'a90c84e7ddac6bbfff5cb2fc209eeb297fd7642504576ff19461342f192ba9c0', '[\"*\"]', NULL, NULL, '2026-02-14 01:14:01', '2026-02-14 01:14:01'),
(5, 'App\\Models\\User', 1, 'erp_token', '2efd202fa15cb1b8e9d5a2115d52bb995cb604a26823f20c0560470b0c67d188', '[\"*\"]', '2026-03-15 02:08:06', NULL, '2026-03-11 05:13:19', '2026-03-15 02:08:06'),
(6, 'App\\Models\\User', 1, 'erp_token', 'b6b5e859a235065e60b7d5b2ab375bdf9119c27368ed33141204de2b2fc48db6', '[\"*\"]', NULL, NULL, '2026-03-15 02:10:04', '2026-03-15 02:10:04'),
(7, 'App\\Models\\User', 1, 'erp_token', '5d4d6e2aa9ff8168ef80a8687bcb8576b16267e5065f59273a841ba2641177cf', '[\"*\"]', NULL, NULL, '2026-03-15 04:53:51', '2026-03-15 04:53:51'),
(8, 'App\\Models\\User', 1, 'erp_token', 'c588c0706a888666398dd5ea4930affb7dfcf4d588db7b6ed8c68ce01547eb8b', '[\"*\"]', '2026-03-15 06:08:43', NULL, '2026-03-15 05:11:11', '2026-03-15 06:08:43'),
(9, 'App\\Models\\User', 1, 'erp_token', '3418f0f21c8fba8904a65847bc2276ecab5d32505da501e93bab92b578c2f91a', '[\"*\"]', '2026-03-21 06:13:53', NULL, '2026-03-21 01:23:38', '2026-03-21 06:13:53'),
(10, 'App\\Models\\User', 13, 'erp_token', 'a1741e1f9f01bf984335e2fb096ab7e945515d85c11f528c5ea42109ef3d0647', '[\"*\"]', NULL, NULL, '2026-03-21 06:22:53', '2026-03-21 06:22:53'),
(11, 'App\\Models\\User', 13, 'erp_token', 'db20662e51a5fb3b26bc57e6b1728ec2a18855fce5778b21ee673b70f51aeabb', '[\"*\"]', NULL, NULL, '2026-03-21 06:27:45', '2026-03-21 06:27:45'),
(12, 'App\\Models\\User', 1, 'test', '6063703fa30cd89a73868c383cec2068ddbd44ab015079b387e38e80e059cea1', '[\"*\"]', '2026-03-25 23:49:41', NULL, '2026-03-25 23:49:40', '2026-03-25 23:49:41'),
(13, 'App\\Models\\User', 13, 'erp_token', '00a8ed54896f87a3dd19533abe2d62ee6b9d6d79d616588122c092c4269c90ee', '[\"*\"]', NULL, NULL, '2026-03-26 01:57:25', '2026-03-26 01:57:25'),
(14, 'App\\Models\\User', 14, 'erp_token', 'e6484b5db475d427e30bacbba53f74cfe28f9eeda7bff461261aa63c02461d47', '[\"*\"]', NULL, NULL, '2026-04-11 12:47:13', '2026-04-11 12:47:13'),
(15, 'App\\Models\\User', 1, 'erp_token', 'beb438455e87ff4bf48bbade090768fb18de0c255c968ddf3d9c8e3708d4f762', '[\"*\"]', NULL, NULL, '2026-04-11 12:47:49', '2026-04-11 12:47:49'),
(16, 'App\\Models\\User', 1, 'erp_token', '470d8ff721865035e83d9fdd9c5c96c0e5abfb7c9fcbe0df574280efbf63c55c', '[\"*\"]', NULL, NULL, '2026-04-11 21:07:08', '2026-04-11 21:07:08'),
(17, 'App\\Models\\User', 1, 'erp_token', '3a27c08c2453647cac0220df415b7cca8c1c05ebbab7524d9aaadd947db743cb', '[\"*\"]', NULL, NULL, '2026-04-12 03:29:36', '2026-04-12 03:29:36'),
(18, 'App\\Models\\User', 1, 'erp_token', 'b606566ffa25e9b3fde2310725ba0d2209e4ec72fa720bee32a6986260d23228', '[\"*\"]', '2026-04-12 04:15:57', NULL, '2026-04-12 04:01:08', '2026-04-12 04:15:57'),
(19, 'App\\Models\\User', 1, 'erp_token', '6161a15bb76094a17aae5b49108472b3d68d15cf50d084cadf3caf6557f97d20', '[\"*\"]', '2026-04-17 01:31:23', NULL, '2026-04-17 00:02:54', '2026-04-17 01:31:23'),
(20, 'App\\Models\\User', 1, 'erp_token', '5daff68e62f292e449c8b4e5abbc1a6d693ccd067510cd1c5f436d893ddb02e0', '[\"*\"]', '2026-04-20 03:36:21', NULL, '2026-04-20 01:38:26', '2026-04-20 03:36:21'),
(21, 'App\\Models\\User', 14, 'erp_token', 'e8d12d252672ba325952fcc6afb0de97f409cdcdfe81a865186817382df2609c', '[\"*\"]', NULL, NULL, '2026-05-08 17:14:21', '2026-05-08 17:14:21'),
(22, 'App\\Models\\User', 14, 'erp_token', '599fc0e841cd9847b657e26dfd95ab339d5c29d45eb50ab0f621ca934b8b8cb1', '[\"*\"]', '2026-05-24 13:37:08', NULL, '2026-05-08 17:14:30', '2026-05-24 13:37:08'),
(23, 'App\\Models\\User', 14, 'erp_token', 'ee1907b6f1e60f1c93b6375382a70acc80bcd0815dacada89fa7da7404b04c9d', '[\"*\"]', NULL, NULL, '2026-05-10 07:00:56', '2026-05-10 07:00:56'),
(24, 'App\\Models\\User', 14, 'erp_token', '6a58fd8b24c8d97255b8b43693c2a81dc83a82b0266c43fc17051874e5462bff', '[\"*\"]', '2026-05-10 11:11:45', NULL, '2026-05-10 07:01:21', '2026-05-10 11:11:45'),
(25, 'App\\Models\\User', 1, 'erp_token', 'ff971a2f3d9f6581296b5146b893a11e3f3456bd886693c95ff0c62733ef52f1', '[\"*\"]', '2026-07-14 10:18:42', NULL, '2026-05-10 07:01:23', '2026-07-14 10:18:42'),
(26, 'App\\Models\\User', 14, 'erp_token', 'd0b5a88fdb97b9385bbc74776e619f71c60fee18c781556ec384d033c6cbd742', '[\"*\"]', NULL, NULL, '2026-05-10 09:27:35', '2026-05-10 09:27:35'),
(27, 'App\\Models\\User', 14, 'erp_token', '33242330cab434cfab3f408a99195c48b15452e4e70bed16fc1de28a86123ebf', '[\"*\"]', '2026-05-10 10:08:37', NULL, '2026-05-10 09:28:58', '2026-05-10 10:08:37'),
(28, 'App\\Models\\User', 14, 'erp_token', '6eb2704199e4fce8aeb9e6516ee5cfc4d6040eabdf67d6a13499887e78849695', '[\"*\"]', '2026-06-23 06:10:06', NULL, '2026-05-10 10:24:36', '2026-06-23 06:10:06'),
(29, 'App\\Models\\User', 14, 'erp_token', 'd8b91f1c38b68f31d7efeae6b34c84964e0d6fecd878aae03a150de658793b99', '[\"*\"]', '2026-05-10 10:58:51', NULL, '2026-05-10 10:26:52', '2026-05-10 10:58:51'),
(30, 'App\\Models\\User', 14, 'erp_token', 'bfab5cee025a1a23a98509e0c2b7e1d621dbbf319a0023f95e404740633dec06', '[\"*\"]', NULL, NULL, '2026-05-10 12:59:20', '2026-05-10 12:59:20'),
(31, 'App\\Models\\User', 14, 'erp_token', '10014a4026f9b83c31c51ea6e08f2df9140fb8ca8a4a6184636960cedba04f92', '[\"*\"]', '2026-06-10 18:47:02', NULL, '2026-05-16 06:40:03', '2026-06-10 18:47:02'),
(32, 'App\\Models\\User', 14, 'erp_token', '0f061de4b9ba89489231a46b06617f9c32773b9f08b93647ae4146e3b91a05ee', '[\"*\"]', NULL, NULL, '2026-05-19 14:34:18', '2026-05-19 14:34:18'),
(33, 'App\\Models\\User', 14, 'erp_token', '7b4e59e55dfeef4e04a602a503e8934dbc770f09f3d87089099982e8e9d008f8', '[\"*\"]', '2026-07-09 12:10:24', NULL, '2026-05-19 14:37:06', '2026-07-09 12:10:24'),
(34, 'App\\Models\\User', 14, 'erp_token', 'f9483c9306fc84829e6434d7b965dd9d396ff2529a8364380349608cc11e2037', '[\"*\"]', '2026-06-01 09:54:41', NULL, '2026-06-01 09:53:14', '2026-06-01 09:54:41'),
(35, 'App\\Models\\User', 14, 'erp_token', '09805ff7292714a732c7f93cf03a8e8238526b2db8652a32ee3ce9b40122c03d', '[\"*\"]', '2026-06-23 06:13:45', NULL, '2026-06-23 06:10:36', '2026-06-23 06:13:45'),
(36, 'App\\Models\\User', 14, 'erp_token', '4c3e6d72bf8a561bd623e5f3d27f83ae66042706a5da515ba82011ddb55fb0f8', '[\"*\"]', '2026-06-23 06:15:19', NULL, '2026-06-23 06:14:03', '2026-06-23 06:15:19'),
(37, 'App\\Models\\User', 14, 'erp_token', 'd81ec87df48e53c99b8d2f8da30dbb130e7a1115f548b0193c2518bb5942946d', '[\"*\"]', NULL, NULL, '2026-06-23 06:15:32', '2026-06-23 06:15:32'),
(38, 'App\\Models\\User', 14, 'erp_token', '028737ac1fd8ccd6bd9d62a2dc23175352250099cebc2a1234a40d5b055c2eb6', '[\"*\"]', '2026-06-23 06:16:49', NULL, '2026-06-23 06:15:50', '2026-06-23 06:16:49'),
(39, 'App\\Models\\User', 14, 'erp_token', '23e14f194a883b2467d2140e906e3cdf8fde79c1340b21c829ad5e3230369529', '[\"*\"]', '2026-06-23 06:19:11', NULL, '2026-06-23 06:16:58', '2026-06-23 06:19:11'),
(40, 'App\\Models\\User', 14, 'erp_token', 'f488b463f21a462c97503a131ede434115dea384881c82564d61e0ac9d78f834', '[\"*\"]', '2026-06-23 06:21:27', NULL, '2026-06-23 06:21:15', '2026-06-23 06:21:27'),
(41, 'App\\Models\\User', 14, 'erp_token', '5e932cc1741b040c95bd517d454460351108f564c3e1a5a3f980bf09ac0d26ca', '[\"*\"]', NULL, NULL, '2026-06-30 11:15:11', '2026-06-30 11:15:11'),
(42, 'App\\Models\\User', 14, 'erp_token', '4caefee6712e9c1ab27a5afb70acc386ccc761756ddd1846cb8cd99df25b699e', '[\"*\"]', '2026-07-14 05:49:35', NULL, '2026-07-09 12:07:03', '2026-07-14 05:49:35'),
(43, 'App\\Models\\User', 1, 'erp_token', '05518865e3022e3aceb63ad3b03434f06dd4833d8d9fcf8353673cab3b1f18bc', '[\"*\"]', NULL, NULL, '2026-07-09 12:07:22', '2026-07-09 12:07:22'),
(44, 'App\\Models\\User', 1, 'erp_token', '91b04ab4e68cf459e0d3606abff3631346550263a00f3c72799c27228eb7f1aa', '[\"*\"]', NULL, NULL, '2026-07-09 12:07:32', '2026-07-09 12:07:32'),
(45, 'App\\Models\\User', 1, 'erp_token', '787d7a3dd8ec0f83a5e558eb43c5a2f9add37ef807fb58f601a2c3fdef52fc9b', '[\"*\"]', '2026-07-09 13:04:35', NULL, '2026-07-09 12:13:29', '2026-07-09 13:04:35'),
(46, 'App\\Models\\User', 14, 'erp_token', 'a1972fd754e0bee480c54160515dec78ebe07aea67851c1a0c58e64c77983e71', '[\"*\"]', '2026-07-14 05:50:24', NULL, '2026-07-09 12:19:11', '2026-07-14 05:50:24'),
(47, 'App\\Models\\User', 14, 'erp_token', 'cb5b5b95cfbbd02b662ac6516b5ce9ac94579dc5b1e56a434fda7a7b29595768', '[\"*\"]', '2026-07-14 06:24:14', NULL, '2026-07-14 06:14:48', '2026-07-14 06:24:14'),
(48, 'App\\Models\\User', 14, 'erp_token', '6cf84fc4d6879183a27ed3c4846af03de8893ecc361da1054bdf17e7f94979f2', '[\"*\"]', '2026-07-14 06:30:09', NULL, '2026-07-14 06:28:16', '2026-07-14 06:30:09'),
(49, 'App\\Models\\User', 14, 'erp_token', '43580c3b83086dc91a732bf7bf0286e2b826f3a3f2d9cb18d7a25842042547e2', '[\"*\"]', '2026-07-14 06:33:13', NULL, '2026-07-14 06:30:32', '2026-07-14 06:33:13'),
(50, 'App\\Models\\User', 14, 'erp_token', 'cc2474c20e2f21614fa8519c08a78c1af919414316defff5417ff2600769abcc', '[\"*\"]', '2026-07-14 06:33:59', NULL, '2026-07-14 06:33:52', '2026-07-14 06:33:59'),
(51, 'App\\Models\\User', 14, 'erp_token', 'c92f25c78c1bc29abd1cddb49e0ab2a7aa754abb0575932eb04a7d946e461cb1', '[\"*\"]', NULL, NULL, '2026-07-14 06:34:33', '2026-07-14 06:34:33'),
(52, 'App\\Models\\User', 14, 'erp_token', '39ed55f4007588c8d21f0a8eedff35b781b30618e15804a510b04773286114c6', '[\"*\"]', NULL, NULL, '2026-07-14 06:35:46', '2026-07-14 06:35:46'),
(53, 'App\\Models\\User', 14, 'erp_token', '338916febd3dd912651de057948ff2a56758dea624c9b13d372eb5fcc331124f', '[\"*\"]', NULL, NULL, '2026-07-14 06:36:02', '2026-07-14 06:36:02'),
(54, 'App\\Models\\User', 14, 'erp_token', '9a73bf75ac18acc20dea925536207645cd6acc2b164dd48b865a8af0be00a204', '[\"*\"]', '2026-07-14 06:39:51', NULL, '2026-07-14 06:39:36', '2026-07-14 06:39:51'),
(55, 'App\\Models\\User', 14, 'erp_token', '2ba5724d6127d3f30207f7128e051b9d94d7fef0c4200a8db7d44ed1ccb6c69f', '[\"*\"]', '2026-07-14 06:44:12', NULL, '2026-07-14 06:40:21', '2026-07-14 06:44:12'),
(56, 'App\\Models\\User', 14, 'erp_token', 'eb0967e8b81197e0e8b88f7d28c6c41646e0dffbd60086eb9bba01bf248adbb6', '[\"*\"]', '2026-07-14 06:44:33', NULL, '2026-07-14 06:44:32', '2026-07-14 06:44:33'),
(57, 'App\\Models\\User', 14, 'erp_token', '4353c354350d1bbbe59d6739fd193ce881d691b77aa0d3e9476868d780173939', '[\"*\"]', '2026-07-14 06:47:17', NULL, '2026-07-14 06:46:40', '2026-07-14 06:47:17'),
(58, 'App\\Models\\User', 14, 'erp_token', 'e318bf026276ed8269edebad96b47d695c8d969906c35521a140abefbd1e47bb', '[\"*\"]', '2026-07-14 06:48:46', NULL, '2026-07-14 06:47:33', '2026-07-14 06:48:46'),
(59, 'App\\Models\\User', 14, 'erp_token', '57ec915373a3e717e21dec5fe26fd709cdf6903f830a58fffed5af5b6122b91b', '[\"*\"]', '2026-07-14 06:51:13', NULL, '2026-07-14 06:49:08', '2026-07-14 06:51:13'),
(60, 'App\\Models\\User', 14, 'erp_token', '396ad5df32240a83b3e6578e882ad9cb82608b4c34bc99706f4fed8015b010e2', '[\"*\"]', NULL, NULL, '2026-07-14 06:51:31', '2026-07-14 06:51:31'),
(61, 'App\\Models\\User', 14, 'erp_token', 'b225695adbb265609cfc369dc1470d0544fa372ff27b3312183626e4bcc89b89', '[\"*\"]', NULL, NULL, '2026-07-14 06:52:19', '2026-07-14 06:52:19'),
(62, 'App\\Models\\User', 14, 'erp_token', '37a1773bea1fd63de54301e6dd2e2d21ed9d34b08cc1897ce37bd2aef43cd606', '[\"*\"]', NULL, NULL, '2026-07-14 06:52:32', '2026-07-14 06:52:32'),
(63, 'App\\Models\\User', 14, 'erp_token', 'f03592f07763b5beda7d4bb7128afe21eb41aaec3b23afa17866e47e7737d386', '[\"*\"]', NULL, NULL, '2026-07-14 06:52:45', '2026-07-14 06:52:45'),
(64, 'App\\Models\\User', 14, 'erp_token', 'c2b91080dc92b092dfb911b06e614bf614d94f8ac82b3c26d7131c8a329ec4b5', '[\"*\"]', '2026-07-14 07:10:52', NULL, '2026-07-14 06:55:20', '2026-07-14 07:10:52'),
(65, 'App\\Models\\User', 14, 'erp_token', 'b42aaec09bee62b6f9e97e36344388c82c087bb0f05b3cbb7254955341fcc61f', '[\"*\"]', '2026-07-14 07:12:08', NULL, '2026-07-14 07:11:53', '2026-07-14 07:12:08'),
(66, 'App\\Models\\User', 14, 'erp_token', '911431133fa78df8019e9ade300476e0a6b7bcd428a82af325beb26b040c15ad', '[\"*\"]', '2026-07-14 07:47:47', NULL, '2026-07-14 07:24:28', '2026-07-14 07:47:47'),
(67, 'App\\Models\\User', 14, 'erp_token', '2124511a39028164d9423ed63647139d9a0af9144bf09d21f1fed107b46857d9', '[\"*\"]', '2026-07-14 07:55:18', NULL, '2026-07-14 07:48:15', '2026-07-14 07:55:18'),
(68, 'App\\Models\\User', 14, 'erp_token', '00812015b02c33e6afaacf6b6d658ad47df52ceecaa82ef7a5282e069deb0f20', '[\"*\"]', '2026-07-14 07:55:58', NULL, '2026-07-14 07:55:35', '2026-07-14 07:55:58'),
(69, 'App\\Models\\User', 14, 'erp_token', '526d70933357cae00b1eadb79bdeebaa3008d102cec199caf3a7138eecaaf4e8', '[\"*\"]', '2026-07-15 16:23:11', NULL, '2026-07-14 07:56:12', '2026-07-15 16:23:11'),
(70, 'App\\Models\\User', 14, 'erp_token', '63b08c84cd1c55126dd795b16b0e2b511c2bf85bb35deb713545a92a17635bb6', '[\"*\"]', '2026-07-14 09:01:53', NULL, '2026-07-14 09:00:23', '2026-07-14 09:01:53'),
(71, 'App\\Models\\User', 14, 'erp_token', 'f5dca42c202a339dd231b83e7174fac9ffdbcb65621b5f182bea65913b15afbe', '[\"*\"]', '2026-07-14 09:43:29', NULL, '2026-07-14 09:36:38', '2026-07-14 09:43:29'),
(72, 'App\\Models\\User', 14, 'erp_token', '3e3167afccd08a7795f89a4680461c26303922ffae24769c155a608a331aef2d', '[\"*\"]', NULL, NULL, '2026-07-14 09:48:41', '2026-07-14 09:48:41'),
(73, 'App\\Models\\User', 14, 'erp_token', 'f291bd8d98a9066e8da59cb742cf5ecc494b0bcaa8c6bcaae977ad6cb5d45f79', '[\"*\"]', '2026-07-15 16:25:24', NULL, '2026-07-15 16:25:14', '2026-07-15 16:25:24'),
(74, 'App\\Models\\User', 14, 'erp_token', '4b22f78d4814f3e8fb992bfd5aecc02c2da6e6428d168f962c300a3a14d13914', '[\"*\"]', '2026-07-15 16:27:01', NULL, '2026-07-15 16:26:53', '2026-07-15 16:27:01'),
(75, 'App\\Models\\User', 14, 'erp_token', '89dc7243c06f113875e8186e968bf08a18a164cfc7ddb31f09829996e754e927', '[\"*\"]', '2026-07-15 16:28:50', NULL, '2026-07-15 16:28:48', '2026-07-15 16:28:50'),
(76, 'App\\Models\\User', 14, 'erp_token', 'ecae1cd52b0f2d0361ff7fa7696b2c5fe6b68b90aa06dec64ed669ce46061bef', '[\"*\"]', '2026-07-16 09:48:53', NULL, '2026-07-15 16:29:12', '2026-07-16 09:48:53'),
(77, 'App\\Models\\User', 14, 'erp_token', '259ef992fddc21b3ee3ac25fdfbba79a60988f3d4ac32bf4f195703d2446d711', '[\"*\"]', '2026-07-15 16:50:03', NULL, '2026-07-15 16:49:48', '2026-07-15 16:50:03'),
(78, 'App\\Models\\User', 14, 'erp_token', '5f09447c3c90589427a374f171fc24ab779bd71809dd6de7e51ffd9e25f423ff', '[\"*\"]', '2026-07-15 19:06:04', NULL, '2026-07-15 16:50:11', '2026-07-15 19:06:04'),
(79, 'App\\Models\\User', 14, 'erp_token', '0cd9441cc354c62e3901e63346767cccc09f57ed48fbc215b8795a9aaaf01948', '[\"*\"]', '2026-07-15 19:03:41', NULL, '2026-07-15 18:10:42', '2026-07-15 19:03:41'),
(80, 'App\\Models\\User', 14, 'erp_token', 'cc082cc53eba1b2162e04f90dad050a1a501aba0abce75522f033b8135a1f189', '[\"*\"]', '2026-07-16 13:06:06', NULL, '2026-07-16 09:49:17', '2026-07-16 13:06:06');

-- --------------------------------------------------------

--
-- Table structure for table `phone_call_logs`
--

CREATE TABLE `phone_call_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `call_type` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `follow_up_date` date DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `postal_dispatches`
--

CREATE TABLE `postal_dispatches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `to_title` varchar(255) NOT NULL,
  `reference_no` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date` date NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `postal_receives`
--

CREATE TABLE `postal_receives` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `from_title` varchar(255) NOT NULL,
  `reference_no` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date` date NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2026-02-14 00:57:50', '2026-02-14 00:57:50'),
(2, 'teacher', 'web', '2026-02-14 00:57:50', '2026-02-14 00:57:50'),
(3, 'staff', 'web', '2026-02-14 00:57:50', '2026-02-14 00:57:50'),
(4, 'student', 'web', NULL, NULL),
(5, 'parent', 'web', '2026-04-10 14:42:38', '2026-04-10 14:42:38');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `fare` varchar(255) DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `school_classes`
--

CREATE TABLE `school_classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `class_id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 4, 'Science', '2026-03-21 03:33:35', '2026-03-21 03:33:35', NULL),
(2, 4, 'Humanities', '2026-03-21 03:33:35', '2026-03-21 03:33:35', NULL),
(3, 5, 'Section A', '2026-03-21 06:11:09', '2026-03-21 06:11:09', NULL),
(4, 6, 'A', '2026-03-30 10:46:25', '2026-03-30 10:46:25', NULL),
(5, 7, 'A', '2026-04-10 14:42:45', '2026-04-10 14:42:45', NULL),
(6, 7, 'B', '2026-04-10 14:42:45', '2026-04-10 14:42:45', NULL),
(7, 8, 'A', '2026-04-10 14:42:45', '2026-04-10 14:42:45', NULL),
(8, 8, 'B', '2026-04-10 14:42:45', '2026-04-10 14:42:45', NULL),
(9, 9, 'A', '2026-04-10 14:42:45', '2026-04-10 14:42:45', NULL),
(10, 9, 'B', '2026-04-10 14:42:45', '2026-04-10 14:42:45', NULL),
(11, 10, 'A', '2026-04-10 14:42:45', '2026-04-10 14:42:45', NULL),
(12, 10, 'B', '2026-04-10 14:42:45', '2026-04-10 14:42:45', NULL),
(13, 11, 'A', '2026-04-10 14:42:45', '2026-04-10 14:42:45', NULL),
(14, 11, 'B', '2026-04-10 14:42:45', '2026-04-10 14:42:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `shared_contents`
--

CREATE TABLE `shared_contents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `content_id` bigint(20) UNSIGNED NOT NULL,
  `shared_with_role` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `staff_no` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
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
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `user_id`, `email`, `phone`, `designation`, `created_at`, `updated_at`, `deleted_at`, `staff_no`, `role`, `first_name`, `last_name`, `father_name`, `mother_name`, `gender`, `dob`, `date_of_joining`, `marital_status`, `emergency_mobile`, `driving_license`, `photo`, `is_expert_staff`, `current_address`, `permanent_address`, `qualifications`, `experience`, `facebook_url`, `twitter_url`, `linkedin_url`, `instagram_url`, `resume`, `joining_letter`, `other_document`, `bank_account_name`, `bank_account_no`, `bank_name`, `bank_branch_name`, `epf_no`, `basic_salary`, `contract_type`, `location`) VALUES
(1, NULL, 'staff1@school.com', '+1 (814) 656-5259', 'Assistant', '2026-04-10 14:42:49', '2026-04-10 14:42:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, NULL, 'staff2@school.com', '+15125853947', 'Assistant', '2026-04-10 14:42:50', '2026-04-10 14:42:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, NULL, 'staff3@school.com', '712.209.9717', 'Assistant', '2026-04-10 14:42:50', '2026-04-10 14:42:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `staff_attendances`
--

CREATE TABLE `staff_attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `staff_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_departments`
--

CREATE TABLE `staff_departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff_departments`
--

INSERT INTO `staff_departments` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Academic', '2026-04-10 14:42:44', '2026-04-10 14:42:44', NULL),
(2, 'Administration', '2026-04-10 14:42:44', '2026-04-10 14:42:44', NULL),
(3, 'Accounts', '2026-04-10 14:42:44', '2026-04-10 14:42:44', NULL),
(4, 'Library', '2026-04-10 14:42:44', '2026-04-10 14:42:44', NULL),
(5, 'Transport', '2026-04-10 14:42:44', '2026-04-10 14:42:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `admission_no` varchar(255) NOT NULL,
  `admission_date` date DEFAULT NULL,
  `roll_no` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
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
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `section_id` bigint(20) UNSIGNED DEFAULT NULL,
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
  `previous_school_name` varchar(255) DEFAULT NULL,
  `previous_qualification` varchar(255) DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `previous_school_details` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `is_disabled` tinyint(1) NOT NULL DEFAULT 0,
  `disable_reason` varchar(255) DEFAULT NULL,
  `disable_date` date DEFAULT NULL,
  `route_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dormitory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `admission_no`, `admission_date`, `roll_no`, `first_name`, `last_name`, `dob`, `religion`, `caste`, `blood_group`, `height`, `weight`, `as_on_date`, `gender`, `phone`, `emergency_phone`, `alternate_phone`, `email`, `category_id`, `student_group_id`, `photo`, `status`, `created_at`, `updated_at`, `deleted_at`, `class_id`, `section_id`, `address`, `date_of_birth`, `nationality`, `current_address`, `permanent_address`, `bank_account_no`, `bank_name`, `ifsc_code`, `national_id_no`, `local_id_no`, `birth_certificate_no`, `previous_school_name`, `previous_qualification`, `medical_history`, `previous_school_details`, `note`, `is_disabled`, `disable_reason`, `disable_date`, `route_id`, `vehicle_id`, `dormitory_id`, `room_id`) VALUES
(5, NULL, 'STU-1774093269', NULL, NULL, 'John', 'Doe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'male', '1234567890', NULL, NULL, 'john_1774093269@example.com', NULL, NULL, NULL, 1, '2026-03-21 06:11:09', '2026-03-21 06:11:09', NULL, 5, 3, NULL, NULL, 'American', '123 Street Ave', '456 Block Rd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(6, NULL, 'ADM2026001', NULL, NULL, 'John', 'Doe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'male', '0987654321', NULL, NULL, 'john.doe@example.com', NULL, NULL, NULL, 1, '2026-03-30 10:46:25', '2026-03-30 10:46:25', NULL, 6, 4, NULL, '2010-01-01', 'American', '456 Test Ave', '456 Test Ave', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(7, NULL, 'STU0001', NULL, NULL, 'Declan', 'Mraz', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'female', '(283) 836-6510', NULL, NULL, 'ludwig.kunde@example.com', 2, NULL, NULL, 1, '2026-04-10 14:48:24', '2026-04-10 14:48:24', NULL, 7, 5, NULL, '2000-06-19', NULL, '999 Rowe Square Apt. 220\nFeestbury, KS 95615-5294', '228 Justen Estate Apt. 706\nPagacburgh, ID 44848-2249', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(8, NULL, 'STU0002', NULL, NULL, 'Elvera', 'Friesen', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'male', '(703) 536-8121', NULL, NULL, 'brendan24@example.org', 2, NULL, NULL, 1, '2026-04-10 14:48:24', '2026-04-10 14:48:24', NULL, 7, 5, NULL, '1976-11-02', NULL, '388 Koss Ports Suite 096\nWest Macy, NH 66058-6987', '39151 Lind Cape\nHilmaburgh, CA 17117-4689', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(9, NULL, 'STU0003', NULL, NULL, 'Adolfo', 'Cassin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'female', '+1-574-835-5108', NULL, NULL, 'mcdermott.andreane@example.com', 2, NULL, NULL, 1, '2026-04-10 14:48:25', '2026-04-10 14:48:25', NULL, 7, 5, NULL, '2001-08-22', NULL, '382 Herman Mountains Apt. 543\nWardhaven, DC 46326', '3220 Mosciski Stravenue Suite 136\nSouth Ronny, ME 72528-3908', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(10, NULL, 'STU0004', NULL, NULL, 'Tod', 'Dickinson', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'female', '425-839-0708', NULL, NULL, 'kovacek.emmanuelle@example.com', 2, NULL, NULL, 1, '2026-04-10 14:48:25', '2026-04-10 14:48:25', NULL, 7, 5, NULL, '1996-11-03', NULL, '1138 Jefferey Way Apt. 637\nSchmelerberg, KS 57354-1661', '639 Erik Heights\nFeestland, NJ 35247', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(11, NULL, 'STU0005', NULL, NULL, 'Hosea', 'Johnston', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'female', '+1 (651) 895-8612', NULL, NULL, 'schumm.lilly@example.org', 2, NULL, NULL, 1, '2026-04-10 14:48:25', '2026-04-10 14:48:25', NULL, 7, 5, NULL, '1971-03-10', NULL, '8772 Zoila Lane\nLake Gladyce, NC 93076-5023', '8404 Casper Junction Suite 173\nEast Tayatown, NJ 52577-6587', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(12, NULL, 'STU0006', NULL, NULL, 'Waylon', 'Hartmann', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'male', '(805) 656-7543', NULL, NULL, 'ymuller@example.com', 2, NULL, NULL, 1, '2026-04-10 14:48:25', '2026-04-10 14:48:25', NULL, 7, 5, NULL, '1985-06-23', NULL, '3498 Crona Mountain\nEast Spencer, CT 17065-3939', '41502 Kuhlman Trail\nGarrisonport, ND 12613', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(13, NULL, 'STU0007', NULL, NULL, 'Jevon', 'Funk', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'male', '+1 (480) 589-8582', NULL, NULL, 'mariano.deckow@example.net', 2, NULL, NULL, 1, '2026-04-10 14:48:25', '2026-04-10 14:48:25', NULL, 7, 5, NULL, '1972-12-27', NULL, '6019 Ozella Flat\nPort Ashtyn, KS 02747', '4503 Becker Park Suite 974\nCassinfurt, NM 99215-3483', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(14, NULL, 'STU0008', NULL, NULL, 'Chad', 'Jenkins', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'female', '+1-854-662-8153', NULL, NULL, 'nora.oconnell@example.net', 2, NULL, NULL, 1, '2026-04-10 14:48:25', '2026-04-10 14:48:25', NULL, 7, 5, NULL, '2000-09-21', NULL, '6120 Alene Tunnel Apt. 009\nLake Miguel, OR 04740', '1712 Ullrich Spur\nNorth Glorialand, TN 06415', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(15, NULL, 'STU0009', NULL, NULL, 'Emmitt', 'Thiel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'male', '+1-254-979-9760', NULL, NULL, 'brooklyn.hills@example.com', 2, NULL, NULL, 1, '2026-04-10 14:48:25', '2026-04-10 14:48:25', NULL, 7, 5, NULL, '1991-12-07', NULL, '876 Bernhard Meadow Suite 670\nLake Aniyah, CA 79193-3868', '526 Hilpert Trace Suite 566\nNew Lue, TX 00374-8123', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(16, NULL, 'STU0010', NULL, NULL, 'Adaline', 'Johns', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'female', '1-432-677-3296', NULL, NULL, 'una.kertzmann@example.net', 2, NULL, NULL, 1, '2026-04-10 14:48:25', '2026-04-10 14:48:25', NULL, 7, 5, NULL, '1972-09-25', NULL, '281 Langosh Summit Suite 144\nWest Aracelyland, MS 68460', '77117 Gislason Overpass Apt. 801\nProhaskafurt, WI 48890', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 27, '1233', NULL, NULL, 'test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'male', NULL, NULL, NULL, 'test07@gmail.com', NULL, NULL, NULL, 1, '2026-04-12 04:08:00', '2026-04-12 04:08:00', NULL, 4, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 28, '12333', NULL, NULL, 'test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'male', NULL, NULL, NULL, 'test007@gmail.com', NULL, NULL, NULL, 1, '2026-04-12 04:10:02', '2026-04-12 04:10:02', NULL, 4, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 29, '12338', NULL, NULL, 'test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'male', NULL, NULL, NULL, 'test005@gmail.com', NULL, NULL, NULL, 1, '2026-04-12 04:15:58', '2026-04-12 04:15:58', NULL, 4, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 34, '45321', NULL, NULL, 'test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-17 01:31:24', '2026-04-17 01:31:24', NULL, 4, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 35, '4646466', NULL, '6464656', 'Junaid', 'Ahammed', NULL, 'Muslim', 'Muslim mappila', 'A+', NULL, NULL, NULL, 'Male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-10 07:15:42', '2026-05-10 07:15:42', NULL, 3, 6, NULL, NULL, NULL, 'Avunhippuram(House)', 'gdfngndkdhnhnkjd', NULL, NULL, NULL, '64656646464646', NULL, '5456464646546', 'amup school', 'gfsfgdshshsgh', NULL, 'hdhdhhshdddddd', 'hehyyeyehehr', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 36, 'QW123', NULL, '12354', 'Ramees', 'AP', NULL, 'Muslim', 'MAPPILA', 'A+', NULL, NULL, NULL, 'Male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-10 10:33:21', '2026-05-10 10:33:21', NULL, 3, 5, NULL, NULL, NULL, 'TEST ADDRESS', 'TEST ADDRESS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 37, 'STU1002', '2026-05-10', '22', 'Meera', 'Nair', NULL, 'Hindu', 'General', 'O+', NULL, NULL, NULL, 'female', '9876543211', NULL, NULL, 'meera@example.com', NULL, NULL, NULL, 1, '2026-05-10 10:48:11', '2026-05-10 10:48:11', NULL, 7, 5, NULL, NULL, 'Indian', 'Thrissur, Kerala', 'Thrissur, Kerala', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 38, 'STU1001', NULL, '15', 'Arjun', 'Kumar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'male', '9876543210', NULL, NULL, 'arjun@example.com', NULL, NULL, NULL, 1, '2026-05-10 10:50:47', '2026-05-10 10:50:47', NULL, 7, 5, NULL, NULL, 'Indian', 'Kochi, Kerala', 'Kochi, Kerala', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 39, 'STU10045', NULL, '15', 'Arjun', 'Kumar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'male', '9876543210', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-10 10:53:04', '2026-05-10 10:53:04', NULL, 7, 5, NULL, NULL, 'Indian', 'Kochi, Kerala', 'Kochi, Kerala', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 40, 'STU100445', NULL, '15', 'Arjun', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'male', '9876543210', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-10 10:53:53', '2026-05-10 10:53:53', NULL, 7, 5, NULL, NULL, 'Indian', 'Kochi, Kerala', 'Kochi, Kerala', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 41, '246515', NULL, '1', 'anfas', 'kaloor', NULL, 'Muslim', 'MAPPILA', NULL, NULL, NULL, NULL, 'Male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-06-04 14:53:58', '2026-06-04 14:53:58', NULL, 11, 5, NULL, NULL, NULL, 'kaloor house', 'sacasc', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_academics`
--

CREATE TABLE `student_academics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `roll_no` varchar(255) DEFAULT NULL,
  `academic_year_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_academics`
--

INSERT INTO `student_academics` (`id`, `student_id`, `class_id`, `section_id`, `roll_no`, `academic_year_id`, `created_at`, `updated_at`) VALUES
(1, 5, 5, 3, NULL, 7, '2026-03-21 06:11:09', '2026-03-21 06:11:09'),
(2, 6, 6, 4, '101', 7, '2026-03-30 10:53:40', '2026-03-30 10:53:40'),
(3, 7, 7, 5, '1', 7, '2026-04-10 14:48:24', '2026-04-10 14:48:24'),
(4, 8, 7, 5, '2', 7, '2026-04-10 14:48:25', '2026-04-10 14:48:25'),
(5, 9, 7, 5, '3', 7, '2026-04-10 14:48:25', '2026-04-10 14:48:25'),
(6, 10, 7, 5, '4', 7, '2026-04-10 14:48:25', '2026-04-10 14:48:25'),
(7, 11, 7, 5, '5', 7, '2026-04-10 14:48:25', '2026-04-10 14:48:25'),
(8, 12, 7, 5, '6', 7, '2026-04-10 14:48:25', '2026-04-10 14:48:25'),
(9, 13, 7, 5, '7', 7, '2026-04-10 14:48:25', '2026-04-10 14:48:25'),
(10, 14, 7, 5, '8', 7, '2026-04-10 14:48:25', '2026-04-10 14:48:25'),
(11, 15, 7, 5, '9', 7, '2026-04-10 14:48:25', '2026-04-10 14:48:25'),
(12, 16, 7, 5, '10', 7, '2026-04-10 14:48:25', '2026-04-10 14:48:25'),
(13, 19, 4, 2, NULL, 1, '2026-04-12 04:08:00', '2026-04-12 04:08:00'),
(14, 20, 4, 2, NULL, 1, '2026-04-12 04:10:02', '2026-04-12 04:10:02'),
(15, 21, 4, 2, NULL, 1, '2026-04-12 04:15:58', '2026-04-12 04:15:58'),
(16, 23, 4, 2, NULL, 1, '2026-04-17 01:31:24', '2026-04-17 01:31:24'),
(17, 24, 3, 6, NULL, 6, '2026-05-10 07:15:42', '2026-05-10 07:15:42'),
(18, 25, 3, 5, NULL, 7, '2026-05-10 10:33:21', '2026-05-10 10:33:21'),
(19, 26, 7, 5, NULL, 7, '2026-05-10 10:48:11', '2026-05-10 10:48:11'),
(20, 27, 7, 5, NULL, 7, '2026-05-10 10:50:47', '2026-05-10 10:50:47'),
(21, 28, 7, 5, NULL, 7, '2026-05-10 10:53:04', '2026-05-10 10:53:04'),
(22, 29, 7, 5, NULL, 7, '2026-05-10 10:53:53', '2026-05-10 10:53:53'),
(23, 30, 11, 5, NULL, 6, '2026-06-04 14:53:58', '2026-06-04 14:53:58');

-- --------------------------------------------------------

--
-- Table structure for table `student_attendances`
--

CREATE TABLE `student_attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `section_id` bigint(20) UNSIGNED DEFAULT NULL,
  `academic_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_categories`
--

CREATE TABLE `student_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_categories`
--

INSERT INTO `student_categories` (`id`, `name`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'API Test Category', 'Created via test script', '2026-03-25 23:49:42', '2026-03-25 23:49:42', '2026-03-25 23:49:42'),
(2, 'General', 'General Category', '2026-03-30 10:46:25', '2026-03-30 10:46:25', NULL),
(3, 'OBC', NULL, '2026-04-10 14:42:44', '2026-04-10 14:42:44', NULL),
(4, 'SC', NULL, '2026-04-10 14:42:44', '2026-04-10 14:42:44', NULL),
(5, 'ST', NULL, '2026-04-10 14:42:44', '2026-04-10 14:42:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_documents`
--

CREATE TABLE `student_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_emergency_contacts`
--

CREATE TABLE `student_emergency_contacts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `relation` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_fees`
--

CREATE TABLE `student_fees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fee_structure_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `due_date` date DEFAULT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_groups`
--

CREATE TABLE `student_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_multi_class`
--

CREATE TABLE `student_multi_class` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_multi_classes`
--

CREATE TABLE `student_multi_classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `section_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_parents`
--

CREATE TABLE `student_parents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
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
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_parents`
--

INSERT INTO `student_parents` (`id`, `student_id`, `father_name`, `father_phone`, `father_email`, `father_occupation`, `father_photo`, `mother_name`, `mother_phone`, `mother_email`, `mother_occupation`, `mother_photo`, `guardian_name`, `guardian_phone`, `guardian_occupation`, `guardian_email`, `guardian_relation`, `guardian_address`, `guardian_photo`, `guardian_is`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 5, 'John Doe', '1234567890', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-25 23:49:42', '2026-03-25 23:49:42', '2026-03-25 23:49:42'),
(2, 6, 'Michael Doe', '1122334455', NULL, NULL, NULL, 'Sarah Doe', '5544332211', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-30 10:46:25', '2026-03-30 10:46:25', NULL),
(3, 7, 'Darrion Bogan I', '850-659-1894', NULL, NULL, NULL, 'Madonna Powlowski', '(346) 724-0651', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-10 14:48:24', '2026-04-10 14:48:24', NULL),
(4, 8, 'Murphy Ziemann', '1-320-219-4249', NULL, NULL, NULL, 'Juliet Goldner', '(870) 651-1533', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-10 14:48:25', '2026-04-10 14:48:25', NULL),
(5, 9, 'Webster Harber', '478.398.5302', NULL, NULL, NULL, 'Theodora Quitzon', '229.707.1144', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-10 14:48:25', '2026-04-10 14:48:25', NULL),
(6, 10, 'Sherman Wehner', '727-466-2302', NULL, NULL, NULL, 'Laurie Leuschke', '(920) 430-9536', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-10 14:48:25', '2026-04-10 14:48:25', NULL),
(7, 11, 'Dalton Waters', '361-767-7026', NULL, NULL, NULL, 'Dr. Aleen Conroy MD', '220-323-8533', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-10 14:48:25', '2026-04-10 14:48:25', NULL),
(8, 12, 'Dr. Wellington Zboncak PhD', '858-480-0766', NULL, NULL, NULL, 'Susan Runolfsdottir II', '+16783766149', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-10 14:48:25', '2026-04-10 14:48:25', NULL),
(9, 13, 'Dr. Juvenal Altenwerth', '(838) 219-1625', NULL, NULL, NULL, 'Elinor Pouros', '484-970-2263', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-10 14:48:25', '2026-04-10 14:48:25', NULL),
(10, 14, 'Mr. Donny Lemke DDS', '+1-734-333-1113', NULL, NULL, NULL, 'Yessenia Littel', '630-838-9852', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-10 14:48:25', '2026-04-10 14:48:25', NULL),
(11, 15, 'Mr. Marquis Feest DDS', '626.932.4927', NULL, NULL, NULL, 'Prof. Minnie Blanda', '703-326-6329', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-10 14:48:25', '2026-04-10 14:48:25', NULL),
(12, 16, 'Norris Flatley', '630.375.5839', NULL, NULL, NULL, 'Darlene Schuster', '260.841.3346', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-10 14:48:25', '2026-04-10 14:48:25', NULL),
(13, 19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-12 04:08:00', '2026-04-12 04:08:00', NULL),
(14, 20, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-12 04:10:02', '2026-04-12 04:10:02', NULL),
(15, 21, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-12 04:15:58', '2026-04-12 04:15:58', NULL),
(16, 23, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-17 01:31:24', '2026-04-17 01:31:24', NULL),
(17, 24, 'fshgdh', '86464656', NULL, NULL, NULL, 'gdggdh', '65464646', NULL, NULL, NULL, NULL, NULL, NULL, 'a@gmail.com', NULL, 'gfgdfsfgdsh', NULL, NULL, '2026-05-10 07:15:42', '2026-05-10 07:15:42', NULL),
(18, 25, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-10 10:33:21', '2026-05-10 10:33:21', NULL),
(19, 26, 'Ramesh Nair', '9999999991', 'father@example.com', NULL, NULL, 'Lakshmi Nair', '9999999992', 'mother@example.com', NULL, NULL, 'Ramesh Nair', '9999999991', NULL, NULL, 'Father', NULL, NULL, NULL, '2026-05-10 10:48:11', '2026-05-10 10:48:11', NULL),
(20, 27, 'Raj Kumar', '9999999999', NULL, NULL, NULL, 'Anita Kumar', '8888888888', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-10 10:50:47', '2026-05-10 10:50:47', NULL),
(21, 28, 'Raj Kumar', '9999999999', NULL, NULL, NULL, 'Anita Kumar', '8888888888', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-10 10:53:04', '2026-05-10 10:53:04', NULL),
(22, 29, 'Raj Kumar', '9999999999', NULL, NULL, NULL, 'Anita Kumar', '8888888888', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-10 10:53:53', '2026-05-10 10:53:53', NULL),
(23, 30, 'Abdul Azeez', '9874653210', NULL, NULL, NULL, 'Amina VP', '8745632109', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-04 14:53:58', '2026-06-04 14:53:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `study_materials`
--

CREATE TABLE `study_materials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `section_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `uploaded_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `type` enum('Theory','Practical') NOT NULL DEFAULT 'Theory',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `code`, `type`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Mathematics', 'MATH10', 'Theory', '2026-03-30 10:46:25', '2026-03-30 10:46:25', NULL),
(2, 'Mathematics', 'MATH', 'Theory', '2026-04-10 14:42:45', '2026-04-10 14:42:45', NULL),
(3, 'Science', 'SCI', 'Theory', '2026-04-10 14:42:45', '2026-04-10 14:42:45', NULL),
(4, 'English', 'ENG', 'Theory', '2026-04-10 14:42:45', '2026-04-10 14:42:45', NULL),
(5, 'History', 'HIST', 'Theory', '2026-04-10 14:42:45', '2026-04-10 14:42:45', NULL),
(6, 'Physics Lab', 'PHY_LAB', 'Practical', '2026-04-10 14:42:45', '2026-04-10 14:42:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `timetables`
--

CREATE TABLE `timetables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `section_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `day` varchar(255) DEFAULT NULL,
  `start_time` varchar(255) DEFAULT NULL,
  `end_time` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `todos`
--

CREATE TABLE `todos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `branch_id`, `name`, `email`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `is_active`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Admin', 'admin@erp.com', '$2y$12$.c94mO70Tqxg3J/3qnw1bexTGRgIH5j5t3O/lvJhIdQ8XPXskcxX.', NULL, NULL, NULL, 1, NULL, '2026-02-14 06:37:50', '2026-02-14 06:37:50', NULL),
(2, NULL, 'vishnu', 'vishnu@gmail.com', '$2y$12$DqDgUfHOKhFiZ.vwg3AhKuNrI66akF/YImPoHRtOwo168x8ll8Gba', NULL, NULL, NULL, 1, NULL, '2026-03-11 06:12:57', '2026-03-11 06:12:57', NULL),
(6, NULL, 'vishnu', 'vishnu1@gmail.com', '$2y$12$rwnUufKTP5HMtwJJyHsq6Opq5xvvwiCqhF7sqxkMe10qXNjiRWivq', NULL, NULL, NULL, 1, NULL, '2026-03-11 06:18:44', '2026-03-11 06:18:44', NULL),
(9, NULL, 'vishnu', 'vishnuyy@gmail.com', '$2y$12$Z8.t4CVy8sQ7vEUnPwGrKe8j43U/aht/xQ6Weu.89PzJk7mY2i7RW', NULL, NULL, NULL, 1, NULL, '2026-03-11 06:52:28', '2026-03-11 06:52:28', NULL),
(10, NULL, 'John', 'john_1774093145@example.com', '$2y$12$7T2gmVWZVdpRJRKA2zxWle3FA9N/Yy1PpAt2zKu/YJ4FEPSLBcNKu', NULL, NULL, NULL, 1, NULL, '2026-03-21 06:09:06', '2026-03-21 06:09:06', NULL),
(11, NULL, 'John', 'john_1774093209@example.com', '$2y$12$b3BjUkTu5D7jQOF7oNeziOfLALevx.5JBbXnd4l9q12l2f5.GgTBC', NULL, NULL, NULL, 1, NULL, '2026-03-21 06:10:10', '2026-03-21 06:10:10', NULL),
(12, NULL, 'John', 'john_1774093229@example.com', '$2y$12$oRdSYFECBtPagr1I93VQSePvnoODFEk/2ReCr8g7RAzww1og4/Ur.', NULL, NULL, NULL, 1, NULL, '2026-03-21 06:10:29', '2026-03-21 06:10:29', NULL),
(13, NULL, 'John', 'john_1774093269@example.com', '$2y$12$LU2flJO244jR0t5aAbLukeaBFfHOXC8Q7K4LPCdZeD9xlizfrl9a6', NULL, NULL, NULL, 1, NULL, '2026-03-21 06:11:09', '2026-03-21 06:11:09', NULL),
(14, NULL, 'Admin User', 'admin@admin.com', '$2y$12$pr7zsMBvXEwe40420iexBunWSQxIOkNADhE1.ojnNMHE40oGluIo.', NULL, NULL, NULL, 1, NULL, '2026-03-30 10:45:53', '2026-03-30 10:45:53', NULL),
(15, 3, 'Jaylan Buckridge IV', 'teacher1@school.com', '$2y$12$Iv.2maDI3EPYgyYUbpoEtuqMey9fEptc7ZvdxK3hOX2.sLdaC/GBG', NULL, NULL, NULL, 1, NULL, '2026-04-10 14:42:47', '2026-04-10 14:42:47', NULL),
(16, 3, 'Tess Mayer', 'teacher2@school.com', '$2y$12$2vhaUmSs4f/Q5LR92zeqk.76VsoR.0UWfSFWKJcyfGABPWEH.3yQe', NULL, NULL, NULL, 1, NULL, '2026-04-10 14:42:47', '2026-04-10 14:42:47', NULL),
(17, 3, 'Mr. Grover DuBuque', 'teacher3@school.com', '$2y$12$.lf3N9jV9.0M.KwsaoUIwuhQ3LuqoM.I/6bIdNxfPX5kQw4aBg6.i', NULL, NULL, NULL, 1, NULL, '2026-04-10 14:42:48', '2026-04-10 14:42:48', NULL),
(18, 3, 'Gerard Hill', 'teacher4@school.com', '$2y$12$NabUpEQsl6EDfC1w1rPlPe4lzluRncdSRNF3MHQXxEE17ZB8O.ptu', NULL, NULL, NULL, 1, NULL, '2026-04-10 14:42:48', '2026-04-10 14:42:48', NULL),
(19, 3, 'Dr. Heath Borer Jr.', 'teacher5@school.com', '$2y$12$WWfVpblEUSUpkN2kGm1J8eJ08kAh/wgj/cXNduOophc5Qz/XVwP5y', NULL, NULL, NULL, 1, NULL, '2026-04-10 14:42:49', '2026-04-10 14:42:49', NULL),
(20, NULL, 'test', 'test@gmail.com', '$2y$12$SIrA4.AJw5WR7mAKF6hyj.KtLGcdlKFAX4PkJomjukXDnfYch2w6m', NULL, NULL, NULL, 1, NULL, '2026-04-12 03:46:00', '2026-04-12 03:46:00', NULL),
(21, NULL, 'test', '69db63501915d@gmail.com', '$2y$12$L5gsUITgRUhw9L2GkmC/2e3onIkjNlpaMDHeEGMguMlqA7unXo4NK', NULL, NULL, NULL, 1, NULL, '2026-04-12 03:48:08', '2026-04-12 03:48:08', NULL),
(22, NULL, 'test', '69db63d839792@gmail.com', '$2y$12$f17dJvvNozfXDBNDKxWk6OP30okvlLEQjKlLoB1D90LiylKqyOpyi', NULL, NULL, NULL, 1, NULL, '2026-04-12 03:50:24', '2026-04-12 03:50:24', NULL),
(23, NULL, 'test', '69db643339987@gmail.com', '$2y$12$.VAVJvsYzENrFYD62.nis.i0PapIOeAgM0sAHxM/XEv6bP8r5E1Zy', NULL, NULL, NULL, 1, NULL, '2026-04-12 03:51:55', '2026-04-12 03:51:55', NULL),
(24, NULL, 'test', 'test2@gmail.com', '$2y$12$nBfeyPUyhtYPBk2YQ1csc.3a32x2Y3AGC5GLxXOn9P4gIwWm7NINu', NULL, NULL, NULL, 1, NULL, '2026-04-12 04:02:26', '2026-04-12 04:02:26', NULL),
(25, NULL, 'test', 'test6@gmail.com', '$2y$12$rh1jeOYhN.PwUsXlA9vSoukP6UF3TXrFhHPj6jXxexwbRRSEvxG1G', NULL, NULL, NULL, 1, NULL, '2026-04-12 04:03:09', '2026-04-12 04:03:09', NULL),
(26, NULL, 'test', 'test67@gmail.com', '$2y$12$YCF3skLkTO8F/D1g8n2dmOmq2B9OoVwq1DAs1xn2yBJcEIVkFS9cy', NULL, NULL, NULL, 1, NULL, '2026-04-12 04:04:47', '2026-04-12 04:04:47', NULL),
(27, NULL, 'test', 'test07@gmail.com', '$2y$12$SrLeG86cNAJYN09s5w1Ua.En44PbxQwrKdsvgTcCcoZmhfYXVwTk.', NULL, NULL, NULL, 1, NULL, '2026-04-12 04:08:00', '2026-04-12 04:08:00', NULL),
(28, NULL, 'test', 'test007@gmail.com', '$2y$12$EPdLIySTc50EtJWwFz/M7ucaoulW9BNRsftqNlAknqGZr1dDcgDwW', NULL, NULL, NULL, 1, NULL, '2026-04-12 04:10:01', '2026-04-12 04:10:01', NULL),
(29, NULL, 'test', 'test005@gmail.com', '$2y$12$d93XW9xyF2exPVU8wO2JKOVlIdjlyNPKhIez98VS4tLOu88x8Fv4S', NULL, NULL, NULL, 1, NULL, '2026-04-12 04:15:58', '2026-04-12 04:15:58', NULL),
(31, NULL, 'test', NULL, '$2y$12$ba1xuwVw4vrGleXRdSHeG.FNQ6lEKuF4aXHCE2GKlSoXn/aXQNOlG', NULL, NULL, NULL, 1, NULL, '2026-04-17 00:03:56', '2026-04-17 00:03:56', NULL),
(32, NULL, 'test', NULL, '$2y$12$NTso.JU7ugFCPtcnWBe7D.Bs2Il26qmPwV19qBLXlUgnUwT2OXPp2', NULL, NULL, NULL, 1, NULL, '2026-04-17 01:17:18', '2026-04-17 01:17:18', NULL),
(33, NULL, 'test', NULL, '$2y$12$2dh34Vvfv153UaytdBJMXeGYjXNgW5BHAd9ezO6Oz3AsrWMdX64H2', NULL, NULL, NULL, 1, NULL, '2026-04-17 01:17:30', '2026-04-17 01:17:30', NULL),
(34, NULL, 'test', NULL, '$2y$12$M75h7/amFuUvXtJnk/VV3eFmzSKnJvykEUBtB6Wh3Yie9R89yC07G', NULL, NULL, NULL, 1, NULL, '2026-04-17 01:31:23', '2026-04-17 01:31:23', NULL),
(35, NULL, 'Junaid', NULL, '$2y$12$ucZi6Idq50tOdLGEgtw5pOrQFq0uaV.ekNi4umiCxtrzyuIC4mR2y', NULL, NULL, NULL, 1, NULL, '2026-05-10 07:15:42', '2026-05-10 07:15:42', NULL),
(36, NULL, 'Ramees', NULL, '$2y$12$sb87okrEm/FGxRhcHE6FSOBJd3Suj.r5Hh33EtVcczn.zTIkAIv5a', NULL, NULL, NULL, 1, NULL, '2026-05-10 10:33:21', '2026-05-10 10:33:21', NULL),
(37, NULL, 'Meera', 'meera@example.com', '$2y$12$Jp7fhRo1ohoCHA3fPyz/7.UE/leeh3LzsI9lKGEX0wLIWsEPeRAZW', NULL, NULL, NULL, 1, NULL, '2026-05-10 10:48:11', '2026-05-10 10:48:11', NULL),
(38, NULL, 'Arjun', 'arjun@example.com', '$2y$12$AZ2yanTmOALxG6uoqXhvxOk50ZxqCDUYu3mqAdkZI0e6R9qsPczoa', NULL, NULL, NULL, 1, NULL, '2026-05-10 10:50:47', '2026-05-10 10:50:47', NULL),
(39, NULL, 'Arjun', NULL, '$2y$12$1w448DDi42caXjK65fncN.deFz1ZTVi35WWSv/wCnFzvzRqqeNBuC', NULL, NULL, NULL, 1, NULL, '2026-05-10 10:53:04', '2026-05-10 10:53:04', NULL),
(40, NULL, 'Arjun', NULL, '$2y$12$5CD2xr5Y0MDmny1QdK10kukx/kqrWmISxdgoRTj68DMT0R9nu6Z.G', NULL, NULL, NULL, 1, NULL, '2026-05-10 10:53:53', '2026-05-10 10:53:53', NULL),
(41, NULL, 'anfas', NULL, '$2y$12$bdMVxOU.tE2CzGF1/02jbuA78Fn7gtLe1/txyLKs0IsOa0/BYdtWe', NULL, NULL, NULL, 1, NULL, '2026-06-04 14:53:58', '2026-06-04 14:53:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_no` varchar(255) NOT NULL,
  `vehicle_model` varchar(255) DEFAULT NULL,
  `driver_name` varchar(255) DEFAULT NULL,
  `driver_license` varchar(255) DEFAULT NULL,
  `driver_contact` varchar(255) DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `youtube_link` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `uploaded_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admission_queries`
--
ALTER TABLE `admission_queries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignments_class_id_foreign` (`class_id`),
  ADD KEY `assignments_section_id_foreign` (`section_id`),
  ADD KEY `assignments_subject_id_foreign` (`subject_id`),
  ADD KEY `assignments_created_by_foreign` (`created_by`);

--
-- Indexes for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignment_submissions_assignment_id_foreign` (`assignment_id`),
  ADD KEY `assignment_submissions_student_id_foreign` (`student_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `bulk_print_logs`
--
ALTER TABLE `bulk_print_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bulk_print_logs_generated_by_foreign` (`generated_by`);

--
-- Indexes for table `certificate_templates`
--
ALTER TABLE `certificate_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `classes_branch_id_foreign` (`branch_id`),
  ADD KEY `classes_academic_year_id_foreign` (`academic_year_id`);

--
-- Indexes for table `class_rooms`
--
ALTER TABLE `class_rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_routines`
--
ALTER TABLE `class_routines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_routines_class_id_foreign` (`class_id`),
  ADD KEY `class_routines_section_id_foreign` (`section_id`),
  ADD KEY `class_routines_subject_id_foreign` (`subject_id`),
  ADD KEY `class_routines_room_id_foreign` (`room_id`),
  ADD KEY `class_routines_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `class_subjects`
--
ALTER TABLE `class_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_subjects_class_id_foreign` (`class_id`),
  ADD KEY `class_subjects_subject_id_foreign` (`subject_id`),
  ADD KEY `class_subjects_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `class_teachers`
--
ALTER TABLE `class_teachers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_teachers_class_id_foreign` (`class_id`),
  ADD KEY `class_teachers_section_id_foreign` (`section_id`),
  ADD KEY `class_teachers_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dormitories`
--
ALTER TABLE `dormitories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dormitories_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `dormitory_rooms`
--
ALTER TABLE `dormitory_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dormitory_rooms_dormitory_id_foreign` (`dormitory_id`),
  ADD KEY `dormitory_rooms_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `download_contents`
--
ALTER TABLE `download_contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `download_contents_type_id_foreign` (`type_id`),
  ADD KEY `download_contents_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `download_content_types`
--
ALTER TABLE `download_content_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exams_academic_year_id_foreign` (`academic_year_id`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_results_student_id_foreign` (`student_id`);

--
-- Indexes for table `exam_schedules`
--
ALTER TABLE `exam_schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fee_groups`
--
ALTER TABLE `fee_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fee_structures`
--
ALTER TABLE `fee_structures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fee_types`
--
ALTER TABLE `fee_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `files_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `generated_certificates`
--
ALTER TABLE `generated_certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `generated_certificates_student_id_foreign` (`student_id`),
  ADD KEY `generated_certificates_certificate_template_id_foreign` (`certificate_template_id`);

--
-- Indexes for table `generated_id_cards`
--
ALTER TABLE `generated_id_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `generated_id_cards_student_id_foreign` (`student_id`),
  ADD KEY `generated_id_cards_template_id_foreign` (`template_id`);

--
-- Indexes for table `id_card_templates`
--
ALTER TABLE `id_card_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_sender_id_foreign` (`sender_id`),
  ADD KEY `messages_receiver_id_foreign` (`receiver_id`);

--
-- Indexes for table `message_threads`
--
ALTER TABLE `message_threads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_threads_created_by_foreign` (`created_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `optional_subjects`
--
ALTER TABLE `optional_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `optional_subjects_subject_id_foreign` (`subject_id`),
  ADD KEY `optional_subjects_class_id_foreign` (`class_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `phone_call_logs`
--
ALTER TABLE `phone_call_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `postal_dispatches`
--
ALTER TABLE `postal_dispatches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `postal_receives`
--
ALTER TABLE `postal_receives`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `routes_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `school_classes`
--
ALTER TABLE `school_classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sections_class_id_foreign` (`class_id`);

--
-- Indexes for table `shared_contents`
--
ALTER TABLE `shared_contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shared_contents_content_id_foreign` (`content_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_attendances`
--
ALTER TABLE `staff_attendances`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_departments`
--
ALTER TABLE `staff_departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_admission_no_unique` (`admission_no`),
  ADD KEY `students_category_id_foreign` (`category_id`),
  ADD KEY `students_user_id_foreign` (`user_id`),
  ADD KEY `students_class_id_foreign` (`class_id`),
  ADD KEY `students_section_id_foreign` (`section_id`),
  ADD KEY `students_student_group_id_foreign` (`student_group_id`),
  ADD KEY `students_route_id_foreign` (`route_id`),
  ADD KEY `students_vehicle_id_foreign` (`vehicle_id`),
  ADD KEY `students_dormitory_id_foreign` (`dormitory_id`),
  ADD KEY `students_room_id_foreign` (`room_id`);

--
-- Indexes for table `student_academics`
--
ALTER TABLE `student_academics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_academics_student_id_foreign` (`student_id`),
  ADD KEY `student_academics_class_id_foreign` (`class_id`),
  ADD KEY `student_academics_section_id_foreign` (`section_id`),
  ADD KEY `student_academics_academic_year_id_foreign` (`academic_year_id`);

--
-- Indexes for table `student_attendances`
--
ALTER TABLE `student_attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_attendances_student_id_foreign` (`student_id`);

--
-- Indexes for table `student_categories`
--
ALTER TABLE `student_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_documents`
--
ALTER TABLE `student_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_documents_student_id_foreign` (`student_id`);

--
-- Indexes for table `student_emergency_contacts`
--
ALTER TABLE `student_emergency_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_emergency_contacts_student_id_foreign` (`student_id`);

--
-- Indexes for table `student_fees`
--
ALTER TABLE `student_fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_fees_student_id_foreign` (`student_id`);

--
-- Indexes for table `student_groups`
--
ALTER TABLE `student_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_groups_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `student_multi_class`
--
ALTER TABLE `student_multi_class`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_multi_class_student_id_foreign` (`student_id`),
  ADD KEY `student_multi_class_class_id_foreign` (`class_id`),
  ADD KEY `student_multi_class_section_id_foreign` (`section_id`);

--
-- Indexes for table `student_multi_classes`
--
ALTER TABLE `student_multi_classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_multi_classes_student_id_foreign` (`student_id`);

--
-- Indexes for table `student_parents`
--
ALTER TABLE `student_parents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_parents_student_id_foreign` (`student_id`);

--
-- Indexes for table `study_materials`
--
ALTER TABLE `study_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `study_materials_class_id_foreign` (`class_id`),
  ADD KEY `study_materials_section_id_foreign` (`section_id`),
  ADD KEY `study_materials_subject_id_foreign` (`subject_id`),
  ADD KEY `study_materials_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timetables`
--
ALTER TABLE `timetables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `todos`
--
ALTER TABLE `todos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `todos_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_branch` (`branch_id`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_profiles_user_id_foreign` (`user_id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicles_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `videos_uploaded_by_foreign` (`uploaded_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `admission_queries`
--
ALTER TABLE `admission_queries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bulk_print_logs`
--
ALTER TABLE `bulk_print_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificate_templates`
--
ALTER TABLE `certificate_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `class_rooms`
--
ALTER TABLE `class_rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_routines`
--
ALTER TABLE `class_routines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_subjects`
--
ALTER TABLE `class_subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `class_teachers`
--
ALTER TABLE `class_teachers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dormitories`
--
ALTER TABLE `dormitories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dormitory_rooms`
--
ALTER TABLE `dormitory_rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `download_contents`
--
ALTER TABLE `download_contents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `download_content_types`
--
ALTER TABLE `download_content_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_schedules`
--
ALTER TABLE `exam_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fee_groups`
--
ALTER TABLE `fee_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fee_structures`
--
ALTER TABLE `fee_structures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fee_types`
--
ALTER TABLE `fee_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `generated_certificates`
--
ALTER TABLE `generated_certificates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `generated_id_cards`
--
ALTER TABLE `generated_id_cards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `id_card_templates`
--
ALTER TABLE `id_card_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `message_threads`
--
ALTER TABLE `message_threads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `optional_subjects`
--
ALTER TABLE `optional_subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `phone_call_logs`
--
ALTER TABLE `phone_call_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `postal_dispatches`
--
ALTER TABLE `postal_dispatches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `postal_receives`
--
ALTER TABLE `postal_receives`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `school_classes`
--
ALTER TABLE `school_classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `shared_contents`
--
ALTER TABLE `shared_contents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `staff_attendances`
--
ALTER TABLE `staff_attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_departments`
--
ALTER TABLE `staff_departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `student_academics`
--
ALTER TABLE `student_academics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `student_attendances`
--
ALTER TABLE `student_attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_categories`
--
ALTER TABLE `student_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_documents`
--
ALTER TABLE `student_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_emergency_contacts`
--
ALTER TABLE `student_emergency_contacts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_fees`
--
ALTER TABLE `student_fees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_groups`
--
ALTER TABLE `student_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_multi_class`
--
ALTER TABLE `student_multi_class`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_multi_classes`
--
ALTER TABLE `student_multi_classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_parents`
--
ALTER TABLE `student_parents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `study_materials`
--
ALTER TABLE `study_materials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `timetables`
--
ALTER TABLE `timetables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `todos`
--
ALTER TABLE `todos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `assignments_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD CONSTRAINT `assignment_submissions_assignment_id_foreign` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignment_submissions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bulk_print_logs`
--
ALTER TABLE `bulk_print_logs`
  ADD CONSTRAINT `bulk_print_logs_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
  ADD CONSTRAINT `classes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`);

--
-- Constraints for table `class_routines`
--
ALTER TABLE `class_routines`
  ADD CONSTRAINT `class_routines_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `class_routines_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `class_rooms` (`id`),
  ADD CONSTRAINT `class_routines_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`),
  ADD CONSTRAINT `class_routines_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `class_routines_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_subjects`
--
ALTER TABLE `class_subjects`
  ADD CONSTRAINT `class_subjects_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `class_subjects_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `class_subjects_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_teachers`
--
ALTER TABLE `class_teachers`
  ADD CONSTRAINT `class_teachers_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `class_teachers_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`),
  ADD CONSTRAINT `class_teachers_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dormitories`
--
ALTER TABLE `dormitories`
  ADD CONSTRAINT `dormitories_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dormitory_rooms`
--
ALTER TABLE `dormitory_rooms`
  ADD CONSTRAINT `dormitory_rooms_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dormitory_rooms_dormitory_id_foreign` FOREIGN KEY (`dormitory_id`) REFERENCES `dormitories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `download_contents`
--
ALTER TABLE `download_contents`
  ADD CONSTRAINT `download_contents_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `download_content_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `download_contents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD CONSTRAINT `exam_results_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `generated_certificates`
--
ALTER TABLE `generated_certificates`
  ADD CONSTRAINT `generated_certificates_certificate_template_id_foreign` FOREIGN KEY (`certificate_template_id`) REFERENCES `certificate_templates` (`id`),
  ADD CONSTRAINT `generated_certificates_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `generated_id_cards`
--
ALTER TABLE `generated_id_cards`
  ADD CONSTRAINT `generated_id_cards_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `generated_id_cards_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `id_card_templates` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `message_threads`
--
ALTER TABLE `message_threads`
  ADD CONSTRAINT `message_threads_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `optional_subjects`
--
ALTER TABLE `optional_subjects`
  ADD CONSTRAINT `optional_subjects_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `optional_subjects_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `routes`
--
ALTER TABLE `routes`
  ADD CONSTRAINT `routes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shared_contents`
--
ALTER TABLE `shared_contents`
  ADD CONSTRAINT `shared_contents_content_id_foreign` FOREIGN KEY (`content_id`) REFERENCES `download_contents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `student_categories` (`id`),
  ADD CONSTRAINT `students_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_dormitory_id_foreign` FOREIGN KEY (`dormitory_id`) REFERENCES `dormitories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `dormitory_rooms` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_student_group_id_foreign` FOREIGN KEY (`student_group_id`) REFERENCES `student_groups` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `student_academics`
--
ALTER TABLE `student_academics`
  ADD CONSTRAINT `student_academics_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
  ADD CONSTRAINT `student_academics_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `student_academics_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`),
  ADD CONSTRAINT `student_academics_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_attendances`
--
ALTER TABLE `student_attendances`
  ADD CONSTRAINT `student_attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_documents`
--
ALTER TABLE `student_documents`
  ADD CONSTRAINT `student_documents_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_emergency_contacts`
--
ALTER TABLE `student_emergency_contacts`
  ADD CONSTRAINT `student_emergency_contacts_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_fees`
--
ALTER TABLE `student_fees`
  ADD CONSTRAINT `student_fees_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_groups`
--
ALTER TABLE `student_groups`
  ADD CONSTRAINT `student_groups_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_multi_class`
--
ALTER TABLE `student_multi_class`
  ADD CONSTRAINT `student_multi_class_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `student_multi_class_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`),
  ADD CONSTRAINT `student_multi_class_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `student_multi_classes`
--
ALTER TABLE `student_multi_classes`
  ADD CONSTRAINT `student_multi_classes_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_parents`
--
ALTER TABLE `student_parents`
  ADD CONSTRAINT `student_parents_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `study_materials`
--
ALTER TABLE `study_materials`
  ADD CONSTRAINT `study_materials_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `study_materials_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`),
  ADD CONSTRAINT `study_materials_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `study_materials_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `todos`
--
ALTER TABLE `todos`
  ADD CONSTRAINT `todos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 02, 2026 at 12:45 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lead_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `advertisements`
--

CREATE TABLE `advertisements` (
  `id` int(11) NOT NULL,
  `software_name` text NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `badge_text` varchar(100) DEFAULT NULL,
  `media` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text DEFAULT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pricing` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `footer_note` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_time` timestamp NULL DEFAULT NULL,
  `start_location` text DEFAULT NULL,
  `end_location` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_types`
--

CREATE TABLE `attendance_types` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `hours` decimal(4,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_types`
--

INSERT INTO `attendance_types` (`id`, `type`, `hours`, `created_at`, `updated_at`) VALUES
(1, 'full day', 8.00, '2025-08-13 23:17:59', '2025-08-16 06:05:31'),
(2, 'half day', 6.00, '2025-08-13 23:19:58', '2025-08-29 04:24:54'),
(4, 'absent', 0.00, '2025-08-13 23:20:32', '2025-08-13 23:20:32');

-- --------------------------------------------------------

--
-- Table structure for table `campaigns`
--

CREATE TABLE `campaigns` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `created_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `campaigns`
--

INSERT INTO `campaigns` (`id`, `name`, `created_date`) VALUES
(1173, 'asdfa', '2025-11-27 15:57:26'),
(1174, 'Testing', '2026-01-30 16:38:49'),
(1175, 'tesingdslgiasdof', '2026-01-30 16:38:56'),
(1176, 'asdfasdf', '2026-01-30 16:41:46'),
(1177, 'adfasdfa', '2026-01-30 16:41:51'),
(1178, 'asdfadfasdfadf', '2026-01-30 16:42:02'),
(1179, 'asfdasdf', '2026-01-30 16:42:29'),
(1180, 'adfadf', '2026-01-30 16:42:33'),
(1181, 'asdfasdfa', '2026-01-30 16:42:38'),
(1182, 'asdfadfa', '2026-01-30 16:42:46'),
(1183, 'asdfadsga', '2026-01-30 16:42:53');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` bigint(20) NOT NULL,
  `name` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `created_at`, `updated_at`) VALUES
(6, 'asdf', '2025-11-27 10:26:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `checklist`
--

CREATE TABLE `checklist` (
  `id` int(11) NOT NULL,
  `type` enum('buyer','seller','common','post_sale') NOT NULL DEFAULT 'common',
  `name` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conversions`
--

CREATE TABLE `conversions` (
  `id` int(11) NOT NULL,
  `lead_id` int(11) NOT NULL,
  `status` enum('booked','completed','cancelled') NOT NULL,
  `remarks` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `tm_id` int(11) NOT NULL DEFAULT 0,
  `project_name` text DEFAULT NULL,
  `type` text DEFAULT NULL,
  `category` text DEFAULT NULL,
  `sub_category` text DEFAULT NULL,
  `size` text DEFAULT NULL,
  `final_price` decimal(10,2) NOT NULL,
  `name` text DEFAULT NULL,
  `phone` text DEFAULT NULL,
  `city` text DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `doa` date DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_date` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dayend_reports`
--

CREATE TABLE `dayend_reports` (
  `id` int(11) NOT NULL,
  `report_date` date NOT NULL,
  `agent_id` int(11) NOT NULL,
  `pending_followups` int(11) DEFAULT 0,
  `total_allocated_leads` int(11) DEFAULT 0,
  `total_added_leads` int(11) DEFAULT 0,
  `visit_done` int(11) DEFAULT 0,
  `converted_leads` int(11) DEFAULT 0,
  `completed_leads` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `designation`
--

CREATE TABLE `designation` (
  `id` int(11) NOT NULL,
  `designation` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `designation`
--

INSERT INTO `designation` (`id`, `designation`, `created_at`) VALUES
(4, 'adsfads', '2026-01-16 10:36:46');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `comments` text NOT NULL,
  `exp_date` date NOT NULL,
  `status` enum('pending','accepted','rejected','clear') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_img`
--

CREATE TABLE `expense_img` (
  `id` int(11) NOT NULL,
  `expense_id` int(11) NOT NULL,
  `img_url` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `facebook_sync_tracking`
--

CREATE TABLE `facebook_sync_tracking` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_id` varchar(255) NOT NULL,
  `last_synced_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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

--
-- Dumping data for table `failed_jobs`
--

INSERT INTO `failed_jobs` (`id`, `uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at`) VALUES
(1, '6e0de97e-2e37-44ba-814a-ec60a5ee65a6', 'database', 'default', '{\"uuid\":\"6e0de97e-2e37-44ba-814a-ec60a5ee65a6\",\"displayName\":\"App\\\\Jobs\\\\SyncFacebookLeads\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SyncFacebookLeads\",\"command\":\"O:26:\\\"App\\\\Jobs\\\\SyncFacebookLeads\\\":0:{}\"}}', 'Illuminate\\Queue\\MaxAttemptsExceededException: App\\Jobs\\SyncFacebookLeads has been attempted too many times or run too long. The job may have previously timed out. in C:\\xampp\\htdocs\\lead-management\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php:746\nStack trace:\n#0 C:\\xampp\\htdocs\\lead-management\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(505): Illuminate\\Queue\\Worker->maxAttemptsExceededException(Object(Illuminate\\Queue\\Jobs\\DatabaseJob))\n#1 C:\\xampp\\htdocs\\lead-management\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(414): Illuminate\\Queue\\Worker->markJobAsFailedIfAlreadyExceedsMaxAttempts(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), 1)\n#2 C:\\xampp\\htdocs\\lead-management\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(375): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#3 C:\\xampp\\htdocs\\lead-management\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(173): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#4 C:\\xampp\\htdocs\\lead-management\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(147): Illuminate\\Queue\\Worker->daemon(\'database\', \'default\', Object(Illuminate\\Queue\\WorkerOptions))\n#5 C:\\xampp\\htdocs\\lead-management\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(130): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'default\')\n#6 C:\\xampp\\htdocs\\lead-management\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#7 C:\\xampp\\htdocs\\lead-management\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#8 C:\\xampp\\htdocs\\lead-management\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#9 C:\\xampp\\htdocs\\lead-management\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#10 C:\\xampp\\htdocs\\lead-management\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(661): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#11 C:\\xampp\\htdocs\\lead-management\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(183): Illuminate\\Container\\Container->call(Array)\n#12 C:\\xampp\\htdocs\\lead-management\\vendor\\symfony\\console\\Command\\Command.php(326): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#13 C:\\xampp\\htdocs\\lead-management\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(152): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#14 C:\\xampp\\htdocs\\lead-management\\vendor\\symfony\\console\\Application.php(1078): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#15 C:\\xampp\\htdocs\\lead-management\\vendor\\symfony\\console\\Application.php(324): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#16 C:\\xampp\\htdocs\\lead-management\\vendor\\symfony\\console\\Application.php(175): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#17 C:\\xampp\\htdocs\\lead-management\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Application.php(102): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#18 C:\\xampp\\htdocs\\lead-management\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(155): Illuminate\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#19 C:\\xampp\\htdocs\\lead-management\\artisan(35): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#20 {main}', '2025-09-22 05:33:26');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `software_name` varchar(255) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `software_name`, `question`, `answer`, `created_at`, `updated_at`) VALUES
(1, 'ProLead', 'How do I reset my password?', 'Go to Settings > Account > Reset Password.', '2025-10-15 12:55:19', '2025-10-15 12:55:19'),
(2, 'ProLead', 'Can I import leads from Excel?', 'Yes, use the Import Leads feature under Leads section.', '2025-10-15 12:55:19', '2025-10-15 12:55:19'),
(3, 'ProLead', 'Is there a mobile app available?', 'Yes, the mobile app is available for Android and iOS.', '2025-10-15 12:55:19', '2025-10-15 12:55:19'),
(4, 'ProLead', 'How do I upgrade my plan?', 'Navigate to Billing > Upgrade Plan and select your desired plan.', '2025-10-15 12:55:19', '2025-10-15 12:55:19'),
(5, 'ProLead', 'Can multiple users access the same account?', 'Yes, add users via Admin > Manage Users.', '2025-10-15 12:55:19', '2025-10-15 12:55:19'),
(6, 'ProLead', 'How can I export reports?', 'Reports can be exported as CSV or PDF from the Reports tab.', '2025-10-15 12:55:19', '2025-10-15 12:55:19'),
(7, 'ProLead', 'What browsers are supported?', 'ProLead supports Chrome, Firefox, Edge, and Safari.', '2025-10-15 12:55:19', '2025-10-15 12:55:19'),
(8, 'ProLead', 'Is data encrypted?', 'Yes, all data is encrypted in transit and at rest.', '2025-10-15 12:55:19', '2025-10-15 12:55:19'),
(9, 'ProLead', 'How do I delete a lead?', 'Open the lead detail page and click Delete.', '2025-10-15 12:55:19', '2025-10-15 12:55:19'),
(10, 'ProLead', 'Can I customize dashboards?', 'Yes, dashboards can be customized via the Dashboard Settings.', '2025-10-15 12:55:19', '2025-10-15 12:55:19');

-- --------------------------------------------------------

--
-- Table structure for table `inquiry_questions`
--

CREATE TABLE `inquiry_questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `question_text` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inquiry_questions`
--

INSERT INTO `inquiry_questions` (`id`, `user_id`, `question_text`, `is_active`, `created_at`, `updated_at`) VALUES
(3, NULL, 'What is the price of the property?', 1, '2025-08-27 04:27:37', '2025-08-27 04:37:32'),
(4, NULL, 'Is the property still available?', 1, '2025-08-27 04:37:39', '2025-08-27 04:37:39'),
(5, NULL, 'Can I schedule a visit to the property?', 1, '2025-08-27 04:37:47', '2025-08-27 04:37:47'),
(6, NULL, 'What are the financing options available?', 1, '2025-08-27 04:37:53', '2025-08-27 04:37:53'),
(7, NULL, 'Are there any additional fees or taxes?', 1, '2025-08-27 04:37:59', '2025-08-27 04:37:59'),
(8, NULL, 'What is the condition of the property?', 1, '2025-08-27 04:38:04', '2025-08-27 04:38:04'),
(9, NULL, 'How old is the property?', 1, '2025-08-27 04:38:11', '2025-08-27 04:38:11'),
(10, NULL, 'Are pets allowed?', 1, '2025-08-27 04:38:18', '2025-08-27 04:38:18'),
(11, NULL, 'What is the neighborhood like?', 1, '2025-08-27 04:38:24', '2025-08-27 04:38:24'),
(12, NULL, 'What are the nearby amenities (schools, hospitals, parks)?', 1, '2025-08-27 04:38:29', '2025-08-27 04:38:29'),
(13, NULL, 'Is negotiation on price possible?', 1, '2025-08-27 04:38:37', '2025-08-27 04:38:37'),
(14, NULL, 'What is the history of ownership?', 1, '2025-08-27 04:38:42', '2025-08-27 04:38:42'),
(15, NULL, 'Are there any restrictions or zoning laws?', 1, '2025-08-27 04:38:48', '2025-08-27 04:38:48'),
(16, NULL, 'What is the size of the property?', 1, '2025-08-27 04:38:53', '2025-08-27 04:38:53'),
(17, NULL, 'Is the property furnished?', 1, '2025-08-27 04:39:00', '2025-08-27 04:39:00'),
(18, NULL, 'How soon can I move in?', 1, '2025-08-27 04:39:07', '2025-08-27 04:39:07'),
(19, NULL, 'Are there any ongoing maintenance or HOA fees?', 1, '2025-08-27 04:39:12', '2025-08-27 04:39:12'),
(20, NULL, 'Can I get a virtual tour of the property?', 0, '2025-08-27 04:39:18', '2025-08-29 04:24:28');

-- --------------------------------------------------------

--
-- Table structure for table `integration_settings`
--

CREATE TABLE `integration_settings` (
  `id` int(11) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `integration_type` varchar(250) NOT NULL,
  `settings` longtext NOT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `auto_sync` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_det`
--

CREATE TABLE `inventory_det` (
  `id` int(11) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `unit_no` text DEFAULT NULL,
  `size` text DEFAULT NULL,
  `status` enum('pending','cancel','hold','sold') NOT NULL DEFAULT 'pending',
  `sales_person_id` int(11) NOT NULL DEFAULT 0,
  `name` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `number` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_det`
--

INSERT INTO `inventory_det` (`id`, `inventory_id`, `unit_no`, `size`, `status`, `sales_person_id`, `name`, `email`, `number`, `created_at`, `updated_at`) VALUES
(16, 4, '234', 'adsf', 'pending', 0, NULL, NULL, NULL, '2026-01-30 10:59:24', '2026-01-30 10:59:24');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_history`
--

CREATE TABLE `inventory_history` (
  `id` int(11) NOT NULL,
  `inventory_det_id` int(11) NOT NULL,
  `status` enum('pending','hold','sold','cancel') NOT NULL DEFAULT 'pending',
  `sales_person_id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `number` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inv_catg`
--

CREATE TABLE `inv_catg` (
  `id` int(11) NOT NULL,
  `type` text NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `inv_catg`
--

INSERT INTO `inv_catg` (`id`, `type`, `name`) VALUES
(8, 'asdf', 'Skyline Edge');

-- --------------------------------------------------------

--
-- Table structure for table `inv_subcatg`
--

CREATE TABLE `inv_subcatg` (
  `id` int(11) NOT NULL,
  `catg_id` int(11) NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `inv_subcatg`
--

INSERT INTO `inv_subcatg` (`id`, `catg_id`, `name`) VALUES
(6, 8, 'asdf'),
(7, 8, 'asdfas'),
(8, 8, 'adfa'),
(9, 8, 'asdfad'),
(10, 8, 'asdfadsf'),
(11, 8, 'adsfads'),
(12, 8, 'asdfas'),
(13, 8, 'adsfasd'),
(14, 8, 'asdfasdf'),
(15, 8, 'asdfasdf'),
(16, 8, 'asdfasdf'),
(17, 8, 'asdfasdf'),
(18, 8, 'asdfasd'),
(19, 8, 'asdfads'),
(20, 8, 'adsfadf');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(36, 'default', '{\"uuid\":\"20fcd346-f5a1-46f2-a751-a39f3cb16803\",\"displayName\":\"App\\\\Jobs\\\\SyncFacebookLeads\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SyncFacebookLeads\",\"command\":\"O:26:\\\"App\\\\Jobs\\\\SyncFacebookLeads\\\":0:{}\"}}', 1, 1758519207, 1756989743, 1756989743),
(37, 'default', '{\"uuid\":\"688d1819-8ab1-4e53-ac75-0b0ae52e2138\",\"displayName\":\"App\\\\Jobs\\\\SyncFacebookLeads\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SyncFacebookLeads\",\"command\":\"O:26:\\\"App\\\\Jobs\\\\SyncFacebookLeads\\\":0:{}\"}}', 0, NULL, 1757047437, 1757047437),
(38, 'default', '{\"uuid\":\"d544248f-bfde-43ab-9b4f-3ced05932d6b\",\"displayName\":\"App\\\\Jobs\\\\SyncFacebookLeads\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SyncFacebookLeads\",\"command\":\"O:26:\\\"App\\\\Jobs\\\\SyncFacebookLeads\\\":0:{}\"}}', 0, NULL, 1757047515, 1757047515),
(39, 'default', '{\"uuid\":\"0143b616-4538-4c51-b9af-a9966abc0999\",\"displayName\":\"App\\\\Jobs\\\\SyncFacebookLeads\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SyncFacebookLeads\",\"command\":\"O:26:\\\"App\\\\Jobs\\\\SyncFacebookLeads\\\":0:{}\"}}', 0, NULL, 1758519280, 1758519280);

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `source` text DEFAULT NULL,
  `campaign` text DEFAULT NULL,
  `stage_id` bigint(20) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `classification` varchar(255) DEFAULT NULL,
  `field1` text DEFAULT NULL,
  `field2` text DEFAULT NULL,
  `field3` text DEFAULT NULL,
  `field4` text DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `unallocated_lead` bigint(20) NOT NULL DEFAULT 0,
  `is_allocated` bigint(11) NOT NULL DEFAULT 0,
  `lead_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_comment` text DEFAULT NULL,
  `remind_date` date DEFAULT NULL,
  `remind_time` time DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `updated_date` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `allocated_date` datetime DEFAULT NULL,
  `is_interested_allocated` bit(1) NOT NULL DEFAULT b'0',
  `projects` text DEFAULT NULL,
  `app_city` text DEFAULT NULL,
  `app_contact` text DEFAULT NULL,
  `app_doa` date DEFAULT NULL,
  `app_dob` date DEFAULT NULL,
  `app_name` text DEFAULT NULL,
  `budget` text DEFAULT NULL,
  `catg_id` int(11) DEFAULT NULL,
  `conversion_type` varchar(255) DEFAULT NULL,
  `final_price` varchar(255) DEFAULT NULL,
  `project_id` text DEFAULT NULL,
  `size` varchar(255) DEFAULT NULL,
  `sub_catg_id` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `lead_shared_with` text DEFAULT NULL,
  `whatsapp_no` text DEFAULT NULL,
  `checklist_status` enum('open','close') NOT NULL DEFAULT 'open',
  `inquiry_question_id` bigint(20) DEFAULT NULL,
  `visited_on` tinyint(1) NOT NULL DEFAULT 0,
  `is_pinned` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `name`, `email`, `phone`, `notes`, `source`, `campaign`, `stage_id`, `customer_id`, `classification`, `field1`, `field2`, `field3`, `field4`, `status`, `unallocated_lead`, `is_allocated`, `lead_date`, `last_comment`, `remind_date`, `remind_time`, `user_id`, `updated_date`, `allocated_date`, `is_interested_allocated`, `projects`, `app_city`, `app_contact`, `app_doa`, `app_dob`, `app_name`, `budget`, `catg_id`, `conversion_type`, `final_price`, `project_id`, `size`, `sub_catg_id`, `type`, `lead_shared_with`, `whatsapp_no`, `checklist_status`, `inquiry_question_id`, `visited_on`, `is_pinned`, `created_at`, `updated_at`) VALUES
(139307, 'Skyline Edge', NULL, '7060702032', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'FUTURE LEAD', 0, 1, '2025-11-10 14:28:12', 'Status changed to FUTURE LEAD', '2026-02-03', '18:20:00', 1, '2026-02-02 15:17:18', '2025-11-10 14:28:23', b'0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, '4,5', NULL, NULL, NULL, '15,17', NULL, 'open', NULL, 1, 1, '2025-11-10 08:58:12', '2026-02-02 11:00:52'),
(139310, 'adsfa', NULL, '8171620118', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'allocated_lead', 0, 0, '2025-11-27 15:06:03', NULL, NULL, NULL, 1, '2025-11-27 15:08:22', NULL, b'0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '4,5', NULL, NULL, NULL, NULL, NULL, 'open', NULL, 0, 0, '2025-11-27 09:36:03', '2025-11-27 15:08:22'),
(139311, 'saurabh singh', 'saurabhsingh@gmail.com', '9105665874', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CALL SCHEDULED', 0, 1, '2026-01-02 16:15:25', 'Status changed to CALL SCHEDULED', '2026-02-01', '14:40:00', 1, '2026-01-31 10:36:43', '2026-01-15 10:14:43', b'0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'open', NULL, 0, 0, '2026-01-02 10:45:25', NULL),
(139312, 'saurabh', NULL, '9410555274', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CALL SCHEDULED', 0, 1, '2026-01-05 15:49:51', 'Status changed to CALL SCHEDULED', '2026-01-31', '10:35:00', 15, '2026-01-31 10:49:13', '2026-01-15 10:14:24', b'0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'open', NULL, 0, 0, '2026-01-05 10:19:51', '2026-01-31 10:49:13'),
(139313, 'adsfasdf', 'saurabhrawat@clikzopinnovations.com', '6666666666', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, '2026-01-15 10:12:38', 'Lead Added', NULL, NULL, 25, '2026-01-15 10:12:49', '2026-01-15 10:12:49', b'0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'asdf', NULL, '6666666666', 'open', NULL, 0, 0, '2026-01-15 04:42:38', NULL),
(139314, 'Skyline Edge', 'saurabhrawat@clikzopinnovations.com', '7777777788', NULL, 'asdfa', 'asdfa', NULL, NULL, 'hot', NULL, NULL, NULL, NULL, 'LOST', 0, 1, '2026-01-15 10:26:11', 'Status changed to LOST', NULL, NULL, 1, '2026-02-02 11:58:21', '2026-01-31 12:07:52', b'0', NULL, NULL, NULL, NULL, NULL, NULL, '40k-50k', 8, NULL, NULL, NULL, NULL, 9, 'asdf', NULL, '3333333333', 'open', NULL, 0, 0, '2026-01-15 04:56:11', NULL),
(139315, 'asdf', 'R@gmail.com', '7777777777', NULL, 'asdfa', 'asdfa', NULL, NULL, 'hot', 'Arunachal Pradesh', 'Longding', 'Testing', NULL, 'VISIT DONE', 0, 1, '2026-01-30 10:38:24', 'Status changed to VISIT DONE | Project: asdfa', '2026-02-02', '18:22:00', 1, '2026-02-02 15:23:01', '2026-01-30 10:39:25', b'0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, NULL, NULL, '4,5', NULL, 6, 'asdf', '15,17,18,25', '5555555555', 'open', NULL, 1, 1, '2026-01-30 05:08:24', '2026-02-02 11:17:59'),
(139317, 'Saurabh testing 123', 'saurabhrawat123@gmail.com', '3434254512', NULL, NULL, NULL, NULL, NULL, NULL, 'Arunachal Pradesh', 'Lohit', 's', NULL, 'VISIT SCHEDULED', 0, 1, '2026-01-31 13:00:04', 'Status changed to VISIT SCHEDULED', '2026-01-31', '19:06:00', 1, '2026-01-31 13:25:29', '2026-01-31 13:21:31', b'0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, 'open', NULL, 0, 0, '2026-01-31 07:30:04', NULL),
(139318, 'Testing saurabh 123456', 'testing123456@gmail.com', '8745895678', NULL, 'asdfa', 'asdfa', NULL, NULL, 'hot', NULL, NULL, NULL, NULL, 'VISIT SCHEDULED', 0, 1, '2026-01-31 13:14:19', 'Status changed to VISIT SCHEDULED', '2026-01-31', '17:21:00', 1, '2026-01-31 13:20:43', '2026-01-31 13:16:38', b'0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, NULL, NULL, '', NULL, 6, 'asdf', NULL, NULL, 'open', NULL, 0, 0, '2026-01-31 07:44:19', NULL),
(139319, 'saurabh testing lead 12334', 'testing@gmail.com123123', '4578986554', NULL, 'asdfa', 'asdfa', NULL, NULL, 'hot', NULL, NULL, NULL, NULL, 'VISIT SCHEDULED', 0, 1, '2026-01-31 13:28:06', 'Status changed to VISIT SCHEDULED | Project: asdfa', '2026-01-31', '16:00:00', 1, '2026-01-31 14:37:34', '2026-01-31 13:28:56', b'0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, NULL, NULL, '', NULL, 6, 'asdf', NULL, NULL, 'open', NULL, 0, 0, '2026-01-31 07:58:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lead_comments`
--

CREATE TABLE `lead_comments` (
  `id` int(11) NOT NULL,
  `lead_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `status` varchar(255) NOT NULL,
  `remind_date` date DEFAULT NULL,
  `remind_time` time DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lead_comments`
--

INSERT INTO `lead_comments` (`id`, `lead_id`, `comment`, `status`, `remind_date`, `remind_time`, `user_id`, `created_date`) VALUES
(447, 139311, 'Lead Added', 'allocated_lead', NULL, NULL, 1, '2026-01-02 16:15:25'),
(448, 139312, 'Lead Added', 'allocated_lead', NULL, NULL, 1, '2026-01-05 15:49:51'),
(449, 139313, 'Lead Added', 'allocated_lead', NULL, NULL, 1, '2026-01-15 10:12:38'),
(450, 139313, 'Lead allocated to Ravinder Singh 33', 'ALLOCATED', NULL, NULL, 1, '2026-01-15 10:12:49'),
(451, 139312, 'Lead allocated to Testing account', 'ALLOCATED', NULL, NULL, 1, '2026-01-15 10:14:24'),
(452, 139311, 'Lead allocated to Testing account', 'ALLOCATED', NULL, NULL, 1, '2026-01-15 10:14:43'),
(453, 139314, 'Tet', 'allocated_lead', NULL, NULL, 1, '2026-01-15 10:26:11'),
(454, 139315, 'Lead Added', 'allocated_lead', NULL, NULL, 1, '2026-01-30 10:38:24'),
(455, 139315, 'Lead allocated to admin', 'ALLOCATED', NULL, NULL, 1, '2026-01-30 10:39:25'),
(456, 139315, 'Status changed to CALL SCHEDULED', 'CALL SCHEDULED', '2026-01-30', '12:00:00', 1, '2026-01-30 10:39:49'),
(457, 139315, 'Status changed to CALL SCHEDULED', 'CALL SCHEDULED', '2026-01-31', '10:00:00', 1, '2026-01-30 10:44:00'),
(458, 139311, 'Status changed to PENDING', 'PENDING', NULL, NULL, 1, '2026-01-30 10:59:03'),
(459, 139311, 'Status changed to PROCESSING', 'PROCESSING', NULL, NULL, 1, '2026-01-30 10:59:30'),
(460, 139311, 'Status changed to INTERESTED', 'INTERESTED', '2026-01-30', '14:03:00', 1, '2026-01-30 11:00:10'),
(461, 139311, 'Status changed to CALL SCHEDULED', 'CALL SCHEDULED', '2026-01-30', '14:03:00', 1, '2026-01-30 11:00:44'),
(462, 139315, 'Status changed to WHATSAPP', 'WHATSAPP', NULL, NULL, 1, '2026-01-30 11:01:16'),
(463, 139315, 'Status changed to MEETING SCHEDULED', 'MEETING SCHEDULED', '2026-01-30', '15:06:00', 1, '2026-01-30 11:02:24'),
(464, 139315, 'Status changed to VISIT SCHEDULED', 'VISIT SCHEDULED', '2026-01-30', '16:03:00', 1, '2026-01-30 11:03:12'),
(465, 139315, 'Status changed to VISIT SCHEDULED', 'VISIT SCHEDULED', '2026-01-30', '15:28:00', 1, '2026-01-30 11:24:15'),
(466, 139316, 'Lead Added', 'allocated_lead', NULL, NULL, 1, '2026-01-30 11:43:10'),
(467, 139316, 'Lead allocated to admin', 'ALLOCATED', NULL, NULL, 1, '2026-01-30 11:43:40'),
(468, 139307, 'Status changed to VISIT DONE', 'VISIT DONE', '2026-01-30', '18:54:00', 1, '2026-01-30 12:49:20'),
(469, 139307, 'Status changed to WRONG NUMBER', 'WRONG NUMBER', NULL, NULL, 1, '2026-01-30 12:49:48'),
(470, 139307, 'Status changed to BOOKED', 'BOOKED', NULL, NULL, 1, '2026-01-30 12:55:00'),
(471, 139307, 'Status changed to NOT PICKED', 'NOT PICKED', NULL, NULL, 1, '2026-01-30 13:23:31'),
(472, 139307, 'Status changed to CHANNEL PARTNER', 'CHANNEL PARTNER', NULL, NULL, 1, '2026-01-30 13:24:56'),
(473, 139307, 'Status changed to NOT REACHABLE', 'NOT REACHABLE', NULL, NULL, 1, '2026-01-30 13:25:38'),
(474, 139307, 'Status changed to FUTURE LEAD', 'FUTURE LEAD', NULL, NULL, 1, '2026-01-30 13:26:08'),
(475, 139307, 'Status changed to LOST', 'LOST', NULL, NULL, 1, '2026-01-30 13:27:15'),
(476, 139311, 'Test', 'MEETING SCHEDULED', '2026-01-31', '15:30:00', 1, '2026-01-30 13:28:43'),
(477, 139316, 'Status changed to PENDING', 'PENDING', '2026-01-30', '16:26:00', 1, '2026-01-30 16:26:19'),
(478, 139311, 'Status changed to LOST', 'LOST', NULL, NULL, 1, '2026-01-30 18:17:19'),
(479, 139312, 'Status changed to CALL SCHEDULED', 'CALL SCHEDULED', '2026-01-31', '10:35:00', 1, '2026-01-31 10:33:40'),
(480, 139316, 'Status changed to MEETING SCHEDULED', 'MEETING SCHEDULED', '2026-02-01', '14:38:00', 1, '2026-01-31 10:34:02'),
(481, 139307, 'Status changed to MEETING SCHEDULED', 'MEETING SCHEDULED', '2026-01-31', '13:39:00', 1, '2026-01-31 10:36:21'),
(482, 139311, 'Status changed to CALL SCHEDULED', 'CALL SCHEDULED', '2026-02-01', '14:40:00', 1, '2026-01-31 10:36:43'),
(483, 139314, 'Lead allocated to admin', 'ALLOCATED', NULL, NULL, 1, '2026-01-31 12:07:52'),
(484, 139314, 'Test', 'VISIT SCHEDULED', '2026-01-31', '15:32:00', 1, '2026-01-31 12:34:55'),
(485, 139317, 'Lead Added', 'allocated_lead', NULL, NULL, 1, '2026-01-31 13:00:04'),
(486, 139318, 'Lead Added', 'allocated_lead', NULL, NULL, 1, '2026-01-31 13:14:19'),
(487, 139318, 'Lead allocated to admin', 'ALLOCATED', NULL, NULL, 1, '2026-01-31 13:16:38'),
(488, 139318, 'Status changed to VISIT SCHEDULED', 'VISIT SCHEDULED', '2026-01-31', '17:21:00', 1, '2026-01-31 13:20:43'),
(489, 139317, 'Lead allocated to admin', 'ALLOCATED', NULL, NULL, 1, '2026-01-31 13:21:31'),
(490, 139317, 'Status changed to VISIT SCHEDULED', 'VISIT SCHEDULED', '2026-01-31', '19:06:00', 1, '2026-01-31 13:25:29'),
(491, 139319, 'Test', 'allocated_lead', NULL, NULL, 1, '2026-01-31 13:28:06'),
(492, 139319, 'Lead allocated to admin', 'ALLOCATED', NULL, NULL, 1, '2026-01-31 13:28:56'),
(493, 139319, 'Status changed to VISIT SCHEDULED | Project: asdfa', 'VISIT SCHEDULED', '2026-01-31', '16:00:00', 1, '2026-01-31 14:37:34'),
(508, 139307, 'Lead shared with Testing account', 'MEETING SCHEDULED', NULL, NULL, 1, '2026-02-02 11:00:52'),
(509, 139307, 'Lead shared with Skyline Edge', 'MEETING SCHEDULED', NULL, NULL, 1, '2026-02-02 11:00:52'),
(510, 139315, 'Lead shared with Testing account', 'VISIT SCHEDULED', NULL, NULL, 1, '2026-02-02 11:17:59'),
(511, 139315, 'Lead shared with Skyline Edge', 'VISIT SCHEDULED', NULL, NULL, 1, '2026-02-02 11:17:59'),
(512, 139315, 'Lead shared with saurabh talecaller', 'VISIT SCHEDULED', NULL, NULL, 1, '2026-02-02 11:17:59'),
(513, 139315, 'Lead shared with Ravinder Singh 33', 'VISIT SCHEDULED', NULL, NULL, 1, '2026-02-02 11:17:59'),
(514, 139314, 'Status changed to LOST', 'LOST', NULL, NULL, 1, '2026-02-02 11:58:21'),
(515, 139307, 'Status changed to PROCESSING', 'PROCESSING', '2026-02-02', '19:13:00', 1, '2026-02-02 15:09:09'),
(516, 139307, 'Status changed to NOT PICKED', 'NOT PICKED', NULL, NULL, 1, '2026-02-02 15:12:24'),
(517, 139307, 'Status changed to NOT PICKED', 'NOT PICKED', '2026-02-02', '15:13:00', 1, '2026-02-02 15:13:26'),
(518, 139307, 'Status changed to FUTURE LEAD', 'FUTURE LEAD', '2026-02-03', '18:17:00', 1, '2026-02-02 15:14:06'),
(519, 139307, 'Status changed to FUTURE LEAD', 'FUTURE LEAD', '2026-02-03', '18:20:00', 1, '2026-02-02 15:17:18'),
(520, 139315, 'Status changed to VISIT DONE | Project: asdfa', 'VISIT DONE', '2026-02-02', '18:22:00', 1, '2026-02-02 15:23:01'),
(521, 139316, 'Lead shared with Testing account', 'MEETING SCHEDULED', NULL, NULL, 1, '2026-02-02 15:56:16'),
(522, 139316, 'Lead shared with Skyline Edge', 'MEETING SCHEDULED', NULL, NULL, 1, '2026-02-02 15:56:16');

-- --------------------------------------------------------

--
-- Table structure for table `lead_projects`
--

CREATE TABLE `lead_projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `visit_status` varchar(100) DEFAULT NULL,
  `visit_date` date DEFAULT NULL,
  `visit_time` time DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `location_history`
--

CREATE TABLE `location_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lat_long` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(100) NOT NULL,
  `login_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `ip_address`, `login_date`) VALUES
(763, 1, '127.0.0.1', '2025-12-23 17:01:48'),
(764, 1, '127.0.0.1', '2025-12-24 10:32:45'),
(765, 1, '127.0.0.1', '2026-01-02 16:01:25'),
(766, 1, '127.0.0.1', '2026-01-02 16:11:45'),
(767, 1, '127.0.0.1', '2026-01-05 12:58:50'),
(768, 1, '127.0.0.1', '2026-01-05 13:04:15'),
(769, 1, '127.0.0.1', '2026-01-05 13:06:58'),
(770, 1, '127.0.0.1', '2026-01-05 15:47:52'),
(771, 1, '127.0.0.1', '2026-01-05 15:48:31'),
(772, 1, '127.0.0.1', '2026-01-06 12:12:52'),
(773, 1, '127.0.0.1', '2026-01-06 14:51:53'),
(774, 1, '127.0.0.1', '2026-01-06 15:06:53'),
(775, 1, '127.0.0.1', '2026-01-07 13:25:30'),
(776, 1, '127.0.0.1', '2026-01-07 14:46:09'),
(777, 1, '127.0.0.1', '2026-01-15 10:11:06'),
(778, 1, '127.0.0.1', '2026-01-16 16:01:52'),
(779, 1, '127.0.0.1', '2026-01-29 14:49:17'),
(780, 1, '127.0.0.1', '2026-01-29 15:11:32'),
(781, 1, '127.0.0.1', '2026-01-30 10:04:39'),
(782, 1, '127.0.0.1', '2026-01-30 10:26:42'),
(783, 30, '127.0.0.1', '2026-01-30 10:36:58'),
(784, 1, '127.0.0.1', '2026-01-30 10:38:33'),
(785, 1, '127.0.0.1', '2026-01-30 13:19:50'),
(786, 1, '127.0.0.1', '2026-01-30 16:06:04'),
(787, 1, '127.0.0.1', '2026-01-30 16:40:51'),
(788, 1, '127.0.0.1', '2026-01-30 16:41:33'),
(789, 1, '127.0.0.1', '2026-01-30 16:43:45'),
(790, 1, '127.0.0.1', '2026-01-30 16:48:53'),
(791, 1, '127.0.0.1', '2026-01-30 17:19:33'),
(792, 1, '127.0.0.1', '2026-01-30 18:35:24'),
(793, 1, '127.0.0.1', '2026-01-31 10:06:37'),
(794, 1, '127.0.0.1', '2026-01-31 16:45:51'),
(795, 1, '127.0.0.1', '2026-02-02 10:18:12'),
(796, 1, '127.0.0.1', '2026-02-02 11:06:53'),
(797, 1, '127.0.0.1', '2026-02-02 11:16:22'),
(798, 18, '127.0.0.1', '2026-02-02 11:16:58'),
(799, 1, '127.0.0.1', '2026-02-02 11:17:12'),
(800, 18, '127.0.0.1', '2026-02-02 11:18:09'),
(801, 1, '127.0.0.1', '2026-02-02 11:18:36'),
(802, 1, '127.0.0.1', '2026-02-02 14:32:41');

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
(1, '2025_07_03_115120_create_tasks_table', 1),
(2, '2025_07_03_115132_create_task_user_table', 1),
(3, '2025_07_10_050735_create_post_sales_table', 2),
(4, '2025_07_10_054031_create_post_sales_table', 3),
(5, '2025_07_14_062418_create_checklist_items_table', 4),
(6, '2025_07_05_104148_create_property_images_table', 5),
(7, '2019_12_14_000001_create_personal_access_tokens_table', 6),
(8, '2025_07_25_092705_project_types', 7),
(9, '2025_07_25_093038_project_categories', 8),
(10, '2025_07_25_093207_project_sub_categories', 9),
(11, '2025_07_25_094522_project_inventories', 10),
(12, '2025_09_02_153949_create_jobs_table', 11),
(13, '2025_09_02_221905_create_failed_jobs_table', 12);

-- --------------------------------------------------------

--
-- Table structure for table `mis_daily_entries`
--

CREATE TABLE `mis_daily_entries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `team_id` bigint(20) NOT NULL,
  `week` int(11) NOT NULL,
  `entry_date` date NOT NULL,
  `mis_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`mis_data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mis_daily_entries`
--

INSERT INTO `mis_daily_entries` (`id`, `user_id`, `team_id`, `week`, `entry_date`, `mis_data`, `created_at`, `updated_at`) VALUES
(88, 1, 13, 4, '2026-01-21', '{\"2026-01-21\":{\"Number of visits to existing sites today\":150,\"Number of site photos clicked\":150,\"dgfhjhhg\":150}}', '2026-01-29 11:59:50', '2026-01-29 11:59:51'),
(95, 1, 13, 5, '2026-01-29', '{\"2026-01-29\":{\"Number of visits to existing sites today\":2,\"Number of site photos clicked\":2,\"dgfhjhhg\":2}}', '2026-01-29 12:12:34', '2026-01-29 12:12:34');

-- --------------------------------------------------------

--
-- Table structure for table `mis_points`
--

CREATE TABLE `mis_points` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` text DEFAULT NULL,
  `point_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mis_points`
--

INSERT INTO `mis_points` (`id`, `user_id`, `point_name`, `description`, `created_at`, `updated_at`) VALUES
(3, '', 'Number of new leads generated today', NULL, '2025-10-01 05:54:53', '2026-02-02 10:10:56'),
(4, '', 'Number of visits to existing sites today', NULL, '2025-10-01 05:55:04', '2026-02-02 10:10:52'),
(5, '', 'Number of site photos clicked', NULL, '2025-10-01 05:55:10', '2026-02-02 10:10:48'),
(6, NULL, 'Number of new contractors visited today', NULL, '2025-10-01 05:55:17', '2025-10-01 05:55:17'),
(7, NULL, 'Number of contractors enrolled in the Loyalty Scheme today', NULL, '2025-10-01 05:56:35', '2025-10-01 05:56:35'),
(8, NULL, 'Number of calls made to contractors today', NULL, '2025-10-01 05:56:48', '2025-10-01 05:56:48'),
(9, NULL, 'Number of sales made through old contractors today', NULL, '2025-10-01 05:57:02', '2025-10-01 05:57:02'),
(10, NULL, 'Sales amount through old contractors today', NULL, '2025-10-01 05:57:53', '2025-10-01 05:57:53'),
(11, NULL, 'Number of sales done directly through new contractors (Contractor will be considered \"old\" after their first purchase)', NULL, '2025-10-01 05:58:01', '2025-10-01 05:58:01'),
(12, NULL, 'Sales amount through new contractors today', NULL, '2025-10-01 05:58:10', '2025-10-03 07:49:39'),
(14, NULL, 'Had lunch with any contractor today? (If yes, share details in the WhatsApp group)', NULL, '2025-10-01 05:58:26', '2025-10-01 05:58:26'),
(15, NULL, 'Testimonial video', NULL, '2025-10-01 05:58:33', '2025-10-01 10:18:17'),
(16, '', 'dgfhjhhg', 'ygfytfyfy', '2026-01-06 10:38:35', '2026-02-02 10:10:45');

-- --------------------------------------------------------

--
-- Table structure for table `mis_weekly_targets`
--

CREATE TABLE `mis_weekly_targets` (
  `id` int(10) UNSIGNED NOT NULL,
  `team_id` int(10) UNSIGNED NOT NULL,
  `target_type` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `weekly_targets` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`weekly_targets`)),
  `auto_assign` tinyint(1) NOT NULL DEFAULT 0,
  `last_auto_assigned_week` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mis_weekly_targets`
--

INSERT INTO `mis_weekly_targets` (`id`, `team_id`, `target_type`, `year`, `weekly_targets`, `auto_assign`, `last_auto_assigned_week`, `created_at`, `updated_at`) VALUES
(52, 13, 'weekly', 2026, '{\"week1\":{\"start_date\":\"2026-01-21\",\"end_date\":\"2026-01-28\",\"data\":{\"Number of visits to existing sites today\":100,\"Number of site photos clicked\":100,\"dgfhjhhg\":100}},\"week2\":{\"start_date\":\"2026-01-29\",\"end_date\":\"2026-02-04\",\"data\":{\"Number of visits to existing sites today\":100,\"Number of site photos clicked\":100,\"dgfhjhhg\":100}}}', 1, NULL, '2026-01-29 11:36:50', '2026-01-29 12:11:58');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `post_sales`
--

CREATE TABLE `post_sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `sales_person_id` bigint(20) UNSIGNED NOT NULL,
  `applicant_name` varchar(255) NOT NULL,
  `applicant_number` varchar(255) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `unit_number` varchar(255) NOT NULL,
  `project_category` varchar(255) NOT NULL,
  `project_sub_category` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `doa` date DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `permanent_address` text NOT NULL,
  `current_address` text NOT NULL,
  `checklist` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`checklist`)),
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `post_sales`
--

INSERT INTO `post_sales` (`id`, `lead_id`, `sales_person_id`, `applicant_name`, `applicant_number`, `project_name`, `unit_number`, `project_category`, `project_sub_category`, `dob`, `doa`, `email`, `permanent_address`, `current_address`, `checklist`, `user_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(14, 139307, 15, 'asdfasdf', '23423', 'asdfa', '23423', 'Skyline Edge', 'adfa', '2026-01-30', '2026-02-01', 'saurabhrawasdfat@clikzopinnovations.com', 'asdfasd', 'asdfadf', '[]', 1, '2026-01-30 11:42:33', '2026-01-30 11:42:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `post_sale_documents`
--

CREATE TABLE `post_sale_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `post_sale_id` bigint(20) UNSIGNED NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `file_size` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_sale_ratings`
--

CREATE TABLE `post_sale_ratings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `post_sale_id` bigint(20) UNSIGNED NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL,
  `comments` text DEFAULT NULL,
  `ip_address` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `project_name` text NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `project_name`, `created_date`) VALUES
(4, 'asdfa', '2025-11-27 15:07:35'),
(5, 'asdf234', '2025-11-27 15:07:40');

-- --------------------------------------------------------

--
-- Table structure for table `project_inventories`
--

CREATE TABLE `project_inventories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `sub_category` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `short_description` text DEFAULT NULL,
  `location` text NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'India',
  `pin_code` varchar(255) DEFAULT NULL,
  `longitude` text NOT NULL,
  `latitude` text NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `price_unit` varchar(255) NOT NULL DEFAULT 'sqft',
  `price_display` varchar(255) DEFAULT NULL,
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specifications`)),
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`amenities`)),
  `nearby_locations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`nearby_locations`)),
  `social_links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`social_links`)),
  `logo_path` varchar(255) DEFAULT NULL,
  `cover_image_path` varchar(255) DEFAULT NULL,
  `gallery_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery_images`)),
  `video_link` varchar(255) DEFAULT NULL,
  `brochure_path` varchar(255) DEFAULT NULL,
  `floor_plan_path` varchar(255) DEFAULT NULL,
  `site_map_path` varchar(255) DEFAULT NULL,
  `price_list_path` varchar(255) DEFAULT NULL,
  `contact_number_1` varchar(255) NOT NULL,
  `contact_number_2` varchar(255) DEFAULT NULL,
  `whatsapp_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `instagram_link` text DEFAULT NULL,
  `facebook_link` text DEFAULT NULL,
  `twitter_link` text DEFAULT NULL,
  `linkedin_link` text DEFAULT NULL,
  `form_fields` longtext DEFAULT NULL,
  `style_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`style_settings`)),
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_mst`
--

CREATE TABLE `role_mst` (
  `id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL,
  `manager_rights` tinyint(1) NOT NULL DEFAULT 0,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `role_mst`
--

INSERT INTO `role_mst` (`id`, `role_name`, `manager_rights`, `created_date`, `updated_date`) VALUES
(1, 'admin', 1, '2024-05-30 12:06:22', NULL),
(2, 'divisional_head', 1, '2024-05-31 05:16:12', NULL),
(3, 'salesman', 0, '2024-05-31 05:16:12', NULL),
(5, 'reception', 1, '2025-09-17 11:03:37', NULL),
(7, 'task_management', 0, '2025-09-17 11:06:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `field1` text DEFAULT NULL,
  `field2` text DEFAULT NULL,
  `taf_active` int(11) NOT NULL DEFAULT 0,
  `is_rpt_field1` bit(1) NOT NULL DEFAULT b'0',
  `is_rpt_field2` bit(1) NOT NULL DEFAULT b'0',
  `logo` text DEFAULT NULL,
  `field3` text DEFAULT NULL,
  `is_rpt_field3` int(1) NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `field1`, `field2`, `taf_active`, `is_rpt_field1`, `is_rpt_field2`, `logo`, `field3`, `is_rpt_field3`) VALUES
(1, 'City', 'State', 0, b'1', b'1', 'uploads/612854-final_logo_skyland.png', 'Address', 1);

-- --------------------------------------------------------

--
-- Table structure for table `shared_leads`
--

CREATE TABLE `shared_leads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shared_leads`
--

INSERT INTO `shared_leads` (`id`, `user_id`, `token`, `expires_at`, `created_at`, `updated_at`) VALUES
(49, 1, 'mXuwEBwmToQqGSOpANKG4hxH6TbW8t7nInU70VPR', '2025-11-17 04:34:21', '2025-11-10 04:34:21', '2025-11-10 04:34:21'),
(50, 1, 'YQhPvHiDVIOf11CULTU4zBKedXU6e6z262CsXows', '2026-01-22 04:51:47', '2026-01-15 04:51:47', '2026-01-15 04:51:47'),
(51, 30, 'RATYamSvsl6TIfRAxBg2kUpwhjfgd6Ooob1LzKrE', '2026-02-06 05:07:05', '2026-01-30 05:07:05', '2026-01-30 05:07:05'),
(52, 1, 'xCs5PFNN6rQXXdsOFoGHOtiVSSWZGLm894lO7u4b', '2026-02-07 09:57:25', '2026-01-31 09:57:25', '2026-01-31 09:57:25');

-- --------------------------------------------------------

--
-- Table structure for table `shared_post_sale`
--

CREATE TABLE `shared_post_sale` (
  `id` bigint(20) NOT NULL,
  `post_sale_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shared_post_sale`
--

INSERT INTO `shared_post_sale` (`id`, `post_sale_id`, `user_id`, `token`, `expires_at`, `created_at`, `updated_at`) VALUES
(19, 14, NULL, 'MQiZ6rPHVTEYNa2GkSuRQHFfeXwcFcXo', '2026-02-06 11:42:37', '2026-01-30 11:42:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `software_details`
--

CREATE TABLE `software_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `software_name` varchar(150) NOT NULL,
  `software_type` varchar(50) DEFAULT NULL,
  `client_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `version` varchar(50) DEFAULT NULL,
  `apk` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `software_details`
--

INSERT INTO `software_details` (`id`, `software_name`, `software_type`, `client_name`, `description`, `version`, `apk`, `status`, `created_at`, `updated_at`) VALUES
(1, 'demoLeadmanage', 'real_state', 'Demo Client', 'Lead management software demo version', '1.0.0', NULL, 'active', '2025-10-21 10:11:44', '2025-11-11 10:00:34');

-- --------------------------------------------------------

--
-- Table structure for table `software_features`
--

CREATE TABLE `software_features` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `software_name` varchar(255) NOT NULL,
  `feature_name` varchar(255) NOT NULL,
  `price` decimal(12,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'inactive',
  `meta` longtext DEFAULT NULL,
  `activate_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `integration_status` tinyint(4) NOT NULL DEFAULT 0,
  `is_realstate` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `software_features`
--

INSERT INTO `software_features` (`id`, `software_name`, `feature_name`, `price`, `status`, `meta`, `activate_at`, `expires_at`, `video_url`, `integration_status`, `is_realstate`, `created_at`, `updated_at`) VALUES
(1, 'realStateCrm', 'expense_management', 500.00, 'active', '{\"description\":\"Empower your team to record and manage all expenses effortlessly — while maintaining full admin control through approval-based workflows. Every expense entry, from TA and DA to Hotels and Others, is securely tracked, categorized, and supported with digital attachments for complete financial transparency.\",\"key_benefits\":\"Streamlined approval process with admin authority<br \\/>\\nCategorized tracking (TA, DA, Hotels, Others) for easy reporting<br \\/>\\nAttach bills and proofs with every entry<br \\/>\\nReal-time updates and instant approval notifications<br \\/>\\nPrevent misuse with transparent approval logs\",\"analytics\":\"Category-wise expense summary and trends, Daily, weekly, and monthly expense charts<br \\/>\\nUser-wise and project-wise expense reports, Approved vs. Pending vs. Rejected expense statistics\"}', NULL, NULL, 'https://www.youtube.com/embed/dQw4w9WgXcQ', 0, 0, '2025-10-15 07:06:26', '2025-11-04 11:40:54'),
(2, 'realStateCrm', 'employee_tracking', 1000.00, 'active', '{\"description\":\"Track your employees’ live location, daily movements, and work attendance from a single dashboard. Designed for on-field and remote teams, the Employee Tracking module ensures complete transparency, accurate reporting, and improved productivity — without the need for manual check-ins.\",\"key_benefits\":\"Real-time GPS tracking with route history<br \\/>\\nAutomated attendance with location tagging<br \\/>\\nGeo-fencing alerts for unauthorized areas<br \\/>\\nActivity timeline with start & end logs<br \\/>\\nInstant admin visibility of on-field workforce\",\"analytics\":\"Attendance summary and punctuality graph<br \\/>\\nLocation-wise and date-wise tracking reports<br \\/>\\nActive vs. inactive user heatmaps<br \\/>\\nDaily performance score based on visits & timing\"}', NULL, NULL, 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 0, 0, '2025-10-15 07:06:26', '2025-11-04 11:40:54'),
(3, 'realStateCrm', 'task_management', 2000.00, 'active', '{\"description\":\"The Task Management module helps you organize and monitor every team activity in real time. Admins and Managers can assign one-time or recurring tasks (daily, weekly, or monthly), track progress, set deadlines, and ensure accountability — all within the CRM. It streamlines internal communication and keeps the entire team focused on goals.\",\"key_benefits\":\"Create and assign tasks to single or multiple users instantly<br \\/>\\nSet daily, weekly, or monthly recurring schedules<br \\/>\\nAdd task details, deadlines, and priority levels<br \\/>\\nTrack completion percentage and overdue alerts<br \\/>\\nBoost accountability through transparent task logs\",\"analytics\":\"User-wise task completion rate<br \\/>\\nPending vs. completed task ratio<br \\/>\\nProductivity graph per department\\/team<br \\/>\\nWeekly and monthly task summary reports\"}', NULL, NULL, 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 0, 0, '2025-10-15 07:06:26', '2025-11-04 11:40:54'),
(4, 'realStateCrm', 'inventory_management', 1500.00, 'active', '{\"description\":\"The Inventory Management module helps real estate companies maintain complete control over their property listings, units, and project inventories. From adding new projects to marking units as available, on hold, processing, or sold, this feature ensures your sales team always has up-to-date availability data — preventing double bookings and confusion. It simplifies the selling process while maintaining full transparency between teams and management.\",\"key_benefits\":\"•\\tAdd and manage complete project and unit inventory in one place<br \\/>\\n•\\tTrack live availability status: available, hold, processing, or sold<br \\/>\\n•\\tHold units for 24 hours to secure client interest<br \\/>\\n•\\tAvoid double booking through centralized control<br \\/>\\n•\\tImprove coordination between sales and admin teams\",\"analytics\":\"Project-wise inventory and status-wise breakdown<br \\/>\\nReal-time sold vs. available reports<br \\/>\\nSales trend and booking summary insights<br \\/>\\nTeam performance based on closed inventory\"}', NULL, NULL, 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 0, 0, '2025-10-15 07:06:26', '2025-11-04 11:40:54'),
(5, 'realStateCrm', 'integration', 20000.00, 'active', '[]', NULL, NULL, 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 0, 0, '2025-10-15 07:06:26', '2025-11-04 11:40:54'),
(6, 'realStateCrm', 'shared_lead_form', 500.00, 'active', '{\"description\":\"The Shared Lead Form module allows every staff member or admin to create and share personalized lead forms via link — making it easy to capture inquiries from multiple sources. Whether it’s an exhibition, company website, WhatsApp message to clients, or channel partner sharing, every form submission automatically syncs into the CRM as a new lead. This ensures quick response, accurate data, and zero manual entry — giving your team a smarter way to grow the sales pipeline.\",\"key_benefits\":\"Create and share lead form links for exhibitions, websites, or campaigns<br \\/>\\nDistribute forms to clients or channel partners to collect direct leads<br \\/>\\nAuto-capture all submissions inside the CRM with full details<br \\/>\\nEliminate manual data entry and reduce lead leakage<br \\/>\\nEnable instant follow-up through integrated reminders and actions\",\"analytics\":\"Analyze source-wise and campaign-wise form performance<br \\/>\\nIdentify top-performing partners or staff generating form-based leads<br \\/>\\nTrack submission trends across different events or campaigns\"}', NULL, NULL, 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 0, 0, '2025-10-15 07:06:26', '2025-11-04 11:40:54'),
(7, 'realStateCrm', 'mis_management', 2000.00, 'active', '{\"description\":\"The MIS (Management Information System) module helps teams set, track, and analyze weekly performance targets for various measurable MIS points. Admins can assign weekly goals, record daily achievements, and review progress through interactive summary reports. With auto-assignment options, detailed analytics, and Excel export, this feature ensures complete visibility and accountability across all departments.\",\"key_benefits\":\"Set weekly and daily performance targets for teams and MIS points<br \\/>\\nAuto-carry forward weekly targets using “Auto-Assign”<br \\/>\\nMonitor progress through weekly and daily achievement views<br \\/>\\nReview overall performance with summarized target-achieved %<br \\/>\\nExport data to Excel for reporting and trend analysis\",\"analytics\":\"Achievement percentage per MIS point and week<br \\/>\\nNormalized performance comparison across multiple tasks<br \\/>\\nTeam-wise and time-wise performance summaries<br \\/>\\nDetailed daily and weekly trend tracking\"}', NULL, NULL, 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 0, 0, '2025-10-15 07:06:26', '2025-11-04 11:40:54'),
(8, 'realStateCrm', 'project_detail_page', 2000.00, 'active', '{\"description\":\"The Project Detail Page module allows builders, real estate marketers, and channel partners to instantly create a single-page project website for any property — without any coding. By simply adding project details like images, amenities, pricing, contact info, and videos, users can generate N number of SEO-friendly landing pages. Each page can be shared with clients, used in digital ads, or linked to WhatsApp campaigns — helping capture more leads and showcase every project beautifully.\",\"key_benefits\":\"Create stunning single-page websites instantly from project details<br \\/>\\nGenerate unlimited landing pages — one for each project or campaign<br \\/>\\nAuto-sync project pages with CRM for instant lead capture<br \\/>\\nCustomize themes, colors, and layout for brand consistency<br \\/>\\nAdd videos, galleries, and downloadable brochures for better engagement\",\"analytics\":\"Track total project pages created and published<br \\/>\\nMonitor leads and inquiries received from each landing page<br \\/>\\nAnalyze visitor engagement via clicks, views, and downloads<br \\/>\\nCompare project-wise performance and conversion effectiveness\"}', NULL, NULL, 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 0, 0, '2025-10-15 07:06:26', '2025-11-04 11:40:54'),
(9, 'realStateCrm', 'magic_bricks', 10000.00, 'inactive', '{\"description\":\"The Housing Integration feature connects your Housing.com account with the CRM to automatically import all inquiries and property leads in real time. Every new lead appears directly in your dashboard — ready for quick assignment and follow-up.\",\"key_benefits\":\"Auto-capture Housing.com leads into the CRM<br \\/>\\nRemove manual data entry and save time<br \\/>\\nAssign leads instantly to sales executives<br \\/>\\nEnsure no inquiry is missed or delayed\",\"analytics\":\"Track total Housing.com leads received<br \\/>\\nMeasure conversions and response performance\"}', NULL, NULL, 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 1, 0, '2025-10-22 07:49:40', '2025-11-04 11:40:54'),
(10, 'realStateCrm', '99_acres', 500.00, 'inactive', '{\"description\":\"Seamlessly integrate your 99acres account with the CRM to auto-import all property inquiries in real time. Each lead is instantly added to your dashboard and mapped to the right project or team, ensuring zero manual entry and faster follow-up.\",\"key_benefits\":\"Auto-sync leads from 99acres to CRM<br \\/>\\nEliminate manual imports and data loss<br \\/>\\nAssign leads instantly to sales teams<br \\/>\\nImprove response time and lead tracking\",\"analytics\":\"Track total 99acres leads received<br \\/>\\nMonitor lead status and conversions from portal leads\"}', NULL, NULL, 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 1, 0, '2025-10-22 07:49:40', '2025-11-04 11:40:54'),
(11, 'realStateCrm', 'google_sheets', 1000.00, 'inactive', NULL, NULL, NULL, 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 1, 0, '2025-10-22 07:49:40', '2025-11-04 11:40:54'),
(12, 'realStateCrm', 'google_form', 1000.00, 'inactive', '[]', NULL, NULL, 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 1, 0, '2025-10-22 07:49:40', '2025-11-04 11:40:54'),
(13, 'realStateCrm', 'post_sale', 20000.00, 'active', '[]', NULL, NULL, 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 0, 1, '2025-10-24 09:46:03', '2025-11-04 11:40:54'),
(14, 'realStateCrm', 'facebook', 8000.00, 'inactive', '[]', NULL, NULL, 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 1, 0, '2025-10-27 12:00:48', '2025-11-04 11:40:54'),
(15, 'realStateCrm', 'gmail', 1200.00, 'inactive', '[]', NULL, NULL, 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 1, 0, '2025-10-27 12:00:48', '2025-11-04 11:40:54'),
(16, 'realStateCrm', 'housing', 500.00, 'inactive', '[]', NULL, NULL, 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 1, 0, '2025-10-27 12:01:57', '2025-11-04 11:40:54');

-- --------------------------------------------------------

--
-- Table structure for table `software_requests`
--

CREATE TABLE `software_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `software_name` varchar(255) NOT NULL,
  `software_id` bigint(20) DEFAULT NULL,
  `client_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `requested_date` date DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sources`
--

CREATE TABLE `sources` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sources`
--

INSERT INTO `sources` (`id`, `name`, `created_date`) VALUES
(15, 'asdfa', '2025-11-27 15:57:16');

-- --------------------------------------------------------

--
-- Table structure for table `state_district`
--

CREATE TABLE `state_district` (
  `id` int(10) NOT NULL,
  `state` varchar(100) DEFAULT NULL,
  `District` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `state_district`
--

INSERT INTO `state_district` (`id`, `state`, `District`) VALUES
(27, 'Arunachal Pradesh', 'Lohit'),
(28, 'Arunachal Pradesh', 'Longding'),
(29, 'Arunachal Pradesh', 'Lower Dibang Valley'),
(30, 'Arunachal Pradesh', 'Lower Siang'),
(31, 'Arunachal Pradesh', 'Lower Subansiri'),
(32, 'Arunachal Pradesh', 'Namsai'),
(33, 'Arunachal Pradesh', 'Pakke Kessang'),
(34, 'Arunachal Pradesh', 'Papum Pare'),
(35, 'Arunachal Pradesh', 'Shi Yomi'),
(36, 'Arunachal Pradesh', 'Tawang'),
(37, 'Arunachal Pradesh', 'Tirap'),
(38, 'Arunachal Pradesh', 'Upper Siang'),
(39, 'Arunachal Pradesh', 'Upper Subansiri'),
(40, 'Arunachal Pradesh', 'West Kameng'),
(41, 'Arunachal Pradesh', 'West Siang'),
(42, 'Assam', 'Bajali'),
(43, 'Assam', 'Baksa'),
(44, 'Assam', 'Barpeta'),
(45, 'Assam', 'Biswanath'),
(46, 'Assam', 'Bongaigaon'),
(47, 'Assam', 'Cachar'),
(48, 'Assam', 'Charaideo'),
(49, 'Assam', 'Chirang'),
(50, 'Assam', 'Darrang'),
(51, 'Assam', 'Dhemaji'),
(52, 'Assam', 'Dhubri'),
(53, 'Assam', 'Dibrugarh'),
(54, 'Assam', 'Dima Hasao'),
(55, 'Assam', 'Goalpara'),
(56, 'Assam', 'Golaghat'),
(57, 'Assam', 'Hailakandi'),
(58, 'Assam', 'Hojai'),
(59, 'Assam', 'Jorhat'),
(60, 'Assam', 'Kamrup'),
(61, 'Assam', 'Kamrup Metropolitan'),
(62, 'Assam', 'Karbi Anglong'),
(63, 'Assam', 'Karimganj'),
(64, 'Assam', 'Kokrajhar'),
(65, 'Assam', 'Lakhimpur'),
(66, 'Assam', 'Majuli'),
(67, 'Assam', 'Morigaon'),
(68, 'Assam', 'Nagaon'),
(69, 'Assam', 'Nalbari'),
(70, 'Assam', 'Sivasagar'),
(71, 'Assam', 'Sonitpur'),
(72, 'Assam', 'South Salmara-Mankachar'),
(73, 'Assam', 'Tinsukia'),
(74, 'Assam', 'Udalguri'),
(75, 'Assam', 'West Karbi Anglong'),
(76, 'Bihar', 'Araria'),
(77, 'Bihar', 'Arwal'),
(78, 'Bihar', 'Aurangabad'),
(79, 'Bihar', 'Banka'),
(80, 'Bihar', 'Begusarai'),
(81, 'Bihar', 'Bhagalpur'),
(82, 'Bihar', 'Bhojpur'),
(83, 'Bihar', 'Buxar'),
(84, 'Bihar', 'Darbhanga'),
(85, 'Bihar', 'East Champaran'),
(86, 'Bihar', 'Gaya'),
(87, 'Bihar', 'Gopalganj'),
(88, 'Bihar', 'Jamui'),
(89, 'Bihar', 'Jehanabad'),
(90, 'Bihar', 'Kaimur'),
(91, 'Bihar', 'Katihar'),
(92, 'Bihar', 'Khagaria'),
(93, 'Bihar', 'Kishanganj'),
(94, 'Bihar', 'Lakhisarai'),
(95, 'Bihar', 'Madhepura'),
(96, 'Bihar', 'Madhubani'),
(97, 'Bihar', 'Munger'),
(98, 'Bihar', 'Muzaffarpur'),
(99, 'Bihar', 'Nalanda'),
(100, 'Bihar', 'Nawada'),
(101, 'Bihar', 'Patna'),
(102, 'Bihar', 'Purnia'),
(103, 'Bihar', 'Rohtas'),
(104, 'Bihar', 'Saharsa'),
(105, 'Bihar', 'Samastipur'),
(106, 'Bihar', 'Saran'),
(107, 'Bihar', 'Sheikhpura'),
(108, 'Bihar', 'Sheohar'),
(109, 'Bihar', 'Sitamarhi'),
(110, 'Bihar', 'Siwan'),
(111, 'Bihar', 'Supaul'),
(112, 'Bihar', 'Vaishali'),
(113, 'Bihar', 'West Champaran'),
(114, 'Chandigarh', 'Chandigarh'),
(115, 'Chhattisgarh', 'Balod'),
(116, 'Chhattisgarh', 'Baloda Bazar'),
(117, 'Chhattisgarh', 'Balrampur'),
(118, 'Chhattisgarh', 'Bastar'),
(119, 'Chhattisgarh', 'Bemetara'),
(120, 'Chhattisgarh', 'Bijapur'),
(121, 'Chhattisgarh', 'Bilaspur'),
(122, 'Chhattisgarh', 'Dantewada'),
(123, 'Chhattisgarh', 'Dhamtari'),
(124, 'Chhattisgarh', 'Durg'),
(125, 'Chhattisgarh', 'Gariaband'),
(126, 'Chhattisgarh', 'Gaurela Pendra Marwahi'),
(127, 'Chhattisgarh', 'Janjgir Champa'),
(128, 'Chhattisgarh', 'Jashpur'),
(129, 'Chhattisgarh', 'Kabirdham'),
(130, 'Chhattisgarh', 'Kanker'),
(131, 'Chhattisgarh', 'Kondagaon'),
(132, 'Chhattisgarh', 'Korba'),
(133, 'Chhattisgarh', 'Koriya'),
(134, 'Chhattisgarh', 'Mahasamund'),
(135, 'Chhattisgarh', 'Mungeli'),
(136, 'Chhattisgarh', 'Narayanpur'),
(137, 'Chhattisgarh', 'Raigarh'),
(138, 'Chhattisgarh', 'Raipur'),
(139, 'Chhattisgarh', 'Rajnandgaon'),
(140, 'Chhattisgarh', 'Sukma'),
(141, 'Chhattisgarh', 'Surajpur'),
(142, 'Chhattisgarh', 'Surguja'),
(143, 'Dadra Nagar Haveli', 'Dadra Nagar Haveli'),
(144, 'Daman Diu', 'Daman'),
(145, 'Daman Diu', 'Diu'),
(146, 'Delhi', 'Central Delhi'),
(147, 'Delhi', 'East Delhi'),
(148, 'Delhi', 'New Delhi'),
(149, 'Delhi', 'North Delhi'),
(150, 'Delhi', 'North East Delhi'),
(151, 'Delhi', 'North West Delhi'),
(152, 'Delhi', 'Shahdara'),
(153, 'Delhi', 'South Delhi'),
(154, 'Delhi', 'South East Delhi'),
(155, 'Delhi', 'South West Delhi'),
(156, 'Delhi', 'West Delhi'),
(157, 'Goa', 'North Goa'),
(158, 'Goa', 'South Goa'),
(159, 'Gujarat', 'Ahmedabad'),
(160, 'Gujarat', 'Amreli'),
(161, 'Gujarat', 'Anand'),
(162, 'Gujarat', 'Aravalli'),
(163, 'Gujarat', 'Banaskantha'),
(164, 'Gujarat', 'Bharuch'),
(165, 'Gujarat', 'Bhavnagar'),
(166, 'Gujarat', 'Botad'),
(167, 'Gujarat', 'Chhota Udaipur'),
(168, 'Gujarat', 'Dahod'),
(169, 'Gujarat', 'Dang'),
(170, 'Gujarat', 'Devbhoomi Dwarka'),
(171, 'Gujarat', 'Gandhinagar'),
(172, 'Gujarat', 'Gir Somnath'),
(173, 'Gujarat', 'Jamnagar'),
(174, 'Gujarat', 'Junagadh'),
(175, 'Gujarat', 'Kheda'),
(176, 'Gujarat', 'Kutch'),
(177, 'Gujarat', 'Mahisagar'),
(178, 'Gujarat', 'Mehsana'),
(179, 'Gujarat', 'Morbi'),
(180, 'Gujarat', 'Narmada'),
(181, 'Gujarat', 'Navsari'),
(182, 'Gujarat', 'Panchmahal'),
(183, 'Gujarat', 'Patan'),
(184, 'Gujarat', 'Porbandar'),
(185, 'Gujarat', 'Rajkot'),
(186, 'Gujarat', 'Sabarkantha'),
(187, 'Gujarat', 'Surat'),
(188, 'Gujarat', 'Surendranagar'),
(189, 'Gujarat', 'Tapi'),
(190, 'Gujarat', 'Vadodara'),
(191, 'Gujarat', 'Valsad'),
(192, 'Haryana', 'Ambala'),
(193, 'Haryana', 'Bhiwani'),
(194, 'Haryana', 'Charkhi Dadri'),
(195, 'Haryana', 'Faridabad'),
(196, 'Haryana', 'Fatehabad'),
(197, 'Haryana', 'Gurugram'),
(198, 'Haryana', 'Hisar'),
(199, 'Haryana', 'Jhajjar'),
(200, 'Haryana', 'Jind'),
(201, 'Haryana', 'Kaithal'),
(202, 'Haryana', 'Karnal'),
(203, 'Haryana', 'Kurukshetra'),
(204, 'Haryana', 'Mahendragarh'),
(205, 'Haryana', 'Mewat'),
(206, 'Haryana', 'Palwal'),
(207, 'Haryana', 'Panchkula'),
(208, 'Haryana', 'Panipat'),
(209, 'Haryana', 'Rewari'),
(210, 'Haryana', 'Rohtak'),
(211, 'Haryana', 'Sirsa'),
(212, 'Haryana', 'Sonipat'),
(213, 'Haryana', 'Yamunanagar'),
(214, 'Himachal Pradesh', 'Bilaspur'),
(215, 'Himachal Pradesh', 'Chamba'),
(216, 'Himachal Pradesh', 'Hamirpur'),
(217, 'Himachal Pradesh', 'Kangra'),
(218, 'Himachal Pradesh', 'Kinnaur'),
(219, 'Himachal Pradesh', 'Kullu'),
(220, 'Himachal Pradesh', 'Lahaul Spiti'),
(221, 'Himachal Pradesh', 'Mandi'),
(222, 'Himachal Pradesh', 'Shimla'),
(223, 'Himachal Pradesh', 'Sirmaur'),
(224, 'Himachal Pradesh', 'Solan'),
(225, 'Himachal Pradesh', 'Una'),
(226, 'Jammu Kashmir', 'Anantnag'),
(227, 'Jammu Kashmir', 'Bandipora'),
(228, 'Jammu Kashmir', 'Baramulla'),
(229, 'Jammu Kashmir', 'Budgam'),
(230, 'Jammu Kashmir', 'Doda'),
(231, 'Jammu Kashmir', 'Ganderbal'),
(232, 'Jammu Kashmir', 'Jammu'),
(233, 'Jammu Kashmir', 'Kathua'),
(234, 'Jammu Kashmir', 'Kishtwar'),
(235, 'Jammu Kashmir', 'Kulgam'),
(236, 'Jammu Kashmir', 'Kupwara'),
(237, 'Jammu Kashmir', 'Poonch'),
(238, 'Jammu Kashmir', 'Pulwama'),
(239, 'Jammu Kashmir', 'Rajouri'),
(240, 'Jammu Kashmir', 'Ramban'),
(241, 'Jammu Kashmir', 'Reasi'),
(242, 'Jammu Kashmir', 'Samba'),
(243, 'Jammu Kashmir', 'Shopian'),
(244, 'Jammu Kashmir', 'Srinagar'),
(245, 'Jammu Kashmir', 'Udhampur'),
(246, 'Jharkhand', 'Bokaro'),
(247, 'Jharkhand', 'Chatra'),
(248, 'Jharkhand', 'Deoghar'),
(249, 'Jharkhand', 'Dhanbad'),
(250, 'Jharkhand', 'Dumka'),
(251, 'Jharkhand', 'East Singhbhum'),
(252, 'Jharkhand', 'Garhwa'),
(253, 'Jharkhand', 'Giridih'),
(254, 'Jharkhand', 'Godda'),
(255, 'Jharkhand', 'Gumla'),
(256, 'Jharkhand', 'Hazaribagh'),
(257, 'Jharkhand', 'Jamtara'),
(258, 'Jharkhand', 'Khunti'),
(259, 'Jharkhand', 'Koderma'),
(260, 'Jharkhand', 'Latehar'),
(261, 'Jharkhand', 'Lohardaga'),
(262, 'Jharkhand', 'Pakur'),
(263, 'Jharkhand', 'Palamu'),
(264, 'Jharkhand', 'Ramgarh'),
(265, 'Jharkhand', 'Ranchi'),
(266, 'Jharkhand', 'Sahebganj'),
(267, 'Jharkhand', 'Seraikela Kharsawan'),
(268, 'Jharkhand', 'Simdega'),
(269, 'Jharkhand', 'West Singhbhum'),
(270, 'Karnataka', 'Bagalkot'),
(271, 'Karnataka', 'Bangalore Rural'),
(272, 'Karnataka', 'Bangalore Urban'),
(273, 'Karnataka', 'Belgaum'),
(274, 'Karnataka', 'Bellary'),
(275, 'Karnataka', 'Bidar'),
(276, 'Karnataka', 'Chamarajanagar'),
(277, 'Karnataka', 'Chikkaballapur'),
(278, 'Karnataka', 'Chikkamagaluru'),
(279, 'Karnataka', 'Chitradurga'),
(280, 'Karnataka', 'Dakshina Kannada'),
(281, 'Karnataka', 'Davanagere'),
(282, 'Karnataka', 'Dharwad'),
(283, 'Karnataka', 'Gadag'),
(284, 'Karnataka', 'Gulbarga'),
(285, 'Karnataka', 'Hassan'),
(286, 'Karnataka', 'Haveri'),
(287, 'Karnataka', 'Kodagu'),
(288, 'Karnataka', 'Kolar'),
(289, 'Karnataka', 'Koppal'),
(290, 'Karnataka', 'Mandya'),
(291, 'Karnataka', 'Mysore'),
(292, 'Karnataka', 'Raichur'),
(293, 'Karnataka', 'Ramanagara'),
(294, 'Karnataka', 'Shimoga'),
(295, 'Karnataka', 'Tumkur'),
(296, 'Karnataka', 'Udupi'),
(297, 'Karnataka', 'Uttara Kannada'),
(298, 'Karnataka', 'Vijayanagara'),
(299, 'Karnataka', 'Vijayapura'),
(300, 'Karnataka', 'Yadgir'),
(301, 'Kerala', 'Alappuzha'),
(302, 'Kerala', 'Ernakulam'),
(303, 'Kerala', 'Idukki'),
(304, 'Kerala', 'Kannur'),
(305, 'Kerala', 'Kasaragod'),
(306, 'Kerala', 'Kollam'),
(307, 'Kerala', 'Kottayam'),
(308, 'Kerala', 'Kozhikode'),
(309, 'Kerala', 'Malappuram'),
(310, 'Kerala', 'Palakkad'),
(311, 'Kerala', 'Pathanamthitta'),
(312, 'Kerala', 'Thiruvananthapuram'),
(313, 'Kerala', 'Thrissur'),
(314, 'Kerala', 'Wayanad'),
(315, 'Lakshadweep', 'Lakshadweep'),
(316, 'Ladakh', 'Kargil'),
(317, 'Ladakh', 'Leh'),
(318, 'Madhya Pradesh', 'Agar Malwa'),
(319, 'Madhya Pradesh', 'Alirajpur'),
(320, 'Madhya Pradesh', 'Anuppur'),
(321, 'Madhya Pradesh', 'Ashoknagar'),
(322, 'Madhya Pradesh', 'Balaghat'),
(323, 'Madhya Pradesh', 'Barwani'),
(324, 'Madhya Pradesh', 'Betul'),
(325, 'Madhya Pradesh', 'Bhind'),
(326, 'Madhya Pradesh', 'Bhopal'),
(327, 'Madhya Pradesh', 'Burhanpur'),
(328, 'Madhya Pradesh', 'Chachaura'),
(329, 'Madhya Pradesh', 'Chhatarpur'),
(330, 'Madhya Pradesh', 'Chhindwara'),
(331, 'Madhya Pradesh', 'Damoh'),
(332, 'Madhya Pradesh', 'Datia'),
(333, 'Madhya Pradesh', 'Dewas'),
(334, 'Madhya Pradesh', 'Dhar'),
(335, 'Madhya Pradesh', 'Dindori'),
(336, 'Madhya Pradesh', 'Guna'),
(337, 'Madhya Pradesh', 'Gwalior'),
(338, 'Madhya Pradesh', 'Harda'),
(339, 'Madhya Pradesh', 'Hoshangabad'),
(340, 'Madhya Pradesh', 'Indore'),
(341, 'Madhya Pradesh', 'Jabalpur'),
(342, 'Madhya Pradesh', 'Jhabua'),
(343, 'Madhya Pradesh', 'Katni'),
(344, 'Madhya Pradesh', 'Khandwa'),
(345, 'Madhya Pradesh', 'Khargone'),
(346, 'Madhya Pradesh', 'Maihar'),
(347, 'Madhya Pradesh', 'Mandla'),
(348, 'Madhya Pradesh', 'Mandsaur'),
(349, 'Madhya Pradesh', 'Morena'),
(350, 'Madhya Pradesh', 'Narsinghpur'),
(351, 'Madhya Pradesh', 'Nagda'),
(352, 'Madhya Pradesh', 'Neemuch'),
(353, 'Madhya Pradesh', 'Niwari'),
(354, 'Madhya Pradesh', 'Panna'),
(355, 'Madhya Pradesh', 'Raisen'),
(356, 'Madhya Pradesh', 'Rajgarh'),
(357, 'Madhya Pradesh', 'Ratlam'),
(358, 'Madhya Pradesh', 'Rewa'),
(359, 'Madhya Pradesh', 'Sagar'),
(360, 'Madhya Pradesh', 'Satna'),
(361, 'Madhya Pradesh', 'Sehore'),
(362, 'Madhya Pradesh', 'Seoni'),
(363, 'Madhya Pradesh', 'Shahdol'),
(364, 'Madhya Pradesh', 'Shajapur'),
(365, 'Madhya Pradesh', 'Sheopur'),
(366, 'Madhya Pradesh', 'Shivpuri'),
(367, 'Madhya Pradesh', 'Sidhi'),
(368, 'Madhya Pradesh', 'Singrauli'),
(369, 'Madhya Pradesh', 'Tikamgarh'),
(370, 'Madhya Pradesh', 'Ujjain'),
(371, 'Madhya Pradesh', 'Umaria'),
(372, 'Madhya Pradesh', 'Vidisha'),
(373, 'Maharashtra', 'Ahmednagar'),
(374, 'Maharashtra', 'Akola'),
(375, 'Maharashtra', 'Amravati'),
(376, 'Maharashtra', 'Aurangabad'),
(377, 'Maharashtra', 'Beed'),
(378, 'Maharashtra', 'Bhandara'),
(379, 'Maharashtra', 'Buldhana'),
(380, 'Maharashtra', 'Chandrapur'),
(381, 'Maharashtra', 'Dhule'),
(382, 'Maharashtra', 'Gadchiroli'),
(383, 'Maharashtra', 'Gondia'),
(384, 'Maharashtra', 'Hingoli'),
(385, 'Maharashtra', 'Jalgaon'),
(386, 'Maharashtra', 'Jalna'),
(387, 'Maharashtra', 'Kolhapur'),
(388, 'Maharashtra', 'Latur'),
(389, 'Maharashtra', 'Mumbai City'),
(390, 'Maharashtra', 'Mumbai Suburban'),
(391, 'Maharashtra', 'Nagpur'),
(392, 'Maharashtra', 'Nanded'),
(393, 'Maharashtra', 'Nandurbar'),
(394, 'Maharashtra', 'Nashik'),
(395, 'Maharashtra', 'Osmanabad'),
(396, 'Maharashtra', 'Palghar'),
(397, 'Maharashtra', 'Parbhani'),
(398, 'Maharashtra', 'Pune'),
(399, 'Maharashtra', 'Raigad'),
(400, 'Maharashtra', 'Ratnagiri'),
(401, 'Maharashtra', 'Sangli'),
(402, 'Maharashtra', 'Satara'),
(403, 'Maharashtra', 'Sindhudurg'),
(404, 'Maharashtra', 'Solapur'),
(405, 'Maharashtra', 'Thane'),
(406, 'Maharashtra', 'Wardha'),
(407, 'Maharashtra', 'Washim'),
(408, 'Maharashtra', 'Yavatmal'),
(409, 'Manipur', 'Bishnupur'),
(410, 'Manipur', 'Chandel'),
(411, 'Manipur', 'Churachandpur'),
(412, 'Manipur', 'Imphal East'),
(413, 'Manipur', 'Imphal West'),
(414, 'Manipur', 'Jiribam'),
(415, 'Manipur', 'Kakching'),
(416, 'Manipur', 'Kamjong'),
(417, 'Manipur', 'Kangpokpi'),
(418, 'Manipur', 'Noney'),
(419, 'Manipur', 'Pherzawl'),
(420, 'Manipur', 'Senapati'),
(421, 'Manipur', 'Tamenglong'),
(422, 'Manipur', 'Tengnoupal'),
(423, 'Manipur', 'Thoubal'),
(424, 'Manipur', 'Ukhrul'),
(425, 'Meghalaya', 'East Garo Hills'),
(426, 'Meghalaya', 'East Jaintia Hills'),
(427, 'Meghalaya', 'East Khasi Hills'),
(428, 'Meghalaya', 'North Garo Hills'),
(429, 'Meghalaya', 'Ri Bhoi'),
(430, 'Meghalaya', 'South Garo Hills'),
(431, 'Meghalaya', 'South West Garo Hills'),
(432, 'Meghalaya', 'South West Khasi Hills'),
(433, 'Meghalaya', 'West Garo Hills'),
(434, 'Meghalaya', 'West Jaintia Hills'),
(435, 'Meghalaya', 'West Khasi Hills'),
(436, 'Mizoram', 'Aizawl'),
(437, 'Mizoram', 'Champhai'),
(438, 'Mizoram', 'Hnahthial'),
(439, 'Mizoram', 'Kolasib'),
(440, 'Mizoram', 'Khawzawl'),
(441, 'Mizoram', 'Lawngtlai'),
(442, 'Mizoram', 'Lunglei'),
(443, 'Mizoram', 'Mamit'),
(444, 'Mizoram', 'Saiha'),
(445, 'Mizoram', 'Serchhip'),
(446, 'Mizoram', 'Saitual'),
(447, 'Nagaland', 'Mon'),
(448, 'Nagaland', 'Dimapur'),
(449, 'Nagaland', 'Kiphire'),
(450, 'Nagaland', 'Kohima'),
(451, 'Nagaland', 'Longleng'),
(452, 'Nagaland', 'Mokokchung'),
(453, 'Nagaland', 'Noklak'),
(454, 'Nagaland', 'Peren'),
(455, 'Nagaland', 'Phek'),
(456, 'Nagaland', 'Tuensang'),
(457, 'Nagaland', 'Wokha'),
(458, 'Nagaland', 'Zunheboto'),
(459, 'Odisha', 'Angul'),
(460, 'Odisha', 'Balangir'),
(461, 'Odisha', 'Balasore'),
(462, 'Odisha', 'Bargarh'),
(463, 'Odisha', 'Bhadrak'),
(464, 'Odisha', 'Boudh'),
(465, 'Odisha', 'Cuttack'),
(466, 'Odisha', 'Debagarh'),
(467, 'Odisha', 'Dhenkanal'),
(468, 'Odisha', 'Gajapati'),
(469, 'Odisha', 'Ganjam'),
(470, 'Odisha', 'Jagatsinghpur'),
(471, 'Odisha', 'Jajpur'),
(472, 'Odisha', 'Jharsuguda'),
(473, 'Odisha', 'Kalahandi'),
(474, 'Odisha', 'Kandhamal'),
(475, 'Odisha', 'Kendrapara'),
(476, 'Odisha', 'Kendujhar'),
(477, 'Odisha', 'Khordha'),
(478, 'Odisha', 'Koraput'),
(479, 'Odisha', 'Malkangiri'),
(480, 'Odisha', 'Mayurbhanj'),
(481, 'Odisha', 'Nabarangpur'),
(482, 'Odisha', 'Nayagarh'),
(483, 'Odisha', 'Nuapada'),
(484, 'Odisha', 'Puri'),
(485, 'Odisha', 'Rayagada'),
(486, 'Odisha', 'Sambalpur'),
(487, 'Odisha', 'Subarnapur'),
(488, 'Odisha', 'Sundergarh'),
(489, 'Puducherry', 'Karaikal'),
(490, 'Puducherry', 'Mahe'),
(491, 'Puducherry', 'Puducherry'),
(492, 'Puducherry', 'Yanam'),
(493, 'Punjab', 'Amritsar'),
(494, 'Punjab', 'Barnala'),
(495, 'Punjab', 'Bathinda'),
(496, 'Punjab', 'Faridkot'),
(497, 'Punjab', 'Fatehgarh Sahib'),
(498, 'Punjab', 'Fazilka'),
(499, 'Punjab', 'Firozpur'),
(500, 'Punjab', 'Gurdaspur'),
(501, 'Punjab', 'Hoshiarpur'),
(502, 'Punjab', 'Jalandhar'),
(503, 'Punjab', 'Kapurthala'),
(504, 'Punjab', 'Ludhiana'),
(505, 'Punjab', 'Malerkotla'),
(506, 'Punjab', 'Mansa'),
(507, 'Punjab', 'Moga'),
(508, 'Punjab', 'Mohali'),
(509, 'Punjab', 'Muktsar'),
(510, 'Punjab', 'Pathankot'),
(511, 'Punjab', 'Patiala'),
(512, 'Punjab', 'Rupnagar'),
(513, 'Punjab', 'Sangrur'),
(514, 'Punjab', 'Shaheed Bhagat Singh Nagar'),
(515, 'Punjab', 'Tarn Taran'),
(516, 'Rajasthan', 'Ajmer'),
(517, 'Rajasthan', 'Alwar'),
(518, 'Rajasthan', 'Banswara'),
(519, 'Rajasthan', 'Baran'),
(520, 'Rajasthan', 'Barmer'),
(521, 'Rajasthan', 'Bharatpur'),
(522, 'Rajasthan', 'Bhilwara'),
(523, 'Rajasthan', 'Bikaner'),
(524, 'Rajasthan', 'Bundi'),
(525, 'Rajasthan', 'Chittorgarh'),
(526, 'Rajasthan', 'Churu'),
(527, 'Rajasthan', 'Dausa'),
(528, 'Rajasthan', 'Dholpur'),
(529, 'Rajasthan', 'Dungarpur'),
(530, 'Rajasthan', 'Hanumangarh'),
(531, 'Rajasthan', 'Jaipur'),
(532, 'Rajasthan', 'Jaisalmer'),
(533, 'Rajasthan', 'Jalore'),
(534, 'Rajasthan', 'Jhalawar'),
(535, 'Rajasthan', 'Jhunjhunu'),
(536, 'Rajasthan', 'Jodhpur'),
(537, 'Rajasthan', 'Karauli'),
(538, 'Rajasthan', 'Kota'),
(539, 'Rajasthan', 'Nagaur'),
(540, 'Rajasthan', 'Pali'),
(541, 'Rajasthan', 'Pratapgarh'),
(542, 'Rajasthan', 'Rajsamand'),
(543, 'Rajasthan', 'Sawai Madhopur'),
(544, 'Rajasthan', 'Sikar'),
(545, 'Rajasthan', 'Sirohi'),
(546, 'Rajasthan', 'Sri Ganganagar'),
(547, 'Rajasthan', 'Tonk'),
(548, 'Rajasthan', 'Udaipur'),
(549, 'Sikkim', 'East Sikkim'),
(550, 'Sikkim', 'North Sikkim'),
(551, 'Sikkim', 'South Sikkim'),
(552, 'Sikkim', 'West Sikkim'),
(553, 'Tamil Nadu', 'Ariyalur'),
(554, 'Tamil Nadu', 'Chengalpattu'),
(555, 'Tamil Nadu', 'Chennai'),
(556, 'Tamil Nadu', 'Coimbatore'),
(557, 'Tamil Nadu', 'Cuddalore'),
(558, 'Tamil Nadu', 'Dharmapuri'),
(559, 'Tamil Nadu', 'Dindigul'),
(560, 'Tamil Nadu', 'Erode'),
(561, 'Tamil Nadu', 'Kallakurichi'),
(562, 'Tamil Nadu', 'Kanchipuram'),
(563, 'Tamil Nadu', 'Kanyakumari'),
(564, 'Tamil Nadu', 'Karur'),
(565, 'Tamil Nadu', 'Krishnagiri'),
(566, 'Tamil Nadu', 'Madurai'),
(567, 'Tamil Nadu', 'Mayiladuthurai'),
(568, 'Tamil Nadu', 'Nagapattinam'),
(569, 'Tamil Nadu', 'Namakkal'),
(570, 'Tamil Nadu', 'Nilgiris'),
(571, 'Tamil Nadu', 'Perambalur'),
(572, 'Tamil Nadu', 'Pudukkottai'),
(573, 'Tamil Nadu', 'Ramanathapuram'),
(574, 'Tamil Nadu', 'Ranipet'),
(575, 'Tamil Nadu', 'Salem'),
(576, 'Tamil Nadu', 'Sivaganga'),
(577, 'Tamil Nadu', 'Tenkasi'),
(578, 'Tamil Nadu', 'Thanjavur'),
(579, 'Tamil Nadu', 'Theni'),
(580, 'Tamil Nadu', 'Thoothukudi'),
(581, 'Tamil Nadu', 'Tiruchirappalli'),
(582, 'Tamil Nadu', 'Tirunelveli'),
(583, 'Tamil Nadu', 'Tirupattur'),
(584, 'Tamil Nadu', 'Tiruppur'),
(585, 'Tamil Nadu', 'Tiruvallur'),
(586, 'Tamil Nadu', 'Tiruvannamalai'),
(587, 'Tamil Nadu', 'Tiruvarur'),
(588, 'Tamil Nadu', 'Vellore'),
(589, 'Tamil Nadu', 'Viluppuram'),
(590, 'Tamil Nadu', 'Virudhunagar'),
(591, 'Telangana', 'Adilabad'),
(592, 'Telangana', 'Bhadradri Kothagudem'),
(593, 'Telangana', 'Hyderabad'),
(594, 'Telangana', 'Jagtial'),
(595, 'Telangana', 'Jangaon'),
(596, 'Telangana', 'Jayashankar'),
(597, 'Telangana', 'Jogulamba'),
(598, 'Telangana', 'Kamareddy'),
(599, 'Telangana', 'Karimnagar'),
(600, 'Telangana', 'Khammam'),
(601, 'Telangana', 'Komaram Bheem'),
(602, 'Telangana', 'Mahabubabad'),
(603, 'Telangana', 'Mahbubnagar'),
(604, 'Telangana', 'Mancherial'),
(605, 'Telangana', 'Medak'),
(606, 'Telangana', 'Medchal'),
(607, 'Telangana', 'Mulugu'),
(608, 'Telangana', 'Nagarkurnool'),
(609, 'Telangana', 'Nalgonda'),
(610, 'Telangana', 'Narayanpet'),
(611, 'Telangana', 'Nirmal'),
(612, 'Telangana', 'Nizamabad'),
(613, 'Telangana', 'Peddapalli'),
(614, 'Telangana', 'Rajanna Sircilla'),
(615, 'Telangana', 'Ranga Reddy'),
(616, 'Telangana', 'Sangareddy'),
(617, 'Telangana', 'Siddipet'),
(618, 'Telangana', 'Suryapet'),
(619, 'Telangana', 'Vikarabad'),
(620, 'Telangana', 'Wanaparthy'),
(621, 'Telangana', 'Warangal Rural'),
(622, 'Telangana', 'Warangal Urban'),
(623, 'Telangana', 'Yadadri Bhuvanagiri'),
(624, 'Tripura', 'Dhalai'),
(625, 'Tripura', 'Gomati'),
(626, 'Tripura', 'Khowai'),
(627, 'Tripura', 'North Tripura'),
(628, 'Tripura', 'Sepahijala'),
(629, 'Tripura', 'South Tripura'),
(630, 'Tripura', 'Unakoti'),
(631, 'Tripura', 'West Tripura'),
(632, 'Uttar Pradesh', 'Agra'),
(633, 'Uttar Pradesh', 'Aligarh'),
(634, 'Uttar Pradesh', 'Ambedkar Nagar'),
(635, 'Uttar Pradesh', 'Amethi'),
(636, 'Uttar Pradesh', 'Amroha'),
(637, 'Uttar Pradesh', 'Auraiya'),
(638, 'Uttar Pradesh', 'Ayodhya'),
(639, 'Uttar Pradesh', 'Azamgarh'),
(640, 'Uttar Pradesh', 'Baghpat'),
(641, 'Uttar Pradesh', 'Bahraich'),
(642, 'Uttar Pradesh', 'Ballia'),
(643, 'Uttar Pradesh', 'Balrampur'),
(644, 'Uttar Pradesh', 'Banda'),
(645, 'Uttar Pradesh', 'Barabanki'),
(646, 'Uttar Pradesh', 'Bareilly'),
(647, 'Uttar Pradesh', 'Basti'),
(648, 'Uttar Pradesh', 'Bhadohi'),
(649, 'Uttar Pradesh', 'Bijnor'),
(650, 'Uttar Pradesh', 'Budaun'),
(651, 'Uttar Pradesh', 'Bulandshahr'),
(652, 'Uttar Pradesh', 'Chandauli'),
(653, 'Uttar Pradesh', 'Chitrakoot'),
(654, 'Uttar Pradesh', 'Deoria'),
(655, 'Uttar Pradesh', 'Etah'),
(656, 'Uttar Pradesh', 'Etawah'),
(657, 'Uttar Pradesh', 'Farrukhabad'),
(658, 'Uttar Pradesh', 'Fatehpur'),
(659, 'Uttar Pradesh', 'Firozabad'),
(660, 'Uttar Pradesh', 'Gautam Buddha Nagar'),
(661, 'Uttar Pradesh', 'Ghaziabad'),
(662, 'Uttar Pradesh', 'Ghazipur'),
(663, 'Uttar Pradesh', 'Gonda'),
(664, 'Uttar Pradesh', 'Gorakhpur'),
(665, 'Uttar Pradesh', 'Hamirpur'),
(666, 'Uttar Pradesh', 'Hapur'),
(667, 'Uttar Pradesh', 'Hardoi'),
(668, 'Uttar Pradesh', 'Hathras'),
(669, 'Uttar Pradesh', 'Jalaun'),
(670, 'Uttar Pradesh', 'Jaunpur'),
(671, 'Uttar Pradesh', 'Jhansi'),
(672, 'Uttar Pradesh', 'Kannauj'),
(673, 'Uttar Pradesh', 'Kanpur Dehat'),
(674, 'Uttar Pradesh', 'Kanpur Nagar'),
(675, 'Uttar Pradesh', 'Kasganj'),
(676, 'Uttar Pradesh', 'Kaushambi'),
(677, 'Uttar Pradesh', 'Kheri'),
(678, 'Uttar Pradesh', 'Kushinagar'),
(679, 'Uttar Pradesh', 'Lalitpur'),
(680, 'Uttar Pradesh', 'Lucknow'),
(681, 'Uttar Pradesh', 'Maharajganj'),
(682, 'Uttar Pradesh', 'Mahoba'),
(683, 'Uttar Pradesh', 'Mainpuri'),
(684, 'Uttar Pradesh', 'Mathura'),
(685, 'Uttar Pradesh', 'Mau'),
(686, 'Uttar Pradesh', 'Meerut'),
(687, 'Uttar Pradesh', 'Mirzapur'),
(688, 'Uttar Pradesh', 'Moradabad'),
(689, 'Uttar Pradesh', 'Muzaffarnagar'),
(690, 'Uttar Pradesh', 'Pilibhit'),
(691, 'Uttar Pradesh', 'Pratapgarh'),
(692, 'Uttar Pradesh', 'Prayagraj'),
(693, 'Uttar Pradesh', 'Raebareli'),
(694, 'Uttar Pradesh', 'Rampur'),
(695, 'Uttar Pradesh', 'Saharanpur'),
(696, 'Uttar Pradesh', 'Sambhal'),
(697, 'Uttar Pradesh', 'Sant Kabir Nagar'),
(698, 'Uttar Pradesh', 'Shahjahanpur'),
(699, 'Uttar Pradesh', 'Shamli'),
(700, 'Uttar Pradesh', 'Shravasti'),
(701, 'Uttar Pradesh', 'Siddharthnagar'),
(702, 'Uttar Pradesh', 'Sitapur'),
(703, 'Uttar Pradesh', 'Sonbhadra'),
(704, 'Uttar Pradesh', 'Sultanpur'),
(705, 'Uttar Pradesh', 'Unnao'),
(706, 'Uttar Pradesh', 'Varanasi'),
(707, 'Uttarakhand', 'Almora'),
(708, 'Uttarakhand', 'Bageshwar'),
(709, 'Uttarakhand', 'Chamoli'),
(710, 'Uttarakhand', 'Champawat'),
(711, 'Uttarakhand', 'Dehradun'),
(712, 'Uttarakhand', 'Haridwar'),
(713, 'Uttarakhand', 'Nainital'),
(714, 'Uttarakhand', 'Pauri'),
(715, 'Uttarakhand', 'Pithoragarh'),
(716, 'Uttarakhand', 'Rudraprayag'),
(717, 'Uttarakhand', 'Tehri'),
(718, 'Uttarakhand', 'Udham Singh Nagar'),
(719, 'Uttarakhand', 'Uttarkashi'),
(720, 'West Bengal', 'Alipurduar'),
(721, 'West Bengal', 'Bankura'),
(722, 'West Bengal', 'Birbhum'),
(723, 'West Bengal', 'Cooch Behar'),
(724, 'West Bengal', 'Dakshin Dinajpur'),
(725, 'West Bengal', 'Darjeeling'),
(726, 'West Bengal', 'Hooghly'),
(727, 'West Bengal', 'Howrah'),
(728, 'West Bengal', 'Jalpaiguri'),
(729, 'West Bengal', 'Jhargram'),
(730, 'West Bengal', 'Kalimpong'),
(731, 'West Bengal', 'Kolkata'),
(732, 'West Bengal', 'Malda'),
(733, 'West Bengal', 'Murshidabad'),
(734, 'West Bengal', 'Nadia'),
(735, 'West Bengal', 'North 24 Parganas'),
(736, 'West Bengal', 'Paschim Bardhaman'),
(737, 'West Bengal', 'Paschim Medinipur'),
(738, 'West Bengal', 'Purba Bardhaman'),
(739, 'West Bengal', 'Purba Medinipur'),
(740, 'West Bengal', 'Purulia'),
(741, 'West Bengal', 'South 24 Parganas'),
(742, 'West Bengal', 'Uttar Dinajpur');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `visible_role` text DEFAULT NULL,
  `seq` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `name`, `visible_role`, `seq`, `created_date`) VALUES
(1, 'PROCESSING', 'admin,divisional_head,salesman,telecaller', 3, '2022-01-15 16:32:05'),
(2, 'INTERESTED', 'admin,divisional_head,salesman,telecaller', 0, '2022-01-15 16:32:05'),
(3, 'CALL SCHEDULED', 'admin,divisional_head,salesman,telecaller', 4, '2022-01-15 16:32:05'),
(4, 'VISIT SCHEDULED', 'admin,divisional_head,salesman', 5, '2022-01-15 16:32:05'),
(5, 'VISIT DONE', 'admin,divisional_head,salesman', 10, '2022-01-15 16:32:05'),
(6, 'CONVERTED', 'admin,divisional_head,salesman', 6, '2022-01-15 16:32:05'),
(7, 'NEW LEAD', NULL, 1, '2022-01-15 16:32:05'),
(8, 'NOT REACHABLE', 'admin,divisional_head,salesman,telecaller', 0, '2022-01-15 16:32:05'),
(9, 'NOT PICKED', 'admin,divisional_head,salesman,telecaller', 0, '2022-01-15 16:32:05'),
(10, 'LOST', 'admin,divisional_head,salesman,telecaller', 8, '2022-01-15 16:32:05'),
(11, 'CHANNEL PARTNER', 'admin,divisional_head,salesman,telecaller', 0, '2022-01-15 16:32:05'),
(12, 'WRONG NUMBER', 'admin,divisional_head,salesman,telecaller', 0, '2022-01-15 16:32:05'),
(13, 'NOT INTERESTED', 'admin,divisional_head,salesman,telecaller', 0, '2022-01-15 16:32:05'),
(14, 'PENDING ', 'admin,divisional_head,salesman,telecaller', 2, '2022-01-15 16:32:05'),
(15, 'SM NEW LEADS', '', 0, '2022-01-15 16:32:05'),
(16, 'FUTURE LEAD', 'admin,divisional_head,salesman,telecaller', 0, '2022-01-15 16:32:05');

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(10) UNSIGNED NOT NULL,
  `ticket_id` varchar(50) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `software_name` text NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` enum('Low','Medium','High','Critical') DEFAULT 'Low',
  `status` enum('Open','In Progress','Resolved','Closed') DEFAULT 'Open',
  `assigned_to` int(10) UNSIGNED DEFAULT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `task_project_id` int(11) DEFAULT NULL,
  `task_type` enum('project','individual') DEFAULT 'individual',
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('pending','in_progress','completed') NOT NULL DEFAULT 'pending',
  `budget` decimal(10,2) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `repeat_interval` text DEFAULT NULL,
  `repeat_count` bigint(20) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `task_project_id`, `task_type`, `name`, `description`, `start_date`, `end_date`, `priority`, `status`, `budget`, `tags`, `repeat_interval`, `repeat_count`, `created_by`, `created_at`, `updated_at`) VALUES
(52, 1, 8, 'project', 'Test', 'Test', '2025-11-27', '2025-11-27', 'low', 'pending', NULL, NULL, 'daily', 10, 1, '2025-11-27 10:34:36', '2025-11-27 10:34:36');

-- --------------------------------------------------------

--
-- Table structure for table `task_comment`
--

CREATE TABLE `task_comment` (
  `id` int(11) NOT NULL,
  `task_id` bigint(20) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_projects`
--

CREATE TABLE `task_projects` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('planning','active','on_hold','completed','cancelled') DEFAULT 'planning',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task_projects`
--

INSERT INTO `task_projects` (`id`, `name`, `description`, `status`, `priority`, `created_by`, `created_at`, `updated_at`) VALUES
(8, 'Skyline Edge', 'Test', 'active', 'low', 1, '2025-11-27 10:33:56', '2025-11-27 10:33:56');

-- --------------------------------------------------------

--
-- Table structure for table `task_user`
--

CREATE TABLE `task_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `task_user`
--

INSERT INTO `task_user` (`id`, `task_id`, `user_id`, `file_path`, `file_name`, `file_type`, `created_at`, `updated_at`) VALUES
(65, 52, 18, NULL, NULL, NULL, '2025-11-27 10:34:36', '2025-11-27 10:34:36'),
(66, 52, 17, NULL, NULL, NULL, '2025-11-27 10:34:36', '2025-11-27 10:34:36'),
(67, 52, 18, NULL, NULL, NULL, '2025-11-27 10:34:36', '2025-11-27 10:34:36');

-- --------------------------------------------------------

--
-- Table structure for table `transfer_leads`
--

CREATE TABLE `transfer_leads` (
  `id` int(11) NOT NULL,
  `lead_id` bigint(20) NOT NULL,
  `from` bigint(20) NOT NULL,
  `to` bigint(20) NOT NULL,
  `previous_status` text DEFAULT NULL,
  `new_status` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trials`
--

CREATE TABLE `trials` (
  `id` int(11) NOT NULL,
  `software_name` varchar(255) NOT NULL,
  `feature_id` bigint(20) DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('active','inactive','expired','cancelled') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trials`
--

INSERT INTO `trials` (`id`, `software_name`, `feature_id`, `client_name`, `email`, `phone`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES
(6, 'RealEstatePro', 1, 'admin', 'admin@gmail.com', '9999999999', '2025-10-17 10:46:42', '2025-10-17 10:46:42', 'inactive', '2025-10-17 10:46:42', '2025-10-17 10:46:42'),
(7, 'RealEstatePro', 2, 'admin', 'admin@gmail.com', '9999999999', '2025-10-17 10:47:38', '2025-10-17 10:47:38', 'inactive', '2025-10-17 10:47:38', '2025-10-17 10:47:38'),
(8, 'demoLeadmanage', 8, 'admin', 'admin@gmail.com', '9999999999', '2025-10-27 17:49:00', '2025-10-27 17:49:00', 'inactive', '2025-10-27 17:49:00', '2025-10-27 17:49:00'),
(9, 'demoLeadmanage', 3, 'admin', 'admin@gmail.com', '9999999999', '2025-10-27 17:58:34', '2025-10-27 17:58:34', 'inactive', '2025-10-27 17:58:34', '2025-10-27 17:58:34'),
(10, 'demoLeadmanage', 2, 'admin', 'admin@gmail.com', '9999999999', '2025-10-27 18:34:32', '2025-10-27 18:34:32', 'inactive', '2025-10-27 18:34:32', '2025-10-27 18:34:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `role` varchar(255) NOT NULL,
  `tm_id` int(11) NOT NULL DEFAULT 0,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `token` text DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `designation_id` int(11) DEFAULT NULL,
  `current_location` text DEFAULT NULL,
  `password_reset_token` text DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `mobile`, `email`, `password`, `role`, `tm_id`, `is_active`, `token`, `last_login`, `designation_id`, `current_location`, `password_reset_token`, `created_date`, `updated_date`) VALUES
(1, 'admin', '9999999999', 'admin@gmail.com', '123456', 'admin', 0, 1, '9496740bdc44c943d82d21c4', '2026-02-02 14:32:40', NULL, ',', '46YTqDLrCmBlMQwc31zx9dwpO0SHgFUoinEnx2M3KIugtFEbMLaKps3BpOPtt5vV', '2025-03-13 09:14:16', '2026-02-02 14:32:40'),
(15, 'Testing account', '7060702034', 'saurabhrawat@clikzopinnovations.com', '123456', 'salesman', 1, 1, 'd096c86ff1c2d7fd6654a5e8', '2025-10-29 12:30:40', 1, '30.6382015,76.8249854', '8zFirPrteBV1C88g7D4csYychkYKLTUOUDwgPhuCrNkzgGxXdIBhzN1FslzJGYxg', '2025-08-08 13:20:51', '2025-10-29 12:30:40'),
(17, 'Skyline Edge', '7060702032', 'skyline@gmail.com', '123456', 'salesman', 1, 1, '1b279f846bc38bb5f80e916c', '2025-11-03 11:51:01', 1, '30.6382346,76.8251017', NULL, '2025-08-12 08:15:55', '2026-01-30 16:27:46'),
(18, 'saurabh talecaller', '8171620116', 'talecaller@gmail.com', '123456', 'divisional_head', 1, 1, 'e4534c412e7350afba71b81c', '2026-02-02 11:18:09', 2, ',', NULL, '2025-08-14 09:25:11', '2026-02-02 11:18:09'),
(19, 'Skyline Edge', '7060702032', 'rtsaurabh@gmail.com', '123456', 'reception', 1, 1, 'f86c008ad2691c1404f10984', '2025-09-18 16:47:21', NULL, '30.7003392,76.775424', NULL, '2025-09-17 16:42:49', '2025-09-18 16:47:21'),
(25, 'Ravinder Singh 33', '7060702033', 'saurabhrawat@clikzopinnovations.com3', '123456', 'task_management', 1, 1, 'eb0e3b397e1c7dbe57090bbd', '2025-10-29 13:35:17', 2, '30.6382248,76.8250111', NULL, '2025-10-27 10:24:10', '2025-10-29 13:35:17'),
(29, 'test', '3333333333', 'ad33min@gmail.com', '123456', 'divisional_head', 19, 1, NULL, NULL, NULL, NULL, NULL, '2026-01-16 16:05:46', '2026-01-16 16:05:46'),
(30, 'saurabh reception', '9105665309', 'recp@gmail.com', '123456', 'reception', 1, 1, '065601a5f08535fac2ccdb03', '2026-01-30 10:36:58', 4, ',', NULL, '2026-01-30 10:36:40', '2026-01-30 10:36:58'),
(31, 'sa', '2222222222', 'admin3@gmail.com', '123456', 'divisional_head', 1, 1, NULL, NULL, 4, NULL, NULL, '2026-01-30 16:27:23', '2026-01-30 16:27:23');

-- --------------------------------------------------------

--
-- Table structure for table `user_notification`
--

CREATE TABLE `user_notification` (
  `id` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `notification_title` text DEFAULT NULL,
  `message` text DEFAULT NULL,
  `ack` tinyint(4) NOT NULL DEFAULT 0,
  `CreatedDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_notification`
--

INSERT INTO `user_notification` (`id`, `UserId`, `notification_title`, `message`, `ack`, `CreatedDate`) VALUES
(48, 15, '📋 Lead Shared: asdf', '👤 **Shared by:** admin\nLead Phone:** 7777777777\n📧 **Lead Email:** R@gmail.com\n🏢 **Project:** N/A\n💰 **Budget:** N/A', 0, '2026-02-02 11:17:59'),
(49, 1, 'Lead Shared Successfully', 'You shared lead \'asdf\' with Testing account', 0, '2026-02-02 11:17:59'),
(50, 17, '📋 Lead Shared: asdf', '👤 **Shared by:** admin\nLead Phone:** 7777777777\n📧 **Lead Email:** R@gmail.com\n🏢 **Project:** N/A\n💰 **Budget:** N/A', 0, '2026-02-02 11:17:59'),
(51, 1, 'Lead Shared Successfully', 'You shared lead \'asdf\' with Skyline Edge', 0, '2026-02-02 11:17:59'),
(52, 18, '📋 Lead Shared: asdf', '👤 **Shared by:** admin\nLead Phone:** 7777777777\n📧 **Lead Email:** R@gmail.com\n🏢 **Project:** N/A\n💰 **Budget:** N/A', 0, '2026-02-02 11:17:59'),
(53, 1, 'Lead Shared Successfully', 'You shared lead \'asdf\' with saurabh talecaller', 0, '2026-02-02 11:17:59'),
(54, 25, '📋 Lead Shared: asdf', '👤 **Shared by:** admin\nLead Phone:** 7777777777\n📧 **Lead Email:** R@gmail.com\n🏢 **Project:** N/A\n💰 **Budget:** N/A', 0, '2026-02-02 11:17:59'),
(55, 1, 'Lead Shared Successfully', 'You shared lead \'asdf\' with Ravinder Singh 33', 0, '2026-02-02 11:17:59'),
(56, 15, '📋 Lead Shared: saurabh singh rawat', '👤 **Shared by:** admin\nLead Phone:** 2222222222\n📧 **Lead Email:** saurabh@gmail.com\n🏢 **Project:** N/A\n💰 **Budget:** N/A', 0, '2026-02-02 15:56:16'),
(57, 1, 'Lead Shared Successfully', 'You shared lead \'saurabh singh rawat\' with Testing account', 0, '2026-02-02 15:56:16'),
(58, 17, '📋 Lead Shared: saurabh singh rawat', '👤 **Shared by:** admin\nLead Phone:** 2222222222\n📧 **Lead Email:** saurabh@gmail.com\n🏢 **Project:** N/A\n💰 **Budget:** N/A', 0, '2026-02-02 15:56:16'),
(59, 1, 'Lead Shared Successfully', 'You shared lead \'saurabh singh rawat\' with Skyline Edge', 0, '2026-02-02 15:56:16');

-- --------------------------------------------------------

--
-- Table structure for table `user_promotion`
--

CREATE TABLE `user_promotion` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `old_role` text NOT NULL,
  `new_role` text NOT NULL,
  `is_approved` int(11) NOT NULL DEFAULT 0,
  `tm_id` int(11) NOT NULL DEFAULT 0,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_promotion`
--

INSERT INTO `user_promotion` (`id`, `user_id`, `old_role`, `new_role`, `is_approved`, `tm_id`, `created_date`, `updated_date`) VALUES
(3, 18, 'salesman', 'divisional_head', 1, 13, '2025-08-25 06:23:31', '2025-08-25 11:54:19'),
(4, 17, 'staff', 'salesman', 1, 1, '2025-09-18 10:31:01', '2026-01-30 10:57:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advertisements`
--
ALTER TABLE `advertisements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance_types`
--
ALTER TABLE `attendance_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`) USING HASH;

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checklist`
--
ALTER TABLE `checklist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conversions`
--
ALTER TABLE `conversions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dayend_reports`
--
ALTER TABLE `dayend_reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_day_agent` (`report_date`,`agent_id`);

--
-- Indexes for table `designation`
--
ALTER TABLE `designation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `designation` (`designation`) USING HASH;

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_img`
--
ALTER TABLE `expense_img`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facebook_sync_tracking`
--
ALTER TABLE `facebook_sync_tracking`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_form_id` (`form_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_software_name` (`software_name`);

--
-- Indexes for table `inquiry_questions`
--
ALTER TABLE `inquiry_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `integration_settings`
--
ALTER TABLE `integration_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_det`
--
ALTER TABLE `inventory_det`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_history`
--
ALTER TABLE `inventory_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inv_catg`
--
ALTER TABLE `inv_catg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inv_subcatg`
--
ALTER TABLE `inv_subcatg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lead_comments`
--
ALTER TABLE `lead_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lead_projects`
--
ALTER TABLE `lead_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_projects_lead_id_foreign` (`lead_id`),
  ADD KEY `lead_projects_project_id_foreign` (`project_id`),
  ADD KEY `lead_projects_created_by_foreign` (`created_by`);

--
-- Indexes for table `location_history`
--
ALTER TABLE `location_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mis_daily_entries`
--
ALTER TABLE `mis_daily_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_date` (`user_id`,`entry_date`),
  ADD KEY `idx_team_date` (`week`,`entry_date`),
  ADD KEY `idx_entry_date` (`entry_date`);

--
-- Indexes for table `mis_points`
--
ALTER TABLE `mis_points`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `point_name_unique` (`point_name`);

--
-- Indexes for table `mis_weekly_targets`
--
ALTER TABLE `mis_weekly_targets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `team_id_idx` (`team_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `post_sales`
--
ALTER TABLE `post_sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_sale_documents`
--
ALTER TABLE `post_sale_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_post_sale_id` (`post_sale_id`);

--
-- Indexes for table `post_sale_ratings`
--
ALTER TABLE `post_sale_ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_sale_ratings_post_sale_id_foreign` (`post_sale_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_inventories`
--
ALTER TABLE `project_inventories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_inventories_slug_unique` (`slug`);

--
-- Indexes for table `role_mst`
--
ALTER TABLE `role_mst`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shared_leads`
--
ALTER TABLE `shared_leads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indexes for table `shared_post_sale`
--
ALTER TABLE `shared_post_sale`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_post_sale_id` (`post_sale_id`);

--
-- Indexes for table `software_details`
--
ALTER TABLE `software_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `software_features`
--
ALTER TABLE `software_features`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `software_feature_unique` (`software_name`,`feature_name`);

--
-- Indexes for table `software_requests`
--
ALTER TABLE `software_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sources`
--
ALTER TABLE `sources`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `source` (`name`) USING HASH;

--
-- Indexes for table `state_district`
--
ALTER TABLE `state_district`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_comment`
--
ALTER TABLE `task_comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_projects`
--
ALTER TABLE `task_projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_user`
--
ALTER TABLE `task_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_user_task_id_foreign` (`task_id`);

--
-- Indexes for table `transfer_leads`
--
ALTER TABLE `transfer_leads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trials`
--
ALTER TABLE `trials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_notification`
--
ALTER TABLE `user_notification`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_notification` (`UserId`,`notification_title`,`message`) USING HASH;

--
-- Indexes for table `user_promotion`
--
ALTER TABLE `user_promotion`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advertisements`
--
ALTER TABLE `advertisements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `attendance_types`
--
ALTER TABLE `attendance_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1184;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `checklist`
--
ALTER TABLE `checklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `dayend_reports`
--
ALTER TABLE `dayend_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `designation`
--
ALTER TABLE `designation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `expense_img`
--
ALTER TABLE `expense_img`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `facebook_sync_tracking`
--
ALTER TABLE `facebook_sync_tracking`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `inquiry_questions`
--
ALTER TABLE `inquiry_questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `integration_settings`
--
ALTER TABLE `integration_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `inventory_det`
--
ALTER TABLE `inventory_det`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `inventory_history`
--
ALTER TABLE `inventory_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `inv_catg`
--
ALTER TABLE `inv_catg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `inv_subcatg`
--
ALTER TABLE `inv_subcatg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139320;

--
-- AUTO_INCREMENT for table `lead_comments`
--
ALTER TABLE `lead_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=523;

--
-- AUTO_INCREMENT for table `lead_projects`
--
ALTER TABLE `lead_projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `location_history`
--
ALTER TABLE `location_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=803;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `mis_daily_entries`
--
ALTER TABLE `mis_daily_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `mis_points`
--
ALTER TABLE `mis_points`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `mis_weekly_targets`
--
ALTER TABLE `mis_weekly_targets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_sales`
--
ALTER TABLE `post_sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `post_sale_documents`
--
ALTER TABLE `post_sale_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `post_sale_ratings`
--
ALTER TABLE `post_sale_ratings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `project_inventories`
--
ALTER TABLE `project_inventories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `role_mst`
--
ALTER TABLE `role_mst`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `shared_leads`
--
ALTER TABLE `shared_leads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `shared_post_sale`
--
ALTER TABLE `shared_post_sale`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `software_details`
--
ALTER TABLE `software_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `software_features`
--
ALTER TABLE `software_features`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `software_requests`
--
ALTER TABLE `software_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `sources`
--
ALTER TABLE `sources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `task_comment`
--
ALTER TABLE `task_comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `task_projects`
--
ALTER TABLE `task_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `task_user`
--
ALTER TABLE `task_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `transfer_leads`
--
ALTER TABLE `transfer_leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `trials`
--
ALTER TABLE `trials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `user_notification`
--
ALTER TABLE `user_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `user_promotion`
--
ALTER TABLE `user_promotion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `post_sale_documents`
--
ALTER TABLE `post_sale_documents`
  ADD CONSTRAINT `post_sale_documents_ibfk_1` FOREIGN KEY (`post_sale_id`) REFERENCES `post_sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_sale_ratings`
--
ALTER TABLE `post_sale_ratings`
  ADD CONSTRAINT `post_sale_ratings_post_sale_id_foreign` FOREIGN KEY (`post_sale_id`) REFERENCES `post_sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role_mst` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `task_user`
--
ALTER TABLE `task_user`
  ADD CONSTRAINT `task_user_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

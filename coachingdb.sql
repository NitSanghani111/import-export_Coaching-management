-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Dec 27, 2025 at 06:42 AM
-- Server version: 8.0.44
-- PHP Version: 8.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `coachingdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$UL0stokHf0ifrJehscihu.iRvco5CZR2ATgHn/X3t5bL83GuyCwya');

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` longtext NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('draft','published') DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `blog`
--

INSERT INTO `blog` (`id`, `title`, `slug`, `description`, `image`, `status`, `created_at`) VALUES
(2, 'hey businees', 'hey-businees', '<p>hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen hen&nbsp;</p>', '1766577767_f.png', 'published', '2025-12-24 12:02:47'),
(3, 'new startup', 'new-startup', '<p>now time cahnge you businees make a brand now time cahnge you businees make a brand now time cahnge you businees make a brand now time cahnge you businees make a brand now time cahnge you businees make a brand now time cahnge you businees make a brand now time cahnge you businees make a brand now time cahnge you businees make a brand now time cahnge you businees make a brand now time cahnge you businees make a brand now time cahnge you businees make a brand now time cahnge you businees make a brand now time cahnge you businees make a brand now time cahnge you businees make a brand now time cahnge you businees make a brand now time cahnge you businees make a brand now time cahnge you businees make a brand&nbsp;</p>', '1766664068_contact-design.png', 'published', '2025-12-25 12:01:08'),
(4, 'business grow', 'business-grow', '<p>grow your busineess nwow grow your busineess nwow grow your busineess nwow grow your busineess nwow grow your busineess nwow grow your busineess nwow grow your busineess nwow grow your busineess nwow grow your busineess nwow grow your busineess nwow grow your busineess nwow grow your busineess nwow grow your busineess nwow grow your busineess nwow grow your busineess nwow grow your busineess nwow grow your busineess nwow&nbsp;</p>', '1766665529_gdh3.jpg', 'published', '2025-12-25 12:25:29');

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `blog_id` int NOT NULL,
  `category_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `blog_categories`
--

INSERT INTO `blog_categories` (`blog_id`, `category_id`) VALUES
(2, 7);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(2, 'Business Growth & Scaling'),
(8, 'Case Studies & Real Stories\r\n'),
(7, 'Leadership, Team & Culture\r\n'),
(9, 'Life, Relationships & Lessons\r\n'),
(6, 'Mindset & Personal Growth\r\n'),
(4, 'Money, Finance & Business Numbers'),
(5, 'Productivity, Time & Systems\r\n'),
(3, 'Sales, Marketing & Branding\r\n'),
(1, 'Startup & Entrepreneurship');

-- --------------------------------------------------------

--
-- Table structure for table `workshops`
--

CREATE TABLE `workshops` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `short_description` text,
  `thumbnail` varchar(255) DEFAULT NULL,
  `workshop_date` date NOT NULL,
  `workshop_time` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `workshops`
--

INSERT INTO `workshops` (`id`, `title`, `short_description`, `thumbnail`, `workshop_date`, `workshop_time`, `created_at`, `updated_at`) VALUES
(7, 'How to Get international cleint', 'in this casse import in genrateion of lead in this casse import in genrateion of lead in this casse import in genrateion of lead in this casse import in genrateion of lead', 'dd_1766729910.png', '2025-12-26', '11:49:00', '2025-12-26 06:18:30', '2025-12-26 06:18:30'),
(10, 'new busineess', 'now time to grow  in this casse import in genrateion of lead', 'Screenshot_2025-02-18_121409_1766730927.png', '2025-12-26', '12:07:00', '2025-12-26 06:35:27', '2025-12-26 06:35:27'),
(11, 'bvb je v v', 'rse error: syntax error, unexpected token \"<\", expecting end of file in /var/www/html/pages/workshop.php on line 25rse error: syntax error, unexpected token \"<\", expecting end of file in /var/www/html/pages/workshop.php on line 25', 'Screenshot_2024-08-02_100352_1766731667.png', '2025-12-26', '12:18:00', '2025-12-26 06:47:47', '2025-12-26 06:47:47'),
(12, 'fvf ddc ddd dfff', 'You are working on a workshops page that has TWO sections:\r\n\r\n1) Upcoming Workshops\r\n2) Previously Hosted Workshops\r\n\r\nEach workshop has at least:\r\n- id\r\n- title\r\n- date (YYYY-MM-DD)\r\n- time (HH:MM or datetime)\r\n- image\r\n- description\r\n\r\nTASK / LOGIC REQUIREMENT:\r\n\r\n1. Treat \"Upcoming Workshops\" as workshops whose date + time is GREATER than the current date + time.\r\n2. Treat \"Previously Hosted Workshops\" as workshops whose date + time is LESS than OR EQUAL to the current date + time.\r\n\r\n3. When the current date/time passes a workshop’s scheduled date/time:\r\n   - It MUST be automatically REMOVED from the Upcoming Workshops section\r\n   - It MUST be automatically SHOWN in the Previously Hosted Workshops section\r\n   - No manual update should be required\r\n\r\n4. Do NOT change:\r\n   - UI layout\r\n   - Tailwind classes\r\n   - Card design\r\n   - Section headings\r\n   - Existing HTML structure\r\n\r\n5. ONLY update:\r\n   - Filtering logic\r\n   - Date/time comparison logic\r\n   - Data source separation (upcoming vs hosted)\r\n\r\n6. Use the current server time for comparison (not hardcoded dates).\r\n\r\n7. The solution should be:\r\n   - Clean\r\n   - Readable\r\n   - Production-safe\r\n   - Easy to maintain\r\n\r\nImplement this logic in the current codebase using the existing data structure.', 'Screenshot_2025-04-23_111550_1766733052.png', '2025-12-26', '12:41:00', '2025-12-26 07:10:52', '2025-12-26 07:10:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`blog_id`,`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `workshops`
--
ALTER TABLE `workshops`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `workshops`
--
ALTER TABLE `workshops`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

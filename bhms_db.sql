-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2026 at 03:25 AM
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
-- Database: `bhms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `created_at`) VALUES
(2, 'admin', '$2y$10$G.eR2ddiL28G4M9VVW0mzuRPSnKhB6awdQxbaBVhi5vGP63TxerIa', '2026-05-04 11:47:18');

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `target` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `action`, `target`, `details`, `created_at`) VALUES
(1, 2, 'Logged in', NULL, NULL, '2026-05-15 04:52:30'),
(2, 2, 'Logged in', NULL, NULL, '2026-05-15 05:02:48'),
(3, 2, 'Edited resident', 'Lucy Gomez Martinez', NULL, '2026-05-15 05:29:51'),
(4, 2, 'Edited resident', 'Angela Lopez Mendoza', NULL, '2026-05-15 05:30:06'),
(5, 2, 'Logged in', NULL, NULL, '2026-05-15 05:41:31'),
(6, 2, 'Logged in', NULL, NULL, '2026-05-15 05:48:21'),
(7, 2, 'Logged in', NULL, NULL, '2026-05-15 10:42:28'),
(8, 2, 'Logged in', NULL, NULL, '2026-05-20 16:19:21'),
(9, 2, 'Exported residents', 'CSV', '7 record(s)', '2026-05-20 16:32:50'),
(10, 2, 'Exported residents', 'PDF', '7 record(s)', '2026-05-20 16:32:54'),
(11, 2, 'Exported residents', 'PDF', '7 record(s)', '2026-05-20 16:33:25'),
(12, 2, 'Exported residents', 'PDF', '7 record(s)', '2026-05-20 16:34:41'),
(13, 2, 'Exported residents', 'CSV', '7 record(s)', '2026-05-20 16:34:46'),
(14, 2, 'Exported residents', 'PDF', '7 record(s)', '2026-05-20 16:35:15'),
(15, 2, 'Exported residents', 'PDF', '7 record(s)', '2026-05-20 16:35:51'),
(16, 2, 'Exported residents', 'PDF', '7 record(s)', '2026-05-20 16:36:42'),
(17, 2, 'Exported residents', 'PDF', '7 record(s)', '2026-05-20 16:37:27'),
(18, 2, 'Exported residents', 'CSV', '7 record(s)', '2026-05-20 16:40:24'),
(19, 2, 'Exported residents', 'PDF', '7 record(s)', '2026-05-20 16:40:26'),
(20, 2, 'Logged in', NULL, NULL, '2026-05-20 16:43:52'),
(21, 2, 'Logged in', NULL, NULL, '2026-05-20 16:44:15'),
(22, 2, 'Logged in', NULL, NULL, '2026-05-20 16:49:03'),
(23, 2, 'Exported residents', 'PDF', '7 record(s)', '2026-05-20 16:49:42'),
(24, 2, 'Exported residents', 'PDF', '7 record(s)', '2026-05-20 16:57:08'),
(25, 2, 'Logged in', NULL, NULL, '2026-05-20 16:58:03'),
(26, 2, 'Logged in', NULL, NULL, '2026-05-20 17:05:24'),
(27, 2, 'Exported residents', 'PDF', '7 record(s)', '2026-05-20 17:05:39'),
(28, 2, 'Logged in', NULL, NULL, '2026-05-21 04:16:11'),
(29, 2, 'Logged in', NULL, NULL, '2026-05-21 04:21:10'),
(30, 2, 'Exported residents', 'PDF', '8 record(s)', '2026-05-21 04:21:25'),
(31, 2, 'Exported residents', 'PDF', '8 record(s)', '2026-05-21 04:22:18'),
(32, 2, 'Logged in', NULL, NULL, '2026-05-21 04:28:24'),
(33, 2, 'Deleted resident', 'Abdul Jakul Salsalagi', NULL, '2026-05-21 04:28:35'),
(34, 2, 'Logged in', NULL, NULL, '2026-05-21 04:42:24'),
(35, 2, 'Logged in', NULL, NULL, '2026-05-22 01:23:26'),
(36, 2, 'Deleted resident', 'Mark Joshua Apor', NULL, '2026-05-22 01:23:36');

-- --------------------------------------------------------

--
-- Table structure for table `residents`
--

CREATE TABLE `residents` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL DEFAULT '',
  `middle_name` varchar(50) NOT NULL DEFAULT '',
  `last_name` varchar(50) NOT NULL DEFAULT '',
  `full_name` varchar(100) NOT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `age` tinyint(3) UNSIGNED NOT NULL,
  `civil_status` enum('Single','Married','Widowed','Separated') NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `purok` enum('Purok 1','Purok 2','Purok 3','Purok 4','Purok 5') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`id`, `first_name`, `middle_name`, `last_name`, `full_name`, `suffix`, `birthdate`, `age`, `civil_status`, `gender`, `purok`, `created_at`) VALUES
(1, 'David', '', 'Martinez', 'David Martinez', NULL, '1999-05-20', 26, 'Single', 'Male', 'Purok 4', '2026-05-15 04:52:18'),
(2, 'Pearl', '', 'Douglas', 'Pearl Douglas', 'Sr', '1965-03-12', 61, 'Married', 'Female', 'Purok 3', '2026-05-15 05:03:48'),
(3, 'Juan', '', 'Dela Cruz', 'Juan Dela Cruz', NULL, '2001-12-09', 24, 'Single', 'Male', 'Purok 1', '2026-05-15 05:16:12'),
(4, 'Lucy', 'Gomez', 'Martinez', 'Lucy Gomez Martinez', NULL, '2003-07-29', 22, 'Single', 'Female', 'Purok 3', '2026-05-15 05:19:29'),
(5, 'John', 'Lloyd', 'Cruz', 'John Lloyd Cruz', NULL, '1983-05-24', 42, 'Married', 'Male', 'Purok 2', '2026-05-15 05:25:19'),
(6, 'Angela', 'Lopez', 'Mendoza', 'Angela Lopez Mendoza', NULL, '1994-07-03', 31, 'Single', 'Female', 'Purok 5', '2026-05-15 05:29:23'),
(7, 'Samantha', 'Panelo', 'Santos', 'Samantha Panelo Santos', NULL, '2002-06-29', 23, 'Single', 'Female', 'Purok 4', '2026-05-15 05:34:50');

-- --------------------------------------------------------

--
-- Table structure for table `survey_responses`
--

CREATE TABLE `survey_responses` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `vaccination_status` enum('Vaccinated','Unvaccinated','Partially Vaccinated') NOT NULL,
  `last_checkup` date DEFAULT NULL,
  `has_fever` tinyint(1) DEFAULT 0,
  `has_cough` tinyint(1) DEFAULT 0,
  `has_fatigue` tinyint(1) DEFAULT 0,
  `has_headache` tinyint(1) DEFAULT 0,
  `no_symptoms` tinyint(1) DEFAULT 0,
  `health_notes` text DEFAULT NULL,
  `ec_first_name` varchar(50) DEFAULT NULL,
  `ec_middle_name` varchar(50) DEFAULT NULL,
  `ec_last_name` varchar(50) DEFAULT NULL,
  `ec_contact_number` varchar(15) DEFAULT NULL,
  `ec_relationship` varchar(30) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `survey_responses`
--

INSERT INTO `survey_responses` (`id`, `resident_id`, `vaccination_status`, `last_checkup`, `has_fever`, `has_cough`, `has_fatigue`, `has_headache`, `no_symptoms`, `health_notes`, `ec_first_name`, `ec_middle_name`, `ec_last_name`, `ec_contact_number`, `ec_relationship`, `submitted_at`) VALUES
(1, 1, 'Vaccinated', '2026-05-03', 0, 0, 0, 0, 1, '', NULL, NULL, NULL, NULL, NULL, '2026-05-15 04:52:18'),
(2, 2, 'Unvaccinated', '2026-03-20', 1, 1, 1, 1, 0, '', NULL, NULL, NULL, NULL, NULL, '2026-05-15 05:03:48'),
(3, 3, 'Vaccinated', '2026-02-03', 0, 0, 0, 0, 1, '', NULL, NULL, NULL, NULL, NULL, '2026-05-15 05:16:12'),
(4, 4, 'Vaccinated', '2025-12-03', 0, 0, 0, 0, 1, '', NULL, NULL, NULL, NULL, NULL, '2026-05-15 05:19:29'),
(5, 5, 'Vaccinated', '2026-01-29', 1, 0, 0, 1, 0, 'Nagsusuka', NULL, NULL, NULL, NULL, NULL, '2026-05-15 05:25:19'),
(6, 6, 'Unvaccinated', '2025-03-19', 0, 0, 0, 0, 1, '', NULL, NULL, NULL, NULL, NULL, '2026-05-15 05:29:23'),
(7, 7, 'Unvaccinated', '2026-01-25', 0, 1, 1, 0, 0, 'di makahinga', NULL, NULL, NULL, NULL, NULL, '2026-05-15 05:34:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_created` (`admin_id`,`created_at`);

--
-- Indexes for table `residents`
--
ALTER TABLE `residents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `survey_responses`
--
ALTER TABLE `survey_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_resident` (`resident_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `survey_responses`
--
ALTER TABLE `survey_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `survey_responses`
--
ALTER TABLE `survey_responses`
  ADD CONSTRAINT `fk_resident` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

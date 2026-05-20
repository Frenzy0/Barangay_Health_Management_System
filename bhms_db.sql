-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2026 at 07:35 AM
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
(2, 'admin', '$2y$10$L3WFUYS4K3vy8WjvNyozKu9xjR9bEifAJSDC1vjXZfLQMogN70gpO', '2026-05-04 11:47:18');

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
(4, 2, 'Edited resident', 'Angela Lopez Mendoza', NULL, '2026-05-15 05:30:06');

-- --------------------------------------------------------

--
-- Table structure for table `residents`
--

CREATE TABLE `residents` (
  `id` int(11) NOT NULL,
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

INSERT INTO `residents` (`id`, `full_name`, `suffix`, `birthdate`, `age`, `civil_status`, `gender`, `purok`, `created_at`) VALUES
(1, 'David Martinez', NULL, '1999-05-20', 26, 'Single', 'Male', 'Purok 4', '2026-05-15 04:52:18'),
(2, 'Pearl Douglas', 'Sr', '1965-03-12', 61, 'Married', 'Female', 'Purok 3', '2026-05-15 05:03:48'),
(3, 'Juan Dela Cruz', NULL, '2001-12-09', 24, 'Single', 'Male', 'Purok 1', '2026-05-15 05:16:12'),
(4, 'Lucy Gomez Martinez', NULL, '2003-07-29', 22, 'Single', 'Female', 'Purok 3', '2026-05-15 05:19:29'),
(5, 'John Lloyd Cruz', NULL, '1983-05-24', 42, 'Married', 'Male', 'Purok 2', '2026-05-15 05:25:19'),
(6, 'Angela Lopez Mendoza', NULL, '1994-07-03', 31, 'Single', 'Female', 'Purok 5', '2026-05-15 05:29:23'),
(7, 'Samantha Panelo Santos', NULL, '2002-06-29', 23, 'Single', 'Female', 'Purok 4', '2026-05-15 05:34:50');

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
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `survey_responses`
--

INSERT INTO `survey_responses` (`id`, `resident_id`, `vaccination_status`, `last_checkup`, `has_fever`, `has_cough`, `has_fatigue`, `has_headache`, `no_symptoms`, `health_notes`, `submitted_at`) VALUES
(1, 1, 'Vaccinated', '2026-05-03', 0, 0, 0, 0, 1, '', '2026-05-15 04:52:18'),
(2, 2, 'Unvaccinated', '2026-03-20', 1, 1, 1, 1, 0, '', '2026-05-15 05:03:48'),
(3, 3, 'Vaccinated', '2026-02-03', 0, 0, 0, 0, 1, '', '2026-05-15 05:16:12'),
(4, 4, 'Vaccinated', '2025-12-03', 0, 0, 0, 0, 1, '', '2026-05-15 05:19:29'),
(5, 5, 'Vaccinated', '2026-01-29', 1, 0, 0, 1, 0, 'Nagsusuka', '2026-05-15 05:25:19'),
(6, 6, 'Unvaccinated', '2025-03-19', 0, 0, 0, 0, 1, '', '2026-05-15 05:29:23'),
(7, 7, 'Unvaccinated', '2026-01-25', 0, 1, 1, 0, 0, 'di makahinga', '2026-05-15 05:34:50');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `survey_responses`
--
ALTER TABLE `survey_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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

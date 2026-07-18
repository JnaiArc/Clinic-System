-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 18, 2026 at 10:50 AM
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
-- Database: `swiftcare_clinic`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` varchar(20) NOT NULL,
  `purpose` text DEFAULT NULL,
  `complaint` varchar(150) DEFAULT NULL,
  `consultation_type` enum('Online','In Person') NOT NULL DEFAULT 'In Person',
  `status` enum('pending','confirmed','completed','cancelled','missed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_checked` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `purpose`, `complaint`, `consultation_type`, `status`, `created_at`, `admin_checked`) VALUES
(7, 20, 11, '2026-07-09', '3:30 PM', 'Check-up', NULL, 'In Person', '', '2026-07-09 05:52:39', 0),
(8, 19, 9, '2026-07-09', '1:00 PM', 'Check-up', NULL, 'In Person', '', '2026-07-09 05:53:03', 0),
(9, 18, 10, '2026-07-09', '9:00 AM', 'Check-up', NULL, 'In Person', '', '2026-07-09 05:53:22', 0),
(10, 17, 11, '2026-07-09', '12:00 PM', 'Check-up', NULL, 'In Person', 'completed', '2026-07-09 05:53:49', 0),
(11, 16, 9, '2026-07-09', '3:00 PM', 'Check-up', NULL, 'In Person', '', '2026-07-09 05:54:40', 0),
(12, 15, 10, '2026-07-09', '11:30 AM', 'Check-up', NULL, 'In Person', '', '2026-07-09 05:55:09', 0),
(13, 14, 7, '2026-07-12', '8:00 AM', 'Check-up', NULL, 'In Person', '', '2026-07-09 05:57:25', 0),
(14, 13, 6, '2026-07-13', '1:00 PM', 'Check-up', NULL, 'In Person', '', '2026-07-09 05:58:34', 0),
(15, 12, 8, '2026-07-12', '11:00 AM', 'Check-up', NULL, 'In Person', '', '2026-07-09 05:59:01', 0),
(16, 11, 7, '2026-07-22', '1:00 PM', 'Check-up', NULL, 'In Person', 'pending', '2026-07-09 06:00:56', 0),
(17, 10, 8, '2026-07-21', '2:00 PM', 'Check-up', NULL, 'In Person', 'pending', '2026-07-09 06:01:08', 0),
(18, 9, 6, '2026-07-27', '10:30 AM', 'Check-up', NULL, 'In Person', 'pending', '2026-07-09 06:02:05', 0),
(19, 8, 11, '2026-07-09', '2:00 PM', 'Check-up', NULL, 'In Person', '', '2026-07-09 06:07:21', 0),
(20, 6, 11, '2026-07-24', '2:00 PM', 'Check-up', NULL, 'In Person', 'pending', '2026-07-09 06:10:35', 0),
(21, 5, 11, '2026-07-25', '12:00 PM', 'Check-up', NULL, 'In Person', 'pending', '2026-07-09 06:10:56', 0),
(22, 4, 11, '2026-07-18', '12:30 PM', 'Check-up', NULL, 'In Person', 'pending', '2026-07-09 06:11:27', 0),
(23, 3, 11, '2026-07-09', '1:00 PM', 'Check-up', NULL, 'In Person', 'completed', '2026-07-09 06:13:37', 0),
(24, 1, 11, '2026-07-09', '12:30 PM', 'Check-up', NULL, 'In Person', 'completed', '2026-07-09 06:15:22', 0),
(25, 1, 11, '2026-07-23', '1:00 PM', 'Follow-up', NULL, 'In Person', 'pending', '2026-07-09 06:24:10', 0),
(26, 3, 11, '2026-07-23', '3:00 PM', 'Follow-up', NULL, 'In Person', 'pending', '2026-07-09 06:26:08', 0),
(27, 20, 6, '2026-07-15', '12:30 PM', 'Check-up', NULL, 'In Person', 'completed', '2026-07-15 05:20:33', 0),
(28, 20, 9, '2026-07-17', '1:00 PM', 'Check-up', NULL, 'In Person', 'missed', '2026-07-17 10:53:22', 0),
(29, 21, 11, '2026-07-18', '2:00 PM', 'Consultation', NULL, 'Online', 'completed', '2026-07-18 06:12:24', 0),
(30, 5, 11, '2026-07-18', '3:30 PM', 'Check-up', NULL, 'Online', 'pending', '2026-07-18 06:20:51', 0),
(31, 21, 11, '2026-07-23', '1:00 PM', 'Follow-up', NULL, 'In Person', 'completed', '2026-07-18 06:47:45', 0),
(32, 21, 11, '2026-07-18', '12:00 PM', 'Consultation', 'Headache', 'Online', 'cancelled', '2026-07-18 08:31:15', 0);

-- --------------------------------------------------------

--
-- Table structure for table `consultations`
--

CREATE TABLE `consultations` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `findings` text DEFAULT NULL,
  `followup_needed` enum('yes','no') DEFAULT 'no',
  `followup_date` date DEFAULT NULL,
  `status` enum('pending','completed') DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_done` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consultations`
--

INSERT INTO `consultations` (`id`, `appointment_id`, `doctor_id`, `patient_id`, `findings`, `followup_needed`, `followup_date`, `status`, `created_at`, `is_done`) VALUES
(4, 10, 11, 17, 'Common Cold', 'no', NULL, 'completed', '2026-07-09 06:21:02', 0),
(5, 24, 11, 1, 'Hypertension', 'yes', '2026-07-23', 'pending', '2026-07-09 06:24:10', 0),
(6, 23, 11, 3, 'Gastritis', 'yes', '2026-07-23', 'pending', '2026-07-09 06:26:08', 0),
(7, 27, 6, 20, '', 'no', NULL, 'completed', '2026-07-15 05:21:06', 0),
(8, 29, 11, 21, '', 'yes', '2026-07-23', 'pending', '2026-07-18 06:47:45', 0),
(9, 31, 11, 21, '', 'no', NULL, 'completed', '2026-07-18 06:49:37', 0);

-- --------------------------------------------------------

--
-- Table structure for table `consultation_medicines`
--

CREATE TABLE `consultation_medicines` (
  `id` int(11) NOT NULL,
  `consultation_id` int(11) NOT NULL,
  `medicine_name` varchar(255) NOT NULL,
  `dosage` varchar(100) DEFAULT NULL,
  `frequency` varchar(100) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `is_done` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consultation_medicines`
--

INSERT INTO `consultation_medicines` (`id`, `consultation_id`, `medicine_name`, `dosage`, `frequency`, `duration`, `is_done`) VALUES
(4, 4, 'Paracetamol', '500mg', '3x a day', '3 days', 0),
(5, 4, 'Cetirizine', '10mg', '1x a day', '5 days', 0),
(6, 5, 'Amlodipine', '5mg', '1x a day', '30 days', 0),
(7, 6, 'Omeprazole', '20mg', '1x a day', '14 days', 0);

-- --------------------------------------------------------

--
-- Table structure for table `consultation_recommendations`
--

CREATE TABLE `consultation_recommendations` (
  `id` int(11) NOT NULL,
  `consultation_id` int(11) NOT NULL,
  `recommendation` text NOT NULL,
  `is_done` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consultation_recommendations`
--

INSERT INTO `consultation_recommendations` (`id`, `consultation_id`, `recommendation`, `is_done`) VALUES
(4, 4, 'Drink 8-10 glasses of water daily', 0),
(5, 4, 'Sleep atleast 8 hrs', 0),
(6, 4, 'eat fruits rich in Vitamin C', 0),
(7, 5, 'Low-salt diet', 0),
(8, 5, 'BP monitoring', 0),
(9, 5, 'ECG and lipid profile', 0),
(10, 6, 'Avoid Spicy Foods', 0),
(11, 6, 'Avoid coffee and alcohol', 0);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `otp_code` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `otp_code`, `expires_at`, `created_at`) VALUES
(3, 'johannamaetomadong@gmail.com', '900091', '2026-07-13 09:36:44', '2026-07-13 01:31:44'),
(9, 'arcenaljonalyn5@gmail.com', '421570', '2026-07-13 20:41:14', '2026-07-13 12:36:14');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `emergency_contact` varchar(30) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `user_id`, `first_name`, `last_name`, `gender`, `birthdate`, `phone`, `email`, `address`, `emergency_contact`, `allergies`, `medical_history`, `created_at`) VALUES
(1, NULL, 'Owen', 'Penddreth', 'Male', '1934-03-09', '09367785162', 'openddreth0@sina.com.cn', 'General Trias', '09624524115', 'pollen', 'none', '2026-03-30 16:00:00'),
(2, NULL, 'Tedra', 'Abraham', 'Female', '1987-12-16', '09492371743', 'tabraham1@japanpost.jp', 'tagytay', '09773982752', 'peanuts', 'migraine', '2026-01-11 16:00:00'),
(3, NULL, 'Bruno', 'Strowger', 'Male', '1943-12-17', '09915193881', 'bstrowger2@smugmug.com', 'Bacoor', '09418628101', 'seafood', 'allergic rhinitis', '2026-04-13 16:00:00'),
(4, NULL, 'Nonna', 'Pitblado', 'Female', '2012-12-20', '09134991270', 'npitblado3@vk.com', 'Bacoor', '09869655135', 'sulfa drugs', 'heart disease', '2026-03-31 16:00:00'),
(5, NULL, 'Davy', 'Stott', 'Male', '2016-11-25', '09684052270', 'dstott4@phoca.cz', 'tagytay', '09583437730', 'dust', 'diabetes', '2026-07-17 16:00:00'),
(6, NULL, 'Nessie', 'Bedham', 'Female', '1963-10-12', '09541266008', 'nbedham5@yolasite.com', 'General Trias', '09450427812', 'seafood', 'diabetes', '2026-06-09 16:00:00'),
(7, NULL, 'Stevana', 'Tooby', 'Female', '2007-02-20', '09694054669', 'stooby6@narod.ru', 'Dasmarinas', '09208904878', 'peanuts', 'asthma', '2026-02-05 16:00:00'),
(8, NULL, 'Cherilyn', 'Asken', 'Female', '1982-06-07', '09236058817', 'casken7@technorati.com', 'Dasmarinas', '09820494688', 'dust', 'allergic rhinitis', '2026-05-01 16:00:00'),
(9, NULL, 'Elysha', 'Betun', 'Female', '1971-04-05', '09712556463', 'ebetun8@sbwire.com', 'Dasmarinas', '09035107545', 'aspirin', 'migraine', '2026-06-05 16:00:00'),
(10, NULL, 'Darrel', 'Tilling', 'Male', '2009-03-12', '09219513730', 'dtilling9@fastcompany.com', 'General Trias', '09758203769', 'aspirin', 'hypertension', '2026-01-06 16:00:00'),
(11, NULL, 'Ame', 'Noel', 'Female', '1974-12-15', '09222466359', 'anoela@pen.io', 'Cavity City', '09806894282', 'seafood', 'heart disease', '2026-01-29 16:00:00'),
(12, NULL, 'Alvy', 'Jurries', 'Male', '2019-11-03', '09874323093', 'ajurriesb@cloudflare.com', 'Dasmarinas', '09454181702', 'pollen', 'diabetes', '2026-01-27 16:00:00'),
(13, NULL, 'Ketti', 'Pidwell', 'Female', '1943-06-23', '09396393235', 'kpidwellc@discovery.com', 'Dasmarinas', '09468194090', 'aspirin', 'hypertension', '2026-07-17 16:00:00'),
(14, NULL, 'Orland', 'Middlewick', 'Male', '2002-05-21', '09068393425', 'omiddlewickd@shinystat.com', 'Imus', '09615217414', 'penicillin', 'diabetes', '2026-05-07 16:00:00'),
(15, NULL, 'Dory', 'Ohrt', 'Male', '1946-11-26', '09049160048', 'dohrte@nhs.uk', 'Dasmarinas', '09962694670', 'sulfa drugs', 'heart disease', '2026-06-22 16:00:00'),
(16, NULL, 'Dolores', 'Puckett', 'Female', '1976-02-18', '09913817660', 'dpuckettf@sciencedaily.com', 'Carmona', '09414704874', 'seafood', 'heart disease', '2026-02-10 16:00:00'),
(17, NULL, 'Guinna', 'Gingell', 'Male', '1999-04-04', '09136404098', 'ggingellg@blogtalkradio.com', 'Bacoor', '09368921789', 'sulfa drugs', 'hypertension', '2026-03-29 16:00:00'),
(18, NULL, 'Syman', 'Stone', 'Male', '1933-02-11', '09610324880', 'sstoneh@webeden.co.uk', 'Carmona', '09698390875', 'sulfa drugs', 'asthma', '2026-06-07 16:00:00'),
(19, NULL, 'Ignace', 'Gligori', 'Male', '1986-09-27', '09246296023', 'igligorii@redcross.org', 'Cavity City', '09710591006', 'aspirin', 'heart disease', '2026-06-24 16:00:00'),
(20, NULL, 'Charmane', 'Searchwell', 'Female', '1985-10-07', '09870542944', 'csearchwellj@slashdot.org', 'Trece Martires', '09497287632', 'penicillin', 'none', '2026-07-13 16:00:00'),
(21, 18, 'Prima', 'Toben', 'Female', '2005-11-12', '09921234567', 'prima@gmail.com', 'Dasma', '09123456781', 'Chocolate', 'Asthma', '2026-07-18 06:10:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role` enum('admin','doctor','patient') NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `schedule_days` varchar(200) DEFAULT NULL,
  `schedule_time_start` varchar(20) DEFAULT NULL,
  `schedule_time_end` varchar(20) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role`, `first_name`, `last_name`, `email`, `username`, `license_number`, `schedule_days`, `schedule_time_start`, `schedule_time_end`, `profile_photo`, `password`, `created_at`) VALUES
(6, 'doctor', 'Johanna', 'Tomadong', 'johannamaetomadong@gmail.com', '', 'JH123', 'Monday,Tuesday,Wednesday', '8:00 AM', '5:00 PM', '1780373730_Tomadong.jpg', '$2y$10$/HRRla.UAsKlmK13Bgw9W.ahfkJOz5LQICeEBI9gluSkrvGlA8qJq', '2026-06-02 04:15:30'),
(7, 'doctor', 'Sarah', 'Taroy', 'sarahjanetaroy125@gmail.com', '', 'ST125', 'Monday,Tuesday,Wednesday,Sunday', '8:00 AM', '6:00 PM', '1780373849_Taroyy.jpg', '$2y$10$pBBAP2N3RPyHRIbPYXpmCOAvFTz9Ua3y84s99.aywk1lurCf2lXoy', '2026-06-02 04:17:29'),
(8, 'doctor', 'Jody', 'Garduque', 'jodymalditagarduque@gmail.com', '', 'JG816', 'Monday,Tuesday,Wednesday,Sunday', '10:00 AM', '3:00 PM', '1780374010_Garduque.jpg', '$2y$10$tsqLmLmD3KPZM8nhBQdK5ekn10hot1l4V8l1V8ibChBwEykcz1fo6', '2026-06-02 04:20:10'),
(9, 'doctor', 'Khane', 'Bayani', 'khanebayani@gmail.com', '', 'KB33', 'Thursday,Friday,Saturday,Sunday', '1:00 PM', '4:00 PM', '1780374118_Bayani.jpg', '$2y$10$Qj/d/Zm2Rh/n9I.697.SCOakRvtKyfRQQ2c.elBZoZUmwsXJjuHEG', '2026-06-02 04:21:58'),
(10, 'doctor', 'Reymar', 'Macas', 'reymarmacas@gmail.com', '', 'RM112', 'Thursday,Friday,Saturday', '9:00 AM', '12:00 PM', '1780374208_Macas.jpg', '$2y$10$4Cfyn0g1fyQ3KyVpAI9D8eAe3zxPV1px/yxscBjv.C9E9bd5ROaMW', '2026-06-02 04:23:28'),
(11, 'doctor', 'Jonalyn', 'Arcenal', 'arcenaljonalyn5@gmail.com', '', 'JA127', 'Thursday,Friday,Saturday', '12:00 PM', '4:00 PM', '1780374299_Arcenal.jpg', '$2y$10$DX4tESinbjCsMyJW3ToXUOUuvPUBWxoUXgvMf8sIKBEdO1BbnpAb2', '2026-06-02 04:24:59'),
(12, 'admin', 'Clinic', 'System', 'clinic.system@gmail.com', 'admin', '', '', '', '', '1780374499_1780317865_Minimal Yellow Star Aesthetic _ Cute Graphic Design Inspiration.jpg', '$2y$10$3Eok67LqtfdZfK3whvQU5.zodJ9zyVT/9Ej2RYrWDsH932uKvseTG', '2026-06-02 04:28:19'),
(18, 'patient', 'Prima', 'Tober', 'prima@gmail.com', 'prima', '', '', '', '', '', '$2y$10$QSXTITAaTArokN86gdHF7.RW9yZImyferNC/PNQLoLvT8NH6pEC9G', '2026-07-16 01:45:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_appointments_patient` (`patient_id`),
  ADD KEY `fk_appointments_doctor` (`doctor_id`);

--
-- Indexes for table `consultations`
--
ALTER TABLE `consultations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_consultations_appointment` (`appointment_id`),
  ADD KEY `fk_consultations_doctor` (`doctor_id`),
  ADD KEY `fk_consultations_patient` (`patient_id`);

--
-- Indexes for table `consultation_medicines`
--
ALTER TABLE `consultation_medicines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_medicines_consultation` (`consultation_id`);

--
-- Indexes for table `consultation_recommendations`
--
ALTER TABLE `consultation_recommendations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_recommendations_consultation` (`consultation_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_password_resets_email` (`email`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_patients_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `consultations`
--
ALTER TABLE `consultations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `consultation_medicines`
--
ALTER TABLE `consultation_medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `consultation_recommendations`
--
ALTER TABLE `consultation_recommendations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_appointments_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_appointments_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `consultations`
--
ALTER TABLE `consultations`
  ADD CONSTRAINT `fk_consultations_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_consultations_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_consultations_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `consultation_medicines`
--
ALTER TABLE `consultation_medicines`
  ADD CONSTRAINT `fk_medicines_consultation` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `consultation_recommendations`
--
ALTER TABLE `consultation_recommendations`
  ADD CONSTRAINT `fk_recommendations_consultation` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

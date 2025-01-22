-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 22, 2025 at 06:49 AM
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
-- Database: `bus_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `startingLocation` varchar(10) NOT NULL,
  `destination` varchar(15) NOT NULL,
  `num_passengers` int(2) NOT NULL,
  `Pickupdate` datetime NOT NULL,
  `dropoffdate` datetime NOT NULL,
  `Status` varchar(10) NOT NULL,
  `payment` int(20) NOT NULL,
  `processed` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `type` varchar(10) NOT NULL,
  `license` varchar(10) NOT NULL,
  `seats` int(4) NOT NULL,
  `fuel` varchar(7) NOT NULL,
  `fuel_usage` float NOT NULL,
  `price` int(10) NOT NULL,
  `reg-date` date NOT NULL DEFAULT current_timestamp(),
  `image` text NOT NULL,
  `Status` varchar(10) NOT NULL,
  `distance` int(5) NOT NULL,
  `maintenance` varchar(6) NOT NULL,
  `fuel_consumption` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `name`, `type`, `license`, `seats`, `fuel`, `fuel_usage`, `price`, `reg-date`, `image`, `Status`, `distance`, `maintenance`, `fuel_consumption`) VALUES
(14, 'Toyota Coaster', 'minbus', 'Class-E', 22, 'diesel', 0, 60000, '2024-11-03', 'IMG-67274e40dd78d3.33774252.jpg', 'active', 0, 'good', 9),
(18, 'Toyota HiAce', 'minbus', 'Class-E', 16, 'petrol', 0, 60000, '2024-11-05', 'IMG-672a7cc1a57103.75976144.jpg', 'active', 0, 'good', 7);

-- --------------------------------------------------------

--
-- Table structure for table `distance`
--

CREATE TABLE `distance` (
  `id` int(11) NOT NULL,
  `routename` varchar(40) NOT NULL,
  `origin` varchar(40) NOT NULL,
  `destination` varchar(40) NOT NULL,
  `distance` int(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `distance`
--

INSERT INTO `distance` (`id`, `routename`, `origin`, `destination`, `distance`) VALUES
(1, 'Thyolo-Blantyre', 'Thyolo', 'Blantyre', 47),
(2, 'Thyolo-Lilongwe', 'Thyolo', 'Lilongwe', 385),
(3, 'Thyolo-Balaka', 'Thyolo', 'Balaka', 176),
(4, 'Thyolo-Chikwawa', 'Thyolo', 'Chikwawa', 101),
(5, 'Thyolo-Chitipa', 'Thyolo', 'Chitipa', 1046),
(6, 'Thyolo-Dedza', 'Thyolo', 'Dedza', 275),
(7, 'Thyolo-Dowa', 'Thyolo', 'Dowa', 411),
(8, 'Thyolo-Karonga', 'Thyolo', 'Karonga', 885),
(9, 'Thyolo-Kasungu', 'Thyolo', 'Kasungu', 485),
(10, 'Thyolo-Machinga', 'Thyolo', 'Machinga', 141),
(11, 'Thyolo-Mangochi', 'Thyolo', 'Mangochi', 231),
(12, 'Thyolo-Mchinji', 'Thyolo', 'Mchinji', 467),
(13, 'Thyolo-Monkeybay', 'Thyolo', 'Monkeybay', 294),
(14, 'Thyolo-Mulanje', 'Thyolo', 'Mulanje', 47),
(15, 'Thyolo-Mwanza', 'Thyolo', 'Mwanza', 150),
(16, 'Thyolo-Mzimba', 'Thyolo', 'Mzimba', 633),
(17, 'Thyolo-Mzuzu', 'Thyolo', 'Mzuzu', 722),
(18, 'Thyolo-Nkhatabay', 'Thyolo', 'Nkhatabay', 622),
(19, 'Thyolo-Nkhotakota', 'Thyolo', 'Nkhotakota', 424),
(20, 'Thyolo-Nsanje', 'Thyolo', 'Nsanje', 146),
(21, 'Thyolo-Ntcheu', 'Thyolo', 'Ntcheu', 200),
(22, 'Thyolo-Ntchisi', 'Thyolo', 'Ntchisi', 448),
(23, 'Thyolo-Rumphi', 'Thyolo', 'Rumphi', 787),
(24, 'Thyolo-Salima', 'Thyolo', 'Salima', 315);

-- --------------------------------------------------------

--
-- Table structure for table `driver_details`
--

CREATE TABLE `driver_details` (
  `driver_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `date_of_birth` date NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `post_address` text NOT NULL,
  `status` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `driver_identification_credentials`
--

CREATE TABLE `driver_identification_credentials` (
  `id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `license_number` varchar(20) NOT NULL,
  `license_category` enum('B','EB','C1','EC1','C','EC') NOT NULL,
  `experience` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `driver_incident_history`
--

CREATE TABLE `driver_incident_history` (
  `id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `previous_accidents` enum('yes','no') NOT NULL,
  `accident_date` date DEFAULT NULL,
  `previous_suspensions` enum('yes','no') NOT NULL,
  `suspension_reason` text DEFAULT NULL,
  `guilty_of_traffic_offense` enum('yes','no') NOT NULL,
  `offense_details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emergency_contact`
--

CREATE TABLE `emergency_contact` (
  `contact_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registered-users`
--

CREATE TABLE `registered-users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(30) NOT NULL,
  `middlename` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `Gender` varchar(7) NOT NULL,
  `Date-of-birth` date NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(7) NOT NULL,
  `Id-number` varchar(10) NOT NULL,
  `username` varchar(60) NOT NULL,
  `status` varchar(7) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_photo` text NOT NULL,
  `reg-date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registered-users`
--

INSERT INTO `registered-users` (`id`, `firstname`, `middlename`, `lastname`, `Gender`, `Date-of-birth`, `phone`, `email`, `role`, `Id-number`, `username`, `status`, `password`, `profile_photo`, `reg-date`) VALUES
(42, 'admin', '', 'one', 'male', '2012-10-04', '', 'guest@gmail.com', 'admin', '', 'admin', 'active', '$2y$10$w9Kzgcay5BYgR9dEWmLkauYNrFieGM0QtyKYL/2ZqOaeqYM.QYuvO', 'uploaded_image/IMG-6790859ec79a45.59763038.png', '2025-01-22 07:43:58');

-- --------------------------------------------------------

--
-- Table structure for table `uploaded_files`
--

CREATE TABLE `uploaded_files` (
  `file_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` enum('pdf','jpeg','png','gif') NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `work_schedule`
--

CREATE TABLE `work_schedule` (
  `id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `off_sunday` tinyint(1) DEFAULT 0,
  `off_saturday` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `distance`
--
ALTER TABLE `distance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `driver_details`
--
ALTER TABLE `driver_details`
  ADD PRIMARY KEY (`driver_id`);

--
-- Indexes for table `driver_identification_credentials`
--
ALTER TABLE `driver_identification_credentials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `driver_incident_history`
--
ALTER TABLE `driver_incident_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `emergency_contact`
--
ALTER TABLE `emergency_contact`
  ADD PRIMARY KEY (`contact_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `registered-users`
--
ALTER TABLE `registered-users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uploaded_files`
--
ALTER TABLE `uploaded_files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `work_schedule`
--
ALTER TABLE `work_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `distance`
--
ALTER TABLE `distance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `driver_details`
--
ALTER TABLE `driver_details`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `driver_identification_credentials`
--
ALTER TABLE `driver_identification_credentials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `driver_incident_history`
--
ALTER TABLE `driver_incident_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `emergency_contact`
--
ALTER TABLE `emergency_contact`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `registered-users`
--
ALTER TABLE `registered-users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `uploaded_files`
--
ALTER TABLE `uploaded_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `work_schedule`
--
ALTER TABLE `work_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`);

--
-- Constraints for table `driver_identification_credentials`
--
ALTER TABLE `driver_identification_credentials`
  ADD CONSTRAINT `driver_identification_credentials_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `driver_details` (`driver_id`) ON DELETE CASCADE;

--
-- Constraints for table `driver_incident_history`
--
ALTER TABLE `driver_incident_history`
  ADD CONSTRAINT `driver_incident_history_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `driver_details` (`driver_id`) ON DELETE CASCADE;

--
-- Constraints for table `emergency_contact`
--
ALTER TABLE `emergency_contact`
  ADD CONSTRAINT `emergency_contact_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `driver_details` (`driver_id`) ON DELETE CASCADE;

--
-- Constraints for table `uploaded_files`
--
ALTER TABLE `uploaded_files`
  ADD CONSTRAINT `uploaded_files_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `driver_details` (`driver_id`) ON DELETE CASCADE;

--
-- Constraints for table `work_schedule`
--
ALTER TABLE `work_schedule`
  ADD CONSTRAINT `work_schedule_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `driver_details` (`driver_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 02, 2024 at 11:22 AM
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
-- Database: `bus-system`
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
  `processed` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `car_id`, `startingLocation`, `destination`, `num_passengers`, `Pickupdate`, `dropoffdate`, `Status`, `payment`, `processed`) VALUES
(40, 25, 13, 'Thyolo', 'Balaka', 5, '2024-11-01 09:21:00', '2024-11-03 09:21:00', 'confirmed', 236000, 1),
(44, 25, 13, 'Thyolo', 'Chikwawa', 12, '2024-11-01 09:52:00', '2024-11-01 15:58:00', 'pending', 0, 0),
(47, 26, 13, 'Thyolo', 'Lilongwe', 1, '2024-11-02 05:40:00', '2024-11-02 12:36:00', 'pending', 0, 0),
(50, 29, 13, 'Thyolo', 'Balaka', 3, '2024-11-02 13:21:00', '2024-11-02 21:27:00', 'pending', 176000, 0),
(51, 30, 13, 'Thyolo', 'Lilongwe', 6, '2024-11-02 06:35:00', '2024-11-03 17:34:00', 'pending', 385000, 0);

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
  `fuel_usage` int(11) NOT NULL,
  `price` int(10) NOT NULL,
  `reg-date` date NOT NULL DEFAULT current_timestamp(),
  `image` text NOT NULL,
  `Status` varchar(10) NOT NULL,
  `distance` int(5) NOT NULL,
  `maintenance` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `name`, `type`, `license`, `seats`, `fuel`, `fuel_usage`, `price`, `reg-date`, `image`, `Status`, `distance`, `maintenance`) VALUES
(13, 'Toyota HiAce', 'minbus', 'Class-E', 16, 'petrol', 0, 60000, '2024-11-01', 'IMG-6724e99ace6154.54940992.jpg', 'active', 176, 'good');

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
  `password` varchar(255) NOT NULL,
  `profile_photo` text NOT NULL,
  `reg-date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registered-users`
--

INSERT INTO `registered-users` (`id`, `firstname`, `middlename`, `lastname`, `Gender`, `Date-of-birth`, `phone`, `email`, `role`, `Id-number`, `username`, `password`, `profile_photo`, `reg-date`) VALUES
(25, 'John', '', 'Phiri', 'male', '2012-09-05', '0995089639', 'bit-027-22@must.ac.mw', 'Student', 'BIT-027-22', 'jonas', '$2y$10$V/pA/ZBxZnIq0Mb82D587ulXQKGgGKA9L4YIKV5WiQNHxWGTj6SCy', 'uploaded_image/IMG-6724ec3e63a0c8.77907563.jpeg', '2024-11-01 07:57:02'),
(26, 'mr', '', 'p', 'male', '2009-03-04', '0888593057', 'mr@must.ac.mw', 'Staff', 'ass2002222', 'mr', '$2y$10$n1jhVy9Akxri2Slv2Gj.8uw.IUfra9gGtSCEpkxwgj4pfLMLsNzM2', 'uploaded_image/IMG-6725d5fd59cfc4.89362274.jpg', '2024-11-02 00:34:21'),
(29, 'guest', '', 'one', 'female', '2012-10-04', '0999456718', 'guest@gmail.com', 'Guest', '', 'guest', '$2y$10$eE1a7/YzWabZwzkvZIqf4OBYro5mpnAxDZxOMchWxjItF236UgwT6', 'uploaded_image/IMG-6725e0993fa9d3.40591608.jpg', '2024-11-02 01:19:37'),
(30, 'guest', '', 'two', 'male', '2012-10-10', '0991234234', 'guesttwo@gmail.com', 'Guest', '', 'two', '$2y$10$lRnoW6n5fIH4ZDCCuw9cmuuLF15QEyycXoFdyKWZNhUyxfxVr.adq', 'uploaded_image/IMG-6725e4a4916ba3.87710072.jpeg', '2024-11-02 01:36:52');

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
-- Indexes for table `registered-users`
--
ALTER TABLE `registered-users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `distance`
--
ALTER TABLE `distance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `registered-users`
--
ALTER TABLE `registered-users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

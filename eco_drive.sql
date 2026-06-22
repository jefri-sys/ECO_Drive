-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 02, 2025 at 09:18 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eco_drive`
--

-- --------------------------------------------------------

--
-- Table structure for table `bill`
--

DROP TABLE IF EXISTS `bill`;
CREATE TABLE IF NOT EXISTS `bill` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_id` int NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `itemized_details` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_posts`
--

DROP TABLE IF EXISTS `community_posts`;
CREATE TABLE IF NOT EXISTS `community_posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_reviews`
--

DROP TABLE IF EXISTS `community_reviews`;
CREATE TABLE IF NOT EXISTS `community_reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `service_type` varchar(100) NOT NULL,
  `rating` int NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
CREATE TABLE IF NOT EXISTS `inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `spare_part_name` varchar(100) NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `low_stock_alert` int DEFAULT '5',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `spare_part_name`, `quantity`, `price`, `image_path`, `low_stock_alert`) VALUES
(1, 'Brake Pads', 1, 2000.00, '/S6 PROJECT(TEAM 6)/uploads/67d1d22fe1a2a.jpg', 5),
(2, 'Battery Diagnostic Tool', 1, 25000.00, '/S6 PROJECT(TEAM 6)/uploads/67d1d3b9e66d8.png', 2),
(3, 'Tire Alignment Kit', 2, 10000.00, '/S6 PROJECT(TEAM 6)/uploads/67d1d40e4d60d.jpeg', 3),
(4, 'Coolant Fluid (5L)', 2, 800.00, '/S6 PROJECT(TEAM 6)/uploads/67d1d4fc52fac.jpg', 5),
(5, 'Brake Fluid (1L)', 8, 600.00, '/S6 PROJECT(TEAM 6)/uploads/67d1d54857458.jpg', 5),
(32, 'Suspension', 14, 5000.00, '/S6 PROJECT(TEAM 6)/uploads/67d1df621c2c8.jpg', 5);

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
CREATE TABLE IF NOT EXISTS `locations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `latitude` decimal(10,6) NOT NULL,
  `longitude` decimal(10,6) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`, `latitude`, `longitude`, `address`, `phone`, `email`) VALUES
(1, 'EcoDrive Mumbai', 19.076000, 72.877700, '123 EV Road, Andheri East, Mumbai, MH 400069', '+91 22 1234 567', 'mumbai@ecodrive.com'),
(2, 'EcoDrive Delhi', 28.704100, 77.102500, '456 Green Lane, Connaught Place, New Delhi, DL 110001', '+91 11 9876 543', 'delhi@ecodrive.com'),
(3, 'EcoDrive Bangalore', 12.971600, 77.594600, '789 Tech Street, Koramangala, Bangalore, KA 560034', '+91 80 5555 123', 'bangalore@ecodrive.com'),
(4, 'EcoDrive Chennai', 13.082700, 80.270700, '101 Electric Avenue, T. Nagar, Chennai, TN 600017', '+91 44 6666 789', 'chennai@ecodrive.com'),
(5, 'EcoDrive Hyderabad', 17.385000, 78.486700, '321 Charge Road, Banjara Hills, Hyderabad, TS 500034', '+91 40 4444 321', 'hyderabad@ecodrive.com');

-- --------------------------------------------------------

--
-- Table structure for table `mechanic`
--

DROP TABLE IF EXISTS `mechanic`;
CREATE TABLE IF NOT EXISTS `mechanic` (
  `id` int NOT NULL AUTO_INCREMENT,
  `specialization` varchar(100) NOT NULL,
  `experience_years` int NOT NULL,
  `availability_status` enum('Available','Not Available') DEFAULT 'Available',
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `mechanic`
--

INSERT INTO `mechanic` (`id`, `specialization`, `experience_years`, `availability_status`, `user_id`) VALUES
(10, 'Battery Specialist', 5, 'Available', 29),
(11, 'Battery Specialist', 5, 'Available', 32),
(12, 'Battery Specialist', 5, 'Available', 34),
(13, 'Battery Specialist', 5, 'Available', 36),
(15, 'Battery Specialist', 5, 'Available', 43),
(16, 'Battery Specialist', 5, 'Available', 46),
(17, 'General EV Service Mechanic', 10, 'Available', 48),
(18, 'General EV Service Mechanic', 10, 'Available', 49),
(19, 'engine', 10, 'Available', 50);

-- --------------------------------------------------------

--
-- Table structure for table `mechanic_leave`
--

DROP TABLE IF EXISTS `mechanic_leave`;
CREATE TABLE IF NOT EXISTS `mechanic_leave` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mechanic_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  PRIMARY KEY (`id`),
  KEY `mechanic_id` (`mechanic_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `message` varchar(255) NOT NULL,
  `type` enum('leave','service_request','mechanic_update','inventory_alert','bill','general','resheduling') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `related_id` int DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_is_read` (`is_read`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `type`, `related_id`, `is_read`, `created_at`) VALUES
(1, 47, 'Your service request #13 has been approved for March 14, 2025.', 'service_request', 13, 0, '2025-03-30 14:33:59'),
(2, 51, 'Service request #26 for vehicle KL-05 AE2345 is completed.', 'service_request', 26, 1, '2025-03-30 14:33:59'),
(3, 1, 'Brake Pads stock is low (1 remaining).', 'inventory_alert', 1, 0, '2025-03-30 14:33:59'),
(4, 51, 'Bill #1 for service request #14 has been generated (₹3000).', 'bill', 1, 1, '2025-03-30 14:33:59'),
(5, 51, 'Welcome to Eco Drive! Book your first service today.', 'general', NULL, 1, '2025-03-30 14:33:59'),
(27, 50, 'You have been assigned to service request #40', 'mechanic_update', 40, 1, '2025-03-30 17:33:59'),
(28, 51, 'Service request #38 has been Approved', 'service_request', 38, 1, '2025-03-30 17:34:14'),
(29, 50, 'You have been assigned to service request #38', 'mechanic_update', 38, 1, '2025-03-30 17:34:14'),
(31, 50, 'Your leave request from 2025-03-30 to 2025-03-31 has been Rejected', 'mechanic_update', 12, 1, '2025-03-30 18:23:47'),
(32, 50, 'Your leave request from 2025-03-30 to 2025-03-31 has been Approved', 'mechanic_update', 13, 1, '2025-03-30 18:27:18'),
(33, 50, 'Your leave request from 2025-03-31 to 2025-04-02 has been Approved', 'mechanic_update', 14, 1, '2025-03-30 18:38:46'),
(34, 50, 'Your leave request from 2025-03-31 to 2025-04-02 has been Approved', 'mechanic_update', 15, 1, '2025-03-30 18:41:22'),
(35, 47, 'Your service request has been cancelled due to slot unavailability. Please click here to reschedule.', '', 13, 0, '2025-04-01 07:26:21'),
(36, 47, 'Your service request has been cancelled due to slot unavailability. Please click here to reschedule.', '', 14, 0, '2025-04-01 07:26:21'),
(37, 47, 'Your service request has been cancelled due to slot unavailability. Please click here to reschedule.', '', 15, 0, '2025-04-01 07:26:21'),
(39, 50, 'You have been assigned to service request #44', 'mechanic_update', 44, 1, '2025-04-01 10:41:02'),
(40, 51, 'Service request #44 status updated to: Servicing', 'service_request', 44, 1, '2025-04-01 10:42:14'),
(41, 51, 'Your service request has been cancelled due to slot unavailability. Please click here to reschedule.', '', 49, 1, '2025-04-02 16:50:38'),
(42, 51, 'Your service request has been cancelled due to slot unavailability. Please click here to reschedule.', '', 38, 1, '2025-04-02 18:31:09'),
(43, 51, 'Service request #40 status updated to: Completed', 'service_request', 40, 1, '2025-04-02 18:33:56'),
(45, 50, 'You have been assigned to service request #50', 'mechanic_update', 50, 1, '2025-04-02 18:36:17'),
(51, 51, 'Service request #50 status updated to: Completed', 'service_request', 50, 1, '2025-04-02 18:45:58'),
(54, 51, 'Service request #51 status updated to: Completed', 'service_request', 51, 1, '2025-04-02 18:50:46'),
(56, 50, 'You have been assigned to service request #52', 'mechanic_update', 52, 1, '2025-04-02 19:42:42'),
(62, 51, 'Service request #52 status updated to: Completed', 'service_request', 52, 1, '2025-04-02 19:55:57'),
(63, 50, 'Your leave request from 2025-04-03 to 2025-04-04 has been Approved', 'mechanic_update', 17, 1, '2025-04-02 19:58:18'),
(64, 51, 'Service request #54 has been Approved', 'service_request', 54, 1, '2025-04-02 20:07:32'),
(65, 50, 'You have been assigned to service request #54', 'mechanic_update', 54, 0, '2025-04-02 20:07:32');

-- --------------------------------------------------------

--
-- Table structure for table `post_likes`
--

DROP TABLE IF EXISTS `post_likes`;
CREATE TABLE IF NOT EXISTS `post_likes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_user_unique` (`post_id`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_name` (`service_name`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `description`, `price`) VALUES
(12, 'Battery Health Check', 'Diagnostic test for battery performance and degradation', 2500.00),
(13, 'Tire Rotation & Alignment', 'Ensures even tire wear and proper wheel alignment', 2000.00),
(14, 'Brake System Service', 'Inspection and maintenance of brake pads, rotors, and fluid', 3000.00),
(15, 'Coolant System Check', 'Checks and refills EV battery cooling system', 1800.00),
(16, 'AC Filter & Cabin Cleaning', 'Air filter replacement and interior sanitization', 1500.00),
(17, 'Suspension & Steering Check', 'Ensures smooth handling and stability', 2200.00),
(18, 'Software Update & Diagnostics', 'Updates EV firmware and scans for system issues', 2800.00),
(19, 'Charging Port & Cable Inspection', 'Ensures proper charging connection and safety', 1200.00),
(20, 'Motor & Drivetrain Inspection', 'Checks electric motor, transmission, and performance', 3500.00),
(21, 'High Voltage Wiring Check', 'Ensures safe and efficient operation of high-voltage components', 2000.00);

-- --------------------------------------------------------

--
-- Table structure for table `service_plans`
--

DROP TABLE IF EXISTS `service_plans`;
CREATE TABLE IF NOT EXISTS `service_plans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `plan_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `total_cost_inr` decimal(10,2) NOT NULL,
  `duration_months` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `service_plans`
--

INSERT INTO `service_plans` (`id`, `plan_name`, `description`, `total_cost_inr`, `duration_months`) VALUES
(25, 'Standard EV Care', 'Mid-tier plan with all basic services + brake system check and coolant system maintenance.', 12000.00, 12),
(26, 'Fleet EV Plan', 'Bulk maintenance plan for businesses managing multiple EVs, with priority scheduling.', 40000.00, 12),
(28, 'Basic EV Maintenance', 'Covers essential maintenance like battery check, tire rotation, and software updates.', 7500.00, 6),
(29, 'Premium EV Protection', 'Advanced plan with high-voltage wiring checks, complete diagnostics, and AC system servicing.', 18000.00, 12),
(31, 'Family EV Bundle', 'Comprehensive plan for families with multiple EVs, covering essential services.', 15000.00, 12),
(32, 'Performance EV Tune-Up', 'Specialized plan for high-performance EVs with motor and software optimization.', 20000.00, 12);

-- --------------------------------------------------------

--
-- Table structure for table `service_plans_services`
--

DROP TABLE IF EXISTS `service_plans_services`;
CREATE TABLE IF NOT EXISTS `service_plans_services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `plan_id` int NOT NULL,
  `service_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plan_id` (`plan_id`,`service_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `service_plans_services`
--

INSERT INTO `service_plans_services` (`id`, `plan_id`, `service_id`) VALUES
(57, 25, 12),
(58, 25, 13),
(59, 25, 14),
(60, 25, 16),
(61, 25, 17),
(62, 25, 18),
(63, 25, 21),
(64, 26, 12),
(65, 26, 13),
(66, 26, 14),
(67, 26, 15),
(68, 26, 16),
(69, 26, 17),
(70, 26, 18),
(71, 26, 19),
(72, 26, 20),
(73, 26, 21),
(79, 28, 12),
(80, 28, 13),
(81, 28, 18),
(82, 29, 12),
(83, 29, 13),
(84, 29, 14),
(85, 29, 15),
(86, 29, 16),
(87, 29, 17),
(88, 29, 18),
(92, 31, 12),
(93, 31, 13),
(94, 31, 14),
(95, 31, 16),
(96, 31, 17),
(97, 31, 19),
(98, 32, 12),
(99, 32, 18),
(100, 32, 20),
(101, 32, 21);

-- --------------------------------------------------------

--
-- Table structure for table `service_required_inventory`
--

DROP TABLE IF EXISTS `service_required_inventory`;
CREATE TABLE IF NOT EXISTS `service_required_inventory` (
  `service_id` int NOT NULL,
  `inventory_id` int NOT NULL,
  PRIMARY KEY (`service_id`,`inventory_id`),
  KEY `inventory_id` (`inventory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_rq`
--

DROP TABLE IF EXISTS `service_rq`;
CREATE TABLE IF NOT EXISTS `service_rq` (
  `service_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `vehicle_id` int NOT NULL,
  `request_date` date NOT NULL,
  `assigned_mechanic_id` int DEFAULT NULL,
  `service_date` date DEFAULT NULL,
  `request_status` enum('Pending','Approved','Rejected','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Pending',
  `service_status` enum('Requested','Assigned','Servicing','Completed','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Assigned',
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `plan_id` int DEFAULT NULL,
  `slot_id` int DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `completion_date` date DEFAULT NULL,
  PRIMARY KEY (`service_id`),
  KEY `user_id` (`user_id`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `assigned_mechanic_id` (`assigned_mechanic_id`),
  KEY `plan_id` (`plan_id`),
  KEY `service_rq_ibfk_5` (`slot_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `service_rq`
--

INSERT INTO `service_rq` (`service_id`, `user_id`, `vehicle_id`, `request_date`, `assigned_mechanic_id`, `service_date`, `request_status`, `service_status`, `cancelled_at`, `plan_id`, `slot_id`, `notes`, `completion_date`) VALUES
(26, 51, 26, '2025-03-29', 19, '2025-03-29', 'Approved', 'Completed', NULL, 26, NULL, NULL, NULL),
(50, 51, 26, '2025-04-03', 19, '2025-04-03', 'Approved', 'Completed', NULL, 26, 68, 'llllllllllllllll', '2025-04-03'),
(51, 51, 29, '2025-04-03', 19, '2025-04-03', 'Approved', 'Completed', NULL, 26, 68, '', '2025-04-03'),
(52, 51, 34, '2025-04-03', 19, '2025-04-06', 'Approved', 'Completed', NULL, 28, 77, 'Also clean car neatly', '2025-04-03'),
(54, 51, 29, '2025-04-03', 19, '2025-04-03', 'Approved', 'Assigned', NULL, 26, 69, 'FDGHDF', NULL),
(56, 51, 26, '2025-04-03', NULL, '2025-04-03', 'Pending', 'Requested', NULL, 29, 68, '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `service_rq_services`
--

DROP TABLE IF EXISTS `service_rq_services`;
CREATE TABLE IF NOT EXISTS `service_rq_services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_rq_id` int NOT NULL,
  `service_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_rq_id` (`service_rq_id`,`service_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `service_rq_services`
--

INSERT INTO `service_rq_services` (`id`, `service_rq_id`, `service_id`) VALUES
(45, 26, 19),
(104, 50, 12),
(105, 50, 14),
(103, 50, 16),
(106, 51, 12),
(107, 51, 14),
(109, 52, 12),
(108, 52, 16),
(112, 54, 12),
(113, 54, 14),
(117, 56, 12),
(116, 56, 16);

-- --------------------------------------------------------

--
-- Table structure for table `service_slots`
--

DROP TABLE IF EXISTS `service_slots`;
CREATE TABLE IF NOT EXISTS `service_slots` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slot_date` date NOT NULL,
  `slot_time` time NOT NULL,
  `max_capacity` int NOT NULL DEFAULT '3',
  `current_bookings` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slot_unique` (`slot_date`,`slot_time`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `service_slots`
--

INSERT INTO `service_slots` (`id`, `slot_date`, `slot_time`, `max_capacity`, `current_bookings`) VALUES
(56, '2025-03-30', '09:00:00', 3, 0),
(57, '2025-03-30', '12:00:00', 3, 1),
(58, '2025-03-30', '15:00:00', 3, 0),
(59, '2025-03-31', '09:00:00', 3, 0),
(60, '2025-03-31', '12:00:00', 3, 0),
(61, '2025-03-31', '15:00:00', 3, 0),
(62, '2025-04-01', '09:00:00', 3, 0),
(63, '2025-04-01', '12:00:00', 3, 0),
(64, '2025-04-01', '15:00:00', 3, 0),
(65, '2025-04-02', '09:00:00', 3, 1),
(66, '2025-04-02', '12:00:00', 3, 0),
(67, '2025-04-02', '15:00:00', 3, 0),
(68, '2025-04-03', '09:00:00', 3, 3),
(69, '2025-04-03', '12:00:00', 3, 1),
(70, '2025-04-03', '15:00:00', 3, 0),
(71, '2025-04-04', '09:00:00', 3, 0),
(72, '2025-04-04', '12:00:00', 3, 0),
(73, '2025-04-04', '15:00:00', 3, 0),
(74, '2025-04-05', '09:00:00', 3, 0),
(75, '2025-04-05', '12:00:00', 3, 0),
(76, '2025-04-05', '15:00:00', 3, 0),
(77, '2025-04-06', '09:00:00', 3, 1),
(78, '2025-04-06', '12:00:00', 3, 0),
(79, '2025-04-06', '15:00:00', 3, 0),
(80, '2025-04-07', '09:00:00', 3, 0),
(81, '2025-04-07', '12:00:00', 3, 0),
(82, '2025-04-07', '15:00:00', 3, 0),
(83, '2025-04-08', '09:00:00', 3, 0),
(84, '2025-04-08', '12:00:00', 3, 0),
(85, '2025-04-08', '15:00:00', 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `service_spare_parts`
--

DROP TABLE IF EXISTS `service_spare_parts`;
CREATE TABLE IF NOT EXISTS `service_spare_parts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_id` int NOT NULL,
  `spare_part_id` int NOT NULL,
  `quantity_used` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  KEY `spare_part_id` (`spare_part_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `service_spare_parts`
--

INSERT INTO `service_spare_parts` (`id`, `service_id`, `spare_part_id`, `quantity_used`) VALUES
(20, 14, 1, 1),
(21, 14, 5, 2),
(22, 50, 2, 1),
(23, 52, 32, 4),
(24, 52, 4, 1),
(25, 52, 5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_tbl`
--

DROP TABLE IF EXISTS `user_tbl`;
CREATE TABLE IF NOT EXISTS `user_tbl` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact_no` varchar(15) NOT NULL,
  `address` text,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','mechanic','admin') NOT NULL DEFAULT 'customer',
  `image_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_tbl`
--

INSERT INTO `user_tbl` (`id`, `fname`, `lname`, `email`, `contact_no`, `address`, `password`, `role`, `image_path`) VALUES
(1, 'Rohan', 'John Thomas', 'rohanjt.inmca2227@saintgits.org', '8089549345', 'Mattathil House Kumarakom North P.O', '$2y$10$qqG5MuPcDdHG2swjIq3K.ecJCP6Gf/amcOgRMdSbEnAzsb/1ffILC', 'admin', NULL),
(27, 'Alice', 'Smith', 'alice.smith@example.com', '9876543210', '123 Green St, New York', '3d5e9ac865d0e0ec9a56fa0f8739772b001269721c5776eb4dfe25c1eab1919f', 'customer', NULL),
(28, 'Bob', 'Johnson', 'bob.johnson@example.com', '8765432109', '456 Park Ave, LA', 'fc6e5ceb0061b7059f2d38619c03c7b2f4f1ca6e0d29be5b2c0ea7f7d903c197', 'customer', NULL),
(29, 'Charlie', 'Brown', 'charlie.brown@example.com', '7654321098', '789 River Rd, SF', 'add2a3bedcb093b9a0bdc7031889186844ef3000abd2f294d7987bfddae3615a', 'mechanic', NULL),
(30, 'David', 'White', 'david.white@example.com', '6543210987', '321 Hill St, Chicago', '011f43602454216a9788b99e03e2bde8eae0a97e5b760507c8402bdd78b6f10d', 'customer', NULL),
(31, 'Emma', 'Green', 'emma.green@example.com', '5432109876', '654 Lake St, Houston', '7749bdfd18ba22d53b2a4eaaef2556eabb5197bc3ab19280a9bd27387503d5b8', 'customer', NULL),
(32, 'Frank', 'Hall', 'frank.hall@example.com', '4321098765', '987 Maple Ave, Boston', '025ecc36e5de7d151a568b0a2d42c847cbe2c76be40145df95344ce6a7f3d8ba', 'mechanic', NULL),
(33, 'Grace', 'Lee', 'grace.lee@example.com', '3210987654', '234 Oak St, Miami', '1a68cb068e9bee74c03cde92796e8a5e0c9acd3aa6096864cb79380aa17856e5', 'customer', NULL),
(34, 'Henry', 'Adams', 'henry.adams@example.com', '2109876543', '567 Pine St, Dallas', '2aaa406794a570ccf819cd3f92b7c898a92a50bb6ff260e4cc6d05ba27f631c9', 'mechanic', NULL),
(35, 'Isla', 'Nelson', 'isla.nelson@example.com', '1098765432', '789 Elm St, Seattle', 'fece9ff126be7d013e156effb1b1b98619362150a9caa739d664d5a7b89ee796', 'customer', NULL),
(36, 'Jack', 'Roberts', 'jack.roberts@example.com', '9876501234', '111 Birch St, Denver', '29409af3b6b7ca673ffd6b4407741e095c741aa28d2c55c65449e460c051c01d', 'mechanic', NULL),
(37, 'Kate', 'Wright', 'kate.wright@example.com', '8765409876', '222 Cedar St, Phoenix', '3eeb3b538e18b958e7e5cdb2a3f68808ed6b684c574cbb180fdd92b278470f51', 'customer', NULL),
(38, 'Leo', 'Harris', 'leo.harris@example.com', '7654308765', '333 Spruce St, Philadelphia', '433a6285c29c0265c19582e53321cc7892554d468576494da9247c6be903b867', 'customer', NULL),
(39, 'Mia', 'Young', 'mia.young@example.com', '6543207654', '444 Ash St, San Diego', 'ef8dde427eea2221d0944d3840d0f9ac7f90afc317f9cb5a5bc7a1409adc5195', 'customer', NULL),
(41, 'Olivia', 'Scott', 'olivia.scott@example.com', '4321005432', '666 Redwood St, Atlanta', 'df1d969017753ce640b8afc1036c8ac50fafba8629049f4301c2cfc49a9e37d9', 'customer', NULL),
(42, 'Paul', 'Evans', 'paul.evans@example.com', '3210004321', '777 Fir St, Detroit', '1aac2d955a8470b5d2d74cac7548e88af5c0e1551acee4fbcead426c087ff939', 'customer', NULL),
(43, 'Quinn', 'Carter', 'quinn.carter@example.com', '2100003210', '888 Walnut St, Nashville', 'f91911af535202106443994559345086a75a81760c663694100987a8c02aa4f7', 'mechanic', NULL),
(45, 'Sophia', 'Morris', 'sophia.morris@example.com', '9870001098', '101 Oakwood St, Minneapolis', 'c768682a81b6c45fc08ef55b80381f5a0aca2946e69414b78310f9c0c018ed57', 'customer', NULL),
(46, 'Tom', 'Fisher', 'tom.fisher@example.com', '8760000987', '202 Pinewood St, St. Louis', 'f11c799502ed788b70e2130749cf17d1263720aaa1513f28c2acf9c5230cc702', 'mechanic', NULL),
(47, 'Jefri', 'Jiji', 'jefrij.inmca2227@saintgits.org', '7907809910', 'Abc house', '$2y$10$jyehwn42yeby9zAVTHGQ/.0jD41TZE88IkeP8abUdGdsPk1ck0urC', 'customer', NULL),
(48, 'Sidharth C', 'Manoj', 'sidharth.inmca2227@saintgits.org', '8921809963', 'BCD House', '$2y$10$9xrcAt2y34f/qfKgO6RlaO2G.SZx9P2Wi3OfvFJFZ2Ae4I4bdjzWK', 'mechanic', NULL),
(49, 'Sidharth C', 'Manoj', 'sidharth.inmca@saintgits.org', '8921809963', 'BCD House', '$2y$10$bH7kJCVYWbLsg9nTGj49GOAHt6UO8a.XEjP53zmUi8/OAvX2SeS6q', 'mechanic', NULL),
(50, 'Rohan', 'John Thomas', 'rohanjohnthomas749@gmail.com', '8921809963', 'ABCD House', '$2y$10$6a224.nc/iUgmmaU0eb7Q.Xr0wXfI5gu/.9y75WFuzOH3/q3ShIrW', 'mechanic', NULL),
(51, 'Dark', 'Eye', 'rohanjohn366@gmail.com', '8089549345', 'ABC House', '$2y$10$V2Q6NDIGDkatz/xI.qn/WeU5UwaOmr7jCdYP2FbFZ8HxAwfLyShPu', 'customer', '');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle`
--

DROP TABLE IF EXISTS `vehicle`;
CREATE TABLE IF NOT EXISTS `vehicle` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vehicle_number` varchar(20) NOT NULL,
  `user_id` int NOT NULL,
  `vehicle_list_id` int NOT NULL,
  `image` longblob,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicle_number` (`vehicle_number`),
  KEY `user_id` (`user_id`),
  KEY `fk_vehicle_vehicle_list` (`vehicle_list_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vehicle`
--

INSERT INTO `vehicle` (`id`, `vehicle_number`, `user_id`, `vehicle_list_id`, `image`) VALUES
(12, 'KL11AU1098', 47, 38, NULL),
(13, 'KL05AR0001', 47, 25, NULL),
(14, 'KL05RZ100', 47, 28, NULL),
(15, 'HERCULES-800', 47, 37, NULL),
(26, 'KL-05 AE2345', 51, 37, NULL),
(29, 'KL-05 ME4466', 51, 40, NULL),
(30, 'KL-05 AE 666', 51, 38, NULL),
(34, 'KL-05 AA333', 51, 43, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicles_list`
--

DROP TABLE IF EXISTS `vehicles_list`;
CREATE TABLE IF NOT EXISTS `vehicles_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `model` varchar(100) NOT NULL,
  `manufacturer` varchar(50) NOT NULL,
  `launch_year` int NOT NULL,
  `notes` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vehicles_list`
--

INSERT INTO `vehicles_list` (`id`, `model`, `manufacturer`, `launch_year`, `notes`) VALUES
(25, 'Tata Nexon EV', 'Tata', 2023, 'Best-selling compact electric SUV in India'),
(26, 'Tata Tigor EV', 'Tata', 2022, 'Affordable electric sedan with fast charging'),
(27, 'Tata Punch EV', 'Tata', 2024, 'Compact EV with good ground clearance'),
(28, 'Tata Tiago EV', 'Tata', 2023, 'Entry-level electric hatchback with high efficiency'),
(29, 'MG ZS EV', 'MG', 2023, 'Premium electric SUV with good range'),
(30, 'MG Comet EV', 'MG', 2023, 'Ultra-compact city EV with a modern design'),
(31, 'Mahindra XUV400 EV', 'Mahindra', 2023, 'Mid-size electric SUV with long range'),
(32, 'Mahindra eVerito', 'Mahindra', 2022, 'Electric sedan used for fleet and commercial purposes'),
(33, 'Hyundai Kona Electric', 'Hyundai', 2023, 'Stylish crossover with fast charging'),
(34, 'Hyundai Ioniq 5', 'Hyundai', 2023, 'Premium crossover EV with futuristic features'),
(35, 'BYD Atto 3', 'BYD', 2023, 'Feature-loaded electric SUV with high safety rating'),
(36, 'BYD E6', 'BYD', 2022, 'MPV electric car mainly for commercial use'),
(37, 'Citroën ë-C3', 'Citroën', 2023, 'Electric version of the Citroën C3 hatchback'),
(38, 'Mercedes-Benz EQB', 'Mercedes-Benz', 2023, 'Luxury 7-seater electric SUV'),
(39, 'Mercedes-Benz EQS', 'Mercedes-Benz', 2023, 'Flagship luxury electric sedan with long range'),
(40, 'Kia EV6', 'Kia', 2023, 'Performance electric SUV with ultra-fast charging'),
(41, 'Audi e-tron', 'Audi', 2023, 'Luxury electric SUV with AWD capability'),
(42, 'Porsche Taycan', 'Porsche', 2023, 'High-performance electric sports car'),
(43, 'BMW iX', 'BMW', 2024, 'Luxury electric SUV with advanced technology'),
(44, 'BMW i4', 'BMW', 2023, 'Electric sedan with premium features and sporty design');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_services`
--

DROP TABLE IF EXISTS `vehicle_services`;
CREATE TABLE IF NOT EXISTS `vehicle_services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vehicle_id` int NOT NULL,
  `service_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicle_service_unique` (`vehicle_id`,`service_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bill`
--
ALTER TABLE `bill`
  ADD CONSTRAINT `bill_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service_rq` (`service_id`) ON DELETE CASCADE;

--
-- Constraints for table `mechanic`
--
ALTER TABLE `mechanic`
  ADD CONSTRAINT `mechanic_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_tbl` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mechanic_leave`
--
ALTER TABLE `mechanic_leave`
  ADD CONSTRAINT `mechanic_leave_ibfk_1` FOREIGN KEY (`mechanic_id`) REFERENCES `mechanic` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `user_tbl` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_plans_services`
--
ALTER TABLE `service_plans_services`
  ADD CONSTRAINT `service_plans_services_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `service_plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_plans_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_required_inventory`
--
ALTER TABLE `service_required_inventory`
  ADD CONSTRAINT `service_required_inventory_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_required_inventory_ibfk_2` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_rq`
--
ALTER TABLE `service_rq`
  ADD CONSTRAINT `service_rq_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_tbl` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_rq_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_rq_ibfk_3` FOREIGN KEY (`assigned_mechanic_id`) REFERENCES `mechanic` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `service_rq_ibfk_4` FOREIGN KEY (`plan_id`) REFERENCES `service_plans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `service_rq_ibfk_5` FOREIGN KEY (`slot_id`) REFERENCES `service_slots` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `service_rq_services`
--
ALTER TABLE `service_rq_services`
  ADD CONSTRAINT `service_rq_services_ibfk_1` FOREIGN KEY (`service_rq_id`) REFERENCES `service_rq` (`service_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_rq_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD CONSTRAINT `fk_vehicle_vehicle_list` FOREIGN KEY (`vehicle_list_id`) REFERENCES `vehicles_list` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `vehicle_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_tbl` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_services`
--
ALTER TABLE `vehicle_services`
  ADD CONSTRAINT `vehicle_services_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vehicle_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

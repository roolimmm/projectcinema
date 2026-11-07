-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 26, 2026 at 01:28 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12
--
-- FULL DATABASE RESET: Running this script drops the existing `cinema`
-- database, including all orders, then recreates it with the seed data.
-- Paste the entire file into the SQL terminal while connected as a user
-- that has permission to drop and create databases.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cinema`
--
DROP DATABASE IF EXISTS `cinema`;
CREATE DATABASE `cinema`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;
USE `cinema`;
START TRANSACTION;

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE IF NOT EXISTS `movies` (
  `movie_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `synopsis` text DEFAULT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `rating` varchar(10) DEFAULT NULL,
  `advisory` varchar(255) DEFAULT NULL,
  `poster_url` varchar(255) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `status` enum('now-showing','coming-soon') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`movie_id`, `title`, `synopsis`, `genre`, `duration`, `rating`, `advisory`, `poster_url`, `release_date`, `status`) VALUES
(1, 'Project Hail Mary', 'An astronaut embarks on a dangerous mission to uncover the mysteries of a distant planet.', 'Sci-Fi', 125, 'PG13', 'Some Violence', 'images/phm.jpg', '2026-05-06', 'now-showing'),
(2, 'The Invite', 'A detective investigates a series of mysterious disappearances in a quiet town.', 'Comedy', 110, 'M18', 'Mature Themes and Language', 'images/theinvite.jpg', '2026-07-18', 'now-showing'),
(3, 'Look Back', 'Two childhood friends reunite during an unforgettable summer.', 'Coming-of-Age', 105, 'PG', 'Brief Nudity', 'images/lookback.jpg', '2026-11-26', 'coming-soon'),
(4, 'Dune 3', 'A team of explorers travels across the galaxy in search of a new home.', 'Adventure', 135, 'PG13', 'Some Violence', 'images/dune3.jpg', '2026-12-18', 'coming-soon'),
(5, 'Spider-Man: Brand New Day', 'A spider bites a man, leading to a daring expedition into uncharted waters.', 'Action', 130, 'PG13', 'Some Violence', 'images/bnd.jpg', '2026-08-11', 'now-showing'),
(6, 'Avengers: Doomsday', 'A scientist discovers a device that reveals possible futures.', 'Sci-Fi', 115, 'PG13', 'Some Frightening Scenes', 'images/dd.jpg', '2026-12-25', 'coming-soon'),
(7, 'Shrek 5', 'Ogre, donkey and cat sets sail on another adventure.', 'Comedy', 100, 'PG', NULL, 'images/shrek.jpg', '2027-05-13', ''),
(8, 'HOPE', 'A police chief, a policewoman and a group of hunters try to track down and kill a mysterious creature that\'s laying waste to a Korean village.', 'Action', 125, 'NC16', 'Mature Themes', 'images/hope.jpg', '2026-10-01', 'now-showing'),
(9, 'Moana', 'Leaving her island for the first time, young Moana embarks on an epic seafaring adventure to restore prosperity to her people.', 'Adventure', 100, 'PG', NULL, 'images/moana.jpg', '2026-07-31', 'now-showing'),
(10, 'Focker-in-Law', 'Greg Focker\'s son, Henry, brings his girlfriend, a trained FBI hostage negotiator, home to meet the family. Greg soon gets worried when Henry tells everyone that he plans to propose to her.', 'Comedy', 120, 'PG', NULL, 'images/fil.jpg', '2026-11-19', 'coming-soon');

-- --------------------------------------------------------

--
-- Table structure for table `screenings`
--

CREATE TABLE `screenings` (
  `screening_id` int(11) NOT NULL AUTO_INCREMENT,
  `movie_id` int(11) NOT NULL,
  `cinema` varchar(100) NOT NULL,
  `screening_date` date NOT NULL,
  `screening_time` time NOT NULL,
  PRIMARY KEY (`screening_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `screenings`
--

INSERT INTO `screenings` (`screening_id`, `movie_id`, `cinema`, `screening_date`, `screening_time`) VALUES
(1, 1, 'Orchard', '2026-10-27', '12:30:00'),
(2, 1, 'Harbour', '2026-10-27', '19:00:00'),
(3, 1, 'Yishun', '2026-10-27', '15:45:00'),
(4, 1, 'Yishun', '2026-10-28', '14:00:00'),
(5, 1, 'Orchard', '2026-10-28', '20:30:00'),
(6, 1, 'Orchard', '2026-11-01', '13:30:00'),
(7, 1, 'Orchard', '2026-11-01', '19:15:00'),
(8, 1, 'Harbour', '2026-11-02', '18:30:00'),
(9, 1, 'Yishun', '2026-11-04', '19:00:00'),
(10, 1, 'Harbour', '2026-11-06', '16:00:00'),
(11, 1, 'Yishun', '2026-11-07', '20:00:00'),
(12, 2, 'Harbour', '2026-11-01', '16:00:00'),
(13, 2, 'Yishun', '2026-11-02', '20:15:00'),
(14, 2, 'Orchard', '2026-11-03', '18:45:00'),
(15, 2, 'Harbour', '2026-11-04', '20:30:00'),
(16, 2, 'Yishun', '2026-11-06', '17:00:00'),
(17, 2, 'Orchard', '2026-11-07', '20:30:00'),
(18, 5, 'Yishun', '2026-11-01', '11:30:00'),
(19, 5, 'Orchard', '2026-11-02', '20:00:00'),
(20, 5, 'Harbour', '2026-11-03', '17:30:00'),
(21, 5, 'Yishun', '2026-11-05', '19:45:00'),
(22, 5, 'Orchard', '2026-11-06', '16:30:00'),
(23, 5, 'Harbour', '2026-11-07', '20:15:00'),
(24, 8, 'Orchard', '2026-11-01', '20:00:00'),
(25, 8, 'Harbour', '2026-11-02', '19:30:00'),
(26, 8, 'Yishun', '2026-11-03', '18:00:00'),
(27, 8, 'Orchard', '2026-11-04', '20:30:00'),
(28, 8, 'Harbour', '2026-11-06', '19:45:00'),
(29, 8, 'Yishun', '2026-11-07', '20:30:00'),
(30, 9, 'Harbour', '2026-11-01', '10:30:00'),
(31, 9, 'Yishun', '2026-11-02', '14:00:00'),
(32, 9, 'Orchard', '2026-11-03', '15:30:00'),
(33, 9, 'Harbour', '2026-11-05', '11:00:00'),
(34, 9, 'Yishun', '2026-11-06', '14:30:00'),
(35, 9, 'Orchard', '2026-11-07', '11:30:00'),
(36, 1, 'Orchard', '2026-11-10', '13:00:00'),
(37, 1, 'Harbour', '2026-11-14', '19:30:00'),
(38, 1, 'Yishun', '2026-11-21', '16:15:00'),
(39, 1, 'Harbour', '2026-12-01', '18:00:00'),
(40, 1, 'Orchard', '2026-12-10', '20:15:00'),
(41, 1, 'Yishun', '2026-12-20', '14:00:00'),
(42, 2, 'Yishun', '2026-11-09', '17:30:00'),
(43, 2, 'Orchard', '2026-11-15', '20:00:00'),
(44, 2, 'Harbour', '2026-11-25', '18:30:00'),
(45, 2, 'Yishun', '2026-12-03', '19:15:00'),
(46, 2, 'Orchard', '2026-12-15', '16:45:00'),
(47, 2, 'Harbour', '2026-12-28', '20:30:00'),
(48, 5, 'Harbour', '2026-11-11', '19:00:00'),
(49, 5, 'Yishun', '2026-11-18', '16:30:00'),
(50, 5, 'Orchard', '2026-11-27', '20:00:00'),
(51, 5, 'Harbour', '2026-12-06', '17:15:00'),
(52, 5, 'Yishun', '2026-12-17', '19:45:00'),
(53, 5, 'Orchard', '2026-12-29', '14:30:00'),
(54, 8, 'Yishun', '2026-11-12', '18:15:00'),
(55, 8, 'Orchard', '2026-11-19', '20:30:00'),
(56, 8, 'Harbour', '2026-11-30', '19:00:00'),
(57, 8, 'Yishun', '2026-12-08', '17:30:00'),
(58, 8, 'Orchard', '2026-12-19', '20:15:00'),
(59, 8, 'Harbour', '2026-12-30', '18:45:00'),
(60, 9, 'Orchard', '2026-11-13', '11:00:00'),
(61, 9, 'Harbour', '2026-11-22', '14:30:00'),
(62, 9, 'Yishun', '2026-12-04', '10:45:00'),
(63, 9, 'Orchard', '2026-12-11', '13:15:00'),
(64, 9, 'Harbour', '2026-12-22', '11:30:00'),
(65, 9, 'Yishun', '2026-12-31', '15:00:00'),
(66, 3, 'Orchard', '2026-11-26', '18:00:00'),
(67, 3, 'Harbour', '2026-11-28', '20:15:00'),
(68, 3, 'Yishun', '2026-12-02', '17:45:00'),
(69, 3, 'Orchard', '2026-12-06', '19:30:00'),
(70, 3, 'Harbour', '2026-12-12', '16:00:00'),
(71, 4, 'Harbour', '2026-12-18', '18:30:00'),
(72, 4, 'Orchard', '2026-12-20', '20:00:00'),
(73, 4, 'Yishun', '2026-12-24', '17:15:00'),
(74, 4, 'Harbour', '2026-12-27', '19:45:00'),
(75, 4, 'Orchard', '2026-12-31', '16:30:00'),
(76, 6, 'Yishun', '2026-12-25', '14:00:00'),
(77, 6, 'Harbour', '2026-12-27', '18:15:00'),
(78, 6, 'Orchard', '2026-12-30', '20:30:00'),
(79, 10, 'Yishun', '2026-11-20', '19:00:00'),
(80, 10, 'Orchard', '2026-11-23', '18:00:00'),
(81, 10, 'Harbour', '2026-11-29', '20:15:00'),
(82, 10, 'Yishun', '2026-12-05', '16:30:00'),
(83, 10, 'Orchard', '2026-12-13', '19:45:00');

-- Add up to four recurring weekly screenings per movie, rotating between cinemas.
-- Release dates are respected, and the schedule ends on December 31, 2026.
INSERT INTO `screenings` (`movie_id`, `cinema`, `screening_date`, `screening_time`)
SELECT
    m.`movie_id`,
    CASE MOD(m.`movie_id` + schedule_weeks.`week_no` + screening_slots.`slot_no`, 3)
        WHEN 0 THEN 'Orchard'
        WHEN 1 THEN 'Harbour'
        ELSE 'Yishun'
    END,
    DATE_ADD(schedule_weeks.`week_start`, INTERVAL screening_slots.`day_offset` DAY),
    CASE screening_slots.`slot_no`
        WHEN 1 THEN '11:30:00'
        WHEN 2 THEN '14:30:00'
        WHEN 3 THEN '18:00:00'
        ELSE '20:30:00'
    END
FROM `movies` AS m
CROSS JOIN (
    SELECT 0 AS `week_no`, DATE('2026-11-09') AS `week_start`
    UNION ALL SELECT 1, DATE('2026-11-16')
    UNION ALL SELECT 2, DATE('2026-11-23')
    UNION ALL SELECT 3, DATE('2026-11-30')
    UNION ALL SELECT 4, DATE('2026-12-07')
    UNION ALL SELECT 5, DATE('2026-12-14')
    UNION ALL SELECT 6, DATE('2026-12-21')
    UNION ALL SELECT 7, DATE('2026-12-28')
) AS schedule_weeks
CROSS JOIN (
    SELECT 1 AS `slot_no`, 0 AS `day_offset`
    UNION ALL SELECT 2, 2
    UNION ALL SELECT 3, 4
    UNION ALL SELECT 4, 6
) AS screening_slots
WHERE m.`movie_id` <> 7
  AND m.`status` IN ('now-showing', 'coming-soon')
  AND DATE_ADD(schedule_weeks.`week_start`, INTERVAL screening_slots.`day_offset` DAY) >= m.`release_date`
  AND DATE_ADD(schedule_weeks.`week_start`, INTERVAL screening_slots.`day_offset` DAY) <= '2026-12-31';

-- --------------------------------------------------------

--
-- Table structure for table `screening_seats`
--

CREATE TABLE `screening_seats` (
  `screening_seat_id` int(11) NOT NULL AUTO_INCREMENT,
  `screening_id` int(11) NOT NULL,
  `seat_label` varchar(4) NOT NULL,
  `status` enum('available','unavailable') NOT NULL DEFAULT 'available',
  PRIMARY KEY (`screening_seat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Orders use a random 32-character hexadecimal ID as the customer-facing reference.
CREATE TABLE `orders` (
  `order_id` char(32) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `order_tickets` (
  `order_ticket_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` char(32) NOT NULL,
  `screening_id` int(11) NOT NULL,
  `seat_label` varchar(4) NOT NULL,
  `ticket_price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`order_ticket_id`),
  UNIQUE KEY `screening_seat_ordered` (`screening_id`, `seat_label`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Seed every 8 x 10 seat plan for each existing screening.
--

INSERT INTO `screening_seats` (`screening_id`, `seat_label`, `status`)
SELECT
    s.screening_id,
    CONCAT(CHAR(64 + seat_rows.row_num), seat_columns.seat_num),
    CASE
        WHEN MOD(s.screening_id + seat_rows.row_num + seat_columns.seat_num * 3, 11) = 0
          OR (seat_rows.row_num = 4 AND seat_columns.seat_num = 5)
        THEN 'unavailable'
        ELSE 'available'
    END
FROM screenings s
CROSS JOIN (
    SELECT 1 AS row_num UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
    UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
) AS seat_rows
CROSS JOIN (
    SELECT 1 AS seat_num UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
    UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
) AS seat_columns;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`movie_id`);

--
-- Indexes for table `screenings`
--
ALTER TABLE `screenings`
  ADD KEY `movie_id` (`movie_id`);

--
-- Indexes for table `screening_seats`
--
ALTER TABLE `screening_seats`
  ADD UNIQUE KEY `screening_seat` (`screening_id`, `seat_label`),
  ADD KEY `screening_id` (`screening_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `movie_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `screenings`
--
ALTER TABLE `screenings`
  MODIFY `screening_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=290;

ALTER TABLE `screening_seats`
  MODIFY `screening_seat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23121;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `screenings`
--
ALTER TABLE `screenings`
  ADD CONSTRAINT `screenings_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`);

ALTER TABLE `screening_seats`
  ADD CONSTRAINT `screening_seats_ibfk_1` FOREIGN KEY (`screening_id`) REFERENCES `screenings` (`screening_id`) ON DELETE CASCADE;

ALTER TABLE `order_tickets`
  ADD CONSTRAINT `order_tickets_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_tickets_ibfk_2` FOREIGN KEY (`screening_id`) REFERENCES `screenings` (`screening_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

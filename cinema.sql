-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 26, 2026 at 01:28 PM
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
-- Database: `cinema`
--
CREATE DATABASE IF NOT EXISTS `cinema`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;
USE `cinema`;

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
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
  `screening_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `cinema` varchar(100) NOT NULL,
  `screening_date` date NOT NULL,
  `screening_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `screenings`
--

INSERT INTO `screenings` (`screening_id`, `movie_id`, `cinema`, `screening_date`, `screening_time`) VALUES
(1, 1, 'Orchard', '2026-10-27', '12:30:00'),
(2, 1, 'Harbour', '2026-10-27', '19:00:00'),
(3, 1, 'Yishun', '2026-10-27', '15:45:00'),
(4, 1, 'Yishun', '2026-10-28', '14:00:00'),
(5, 1, 'Orchard', '2026-10-28', '20:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `screening_seats`
--

CREATE TABLE `screening_seats` (
  `screening_seat_id` int(11) NOT NULL,
  `screening_id` int(11) NOT NULL,
  `seat_label` varchar(4) NOT NULL,
  `status` enum('available','unavailable') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Seed every 8 x 10 seat plan for each existing screening.
--

INSERT INTO `screening_seats` (`screening_id`, `seat_label`, `status`)
SELECT
    s.screening_id,
    CONCAT(CHAR(64 + r.row_number), seat_number.seat_number),
    CASE
        WHEN MOD(s.screening_id + r.row_number + seat_number.seat_number * 3, 11) = 0
          OR (r.row_number = 4 AND seat_number.seat_number = 5)
        THEN 'unavailable'
        ELSE 'available'
    END
FROM screenings s
CROSS JOIN (
    SELECT 1 AS row_number UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
    UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
) r
CROSS JOIN (
    SELECT 1 AS seat_number UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
    UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
) seat_number;

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
  ADD PRIMARY KEY (`screening_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Indexes for table `screening_seats`
--
ALTER TABLE `screening_seats`
  ADD PRIMARY KEY (`screening_seat_id`),
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
  MODIFY `screening_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `screening_seats`
  MODIFY `screening_seat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=401;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

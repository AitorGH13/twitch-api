-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 10.200.9.204
-- Generation Time: Feb 27, 2025 at 05:00 PM
-- Server version: 10.5.28-MariaDB-deb11-log
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `twitchanalytics`
--

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE `tokens` (
  `email` varchar(256) NOT NULL,
  `api_key` varchar(256) NOT NULL,
  `token` varchar(256) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tokens`
--

INSERT INTO `tokens` (`email`, `api_key`, `token`, `expires_at`) VALUES
('admin@dxl.com', '', 'unlimitedtoken', '2025-12-31 23:59:59'),
('asd@gmail.com', 'e187974a8d36c32de7772c4981c7a63e', '892cb9b038b307aae79d863b3709dbf1', '2025-02-22 16:22:41'),
('asds@gmail.com', '21815b32f5503627', '11fa4bb18d25f645fd8724d24abfa99e', '2025-03-02 16:19:22'),
('hola@gmail.com', '22462d4861972e13', '46e344644e3715a5148553cd4735e9c1', '2025-02-22 16:45:43');

-- --------------------------------------------------------

--
-- Table structure for table `topsofthetops`
--

CREATE TABLE `topsofthetops` (
  `id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `game_name` varchar(256) NOT NULL,
  `user_name` varchar(256) NOT NULL,
  `total_videos` int(11) NOT NULL,
  `total_views` int(11) NOT NULL,
  `mv_title` varchar(2048) NOT NULL,
  `mv_views` int(11) NOT NULL,
  `mv_duration` varchar(256) NOT NULL,
  `mv_created_at` varchar(256) NOT NULL,
  `expires_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `topsofthetops`
--

INSERT INTO `topsofthetops` (`id`, `game_id`, `game_name`, `user_name`, `total_videos`, `total_views`, `mv_title`, `mv_views`, `mv_duration`, `mv_created_at`, `expires_at`) VALUES
(28, 509658, 'Just Chatting', 'KaiCenat', 36, 414863296, '???? MAFIATHON 2 ???? KAI X KEVIN HART X DRUSKI ???? DAY 27 ???? 20% OF REVENUE GOING TO SCHOOL IN NIGERIA ???? ALL MONTH ???? CLICK HERE ???? !Subathon', 24870042, '22h5m32s', '2024-11-28T02:06:07Z', '2025-02-20 16:01:43'),
(29, 516575, 'VALORANT', 'Ninja', 3, 7705401, 'V a l o r a n t Grind begins | Among us tonight at 8 CENTRAL with a SOLID crew! ', 4549589, '15h15m21s', '2020-09-11T18:52:09Z', '2025-02-20 16:01:43'),
(30, 21779, 'League of Legends', 'Riot Games', 26, 124973970, 'WORLDS 22 FINALS COUNTDOWN', 11620707, '9h25m12s', '2022-11-05T21:00:23Z', '2025-02-20 16:01:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(64) NOT NULL,
  `login` varchar(128) DEFAULT NULL,
  `display_name` varchar(256) DEFAULT NULL,
  `type` varchar(256) DEFAULT NULL,
  `broadcaster_type` varchar(256) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  `profile_image_url` varchar(512) DEFAULT NULL,
  `offline_image_url` varchar(512) DEFAULT NULL,
  `view_count` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `login`, `display_name`, `type`, `broadcaster_type`, `description`, `profile_image_url`, `offline_image_url`, `view_count`, `created_at`) VALUES
('1001', 'dohyun990', 'dohyun990', '', '', '', 'https://static-cdn.jtvnw.net/user-default-pictures-uv/13e5fa74-defa-11e9-809c-784f43822e80-profile_image-300x300.png', '', 0, '2018-09-04 15:22:20'),
('640542', 'one_two_teddy', 'one_two_teddy', '', '', '', 'https://static-cdn.jtvnw.net/user-default-pictures-uv/cdd517fe-def4-11e9-948e-784f43822e80-profile_image-300x300.png', '', 0, '2008-05-25 23:20:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`email`),
  ADD UNIQUE KEY `token_unique` (`token`);

--
-- Indexes for table `topsofthetops`
--
ALTER TABLE `topsofthetops`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `topsofthetops`
--
ALTER TABLE `topsofthetops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

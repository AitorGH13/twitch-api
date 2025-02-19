-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 10.200.9.204
-- Generation Time: Feb 19, 2025 at 08:07 PM
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
('asds@gmail.com', 'f5618b2c75319824', '2f3796adea879f46abc8b9a87968659c', '2025-02-22 16:44:51'),
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
(22, 509658, 'Just Chatting', 'KaiCenat', 36, 414858892, '???? MAFIATHON 2 ???? KAI X KEVIN HART X DRUSKI ???? DAY 27 ???? 20% OF REVENUE GOING TO SCHOOL IN NIGERIA ???? ALL MONTH ???? CLICK HERE ???? !Subathon', 24869073, '22h5m32s', '2024-11-28T02:06:07Z', '2025-02-19 20:09:43'),
(23, 21779, 'League of Legends', 'Riot Games', 26, 124973894, 'WORLDS 22 FINALS COUNTDOWN', 11620701, '9h25m12s', '2022-11-05T21:00:23Z', '2025-02-19 20:09:43'),
(24, 29595, 'Dota 2', 'dota2ti_ru', 21, 148459279, '[RU] Team Secret vs Team Spirit | Основной этап | The International 10 | День 6', 17072310, '12h45m1s', '2021-10-17T05:57:42Z', '2025-02-19 20:09:43');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

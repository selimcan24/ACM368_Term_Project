-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2026 at 12:29 PM
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
-- Database: `game_log_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `release_year` int(11) DEFAULT NULL,
  `developer` varchar(100) DEFAULT NULL,
  `cover_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `title`, `release_year`, `developer`, `cover_url`, `description`, `created_at`) VALUES
(1, 'Devil May Cry 3', 2005, 'Capcom', 'https://upload.wikimedia.org/wikipedia/en/7/76/Devil_May_Cry_3_boxshot.jpg', 'Devil May Cry 3: Dante\'s Awakening is a 2005 action-adventure game developed and published by Capcom for the PlayStation 2. The game is a prequel to the original Devil May Cry, featuring a younger Dante.', '2026-05-28 09:29:11'),
(2, 'Silent Hill 2', 2001, 'Konami', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRrF7EFmbp_rvrBMoxVkghdg5Gy9FzdBxqBxQ&s', 'Silent Hill 2 is a 2001 survival horror video game developed by Team Silent, a group in Konami Computer Entertainment Tokyo, and published by Konami for the PlayStation 2.', '2026-05-28 09:35:54'),
(3, 'Resident Evil 1', 1996, 'Capcom', 'https://upload.wikimedia.org/wikipedia/en/thumb/a/a6/Resident_Evil_1_cover.png/250px-Resident_Evil_1_cover.png', 'Resident Evil is a 1996 survival horror video game developed and published by Capcom for the PlayStation. It is the first main installment in Capcom\'s Resident Evil series.', '2026-05-28 10:23:47'),
(4, 'Cyberpunk 2077', 2020, 'CD Project RED', 'https://upload.wikimedia.org/wikipedia/en/9/9f/Cyberpunk_2077_box_art.jpg', 'Cyberpunk 2077 is a 2020 action role-playing game developed by CD Projekt Red and published by CD Projekt. Based on Mike Pondsmith\'s Cyberpunk tabletop game series, the plot is set in the fictional metropolis of Night City in California, within the dystopian Cyberpunk universe.', '2026-05-28 10:24:28'),
(5, 'Elden Ring', 2022, 'FromSoftware Inc', 'https://upload.wikimedia.org/wikipedia/en/b/b9/Elden_Ring_Box_art.jpg', 'Elden Ring is a 2022 action role-playing game directed by Hidetaka Miyazaki with worldbuilding provided by the American fantasy writer George R. R. Martin.', '2026-05-28 10:25:31'),
(6, 'Bloodborne', 2015, 'FromSoftware Inc', 'https://upload.wikimedia.org/wikipedia/en/6/68/Bloodborne_Cover_Wallpaper.jpg', 'Bloodborne is a 2015 action role-playing game, developed by FromSoftware and published by Sony Computer Entertainment for the PlayStation 4.', '2026-05-28 10:26:02');

-- --------------------------------------------------------

--
-- Table structure for table `game_logs`
--

CREATE TABLE `game_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `rating` tinyint(3) UNSIGNED DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text DEFAULT NULL,
  `status` enum('backlog','playing','completed','dropped') DEFAULT 'completed',
  `logged_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game_logs`
--

INSERT INTO `game_logs` (`id`, `user_id`, `game_id`, `rating`, `review_text`, `status`, `logged_at`) VALUES
(1, 2, 2, 5, 'Absolute masterpiece and a must play for horror lovers!', 'completed', '2026-05-28 09:44:51'),
(2, 2, 1, 3, 'It was good, but i didnt wanted to continue playing it. Maybe hack  \'n slash enjoyers would like it.', 'dropped', '2026-05-28 10:07:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `created_at`, `role`) VALUES
(1, 'admin1', 'user@example.com', '$2y$10$kA/SCUbKERRcTIC9hjfr0OJhSWwCL6mc8uKGh.PYoudfiQkOvhw8G', '2026-05-28 09:22:28', 'admin'),
(2, 'user1', 'user1@example.com', '$2y$10$wM/kFJepfkxgmh3/zyO9G.79e3xdC.97Av4PoFiuTWvBz9ZBQ7fd2', '2026-05-28 09:36:17', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `game_logs`
--
ALTER TABLE `game_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `game_logs`
--
ALTER TABLE `game_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `game_logs`
--
ALTER TABLE `game_logs`
  ADD CONSTRAINT `game_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_logs_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

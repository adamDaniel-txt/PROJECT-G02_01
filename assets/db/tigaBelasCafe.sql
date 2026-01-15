-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 15, 2026 at 03:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tigaBelasCafe`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id` int(11) NOT NULL,
  `user_name` varchar(100) DEFAULT 'Anonymous',
  `user_email` varchar(100) DEFAULT NULL,
  `feedback_text` text NOT NULL,
  `rating` int(1) DEFAULT 3,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`id`, `user_name`, `user_email`, `feedback_text`, `rating`, `created_at`) VALUES
(1, 'Anonymous', NULL, 'very good very nice', 3, '2026-01-12 11:31:17'),
(2, 'Anonymous', NULL, 'i very like this coffee, it is very goood', 3, '2026-01-14 22:48:15'),
(3, 'Anonymous', NULL, 'i loooove this cafe', 5, '2026-01-14 22:48:33'),
(4, 'Anonymous', NULL, 'It is okay, overrated', 1, '2026-01-14 23:49:20');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(50) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `description`, `price`, `category`, `image_url`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 'Espresso', 'Strong black coffee made by forcing steam through ground coffee beans', 3.50, 'Coffee', 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ftse1.mm.bing.net%2Fth%2Fid%2FOIP.NZGzV2GOHNqBXmBPXiH6YwHaE8%3Fcb%3Ddefcache2%26pid%3DApi%26defcache%3D1&f=1&ipt=61f57b0bd7a48fdd80170f1e1246bde987049c453e3f764de40c3b102561b3b3&ipo=images', 1, '2026-01-14 17:17:36', '2026-01-15 02:25:46'),
(2, 'Cappuccino', 'Espresso with steamed milk and a layer of milk foam', 4.50, 'Coffee', 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ftse1.mm.bing.net%2Fth%2Fid%2FOIP.kbjQCtA_at72z3EldA1UmQHaFJ%3Fpid%3DApi&f=1&ipt=d4a8a2daa404d6ba2ff2d3ba2e744e7f45ec1b9c6e5d9915f383b721979ec60e&ipo=images', 1, '2026-01-14 17:17:36', '2026-01-15 02:24:40'),
(3, 'Latte', 'Espresso with steamed milk and a light layer of foam', 5.00, 'Coffee', 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ftse1.mm.bing.net%2Fth%2Fid%2FOIP.6Lb2xzBM3B6maFHPDwhw5AHaE8%3Fpid%3DApi&f=1&ipt=1d8c9eb33ac101c33c5a67ceb1516f78667fadee1c94ecc6fa3a223e48e72ba8&ipo=images', 1, '2026-01-14 17:17:36', '2026-01-15 02:26:06'),
(4, 'Hot Chocolate', 'Rich chocolate drink with steamed milk', 4.00, 'Non-Coffee', 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ftse1.mm.bing.net%2Fth%2Fid%2FOIP.ZrDN-j89uEKUSh7rWteCxAHaE8%3Fpid%3DApi&f=1&ipt=e04c69e671ca6fdec7673d2b9af3d379060d9a41de954b926644e30f0767dafe&ipo=images', 1, '2026-01-14 17:17:36', '2026-01-15 02:27:17'),
(5, 'Green Tea', 'Refreshing traditional green tea', 3.00, 'Tea', 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ftse1.mm.bing.net%2Fth%2Fid%2FOIP.OrD-0SE3pvuxamciektv-AHaE8%3Fpid%3DApi&f=1&ipt=2a8975355f6d015458d8960615a34375eb2feefa3a0519bccc2ff887b2073887&ipo=images', 1, '2026-01-14 17:17:36', '2026-01-15 02:27:41'),
(6, 'Chocolate Cake', 'Rich chocolate cake with ganache frosting', 6.50, 'Dessert', 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ftse1.mm.bing.net%2Fth%2Fid%2FOIP.EH61MhxlPzlSlbABSSVLawHaE8%3Fpid%3DApi&f=1&ipt=c909f3718a347a973fd715bb67ff3c5b82c5d0802aa40a8815da9a68c966de7e&ipo=images', 1, '2026-01-14 17:17:36', '2026-01-15 02:26:34'),
(7, 'Croissant', 'Freshly baked butter croissant', 3.50, 'Bakery', 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ftse1.mm.bing.net%2Fth%2Fid%2FOIP.oiiTqE1huSD65JUzFiXVCwHaHa%3Fpid%3DApi&f=1&ipt=7a594e6cb023c6e5664dc6b1171d7fa11cbb902b1bbb93e743490e6ec0f9f872&ipo=images', 1, '2026-01-14 17:17:36', '2026-01-14 17:23:47'),
(8, 'Club Sandwich', 'Turkey, bacon, lettuce, tomato with mayo', 8.50, 'Food', 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ftse1.mm.bing.net%2Fth%2Fid%2FOIP.sjci0ZZ6vOr5OyBbPYhcYwHaFj%3Fpid%3DApi&f=1&ipt=afb450587920559c908487666f871ee4222739455b91a3bf9653911b0e742dfa&ipo=images', 1, '2026-01-14 17:17:36', '2026-01-15 02:26:56');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `permission_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `permission_name`) VALUES
(3, 'create_feedback'),
(4, 'manage_feedback'),
(1, 'view_dashboard'),
(2, 'view_feedback');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'Admin'),
(3, 'Customer'),
(4, 'Guest'),
(2, 'Staff');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(2, 1),
(2, 3),
(2, 4),
(3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(64) DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role_id`, `email_verified`, `verification_token`, `token_expires`) VALUES
(10, 'admin', 'admin@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 1, 1, NULL, NULL),
(11, 'guest', 'guest@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 4, 1, NULL, NULL),
(12, 'customer', 'customer@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 3, 1, NULL, NULL),
(13, 'staff', 'staff@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 2, 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_available` (`is_available`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 11, 2026 at 01:14 AM
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
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

DROP TABLE IF EXISTS `feedbacks`;
CREATE TABLE `feedbacks` (
  `id` int(11) NOT NULL,
  `feedback_text` text NOT NULL,
  `rating` int(1) DEFAULT 3,
  `created_at` datetime NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`id`, `feedback_text`, `rating`, `created_at`, `user_id`) VALUES
(5, 'Very Good, Very Nice', 5, '2026-02-10 23:26:46', 14),
(6, 'Pretty Mid, Overrated', 3, '2026-02-10 23:29:34', 15),
(7, 'Great Atmosphere, would come again', 5, '2026-02-10 23:36:50', 14);

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
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
(3, 'Latte', 'A smooth and comforting blend of espresso with plenty of creamy steamed milk', 11.00, 'Coffee', 'https://www.brighteyedbaker.com/wp-content/uploads/2024/07/Dulce-de-Leche-Latte-Recipe.jpg', 1, '2026-01-14 17:17:36', '2026-01-22 09:14:21'),
(5, 'Green Tea', 'Refreshing traditional green tea', 7.00, 'Tea', 'https://tuyabeauty.com/cdn/shop/articles/Benefits_of_Green_Tea_For_Skin.jpg', 1, '2026-01-14 17:17:36', '2026-01-22 09:19:31'),
(10, 'Americano', 'A clean and robust classic made with deep espresso shots and hot water', 7.00, 'Coffee', 'https://mocktail.net/wp-content/uploads/2022/05/homemade-Iced-Americano-recipe_5.jpg', 1, '2026-01-22 09:21:17', '2026-02-10 11:06:48'),
(11, 'Cappuccino', 'A rich espresso base topped with a thick, velvety layer of steamed milk foam', 11.00, 'Coffee', 'https://www.shutterstock.com/image-photo/heart-shaped-latte-art-white-600nw-2506388167.jpg', 1, '2026-01-22 09:23:15', '2026-01-22 09:23:15'),
(12, 'Spanish Latte', 'A rich, velvety espresso drink sweetened with a touch of condensed milk', 13.00, 'Coffee', 'https://img.freepik.com/premium-photo/cappuccino-latte-with-milk-foam-caramel-glass-with-coffee-beans-light-marble-background-with-branches-front-view-copy-space_185452-4001.jpg', 1, '2026-01-22 09:25:52', '2026-01-22 09:29:53'),
(13, 'Hazelnut latte', 'A fragrant and nutty twist on our classic latte with sweet hazelnut notes', 13.00, 'Coffee', 'https://tyberrymuch.com/wp-content/uploads/2024/03/Hazelnut-Iced-Coffee-Hero.jpg', 1, '2026-01-22 09:28:49', '2026-01-22 09:34:06'),
(14, 'Creme Brulee Latte', 'A dessert-inspired latte featuring a caramelized sugar finish and custard-like sweetness', 14.00, 'Coffee', 'https://www.shutterstock.com/image-photo/creme-brulee-milk-tea-cold-600nw-2424254335.jpg', 1, '2026-01-22 09:32:24', '2026-01-22 09:35:53'),
(16, 'Macchiato', 'A bold, concentrated espresso \"marked\" with a light dollop of silky milk foam', 13.00, 'Coffee', 'https://jivajava.cafe/coffeetalk/wp-content/uploads/2024/04/macchiato-e1713123117777.jpg', 1, '2026-01-22 09:40:47', '2026-01-22 09:40:47'),
(17, 'Chocolate', 'A decadent and smooth cocoa experience, perfect for any time of day', 13.00, 'Non-Coffee', 'https://coffeeclub.com.au/cdn/shop/files/Beverages_Product_Images_1200x1200_IcedChoc.jpg?v=1716270110', 1, '2026-01-22 09:42:28', '2026-01-22 09:42:28'),
(18, 'Matcha', 'Earthy, premium green tea whisked with milk for a vibrant and creamy energy boost', 13.00, 'Non-Coffee', 'https://iswari.s3.eu-west-3.amazonaws.com/products/aeqdws-iced%20matcha%20latte-5%20-%20c%C3%B3pia.jpg', 1, '2026-01-22 09:43:37', '2026-01-22 09:43:37'),
(19, 'Strawberry Latte', 'A fresh and fruity milk-based drink layered with sweet strawberry puree', 12.00, 'Coffee', 'https://cookhousediary.com/wp-content/uploads/2025/02/strawberry-latte-in-glass.jpg', 1, '2026-01-22 09:44:52', '2026-01-22 09:44:52'),
(20, 'Sakura Grape Sparkling', 'A light and fizzy refresher with delicate floral notes and sweet grape', 12.00, 'Refreshing', 'https://mocktail.net/wp-content/uploads/2022/05/Homemade-Grape-Soda_1.jpg', 1, '2026-01-22 09:47:29', '2026-01-22 09:47:29'),
(21, 'Peach Soda', 'A bright, bubbly iced drink bursting with juicy peach flavor', 12.00, 'Refreshing', 'https://img.freepik.com/premium-photo/refreshing-peach-ice-mint-tea-vegan-homemade-cold-summer-drink-tall-glass-orange-background-with-fresh-fruits_185452-6410.jpg?semt=ais_hybrid&w=740&q=80', 1, '2026-01-22 09:49:36', '2026-01-22 09:55:25'),
(22, 'Mojito', 'A zesty and cooling blend of fresh lime and mint for a sparkling citrus kick', 12.00, 'Refreshing', 'https://www.saveur.com/uploads/2007/02/SAVEUR_Mojito_1149-Edit-scaled.jpg?auto=webp', 1, '2026-01-22 09:52:04', '2026-01-22 09:52:04'),
(23, 'Earl Grey Tea', 'A classic black tea infused with the distinct citrus aroma of bergamot', 7.00, 'Tea', 'https://weeteacompany.com/wp-content/uploads/2024/11/Vanilla-Earl-Grey-Tea.webp', 1, '2026-01-22 09:53:26', '2026-01-22 09:53:26');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_id` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `estimated_ready_time` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `pickup_code` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `payment_id`, `status`, `created_at`, `updated_at`, `estimated_ready_time`, `notes`, `pickup_code`) VALUES
(31, 14, 30.00, 'cs_test_b1iSQyLAgyvigfHrgzbLsHrr7KwArxuXOsN2CXCrfmhgg6gi8roQEpMm4r', 'completed', '2026-02-10 15:38:51', '2026-02-10 16:00:56', NULL, NULL, NULL),
(32, 14, 37.00, 'cs_test_b1pGSYzgbUlyfXHpCooQlPZCCYwGvtkGLeTXsQ7kRK44AXmjnNZkaItNJt', 'ready', '2026-02-10 15:45:05', '2026-02-10 16:28:07', NULL, NULL, 'FBF8C8'),
(33, 14, 52.00, 'cs_test_b1fcnnChkfuylLeavbs1tZ8E1kg7W9jYdzlPgg2fuQkFTj8OHJPdph4JzJ', 'preparing', '2026-02-10 16:03:10', '2026-02-10 16:27:54', '2026-02-10 17:42:54', NULL, NULL),
(34, 14, 59.00, 'cs_test_b1n22DKlRo3W2N2dBwIfRoTtYjN8BoHxgookBFqNg8xosTKeKQUnwLkIWF', 'confirmed', '2026-02-10 16:04:06', '2026-02-10 16:27:48', NULL, NULL, NULL),
(35, 15, 7.00, 'cs_test_a1RbLC2V5jSsexrZUF8IVBh6KFfLZGAsFljUcLwg0gfQ8nHgEBh6IeFZ7r', 'cancelled', '2026-02-10 16:05:23', '2026-02-10 16:58:46', NULL, NULL, NULL),
(36, 15, 170.00, 'cs_test_b1cdrh0h6RbHDNkzKQsr4TXofbC2TOH352SNCz1RTXdFBn2pk64uC3Z7Mi', 'pending', '2026-02-10 16:07:09', '2026-02-10 16:07:09', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price`) VALUES
(22, 31, 19, 1, 12.00),
(23, 31, 3, 1, 11.00),
(24, 31, 10, 1, 7.00),
(25, 32, 16, 1, 13.00),
(26, 32, 3, 1, 11.00),
(27, 32, 13, 1, 13.00),
(28, 33, 5, 2, 7.00),
(29, 33, 20, 1, 12.00),
(30, 33, 23, 1, 7.00),
(31, 33, 22, 1, 12.00),
(32, 33, 10, 1, 7.00),
(33, 34, 16, 1, 13.00),
(34, 34, 13, 3, 13.00),
(35, 34, 10, 1, 7.00),
(36, 35, 10, 1, 7.00),
(37, 36, 5, 1, 7.00),
(38, 36, 23, 1, 7.00),
(39, 36, 20, 1, 12.00),
(40, 36, 21, 1, 12.00),
(41, 36, 22, 1, 12.00),
(42, 36, 18, 1, 13.00),
(43, 36, 19, 1, 12.00),
(44, 36, 17, 1, 13.00),
(45, 36, 12, 1, 13.00),
(46, 36, 16, 1, 13.00),
(47, 36, 3, 1, 11.00),
(48, 36, 13, 1, 13.00),
(49, 36, 14, 1, 14.00),
(50, 36, 11, 1, 11.00),
(51, 36, 10, 1, 7.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_status_logs`
--

DROP TABLE IF EXISTS `order_status_logs`;
CREATE TABLE `order_status_logs` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_status_logs`
--

INSERT INTO `order_status_logs` (`id`, `order_id`, `status`, `notes`, `created_at`) VALUES
(31, 31, 'pending', 'Order created and payment confirmed', '2026-02-10 15:38:51'),
(32, 32, 'pending', 'Order created and payment confirmed', '2026-02-10 15:45:05'),
(33, 31, 'completed', '', '2026-02-10 16:00:56'),
(34, 32, 'confirmed', '', '2026-02-10 16:01:20'),
(35, 33, 'pending', 'Order created and payment confirmed', '2026-02-10 16:03:10'),
(36, 34, 'pending', 'Order created and payment confirmed', '2026-02-10 16:04:06'),
(37, 35, 'pending', 'Order created and payment confirmed', '2026-02-10 16:05:23'),
(38, 36, 'pending', 'Order created and payment confirmed', '2026-02-10 16:07:09'),
(39, 35, 'cancelled', '', '2026-02-10 16:24:34'),
(40, 35, 'pending', '', '2026-02-10 16:27:17'),
(41, 35, 'cancelled', 'Customer requested cancellation', '2026-02-10 16:27:26'),
(42, 34, 'confirmed', '', '2026-02-10 16:27:48'),
(43, 33, 'preparing', '', '2026-02-10 16:27:54'),
(44, 32, 'ready', '', '2026-02-10 16:28:07'),
(45, 35, 'pending', '', '2026-02-10 16:31:17'),
(46, 35, 'cancelled', 'noreson', '2026-02-10 16:31:33'),
(47, 35, 'completed', '', '2026-02-10 16:58:01'),
(48, 35, 'cancelled', '', '2026-02-10 16:58:46');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `permission_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `permission_name`) VALUES
(3, 'create_feedback'),
(5, 'manage_customers'),
(4, 'manage_feedback'),
(6, 'manage_staff'),
(1, 'view_dashboard'),
(7, 'view_order_status/history');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
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

DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 6),
(2, 1),
(2, 3),
(2, 4),
(2, 5),
(3, 3),
(3, 7);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(64) DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT 'default-profile.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role_id`, `email_verified`, `verification_token`, `token_expires`, `profile_picture`) VALUES
(10, 'admin', 'admin@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 1, 1, NULL, NULL, NULL),
(11, 'guest', 'guest@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 4, 1, NULL, NULL, NULL),
(12, 'customer', 'customer@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 3, 1, NULL, NULL, NULL),
(13, 'staff', 'staff@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 2, 1, NULL, NULL, NULL),
(14, 'anon', 'anon@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 3, 1, NULL, NULL, 'assets/uploads/profile_pics/profile_14_1770732219.jpg'),
(15, 'karen', 'karen@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 3, 1, NULL, NULL, 'assets/uploads/profile_pics/profile_15_1770737387.jpeg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`session_id`,`menu_item_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_feedbacks_user_id` (`user_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_available` (`is_available`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `order_status_logs`
--
ALTER TABLE `order_status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

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
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `order_status_logs`
--
ALTER TABLE `order_status_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `feedbacks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_feedbacks_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_status_logs`
--
ALTER TABLE `order_status_logs`
  ADD CONSTRAINT `order_status_logs_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

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

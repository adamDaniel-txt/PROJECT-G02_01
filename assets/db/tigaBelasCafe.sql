-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 03, 2026 at 05:24 PM
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
-- Database: `tigabelascafe`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`cart_id`, `user_id`, `menu_item_id`, `quantity`, `added_at`) VALUES
(5, 12, 14, 1, '2026-01-28 14:58:24'),
(11, 12, 11, 1, '2026-01-28 15:24:01');

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
(4, 'Anonymous', NULL, 'It is okay, overrated', 1, '2026-01-14 23:49:20'),
(5, 'Anonymous', NULL, 'Food is very good, will revisit', 3, '2026-01-17 14:59:00');

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
(3, 'Latte', 'A smooth and comforting blend of espresso with plenty of creamy steamed milk', 11.00, 'Coffee', 'https://www.brighteyedbaker.com/wp-content/uploads/2024/07/Dulce-de-Leche-Latte-Recipe.jpg', 1, '2026-01-14 17:17:36', '2026-01-22 09:14:21'),
(5, 'Green Tea', 'Refreshing traditional green tea', 7.00, 'Tea', 'https://tuyabeauty.com/cdn/shop/articles/Benefits_of_Green_Tea_For_Skin.jpg', 1, '2026-01-14 17:17:36', '2026-01-22 09:19:31'),
(10, 'Americano', 'A clean and robust classic made with deep espresso shots and hot water', 7.00, 'Coffee', 'https://mocktail.net/wp-content/uploads/2022/05/homemade-Iced-Americano-recipe_5.jpg', 1, '2026-01-22 09:21:17', '2026-01-22 09:21:17'),
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

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `orders_id` int(11) NOT NULL,
  `menu_items_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price_at_purchase` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `profile_pic` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(64) DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `profile_pic`, `password`, `role_id`, `email_verified`, `verification_token`, `token_expires`, `reset_token`) VALUES
(10, 'admin', 'admin@example.com', 'profile_10_1769099142.jpg', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 1, 1, NULL, NULL, NULL),
(11, 'guest', 'guest@example.com', NULL, '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 4, 1, NULL, NULL, NULL),
(12, 'customer', 'customer@example.com', 'profile_12_1769608791.jpg', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 3, 1, NULL, NULL, NULL),
(13, 'staff', 'staff@example.com', NULL, '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 2, 1, NULL, NULL, NULL),
(51, 'zihui', 'zihui12547@gmail.com', NULL, '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 3, 1, NULL, NULL, '98750992547d1e833fdbbec4bbee20f13ce8d03ff5c4dc37a283cb4409b2033e'),
(52, '2d53bc6fd8c4f8', 'gihago7677@dnsclick.com', NULL, '089542505d659cecbb988bb5ccff5bccf85be2dfa8c221359079aee2531298bb', 3, 1, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `unique_user_item` (`user_id`,`menu_item_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

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
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`users_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`orders_id`),
  ADD KEY `menu_item_id` (`menu_items_id`);

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
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`orders_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_items_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

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

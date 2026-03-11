-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 11, 2026 at 06:16 PM
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
(7, 'Great Atmosphere, would come again', 5, '2026-02-10 23:36:50', 14),
(8, 'This is soo PEAK', 5, '2026-02-11 08:54:14', 60);

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
(10, 'Americano', 'A clean and robust classic made with deep espresso shots and hot water', 10.00, 'Coffee', 'https://mocktail.net/wp-content/uploads/2022/05/homemade-Iced-Americano-recipe_5.jpg', 1, '2026-01-22 09:21:17', '2026-02-23 05:25:01'),
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
(23, 'Earl Grey Tea', 'A classic black tea infused with the distinct citrus aroma of bergamot', 7.00, 'Tea', 'https://weeteacompany.com/wp-content/uploads/2024/11/Vanilla-Earl-Grey-Tea.webp', 1, '2026-01-22 09:53:26', '2026-01-22 09:53:26'),
(26, 'Vietnamese Spanish Latte', 'hybrid coffee drink blending robust, dark-roasted Vietnamese coffee with the creamy sweetness of a Spanish latte', 14.00, 'Coffee', 'https://www.brighteyedbaker.com/wp-content/uploads/2024/03/Iced-Spanish-Latte-Recipe.jpg', 1, '2026-03-10 21:33:45', '2026-03-10 21:33:45'),
(27, 'Decaf', 'A type of coffee with at least 97% of its caffeine removed. Suitable for light coffee lovers!', 12.00, 'Coffee', 'https://cdn.shopify.com/s/files/1/0530/5145/7703/files/Everything_you_need_to_know_about_decaf_coffee.jpg?v=1638514642', 1, '2026-03-10 21:49:31', '2026-03-10 21:49:31'),
(28, 'Coconut milk latte', 'A creamy, dairy-free, and lightly sweet drink made by combining espresso or strong brewed coffee with steamed or iced coconut milk', 14.00, 'Coffee', 'https://thecoconutmama.com/wp-content/uploads/2021/06/Coconut-Milk-Coffee-Latte-Blog.png', 1, '2026-03-10 21:52:58', '2026-03-10 21:52:58'),
(29, 'Oat milk latte', 'A creamy, dairy-free coffee drink made by mixing 1-2 shots of espresso with steamed oat milk', 12.00, 'Coffee', 'https://i0.wp.com/www.yesmooretea.com/wp-content/uploads/2022/03/Honey-Oatmilk-Latte-6.jpg?resize=1100%2C1243&ssl=1', 1, '2026-03-10 21:55:48', '2026-03-10 21:55:48'),
(30, 'Cookie & Cream milkshakes', 'A luscious, creamy treat blending vanilla cream, milk, ice, and crumbled chocolate sandwich cookies into a thick shake', 16.00, 'Non-Coffee', 'https://thehungrykitchenblog.com/wp-content/uploads/2022/08/Boozy-Cookies-and-Cream-Milkshake-1-2.jpg', 1, '2026-03-10 22:00:24', '2026-03-10 22:00:24'),
(31, 'Avocado milkshake', 'blending a ripe avocado with milk', 16.00, 'Non-Coffee', 'https://i0.wp.com/live.staticflickr.com/65535/48312184476_80434e85f6_h.jpg?resize=640%2C800&ssl=1', 1, '2026-03-10 22:03:59', '2026-03-10 22:03:59'),
(32, 'Matcha frappe', 'A blended, icy green tea drink made by mixing matcha powder, milk, sweetener, and ice until smooth, topped with whipped cream', 14.00, 'Non-Coffee', 'https://cornercoffeestore.com/wp-content/uploads/2021/08/matcha-frappe_Atsushi-Hirao_Shutterstock-500x500.jpg', 1, '2026-03-10 22:06:29', '2026-03-10 22:06:29'),
(33, 'Matcha Strawberry Latte', 'A trending, layered iced beverage combining sweet, fresh strawberry puree with earthy, high-quality whisked matcha and milk', 14.00, 'Non-Coffee', 'https://i.imgur.com/jGHkVNL.jpeg', 1, '2026-03-10 22:08:00', '2026-03-10 22:08:00'),
(34, 'Hojicha latte', 'A creamy, comforting drink made from Japanese roasted green tea powder (hojicha), hot water, and steamed milk, offering a nutty, earthy, and slightly smoky caramel flavour', 14.00, 'Non-Coffee', 'https://www.justonecookbook.com/wp-content/uploads/2023/09/Iced-Hojicha-Latte-3647-II.jpg', 1, '2026-03-10 22:09:18', '2026-03-10 22:11:57'),
(35, 'Genmaicha latte', 'A creamy, comforting, and nutty beverage made by mixing brewed or powdered roasted brown rice green tea (Genmaicha) with frothed milk', 14.00, 'Non-Coffee', 'https://snapcalorie-webflow-website.s3.us-east-2.amazonaws.com/media/food_pics_v2/medium/zus_coffee_japanese_genmaicha_latte.jpg', 1, '2026-03-10 22:11:08', '2026-03-10 22:11:08'),
(36, 'Strawberry banana yoghurt milkshake', 'Blending of frozen bananas and strawberries with Greek yoghurt and vanilla ice cream until smooth and creamy', 16.00, 'Non-Coffee', 'https://gimmedelicious.com/wp-content/uploads/2024/08/Strawberry-Banana-Smoothie-SQ.jpg', 1, '2026-03-10 22:15:58', '2026-03-10 22:15:58'),
(37, 'Lemonade', 'A refreshing beverage made by mixing fresh lemon juice, syrup, and water', 10.00, 'Refreshing', 'https://images.squarespace-cdn.com/content/v1/5ed13dd3465af021e2c1342b/a5b1e544-ee89-4268-b9af-ab49e9cc7006/IMG_1986+%281%29.jpg', 1, '2026-03-10 22:18:44', '2026-03-10 22:19:22'),
(38, 'Fresh cherry limenade', 'Tart cherries blended with lime and soda', 12.00, 'Refreshing', 'https://images.squarespace-cdn.com/content/v1/525d98f0e4b0f07bb3deb091/1624895795115-QNQNRUMTN4KP4XSJYVF6/Cherry+Limeade+Gin+and+Tonic', 1, '2026-03-10 22:21:09', '2026-03-10 22:21:09'),
(39, 'Coconut milkshake', 'A creamy and indulgent coconut milkshake with vanilla ice cream, coconut cream, and coconut milk', 12.00, 'Refreshing', 'https://www.simplystacie.net/wp-content/uploads/2016/07/CoconutCoffeeMilkshakeLoRes-21.jpg', 1, '2026-03-10 22:26:56', '2026-03-10 22:26:56'),
(40, 'Virgin Colada', 'Pineapple juice, coconut cream, and ice', 14.00, 'Refreshing', 'https://platedcravings.com/wp-content/uploads/2022/06/Virgin-Pina-Colada-Plated-Cravings-9.jpg', 1, '2026-03-10 22:32:06', '2026-03-10 22:32:06'),
(41, 'Shirley Temple', 'Ginger ale, grenadine, and a maraschino cherry', 14.00, 'Refreshing', 'https://www.sainsburysmagazine.co.uk/media/12445/download/shirley-temple.jpg?v=1', 1, '2026-03-10 22:35:07', '2026-03-10 22:35:07'),
(42, 'Elderflower Mocktails', 'Combining elderflower cordial with fresh lemon juice and topping with sparkling water over ice, garnished with fresh mint', 12.00, 'Refreshing', 'https://images.squarespace-cdn.com/content/v1/5ff99138a24aef1e5b907732/1624875935102-XDFVHA1C5AZFHGAK5OE2/James-%26-Kerrie-Photography-107.jpg', 1, '2026-03-10 22:37:41', '2026-03-10 22:37:41'),
(43, 'Oolong tea', 'A partially oxidized tea from China', 10.00, 'Tea', 'https://teashop.com/cdn/shop/files/oolong-salted-caramel.jpg?v=1752218216&width=1000', 1, '2026-03-10 22:40:53', '2026-03-10 22:40:53'),
(44, 'Rose tea', 'caffeine-free herbal beverage made from rose petals and buds', 10.00, 'Tea', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQo9em1CkNBrNJW3iQ6aoeHobA0F15exBvZp54lTHERfc9RlA7Ucrmz4SPfprCbBkbAAhHLrgIWmXQ7CmlcxuHMeZIMrQlRhjNXiw7J1LdX&s=10', 1, '2026-03-10 22:42:12', '2026-03-10 22:42:12'),
(45, 'Chamomile tea', 'A popular herbal tea known for its mild, floral flavour and naturally occurring antioxidants', 10.00, 'Tea', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSLQu1p0B_zkrbL7Ub7Ra7Ac7rnFIiVIhVllvvz5N-jsHLd1sso3ljPlq7BV4LlcPdkXueNPRKAyn8NO7i3L7vgne3B22SiP4u3EEHUMPzw&s=10', 1, '2026-03-10 22:43:25', '2026-03-10 22:43:25'),
(46, 'Chrysanthemum tea', 'a fragrant, caffeine-free herbal drink using dried chrysanthemum flower', 10.00, 'Tea', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRo7M6wFcW6gEURSMeSDssgLOYOCMh4tRO1M41J7w8sEHxi0WlNdxuvprLcHF_wa4fYVrVtx9PxG5Gg8KmX6z2dPVUw9Ciao_hvVgAg3r2q&s=10', 1, '2026-03-10 22:46:04', '2026-03-10 22:46:04');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

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
(36, 15, 170.00, 'cs_test_b1cdrh0h6RbHDNkzKQsr4TXofbC2TOH352SNCz1RTXdFBn2pk64uC3Z7Mi', 'pending', '2026-02-10 16:07:09', '2026-02-10 16:07:09', NULL, NULL, NULL),
(37, 60, 13.00, 'cs_test_a1rmXWlqXBnEZnT67qHgyExG79EAKPJsFXZMlbZu0bYzKPGKol7NNtvvwp', 'completed', '2026-02-11 00:55:17', '2026-02-11 00:56:30', NULL, NULL, NULL),
(38, 60, 38.00, 'cs_test_b1LJzMGCE3l7jEFrXEiNSuQEmIxWCk6Oou6wN3ZQeRfHIbcdRObjPwBz5G', 'confirmed', '2026-02-11 01:40:25', '2026-02-11 01:48:36', NULL, NULL, NULL),
(40, 60, 25.00, 'cs_test_b18fsvClP4c2kyN8ipGxxUq1SXvXIlHKNeNxkEwdj5cQG8RK2seMZagMkE', 'completed', '2026-02-11 02:05:10', '2026-02-11 02:08:05', NULL, NULL, NULL),
(41, 60, 14.00, 'cs_test_a17CbEJ6za02Jy6EpnJYn5F178NVvucWvuFLuOZLY7KZ8JGDrR2LRIUyvF', 'cancelled', '2026-02-11 02:09:18', '2026-02-11 02:09:39', NULL, NULL, NULL),
(42, 14, 70.00, 'cs_test_b17zv2WfLGD35MJyTjUexPtmQBmyGX2UlwQc00TLPMzfGTCZrVRplHahky', 'ready', '2026-02-22 15:24:39', '2026-03-11 16:40:18', NULL, NULL, 'C34753'),
(43, 14, 53.00, 'cs_test_b15M2H4ggJjzTd3eVXXScN7cvRATtPOTuGJ48LyM6jJxWxAl2Ao9wTBPKg', 'completed', '2026-02-22 15:26:18', '2026-03-11 16:40:40', NULL, NULL, NULL),
(44, 15, 173.00, 'cs_test_b1UGP6Shc2CFonoLHbZ6HHJqdrUL55f3OQNa8Ny8cpCM8LAvu4lkNrsHCQ', 'preparing', '2026-02-22 15:27:58', '2026-03-11 16:40:53', '2026-03-11 17:55:53', NULL, NULL),
(45, 14, 83.00, 'cs_test_b13nI9pS7DHRYeY6dkh63hyWqwt5YSLFYzMq0UW8NeiKKu39RYpvTjkFhj', 'preparing', '2026-02-25 08:46:41', '2026-03-11 16:41:04', '2026-03-11 17:56:04', NULL, NULL),
(46, 14, 72.00, 'cs_test_b1VcuZAKF8grDk98WvfsOlL8QsYhPvRGpH10WQZibv2dN8lz7tF7XA9reR', 'cancelled', '2026-02-27 12:55:13', '2026-03-11 16:41:11', NULL, NULL, NULL),
(47, 15, 75.00, 'cs_test_b1kHqiy441PuIEdAxncQReFAw9DxCoKSI8EZ2PnbGTowIPkQniAlYkqJrT', 'confirmed', '2026-02-27 12:56:14', '2026-03-11 16:41:30', NULL, NULL, NULL),
(48, 14, 35.00, 'cs_test_b1cg3EHfWtY2tmrvqCmyzzl1ZyG5v4XxTt1PAjiFXJj9jMOvZHJU4uTYSf', 'pending', '2026-03-05 06:07:25', '2026-03-05 06:07:25', NULL, NULL, NULL),
(50, 15, 73.00, 'cs_test_b1oxM4Y00dyhToHOAX802ID355FDmFM8avVt8TmO1fkWjA8l8fWpI6bK8A', 'completed', '2026-03-08 17:17:36', '2026-03-11 16:41:50', NULL, NULL, NULL),
(51, 60, 110.00, 'cs_test_b1lhV1fenPE3iazAHdmUsgspGLDXpVl6YoPOfKnqOqSeQPFmtuk1pW6Eyj', 'pending', '2026-03-11 13:25:52', '2026-03-11 13:25:52', NULL, NULL, NULL),
(52, 14, 54.00, 'cs_test_b1AvsbHHXLPlm9xSYocEDIjuLZFQhtRD0mxGRsH1sHkjizjOU1u5ozNLln', 'pending', '2026-03-11 14:06:20', '2026-03-11 14:06:20', NULL, NULL, NULL),
(53, 15, 149.00, 'cs_test_b1XUSM4JKBBkTPKTor2HfVeBEN7Vs6VtRygtxHK1zqt7T7Dsdiai6VebbX', 'preparing', '2026-03-11 16:32:55', '2026-03-11 16:42:21', '2026-03-11 17:57:21', NULL, NULL),
(54, 15, 72.00, 'cs_test_b14uvo3WhS4pKv2XLuKIGWgAtgDlRTwHP5fHCVHnOgmsRyqjIMARMrZJMI', 'pending', '2026-03-11 16:33:47', '2026-03-11 16:33:47', NULL, NULL, NULL),
(55, 12, 377.00, 'cs_test_b1gPm67Ev4yRwXyJ21RDNpHhqbYe6BI34F2rdWdhNrMyS9wNO2odJwV8y9', 'ready', '2026-03-11 16:39:05', '2026-03-11 16:42:33', NULL, NULL, '8654FD');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

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
(51, 36, 10, 1, 7.00),
(52, 37, 13, 1, 13.00),
(53, 38, 12, 1, 13.00),
(54, 38, 17, 1, 13.00),
(55, 38, 19, 1, 12.00),
(56, 40, 13, 1, 13.00),
(57, 40, 22, 1, 12.00),
(58, 41, 14, 1, 14.00),
(59, 42, 16, 1, 13.00),
(60, 42, 3, 1, 11.00),
(61, 42, 14, 1, 14.00),
(62, 42, 10, 1, 10.00),
(63, 42, 11, 2, 11.00),
(64, 43, 23, 1, 7.00),
(65, 43, 22, 1, 12.00),
(66, 43, 3, 1, 11.00),
(67, 43, 19, 1, 12.00),
(68, 43, 11, 1, 11.00),
(69, 44, 5, 1, 7.00),
(70, 44, 23, 1, 7.00),
(71, 44, 20, 1, 12.00),
(72, 44, 21, 1, 12.00),
(73, 44, 22, 1, 12.00),
(74, 44, 18, 1, 13.00),
(75, 44, 17, 1, 13.00),
(76, 44, 19, 1, 12.00),
(77, 44, 12, 1, 13.00),
(78, 44, 16, 1, 13.00),
(79, 44, 3, 1, 11.00),
(80, 44, 13, 1, 13.00),
(81, 44, 14, 1, 14.00),
(82, 44, 11, 1, 11.00),
(83, 44, 10, 1, 10.00),
(84, 45, 20, 1, 12.00),
(85, 45, 18, 3, 13.00),
(86, 45, 3, 1, 11.00),
(87, 45, 11, 1, 11.00),
(88, 45, 10, 1, 10.00),
(89, 46, 5, 1, 7.00),
(90, 46, 23, 1, 7.00),
(91, 46, 20, 1, 12.00),
(92, 46, 17, 1, 13.00),
(93, 46, 19, 1, 12.00),
(94, 46, 10, 1, 10.00),
(95, 46, 11, 1, 11.00),
(96, 47, 5, 2, 7.00),
(97, 47, 20, 1, 12.00),
(98, 47, 18, 1, 13.00),
(99, 47, 12, 1, 13.00),
(100, 47, 13, 1, 13.00),
(101, 47, 10, 1, 10.00),
(102, 48, 14, 1, 14.00),
(103, 48, 11, 1, 11.00),
(104, 48, 10, 1, 10.00),
(105, 50, 21, 1, 12.00),
(106, 50, 22, 1, 12.00),
(107, 50, 13, 3, 13.00),
(108, 50, 10, 1, 10.00),
(109, 51, 41, 1, 14.00),
(110, 51, 40, 1, 14.00),
(111, 51, 20, 1, 12.00),
(112, 51, 22, 1, 12.00),
(113, 51, 21, 1, 12.00),
(114, 51, 37, 1, 10.00),
(115, 51, 38, 1, 12.00),
(116, 51, 42, 1, 12.00),
(117, 51, 39, 1, 12.00),
(118, 52, 44, 1, 10.00),
(119, 52, 43, 1, 10.00),
(120, 52, 5, 1, 7.00),
(121, 52, 23, 1, 7.00),
(122, 52, 45, 1, 10.00),
(123, 52, 46, 1, 10.00),
(124, 53, 26, 1, 14.00),
(125, 53, 12, 1, 13.00),
(126, 53, 19, 1, 12.00),
(127, 53, 29, 1, 12.00),
(128, 53, 3, 1, 11.00),
(129, 53, 16, 1, 13.00),
(130, 53, 13, 1, 13.00),
(131, 53, 27, 1, 12.00),
(132, 53, 14, 1, 14.00),
(133, 53, 11, 1, 11.00),
(134, 53, 28, 1, 14.00),
(135, 53, 10, 1, 10.00),
(136, 54, 30, 1, 16.00),
(137, 54, 31, 1, 16.00),
(138, 54, 17, 1, 13.00),
(139, 54, 46, 1, 10.00),
(140, 54, 23, 1, 7.00),
(141, 54, 45, 1, 10.00),
(142, 55, 40, 1, 14.00),
(143, 55, 41, 1, 14.00),
(144, 55, 20, 1, 12.00),
(145, 55, 22, 1, 12.00),
(146, 55, 21, 1, 12.00),
(147, 55, 37, 1, 10.00),
(148, 55, 42, 1, 12.00),
(149, 55, 38, 1, 12.00),
(150, 55, 39, 1, 12.00),
(151, 55, 36, 1, 16.00),
(152, 55, 32, 1, 14.00),
(153, 55, 33, 1, 14.00),
(154, 55, 18, 1, 13.00),
(155, 55, 34, 1, 14.00),
(156, 55, 35, 1, 14.00),
(157, 55, 30, 1, 16.00),
(158, 55, 31, 1, 16.00),
(159, 55, 17, 1, 13.00),
(160, 55, 26, 1, 14.00),
(161, 55, 12, 1, 13.00),
(162, 55, 29, 1, 12.00),
(163, 55, 16, 1, 13.00),
(164, 55, 3, 1, 11.00),
(165, 55, 27, 1, 12.00),
(166, 55, 13, 1, 13.00),
(167, 55, 14, 1, 14.00),
(168, 55, 11, 1, 11.00),
(169, 55, 28, 1, 14.00),
(170, 55, 10, 1, 10.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_status_logs`
--

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
(48, 35, 'cancelled', '', '2026-02-10 16:58:46'),
(49, 37, 'pending', 'Order created and payment confirmed', '2026-02-11 00:55:17'),
(50, 37, 'completed', '', '2026-02-11 00:56:30'),
(51, 38, 'pending', 'Order created and payment confirmed', '2026-02-11 01:40:25'),
(53, 38, 'confirmed', '', '2026-02-11 01:48:36'),
(54, 40, 'pending', 'Order created and payment confirmed', '2026-02-11 02:05:10'),
(55, 40, 'confirmed', '', '2026-02-11 02:07:36'),
(56, 40, 'completed', '', '2026-02-11 02:07:54'),
(57, 40, 'completed', 'Tq', '2026-02-11 02:08:05'),
(58, 41, 'pending', 'Order created and payment confirmed', '2026-02-11 02:09:18'),
(59, 41, 'cancelled', 'tanak', '2026-02-11 02:09:39'),
(60, 42, 'pending', 'Order created and payment confirmed', '2026-02-22 15:24:39'),
(61, 43, 'pending', 'Order created and payment confirmed', '2026-02-22 15:26:18'),
(62, 44, 'pending', 'Order created and payment confirmed', '2026-02-22 15:27:58'),
(63, 45, 'pending', 'Order created and payment confirmed', '2026-02-25 08:46:41'),
(64, 46, 'pending', 'Order created and payment confirmed', '2026-02-27 12:55:13'),
(65, 47, 'pending', 'Order created and payment confirmed', '2026-02-27 12:56:14'),
(66, 48, 'pending', 'Order created and payment confirmed', '2026-03-05 06:07:25'),
(68, 50, 'pending', 'Order created and payment confirmed', '2026-03-08 17:17:36'),
(69, 51, 'pending', 'Order created and payment confirmed', '2026-03-11 13:25:52'),
(70, 52, 'pending', 'Order created and payment confirmed', '2026-03-11 14:06:20'),
(71, 53, 'pending', 'Order created and payment confirmed', '2026-03-11 16:32:55'),
(72, 54, 'pending', 'Order created and payment confirmed', '2026-03-11 16:33:47'),
(73, 55, 'pending', 'Order created and payment confirmed', '2026-03-11 16:39:05'),
(74, 42, 'ready', '', '2026-03-11 16:40:18'),
(75, 43, 'completed', '', '2026-03-11 16:40:40'),
(76, 44, 'preparing', '', '2026-03-11 16:40:53'),
(77, 45, 'preparing', '', '2026-03-11 16:41:04'),
(78, 46, 'cancelled', '', '2026-03-11 16:41:11'),
(79, 47, 'confirmed', '', '2026-03-11 16:41:30'),
(80, 50, 'completed', '', '2026-03-11 16:41:50'),
(81, 53, 'confirmed', '', '2026-03-11 16:42:15'),
(82, 53, 'preparing', '', '2026-03-11 16:42:21'),
(83, 55, 'ready', '', '2026-03-11 16:42:33');

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
(5, 'manage_customers'),
(4, 'manage_feedback'),
(6, 'manage_staff'),
(1, 'view_dashboard'),
(7, 'view_order_status/history');

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

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(64) DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT 'default-profile.png',
  `is_active` tinyint(1) DEFAULT 1,
  `banned_at` datetime DEFAULT NULL,
  `ban_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role_id`, `email_verified`, `verification_token`, `token_expires`, `profile_picture`, `is_active`, `banned_at`, `ban_reason`) VALUES
(10, 'admin', 'admin@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 1, 1, NULL, NULL, 'assets/uploads/profile_pics/profile_10_1770777506.jpeg', 1, NULL, NULL),
(11, 'guest', 'guest@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 4, 1, NULL, NULL, NULL, 1, NULL, NULL),
(12, 'customer', 'customer@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 3, 1, NULL, NULL, 'assets/uploads/profile_pics/profile_12_1771774890.jpeg', 1, NULL, NULL),
(13, 'staff', 'staff@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 2, 1, NULL, NULL, 'assets/uploads/profile_pics/profile_13_1773071948.jpeg', 1, NULL, NULL),
(14, 'anon', 'anon@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 3, 1, NULL, NULL, 'assets/uploads/profile_pics/profile_14_1770732219.jpg', 1, NULL, NULL),
(15, 'karen', 'karen@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 3, 1, NULL, NULL, 'assets/uploads/profile_pics/profile_15_1770737387.jpeg', 1, NULL, NULL),
(60, 'dan', 'dan@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 3, 1, NULL, NULL, 'assets/uploads/profile_pics/profile_60_1770775982.jpeg', 1, NULL, NULL),
(74, 'azril', 'azril@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 2, 1, NULL, NULL, 'assets/uploads/profile_pics/profile_74_1773247631.jpg', 1, NULL, NULL),
(75, 'tham', 'tham@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 2, 1, NULL, NULL, 'assets/uploads/profile_pics/profile_75_1773247701.png', 1, NULL, NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;

--
-- AUTO_INCREMENT for table `order_status_logs`
--
ALTER TABLE `order_status_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

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

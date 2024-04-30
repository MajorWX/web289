-- phpMyAdmin SQL Dump
-- version 5.1.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 30, 2024 at 04:54 AM
-- Server version: 8.0.34-26
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbu7plrbumrykh`
--

-- --------------------------------------------------------

--
-- Table structure for table `calendar`
--

CREATE TABLE `calendar` (
  `calendar_id` int NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `calendar`
--

INSERT INTO `calendar` (`calendar_id`, `date`) VALUES
(1, '2024-02-23'),
(2, '2024-02-28'),
(3, '2024-03-04'),
(4, '2024-03-22'),
(5, '2024-03-11'),
(6, '2024-04-05'),
(20, '2024-04-20'),
(23, '2024-05-04'),
(26, '2024-05-11'),
(29, '2024-04-26');

-- --------------------------------------------------------

--
-- Table structure for table `calendar_listing`
--

CREATE TABLE `calendar_listing` (
  `listing_id` int NOT NULL,
  `li_calendar_id` int NOT NULL,
  `li_vendor_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `calendar_listing`
--

INSERT INTO `calendar_listing` (`listing_id`, `li_calendar_id`, `li_vendor_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(54, 6, 2),
(62, 20, 1),
(74, 23, 2),
(75, 23, 1),
(76, 26, 1),
(77, 29, 1),
(80, 23, 9),
(81, 23, 10);

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `image_id` int NOT NULL,
  `im_user_id` int NOT NULL,
  `content` varchar(255) NOT NULL,
  `upload_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `alt_text` text,
  `im_vendor_id` int DEFAULT NULL,
  `im_product_id` int DEFAULT NULL,
  `image_purpose` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`image_id`, `im_user_id`, `content`, `upload_date`, `alt_text`, `im_vendor_id`, `im_product_id`, `image_purpose`) VALUES
(24, 1, '24-04-30-03-42-35-$Eggs_Chicken.jpg', '2024-04-30 03:42:35', '', 1, 4, 'inventory'),
(25, 1, '24-04-30-03-52-43-$Spinach_Plant_Nourishment_Meal_Fresh_Healthy_Bio.jpg', '2024-04-30 03:52:43', '', 1, 1, 'inventory'),
(26, 1, '24-04-30-03-57-25-$640px-5495Romaine_lettuce_in_the_Philippines_01.jpg', '2024-04-30 03:57:25', '', 1, 3, 'inventory'),
(27, 1, '24-04-30-03-59-09-$Carrots_in_Freiburg_-_DSC06503.jpg', '2024-04-30 03:59:09', '', 1, 13, 'inventory'),
(28, 1, '24-04-30-04-01-54-$1024px-Hill_Farm_Single_Plot.jpg', '2024-04-30 04:01:54', '', 1, 0, 'profile'),
(29, 1, '24-04-30-04-05-52-$The_USDA_Farmers_Market_on_the_National_Mall_during_the_2023_market_season_is_the_Department’s_own_“living_laboratory”_for_farmers_market_operations_across_the_country_-_.jpg', '2024-04-30 04:05:52', '', 0, 0, 'home_page'),
(30, 1, '24-04-30-04-07-28-$Peaches_at_a_Farmers_Market_-_49806604067.jpg', '2024-04-30 04:07:28', '', 0, 0, 'home_page');

-- --------------------------------------------------------

--
-- Table structure for table `phone_numbers`
--

CREATE TABLE `phone_numbers` (
  `phone_id` int NOT NULL,
  `ph_vendor_id` int NOT NULL,
  `phone_number` char(10) NOT NULL,
  `phone_type` enum('home','mobile','work') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `phone_numbers`
--

INSERT INTO `phone_numbers` (`phone_id`, `ph_vendor_id`, `phone_number`, `phone_type`) VALUES
(1, 1, '8282797420', 'mobile'),
(3, 1, '1234567890', 'home');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int NOT NULL,
  `prd_category_id` int NOT NULL,
  `product_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `prd_category_id`, `product_name`) VALUES
(1, 1, 'Spinach'),
(2, 1, 'Broccoli'),
(3, 1, 'Lettuce'),
(4, 3, 'Eggs'),
(13, 1, 'Carrots');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `category_id` int NOT NULL,
  `category_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`category_id`, `category_name`) VALUES
(1, 'Vegetables'),
(2, 'Artisan Foods'),
(3, 'Dairy &amp; Eggs'),
(4, 'Flowers'),
(5, 'Fruits &amp; Berries'),
(6, 'Grains'),
(7, 'Herbs'),
(8, 'Meat, Fish &amp; Fowl'),
(9, 'Nuts &amp; Seeds'),
(10, 'Specialty Products'),
(11, 'Sprouts');

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `state_id` int NOT NULL,
  `state_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`state_id`, `state_name`) VALUES
(1, 'North Carolina');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `display_name` varchar(30) NOT NULL,
  `hashed_password` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `role` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `display_name`, `hashed_password`, `email`, `role`) VALUES
(1, 'admin', '$2y$10$FiDPPxQDjnunBcyrVxh7d.1JRO6bO/ZwQsyRyHQly/kvYyhPbtRJO', 'majorwx2@gmail.com', 's'),
(2, 'TestUser', '$2y$10$FiDPPxQDjnunBcyrVxh7d.1JRO6bO/ZwQsyRyHQly/kvYyhPbtRJO', 'majorwx2@Gmail.com', 'm'),
(3, 'AsLongAsICanReasonablyMakeIt', '$2y$10$RUEZfaZ0JHkKybYUOsNlsuafsklHyPFrfS1dXtIunPXKZn.sUlNZO', 'majorwx2@Gmail.com', 'm'),
(7, 'Userman', '$2y$10$eMM/kKXL6cEhN48zxNlDRePsoezBPEiXFXAV6PDHbQaeB7PTsxePS', 'majorwx2@Gmail.com', 'm'),
(8, 'VendorUser', '$2y$10$t/aeKHpme9Da/5aDmQP4K.0seV.fkwwimLdxOxO3.dcTJcRjXEg2.', 'majorwx2@Gmail.com', 'm'),
(9, 'AdminUser', '$2y$10$Lfoy8sIUfwUSd9seWDLzJu.VW5ClpT9724F76fVTYb89Sx0aTQxNa', 'majorwx2@Gmail.com', 's');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `vendor_id` int NOT NULL,
  `vd_user_id` int DEFAULT NULL,
  `vendor_display_name` varchar(50) NOT NULL,
  `vendor_desc` text NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `vd_state_id` int NOT NULL,
  `zip` int NOT NULL,
  `is_pending` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`vendor_id`, `vd_user_id`, `vendor_display_name`, `vendor_desc`, `address`, `city`, `vd_state_id`, `zip`, `is_pending`) VALUES
(1, 1, 'Reynolds Hill Farms', 'A farm like any other.', '18 Spring Hollow Ln.', 'Fairview', 1, 28730, 0),
(2, 3, 'Very Long Vendor Name Test', 'Testing Longways', '18 Spring Hollow Ln.', 'Fairview', 1, 28730, 0),
(9, 7, 'Vendorman', '', '18 Spring Hollow Ln.', 'Fairview', 1, 28730, 0),
(10, 2, 'Usability Tester', '', '18 Spring Hollow Ln.', 'Fairview', 1, 28730, 0),
(11, 8, 'Test Farm', '', '18 Spring Hollow Ln.', 'Fairview', 1, 28730, 0),
(12, 9, 'Admin Farm', '', '18 Spring Hollow Ln.', 'Fairview', 1, 28730, 0);

-- --------------------------------------------------------

--
-- Table structure for table `vendor_inventory`
--

CREATE TABLE `vendor_inventory` (
  `inventory_id` int NOT NULL,
  `inv_vendor_id` int NOT NULL,
  `inv_product_id` int NOT NULL,
  `listing_price` decimal(10,2) DEFAULT NULL,
  `in_stock` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vendor_inventory`
--

INSERT INTO `vendor_inventory` (`inventory_id`, `inv_vendor_id`, `inv_product_id`, `listing_price`, `in_stock`) VALUES
(1, 1, 1, '3.15', 1),
(3, 1, 3, '2.00', 1),
(4, 1, 4, '0.70', 1),
(10, 1, 13, '2.00', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `calendar`
--
ALTER TABLE `calendar`
  ADD PRIMARY KEY (`calendar_id`);

--
-- Indexes for table `calendar_listing`
--
ALTER TABLE `calendar_listing`
  ADD PRIMARY KEY (`listing_id`),
  ADD KEY `calendar listing fk vendor` (`li_vendor_id`),
  ADD KEY `calendar listing fk calendar` (`li_calendar_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `images fk user` (`im_user_id`),
  ADD KEY `images fk vendor` (`im_vendor_id`);

--
-- Indexes for table `phone_numbers`
--
ALTER TABLE `phone_numbers`
  ADD PRIMARY KEY (`phone_id`),
  ADD KEY `phone fk vendor` (`ph_vendor_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `products fk category` (`prd_category_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`state_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `display_name` (`display_name`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`vendor_id`),
  ADD KEY `vendor fk user` (`vd_user_id`),
  ADD KEY `vendor fk state` (`vd_state_id`);

--
-- Indexes for table `vendor_inventory`
--
ALTER TABLE `vendor_inventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD KEY `inventory fk vendor` (`inv_vendor_id`),
  ADD KEY `inventory fk product` (`inv_product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `calendar`
--
ALTER TABLE `calendar`
  MODIFY `calendar_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `calendar_listing`
--
ALTER TABLE `calendar_listing`
  MODIFY `listing_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `image_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `phone_numbers`
--
ALTER TABLE `phone_numbers`
  MODIFY `phone_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `category_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `state_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `vendor_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `vendor_inventory`
--
ALTER TABLE `vendor_inventory`
  MODIFY `inventory_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `calendar_listing`
--
ALTER TABLE `calendar_listing`
  ADD CONSTRAINT `calendar listing fk calendar` FOREIGN KEY (`li_calendar_id`) REFERENCES `calendar` (`calendar_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `calendar listing fk vendor` FOREIGN KEY (`li_vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `images fk user` FOREIGN KEY (`im_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `phone_numbers`
--
ALTER TABLE `phone_numbers`
  ADD CONSTRAINT `phone fk vendor` FOREIGN KEY (`ph_vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products fk category` FOREIGN KEY (`prd_category_id`) REFERENCES `product_categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vendors`
--
ALTER TABLE `vendors`
  ADD CONSTRAINT `vendor fk state` FOREIGN KEY (`vd_state_id`) REFERENCES `states` (`state_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vendor fk user` FOREIGN KEY (`vd_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vendor_inventory`
--
ALTER TABLE `vendor_inventory`
  ADD CONSTRAINT `inventory fk product` FOREIGN KEY (`inv_product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `inventory fk vendor` FOREIGN KEY (`inv_vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

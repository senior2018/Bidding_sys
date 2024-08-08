-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 26, 2024 at 05:42 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bid_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `auctions`
--

CREATE TABLE `auctions` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `duration` int(11) NOT NULL,
  `status` enum('upcoming','ongoing','paused','ended','sold_out') DEFAULT 'upcoming',
  `end_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `bids`
--

CREATE TABLE `bids` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `bid_amount` decimal(10,2) NOT NULL,
  `bid_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `bids`
--

INSERT INTO `bids` (`id`, `product_id`, `buyer_id`, `bid_amount`, `bid_time`, `created_at`) VALUES
(12, 55, 8, '13452.00', '2024-06-24 13:38:58', '2024-06-24 13:38:58'),
(13, 55, 8, '15900.00', '2024-06-24 13:40:54', '2024-06-24 13:40:54'),
(14, 55, 9, '1600000.00', '2024-06-24 15:29:01', '2024-06-24 15:29:01'),
(15, 55, 10, '15999900.00', '2024-06-24 15:30:33', '2024-06-24 15:30:33');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` varchar(50) NOT NULL DEFAULT 'general'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `product_id`, `message`, `created_at`, `type`) VALUES
(1, 10, 55, 'Congratulations! You have won the auction with a bid of 15999900.00. Please proceed to payment. <a href=\'payment.php?product_id=55\'>Click here to pay</a>', '2024-06-25 05:53:36', 'general');

-- --------------------------------------------------------

--
-- Table structure for table `payment_confirmations`
--

CREATE TABLE `payment_confirmations` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `payment_type` varchar(50) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `receipt_number` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `starting_price` decimal(10,2) NOT NULL,
  `max_bid_amount` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected','sold_out') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `seller_id`, `name`, `description`, `starting_price`, `max_bid_amount`, `image`, `status`, `created_at`) VALUES
(55, 8, 'tv', 'qwerasxcwerwedfx', '12452.00', '15999900.00', '', '', '2024-06-23 18:12:21'),
(56, 8, 'qwe', 'jhgfdmnvcx', '76312.00', NULL, '', 'pending', '2024-06-23 18:13:38'),
(57, 8, 'smart', 'ertyu dfghj bn ', '1324.00', NULL, '', 'pending', '2024-06-23 18:14:32'),
(59, 8, 'LAPTOP', 'RAM 16GB', '500000.00', NULL, '', 'pending', '2024-06-26 14:18:12');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`) VALUES
(13, 55, 'uploads/c2.jpeg'),
(14, 56, 'uploads/b1.jpeg'),
(16, 56, 'uploads/b3.jpeg'),
(19, 57, 'uploads/b1.jpeg'),
(21, 57, 'uploads/b3.jpeg'),
(24, 59, 'uploads/a4.jpeg'),
(25, 55, '0'),
(26, 55, '0');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('buyer','seller','admin') DEFAULT 'buyer',
  `approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `mobile_number`, `national_id`, `email`, `username`, `password`, `role`, `approved`, `created_at`, `last_login`) VALUES
(7, 'samwel', 'paul', '21968103312', '123123618731', 'samwel@gmail.com', 'samwel', '$2y$10$D1mjbv87pF0w1vnZ/ytTAerZn5WNG9B2n4i2b67AdcKWRR.JbgClC', 'admin', 1, '2024-06-15 14:04:22', NULL),
(8, 'aura', 'aura', '28179183018', '33857`973`9843', 'aura@gmail.com', 'aura', '$2y$10$08YN/9HTMFOgWnMST0MHgui7XTxOEaItt6EEWzmbvmq/XcJkTZD.q', 'seller', 1, '2024-06-15 14:08:45', NULL),
(9, 'jully', 'jully', '262739731', '8729582749823', 'jully@gmail.com', 'jully', '$2y$10$3t/5/mCLln3xJkTnPTJLpup9u8S/BAoAK9MkJhz03h3shudh6/w1q', 'buyer', 1, '2024-06-15 14:09:26', NULL),
(10, 'tony', 'tony', '26712187298', '21658372973', 'tony@gmail.com', 'tony', '$2y$10$wqMQh0/KEgiCx5A4YmwsHe9uMskhQ1kaM4R5Oj7uH03ubMm8jDIZq', 'buyer', 1, '2024-06-15 14:10:00', NULL),
(14, 'marry', 'marry', '45895643', '765433123', 'marry@gmail.com', 'marry', '$2y$10$kHvDZdG1u9JXyVHCFwl8Q.48ap0DG0Rkt5ZqsCLeq6.xqz3vKG8/2', 'buyer', 1, '2024-06-24 11:52:57', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auctions`
--
ALTER TABLE `auctions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `bids`
--
ALTER TABLE `bids`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bids_ibfk_1` (`product_id`),
  ADD KEY `bids_ibfk_2` (`buyer_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payment_confirmations`
--
ALTER TABLE `payment_confirmations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `national_id` (`national_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auctions`
--
ALTER TABLE `auctions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `bids`
--
ALTER TABLE `bids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment_confirmations`
--
ALTER TABLE `payment_confirmations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auctions`
--
ALTER TABLE `auctions`
  ADD CONSTRAINT `auctions_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bids`
--
ALTER TABLE `bids`
  ADD CONSTRAINT `bids_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bids_ibfk_2` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_confirmations`
--
ALTER TABLE `payment_confirmations`
  ADD CONSTRAINT `payment_confirmations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

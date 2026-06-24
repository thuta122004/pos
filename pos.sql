-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 24, 2026 at 02:49 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `image_path` varchar(255) DEFAULT 'default_icepop.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `price`, `stock_quantity`, `is_active`, `image_path`) VALUES
(1, 'Raspberry', 1500.00, 14, 1, '1782193903_BlueBerry.png.webp'),
(2, 'Rainbow', 1800.00, 20, 1, 'default.png'),
(3, 'Pineapple', 1700.00, 2, 1, '1782197837_pngtree-bright-yellow-popsicle-ice-cream-on-a-wooden-stick-png-image_16048646.png'),
(4, 'Chocolate', 1800.00, 25, 1, '1782197944_pngtree-chocolate-popsicle-isolated-png-image_10188240.png'),
(5, 'Green Apple', 1400.00, 9, 1, '1782198253_pngtree-green-popsicle-ice-cream-isolated-on-white-background-png-image_15396245.png'),
(6, 'Mango', 1800.00, 10, 1, '1782198093_pngtree-mango-popsicle-png-image_16478802.png'),
(7, 'Bluberry', 1400.00, 17, 1, '1782198185_pngtree-3d-render-blue-ice-pop-png-image_11573288.png'),
(8, 'Strawberry', 1900.00, 11, 1, '1782204504_54535-4-ice-pop-picture-free-transparent-image-hq.png'),
(9, 'Jelly Pop', 1600.00, 18, 1, '1782205425_pngtree-delicious-ice-lolly-popsicles-for-summer-png-image_12913078.png');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `transaction_date`, `total_amount`, `customer_name`) VALUES
(1, '2026-06-22 09:15:47', 3000.00, 'Thu Ta'),
(2, '2026-06-23 09:16:49', 3300.00, 'Thu Ta'),
(3, '2026-06-23 11:54:01', 3600.00, ''),
(4, '2026-06-24 12:05:41', 3300.00, 'Thu Ta'),
(5, '2026-06-24 12:05:57', 3300.00, 'Thu Ta'),
(6, '2026-06-24 12:06:05', 5100.00, ''),
(7, '2026-06-24 12:06:14', 5100.00, 'Thu Ta'),
(8, '2026-06-24 12:19:00', 1800.00, ''),
(9, '2026-06-24 12:20:19', 1800.00, ''),
(10, '2026-06-24 12:20:59', 1800.00, ''),
(11, '2026-06-24 12:21:17', 1500.00, ''),
(12, '2026-06-24 12:25:58', 13600.00, ''),
(13, '2026-06-24 12:31:52', 1500.00, ''),
(14, '2026-06-24 12:38:52', 1500.00, ''),
(15, '2026-06-24 12:40:30', 1800.00, ''),
(16, '2026-06-24 12:46:02', 8700.00, 'Honey');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `line_item_id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_items`
--

INSERT INTO `transaction_items` (`line_item_id`, `transaction_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 1400.00),
(2, 1, 2, 1, 1600.00),
(3, 2, 1, 1, 1500.00),
(4, 2, 2, 1, 1800.00),
(5, 3, 2, 2, 1800.00),
(6, 4, 1, 1, 1500.00),
(7, 4, 4, 1, 1800.00),
(8, 5, 7, 1, 1400.00),
(9, 5, 8, 1, 1900.00),
(10, 6, 1, 1, 1500.00),
(11, 6, 4, 2, 1800.00),
(12, 7, 1, 1, 1500.00),
(13, 7, 4, 2, 1800.00),
(14, 8, 2, 1, 1800.00),
(15, 9, 2, 1, 1800.00),
(16, 10, 2, 1, 1800.00),
(17, 11, 1, 1, 1500.00),
(18, 12, 3, 8, 1700.00),
(19, 13, 1, 1, 1500.00),
(20, 14, 1, 1, 1500.00),
(21, 15, 4, 1, 1800.00),
(22, 16, 8, 3, 1900.00),
(23, 16, 9, 2, 1500.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`);

--
-- Indexes for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`line_item_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `line_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`),
  ADD CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

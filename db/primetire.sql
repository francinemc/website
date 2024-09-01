-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2024 at 11:41 AM
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
-- Database: `e-commerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL,
  `brand_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`brand_id`, `brand_name`) VALUES
(1, 'Bridgestone'),
(2, 'Goodyear'),
(3, 'Dunlop');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','completed','abandoned') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `created_at`, `category_name`) VALUES
(1, '2024-08-16 07:57:22', 'hehe'),
(2, '2024-08-18 09:12:36', 'imissuxD');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `feedback` text NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `full_name`, `email`, `feedback`, `rating`, `created_at`) VALUES
(8, 1, 'Francine Cadavid', 'eyy@gmail.com', 'Hello, test feedback', 1, '2024-08-19 10:33:57'),
(9, 1, 'Mark Andrew L. Manaig', 'marky@gmail.com', 'hello, i\'m marky!', 5, '2024-08-19 10:35:43'),
(10, 1, 'Francinesaurpretty', 'eyy@gmail.com', 'tama na to, ayoko na xD', 3, '2024-08-19 10:46:35'),
(11, 1, 'min9yu', 'min9yu17@gmail.com', 'aju nice!', 2, '2024-08-19 12:43:47'),
(12, 1, 'dai dacasin', 'blancodairen5@gmail.com', 'wazzup', 1, '2024-08-19 13:04:36'),
(13, 1, 'eca', 'eca@gmail.com', 'woooOoOoooOoW', 5, '2024-08-19 13:28:15'),
(14, 19, 'hjkhk', 'testuser@gmail.com', 'mjmjhg vn vnnhf', 3, '2024-08-19 15:00:26'),
(16, 19, 'Erica', 'eca@gmail.com', 'whahahahhhaahahah', 5, '2024-08-20 08:56:02');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','completed','shipped','canceled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `stock` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `size_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `brand_id`, `image_url`, `description`, `stock`, `category`, `size_id`) VALUES
(152, 'Bridgestone Gulong1', 78467.00, 1, '', 'hello, gulong', 7346, 1, NULL),
(153, 'Burat Si Erica', 5.00, 3, '', 'AYOKO NGA MUKHANG BURAT SI ERICA ', 635, 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_details`
--

CREATE TABLE `product_details` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_details`
--

INSERT INTO `product_details` (`id`, `name`, `description`, `price`, `image`, `size`) VALUES
(1, 'Bridgestone Turanza QuietTrack', '   Description: Premium touring all-season tire for sedans, coupes, and crossovers. It provides a quiet, comfortable ride with long-lasting performance and enhanced wet and dry traction.\r\n', 8.00, NULL, ' Sizes: 195/65R15');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`) VALUES
(1, 111, 'C:\\xampp\\htdocs\\ITEC75\\admin/uploads/side.avif'),
(2, 112, 'C:\\xampp\\htdocs\\ITEC75\\admin/uploads/side.avif'),
(3, 113, 'C:\\xampp\\htdocs\\ITEC75\\admin/uploads/side.avif'),
(4, 114, 'C:\\xampp\\htdocs\\ITEC75\\admin/uploads/side.avif'),
(5, 135, 'C:\\xampp\\htdocs\\E-commerce\\ITEC75\\admin/uploads/tire1.jpg'),
(6, 136, 'C:\\xampp\\htdocs\\E-commerce\\ITEC75\\admin/uploads/tire1.jpg'),
(7, 137, 'C:\\xampp\\htdocs\\E-commerce\\ITEC75\\admin/uploads/tire1.jpg'),
(8, 139, 'C:\\xampp\\htdocs\\E-commerce\\ITEC75\\admin/uploads/tire1.jpg'),
(9, 140, 'C:\\xampp\\htdocs\\E-commerce\\ITEC75\\admin/uploads/tire1.jpg'),
(10, 142, 'C:\\xampp\\htdocs\\E-commerce\\ITEC75\\admin/uploads/tire1.jpg'),
(11, 144, 'C:\\xampp\\htdocs\\E-commerce\\ITEC75\\admin/uploads/tire1.jpg'),
(12, 146, 'C:\\xampp\\htdocs\\E-commerce\\ITEC75\\admin/uploads/tire1.jpg'),
(13, 147, 'C:\\xampp\\htdocs\\E-commerce\\ITEC75\\admin/uploads/tire1.jpg'),
(14, 148, 'C:\\xampp\\htdocs\\E-commerce\\ITEC75\\admin/uploads/tire1.jpg'),
(15, 149, 'C:\\xampp\\htdocs\\E-commerce\\ITEC75\\admin/uploads/tire2.jpg'),
(16, 150, 'C:\\xampp\\htdocs\\E-commerce\\ITEC75\\admin/uploads/tire1.jpg'),
(17, 151, 'C:\\xampp\\htdocs\\E-commerce\\ITEC75\\admin/uploads/tire1.jpg'),
(18, 152, 'C:\\xampp\\htdocs\\E-commerce\\ITEC75\\admin/uploads/tire1.jpg'),
(19, 153, 'C:\\xampp\\htdocs\\E-commerce\\ITEC75\\admin/uploads/contacts.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `product_sizes`
--

CREATE TABLE `product_sizes` (
  `id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_sizes`
--

INSERT INTO `product_sizes` (`id`, `size_id`) VALUES
(152, 1),
(152, 2),
(152, 3),
(152, 4),
(153, 1),
(153, 2),
(153, 3),
(153, 4);

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

CREATE TABLE `sizes` (
  `size_id` int(11) NOT NULL,
  `size_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`size_id`, `size_value`) VALUES
(1, '205/55R16'),
(2, '225/45R17'),
(3, '245/40R18'),
(4, '275/35R19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `birth_date` date NOT NULL,
  `gender` enum('Male','Female','Prefer not to say') NOT NULL,
  `address1` varchar(255) NOT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `region` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `barangay` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` tinyint(4) NOT NULL DEFAULT 1 CHECK (`role` in (0,1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `birth_date`, `gender`, `address1`, `address2`, `region`, `city`, `barangay`, `postal_code`, `password`, `created_at`, `role`) VALUES
(1, 'erica ', 'test@gmail.com', '09053126779', '2004-06-10', 'Female', 'ivory st', 'blk 28 lot 8 ', 'America', 'cavite', 'calabarzon', '4108', '$2y$10$VCQOJmIU139PyD.AFP/O/eiP3LH3R8uFB7goMjyBQnL3zblM7iJjS', '2024-08-10 14:54:25', 1),
(2, 'francine', 'hehe@gmail.com', '09811994390', '2024-08-10', 'Female', 'ivory st', 'blk 28 lot 8', 'India', 'cavite', 'calabarzon', '4108', '$2y$10$6DTUofJxp6tEUX22I52ME.ggNV4weayzwndjxBpiznePEiTr14ss.', '2024-08-10 15:14:04', 1),
(4, 'francine', 'francine@gmail.com', '09811994390', '2024-08-05', 'Male', 'ivory st', 'blk 28 lot 8', 'CALABARZON (Region IV-A)', 'cavite', 'Silang', '4108', '$2y$10$XGEhn6eQpnye/AHNrhrz3.hfTrn0PciHHjhbqWDuqhW2o.ikHdy1q', '2024-08-14 13:06:56', 1),
(5, 'francine', 'test1@gmail.com', '09811994390', '2024-08-01', 'Male', 'ivory st', 'blk 28 lot 8', 'CALABARZON (Region IV-A)', 'cavite', 'errfdfs', '4108', '$2y$10$ryqbXTpsMkY/0j4CxB8Ememd3lMCN6zSOXsZQ.EBzQHXGdIL5hiim', '2024-08-14 13:31:14', 1),
(6, 'francine', 'francine1@gmail.com', '09811994390', '2024-08-06', 'Male', 'ivory st', 'blk 28 lot 8', 'CALABARZON (Region IV-A)', 'cavite', 'Silang', '4108', '$2y$10$sNptgQtSb79oy.UVMF.ayOaTpp5IVMAhiXZQGFDcTLoDluXmIDFuq', '2024-08-14 13:37:06', 1),
(19, 'user', 'user@gmail.com', '73678465', '2024-07-30', 'Male', '123', '356', 'Northern Mindanao (Region X)', 'ormoc', 'burat', '7889', '$2y$10$oGeJT3cD7QE56sfQQsXI/.lq7Ko1NfxlPcR0k2GROjs.oH0fq9v7u', '2024-08-17 04:20:19', 1),
(23, 'test admin', 'admin@gmail.com', '09123456789', '2024-08-22', 'Female', 'fhjrwh', 'uyrfuyrgj', 'Central Visayas (Region VII)', 'yjerrgtf', 'yujsfguyregfu', '2345', '$2y$10$O1ScHoNYxHCYhIs9HR7SBexCJwqcLa.LAAUeRQvNvLpNEVEHNiHP2', '2024-08-19 10:53:50', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `fk_user_feedback` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_ibfk_1` (`brand_id`),
  ADD KEY `fk_category` (`category`),
  ADD KEY `products_ibfk_2` (`size_id`);

--
-- Indexes for table `product_details`
--
ALTER TABLE `product_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`id`,`size_id`),
  ADD KEY `fk_size` (`size_id`);

--
-- Indexes for table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`size_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT for table `product_details`
--
ALTER TABLE `product_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `fk_user_feedback` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`size_id`);

--
-- Constraints for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD CONSTRAINT `fk_size` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`size_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_sizes_ibfk_1` FOREIGN KEY (`id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_sizes_ibfk_2` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`size_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

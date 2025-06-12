-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2025 at 05:11 AM
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
-- Database: `ecommerce_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `description`, `created_at`) VALUES
(5, 'Ten11', 'ten11', '2025-03-02 11:11:56'),
(6, 'ZANDO', 'zando', '2025-03-02 11:12:06'),
(7, 'Varman', 'varman', '2025-03-02 13:33:53');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(4, 'NEW ARRIVALS', 'man clhotes', '2025-03-02 11:11:11'),
(5, 'COUPLE WATCHES', 'woman', '2025-03-02 11:11:30'),
(6, 'MEN WATCHES', 'wtach', '2025-03-02 13:35:07'),
(7, 'LADY WATCHES', 'Woman-Watch', '2025-03-02 13:37:27');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `shipping_address` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `shipping_address`, `payment_method`, `created_at`, `updated_at`) VALUES
(2, 17, 1299.99, 'delivered', '123 Main St, City, Country', 'credit_card', '2025-05-11 09:54:50', '2025-06-10 09:58:15');

-- --------------------------------------------------------

--
-- Stand-in structure for view `order_details`
-- (See below for the actual view)
--
CREATE TABLE `order_details` (
`item_id` int(11)
,`order_id` int(11)
,`product_id` int(11)
,`product_name` varchar(255)
,`quantity` int(11)
,`price` decimal(10,2)
,`subtotal` decimal(20,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `created_at`) VALUES
(7, 2, 40, 1, 1299.99, '2025-06-10 09:58:15');

--
-- Triggers `order_items`
--
DELIMITER $$
CREATE TRIGGER `after_order_item_delete` AFTER DELETE ON `order_items` FOR EACH ROW BEGIN
    UPDATE orders 
    SET total_price = (
        SELECT COALESCE(SUM(quantity * price), 0)
        FROM order_items 
        WHERE order_id = OLD.order_id
    )
    WHERE id = OLD.order_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_order_item_delete_stock` AFTER DELETE ON `order_items` FOR EACH ROW BEGIN
    UPDATE products 
    SET stock = stock + OLD.quantity
    WHERE id = OLD.product_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_order_item_insert` AFTER INSERT ON `order_items` FOR EACH ROW BEGIN
    UPDATE orders 
    SET total_price = (
        SELECT SUM(quantity * price) 
        FROM order_items 
        WHERE order_id = NEW.order_id
    )
    WHERE id = NEW.order_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_order_item_insert_stock` AFTER INSERT ON `order_items` FOR EACH ROW BEGIN
    UPDATE products 
    SET stock = stock - NEW.quantity
    WHERE id = NEW.product_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_order_item_update` AFTER UPDATE ON `order_items` FOR EACH ROW BEGIN
    UPDATE orders 
    SET total_price = (
        SELECT SUM(quantity * price) 
        FROM order_items 
        WHERE order_id = NEW.order_id
    )
    WHERE id = NEW.order_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_order_item_update_stock` AFTER UPDATE ON `order_items` FOR EACH ROW BEGIN
    UPDATE products 
    SET stock = stock + OLD.quantity - NEW.quantity
    WHERE id = NEW.product_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `order_summaries`
-- (See below for the actual view)
--
CREATE TABLE `order_summaries` (
`id` int(11)
,`user_id` int(11)
,`user_email` varchar(255)
,`total_price` decimal(10,2)
,`status` enum('pending','processing','shipped','delivered','cancelled')
,`shipping_address` text
,`payment_method` varchar(50)
,`created_at` timestamp
,`total_items` bigint(21)
,`total_quantity` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle1` varchar(255) DEFAULT NULL,
  `description1` text DEFAULT NULL,
  `subtitle2` varchar(255) DEFAULT NULL,
  `description2` text DEFAULT NULL,
  `image1` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `page_name` enum('About','Contact') NOT NULL,
  `banner_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `title`, `subtitle1`, `description1`, `subtitle2`, `description2`, `image1`, `image2`, `address`, `phone`, `email`, `page_name`, `banner_image`) VALUES
(1, 'About', 'Our Story', '<pre>\r\nVARMANKH Co.,Ltd was established in 2018, our brand has swiftly become a beacon of trust and quality in the Cambodian market. We take pride in being fully registered with the government, a testament to our commitment to fair trade practices. Our meticulous attention to detail is reflected in our product line, crafted by seasoned experts with a wealth of experience in the industry. The adept minds behind our designs possess not only extensive knowledge but also an innate talent for delivering cutting-edge solutions. We set the standard for professionalism, ensuring that our clients receive nothing short of the best.</pre>\r\n', 'Our MIssion', '<p>Mauris non lacinia magna. Sed nec lobortis dolor. Vestibulum rhoncus dignissim risus, sed consectetur erat. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam maximus mauris sit amet odio convallis, in pharetra magna gravida. Praesent sed nunc fermentum mi molestie tempor. Morbi vitae viverra odio. Pellentesque ac velit egestas, luctus arcu non, laoreet mauris. Sed in ipsum tempor, consequat odio in, porttitor ante. Ut mauris ligula, volutpat in sodales in, porta non odio. Pellentesque tempor urna vitae mi vestibulum, nec venenatis nulla lobortis. Proin at gravida ante. Mauris auctor purus at lacus maximus euismod. Pellentesque vulputate massa ut nisl hendrerit, eget elementum libero iaculis..</p>\r\n', 'UAS3.webp', 'IMG_5509.webp', '', '', '', 'About', 'banner_image_684946d088068.webp'),
(2, 'Contact Us', NULL, NULL, NULL, NULL, './admin/views/pages/uploads/image1_684849a25435b.webp', NULL, '379 Hudson St, New York, NY 10018', '+855 883386537', 'hengvanreuth@gamil.com', 'Contact', 'banner_image_6849489c5f745.webp');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `category_id`, `brand_id`, `image`, `created_at`) VALUES
(27, 'The AirCross Next-Gen Black', 'The AirCross Next-Gen Black', 119.00, 10, 6, 5, 'uploads/IMG_1895.webp', '2025-03-02 13:35:40'),
(28, 'The King Majestic Silver', 'The King Majestic Silver', 650.00, 5, 6, 7, 'uploads/IMG_3853_df4d09d6-66ef-40a4-ba55-ce3862e8d6cd.webp', '2025-03-02 13:36:24'),
(29, 'The King AirCross Cyan Black', 'The King AirCross Cyan Black', 189.00, 10, 6, 7, 'uploads/IMG_3943.webp', '2025-03-02 13:38:12'),
(30, 'The King AirCross Midnight Blue', 'The King AirCross Midnight Blue', 189.00, 10, 6, 7, 'uploads/IMG_3924.webp', '2025-03-02 13:38:52'),
(31, 'The Queen Marvelous Silver', 'The Queen Marvelous Silver', 569.00, 2, 7, 7, 'uploads/IMG_9601.webp', '2025-03-02 13:40:02'),
(32, 'The Queen Majestic Silver', 'The Queen Majestic Silver', 620.00, 5, 7, 7, 'uploads/IMG_3864.webp', '2025-03-02 13:40:52'),
(33, 'VARMAN THE GEAR BLUE', 'VARMAN THE GEAR BLUE', 169.00, 10, 6, 7, 'uploads/IMG_0220.webp', '2025-03-04 02:09:40'),
(34, 'THE NOBLE SHADOW GRAY STEEL', 'THE NOBLE SHADOW GRAY STEEL', 349.00, 5, 4, 7, 'uploads/IMG_0319.webp', '2025-06-08 02:33:41'),
(35, 'THE NOBLE STONE BLUE RUBBER', 'THE NOBLE STONE BLUE RUBBER', 349.00, 10, 4, 7, 'uploads/IMG_0303.webp', '2025-06-08 02:34:39'),
(36, 'The King Marvelous Silver', 'The King Marvelous Silver', 249.00, 15, 5, 7, 'uploads/IMG_9568.webp', '2025-06-08 02:35:27'),
(37, 'The Queen Marvelous Silver', 'The Queen Marvelous Silver', 249.00, 15, 5, 7, 'uploads/IMG_9568.webp', '2025-06-08 02:36:01'),
(38, 'THE NOBLE AIR DESERT STEEL', 'THE NOBLE AIR DESERT STEEL', 349.00, 15, 4, 7, 'uploads/IMG_0311.webp', '2025-06-08 02:37:10'),
(39, 'THE NOBLE SHADOW GRAY RUBBER', 'THE NOBLE SHADOW GRAY RUBBER', 349.00, 5, 4, 7, 'uploads/IMG_0307.webp', '2025-06-08 02:37:45'),
(40, 'VARMAN The Milky Way (Black-Rosegold)', 'VARMAN The Milky Way (Black-Rosegold)', 219.00, 9, 5, 7, 'uploads/IMG_5418.webp', '2025-06-08 02:38:32'),
(41, 'VARMAN BAME3 LADY ROSE GOLD', 'VARMAN BAME3 LADY ROSE GOLD', 219.00, 5, 5, 7, 'uploads/DPP_0057.webp', '2025-06-08 02:39:07'),
(42, 'VARMAN BAME3 LADY ROSE GOLD', 'VARMAN BAME3 LADY ROSE GOLD', 219.00, 10, 7, 7, 'uploads/DPP_0057.webp', '2025-06-08 02:39:59'),
(43, 'VARMAN BAME3 LADY GOLD', 'VARMAN BAME3 LADY GOLD', 299.00, 10, 7, 7, 'uploads/DPP_0055.webp', '2025-06-08 02:40:31');

-- --------------------------------------------------------

--
-- Table structure for table `sliders`
--

CREATE TABLE `sliders` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sliders`
--

INSERT INTO `sliders` (`id`, `title`, `description`, `image_path`, `status`, `created_at`, `updated_at`) VALUES
(3, 'Man New-Season', 'NEW SEASON', 'slider_68494162768858.87423164.jpg', 1, '2025-06-11 03:42:04', '2025-06-11 08:43:25'),
(4, 'Women New-Season', 'Jackets & Coats', 'slider_684941c85f1188.58232439.jpg', 1, '2025-06-11 08:43:52', '2025-06-11 08:43:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `role`, `created_at`, `status`) VALUES
(4, 'Vanreuth', 'Vanreuth@gmail.com', '123', '1234567890', '123 Main St', 'admin', '2025-02-26 09:02:17', 'active'),
(6, 'Vanreuth', 'Vanreuth17@gmail.com', '$2y$10$5n02VpLmy6K76kUbQZFiPAslYdZthKN9Oa7y5cOKS6qfO2r4xj5WS', '1234567890', '123 Street, City', 'admin', '2025-02-26 05:34:56', 'active'),
(13, 'John Doe', 'Vanreuth18@gmail.com', '1234', '1234567890', '123 Main St, City', 'admin', '2025-03-05 09:00:17', 'active'),
(17, 'Heng Vanreuth', 'hengvanreuth@17gmail.com', '$2y$10$wBxKajvzwrgxOdbTmI2jI.kMjVgw0cgibs2FUWK1UV9vR9TZxwAEG', '0999999230', 'BTB\r\nBTB', 'admin', '2025-06-10 06:18:12', 'active');

-- --------------------------------------------------------

--
-- Structure for view `order_details`
--
DROP TABLE IF EXISTS `order_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `order_details`  AS SELECT `oi`.`id` AS `item_id`, `oi`.`order_id` AS `order_id`, `oi`.`product_id` AS `product_id`, `p`.`name` AS `product_name`, `oi`.`quantity` AS `quantity`, `oi`.`price` AS `price`, `oi`.`quantity`* `oi`.`price` AS `subtotal` FROM (`order_items` `oi` left join `products` `p` on(`oi`.`product_id` = `p`.`id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `order_summaries`
--
DROP TABLE IF EXISTS `order_summaries`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `order_summaries`  AS SELECT `o`.`id` AS `id`, `o`.`user_id` AS `user_id`, `u`.`email` AS `user_email`, `o`.`total_price` AS `total_price`, `o`.`status` AS `status`, `o`.`shipping_address` AS `shipping_address`, `o`.`payment_method` AS `payment_method`, `o`.`created_at` AS `created_at`, count(`oi`.`id`) AS `total_items`, sum(`oi`.`quantity`) AS `total_quantity` FROM ((`orders` `o` left join `users` `u` on(`o`.`user_id` = `u`.`id`)) left join `order_items` `oi` on(`o`.`id` = `oi`.`order_id`)) GROUP BY `o`.`id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orders_user` (`user_id`),
  ADD KEY `idx_orders_status` (`status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_items_order` (`order_id`),
  ADD KEY `idx_order_items_product` (`product_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Indexes for table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

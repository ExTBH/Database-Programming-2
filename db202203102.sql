-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: April 28, 2025 at 12:12 PM
-- Server version: 10.3.27-MariaDB
-- PHP Version: 7.2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db202203102`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `charge_point_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `status` enum('pending','approved','declined','completed','cancelled') DEFAULT 'pending',
  `total_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `charge_points`
--

CREATE TABLE `charge_points` (
  `charge_point_id` int(11) NOT NULL,
  `homeowner_id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `postcode` varchar(20) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `price_per_kwh` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `charge_point_images`
--

CREATE TABLE `charge_point_images` (
  `image_id` int(11) NOT NULL,
  `charge_point_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `role` enum('admin','homeowner','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `HomeOwnerRequests`
--

CREATE TABLE `HomeOwnerRequests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `approval_status` enum('pending', 'rejected', 'approved') NOT NULL DEFAULT 'pending',
  `rejection_message` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Dumping data for table `users`

INSERT INTO `users` (`user_id`, `email`, `password`, `first_name`, `last_name`, `role`) VALUES
(1, 'admin@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'admin'),
(2, 'lee@lee.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lee', 'Griffiths', 'homeowner'),
(3, 'user@user.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'User', 'Lee', 'user'),
(4, 'email@gmail.com', '$2y$10$yD.OtOGMKRe6BhudiGbDZ.2jGL06KCSOKKWWlRoOtvQq9VGgaFBUG', 'Maryam', 'K', 'user'),
(5, 'emai@gmail.com', '$2y$10$pg/ZkVq4tTAtTy1zOWZN6Oxvd4pnXFKjHWAFrLxSdUJNvzPT/p6mW', 'Maryam', 'K', 'user'),
(6, 'natheer@gmail.com', '$2y$12$Fcvz7e4Fm/kEihldSBjhXu4BPL7nsPp9qZRt2SWKaIYetoXdUIC6.', 'Natheer', 'Radhi', 'user');

-- --------------------------------------------------------

-- Indexes for dumped tables

-- Indexes for table `bookings`
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `charge_point_id` (`charge_point_id`),
  ADD KEY `user_id` (`user_id`);

-- Indexes for table `charge_points`
ALTER TABLE `charge_points`
  ADD PRIMARY KEY (`charge_point_id`),
  ADD KEY `homeowner_id` (`homeowner_id`);

-- Indexes for table `charge_point_images`
ALTER TABLE `charge_point_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `charge_point_id` (`charge_point_id`);

-- Indexes for table `messages`
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `recipient_id` (`recipient_id`);

-- Indexes for table `reviews`
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `booking_id` (`booking_id`);

-- Indexes for table `users`
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

-- Indexes for table `HomeOwnerRequests`
ALTER TABLE `HomeOwnerRequests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_unique` (`email`);

-- --------------------------------------------------------

-- AUTO_INCREMENT for dumped tables

-- AUTO_INCREMENT for table `bookings`
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT for table `charge_points`
ALTER TABLE `charge_points`
  MODIFY `charge_point_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

-- AUTO_INCREMENT for table `charge_point_images`
ALTER TABLE `charge_point_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT for table `messages`
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT for table `reviews`
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT for table `users`
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

-- AUTO_INCREMENT for table `HomeOwnerRequests`
ALTER TABLE `HomeOwnerRequests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- Constraints for dumped tables

-- Constraints for table `bookings`
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`charge_point_id`) REFERENCES `charge_points` (`charge_point_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

-- Constraints for table `charge_points`
ALTER TABLE `charge_points`
  ADD CONSTRAINT `charge_points_ibfk_1` FOREIGN KEY (`homeowner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

-- Constraints for table `charge_point_images`
ALTER TABLE `charge_point_images`
  ADD CONSTRAINT `charge_point_images_ibfk_1` FOREIGN KEY (`charge_point_id`) REFERENCES `charge_points` (`charge_point_id`) ON DELETE CASCADE;

-- Constraints for table `messages`
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

-- Constraints for table `reviews`
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

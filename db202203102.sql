SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


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

INSERT INTO `bookings` (`booking_id`, `charge_point_id`, `user_id`, `start_time`, `end_time`, `status`, `total_price`, `created_at`, `updated_at`) VALUES
(1, 2, 7, '2025-05-06 09:00:00', '2025-05-06 11:00:00', 'approved', '4.50', '2025-05-05 07:50:50', '2025-05-05 07:50:50'),
(2, 3, 9, '2025-05-07 14:00:00', '2025-05-07 16:00:00', 'pending', '5.00', '2025-05-05 07:50:50', '2025-05-05 07:50:50'),
(3, 2, 3, '2025-05-08 18:00:00', '2025-05-08 20:00:00', 'completed', '6.00', '2025-05-05 07:50:50', '2025-05-05 07:50:50');

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

INSERT INTO `charge_points` (`charge_point_id`, `homeowner_id`, `address`, `postcode`, `latitude`, `longitude`, `price_per_kwh`, `description`, `is_available`, `created_at`, `updated_at`) VALUES
(2, 8, '123 Main St, Capital City', 'CC1001', '26.22340000', '50.58750000', '0.30', 'Covered garage charge point', 1, '2025-05-05 07:50:50', '2025-05-05 07:50:50'),
(3, 10, '456 Palm Ave, Green Town', 'GT2202', '26.20210000', '50.58220000', '0.25', 'Fast charger under shade', 1, '2025-05-05 07:50:50', '2025-05-05 07:50:50');

CREATE TABLE `charge_point_images` (
  `image_id` int(11) NOT NULL,
  `charge_point_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `HomeOwnerRequests` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `approval_status` enum('pending','rejected','approved') NOT NULL DEFAULT 'pending',
  `rejection_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `HomeOwnerRequests` (`id`, `email`, `first_name`, `last_name`, `password`, `created_at`, `approval_status`, `rejection_message`) VALUES
(1, 'future.owner1@example.com', 'Ali', 'Hassan', '$2y$10$FuturePass123', '2025-05-05 07:50:50', 'pending', NULL),
(2, 'future.owner2@example.com', 'Fatima', 'Yousef', '$2y$10$FuturePass456', '2025-05-05 07:50:50', 'pending', NULL);

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `messages` (`message_id`, `booking_id`, `sender_id`, `recipient_id`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 7, 8, 'Hi, I will arrive a bit early, hope that’s okay.', 0, '2025-05-05 07:50:50'),
(2, 2, 9, 10, 'Can I extend the time if needed?', 0, '2025-05-05 07:50:50'),
(3, 3, 3, 8, 'Thanks for the great service!', 1, '2025-05-05 07:50:50');

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `reviews` (`review_id`, `booking_id`, `rating`, `comment`, `created_at`) VALUES
(1, 3, 5, 'Very smooth experience. Highly recommended.', '2025-05-05 07:50:50'),
(2, 1, 4, 'Charger worked fine, but location a bit tricky to find.', '2025-05-05 07:50:50');

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `role` enum('admin','homeowner','user') NOT NULL,
  `suspended` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `users` (`user_id`, `email`, `password`, `first_name`, `last_name`, `role`, `suspended`) VALUES
(1, 'admin@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'nothing', 'User', 'homeowner', 0),
(2, 'lee@lee.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lee', 'Griffiths', 'homeowner', 0),
(3, 'user@user.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'User', 'Lee', 'user', 0),
(4, 'email@gmail.com', '$2y$10$yD.OtOGMKRe6BhudiGbDZ.2jGL06KCSOKKWWlRoOtvQq9VGgaFBUG', 'Maryam', 'K', 'user', 0),
(5, 'emai@gmail.com', '$2y$10$pg/ZkVq4tTAtTy1zOWZN6Oxvd4pnXFKjHWAFrLxSdUJNvzPT/p6mW', 'Maryam', 'K', 'user', 0),
(6, 'natheer@gmail.com', '$2y$12$i4NMnKp3mE5It8aBhzYeZ.KQwwwOFGqwLVaCwEh1RWIkdw87T1wpC', 'Natheer', 'Radhi', 'user', 0),
(7, 'emailll@gmail.com', '$2y$10$X.WlNs4044zKNDhwC8d.Ze99DFIifxXqIZHHnrig8psamPZp/j7hm', 'Maryam', 'K', 'user', 0),
(8, 'eee@gmail.com', '$2y$10$teCZIgXKcpRbFwTGRPGL5OdTOPDaPn09TVjF8Ua6GcBPHJe/B4Zq6', 'Maryam', 'K', 'user', 0),
(9, 'u202201969@email.com', '$2y$10$UrtkU.eG7zbthg9c3dSez./NpljI7wNhh7T2cVUlbWJljxpBdE7n.', 'Maryam', 'K', 'user', 0),
(10, 'lifeasmaryam7@gmail.com', '$2y$10$N2i/ZeJXtGVpU8cuOyTdzuSePsEnRC9QrjSECKSncSBo7n1zV4XD2', 'Maryam', 'K', 'user', 0),
(13, '22222@gmail.com', '$2y$10$q7EExZGdYSbbN03z5nEpzutayzOZHF65L2yIFk051ujjDkUgPoqxa', 'Maryam', 'K', 'admin', 0),
(15, 'WW@gmail.com', '11', 'Admin', 'User', 'homeowner', 0),
(16, 'john.doe@example.com', '$2y$10$drkFk9LtYOPa2idTPWVAy.wnQxhEod44B/8EFiTqXCLZeQNawlwbG', 'soethinf', 'jj', 'homeowner', 0),
(17, 'admin@example.com', '$2y$10$PA00CI/hhsJ6Qi7KY5FUEu1wqs6Y/I83XqsUBPU0orlRIPp7JFvMO', 'soethinf', 'jj', 'user', 0),
(18, 'jane.doe@example.com', '$2y$10$abc123hashedpasswordtextxyz', 'Jane', 'Doe', 'user', 0),
(19, 'john.smith@example.com', '$2y$10$xyz789hashedpasswordtextabc', 'John', 'Smith', 'homeowner', 0),
(20, 'sara.connor@example.com', '$2y$10$passforSaraXyz1234567890', 'Sara', 'Connor', 'user', 0),
(21, 'mike.ross@example.com', '$2y$10$MikeHashedPass2024TestData', 'Mike', 'Ross', 'homeowner', 0);


ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `charge_point_id` (`charge_point_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `charge_points`
  ADD PRIMARY KEY (`charge_point_id`),
  ADD KEY `homeowner_id` (`homeowner_id`);

ALTER TABLE `charge_point_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `charge_point_id` (`charge_point_id`);

ALTER TABLE `HomeOwnerRequests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_unique` (`email`);

ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `recipient_id` (`recipient_id`);

ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `booking_id` (`booking_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);


ALTER TABLE `HomeOwnerRequests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

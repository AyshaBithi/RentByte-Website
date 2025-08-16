-- RentByte Database Schema
-- Create database and tables for the rental system

-- Create database
CREATE DATABASE IF NOT EXISTS `rent` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rent`;

-- Users table
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_username` (`username`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories table
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Gadgets table
CREATE TABLE `gadgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `category_id` int(11) NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `location` varchar(100) NOT NULL,
  `status` enum('available','rented','maintenance','inactive') NOT NULL DEFAULT 'available',
  `specifications` text DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `condition_note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_gadgets_category` (`category_id`),
  KEY `idx_status` (`status`),
  KEY `idx_location` (`location`),
  CONSTRAINT `fk_gadgets_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rentals table
CREATE TABLE `rentals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `gadget_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` int(11) NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','active','completed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_status` enum('pending','paid','refunded') NOT NULL DEFAULT 'pending',
  `delivery_type` enum('pickup','standard','express') NOT NULL DEFAULT 'pickup',
  `delivery_address` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_rentals_user` (`user_id`),
  KEY `fk_rentals_gadget` (`gadget_id`),
  KEY `idx_status` (`status`),
  KEY `idx_dates` (`start_date`, `end_date`),
  CONSTRAINT `fk_rentals_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rentals_gadget` FOREIGN KEY (`gadget_id`) REFERENCES `gadgets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rental history table for tracking status changes
CREATE TABLE `rental_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rental_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_rental_history_rental` (`rental_id`),
  KEY `fk_rental_history_user` (`changed_by`),
  CONSTRAINT `fk_rental_history_rental` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rental_history_user` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default categories
INSERT INTO `categories` (`name`, `description`) VALUES
('Laptops', 'Portable computers for work and entertainment'),
('Smartphones', 'Mobile phones with advanced features'),
('Cameras', 'Digital cameras for photography and videography'),
('Drones', 'Unmanned aerial vehicles for photography and recreation'),
('Gaming', 'Gaming consoles and accessories'),
('Audio', 'Speakers, headphones, and audio equipment'),
('Home Appliances', 'Kitchen and household appliances'),
('Tablets', 'Tablet computers and e-readers');

-- Insert default admin user (password: password)
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`) VALUES
('admin', 'admin@mail.com', '$2y$12$XsMwY0o/.NdEgbUWUrTZJeYhTsxAkTqKYfKhHowBBcq4km7KkQC/G', 'System Administrator', 'admin');

-- Insert sample gadgets
INSERT INTO `gadgets` (`name`, `description`, `category_id`, `price_per_day`, `image`, `location`, `brand`, `model`, `specifications`) VALUES
('Canon EOS R5', 'Professional mirrorless camera with 45MP sensor', 3, 25.00, 'assets/img/gadget-canon.jpg', 'Dhaka', 'Canon', 'EOS R5', '45MP Full-frame sensor, 8K video recording'),
('MacBook Pro 16"', 'High-performance laptop for professionals', 1, 35.00, 'assets/img/gadget-laptop.jpg', 'Dhaka', 'Apple', 'MacBook Pro', 'M2 Pro chip, 16GB RAM, 512GB SSD'),
('DJI Air 2S', 'Compact drone with 1-inch sensor', 4, 20.00, 'assets/img/gadget-drone.jpg', 'Chittagong', 'DJI', 'Air 2S', '1-inch CMOS sensor, 5.4K video'),
('iPhone 15 Pro', 'Latest iPhone with titanium design', 2, 15.00, 'assets/img/gadget-iphone.jpg', 'Dhaka', 'Apple', 'iPhone 15 Pro', 'A17 Pro chip, 128GB storage, Triple camera'),
('Electric Oven', 'Compact electric oven for cooking', 7, 8.00, 'assets/img/gadget-oven.jpg', 'Rajshahi', 'Panasonic', 'NN-ST34H', '25L capacity, 800W power'),
('Bluetooth Speaker', 'Portable wireless speaker', 6, 5.00, 'assets/img/gadgte-speaker.jpg', 'Cox\'s Bazar', 'JBL', 'Charge 5', 'Waterproof, 20-hour battery life');

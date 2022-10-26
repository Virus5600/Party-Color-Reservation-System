-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 26, 2022 at 02:46 PM
-- Server version: 5.7.36
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pcrs`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
CREATE TABLE IF NOT EXISTS `announcements` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `poster` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default.png',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci,
  `is_draft` tinyint(4) NOT NULL DEFAULT '1',
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `announcements_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `announcement_content_images`
--

DROP TABLE IF EXISTS `announcement_content_images`;
CREATE TABLE IF NOT EXISTS `announcement_content_images` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `announcement_id` bigint(20) UNSIGNED NOT NULL,
  `image_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `announcement_content_images_announcement_id_foreign` (`announcement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(10, '2014_10_11_999997_create_permissions_table', 1),
(11, '2014_10_11_999998_create_types_table', 1),
(12, '2014_10_11_999999_create_type_permissions_table', 1),
(13, '2014_10_12_000000_create_users_table', 1),
(14, '2014_10_12_100000_create_password_resets_table', 1),
(15, '2022_10_10_135607_create_user_permissions_table', 1),
(16, '2022_10_14_012146_create_settings_table', 1),
(17, '2022_10_21_135542_create_announcements_table', 1),
(18, '2022_10_25_053030_create_announcement_content_images_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_permission` int(11) DEFAULT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `parent_permission`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Reservations Tab Access', 'reservations_tab_access', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(2, 1, 'Reservations Tab Respond', 'reservations_tab_respond', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(3, 1, 'Reservations Tab Delete', 'reservations_tab_delete', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(4, 1, 'Reservations Tab Perma Delete', 'reservations_tab_perma_delete', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(5, NULL, 'Inventory Tab Access', 'inventory_tab_access', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(6, 5, 'Inventory Tab Create', 'inventory_tab_create', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(7, 5, 'Inventory Tab Edit', 'inventory_tab_edit', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(8, 5, 'Inventory Tab Delete', 'inventory_tab_delete', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(9, 5, 'Inventory Tab Perma Delete', 'inventory_tab_perma_delete', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(10, NULL, 'Announcements Tab Access', 'announcements_tab_access', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(11, 10, 'Announcements Tab Create', 'announcements_tab_create', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(12, 10, 'Announcements Tab Edit', 'announcements_tab_edit', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(13, 10, 'Announcements Tab Publish', 'announcements_tab_publish', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(14, 10, 'Announcements Tab Unpublish', 'announcements_tab_unpublish', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(15, 10, 'Announcements Tab Send Mail', 'announcements_tab_send_mail', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(16, 10, 'Announcements Tab Delete', 'announcements_tab_delete', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(17, 10, 'Announcements Tab Perma Delete', 'announcements_tab_perma_delete', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(18, NULL, 'Users Tab Access', 'users_tab_access', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(19, 18, 'Users Tab Create', 'users_tab_create', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(20, 18, 'Users Tab Edit', 'users_tab_edit', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(21, 18, 'Users Tab Permissions', 'users_tab_permissions', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(22, 18, 'Users Tab Delete', 'users_tab_delete', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(23, 18, 'Users Tab Perma Delete', 'users_tab_perma_delete', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(24, NULL, 'Permissions Tab Access', 'permissions_tab_access', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(25, 24, 'Permissions Tab Manage', 'permissions_tab_manage', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(26, NULL, 'Settings Tab Access', 'settings_tab_access', '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(27, 26, 'Settings Tab Edit', 'settings_tab_edit', '2022-10-26 06:45:37', '2022-10-26 06:45:37');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_file` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`, `is_file`, `created_at`, `updated_at`) VALUES
(1, 'web-logo', 'party_color.png', 1, '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(2, 'web-name', 'Party Color', 0, '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(3, 'web-desc', 'Party Color website that offers reservation for barbecue plan, promos etc', 0, '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(4, 'address', '2-2-12 Nakahara Building 3F Tsuboya Naha city Okinawa, Japan', 0, '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(5, 'contacts', '080-3980-4560', 0, '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(6, 'email', 'partycolor3f@gmail.com', 0, '2022-10-26 06:45:37', '2022-10-26 06:45:37');

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

DROP TABLE IF EXISTS `types`;
CREATE TABLE IF NOT EXISTS `types` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `types`
--

INSERT INTO `types` (`id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Master Admin', NULL, '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(2, 'Manager', NULL, '2022-10-26 06:45:37', '2022-10-26 06:45:37'),
(3, 'Staff', NULL, '2022-10-26 06:45:37', '2022-10-26 06:45:37');

-- --------------------------------------------------------

--
-- Table structure for table `type_permissions`
--

DROP TABLE IF EXISTS `type_permissions`;
CREATE TABLE IF NOT EXISTS `type_permissions` (
  `type_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  KEY `type_permissions_type_id_foreign` (`type_id`),
  KEY `type_permissions_permission_id_foreign` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `type_permissions`
--

INSERT INTO `type_permissions` (`type_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `suffix` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default.png',
  `type_id` bigint(20) UNSIGNED NOT NULL,
  `login_attempts` tinyint(4) NOT NULL DEFAULT '0',
  `locked` tinyint(4) NOT NULL DEFAULT '0',
  `locked_by` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_auth` datetime DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_type_id_foreign` (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `middle_name`, `last_name`, `suffix`, `email`, `avatar`, `type_id`, `login_attempts`, `locked`, `locked_by`, `password`, `last_auth`, `remember_token`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'アドミン', NULL, 'アカウント', NULL, 'privatelaravelmailtester@gmail.com', 'default.png', 1, 0, 0, NULL, '$2y$10$nshe1evhiHuwvaVlR70q7.CwoBnwI98nvmwbvh3j3EQDMhzAbG3Se', NULL, NULL, NULL, '2022-10-26 06:45:37', '2022-10-26 06:45:37');

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

DROP TABLE IF EXISTS `user_permissions`;
CREATE TABLE IF NOT EXISTS `user_permissions` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  KEY `user_permissions_user_id_foreign` (`user_id`),
  KEY `user_permissions_permission_id_foreign` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `announcement_content_images`
--
ALTER TABLE `announcement_content_images`
  ADD CONSTRAINT `announcement_content_images_announcement_id_foreign` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `type_permissions`
--
ALTER TABLE `type_permissions`
  ADD CONSTRAINT `type_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `type_permissions_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

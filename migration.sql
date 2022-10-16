-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 16, 2022 at 01:06 PM
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
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_11_999997_create_permissions_table', 1),
(2, '2014_10_11_999998_create_types_table', 1),
(3, '2014_10_11_999999_create_type_permissions_table', 1),
(4, '2014_10_12_000000_create_users_table', 1),
(5, '2014_10_12_100000_create_password_resets_table', 1),
(6, '2022_10_10_135607_create_user_permissions_table', 1),
(7, '2022_10_14_012146_create_settings_table', 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `parent_permission`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Users Tab Access', 'users_tab_access', '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(2, 1, 'Users Tab Create', 'users_tab_create', '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(3, 1, 'Users Tab Edit', 'users_tab_edit', '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(4, 1, 'Users Tab Permissions', 'users_tab_permissions', '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(5, 1, 'Users Tab Delete', 'users_tab_delete', '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(6, 1, 'Users Tab Perma Delete', 'users_tab_perma_delete', '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(7, NULL, 'Reservations Tab Access', 'reservations_tab_access', '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(8, 7, 'Reservations Tab Respond', 'reservations_tab_respond', '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(9, 7, 'Reservations Tab Delete', 'reservations_tab_delete', '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(10, 7, 'Reservations Tab Perma Delete', 'reservations_tab_perma_delete', '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(11, NULL, 'Permissions Tab Access', 'permissions_tab_access', '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(12, 11, 'Permissions Tab Manage', 'permissions_tab_manage', '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(13, NULL, 'Settings Tab Access', 'settings_tab_access', '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(14, 13, 'Settings Tab Edit', 'settings_tab_edit', '2022-10-16 04:51:21', '2022-10-16 04:51:21');

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
(1, 'web-logo', 'party_color.png', 1, '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(2, 'web-name', 'Party Color', 0, '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(3, 'web-desc', 'Party Color website that offers reservation for barbecue plan, promos etc', 0, '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(4, 'address', '2-2-12 Nakahara Building 3F Tsuboya Naha city Okinawa, Japan', 0, '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(5, 'contacts', '080-3980-4560', 0, '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(6, 'email', 'partycolor3f@gmail.com', 0, '2022-10-16 04:51:21', '2022-10-16 04:51:21');

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
(1, 'Master Admin', NULL, '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(2, 'Manager', NULL, '2022-10-16 04:51:21', '2022-10-16 04:51:21'),
(3, 'Staff', NULL, '2022-10-16 04:51:21', '2022-10-16 04:51:21');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

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

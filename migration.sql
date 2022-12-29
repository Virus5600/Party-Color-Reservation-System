-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 29, 2022 at 03:56 AM
-- Server version: 5.7.36
-- PHP Version: 8.0.13

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
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_marked` tinyint(4) NOT NULL DEFAULT '0',
  `reason` mediumtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `poster`, `title`, `slug`, `summary`, `content`, `is_draft`, `user_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, '1-63ab36cd649ecポスター.png', 'Halloween 15% Discount Promo', 'Halloween_15%_Discount_Promo', 'Limited time discount available this Holloween!', '<p>BBQ &amp; Drinks Plan<p>Adult - Senior High: &#65509;3,500 to &#65509; 2,975 BBQ &amp; Drinks Plan</p></p>', 0, 1, NULL, '2022-12-28 19:55:48', '2022-12-28 19:55:48');

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
-- Table structure for table `contact_information`
--

DROP TABLE IF EXISTS `contact_information`;
CREATE TABLE IF NOT EXISTS `contact_information` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `reservation_id` bigint(20) UNSIGNED NOT NULL,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `inventories`
--

DROP TABLE IF EXISTS `inventories`;
CREATE TABLE IF NOT EXISTS `inventories` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `item_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `measurement_unit` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventories_item_name_unique` (`item_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `inventories`
--

INSERT INTO `inventories` (`id`, `item_name`, `quantity`, `measurement_unit`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Pork', 50, 'kg', NULL, '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(2, 'Beef', 50, 'kg', NULL, '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(3, 'Chicken', 50, 'kg', NULL, '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(4, 'Coke', 10, 'L', NULL, '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(5, 'Iced Tea', 10, 'L', NULL, '2022-12-28 19:55:48', '2022-12-28 19:55:48');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
CREATE TABLE IF NOT EXISTS `menus` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double UNSIGNED NOT NULL DEFAULT '0',
  `duration` time NOT NULL DEFAULT '01:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `name`, `price`, `duration`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'BBQ Plan', 3500, '02:00:00', NULL, '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(2, 'Drink All You Can', 1200, '01:00:00', NULL, '2022-12-28 19:55:48', '2022-12-28 19:55:48');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE IF NOT EXISTS `menu_items` (
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_id` bigint(20) UNSIGNED NOT NULL,
  `amount` double(8,2) UNSIGNED NOT NULL,
  KEY `menu_items_menu_id_foreign` (`menu_id`),
  KEY `menu_items_inventory_id_foreign` (`inventory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`menu_id`, `inventory_id`, `amount`) VALUES
(1, 1, 5.00),
(1, 2, 5.00),
(1, 3, 5.00),
(2, 4, 5.00),
(2, 5, 5.00);

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
(1, '2014_10_11_999997_create_permissions_table', 1),
(2, '2014_10_11_999998_create_types_table', 1),
(3, '2014_10_11_999999_create_type_permissions_table', 1),
(4, '2014_10_12_000000_create_users_table', 1),
(5, '2014_10_12_100000_create_password_resets_table', 1),
(6, '2022_10_10_135607_create_user_permissions_table', 1),
(7, '2022_10_14_012146_create_settings_table', 1),
(8, '2022_10_21_135542_create_announcements_table', 1),
(9, '2022_10_25_053030_create_announcement_content_images_table', 1),
(10, '2022_11_03_151133_create_inventories_table', 1),
(11, '2022_11_25_071509_create_reservations_table', 1),
(12, '2022_11_27_171832_create_jobs_table', 1),
(13, '2022_11_28_061431_create_failed_jobs_table', 1),
(14, '2022_12_03_153243_create_contact_information_table', 1),
(15, '2022_12_06_055129_create_menus_table', 1),
(16, '2022_12_06_063713_create_menu_items_table', 1),
(17, '2022_12_06_084348_create_reservation_menus_table', 1),
(18, '2022_12_18_085848_create_activity_logs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `password_resets_email_unique` (`email`)
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
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `parent_permission`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Reservations Tab Access', 'reservations_tab_access', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(2, 1, 'Reservations Tab Create', 'reservations_tab_create', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(3, 1, 'Reservations Tab Edit', 'reservations_tab_edit', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(4, 1, 'Reservations Tab Respond', 'reservations_tab_respond', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(5, 1, 'Reservations Tab Delete', 'reservations_tab_delete', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(6, 1, 'Reservations Tab Perma Delete', 'reservations_tab_perma_delete', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(7, NULL, 'Inventory Tab Access', 'inventory_tab_access', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(8, 7, 'Inventory Tab Create', 'inventory_tab_create', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(9, 7, 'Inventory Tab Edit', 'inventory_tab_edit', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(10, 7, 'Inventory Tab Delete', 'inventory_tab_delete', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(11, 7, 'Inventory Tab Perma Delete', 'inventory_tab_perma_delete', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(12, NULL, 'Menu Tab Access', 'menu_tab_access', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(13, 12, 'Menu Tab Create', 'menu_tab_create', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(14, 12, 'Menu Tab Edit', 'menu_tab_edit', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(15, 12, 'Menu Tab Delete', 'menu_tab_delete', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(16, 12, 'Menu Tab Perma Delete', 'menu_tab_perma_delete', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(17, NULL, 'Announcements Tab Access', 'announcements_tab_access', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(18, 17, 'Announcements Tab Create', 'announcements_tab_create', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(19, 17, 'Announcements Tab Edit', 'announcements_tab_edit', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(20, 17, 'Announcements Tab Publish', 'announcements_tab_publish', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(21, 17, 'Announcements Tab Unpublish', 'announcements_tab_unpublish', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(22, 17, 'Announcements Tab Send Mail', 'announcements_tab_send_mail', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(23, 17, 'Announcements Tab Delete', 'announcements_tab_delete', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(24, 17, 'Announcements Tab Perma Delete', 'announcements_tab_perma_delete', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(25, NULL, 'Users Tab Access', 'users_tab_access', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(26, 25, 'Users Tab Create', 'users_tab_create', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(27, 25, 'Users Tab Edit', 'users_tab_edit', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(28, 25, 'Users Tab Permissions', 'users_tab_permissions', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(29, 25, 'Users Tab Delete', 'users_tab_delete', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(30, 25, 'Users Tab Perma Delete', 'users_tab_perma_delete', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(31, NULL, 'Permissions Tab Access', 'permissions_tab_access', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(32, 31, 'Permissions Tab Manage', 'permissions_tab_manage', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(33, NULL, 'Settings Tab Access', 'settings_tab_access', '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(34, 33, 'Settings Tab Edit', 'settings_tab_edit', '2022-12-28 19:55:48', '2022-12-28 19:55:48');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE IF NOT EXISTS `reservations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `start_at` time NOT NULL,
  `end_at` time NOT NULL,
  `reserved_at` date NOT NULL,
  `extension` double UNSIGNED NOT NULL DEFAULT '0',
  `price` double UNSIGNED NOT NULL,
  `pax` int(11) NOT NULL,
  `phone_numbers` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT '0',
  `approved` tinyint(4) NOT NULL DEFAULT '0',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `reservation_menus`
--

DROP TABLE IF EXISTS `reservation_menus`;
CREATE TABLE IF NOT EXISTS `reservation_menus` (
  `reservation_id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  KEY `reservation_menus_reservation_id_foreign` (`reservation_id`),
  KEY `reservation_menus_menu_id_foreign` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`, `is_file`, `created_at`, `updated_at`) VALUES
(1, 'web-logo', 'default.png', 1, '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(2, 'web-name', 'Party Color', 0, '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(3, 'web-desc', 'Party Color website that offers reservation for barbecue plan, promos etc', 0, '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(4, 'address', '2-2-12 Nakahara Building 3F Tsuboya Naha city Okinawa, Japan', 0, '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(5, 'contacts', '080-3980-4560', 0, '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(6, 'emails', 'partycolor3f@gmail.com', 0, '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(7, 'capacity', '50', 0, '2022-12-28 19:55:48', '2022-12-28 19:55:48');

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
(1, 'Master Admin', NULL, '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(2, 'Manager', NULL, '2022-12-28 19:55:48', '2022-12-28 19:55:48'),
(3, 'Staff', NULL, '2022-12-28 19:55:48', '2022-12-28 19:55:48');

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
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 33),
(1, 34),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 10),
(2, 11),
(2, 12),
(2, 13),
(2, 14),
(2, 15),
(2, 16),
(2, 17),
(2, 18),
(2, 19),
(2, 20),
(2, 21),
(2, 22),
(2, 23),
(2, 24),
(3, 7),
(3, 8),
(3, 9);

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
(1, 'アドミン', NULL, 'アカウント', NULL, 'privatelaravelmailtester@gmail.com', 'Karl Satchi-Navida-DP.png', 1, 0, 0, NULL, '$2y$10$13HjywdaQx5/SkP6GSkGD.JfzO01fNPNPCRR3Ac6KOfGnI/A2hoye', NULL, NULL, NULL, '2022-12-28 19:55:48', '2022-12-28 19:55:48');

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
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_email_foreign` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON DELETE CASCADE;

--
-- Constraints for table `reservation_menus`
--
ALTER TABLE `reservation_menus`
  ADD CONSTRAINT `reservation_menus_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_menus_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;

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

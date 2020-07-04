-- Adminer 4.6.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `property_type`;
CREATE TABLE `property_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `property_type_title_index` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `properties`;
CREATE TABLE `properties` (
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `property_type_id` int(10) unsigned DEFAULT '0',
  `county` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `town` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `address` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_full` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_thumbnail` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_local` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `num_bedrooms` tinyint(3) unsigned DEFAULT '0',
  `num_bathrooms` tinyint(3) unsigned DEFAULT '0',
  `price` decimal(12,2) unsigned DEFAULT '0.00',
  `type` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_from` enum('local','live') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'local',
  `postcode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `properties_uuid_index` (`uuid`),
  KEY `properties_property_type_id_index` (`property_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2020-07-04 12:58:59
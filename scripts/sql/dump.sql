-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.16 - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL version:             7.0.0.4093
-- Date/time:                    2012-03-21 22:00:09
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping structure for table impulse.members_members
DROP TABLE IF EXISTS `members_members`;
CREATE TABLE IF NOT EXISTS `members_members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `number` bigint(20) unsigned NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sex` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'MALE',
  `firstname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `birth_date` date NOT NULL,
  `phone` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `number` (`number`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table impulse.members_members: ~8 rows (approximately)
/*!40000 ALTER TABLE `members_members` DISABLE KEYS */;
INSERT INTO `members_members` (`id`, `number`, `email`, `sex`, `firstname`, `lastname`, `birth_date`, `phone`, `created_at`, `updated_at`) VALUES
	(1, 11000, 'pavel.galaton@gmail.com', 'MALE', 'Pavel', 'Galaton', '1987-12-14', '37369520295', '2012-03-13 20:47:46', '2012-03-13 20:47:46'),
	(2, 11001, 'iulia.galaton@gmail.com', 'FEMALE', 'Iulia', 'Galaton', '1989-11-15', '37368116632', '2012-03-13 20:47:46', '2012-03-13 20:47:46'),
	(3, 110012, 'iulia2.galaton@gmail.com', 'MALE', 'Iulia', 'Galaton', '1989-11-15', '37368116632', '2012-03-13 20:47:46', '2012-03-13 20:47:46'),
	(4, 110013, 'iulia3.galaton@gmail.com', 'MALE', 'Iulia', 'Galaton', '1989-11-15', '37368116632', '2012-03-13 20:47:46', '2012-03-13 20:47:46'),
	(5, 110014, 'iulia4.galaton@gmail.com', 'MALE', 'Iulia', 'Galaton', '1989-11-15', '37368116632', '2012-03-13 20:47:46', '2012-03-13 20:47:46'),
	(6, 110015, 'iulia5.galaton@gmail.com', 'MALE', 'Iulia', 'Galaton', '1989-11-15', '37368116632', '2012-03-13 20:47:46', '2012-03-13 20:47:46'),
	(7, 110016, 'iulia6.galaton@gmail.com', 'MALE', 'Iulia', 'Galaton', '1989-11-15', '37368116632', '2012-03-13 20:47:46', '2012-03-13 20:47:46'),
	(8, 110017, 'iulia7.galaton@gmail.com', 'MALE', 'Iulia', 'Galaton', '1989-11-15', '37368116632', '2012-03-13 20:47:46', '2012-03-13 20:47:46');
/*!40000 ALTER TABLE `members_members` ENABLE KEYS */;


-- Dumping structure for table impulse.members_subscriptions
DROP TABLE IF EXISTS `members_subscriptions`;
CREATE TABLE IF NOT EXISTS `members_subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned NOT NULL,
  `price_on_signup` decimal(10,2) NOT NULL,
  `type_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `start_date` date NOT NULL,
  `expire_date` date NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `type_id` (`type_id`),
  CONSTRAINT `members_subscriptions_member_id_members_members_id` FOREIGN KEY (`member_id`) REFERENCES `members_members` (`id`),
  CONSTRAINT `members_subscriptions_type_id_members_subscription_types_id` FOREIGN KEY (`type_id`) REFERENCES `members_subscription_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table impulse.members_subscriptions: ~2 rows (approximately)
/*!40000 ALTER TABLE `members_subscriptions` DISABLE KEYS */;
INSERT INTO `members_subscriptions` (`id`, `member_id`, `price_on_signup`, `type_id`, `status`, `start_date`, `expire_date`, `created_at`, `updated_at`) VALUES
	(1, 1, 3400.00, 1, 'ACTIVE', '2011-10-15', '2012-10-15', '2012-03-13 20:47:46', '2012-03-13 20:47:46'),
	(2, 2, 3400.00, 1, 'ACTIVE', '2011-10-15', '2012-10-15', '2012-03-13 20:47:46', '2012-03-13 20:47:46'),
	(3, 7, 980.00, 2, 'ACTIVE', '2012-03-21', '2012-06-21', '2012-03-21 20:27:50', '2012-03-21 20:27:50');
/*!40000 ALTER TABLE `members_subscriptions` ENABLE KEYS */;


-- Dumping structure for table impulse.members_subscription_types
DROP TABLE IF EXISTS `members_subscription_types`;
CREATE TABLE IF NOT EXISTS `members_subscription_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `duration` int(11) NOT NULL,
  `units` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `enter_time` time DEFAULT NULL,
  `exit_time` time DEFAULT NULL,
  `visits_per_week` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table impulse.members_subscription_types: ~2 rows (approximately)
/*!40000 ALTER TABLE `members_subscription_types` DISABLE KEYS */;
INSERT INTO `members_subscription_types` (`id`, `title`, `description`, `duration`, `units`, `enter_time`, `exit_time`, `visits_per_week`, `price`, `created_at`, `updated_at`) VALUES
	(1, '1 Year Anytime Unlimited', '1 Year Anytime Unlimited', 1, 'YEARS', NULL, NULL, NULL, 3400.00, '2012-03-13 20:47:46', '2012-03-13 20:47:46'),
	(2, '3 Montsh Anytime 3 times / week', '3 Montsh Anytime 3 times / week', 3, 'MONTHS', NULL, NULL, 3, 980.00, '2012-03-13 20:47:46', '2012-03-13 20:47:46');
/*!40000 ALTER TABLE `members_subscription_types` ENABLE KEYS */;


-- Dumping structure for table impulse.members_visits
DROP TABLE IF EXISTS `members_visits`;
CREATE TABLE IF NOT EXISTS `members_visits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned NOT NULL,
  `day` date NOT NULL,
  `enter_time` time NOT NULL,
  `exit_time` time DEFAULT NULL,
  `subscription_id` bigint(20) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `subscription_id` (`subscription_id`),
  CONSTRAINT `members_visits_member_id_members_members_id` FOREIGN KEY (`member_id`) REFERENCES `members_members` (`id`),
  CONSTRAINT `members_visits_subscription_id_members_subscriptions_id` FOREIGN KEY (`subscription_id`) REFERENCES `members_subscriptions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table impulse.members_visits: ~5 rows (approximately)
/*!40000 ALTER TABLE `members_visits` DISABLE KEYS */;
INSERT INTO `members_visits` (`id`, `member_id`, `day`, `enter_time`, `exit_time`, `subscription_id`, `created_at`, `updated_at`) VALUES
	(1, 2, '2011-10-15', '22:00:00', '23:00:00', 2, '2012-03-13 20:47:46', '2012-03-13 20:47:46'),
	(2, 2, '2011-10-16', '21:00:00', '22:00:00', 2, '2012-03-13 20:47:46', '2012-03-13 20:47:46'),
	(3, 2, '2012-03-12', '21:06:43', '21:06:51', 2, '2012-03-13 21:06:44', '2012-03-13 21:06:51'),
	(4, 2, '2012-03-20', '21:06:53', '21:07:04', 2, '2012-03-13 21:06:54', '2012-03-13 21:07:04'),
	(5, 2, '2012-03-13', '21:07:05', NULL, 2, '2012-03-13 21:07:07', '2012-03-13 21:07:07'),
	(6, 7, '2012-03-21', '20:27:54', '20:27:59', 3, '2012-03-21 20:27:56', '2012-03-21 20:27:59');
/*!40000 ALTER TABLE `members_visits` ENABLE KEYS */;


-- Dumping structure for table impulse.navigation_items
DROP TABLE IF EXISTS `navigation_items`;
CREATE TABLE IF NOT EXISTS `navigation_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `external_link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sysmap_identifier` varchar(35) COLLATE utf8_unicode_ci DEFAULT NULL,
  `route` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `read_only` tinyint(1) DEFAULT '0',
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  `level` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table impulse.navigation_items: ~20 rows (approximately)
/*!40000 ALTER TABLE `navigation_items` DISABLE KEYS */;
INSERT INTO `navigation_items` (`id`, `type`, `title`, `external_link`, `sysmap_identifier`, `route`, `read_only`, `lft`, `rgt`, `level`) VALUES
	(1, 'menu', 'App navigation', NULL, NULL, NULL, 1, 1, 42, 0),
	(2, 'menu', 'Developer navigation', NULL, NULL, NULL, 1, 26, 41, 1),
	(3, 'programmatic', 'Templater', NULL, '3-46aeab2a0dff4f86431a32b5c8870f88', 'admin', 0, 37, 38, 2),
	(4, 'programmatic', 'System Map', NULL, '3-01fffb3ecd840e0de77fa90b11740b08', 'admin', 0, 31, 36, 2),
	(5, 'programmatic', 'Create Pattern Extend', NULL, '3-4a404a1f6b1da6edab1b0b0751ff383c', 'admin', 0, 34, 35, 3),
	(6, 'programmatic', 'Create Extend', NULL, '3-4c7d5c8b3ab7ea48e9e73d7ce80a210f', 'admin', 0, 32, 33, 3),
	(7, 'programmatic', 'Navigation', NULL, '3-bdc947d7cfae70f3e3c9182fd861b2c5', 'admin', 0, 27, 30, 2),
	(8, 'programmatic', 'Create Item', NULL, '3-3fbfc3832a2bb875d54b42bffefdf1c8', 'admin', 0, 28, 29, 3),
	(9, 'menu', 'Admin navigation', NULL, NULL, NULL, 1, 8, 25, 1),
	(11, 'programmatic', 'Users', NULL, '3-d5ce379013095fd018384793812ae266', 'admin', 0, 21, 22, 2),
	(12, 'programmatic', 'Subscription Types', NULL, '3-78279d996cb2056ed9d9733cac01b0f3', 'default', 0, 19, 20, 2),
	(13, 'programmatic', 'Members', NULL, '3-2c7dd48376b8a04e8161c593dd7d027d', 'default', 0, 9, 18, 2),
	(14, 'programmatic', 'View', NULL, '3-f3b84cf29ae65668f73fe36c3c75ccd5', 'default', 0, 14, 15, 3),
	(15, 'programmatic', 'Edit/Add', NULL, '3-7dc6ea16f0d4289702c189bef812a7eb', 'default', 0, 12, 13, 3),
	(16, 'programmatic', 'List', NULL, '3-2cb61741a956b908897c5147bc32b8eb', 'default', 0, 10, 11, 3),
	(17, 'menu', 'Site navigation', NULL, NULL, NULL, 1, 2, 7, 1),
	(18, 'menu', 'Side navigation', NULL, NULL, NULL, 1, 5, 6, 2),
	(19, 'menu', 'Top navigation', NULL, NULL, NULL, 1, 3, 4, 2),
	(20, 'programmatic', 'Roles', NULL, '3-c6053e0770008c1eb20b60cea00a272e', 'admin', 0, 39, 40, 2),
	(21, 'programmatic', 'Log out', NULL, '3-1cc22a2ae3468d33cf6a09274d491b4a', 'default', 0, 23, 24, 2),
	(22, 'programmatic', 'Emails list', NULL, '3-b9b2e5d909123c89b41590a5b3dc426b', 'default', 0, 16, 17, 3);
/*!40000 ALTER TABLE `navigation_items` ENABLE KEYS */;


-- Dumping structure for table impulse.sysmap_items
DROP TABLE IF EXISTS `sysmap_items`;
CREATE TABLE IF NOT EXISTS `sysmap_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mca` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hash` varchar(35) COLLATE utf8_unicode_ci DEFAULT NULL,
  `form_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `index_date` datetime DEFAULT NULL,
  `path` text COLLATE utf8_unicode_ci,
  `params` longtext COLLATE utf8_unicode_ci,
  `is_pattern` tinyint(1) DEFAULT NULL,
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  `level` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mca` (`mca`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table impulse.sysmap_items: ~88 rows (approximately)
/*!40000 ALTER TABLE `sysmap_items` DISABLE KEYS */;
INSERT INTO `sysmap_items` (`id`, `mca`, `hash`, `form_name`, `title`, `description`, `index_date`, `path`, `params`, `is_pattern`, `lft`, `rgt`, `level`) VALUES
	(1, '*.*.*', '0-816563134a61e1b2c7cd7899b126bde4', NULL, 'All', NULL, '2012-03-21 20:46:20', NULL, NULL, NULL, 1, 178, 0),
	(2, '*.admin.*', '1-9fe21b10c624fe854dc4aa3387a0e1ec', NULL, 'All admin actions', NULL, NULL, NULL, NULL, NULL, 2, 3, 1),
	(3, 'default.*.*', '1-f271393c3ea9ff5e20c72687922e3e5e', NULL, 'default.*.*', NULL, NULL, '/controllers', NULL, NULL, 4, 19, 1),
	(4, 'default.admin.*', '2-536fca5b41c263d3c75be3a6b172e3c6', NULL, 'admin', '', NULL, '/controllers/AdminController.php', NULL, NULL, 5, 8, 2),
	(5, 'default.admin.index', '3-13e4eee66bbfa85479505625b22db026', '', 'default.admin.index', '', NULL, '/controllers/AdminController.php', NULL, NULL, 6, 7, 3),
	(6, 'default.error.*', '2-70f9e1606f2ac9a92eb13d68b39695fd', NULL, 'error', '', NULL, '/controllers/ErrorController.php', NULL, NULL, 9, 14, 2),
	(7, 'default.error.error', '3-00120ea57d828266b32864d2babeece1', '', 'default.error.error', '', NULL, '/controllers/ErrorController.php', NULL, NULL, 10, 11, 3),
	(8, 'default.error.error404', '3-b1fdac9bf9073baa7fc64ca287fe7933', '', 'default.error.error404', '', NULL, '/controllers/ErrorController.php', NULL, NULL, 12, 13, 3),
	(9, 'default.index.*', '2-783cd78083aa731e4afbf14353d512a8', NULL, 'index', '', NULL, '/controllers/IndexController.php', NULL, NULL, 15, 18, 2),
	(10, 'default.index.index', '3-83a4643bbd583eb167f1b5490c05e7a6', '', 'default.index.index', '', NULL, '/controllers/IndexController.php', NULL, NULL, 16, 17, 3),
	(11, 'members.*.*', '1-e435cb5103d8de8c76bd58a7f3523188', NULL, 'members.*.*', NULL, NULL, '/modules/members/controllers', NULL, NULL, 20, 63, 1),
	(12, 'members.admin.*', '2-1d859736e8580870a87e4976f786705d', NULL, 'Members administration', '', NULL, '/modules/members/controllers/AdminController.php', NULL, NULL, 21, 32, 2),
	(13, 'members.admin.index', '3-2c7dd48376b8a04e8161c593dd7d027d', '', 'Members dashboard', '', NULL, '/modules/members/controllers/AdminController.php', NULL, NULL, 22, 23, 3),
	(14, 'members.admin.list', '3-2cb61741a956b908897c5147bc32b8eb', '', 'Members list', '', NULL, '/modules/members/controllers/AdminController.php', NULL, NULL, 24, 25, 3),
	(15, 'members.admin.edit', '3-7dc6ea16f0d4289702c189bef812a7eb', '', 'Edit / Add member', '', NULL, '/modules/members/controllers/AdminController.php', NULL, NULL, 26, 27, 3),
	(16, 'members.admin.view', '3-f3b84cf29ae65668f73fe36c3c75ccd5', '', 'members.admin.view', '', NULL, '/modules/members/controllers/AdminController.php', NULL, NULL, 28, 29, 3),
	(17, 'members.admin.add-subscription', '3-72bfcd5f7464bd37e28ed9d7c881c112', '', 'members.admin.add-subscription', '', NULL, '/modules/members/controllers/AdminController.php', NULL, NULL, 30, 31, 3),
	(18, 'members.report.*', '2-8b96f80b3da5ea4f53d036d50bc9d354', NULL, 'Members Report controller', '', NULL, '/modules/members/controllers/ReportController.php', NULL, NULL, 33, 40, 2),
	(19, 'members.report.index', '3-5699126d7ee180bc1be250012d0bc3ed', '', 'Dashboard', '', NULL, '/modules/members/controllers/ReportController.php', NULL, NULL, 34, 35, 3),
	(20, 'members.report.subscriptions-chart', '3-13e4961d1273f9217ab3de53b16bfb70', '', 'members.report.subscriptions-chart', '', NULL, '/modules/members/controllers/ReportController.php', NULL, NULL, 36, 37, 3),
	(21, 'members.subscription.*', '2-19182e3b5bf27be5f884c7d3d42fd6fd', NULL, 'Subscription administration', '', NULL, '/modules/members/controllers/SubscriptionController.php', NULL, NULL, 41, 50, 2),
	(22, 'members.subscription.index', '3-0b8ab09179c243234dac31c4f79b9273', '', 'Subscription dashboard', '', NULL, '/modules/members/controllers/SubscriptionController.php', NULL, NULL, 42, 43, 3),
	(23, 'members.subscription.list', '3-78279d996cb2056ed9d9733cac01b0f3', '', 'members.subscription.list', '', NULL, '/modules/members/controllers/SubscriptionController.php', NULL, NULL, 44, 45, 3),
	(24, 'members.subscription.edit', '3-0787cde283217ab0643728e61078b28f', '', 'members.subscription.edit', '', NULL, '/modules/members/controllers/SubscriptionController.php', NULL, NULL, 46, 47, 3),
	(25, 'members.subscription.view', '3-0be3ca647630c5dcb3fc531b77e7e98a', '', 'members.subscription.view', '', NULL, '/modules/members/controllers/SubscriptionController.php', NULL, NULL, 48, 49, 3),
	(26, 'members.visit.*', '2-07f7f87b684490b739f5eeda70a27cd1', NULL, 'visit', '', NULL, '/modules/members/controllers/VisitController.php', NULL, NULL, 51, 62, 2),
	(27, 'members.visit.index', '3-d018cf203ebc0b0528bf84ffc89f3aea', '', 'Visits dashboard', '', NULL, '/modules/members/controllers/VisitController.php', NULL, NULL, 52, 53, 3),
	(28, 'members.visit.view', '3-415867064791e6270046af7489673672', '', 'members.visit.view', '', NULL, '/modules/members/controllers/VisitController.php', NULL, NULL, 54, 55, 3),
	(29, 'members.visit.list', '3-360a6971e8aa71b8fde5ec4aca2e5e0d', '', 'Visits list', '', NULL, '/modules/members/controllers/VisitController.php', NULL, NULL, 56, 57, 3),
	(30, 'members.visit.new', '3-5029daae208d4a62bf13853815ab37d7', '', 'members.visit.new', '', NULL, '/modules/members/controllers/VisitController.php', NULL, NULL, 58, 59, 3),
	(31, 'members.visit.finish', '3-5f4afe5e4bf349f25b31cba11e7fdf30', '', 'members.visit.finish', '', NULL, '/modules/members/controllers/VisitController.php', NULL, NULL, 60, 61, 3),
	(32, 'navigation.*.*', '1-4d24f38495c5978a0369e102588e7ffe', NULL, 'navigation.*.*', NULL, NULL, '/modules/navigation/controllers', NULL, NULL, 64, 85, 1),
	(33, 'navigation.admin.*', '2-5c9a7d59bbb71bb2313f086de217d3f4', NULL, 'Navigation administrative controller', '', NULL, '/modules/navigation/controllers/AdminController.php', NULL, NULL, 65, 76, 2),
	(34, 'navigation.admin.index', '3-ed97ef94b5ad6d4d52b8309bdad308ec', '', 'navigation.admin.index', '', NULL, '/modules/navigation/controllers/AdminController.php', NULL, NULL, 66, 67, 3),
	(35, 'navigation.admin.list-menu', '3-bdc947d7cfae70f3e3c9182fd861b2c5', '', 'List structured menu', '', NULL, '/modules/navigation/controllers/AdminController.php', NULL, NULL, 68, 69, 3),
	(36, 'navigation.admin.move', '3-a1f9dfe7f5aeab1dddd62fee219e08d5', '', 'Move menu item', '', NULL, '/modules/navigation/controllers/AdminController.php', NULL, NULL, 70, 71, 3),
	(37, 'navigation.admin.edit-menu-item', '3-3fbfc3832a2bb875d54b42bffefdf1c8', '', 'Editing menu item (leaf node)', '', NULL, '/modules/navigation/controllers/AdminController.php', NULL, NULL, 72, 73, 3),
	(38, 'navigation.admin.delete-menu-item', '3-6597fc510b6e383fdc1ba48fc41216d7', '', 'Deleting menu node', '', NULL, '/modules/navigation/controllers/AdminController.php', NULL, NULL, 74, 75, 3),
	(39, 'navigation.index.*', '2-6663c8061d1f9e5dfc143f53c337bb56', NULL, 'Contains methods for displaying parts of the navigation', '', NULL, '/modules/navigation/controllers/IndexController.php', NULL, NULL, 77, 84, 2),
	(40, 'navigation.index.display-menu', '3-74078250f9d9a091d1f8399df232238c', 'Navigation_Form_DisplayMenuParams', 'Display user defined navigation', '', NULL, '/modules/navigation/controllers/IndexController.php', NULL, NULL, 78, 83, 3),
	(41, 'sysmap.*.*', '1-70943d718c85b43b8e02611f7d4cedd7', NULL, 'sysmap.*.*', NULL, NULL, '/modules/sysmap/controllers', NULL, NULL, 86, 99, 1),
	(42, 'sysmap.admin.*', '2-376b3b65ea372d9e5cbad0d147d33218', NULL, 'Sysmap admin controller', '', NULL, '/modules/sysmap/controllers/AdminController.php', NULL, NULL, 87, 98, 2),
	(43, 'sysmap.admin.index', '3-1b165c5fbb6a2c4fd7d2c56ce64533d8', '', 'sysmap.admin.index', '', NULL, '/modules/sysmap/controllers/AdminController.php', NULL, NULL, 88, 89, 3),
	(44, 'sysmap.admin.list', '3-01fffb3ecd840e0de77fa90b11740b08', '', 'List the map', 'Shows the list of the map\r\nin hierarchy', NULL, '/modules/sysmap/controllers/AdminController.php', NULL, NULL, 90, 91, 3),
	(45, 'sysmap.admin.edit-extend', '3-4c7d5c8b3ab7ea48e9e73d7ce80a210f', '', 'sysmap.admin.edit-extend', '', NULL, '/modules/sysmap/controllers/AdminController.php', NULL, NULL, 92, 93, 3),
	(46, 'sysmap.admin.delete-extend', '3-2b9712fa8db157bd87a3a7ea6d89674c', '', 'sysmap.admin.delete-extend', '', NULL, '/modules/sysmap/controllers/AdminController.php', NULL, NULL, 94, 95, 3),
	(47, 'sysmap.admin.edit-extend-pattern', '3-4a404a1f6b1da6edab1b0b0751ff383c', '', 'sysmap.admin.edit-extend-pattern', '', NULL, '/modules/sysmap/controllers/AdminController.php', NULL, NULL, 96, 97, 3),
	(48, 'templater.*.*', '1-a7f29d0f18ba726fb5cd5f55cf1861d0', NULL, 'templater.*.*', NULL, NULL, '/modules/templater/controllers', NULL, NULL, 100, 127, 1),
	(49, 'templater.admin.*', '2-c2935b0c7994fd961849eb60d35e8d9a', NULL, 'Themes administrator panel', '', NULL, '/modules/templater/controllers/AdminController.php', NULL, NULL, 101, 122, 2),
	(50, 'templater.admin.index', '3-184b38b59367422bd61a819b7242c654', '', 'Display templater admin dashboard', '', NULL, '/modules/templater/controllers/AdminController.php', NULL, NULL, 102, 103, 3),
	(51, 'templater.admin.themes', '3-46aeab2a0dff4f86431a32b5c8870f88', '', 'Themes list action', '', NULL, '/modules/templater/controllers/AdminController.php', NULL, NULL, 104, 105, 3),
	(52, 'templater.admin.edit-theme', '3-70a03feb03653464c4ddc008821c0925', '', 'Edit Theme action', '', NULL, '/modules/templater/controllers/AdminController.php', NULL, NULL, 106, 107, 3),
	(53, 'templater.admin.delete-theme', '3-716b83ff367670545623487a05b9091d', '', 'Delete Theme action', '', NULL, '/modules/templater/controllers/AdminController.php', NULL, NULL, 108, 109, 3),
	(54, 'templater.admin.layouts', '3-7f4582eaeba1639797df547b8595d397', '', 'Layouts list action', '', NULL, '/modules/templater/controllers/AdminController.php', NULL, NULL, 110, 111, 3),
	(55, 'templater.admin.edit-layout', '3-aa1a488e98319fb008d7eae5b378c3a1', '', 'Edit Theme action', '', NULL, '/modules/templater/controllers/AdminController.php', NULL, NULL, 112, 113, 3),
	(56, 'templater.admin.delete-layout', '3-4348bb60295ae5123ac0e2b73875f250', '', 'Delete widget action', '', NULL, '/modules/templater/controllers/AdminController.php', NULL, NULL, 114, 115, 3),
	(57, 'templater.admin.widgets', '3-4ebb665441f395db86625982e7ce69ac', '', 'Widgets list action', '', NULL, '/modules/templater/controllers/AdminController.php', NULL, NULL, 116, 117, 3),
	(58, 'templater.admin.edit-widget', '3-f7514a9db40ea35ab79acb19c0260921', '', 'Edit widget action', '', NULL, '/modules/templater/controllers/AdminController.php', NULL, NULL, 118, 119, 3),
	(59, 'templater.admin.delete-widget', '3-d084458852e64f29882e61b3baf1eee4', '', 'Delete widget action', '', NULL, '/modules/templater/controllers/AdminController.php', NULL, NULL, 120, 121, 3),
	(60, 'templater.tools.*', '2-fcad0a9e5cdd92392ed64293468e91c7', NULL, '	SlyS', '', NULL, '/modules/templater/controllers/ToolsController.php', NULL, NULL, 123, 126, 2),
	(61, 'templater.tools.display-flash-messages', '3-80dc6b3841ef4ff214f33344d91b66d4', '', 'Display flash system messages', '', NULL, '/modules/templater/controllers/ToolsController.php', NULL, NULL, 124, 125, 3),
	(62, 'user.*.*', '1-e674602537a9ec1d0ffdfdf497aa977b', NULL, 'user.*.*', NULL, NULL, '/modules/user/controllers', NULL, NULL, 128, 177, 1),
	(63, 'user.admin.*', '2-afaf43926eecf8541044c75fb6afd8fa', NULL, 'User administrator panel', '', NULL, '/modules/user/controllers/AdminController.php', NULL, NULL, 129, 148, 2),
	(64, 'user.admin.index', '3-83a8e046db64efe6cc0cd80a142d5e59', '', 'User module admin dashboard', '', NULL, '/modules/user/controllers/AdminController.php', NULL, NULL, 130, 131, 3),
	(65, 'user.admin.login', '3-b66ea47d80b055d62d1ea4cd133c575d', '', 'Administrator login action', '', NULL, '/modules/user/controllers/AdminController.php', NULL, NULL, 132, 133, 3),
	(66, 'user.admin.users', '3-d5ce379013095fd018384793812ae266', 'User_Form_Widget_UserFilter', 'Users list', '', NULL, '/modules/user/controllers/AdminController.php', NULL, NULL, 134, 135, 3),
	(67, 'user.admin.edit-user', '3-edd5d37c76c4858b735a071ad5b2582f', '', 'Edit user action', '', NULL, '/modules/user/controllers/AdminController.php', NULL, NULL, 136, 137, 3),
	(68, 'user.admin.delete-user', '3-89b64393fcab7e0777f1d5f22d7429eb', '', 'Delete user action', '', NULL, '/modules/user/controllers/AdminController.php', NULL, NULL, 138, 139, 3),
	(69, 'user.admin.roles', '3-c6053e0770008c1eb20b60cea00a272e', '', 'user.admin.roles', '', NULL, '/modules/user/controllers/AdminController.php', NULL, NULL, 140, 141, 3),
	(70, 'user.admin.edit-role', '3-be3bc0f1518ab58720f23f30008c6f8e', '', 'Edit user action', '', NULL, '/modules/user/controllers/AdminController.php', NULL, NULL, 142, 143, 3),
	(71, 'user.admin.delete-role', '3-5c734d932d443f40025b339eae5af7a7', '', 'Delete role action', '', NULL, '/modules/user/controllers/AdminController.php', NULL, NULL, 144, 145, 3),
	(72, 'user.admin.settings', '3-daf7dc399f8421cef0541941be47f3c3', '', 'Setting display action', '', NULL, '/modules/user/controllers/AdminController.php', NULL, NULL, 146, 147, 3),
	(73, 'user.auth.*', '2-f600167a6d2067dba4eaea617ed05c45', NULL, 'User authorization pages', '', NULL, '/modules/user/controllers/AuthController.php', NULL, NULL, 149, 162, 2),
	(74, 'user.auth.login', '3-c8d76e3e03cdaa72e24f02586ce9b1d1', '', 'Login action', '', NULL, '/modules/user/controllers/AuthController.php', NULL, NULL, 150, 151, 3),
	(75, 'user.auth.logout', '3-1cc22a2ae3468d33cf6a09274d491b4a', '', 'Logout action', '', NULL, '/modules/user/controllers/AuthController.php', NULL, NULL, 152, 153, 3),
	(76, 'user.auth.login-box', '3-00af1dbd06b75a42535f5abea0e38fb5', '', 'User login box', '', NULL, '/modules/user/controllers/AuthController.php', NULL, NULL, 154, 155, 3),
	(77, 'user.auth.forgot', '3-64aba5a0e28bbfdf7d4aad8ab81d201e', '', 'Forgot password action', '', NULL, '/modules/user/controllers/AuthController.php', NULL, NULL, 156, 157, 3),
	(78, 'user.auth.check-email', '3-df88dec9f41427fe87c780c275c7dd91', '', 'user.auth.check-email', '', NULL, '/modules/user/controllers/AuthController.php', NULL, NULL, 158, 159, 3),
	(79, 'user.auth.recovery', '3-214c8e6eed72d13788ff0769dd2cf880', '', 'Forgot password action', '', NULL, '/modules/user/controllers/AuthController.php', NULL, NULL, 160, 161, 3),
	(80, 'user.profile.*', '2-e1fe67446af8334782e9b9dad1190244', NULL, 'User profile pages', '', NULL, '/modules/user/controllers/ProfileController.php', NULL, NULL, 163, 170, 2),
	(81, 'user.profile.index', '3-107ced5a0fe84ae245a76dcd4e282716', '', 'Display&Edit user profile form', '', NULL, '/modules/user/controllers/ProfileController.php', NULL, NULL, 164, 165, 3),
	(82, 'user.profile.change-password', '3-2946e9d2c9613340239e7f14f4929644', '', 'Change user password page', '', NULL, '/modules/user/controllers/ProfileController.php', NULL, NULL, 166, 167, 3),
	(83, 'user.profile.user-box', '3-2070f4d37882d965b025370ce2ed37b5', 'User_Form_Widget_UserBox', 'Display user box', '', NULL, '/modules/user/controllers/ProfileController.php', NULL, NULL, 168, 169, 3),
	(84, 'user.registration.*', '2-bd83908a614726a5d0c3a0e62e5cc5bf', NULL, 'User registration pages', '', NULL, '/modules/user/controllers/RegistrationController.php', NULL, NULL, 171, 176, 2),
	(85, 'user.registration.index', '3-abd015e82a7dcb9e73494b5de189aacb', '', 'Display registration page', '', NULL, '/modules/user/controllers/RegistrationController.php', NULL, NULL, 172, 173, 3),
	(86, 'user.registration.check-email', '3-92b891cb44a287a47dfa520566362e6b', '', 'Pag with check email message', '', NULL, '/modules/user/controllers/RegistrationController.php', NULL, NULL, 174, 175, 3),
	(87, NULL, '4-6d238270eb43ceb9ce2d4d57dff6d883', NULL, 'Admin navigation provider', NULL, NULL, NULL, 'a:3:{s:7:"item_id";a:1:{i:0;s:1:"9";}s:3:"css";s:0:"";s:7:"partial";s:0:"";}', NULL, 79, 80, 4),
	(88, NULL, '4-f262f999478f22cbc80db905c2e7dfc6', NULL, 'Developer navigation provider', NULL, NULL, NULL, 'a:3:{s:7:"item_id";a:1:{i:0;s:1:"2";}s:3:"css";s:0:"";s:7:"partial";s:0:"";}', NULL, 81, 82, 4),
	(89, 'members.report.emails-list', '3-b9b2e5d909123c89b41590a5b3dc426b', '', 'members.report.emails-list', '', NULL, '/modules/members/controllers/ReportController.php', NULL, NULL, 38, 39, 3);
/*!40000 ALTER TABLE `sysmap_items` ENABLE KEYS */;


-- Dumping structure for table impulse.templater_layouts
DROP TABLE IF EXISTS `templater_layouts`;
CREATE TABLE IF NOT EXISTS `templater_layouts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `theme_id` bigint(20) DEFAULT NULL,
  `params` longtext COLLATE utf8_unicode_ci,
  `published` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `theme_id` (`theme_id`),
  CONSTRAINT `templater_layouts_theme_id_templater_themes_id` FOREIGN KEY (`theme_id`) REFERENCES `templater_themes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table impulse.templater_layouts: ~3 rows (approximately)
/*!40000 ALTER TABLE `templater_layouts` DISABLE KEYS */;
INSERT INTO `templater_layouts` (`id`, `title`, `name`, `theme_id`, `params`, `published`) VALUES
	(1, 'Index template', 'index', 1, NULL, 1),
	(2, 'Admin template', 'admin', 1, NULL, 1),
	(3, 'Login template', 'login', 1, NULL, 1);
/*!40000 ALTER TABLE `templater_layouts` ENABLE KEYS */;


-- Dumping structure for table impulse.templater_layout_points
DROP TABLE IF EXISTS `templater_layout_points`;
CREATE TABLE IF NOT EXISTS `templater_layout_points` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `map_id` varchar(35) COLLATE utf8_unicode_ci DEFAULT NULL,
  `layout_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `layout_id` (`layout_id`),
  CONSTRAINT `templater_layout_points_layout_id_templater_layouts_id` FOREIGN KEY (`layout_id`) REFERENCES `templater_layouts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table impulse.templater_layout_points: ~1 rows (approximately)
/*!40000 ALTER TABLE `templater_layout_points` DISABLE KEYS */;
INSERT INTO `templater_layout_points` (`id`, `map_id`, `layout_id`) VALUES
	(1, '1-e435cb5103d8de8c76bd58a7f3523188', 2);
/*!40000 ALTER TABLE `templater_layout_points` ENABLE KEYS */;


-- Dumping structure for table impulse.templater_themes
DROP TABLE IF EXISTS `templater_themes`;
CREATE TABLE IF NOT EXISTS `templater_themes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `current` tinyint(1) DEFAULT '0',
  `ordering` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table impulse.templater_themes: ~1 rows (approximately)
/*!40000 ALTER TABLE `templater_themes` DISABLE KEYS */;
INSERT INTO `templater_themes` (`id`, `title`, `name`, `current`, `ordering`) VALUES
	(1, 'Default Theme', 'default', 1, 1);
/*!40000 ALTER TABLE `templater_themes` ENABLE KEYS */;


-- Dumping structure for table impulse.templater_widgets
DROP TABLE IF EXISTS `templater_widgets`;
CREATE TABLE IF NOT EXISTS `templater_widgets` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `map_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  `layout_id` bigint(20) DEFAULT NULL,
  `ordering` bigint(20) DEFAULT NULL,
  `placeholder` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `layout_id` (`layout_id`),
  CONSTRAINT `templater_widgets_layout_id_templater_layouts_id` FOREIGN KEY (`layout_id`) REFERENCES `templater_layouts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table impulse.templater_widgets: ~3 rows (approximately)
/*!40000 ALTER TABLE `templater_widgets` DISABLE KEYS */;
INSERT INTO `templater_widgets` (`id`, `name`, `map_id`, `published`, `layout_id`, `ordering`, `placeholder`) VALUES
	(1, 'Side admin navigation', '4-6d238270eb43ceb9ce2d4d57dff6d883', 1, 2, 110, 'left'),
	(2, 'Flash Messages', '3-80dc6b3841ef4ff214f33344d91b66d4', 1, 2, 1, 'messages'),
	(3, 'Developer Navigation', '4-f262f999478f22cbc80db905c2e7dfc6', 1, 2, 111, 'left');
/*!40000 ALTER TABLE `templater_widgets` ENABLE KEYS */;


-- Dumping structure for table impulse.templater_widget_points
DROP TABLE IF EXISTS `templater_widget_points`;
CREATE TABLE IF NOT EXISTS `templater_widget_points` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `map_id` varchar(35) COLLATE utf8_unicode_ci DEFAULT NULL,
  `widget_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `widget_id` (`widget_id`),
  CONSTRAINT `templater_widget_points_widget_id_templater_widgets_id` FOREIGN KEY (`widget_id`) REFERENCES `templater_widgets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table impulse.templater_widget_points: ~6 rows (approximately)
/*!40000 ALTER TABLE `templater_widget_points` DISABLE KEYS */;
INSERT INTO `templater_widget_points` (`id`, `map_id`, `widget_id`) VALUES
	(1, '1-9fe21b10c624fe854dc4aa3387a0e1ec', 1),
	(2, '1-9fe21b10c624fe854dc4aa3387a0e1ec', 2),
	(3, '1-9fe21b10c624fe854dc4aa3387a0e1ec', 3),
	(4, '1-e435cb5103d8de8c76bd58a7f3523188', 1),
	(5, '1-e435cb5103d8de8c76bd58a7f3523188', 2),
	(6, '1-e435cb5103d8de8c76bd58a7f3523188', 3);
/*!40000 ALTER TABLE `templater_widget_points` ENABLE KEYS */;


-- Dumping structure for table impulse.user_roles
DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  `register` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table impulse.user_roles: ~3 rows (approximately)
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` (`id`, `name`, `parent_id`, `is_default`, `register`) VALUES
	(1, 'guest', 0, 1, NULL),
	(2, 'manager', 1, NULL, NULL),
	(3, 'admin', 1, NULL, NULL);
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;


-- Dumping structure for table impulse.user_rules
DROP TABLE IF EXISTS `user_rules`;
CREATE TABLE IF NOT EXISTS `user_rules` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) DEFAULT NULL,
  `resource_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rule` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'allow',
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `user_rules_role_id_user_roles_id` FOREIGN KEY (`role_id`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table impulse.user_rules: ~16 rows (approximately)
/*!40000 ALTER TABLE `user_rules` DISABLE KEYS */;
INSERT INTO `user_rules` (`id`, `role_id`, `resource_id`, `rule`) VALUES
	(2, 3, '0-816563134a61e1b2c7cd7899b126bde4', 'allow'),
	(3, 1, '3-80dc6b3841ef4ff214f33344d91b66d4', 'allow'),
	(4, 1, '2-f600167a6d2067dba4eaea617ed05c45', 'allow'),
	(5, 1, '3-c8d76e3e03cdaa72e24f02586ce9b1d1', 'allow'),
	(6, 1, '3-1cc22a2ae3468d33cf6a09274d491b4a', 'allow'),
	(7, 1, '3-00af1dbd06b75a42535f5abea0e38fb5', 'allow'),
	(8, 1, '3-64aba5a0e28bbfdf7d4aad8ab81d201e', 'allow'),
	(9, 1, '3-df88dec9f41427fe87c780c275c7dd91', 'allow'),
	(10, 1, '3-214c8e6eed72d13788ff0769dd2cf880', 'allow'),
	(11, 2, '3-2cb61741a956b908897c5147bc32b8eb', 'allow'),
	(12, 2, '3-f3b84cf29ae65668f73fe36c3c75ccd5', 'allow'),
	(13, 2, '3-72bfcd5f7464bd37e28ed9d7c881c112', 'allow'),
	(14, 2, '3-0be3ca647630c5dcb3fc531b77e7e98a', 'allow'),
	(15, 2, '3-415867064791e6270046af7489673672', 'allow'),
	(16, 2, '3-5029daae208d4a62bf13853815ab37d7', 'allow'),
	(17, 2, '3-5f4afe5e4bf349f25b31cba11e7fdf30', 'allow'),
	(18, 2, '3-2c7dd48376b8a04e8161c593dd7d027d', 'allow'),
	(19, 2, '2-f600167a6d2067dba4eaea617ed05c45', 'allow'),
	(20, 2, '3-c8d76e3e03cdaa72e24f02586ce9b1d1', 'allow'),
	(21, 2, '3-1cc22a2ae3468d33cf6a09274d491b4a', 'allow'),
	(22, 2, '3-00af1dbd06b75a42535f5abea0e38fb5', 'allow'),
	(23, 2, '3-64aba5a0e28bbfdf7d4aad8ab81d201e', 'allow'),
	(24, 2, '3-df88dec9f41427fe87c780c275c7dd91', 'allow'),
	(25, 2, '3-214c8e6eed72d13788ff0769dd2cf880', 'allow'),
	(26, 2, '3-78279d996cb2056ed9d9733cac01b0f3', 'allow'),
	(27, 2, '4-6d238270eb43ceb9ce2d4d57dff6d883', 'allow');
/*!40000 ALTER TABLE `user_rules` ENABLE KEYS */;


-- Dumping structure for table impulse.user_users
DROP TABLE IF EXISTS `user_users`;
CREATE TABLE IF NOT EXISTS `user_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `role_id` bigint(20) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `firstname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `birth` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `token_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `user_users_role_id_user_roles_id` FOREIGN KEY (`role_id`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table impulse.user_users: ~2 rows (approximately)
/*!40000 ALTER TABLE `user_users` DISABLE KEYS */;
INSERT INTO `user_users` (`id`, `login`, `password`, `role_id`, `active`, `firstname`, `lastname`, `birth`, `email`, `phone`, `zip`, `token`, `token_date`) VALUES
	(1, 'user', '4297f44b13955235245b2497399d7a93', 2, 1, 'Иван', 'Иванов', NULL, NULL, NULL, NULL, NULL, NULL),
	(2, 'admin', '4297f44b13955235245b2497399d7a93', 3, 1, 'Администратор', 'Администратор', NULL, NULL, NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE `user_users` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
